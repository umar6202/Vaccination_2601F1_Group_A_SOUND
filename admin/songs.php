<?php
require_once __DIR__ . "/../includes/db.php";
require_once __DIR__ . "/upload_helper.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin security guard
if (!isset($_SESSION["user_id"]) || ($_SESSION["role"] ?? "") !== "admin") {
    header("Location: ../auth/login.php?redirect=" . urlencode("../admin/songs.php"));
    exit;
}

$notice = "";
$error = "";
$editingSong = null;

// Handle Actions
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    // ADD SONG
    if ($action === "add") {
        $title = trim($_POST["title"] ?? "");
        $artistId = intval($_POST["artist_id"] ?? 0);
        $genreId = intval($_POST["genre_id"] ?? 0);
        $year = trim($_POST["year"] ?? 2026);
        $language = trim($_POST["language"] ?? "English");
        $audioPath = trim($_POST["audio_path"] ?? "");
        $artwork = trim($_POST["artwork"] ?? "");
        $status = in_array($_POST["status"] ?? "", ["approved", "pending", "rejected"]) ? $_POST["status"] : "approved";

        // Process audio upload if provided
        if (isset($_FILES["audio_file"]) && $_FILES["audio_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $upAudio = vebra_handle_upload($_FILES["audio_file"], "audio", "assets/media/music");
            if ($upAudio["success"]) {
                $audioPath = $upAudio["path"];
            } else {
                $error = $upAudio["error"];
            }
        }

        // Process artwork upload if provided
        if (isset($_FILES["artwork_file"]) && $_FILES["artwork_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $upArt = vebra_handle_upload($_FILES["artwork_file"], "image", "assets/media/album-art");
            if ($upArt["success"]) {
                $artwork = $upArt["path"];
            } else {
                $error = $upArt["error"];
            }
        }

        if (empty($title)) {
            $error = "Song title is required.";
        } elseif (!$error) {
            if (empty($audioPath)) $audioPath = "assets/musics/1.mp3";
            if (empty($artwork)) $artwork = "assets/images/album-1.jpg";

            try {
                $stmt = mysqli_prepare($conn, "INSERT INTO songs (title, artist_id, genre_id, release_year, language, audio_path, artwork, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                if ($stmt) {
                    $artistIdParam = $artistId ?: null;
                    $genreIdParam = $genreId ?: null;
                    $yearParam = $year ?: null;
                    mysqli_stmt_bind_param($stmt, "siisssss", $title, $artistIdParam, $genreIdParam, $yearParam, $language, $audioPath, $artwork, $status);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $notice = "Track \"" . htmlspecialchars($title) . "\" added successfully!";
                }
            } catch (Exception $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }

    // EDIT SONG
    if ($action === "edit") {
        $songId = intval($_POST["song_id"] ?? 0);
        $title = trim($_POST["title"] ?? "");
        $artistId = intval($_POST["artist_id"] ?? 0);
        $genreId = intval($_POST["genre_id"] ?? 0);
        $year = trim($_POST["year"] ?? 2026);
        $language = trim($_POST["language"] ?? "English");
        $audioPath = trim($_POST["audio_path"] ?? "");
        $artwork = trim($_POST["artwork"] ?? "");
        $status = in_array($_POST["status"] ?? "", ["approved", "pending", "rejected"]) ? $_POST["status"] : "approved";

        if (isset($_FILES["audio_file"]) && $_FILES["audio_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $upAudio = vebra_handle_upload($_FILES["audio_file"], "audio", "assets/media/music");
            if ($upAudio["success"]) {
                $audioPath = $upAudio["path"];
            } else {
                $error = $upAudio["error"];
            }
        }
        if (isset($_FILES["artwork_file"]) && $_FILES["artwork_file"]["error"] !== UPLOAD_ERR_NO_FILE) {
            $upArt = vebra_handle_upload($_FILES["artwork_file"], "image", "assets/media/album-art");
            if ($upArt["success"]) {
                $artwork = $upArt["path"];
            } else {
                $error = $upArt["error"];
            }
        }

        if (empty($title)) {
            $error = "Title cannot be empty.";
        } elseif (!$error) {
            try {
                $stmt = mysqli_prepare($conn, "UPDATE songs SET title = ?, artist_id = ?, genre_id = ?, release_year = ?, language = ?, audio_path = ?, artwork = ?, status = ? WHERE id = ?");
                if ($stmt) {
                    $artistIdParam = $artistId ?: null;
                    $genreIdParam = $genreId ?: null;
                    $yearParam = $year ?: null;
                    mysqli_stmt_bind_param($stmt, "siisssssi", $title, $artistIdParam, $genreIdParam, $yearParam, $language, $audioPath, $artwork, $status, $songId);
                    mysqli_stmt_execute($stmt);
                    mysqli_stmt_close($stmt);
                    $notice = "Track \"" . htmlspecialchars($title) . "\" updated successfully.";
                }
            } catch (Exception $e) {
                $error = "Database error: " . $e->getMessage();
            }
        }
    }

    // TOGGLE APPROVAL
    if ($action === "toggle_approval") {
        $songId = intval($_POST["song_id"] ?? 0);
        $currStatus = $_POST["current_status"] ?? "pending";
        $newStatus = ($currStatus === "approved") ? "pending" : "approved";
        $stmt = mysqli_prepare($conn, "UPDATE songs SET status = ? WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "si", $newStatus, $songId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notice = "Song approval status updated to " . $newStatus . ".";
        }
    }

    // DELETE SONG
    if ($action === "delete") {
        $songId = intval($_POST["song_id"] ?? 0);
        $stmt = mysqli_prepare($conn, "DELETE FROM songs WHERE id = ?");
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "i", $songId);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            $notice = "Track removed from VEBRA catalogue.";
        }
    }
}

// Fetch song for editing
if (isset($_GET["edit"])) {
    $editId = intval($_GET["edit"]);
    $stmt = mysqli_prepare($conn, "SELECT * FROM songs WHERE id = ? LIMIT 1");
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $editId);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $editingSong = mysqli_fetch_assoc($res);
        mysqli_stmt_close($stmt);
    }
}

// Fetch all songs
$filter = $_GET["filter"] ?? "all";
$querySql = "SELECT s.*, a.name as artist_name, g.name as genre_name FROM songs s LEFT JOIN artists a ON a.id = s.artist_id LEFT JOIN genres g ON g.id = s.genre_id";
if ($filter === "pending") $querySql .= " WHERE s.status = 'pending'";
elseif ($filter === "approved") $querySql .= " WHERE s.status = 'approved'";
$querySql .= " ORDER BY s.id DESC";

$allSongs = [];
$res = mysqli_query($conn, $querySql);
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $allSongs[] = $row;
    }
}

// Counts
$cntAll = 0; $cntPending = 0; $cntApproved = 0;
$resCount = mysqli_query($conn, "SELECT status, COUNT(*) as cnt FROM songs GROUP BY status");
if ($resCount) {
    while ($row = mysqli_fetch_assoc($resCount)) {
        if ($row["status"] === "pending") $cntPending = (int)$row["cnt"];
        elseif ($row["status"] === "approved") $cntApproved = (int)$row["cnt"];
        $cntAll += (int)$row["cnt"];
    }
}

// Fetch genres and artists
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
    <title>Manage Songs &mdash; VEBRA Admin</title>
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
                        <li><a href="songs.php" class="is-active"><i class="ri-music-2-line"></i> Songs</a></li>
                        <li><a href="videos.php"><i class="ri-video-line"></i> Videos</a></li>
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

            <div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 12px;">
                <div>
                    <h1 style="font-family: 'Unbounded', sans-serif; font-size: 1.8rem; font-weight: 800; text-transform: uppercase; margin: 0 0 6px;">
                        Music &amp; <em>Audio Tracks</em>
                    </h1>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.9rem; margin: 0;">Add new audio files, manage metadata, and moderate track approvals.</p>
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

            <!-- ADD OR EDIT SONG FORM -->
            <div class="admin-card">
                <h2 class="admin-card__title">
                    <i class="ri-<?= $editingSong ? 'edit-2-line' : 'add-circle-line' ?>" style="color:var(--accent, #FF2E93); margin-right:6px;"></i>
                    <?= $editingSong ? 'Edit Track: ' . htmlspecialchars($editingSong['title']) : 'Add New Audio Track' ?>
                </h2>
                <form method="POST" action="songs.php" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="<?= $editingSong ? 'edit' : 'add' ?>">
                    <?php if ($editingSong): ?>
                        <input type="hidden" name="song_id" value="<?= (int)$editingSong['id'] ?>">
                    <?php endif; ?>

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label" for="song-title">Track Title *</label>
                            <input type="text" name="title" id="song-title" class="form-control" placeholder="e.g. Halogen Kids" value="<?= htmlspecialchars($editingSong['title'] ?? '') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="song-artist">Artist *</label>
                            <select name="artist_id" id="song-artist" class="form-select">
                                <option value="0">&mdash; Select Artist &mdash;</option>
                                <?php foreach ($artists as $ar): ?>
                                    <option value="<?= $ar['id'] ?>" <?= ($editingSong && (int)$editingSong['artist_id'] === (int)$ar['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($ar['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label" for="song-genre">Genre</label>
                            <select name="genre_id" id="song-genre" class="form-select">
                                <option value="0">&mdash; Select Genre &mdash;</option>
                                <?php foreach ($genres as $g): ?>
                                    <option value="<?= $g['id'] ?>" <?= ($editingSong && (int)$editingSong['genre_id'] === (int)$g['id']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($g['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label" for="song-year">Release Year</label>
                            <input type="number" name="year" id="song-year" class="form-control" value="<?= htmlspecialchars($editingSong['release_year'] ?? 2026) ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="song-lang">Language</label>
                            <input type="text" name="language" id="song-lang" class="form-control" value="<?= htmlspecialchars($editingSong['language'] ?? 'English') ?>">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label" for="song-status">Moderation Status</label>
                            <select name="status" id="song-status" class="form-select">
                                <option value="approved" <?= ($editingSong && $editingSong['status'] === 'approved') ? 'selected' : '' ?>>Approved</option>
                                <option value="pending" <?= ($editingSong && $editingSong['status'] === 'pending') ? 'selected' : '' ?>>Pending</option>
                                <option value="rejected" <?= ($editingSong && $editingSong['status'] === 'rejected') ? 'selected' : '' ?>>Rejected</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Cover Artwork Upload</label>
                            <input type="file" name="artwork_file" class="form-control" accept="image/jpeg,image/png,image/webp">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="song-audio-file">Audio File Upload (MP3, WAV, OGG, M4A)</label>
                            <input type="file" name="audio_file" id="song-audio-file" class="form-control" accept="audio/*">
                            <input type="hidden" name="audio_path" value="<?= htmlspecialchars($editingSong['audio_path'] ?? 'assets/musics/1.mp3') ?>">
                            <?php if ($editingSong): ?>
                                <span style="font-size:0.75rem; color:rgba(255,255,255,0.4); display:block; margin-top:4px;">Current file: <?= htmlspecialchars($editingSong['audio_path']) ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label" for="song-artwork-path">Or Preset Artwork Path</label>
                            <input type="text" name="artwork" id="song-artwork-path" class="form-control" value="<?= htmlspecialchars($editingSong['artwork'] ?? 'assets/images/album-1.jpg') ?>">
                        </div>
                    </div>

                    <div style="margin-top: 20px; display: flex; gap: 12px; align-items: center;">
                        <button type="submit" class="btn btn--solid btn--sm">
                            <span class="btn__label"><i class="ri-save-line"></i> <?= $editingSong ? 'Update Track' : 'Save &amp; Publish Track' ?></span>
                        </button>
                        <?php if ($editingSong): ?>
                            <a href="songs.php" class="btn btn--glass btn--sm"><span class="btn__label">Cancel Edit</span></a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- SONGS LIST WITH FILTER PILLS -->
            <div class="admin-card">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px;">
                    <h2 class="admin-card__title" style="margin: 0;">Catalog Tracks (<?= count($allSongs) ?>)</h2>
                    <div class="filter-nav">
                        <a href="songs.php?filter=all" class="filter-btn <?= $filter === 'all' ? 'is-active' : '' ?>">All (<?= $cntAll ?>)</a>
                        <a href="songs.php?filter=pending" class="filter-btn <?= $filter === 'pending' ? 'is-active' : '' ?>">Pending (<?= $cntPending ?>)</a>
                        <a href="songs.php?filter=approved" class="filter-btn <?= $filter === 'approved' ? 'is-active' : '' ?>">Approved (<?= $cntApproved ?>)</a>
                    </div>
                </div>

                <div style="overflow-x: auto;">
                    <table>
                        <thead>
                            <tr>
                                <th>Cover</th>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Artist</th>
                                <th>Genre</th>
                                <th>Year</th>
                                <th>Audio File</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($allSongs)): ?>
                                <tr><td colspan="9" style="text-align: center; color: rgba(255,255,255,0.4); padding: 24px;">No tracks found for this filter.</td></tr>
                            <?php else: ?>
                                <?php foreach ($allSongs as $song): ?>
                                    <tr>
                                        <td style="width: 50px;">
                                            <img src="../<?= htmlspecialchars($song['artwork'] ?: 'assets/images/album-1.jpg') ?>" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover;">
                                        </td>
                                        <td>#<?= (int)$song['id'] ?></td>
                                        <td>
                                            <strong><?= htmlspecialchars($song['title']) ?></strong>
                                        </td>
                                        <td><?= htmlspecialchars($song['artist_name'] ?? '&mdash;') ?></td>
                                        <td><?= htmlspecialchars($song['genre_name'] ?? '&mdash;') ?></td>
                                        <td><?= htmlspecialchars($song['release_year'] ?: '&mdash;') ?></td>
                                        <td style="font-size:0.75rem; color:rgba(255,255,255,0.4);">
                                            <?= htmlspecialchars(basename($song['audio_path'] ?? '')) ?>
                                        </td>
                                        <td>
                                            <?php if ($song['status'] === 'approved'): ?>
                                                <span class="badge badge--success"><i class="ri-check-line"></i> Approved</span>
                                            <?php elseif ($song['status'] === 'pending'): ?>
                                                <span class="badge badge--warning"><i class="ri-time-line"></i> Pending</span>
                                            <?php else: ?>
                                                <span class="badge badge--danger"><?= htmlspecialchars(ucfirst($song['status'])) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <div style="display: flex; gap: 6px; align-items: center;">
                                                <form method="POST" action="songs.php" style="display:inline;">
                                                    <input type="hidden" name="action" value="toggle_approval">
                                                    <input type="hidden" name="song_id" value="<?= (int)$song['id'] ?>">
                                                    <input type="hidden" name="current_status" value="<?= htmlspecialchars($song['status']) ?>">
                                                    <button type="submit" class="btn btn--glass btn--sm" style="padding: 4px 10px; font-size: 0.75rem;" title="Toggle Approval Status">
                                                        <?= $song['status'] === 'approved' ? 'Reject' : 'Approve' ?>
                                                    </button>
                                                </form>

                                                <a href="songs.php?edit=<?= (int)$song['id'] ?>" class="btn btn--glass btn--sm" style="padding: 4px 10px; font-size: 0.75rem;">
                                                    <i class="ri-edit-line"></i> Edit
                                                </a>

                                                <a href="../track.php?id=<?= (int)$song['id'] ?>" class="btn btn--glass btn--sm" style="padding: 4px 10px; font-size: 0.75rem;" target="_blank">
                                                    <i class="ri-external-link-line"></i>
                                                </a>

                                                <form method="POST" action="songs.php" style="display:inline;" onsubmit="return confirm('Are you sure you want to permanently delete this track?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="song_id" value="<?= (int)$song['id'] ?>">
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
