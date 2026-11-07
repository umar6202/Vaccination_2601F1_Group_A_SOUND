<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/upload_helper.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin security guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php?redirect=' . urlencode('../admin/videos.php'));
    exit;
}

$notice = '';
$error = '';
$editingVideo = null;

// Handle Actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    // ADD VIDEO
    if ($action === 'add') {
        $title = trim($_POST['title'] ?? '');
        $artistId = intval($_POST['artist_id'] ?? 0);
        $genreId = intval($_POST['genre_id'] ?? 0);
        $year = trim($_POST['year'] ?? 2026);
        $language = trim($_POST['language'] ?? 'English');
        $videoPath = trim($_POST['video_path'] ?? '');
        $videoUrl = trim($_POST['video_url'] ?? '');
        $thumbnail = trim($_POST['thumbnail'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['approved', 'pending', 'rejected']) ? $_POST['status'] : 'approved';

        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upVid = vebra_handle_upload($_FILES['video_file'], 'video', 'assets/media/videos');
            if ($upVid['success']) {
                $videoPath = $upVid['path'];
            } else {
                $error = $upVid['error'];
            }
        }

        if (isset($_FILES['thumb_file']) && $_FILES['thumb_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upThumb = vebra_handle_upload($_FILES['thumb_file'], 'image', 'assets/media/video-thumbs');
            if ($upThumb['success']) {
                $thumbnail = $upThumb['path'];
            } else {
                $error = $upThumb['error'];
            }
        }

        if (empty($title)) {
            $error = 'Video title is required.';
        } elseif (!$error) {
            if (empty($thumbnail)) $thumbnail = 'assets/images/video-cover-1.jpg';

            try {
                $stmt = mysqli_prepare($conn, "INSERT INTO videos (title, artist_id, genre_id, release_year, language, video_path, video_url, thumbnail, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $artistIdParam = $artistId ?: null;
                    $genreIdParam = $genreId ?: null;
                    $yearParam = $year ?: null;
                    mysqli_stmt_bind_param($stmt, "siissssss", $title, $artistIdParam, $genreIdParam, $yearParam, $language, $videoPath, $videoUrl, $thumbnail, $status);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $notice = 'Video "' . htmlspecialchars($title) . '" successfully added to library!';
                }
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }

    // EDIT VIDEO
    if ($action === 'edit') {
        $vidId = intval($_POST['video_id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $artistId = intval($_POST['artist_id'] ?? 0);
        $genreId = intval($_POST['genre_id'] ?? 0);
        $year = trim($_POST['year'] ?? 2026);
        $language = trim($_POST['language'] ?? 'English');
        $videoPath = trim($_POST['video_path'] ?? '');
        $videoUrl = trim($_POST['video_url'] ?? '');
        $thumbnail = trim($_POST['thumbnail'] ?? '');
        $status = in_array($_POST['status'] ?? '', ['approved', 'pending', 'rejected']) ? $_POST['status'] : 'approved';

        $existingStmt = mysqli_prepare($conn, "SELECT video_path, video_url, thumbnail FROM videos WHERE id = ? LIMIT 1");
        if ($existingStmt) {
            mysqli_stmt_bind_param($existingStmt, "i", $vidId);
            mysqli_stmt_execute($existingStmt);
            $resEx = mysqli_stmt_get_result($existingStmt);
            $existing = mysqli_fetch_assoc($resEx);
            mysqli_stmt_close($existingStmt);

            if ($existing) {
                if ($videoPath === '') $videoPath = $existing['video_path'];
                if ($videoUrl === '') $videoUrl = $existing['video_url'];
                if ($thumbnail === '') $thumbnail = $existing['thumbnail'];
            }
        }

        if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upVid = vebra_handle_upload($_FILES['video_file'], 'video', 'assets/media/videos');
            if ($upVid['success']) {
                $videoPath = $upVid['path'];
            } else {
                $error = $upVid['error'];
            }
        }

        if (isset($_FILES['thumb_file']) && $_FILES['thumb_file']['error'] !== UPLOAD_ERR_NO_FILE) {
            $upThumb = vebra_handle_upload($_FILES['thumb_file'], 'image', 'assets/media/video-thumbs');
            if ($upThumb['success']) {
                $thumbnail = $upThumb['path'];
            } else {
                $error = $upThumb['error'];
            }
        }

        if (empty($title)) {
            $error = 'Video title cannot be empty.';
        } elseif (!$error) {
            try {
                $stmt = mysqli_prepare($conn, "UPDATE videos SET title = ?, artist_id = ?, genre_id = ?, release_year = ?, language = ?, video_path = ?, video_url = ?, thumbnail = ?, status = ? WHERE id = ?");
                if ($stmt) {
                    $artistIdParam = $artistId ?: null;
                    $genreIdParam = $genreId ?: null;
                    $yearParam = $year ?: null;
                    mysqli_stmt_bind_param($stmt, "siissssssi", $title, $artistIdParam, $genreIdParam, $yearParam, $language, $videoPath, $videoUrl, $thumbnail, $status, $vidId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $notice = 'Video details updated successfully.';
                }
            } catch (Exception $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }

    // TOGGLE APPROVAL
    if ($action === 'toggle_approval') {
        $vidId = intval($_POST['video_id'] ?? 0);
        $currStatus = $_POST['current_status'] ?? 'pending';
        $newStatus = ($currStatus === 'approved') ? 'pending' : 'approved';
        $stmt = mysqli_prepare($conn, "UPDATE videos SET status = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $newStatus, $vidId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notice = 'Video approval status updated.';
        }
    }

    // DELETE VIDEO
    if ($action === 'delete') {
        $vidId = intval($_POST['video_id'] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM videos WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $vidId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notice = 'Video removed from library.';
        }
    }
}

// Fetch for edit
if (isset($_GET['edit'])) {
    $editId = intval($_GET['edit']);
    $stmt = mysqli_prepare($conn, "SELECT * FROM videos WHERE id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $editId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $editingVideo = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
    }
}

$filter = $_GET['filter'] ?? 'all';
$querySql = "SELECT v.*, a.name AS artist_name, g.name AS genre_name FROM videos v LEFT JOIN artists a ON a.id = v.artist_id LEFT JOIN genres g ON g.id = v.genre_id";
if ($filter === 'pending') $querySql .= " WHERE v.status = 'pending'";
elseif ($filter === 'approved') $querySql .= " WHERE v.status = 'approved'";
$querySql .= " ORDER BY v.id DESC";

$allVideos = [];
$resV = mysqli_query($conn, $querySql);
if ($resV) {
    while ($row = mysqli_fetch_assoc($resV)) {
        $allVideos[] = $row;
    }
}

$cntAll = 0; $cntPending = 0; $cntApproved = 0;
$resCnt = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM videos GROUP BY status");
if ($resCnt) {
    while ($row = mysqli_fetch_assoc($resCnt)) {
        if ($row['status'] === 'pending') $cntPending = (int)$row['cnt'];
        elseif ($row['status'] === 'approved') $cntApproved = (int)$row['cnt'];
        $cntAll += (int)$row['cnt'];
    }
}

$genres = [];
$resG = mysqli_query($conn, "SELECT id, name FROM genres ORDER BY name ASC");
if ($resG) { while($row = mysqli_fetch_assoc($resG)) { $genres[] = $row; } }
$artists = [];
$resA = mysqli_query($conn, "SELECT id, name FROM artists ORDER BY name ASC");
if ($resA) { while($row = mysqli_fetch_assoc($resA)) { $artists[] = $row; } }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Videos — VEBRA Admin</title>
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
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.88rem; vertical-align: middle; }
        th { color: rgba(255,255,255,0.45); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }
        
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.04em; }
        .badge--success { background: rgba(46, 204, 113, 0.18); color: #2ecc71; border: 1px solid rgba(46, 204, 113, 0.4); }
        .badge--warning { background: rgba(241, 196, 15, 0.18); color: #f1c40f; border: 1px solid rgba(241, 196, 15, 0.4); }
        .badge--danger { background: rgba(231, 76, 60, 0.18); color: #e74c3c; border: 1px solid rgba(231, 76, 60, 0.4); }

        .filter-nav { display: flex; gap: 8px; margin-bottom: 18px; }
        .filter-btn { padding: 6px 14px; border-radius: 20px; font-size: 0.8rem; font-weight: 600; text-decoration: none; color: rgba(255,255,255,0.6); background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.08); transition: all 0.2s; }
        .filter-btn:hover, .filter-btn.is-active { color: #fff; background: var(--accent, #FF2E93); border-color: var(--accent, #FF2E93); }
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
                        <li><a href="videos.php" class="is-active"><i class="ri-video-line"></i> Videos</a></li>
                        <li><a href="artists.php"><i class="ri-user-star-line"></i> Artists</a></li>
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

            <div style="margin-bottom: 24px;">
                <h1 style="font-family: 'Unbounded', sans-serif; font-size: 1.8rem; font-weight: 800; text-transform: uppercase; margin: 0 0 6px;">
                    Music <em>Videos</em>
                </h1>
                <p style="color: rgba(255,255,255,0.5); font-size: 0.9rem; margin: 0;">Upload and organize MP4 videos, cover thumbnails, and external stream URLs.</p>
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

            <!-- ADD OR EDIT VIDEO FORM -->
            <div class="admin-card">
                <h2 class="admin-card__title">
                    <i class="ri-<?= $editingVideo ? 'edit-2-line' : 'video-add-line' ?>" style="color:var(--accent, #FF2E93); margin-right:6px;"></i>
                    <?= $editingVideo ? 'Edit Video: ' . htmlspecialchars($editingVideo['title']) : 'Add New Video' ?>
                </h2>
                <form method="POST" action="videos.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $editingVideo ? 'edit' : 'add' ?>">
                    <?php if ($editingVideo): ?>
                        <input type="hidden" name="video_id" value="<?= (int)$editingVideo['id'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="vid-title">Video Title *</label>
                            <input type="text" name="title" id="vid-title" class="form-control" placeholder="e.g. Nightform — Official Video" value="<?= htmlspecialchars($editingVideo['title'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="vid-artist">Artist *</label>
                            <select name="artist_id" id="vid-artist" class="form-select">
                                <option value="0">— Select Artist —</option>
                                <?php foreach ($artists as $ar): ?>
                                    <option value="<?= $ar['id'] ?>" <?= ($editingVideo && (int)$editingVideo['artist_id'] === (int)$ar['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ar['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="vid-genre">Genre</label>
                            <select name="genre_id" id="vid-genre" class="form-select">
                                <option value="0">— Select Genre —</option>
                                <?php foreach ($genres as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= ($editingVideo && (int)$editingVideo['genre_id'] === (int)$g['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="vid-year">Release Year</label>
                            <input type="number" name="year" id="vid-year" class="form-control" value="<?= htmlspecialchars($editingVideo['release_year'] ?? 2026) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="vid-lang">Language</label>
                            <input type="text" name="language" id="vid-lang" class="form-control" value="<?= htmlspecialchars($editingVideo['language'] ?? 'English') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="vid-status">Moderation Status</label>
                            <select name="status" id="vid-status" class="form-select">
                                <option value="approved" <?= ($editingVideo && $editingVideo['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                                <option value="pending" <?= ($editingVideo && $editingVideo['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="rejected" <?= ($editingVideo && $editingVideo['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Thumbnail Image Upload</label>
                            <input type="file" name="thumbnail_file" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>
<div class="col-md-6">
    <label class="form-label" for="vid-url">
        YouTube Video URL
    </label>

    <input
        type="url"
        name="video_url"
        id="vid-url"
        class="form-control"
        placeholder="https://www.youtube.com/watch?v=..."
        value="<?= htmlspecialchars($editingVideo['video_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>"
    >

    <small style="color:rgba(255,255,255,.45);display:block;margin-top:6px;">
        Enter an official YouTube link, or upload an MP4 file.
    </small>
</div>
                        <div class="col-md-6">
                            <label class="form-label" for="vid-file">Video File Upload (MP4, WebM)</label>
                            <input type="file" name="video_file" id="vid-file" class="form-control" accept="video/mp4,video/webm">
                            <input type="hidden" name="video_path" value="<?= htmlspecialchars($editingVideo['video_path'] ?? '') ?>">
                            <?php if ($editingVideo && !empty($editingVideo['video_path'])): ?>
                                <span style="font-size:0.75rem; color:rgba(255,255,255,0.4); display:block; margin-top:4px;">Current file: <?= htmlspecialchars($editingVideo['video_path']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="vid-thumb-path">Or Preset Thumbnail Path</label>
                            <input type="text" name="thumbnail" id="vid-thumb-path" class="form-control" value="<?= htmlspecialchars($editingVideo['thumbnail'] ?? 'assets/images/video-cover-1.jpg') ?>">
                        </div>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 12px; align-items: center;">
                        <button type="submit" class="btn btn--solid btn--sm">
                            <span class="btn__label"><i class="ri-save-line"></i> <?= $editingVideo ? 'Update Video' : 'Save &amp; Publish Video' ?></span>
                        </button>
                        <?php if ($editingVideo): ?>
                            <a href="videos.php" class="btn btn--glass btn--sm"><span class="btn__label">Cancel Edit</span></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- VIDEOS LIST WITH FILTER PILLS -->
            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                    <h2 class="admin-card__title" style="margin: 0;">Catalog Videos (<?= count($allVideos) ?>)</h2>
                    <div class="filter-nav">
                        <a href="videos.php?filter=all" class="filter-btn <?= $filter === 'all' ? 'is-active' : '' ?>">All (<?= $cntAll ?>)</a>
                        <a href="videos.php?filter=pending" class="filter-btn <?= $filter === 'pending' ? 'is-active' : '' ?>">Pending (<?= $cntPending ?>)</a>
                        <a href="videos.php?filter=approved" class="filter-btn <?= $filter === 'approved' ? 'is-active' : '' ?>">Approved (<?= $cntApproved ?>)</a>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Thumb</th>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Artist</th>
                                <th>Genre</th>
                                <th>Year</th>
                                <th>Source / Stream</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allVideos)): ?>
                                <tr><td colspan="9" style="text-align: center; color: rgba(255,255,255,0.4); padding: 24px;">No videos found for this filter.</td></tr>
                            <?php else: ?>
                                <?php foreach ($allVideos as $vid): ?>
                                    <tr>
                                        <td style="width: 70px;">
                                            <img src="../<?= htmlspecialchars($vid['thumbnail'] ?: 'assets/images/video-cover-1.jpg') ?>" alt="" style="width: 60px; height: 38px; border-radius: 4px; object-fit: cover;">
                                        </td>
                                        <td>#<?= (int)$vid['id'] ?></td>
                                        <td><strong><?= htmlspecialchars($vid['title']) ?></strong></td>
                                        <td><?= htmlspecialchars($vid['artist_name'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($vid['genre_name'] ?? '—') ?></td>
                                        <td><?= htmlspecialchars($vid['release_year'] ?: '—') ?></td>
                                        <td style="font-size:0.78rem;">
                                            <?php if (!empty($vid['video_url'])): ?>
                                                <a href="<?= htmlspecialchars($vid['video_url']) ?>" target="_blank" rel="noopener noreferrer" style="color:var(--accent, #FF2E93); text-decoration:none; display:inline-flex; align-items:center; gap:4px;">
                                                    <i class="ri-youtube-fill"></i> YouTube
                                                </a>
                                            <?php elseif (!empty($vid['video_path'])): ?>
                                                <span title="<?= htmlspecialchars($vid['video_path']) ?>" style="color:rgba(255,255,255,0.7); display:inline-flex; align-items:center; gap:4px;">
                                                    <i class="ri-film-line"></i> <?= htmlspecialchars(basename($vid['video_path'])) ?>
                                                </span>
                                            <?php else: ?>
                                                <span style="color:rgba(255,255,255,0.3);">—</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($vid['status'] === 'approved'): ?>
                                                <span class="badge badge--success"><i class="ri-check-line"></i> Approved</span>
                                            <?php elseif ($vid['status'] === 'pending'): ?>
                                                <span class="badge badge--warning"><i class="ri-time-line"></i> Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge--danger"><?= htmlspecialchars(ucfirst($vid['status'])) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 6px; align-items: center;">
                                                <form method="POST" action="videos.php" style="display:inline;">
                                                    <input type="hidden" name="action" value="toggle_approval">
                                                    <input type="hidden" name="video_id" value="<?= (int)$vid['id'] ?>">
                                                    <input type="hidden" name="current_status" value="<?= htmlspecialchars($vid['status']) ?>">
                                                    <button type="submit" class="btn btn--glass btn--sm" style="padding: 4px 10px; font-size: 0.75rem;">
                                                        <?= $vid['status'] === 'approved' ? 'Reject' : 'Approve' ?>
                                                    </button>
                                                </form>

                                                <a href="videos.php?edit=<?= (int)$vid['id'] ?>" class="btn btn--glass btn--sm" style="padding: 4px 10px; font-size: 0.75rem;">
                                                    <i class="ri-edit-line"></i> Edit
                                                </a>

                                                <a href="../single-video.php?id=<?= (int)$vid['id'] ?>" class="btn btn--glass btn--sm" style="padding: 4px 10px; font-size: 0.75rem;" target="_blank">
                                                    <i class="ri-external-link-line"></i>
                                                </a>

                                                <form method="POST" action="videos.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this video?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="video_id" value="<?= (int)$vid['id'] ?>">
                                                    <button type="submit" class="btn btn--sm" style="padding: 4px 10px; font-size: 0.75rem; background: rgba(255,60,60,0.15); color:#ff7a7a; border: 1px solid rgba(255,60,60,0.3);">
                                                        <i class="ri-delete-bin-line"></i>
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
