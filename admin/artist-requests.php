
<?php
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ADMIN AUTHORIZATION
if (
    !isset($_SESSION['user_id']) ||
    ($_SESSION['role'] ?? '') !== 'admin'
) {
    header('Location: ../auth/login.php?redirect=' . urlencode('../admin/artist-requests.php'));
    exit;
}

$notice = '';
$error = '';

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

// CSRF PROTECTION
if (empty($_SESSION['artist_request_csrf'])) {
    $_SESSION['artist_request_csrf'] = bin2hex(random_bytes(32));
}

// ======================================================
// HANDLE ADMIN ACTIONS
// ======================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['artist_request_csrf'], $token)) {

        $error = 'Invalid request. Please refresh the page and try again.';

    } else {

        $action = $_POST['action'] ?? '';
        $applicationId = (int)($_POST['artist_id'] ?? 0);

        if ($applicationId <= 0) {

            $error = 'Invalid application ID.';

        } elseif ($action === 'approve') {

            mysqli_begin_transaction($conn);

            try {

                // Lock application to prevent duplicate approval.
                $result = mysqli_query(
                    $conn,
                    "SELECT * FROM artist_applications
                     WHERE id = {$applicationId}
                     FOR UPDATE"
                );

                if (!$result || mysqli_num_rows($result) !== 1) {
                    throw new Exception('Application not found.');
                }

                $application = mysqli_fetch_assoc($result);

                $artistId = (int)($application['artist_id'] ?? 0);

                // Check whether this application already created an artist.
                $artistExists = false;

                if ($artistId > 0) {

                    $check = mysqli_prepare(
                        $conn,
                        "SELECT id FROM artists WHERE id = ?"
                    );

                    mysqli_stmt_bind_param($check, "i", $artistId);
                    mysqli_stmt_execute($check);
                    mysqli_stmt_store_result($check);

                    $artistExists = mysqli_stmt_num_rows($check) > 0;

                    mysqli_stmt_close($check);
                }

                // Insert artist only if it does not already exist.
                if (!$artistExists) {

                    $name = $application['name'];
                    $bio = $application['bio'] ?? '';
                    $image = $application['image'] ?? '';

                    $insert = mysqli_prepare(
                        $conn,
                        "INSERT INTO artists (name, bio, image)
                         VALUES (?, ?, ?)"
                    );

                    if (!$insert) {
                        throw new Exception('Could not prepare artist record.');
                    }

                    mysqli_stmt_bind_param(
                        $insert,
                        "sss",
                        $name,
                        $bio,
                        $image
                    );

                    if (!mysqli_stmt_execute($insert)) {
                        throw new Exception('Could not publish artist.');
                    }

                    $artistId = mysqli_insert_id($conn);

                    mysqli_stmt_close($insert);
                }

                // Link application to published artist.
                $update = mysqli_prepare(
                    $conn,
                    "UPDATE artist_applications
                     SET status = 'approved',
                         artist_id = ?,
                         reviewed_at = NOW()
                     WHERE id = ?"
                );

                if (!$update) {
                    throw new Exception('Could not update application.');
                }

                mysqli_stmt_bind_param(
                    $update,
                    "ii",
                    $artistId,
                    $applicationId
                );

                if (!mysqli_stmt_execute($update)) {
                    throw new Exception('Could not approve application.');
                }

                mysqli_stmt_close($update);

                mysqli_commit($conn);

                $notice = "Application #{$applicationId} approved. Artist published successfully.";

            } catch (Throwable $e) {

                mysqli_rollback($conn);

                $error = $e->getMessage();
            }

        } elseif ($action === 'reject') {

            // Do not reject an already published artist through this action.
            $update = mysqli_prepare(
                $conn,
                "UPDATE artist_applications
                 SET status = 'rejected',
                     reviewed_at = NOW()
                 WHERE id = ?
                   AND status = 'pending'
                   AND artist_id IS NULL"
            );

            if ($update) {

                mysqli_stmt_bind_param($update, "i", $applicationId);

                if (mysqli_stmt_execute($update) && mysqli_stmt_affected_rows($update) > 0) {

                    $notice = "Application #{$applicationId} has been rejected.";

                } else {

                    $error = 'Application could not be rejected. It may already have been processed.';
                }

                mysqli_stmt_close($update);

            } else {

                $error = 'Database error while rejecting application.';
            }

        } else {

            $error = 'Unknown admin action.';
        }
    }
}

// ======================================================
// FETCH PENDING APPLICATIONS
// ======================================================

$pendingRequests = [];

$resPending = mysqli_query(
    $conn,
    "SELECT *
     FROM artist_applications
     WHERE status = 'pending'
     ORDER BY id DESC"
);

if ($resPending) {
    while ($row = mysqli_fetch_assoc($resPending)) {
        $pendingRequests[] = $row;
    }
}

// ======================================================
// FETCH RECENTLY PROCESSED APPLICATIONS
// ======================================================

$recentReviewed = [];

$resReviewed = mysqli_query(
    $conn,
    "SELECT *
     FROM artist_applications
     WHERE status IN ('approved', 'rejected')
     ORDER BY reviewed_at DESC, id DESC
     LIMIT 15"
);

if ($resReviewed) {
    while ($row = mysqli_fetch_assoc($resReviewed)) {
        $recentReviewed[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Artist Requests Queue — VEBRA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="../favicon.svg" rel="icon" type="image/svg+xml">

    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&family=Inter:wght@400;500;600&display=swap">

    <link rel="stylesheet" href="../assets/css/vendor/bootstrap-grid.min.css">
    <link rel="stylesheet" href="../assets/icon-fonts/remixicon/remixicon.css">
    <link rel="stylesheet" href="../assets/css/main.css">

    <style>
        body {
            background: #08080C;
            color: #fff;
            font-family: 'Inter', sans-serif;
        }

        .admin-nav {
            background: rgba(14, 14, 20, 0.96);
            border-bottom: 1px solid rgba(255,255,255,0.08);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 100;
            backdrop-filter: blur(12px);
        }

        .admin-nav__links {
            display: flex;
            gap: 20px;
            align-items: center;
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .admin-nav__links a {
            color: #888;
            text-decoration: none;
            font-size: 0.88rem;
            font-weight: 500;
            transition: color 0.2s;
            padding: 6px 0;
            position: relative;
        }

        .admin-nav__links a:hover,
        .admin-nav__links a.is-active {
            color: var(--accent, #FF2E93);
            font-weight: 600;
        }

        .admin-nav__links a.is-active::after {
            content: "";
            position: absolute;
            left: 0;
            right: 0;
            bottom: 0;
            height: 2px;
            background: var(--accent, #FF2E93);
            border-radius: 2px;
        }

        .admin-card {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            backdrop-filter: blur(10px);
            margin-bottom: 24px;
        }

        .admin-card__title {
            font-family: 'Unbounded', sans-serif;
            font-size: 1.1rem;
            font-weight: 700;
            text-transform: uppercase;
            margin: 0 0 18px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 14px 16px;
            text-align: left;
            border-bottom: 1px solid rgba(255,255,255,0.06);
            font-size: 0.9rem;
            vertical-align: middle;
        }

        th {
            color: rgba(255,255,255,0.45);
            font-weight: 600;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
        }

        tbody tr:hover {
            background: rgba(255,255,255,0.02);
        }

        .artist-avatar {
            width: 44px;
            height: 44px;
            border-radius: 8px;
            object-fit: cover;
            background: #222;
        }

        .badge-status {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 10px;
            border-radius: 999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .badge--pending {
            background: rgba(245,158,11,0.15);
            color: #F59E0B;
            border: 1px solid rgba(245,158,11,0.3);
        }

        .badge--approved {
            background: rgba(16,185,129,0.15);
            color: #10B981;
            border: 1px solid rgba(16,185,129,0.3);
        }

        .badge--rejected {
            background: rgba(239,68,68,0.15);
            color: #EF4444;
            border: 1px solid rgba(239,68,68,0.3);
        }
    </style>
</head>

<body class="theme-magenta">

<header class="admin-nav">
    <div class="container-vebra">
        <div style="display:flex;align-items:center;justify-content:space-between;width:100%;">

            <div style="display:flex;align-items:center;gap:28px;">

                <a href="index.php"
                   style="font-family:'Unbounded',sans-serif;font-weight:800;font-size:1.2rem;color:#fff;text-decoration:none;">
                    vebra<span style="color:var(--accent,#FF2E93);">.</span>
                    <span style="font-size:0.7rem;font-weight:600;opacity:0.6;text-transform:uppercase;letter-spacing:1px;margin-left:6px;vertical-align:middle;">Control</span>
                </a>

                <ul class="admin-nav__links">
                    <li><a href="index.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                    <li><a href="songs.php"><i class="ri-music-2-line"></i> Songs</a></li>
                    <li><a href="videos.php"><i class="ri-video-line"></i> Videos</a></li>
                    <li><a href="artists.php"><i class="ri-user-star-line"></i> Artists</a></li>
                    <li><a href="artist-requests.php" class="is-active"><i class="ri-user-follow-line"></i> Requests</a></li>
                    <li><a href="bookings.php"><i class="ri-ticket-line"></i> Bookings</a></li>
                    <li><a href="orders.php"><i class="ri-shopping-bag-3-line"></i> Orders</a></li>
                    <li><a href="reviews.php"><i class="ri-chat-smile-2-line"></i> Reviews</a></li>
                    <li><a href="users.php"><i class="ri-user-settings-line"></i> Users</a></li>
                </ul>

            </div>

            <div style="display:flex;align-items:center;gap:14px;">

                <a href="../artists.php" class="btn btn--glass btn--sm" target="_blank">
                    <span class="btn__label"><i class="ri-external-link-line"></i> View Roster</span>
                </a>

                <a href="../auth/logout.php"
                   class="btn btn--solid btn--sm"
                   style="background:rgba(255,255,255,0.08);">
                    <span class="btn__label"><i class="ri-logout-box-r-line"></i> Logout</span>
                </a>

            </div>
        </div>
    </div>
</header>

<main style="padding:36px 0 80px;">
    <div class="container-vebra">

        <div style="margin-bottom:24px;display:flex;justify-content:space-between;align-items:center;">

            <div>
                <h1 style="font-family:'Unbounded',sans-serif;font-size:1.8rem;font-weight:800;text-transform:uppercase;margin:0 0 6px;">
                    Artist <em>Requests</em>
                </h1>

                <p style="color:rgba(255,255,255,0.5);font-size:0.9rem;margin:0;">
                    Review, approve, or reject submissions from artists wanting to join VEBRA.
                </p>
            </div>

            <a href="artists.php" class="btn btn--glass btn--sm">
                <span class="btn__label"><i class="ri-list-check"></i> Manage Full Roster</span>
            </a>

        </div>

        <?php if ($notice !== ''): ?>
            <div style="background:rgba(16,185,129,0.12);border:1px solid #10B981;color:#fff;padding:12px 18px;border-radius:8px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
                <i class="ri-checkbox-circle-line" style="color:#10B981;font-size:1.2rem;"></i>
                <span><?= h($notice) ?></span>
            </div>
        <?php endif; ?>

        <?php if ($error !== ''): ?>
            <div style="background:rgba(239,68,68,0.12);border:1px solid #EF4444;color:#fff;padding:12px 18px;border-radius:8px;margin-bottom:24px;display:flex;align-items:center;gap:10px;">
                <i class="ri-error-warning-line" style="color:#EF4444;font-size:1.2rem;"></i>
                <span><?= h($error) ?></span>
            </div>
        <?php endif; ?>

        <!-- PENDING QUEUE -->

        <div class="admin-card">

            <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;">
                <h2 class="admin-card__title" style="margin:0;">
                    <i class="ri-time-line" style="color:#F59E0B;"></i>
                    Pending Review Queue (<?= count($pendingRequests) ?>)
                </h2>
            </div>

            <?php if (empty($pendingRequests)): ?>

                <p style="color:rgba(255,255,255,0.4);margin:0;padding:20px 0;text-align:center;">
                    No pending artist requests. All submissions have been processed.
                </p>

            <?php else: ?>

                <div style="overflow-x:auto;">

                    <table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Artist / Band</th>
                                <th>Bio &amp; Style</th>
                                <th>Status</th>
                                <th style="text-align:right;">Decision</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($pendingRequests as $req):
                                $img = !empty($req['image'])
                                    ? '../' . h($req['image'])
                                    : '../assets/images/team-1.jpg';
                            ?>

                                <tr>

                                    <td style="color:rgba(255,255,255,0.4);">
                                        #<?= (int)$req['id'] ?>
                                    </td>

                                    <td>
                                        <img src="<?= $img ?>"
                                             alt=""
                                             class="artist-avatar"
                                             onerror="this.src='../assets/images/team-1.jpg'">
                                    </td>

                                    <td>
                                        <strong style="color:#fff;font-size:0.95rem;">
                                            <?= h($req['name']) ?>
                                        </strong>
                                    </td>

                                    <td style="max-width:380px;">
                                        <div style="color:rgba(255,255,255,0.7);font-size:0.85rem;line-height:1.4;max-height:60px;overflow:hidden;text-overflow:ellipsis;">
                                            <?= h($req['bio'] ?? '') ?>
                                        </div>
                                    </td>

                                    <td>
                                        <span class="badge-status badge--pending">
                                            <i class="ri-time-line"></i> Pending
                                        </span>
                                    </td>

                                    <td style="text-align:right;white-space:nowrap;">

                                        <form method="POST" style="display:inline-flex;gap:8px;">

                                            <input type="hidden" name="csrf_token"
                                                   value="<?= h($_SESSION['artist_request_csrf']) ?>">

                                            <input type="hidden" name="artist_id"
                                                   value="<?= (int)$req['id'] ?>">

                                            <button type="submit"
                                                    name="action"
                                                    value="approve"
                                                    class="btn btn--sm"
                                                    style="background:#10B981;color:#fff;border:none;padding:6px 12px;cursor:pointer;"
                                                    onclick="return confirm('Approve this artist application? They will immediately appear on the public roster.');">
                                                <i class="ri-check-line"></i> Approve
                                            </button>

                                            <button type="submit"
                                                    name="action"
                                                    value="reject"
                                                    class="btn btn--sm"
                                                    style="background:rgba(239,68,68,0.2);color:#EF4444;border:1px solid #EF4444;padding:6px 12px;cursor:pointer;"
                                                    onclick="return confirm('Reject this artist application?');">
                                                <i class="ri-close-line"></i> Reject
                                            </button>

                                        </form>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>
                    </table>

                </div>

            <?php endif; ?>

        </div>

        <!-- RECENTLY PROCESSED APPLICATIONS -->

        <div class="admin-card">

            <h2 class="admin-card__title">
                <i class="ri-history-line"></i> Recently Processed Applications
            </h2>

            <?php if (empty($recentReviewed)): ?>

                <p style="color:rgba(255,255,255,0.4);padding:20px 0;text-align:center;">
                    No applications have been reviewed yet.
                </p>

            <?php else: ?>

                <div style="overflow-x:auto;">

                    <table>

                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Photo</th>
                                <th>Artist / Band</th>
                                <th>Status</th>
                                <th>Published Artist ID</th>
                                <th style="text-align:right;">Action</th>
                            </tr>
                        </thead>

                        <tbody>

                            <?php foreach ($recentReviewed as $art):

                                $img = !empty($art['image'])
                                    ? '../' . h($art['image'])
                                    : '../assets/images/team-1.jpg';

                                $st = $art['status'];
                            ?>

                                <tr>

                                    <td style="color:rgba(255,255,255,0.4);">
                                        #<?= (int)$art['id'] ?>
                                    </td>

                                    <td>
                                        <img src="<?= $img ?>"
                                             alt=""
                                             class="artist-avatar"
                                             onerror="this.src='../assets/images/team-1.jpg'">
                                    </td>

                                    <td>
                                        <strong style="color:#fff;">
                                            <?= h($art['name']) ?>
                                        </strong>
                                    </td>

                                    <td>

                                        <?php if ($st === 'approved'): ?>

                                            <span class="badge-status badge--approved">
                                                <i class="ri-checkbox-circle-line"></i> Approved
                                            </span>

                                        <?php else: ?>

                                            <span class="badge-status badge--rejected">
                                                <i class="ri-close-circle-line"></i> Rejected
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                    <td>
                                        <?= !empty($art['artist_id'])
                                            ? '#' . (int)$art['artist_id']
                                            : '—' ?>
                                    </td>

                                    <td style="text-align:right;">

                                        <?php if ($st === 'rejected'): ?>

                                            <form method="POST" style="display:inline;">

                                                <input type="hidden" name="csrf_token"
                                                       value="<?= h($_SESSION['artist_request_csrf']) ?>">

                                                <input type="hidden" name="artist_id"
                                                       value="<?= (int)$art['id'] ?>">

                                                <button type="submit"
                                                        name="action"
                                                        value="approve"
                                                        class="btn btn--sm"
                                                        style="background:rgba(16,185,129,0.15);color:#10B981;border:1px solid #10B981;padding:4px 8px;font-size:0.75rem;cursor:pointer;"
                                                        onclick="return confirm('Approve this application again?');">
                                                    Re-Approve
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span style="font-size:0.78rem;color:rgba(255,255,255,0.4);">
                                                Published
                                            </span>

                                        <?php endif; ?>

                                    </td>

                                </tr>

                            <?php endforeach; ?>

                        </tbody>

                    </table>

                </div>

            <?php endif; ?>

        </div>

    </div>
</main>

</body>
</html>
