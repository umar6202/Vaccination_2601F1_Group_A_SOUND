<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/upload_helper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strict Admin Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php?redirect=' . urlencode('../admin/artists.php'));
    exit;
}

$notice = '';
$error = '';
$editingArtist = null;

// Handle Actions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD ARTIST
    if ($action === 'add') {
        $name = trim($_POST['name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $imagePath = trim($_POST['existing_image'] ?? '');

        // Handle File Upload if provided
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadRes = vebra_handle_upload($_FILES['image_file'], 'image', 'assets/media/artists');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];
            } else {
                $error = $uploadRes['error'];
            }
        }

        if (empty($name)) {
            $error = 'Artist name is required.';
        } elseif (!$error) {
            if (empty($imagePath)) {
                $imagePath = 'assets/images/team-1.jpg'; // default fallback
            }
            try {
                $stmt = mysqli_prepare($conn, "INSERT INTO artists (name, bio, image) VALUES (?, ?, ?)");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "sss", $name, $bio, $imagePath);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $notice = 'Artist "' . htmlspecialchars($name) . '" successfully added to the VEBRA roster.';
                } else {
                    $error = 'Failed to prepare add artist statement.';
                }
            } catch (Exception $e) {
                $error = 'Failed to create artist: ' . $e->getMessage();
            }
        }
    }

    // EDIT ARTIST
    if ($action === 'edit') {
        $id = (int)($_POST['artist_id'] ?? 0);
        $name = trim($_POST['name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');
        $imagePath = trim($_POST['image_path'] ?? '');

        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadRes = vebra_handle_upload($_FILES['image_file'], 'image', 'assets/media/artists');
            if ($uploadRes['success']) {
                $imagePath = $uploadRes['path'];
            } else {
                $error = $uploadRes['error'];
            }
        }

        $status = trim($_POST['status'] ?? 'approved');
        if (!in_array($status, ['approved', 'pending', 'rejected'], true)) {
            $status = 'approved';
        }

        if (empty($name)) {
            $error = 'Artist name cannot be empty.';
        } elseif (!$error) {
            try {
                $stmt = mysqli_prepare($conn, "UPDATE artists SET name = ?, bio = ?, image = ?, status = ? WHERE id = ?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "ssssi", $name, $bio, $imagePath, $status, $id);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $notice = 'Artist details updated successfully.';
                } else {
                    $error = 'Failed to prepare update artist statement.';
                }
            } catch (Exception $e) {
                $error = 'Failed to update artist: ' . $e->getMessage();
            }
        }
    }

    // DELETE ARTIST
    if ($action === 'delete') {
        $id = (int)($_POST['artist_id'] ?? 0);
        try {
            $stmt = mysqli_prepare($conn, "DELETE FROM artists WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $notice = 'Artist removed successfully.';
            } else {
                $error = 'Failed to prepare delete artist statement.';
            }
        } catch (Exception $e) {
            $error = 'Failed to delete artist: ' . $e->getMessage();
        }
    }
}

// Fetch artist for editing if requested
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmt = mysqli_prepare($conn, "SELECT * FROM artists WHERE id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $editId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $editingArtist = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
    }
}

// Fetch all artists with release count
$artists = [];
$res = mysqli_query($conn, "
    SELECT a.*, COUNT(s.id) AS song_count 
    FROM artists a 
    LEFT JOIN songs s ON s.artist_id = a.id 
    GROUP BY a.id 
    ORDER BY a.id ASC
");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $artists[] = $row;
    }
}

// List available preset images from assets/images/team-*.jpg
$presetImages = glob(dirname(__DIR__) . '/assets/images/team-*.jpg');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Artists — VEBRA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../favicon.svg" rel="icon" type="image/svg+xml">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;700;800&family=Inter:wght@400;500;600&display=swap">
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap-grid.min.css">
    <link rel="stylesheet" href="../assets/icon-fonts/remixicon/remixicon.css">
    <link rel="stylesheet" href="../assets/css/main.css">
    <style>
        body { background: #08080C; color: #fff; font-family: 'Inter', sans-serif; }
        .admin-nav { background: rgba(14, 14, 20, 0.96); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 14px 0; position: sticky; top: 0; z-index: 100; backdrop-filter: blur(12px); }
        .admin-nav__links { display: flex; gap: 20px; align-items: center; list-style: none; margin: 0; padding: 0; }
        .admin-nav__links a { color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500; transition: color 0.2s; padding: 6px 0; position: relative; }
        .admin-nav__links a:hover, .admin-nav__links a.is-active { color: var(--accent, #FF2E93); font-weight: 600; }
        .admin-nav__links a.is-active::after { content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 2px; background: var(--accent, #FF2E93); border-radius: 2px; }
        
        .admin-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 14px; padding: 24px; box-shadow: 0 10px 30px rgba(0,0,0,0.3); backdrop-filter: blur(10px); margin-bottom: 24px; }
        .admin-card__title { font-family: 'Unbounded', sans-serif; font-size: 1.1rem; font-weight: 700; text-transform: uppercase; margin: 0 0 18px; }
        
        .form-label { display: block; font-size: 0.82rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.05em; color: rgba(255,255,255,0.6); margin-bottom: 6px; }
        .form-control, .form-select { width: 100%; background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.14); border-radius: 8px; color: #fff; padding: 10px 14px; font-size: 0.92rem; outline: none; transition: border-color 0.2s; }
        .form-control:focus, .form-select:focus { border-color: var(--accent, #FF2E93); }
        .form-select option { background: #121218; color: #fff; }
        
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.9rem; vertical-align: middle; }
        th { color: rgba(255,255,255,0.45); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }
        
        .artist-avatar { width: 44px; height: 44px; border-radius: 8px; object-fit: cover; background: #222; }
    </style>
</head>
<body class="theme-magenta">

    <header class="admin-nav">
        <div class="container-vebra">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%;">
                <div style="display: flex; align-items: center; gap: 28px;">
                    <a href="index.php" style="font-family: 'Unbounded', sans-serif; font-weight: 800; font-size: 1.2rem; color: #fff; text-decoration: none;">
                        vebra<span style="color:var(--accent, #FF2E93);">.</span> <span style="font-size:0.7rem; font-weight:600; opacity:0.6; text-transform:uppercase; letter-spacing:1px; margin-left:6px; vertical-align: middle;">Control</span>
                    </a>
                    <ul class="admin-nav__links">
                        <li><a href="index.php"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                        <li><a href="songs.php"><i class="ri-music-2-line"></i> Songs</a></li>
                        <li><a href="videos.php"><i class="ri-video-line"></i> Videos</a></li>
                        <li><a href="artists.php" class="is-active"><i class="ri-user-star-line"></i> Artists</a></li>
                        <li><a href="artist-requests.php"><i class="ri-user-follow-line"></i> Requests</a></li>
                        <li><a href="bookings.php"><i class="ri-ticket-line"></i> Bookings</a></li>
                        <li><a href="orders.php"><i class="ri-shopping-bag-3-line"></i> Orders</a></li>
                        <li><a href="reviews.php"><i class="ri-chat-smile-2-line"></i> Reviews</a></li>
                        <li><a href="users.php"><i class="ri-user-settings-line"></i> Users</a></li>
                    </ul>
                </div>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <a href="../index.php" class="btn btn--glass btn--sm" target="_blank"><span class="btn__label"><i class="ri-external-link-line"></i> View Site</span></a>
                    <a href="../auth/logout.php" class="btn btn--solid btn--sm" style="background: rgba(255,255,255,0.08);"><span class="btn__label"><i class="ri-logout-box-r-line"></i> Logout</span></a>
                </div>
            </div>
        </div>
    </header>

    <main style="padding: 36px 0 80px;">
        <div class="container-vebra">

            <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
                <div>
                    <h1 style="font-family: 'Unbounded', sans-serif; font-size: 1.8rem; font-weight: 800; text-transform: uppercase; margin: 0 0 6px;">
                        Artist <em>Roster</em>
                    </h1>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.9rem; margin: 0;">Manage performers, band line-up, bios, and portrait assets.</p>
                </div>
            </div>

            <?php if ($notice): ?>
                <div style="background: rgba(var(--accent-rgb, 255, 46, 147), 0.12); border: 1px solid var(--accent, #FF2E93); color: #fff; padding: 12px 18px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                    <i class="ri-checkbox-circle-fill" style="color:var(--accent, #FF2E93);"></i>
                    <span><?= htmlspecialchars($notice) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div style="background: rgba(255, 60, 60, 0.12); border: 1px solid rgba(255, 60, 60, 0.4); color: #ff8585; padding: 12px 18px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                    <i class="ri-error-warning-fill" style="color:#ff5c5c;"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <!-- ADD OR EDIT ARTIST FORM -->
            <div class="admin-card">
                <h2 class="admin-card__title">
                    <?= $editingArtist ? 'Edit Artist Details: ' . htmlspecialchars($editingArtist['name']) : 'Add New Artist' ?>
                </h2>
                <form method="POST" action="artists.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $editingArtist ? 'edit' : 'add' ?>">
                    <?php if ($editingArtist): ?>
                        <input type="hidden" name="artist_id" value="<?= (int)$editingArtist['id'] ?>">
                        <input type="hidden" name="image_path" value="<?= htmlspecialchars($editingArtist['image'] ?? '') ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-5">
                            <label class="form-label" for="artist-name">Artist / Member Name *</label>
                            <input type="text" name="name" id="artist-name" class="form-control" placeholder="e.g. Mara Vance" value="<?= htmlspecialchars($editingArtist['name'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="artist-image-file">Portrait Photo (Upload or Preset)</label>
                            <input type="file" name="image_file" id="artist-image-file" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="artist-status">Status</label>
                            <select name="status" id="artist-status" class="form-select">
                                <option value="approved" <?= ($editingArtist['status'] ?? 'approved') === 'approved' ? 'selected' : '' ?>>Approved</option>
                                <option value="pending" <?= ($editingArtist['status'] ?? '') === 'pending' ? 'selected' : '' ?>>Pending</option>
                                <option value="rejected" <?= ($editingArtist['status'] ?? '') === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label" for="artist-bio">Biography / Role Description</label>
                            <textarea name="bio" id="artist-bio" class="form-control" rows="3" placeholder="Primary instrument, stage history, gear notes..."><?= htmlspecialchars($editingArtist['bio'] ?? '') ?></textarea>
                        </div>
                    </div>

                    <div style="margin-top: 18px; display: flex; gap: 12px; align-items: center;">
                        <button type="submit" class="btn btn--solid btn--sm">
                            <span class="btn__label"><i class="ri-save-line"></i> <?= $editingArtist ? 'Update Artist' : 'Save Artist' ?></span>
                        </button>
                        <?php if ($editingArtist): ?>
                            <a href="artists.php" class="btn btn--glass btn--sm"><span class="btn__label">Cancel</span></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- ARTISTS TABLE -->
            <div class="admin-card">
                <h2 class="admin-card__title">Current Roster (<?= count($artists) ?>)</h2>
                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Photo</th>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Role / Bio</th>
                                <th>Status</th>
                                <th>Tracks</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($artists as $art): 
                                $artSt = $art['status'] ?? 'approved';
                            ?>
                                <tr>
                                    <td>
                                        <img src="../<?= !empty($art['image']) ? htmlspecialchars($art['image']) : 'assets/images/team-1.jpg' ?>" alt="" class="artist-avatar">
                                    </td>
                                    <td>#<?= (int)$art['id'] ?></td>
                                    <td>
                                        <strong><?= htmlspecialchars($art['name']) ?></strong>
                                    </td>
                                    <td style="max-width: 320px; color: rgba(255,255,255,0.7); font-size: 0.85rem;">
                                        <?= htmlspecialchars(substr($art['bio'] ?? '', 0, 110)) ?><?= strlen($art['bio'] ?? '') > 110 ? '...' : '' ?>
                                    </td>
                                    <td>
                                        <?php if ($artSt === 'approved'): ?>
                                            <span style="display:inline-block; padding:3px 8px; border-radius:999px; font-size:0.75rem; background:rgba(16,185,129,0.15); color:#10B981; border:1px solid rgba(16,185,129,0.3);">Approved</span>
                                        <?php elseif ($artSt === 'pending'): ?>
                                            <span style="display:inline-block; padding:3px 8px; border-radius:999px; font-size:0.75rem; background:rgba(245,158,11,0.15); color:#F59E0B; border:1px solid rgba(245,158,11,0.3);">Pending</span>
                                        <?php else: ?>
                                            <span style="display:inline-block; padding:3px 8px; border-radius:999px; font-size:0.75rem; background:rgba(239,68,68,0.15); color:#EF4444; border:1px solid rgba(239,68,68,0.3);">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong><?= (int)$art['song_count'] ?></strong> release(s)
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <a href="artists.php?edit=<?= (int)$art['id'] ?>" class="btn btn--glass btn--sm" style="padding: 6px 12px;">
                                                <span class="btn__label"><i class="ri-edit-line"></i> Edit</span>
                                            </a>
                                            <form method="POST" action="artists.php" onsubmit="return confirm('Are you sure you want to delete this artist? Associated songs will remain with unassigned artist.');" style="display: inline;">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="artist_id" value="<?= (int)$art['id'] ?>">
                                                <button type="submit" class="btn btn--sm" style="background: rgba(255,60,60,0.15); color:#ff7a7a; border:1px solid rgba(255,60,60,0.3); padding: 6px 12px;">
                                                    <span class="btn__label"><i class="ri-delete-bin-line"></i></span>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>
</html>
