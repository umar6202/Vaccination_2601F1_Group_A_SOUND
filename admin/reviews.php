<?php
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin security guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php?redirect=' . urlencode('../admin/reviews.php'));
    exit;
}

$notice = '';
$error = '';

// Handle Moderation Actions
if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST') {
    $reviewId = intval($_POST['review_id'] ?? 0);

    if (isset($_POST['approve_review']) && $reviewId > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE reviews SET status = 'approved' WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $reviewId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notice = 'Review #' . $reviewId . ' approved successfully.';
        }
    } elseif (isset($_POST['reject_review']) && $reviewId > 0) {
        $stmt = mysqli_prepare($conn, "UPDATE reviews SET status = 'rejected' WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $reviewId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notice = 'Review #' . $reviewId . ' rejected.';
        }
    } elseif (isset($_POST['delete_review']) && $reviewId > 0) {
        $stmt = mysqli_prepare($conn, "DELETE FROM reviews WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $reviewId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notice = 'Review #' . $reviewId . ' removed permanently.';
        }
    }
}

// Filter handling
$filter = $_GET['filter'] ?? 'all';
$validFilters = ['all', 'pending', 'approved', 'rejected'];
if (!in_array($filter, $validFilters, true)) {
    $filter = 'all';
}

$whereClause = "";
if ($filter === 'pending') {
    $whereClause = "WHERE r.status = 'pending'";
} elseif ($filter === 'approved') {
    $whereClause = "WHERE r.status = 'approved'";
} elseif ($filter === 'rejected') {
    $whereClause = "WHERE r.status = 'rejected'";
}

// Counts
$cntAll = 0; $cntPending = 0; $cntApproved = 0; $cntRejected = 0;
$resCount = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM reviews GROUP BY status");
if ($resCount) {
    while ($row = mysqli_fetch_assoc($resCount)) {
        if ($row['status'] === 'pending') $cntPending = (int)$row['cnt'];
        elseif ($row['status'] === 'approved') $cntApproved = (int)$row['cnt'];
        elseif ($row['status'] === 'rejected') $cntRejected = (int)$row['cnt'];
        $cntAll += (int)$row['cnt'];
    }
}

// Fetch reviews
$allReviews = [];
$sql = "SELECT r.*, s.title as song_title, a.name as artist, u.username, u.email 
        FROM reviews r 
        JOIN songs s ON r.song_id = s.id 
        LEFT JOIN artists a ON a.id = s.artist_id 
        JOIN users u ON r.user_id = u.id 
        $whereClause 
        ORDER BY r.created_at DESC";
$res = mysqli_query($conn, $sql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $allReviews[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Moderate Reviews — VEBRA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../favicon.svg" rel="icon" type="image/svg+xml">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap-grid.min.css">
    <link rel="stylesheet" href="../assets/icon-fonts/remixicon/remixicon.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        body { background: #08080C; color: #fff; font-family: 'Inter', sans-serif; }
        .admin-nav { background: rgba(14, 14, 20, 0.95); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 16px 0; position: sticky; top: 0; z-index: 100; backdrop-filter: blur(10px); }
        .admin-nav__links { display: flex; gap: 24px; align-items: center; list-style: none; margin: 0; padding: 0; }
        .admin-nav__links a { color: #aaa; text-decoration: none; font-size: 0.9rem; transition: color 0.2s; }
        .admin-nav__links a:hover, .admin-nav__links a.is-active { color: var(--accent); font-weight: 600; }
        .admin-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 24px; }
        table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.9rem; vertical-align: middle; }
        th { color: var(--ink-40); font-weight: 500; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pill { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.5px; }
        .status-pill--approved { background: rgba(52, 211, 153, 0.15); color: #34D399; border: 1px solid rgba(52, 211, 153, 0.3); }
        .status-pill--pending { background: rgba(245, 158, 11, 0.15); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.3); }
        .status-pill--rejected { background: rgba(239, 68, 68, 0.15); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.3); }
        .filter-btn { display: inline-flex; align-items: center; gap: 6px; padding: 6px 14px; border-radius: 8px; font-size: 0.82rem; text-decoration: none; color: #aaa; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); transition: all 0.2s; }
        .filter-btn:hover, .filter-btn.is-active { background: var(--accent); color: #fff; border-color: var(--accent); }
    </style>
</head>
<body class="theme-magenta">

    <header class="admin-nav">
        <div class="container-vebra">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div style="display: flex; align-items: center; gap: 32px;">
                    <a href="index.php" style="font-family: 'Unbounded', sans-serif; font-weight: 800; font-size: 1.25rem; color: #fff; text-decoration: none;">
                        vebra<span style="color:var(--accent);">.</span> <span style="font-size:0.75rem; font-weight:400; opacity:0.6; text-transform:uppercase; letter-spacing:1px; margin-left:6px;">Admin</span>
                    </a>
                    <ul class="admin-nav__links">
                        <li><a href="index.php">Dashboard</a></li>
                        <li><a href="songs.php">Songs</a></li>
                        <li><a href="videos.php">Videos</a></li>
                        <li><a href="artists.php">Artists</a></li>
                        <li><a href="reviews.php" class="is-active">Reviews</a></li>
                        <li><a href="orders.php">Orders</a></li>
                        <li><a href="users.php">Users</a></li>
                    </ul>
                </div>
                <div style="display: flex; align-items: center; gap: 16px;">
                    <a href="../index.php" class="btn btn--glass btn--sm" target="_blank"><span class="btn__label"><i class="ri-external-link-line"></i> View Site</span></a>
                    <a href="../auth/logout.php" class="btn btn--solid btn--sm" style="background: rgba(255,255,255,0.1);"><span class="btn__label"><i class="ri-logout-box-r-line"></i> Exit</span></a>
                </div>
            </div>
        </div>
    </header>

    <main style="padding: 40px 0 80px;">
        <div class="container-vebra">

            <div style="margin-bottom: 24px;">
                <h1 style="font-family: 'Unbounded', sans-serif; font-size: 1.8rem; margin-bottom: 8px;">
                    Listener <em>Reviews &amp; Ratings</em>
                </h1>
                <p style="color: var(--ink-40); margin: 0;">Moderate, approve, reject, or delete user reviews across all tracks.</p>
            </div>

            <!-- Filter tabs -->
            <div style="display: flex; gap: 10px; margin-bottom: 24px; flex-wrap: wrap;">
                <a href="reviews.php?filter=all" class="filter-btn <?= $filter === 'all' ? 'is-active' : '' ?>">All (<?= $cntAll ?>)</a>
                <a href="reviews.php?filter=pending" class="filter-btn <?= $filter === 'pending' ? 'is-active' : '' ?>">Pending (<?= $cntPending ?>)</a>
                <a href="reviews.php?filter=approved" class="filter-btn <?= $filter === 'approved' ? 'is-active' : '' ?>">Approved (<?= $cntApproved ?>)</a>
                <a href="reviews.php?filter=rejected" class="filter-btn <?= $filter === 'rejected' ? 'is-active' : '' ?>">Rejected (<?= $cntRejected ?>)</a>
            </div>

            <?php if ($notice): ?>
                <div style="background: rgba(var(--accent-rgb, 255, 45, 85), 0.15); border: 1px solid var(--accent); padding: 14px 18px; border-radius: 6px; margin-bottom: 24px; color: #fff;">
                    <i class="ri-checkbox-circle-fill" style="color:var(--accent); margin-right:8px;"></i> <?= htmlspecialchars($notice) ?>
                </div>
            <?php endif; ?>

            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                    <h2 style="font-family: 'Unbounded', sans-serif; font-size: 1.15rem; margin: 0;">
                        Reviews (<?= count($allReviews) ?>)
                    </h2>
                </div>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>User</th>
                                <th>Track</th>
                                <th>Rating</th>
                                <th>Review Text</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th style="text-align: right;">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allReviews)): ?>
                                <tr><td colspan="7" style="text-align: center; color: var(--ink-40); padding: 30px;">No reviews found for this filter.</td></tr>
                            <?php else: ?>
                                <?php foreach ($allReviews as $rev): ?>
                                    <tr>
                                        <td>
                                            <strong>@<?= htmlspecialchars($rev['username']) ?></strong>
                                            <div style="font-size:0.75rem; color:var(--ink-40);"><?= htmlspecialchars($rev['email']) ?></div>
                                        </td>
                                        <td>
                                            <a href="../track.php?id=<?= $rev['song_id'] ?>" target="_blank" style="color:#fff; text-decoration:none;">
                                                <strong><?= htmlspecialchars($rev['song_title']) ?></strong>
                                            </a>
                                            <div style="font-size:0.75rem; color:var(--ink-40);"><?= htmlspecialchars($rev['artist'] ?? 'Unknown Artist') ?></div>
                                        </td>
                                        <td>
                                            <span style="color: #F59E0B; font-size: 0.95rem;">
                                                <?php for ($i=1; $i<=5; $i++): ?>
                                                    <?= $i <= $rev['rating'] ? '★' : '☆' ?>
                                                <?php endfor; ?>
                                            </span>
                                        </td>
                                        <td style="max-width: 300px;">
                                            <div style="font-size: 0.88rem; color: #ddd;">
                                                <?= nl2br(htmlspecialchars($rev['review'] ?: 'No comment left.')) ?>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if ($rev['status'] === 'approved'): ?>
                                                <span class="status-pill status-pill--approved"><i class="ri-check-line"></i> Approved</span>
                                            <?php elseif ($rev['status'] === 'pending'): ?>
                                                <span class="status-pill status-pill--pending"><i class="ri-time-line"></i> Pending</span>
                                            <?php else: ?>
                                                <span class="status-pill status-pill--rejected"><i class="ri-close-line"></i> Rejected</span>
                                            <?php endif; ?>
                                        </td>
                                        <td style="font-size: 0.8rem; color: var(--ink-40); white-space: nowrap;">
                                            <?= htmlspecialchars(substr($rev['created_at'], 0, 10)) ?>
                                        </td>
                                        <td style="text-align: right; white-space: nowrap;">
                                            <div style="display: inline-flex; gap: 6px; align-items: center;">
                                                <?php if ($rev['status'] !== 'approved'): ?>
                                                    <form method="POST" action="" style="display:inline;">
                                                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                                        <button type="submit" name="approve_review" value="1" class="btn btn--solid btn--sm" style="padding: 4px 10px; font-size: 0.75rem; background: rgba(52, 211, 153, 0.2); border: 1px solid #34D399; color: #34D399;" title="Approve this review">
                                                            Approve
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <?php if ($rev['status'] !== 'rejected'): ?>
                                                    <form method="POST" action="" style="display:inline;">
                                                        <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                                        <button type="submit" name="reject_review" value="1" class="btn btn--solid btn--sm" style="padding: 4px 10px; font-size: 0.75rem; background: rgba(245, 158, 11, 0.2); border: 1px solid #F59E0B; color: #F59E0B;" title="Reject this review">
                                                            Reject
                                                        </button>
                                                    </form>
                                                <?php endif; ?>

                                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Remove this review permanently?');">
                                                    <input type="hidden" name="delete_review" value="1">
                                                    <input type="hidden" name="review_id" value="<?= $rev['id'] ?>">
                                                    <button type="submit" class="btn btn--solid btn--sm" style="padding: 4px 10px; font-size: 0.75rem; background: rgba(239, 68, 68, 0.2); border: 1px solid #EF4444; color: #EF4444;" title="Delete permanently">
                                                        Delete
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
