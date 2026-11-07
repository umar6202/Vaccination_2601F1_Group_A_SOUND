<?php
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strict Admin Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php?redirect=' . urlencode('../admin/index.php'));
    exit;
}

// 1. Overview counts from actual database queries
$totalSongs = 0;
$pendingSongs = 0;
$totalVideos = 0;
$pendingVideos = 0;
$totalArtists = 0;
$pendingArtists = 0;
$totalUsers = 0;
$totalReviews = 0;
$totalOrders = 0;
$totalBookings = 0;
$recentSongs = [];
$recentReviews = [];

$countQueries = [
    'totalSongs' => "SELECT COUNT(*) FROM songs",
    'pendingSongs' => "SELECT COUNT(*) FROM songs WHERE status = 'pending'",
    'totalVideos' => "SELECT COUNT(*) FROM videos",
    'pendingVideos' => "SELECT COUNT(*) FROM videos WHERE status = 'pending'",
    'totalArtists' => "SELECT COUNT(*) FROM artists",
    'pendingArtists' => "SELECT COUNT(*) FROM artists WHERE status = 'pending'",
    'totalUsers' => "SELECT COUNT(*) FROM users",
    'totalReviews' => "SELECT COUNT(*) FROM reviews",
    'totalOrders' => "SELECT COUNT(*) FROM store_orders",
    'totalBookings' => "SELECT COUNT(*) FROM ticket_bookings"
];

foreach ($countQueries as $var => $sql) {
    $res = mysqli_query($conn, $sql);
    if ($res) {
        $row = mysqli_fetch_row($res);
        $$var = (int)$row[0];
    }
}
$totalPending = $pendingSongs + $pendingVideos;

// Recent items from real database tables
$resSongs = mysqli_query($conn, "
    SELECT s.*, a.name AS artist_name, g.name AS genre_name 
    FROM songs s 
    LEFT JOIN artists a ON a.id = s.artist_id 
    LEFT JOIN genres g ON g.id = s.genre_id 
    ORDER BY s.id DESC 
    LIMIT 5
");
if ($resSongs) {
    while ($row = mysqli_fetch_assoc($resSongs)) {
        $recentSongs[] = $row;
    }
}

$resReviews = mysqli_query($conn, "
    SELECT r.*, s.title AS song_title, u.username, u.name AS user_name 
    FROM reviews r 
    JOIN songs s ON r.song_id = s.id 
    JOIN users u ON r.user_id = u.id 
    ORDER BY r.created_at DESC 
    LIMIT 5
");
if ($resReviews) {
    while ($row = mysqli_fetch_assoc($resReviews)) {
        $recentReviews[] = $row;
    }
}

// 2. Real Chart Data Queries
$genreQuery = [];
$resGenres = mysqli_query($conn, "
    SELECT g.name AS genre_name, 
           COUNT(DISTINCT s.id) AS song_count, 
           COUNT(DISTINCT v.id) AS video_count, 
           (COUNT(DISTINCT s.id) + COUNT(DISTINCT v.id)) AS total_count
           FROM genres g
           LEFT JOIN songs s ON s.genre_id = g.id
           LEFT JOIN videos v ON v.genre_id = g.id
           GROUP BY g.id, g.name
           HAVING total_count > 0
    ORDER BY total_count DESC
");
if ($resGenres) {
    while ($row = mysqli_fetch_assoc($resGenres)) {
        $genreQuery[] = $row;
    }
}

$statusQuery = [];
$resStatus = mysqli_query($conn, "
    SELECT 'songs' AS media_type, status, COUNT(*) AS cnt FROM songs GROUP BY status
    UNION ALL
    SELECT 'videos' AS media_type, status, COUNT(*) AS cnt FROM videos GROUP BY status
");
if ($resStatus) {
    while ($row = mysqli_fetch_assoc($resStatus)) {
        $statusQuery[] = $row;
    }
}

$approvedCount = 0;
$pendingCount = 0;
$rejectedCount = 0;
foreach ($statusQuery as $st) {
    if ($st['status'] === 'approved') $approvedCount += (int)$st['cnt'];
    elseif ($st['status'] === 'pending') $pendingCount += (int)$st['cnt'];
    elseif ($st['status'] === 'rejected') $rejectedCount += (int)$st['cnt'];
}

// Chart 3: Releases Added Over Time (Daily group)
$contentTimeQuery = [];
$resContentTime = mysqli_query($conn, "
    SELECT dts.dt,
           COALESCE(sc.song_count, 0) AS songs,
           COALESCE(vc.video_count, 0) AS videos
    FROM (
        SELECT DATE(created_at) AS dt FROM songs WHERE created_at IS NOT NULL
        UNION
        SELECT DATE(created_at) AS dt FROM videos WHERE created_at IS NOT NULL
    ) dts
    LEFT JOIN (
        SELECT DATE(created_at) AS dt, COUNT(*) AS song_count FROM songs GROUP BY dt
    ) sc ON sc.dt = dts.dt
    LEFT JOIN (
        SELECT DATE(created_at) AS dt, COUNT(*) AS video_count FROM videos GROUP BY dt
    ) vc ON vc.dt = dts.dt
    ORDER BY dts.dt ASC
    LIMIT 14
");
if ($resContentTime) {
    while ($row = mysqli_fetch_assoc($resContentTime)) {
        $contentTimeQuery[] = $row;
    }
}

// Chart 4: User Growth Over Time
$userTimeQuery = [];
$resUserTime = mysqli_query($conn, "
    SELECT DATE(created_at) AS dt, COUNT(*) AS user_count
    FROM users
    WHERE created_at IS NOT NULL
    GROUP BY DATE(created_at)
    ORDER BY dt ASC
    LIMIT 14
");
if ($resUserTime) {
    while ($row = mysqli_fetch_assoc($resUserTime)) {
        $userTimeQuery[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>VEBRA Operations &mdash; Admin Control Room</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../favicon.svg" rel="icon" type="image/svg+xml">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Unbounded:wght@400;600;700;800&family=Inter:wght@400;500;600;700&display=swap">
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap-grid.min.css">
    <link rel="stylesheet" href="../assets/icon-fonts/remixicon/remixicon.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="theme-magenta" style="background:#08080C; margin:0; padding:0; color:#fff;">

    <div class="admin-shell">
        
        <!-- SIDEBAR NAVIGATION -->
        <aside class="admin-sidebar">
            <div>
                <!-- Brand Header -->
                <a href="index.php" class="admin-sidebar__brand">
                    <span class="admin-sidebar__logo">vebra<span>.</span></span>
                    <span class="admin-sidebar__badge">Control</span>
                </a>

                <div class="admin-sidebar__section-title">Operations Menu</div>
                <ul class="admin-sidebar__menu">
                    <li>
                        <a href="index.php" class="admin-sidebar__link is-active">
                            <i class="ri-dashboard-3-line"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>
                    <li>
                        <a href="songs.php" class="admin-sidebar__link">
                            <i class="ri-music-2-line"></i>
                            <span>Audio Tracks</span>
                        </a>
                    </li>
                    <li>
                        <a href="videos.php" class="admin-sidebar__link">
                            <i class="ri-video-line"></i>
                            <span>Music Videos</span>
                        </a>
                    </li>
                    <li>
                        <a href="artists.php" class="admin-sidebar__link">
                            <i class="ri-user-star-line"></i>
                            <span>Artist Roster</span>
                        </a>
                    </li>
                    <li>
                        <a href="artist-requests.php" class="admin-sidebar__link">
                            <i class="ri-user-follow-line"></i>
                            <span>Artist Requests</span>
                            <?php if ($pendingArtists > 0): ?>
                                <span style="margin-left:auto; background:var(--accent,#FF2E93); color:#fff; font-size:0.68rem; font-weight:700; padding:2px 7px; border-radius:999px;"><?= $pendingArtists ?></span>
                            <?php endif; ?>
                        </a>
                    </li>
                    <li>
                        <a href="bookings.php" class="admin-sidebar__link">
                            <i class="ri-ticket-line"></i>
                            <span>Ticket Bookings</span>
                        </a>
                    </li>
                    <li>
                        <a href="reviews.php" class="admin-sidebar__link">
                            <i class="ri-chat-smile-2-line"></i>
                            <span>Listener Reviews</span>
                        </a>
                    </li>
                    <li>
                        <a href="users.php" class="admin-sidebar__link">
                            <i class="ri-user-settings-line"></i>
                            <span>User Accounts</span>
                        </a>
                    </li>
                    <li>
                        <a href="orders.php" class="admin-sidebar__link">
                            <i class="ri-shopping-bag-3-line"></i>
                            <span>Store Orders</span>
                        </a>
                    </li>
                </ul>

                <div class="admin-sidebar__section-title">Live System Status</div>
                <div style="padding: 10px 14px; background: rgba(255,255,255,0.02); border: 1px solid rgba(255,255,255,0.06); border-radius: 8px; margin-bottom: 20px;">
                    <div style="display:flex; align-items:center; gap:8px; font-size:0.75rem; color:#10B981; font-weight:600; text-transform:uppercase; letter-spacing:0.04em;">
                        <span style="width:7px; height:7px; border-radius:50%; background:#10B981; box-shadow:0 0 6px #10B981;"></span>
                        MySQL: Connected
                    </div>
                    <span style="font-size:0.72rem; color:rgba(255,255,255,0.4); display:block; margin-top:3px;">Database: vebra (InnoDB)</span>
                </div>
            </div>

            <div class="admin-sidebar__footer">
                <a href="../index.php" class="admin-sidebar__link" target="_blank" style="padding: 8px 10px;">
                    <i class="ri-external-link-line"></i>
                    <span>Public Website</span>
                </a>

                <div class="admin-sidebar__user">
                    <div class="admin-sidebar__avatar">
                        <?= strtoupper(substr($_SESSION['username'] ?? 'A', 0, 1)) ?>
                    </div>
                    <div style="flex:1; min-width:0;">
                        <strong style="font-size:0.85rem; color:#fff; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                            <?= htmlspecialchars($_SESSION['username'] ?? 'Administrator') ?>
                        </strong>
                        <span style="font-size:0.7rem; color:var(--accent, #FF2E93); font-weight:600; text-transform:uppercase;">Administrator</span>
                    </div>
                    <a href="../auth/logout.php" title="Sign Out" style="color:rgba(255,255,255,0.4); font-size:1.1rem; text-decoration:none; padding:4px;">
                        <i class="ri-logout-box-r-line"></i>
                    </a>
                </div>
            </div>
        </aside>

        <!-- MAIN CONTENT AREA -->
        <main class="admin-content-area">

            <!-- TOP BAR -->
            <div style="display: flex; justify-content: space-between; align-items: flex-end; flex-wrap: wrap; gap: 16px; margin-bottom: 28px;">
                <div>
                    <div style="font-size: 0.75rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.1em; color: var(--accent, #FF2E93); margin-bottom: 4px;">
                        Control Center &bull; Telemetry
                    </div>
                    <h1 style="font-family: var(--font-display, 'Unbounded', sans-serif); font-size: 1.85rem; font-weight: 800; text-transform: uppercase; margin: 0; letter-spacing: -0.02em;">
                        Operations <em>Overview</em>
                    </h1>
                </div>

                <div style="display: flex; gap: 10px; align-items: center;">
                    <a href="songs.php" class="btn btn--solid btn--sm" style="padding: 8px 16px; font-size: 0.8rem;">
                        <span class="btn__label"><i class="ri-add-line"></i> Add Track</span>
                    </a>
                    <a href="videos.php" class="btn btn--glass btn--sm" style="padding: 8px 16px; font-size: 0.8rem;">
                        <span class="btn__label"><i class="ri-video-add-line"></i> Add Video</span>
                    </a>
                    <a href="artists.php" class="btn btn--glass btn--sm" style="padding: 8px 16px; font-size: 0.8rem;">
                        <span class="btn__label"><i class="ri-user-add-line"></i> Add Artist</span>
                    </a>
                </div>
            </div>

            <!-- PENDING ITEMS ALERT -->
            <?php if ($totalPending > 0): ?>
                <div style="background: rgba(245, 158, 11, 0.12); border: 1px solid rgba(245, 158, 11, 0.4); border-radius: 12px; padding: 14px 20px; margin-bottom: 26px; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; gap: 12px; color: #fff; font-size: 0.9rem;">
                        <i class="ri-error-warning-fill" style="color: #F59E0B; font-size: 1.3rem;"></i>
                        <span>You have <strong><?= $totalPending ?></strong> item(s) awaiting moderation (<?= $pendingSongs ?> songs, <?= $pendingVideos ?> videos).</span>
                    </div>
                    <div style="display: flex; gap: 8px;">
                        <?php if ($pendingSongs > 0): ?><a href="songs.php?filter=pending" class="btn btn--sm btn--solid"><span class="btn__label">Review Tracks</span></a><?php endif; ?>
                        <?php if ($pendingVideos > 0): ?><a href="videos.php?filter=pending" class="btn btn--sm btn--solid"><span class="btn__label">Review Videos</span></a><?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <!-- 6 OVERVIEW METRIC CARDS -->
            <div class="row g-3" style="margin-bottom: 28px;">
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="songs.php" class="admin-stat-card">
                        <div class="admin-stat-icon admin-stat-icon--tracks"><i class="ri-music-2-line"></i></div>
                        <div>
                            <span class="admin-stat-number"><?= $totalSongs ?></span>
                            <span class="admin-stat-label">Tracks</span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="videos.php" class="admin-stat-card">
                        <div class="admin-stat-icon admin-stat-icon--videos"><i class="ri-video-line"></i></div>
                        <div>
                            <span class="admin-stat-number"><?= $totalVideos ?></span>
                            <span class="admin-stat-label">Videos</span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="artists.php" class="admin-stat-card">
                        <div class="admin-stat-icon admin-stat-icon--artists"><i class="ri-user-star-line"></i></div>
                        <div>
                            <span class="admin-stat-number"><?= $totalArtists ?></span>
                            <span class="admin-stat-label">Artists</span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="users.php" class="admin-stat-card">
                        <div class="admin-stat-icon admin-stat-icon--users"><i class="ri-user-follow-line"></i></div>
                        <div>
                            <span class="admin-stat-number"><?= $totalUsers ?></span>
                            <span class="admin-stat-label">Accounts</span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="reviews.php" class="admin-stat-card">
                        <div class="admin-stat-icon admin-stat-icon--reviews"><i class="ri-chat-smile-2-line"></i></div>
                        <div>
                            <span class="admin-stat-number"><?= $totalReviews ?></span>
                            <span class="admin-stat-label">Reviews</span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="orders.php" class="admin-stat-card">
                        <div class="admin-stat-icon admin-stat-icon--tracks" style="background:rgba(255,46,147,0.12); color:var(--accent);"><i class="ri-shopping-bag-3-line"></i></div>
                        <div>
                            <span class="admin-stat-number"><?= $totalOrders ?></span>
                            <span class="admin-stat-label">Orders</span>
                        </div>
                    </a>
                </div>
                <div class="col-sm-6 col-md-4 col-xl-2">
                    <a href="bookings.php" class="admin-stat-card">
                        <div class="admin-stat-icon admin-stat-icon--artists" style="background:rgba(59,130,246,0.12); color:#3B82F6;"><i class="ri-ticket-line"></i></div>
                        <div>
                            <span class="admin-stat-number"><?= $totalBookings ?></span>
                            <span class="admin-stat-label">Bookings</span>
                        </div>
                    </a>
                </div>
            </div>

            <!-- CHARTS ROW 1: Releases Over Time & Genre Distribution -->
            <div class="row g-4" style="margin-bottom: 28px;">
                <div class="col-lg-7">
                    <div class="admin-chart-card">
                        <div class="admin-chart-header">
                            <h2 class="admin-chart-title">
                                <i class="ri-bar-chart-grouped-line" style="color:var(--accent, #FF2E93);"></i>
                                Releases Added Over Time
                            </h2>
                            <span class="admin-chart-pill"><?= $totalSongs ?> Songs &bull; <?= $totalVideos ?> Video</span>
                        </div>
                        <div class="admin-chart-wrap">
                            <?php if (empty($contentTimeQuery)): ?>
                                <div style="display:flex; height:100%; align-items:center; justify-content:center; color:rgba(255,255,255,0.4); font-size:0.85rem;">
                                    No release chronology recorded.
                                </div>
                            <?php else: ?>
                                <canvas id="chartContentTime"></canvas>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="admin-chart-card">
                        <div class="admin-chart-header">
                            <h2 class="admin-chart-title">
                                <i class="ri-pie-chart-2-line" style="color:#9D4EDD;"></i>
                                Genre Distribution
                            </h2>
                            <span class="admin-chart-pill"><?= count($genreQuery) ?> Active Genre(s)</span>
                        </div>
                        <div class="admin-chart-wrap">
                            <?php if (empty($genreQuery)): ?>
                                <div style="display:flex; height:100%; align-items:center; justify-content:center; color:rgba(255,255,255,0.4); font-size:0.85rem;">
                                    No genres assigned to releases yet.
                                </div>
                            <?php else: ?>
                                <canvas id="chartGenre"></canvas>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- CHARTS ROW 2: User Growth Trend & Content Approval Status -->
            <div class="row g-4" style="margin-bottom: 28px;">
                <div class="col-lg-7">
                    <div class="admin-chart-card">
                        <div class="admin-chart-header">
                            <h2 class="admin-chart-title">
                                <i class="ri-line-chart-line" style="color:#00F0FF;"></i>
                                User Growth Trend
                            </h2>
                            <span class="admin-chart-pill"><?= $totalUsers ?> Member Account(s)</span>
                        </div>
                        <div class="admin-chart-wrap">
                            <?php if (empty($userTimeQuery)): ?>
                                <div style="display:flex; height:100%; align-items:center; justify-content:center; color:rgba(255,255,255,0.4); font-size:0.85rem;">
                                    No user registration dates logged.
                                </div>
                            <?php else: ?>
                                <canvas id="chartUserGrowth"></canvas>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="admin-chart-card">
                        <div class="admin-chart-header">
                            <h2 class="admin-chart-title">
                                <i class="ri-shield-check-line" style="color:#10B981;"></i>
                                Content Moderation Status
                            </h2>
                            <span class="admin-chart-pill"><?= $approvedCount ?> Approved &bull; <?= $pendingCount ?> Pending</span>
                        </div>
                        <div class="admin-chart-wrap">
                            <?php if (($approvedCount + $pendingCount + $rejectedCount) === 0): ?>
                                <div style="display:flex; height:100%; align-items:center; justify-content:center; color:rgba(255,255,255,0.4); font-size:0.85rem;">
                                    No media status entries.
                                </div>
                            <?php else: ?>
                                <canvas id="chartStatus"></canvas>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TABLES: Recent Songs & Recent Reviews -->
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="admin-card" style="margin: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <h2 style="font-family: var(--font-display, 'Unbounded', sans-serif); font-size: 1.05rem; font-weight: 700; text-transform: uppercase; margin: 0;">
                                Recent Audio Tracks
                            </h2>
                            <a href="songs.php" class="link-arrow" style="font-size: 0.8rem; text-transform: none;">View all <?= $totalSongs ?> tracks <i class="ri-arrow-right-line"></i></a>
                        </div>
                        <div style="overflow-x: auto;">
                            <table>
                                <thead>
                                    <tr>
                                        <th>Cover</th>
                                        <th>ID</th>
                                        <th>Title</th>
                                        <th>Artist</th>
                                        <th>Status</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($recentSongs)): ?>
                                        <tr><td colspan="6" style="text-align: center; color: rgba(255,255,255,0.4); padding: 20px;">No tracks currently in library.</td></tr>
                                    <?php else: ?>
                                        <?php foreach ($recentSongs as $s): ?>
                                            <tr>
                                                <td style="width: 44px;">
                                                    <img src="../<?= htmlspecialchars($s['artwork'] ?: 'assets/images/album-1.jpg') ?>" alt="" style="width: 38px; height: 38px; border-radius: 6px; object-fit: cover;">
                                                </td>
                                                <td>#<?= (int)$s['id'] ?></td>
                                                <td>
                                                    <strong><?= htmlspecialchars($s['title']) ?></strong>
                                                    <span style="display:block; font-size:0.72rem; color:rgba(255,255,255,0.4);"><?= (int)$s['release_year'] ?> &bull; <?= htmlspecialchars($s['genre_name'] ?? '—') ?></span>
                                                </td>
                                                <td><?= htmlspecialchars($s['artist_name'] ?? 'Unassigned') ?></td>
                                                <td>
                                                    <?php if ($s['status'] === 'approved'): ?>
                                                        <span class="badge badge--success"><i class="ri-check-line"></i> Approved</span>
                                                    <?php elseif ($s['status'] === 'pending'): ?>
                                                        <span class="badge badge--warning"><i class="ri-time-line"></i> Pending</span>
                                                    <?php else: ?>
                                                        <span class="badge badge--danger"><?= htmlspecialchars(ucfirst($s['status'])) ?></span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <a href="songs.php?edit=<?= (int)$s['id'] ?>" style="color:var(--accent, #FF2E93); font-size:0.82rem; font-weight:600; text-decoration:none;"><i class="ri-edit-line"></i> Edit</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-lg-5">
                    <div class="admin-card" style="margin: 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
                            <h2 style="font-family: var(--font-display, 'Unbounded', sans-serif); font-size: 1.05rem; font-weight: 700; text-transform: uppercase; margin: 0;">
                                Recent Listener Reviews
                            </h2>
                            <a href="reviews.php" class="link-arrow" style="font-size: 0.8rem; text-transform: none;">Moderate <i class="ri-arrow-right-line"></i></a>
                        </div>
                        <?php if (empty($recentReviews)): ?>
                            <div style="display: flex; flex-direction: column; align-items: center; justify-content: center; height: 180px; color: rgba(255,255,255,0.4); font-size: 0.85rem; text-align: center; gap: 8px;">
                                <i class="ri-chat-voice-line" style="font-size: 1.8rem; opacity: 0.3;"></i>
                                <span>No member reviews submitted yet.</span>
                            </div>
                        <?php else: ?>
                            <div style="display: flex; flex-direction: column; gap: 10px;">
                                <?php foreach ($recentReviews as $rv): ?>
                                    <div style="background: rgba(255,255,255,0.02); padding: 12px; border-radius: 8px; border: 1px solid rgba(255,255,255,0.06);">
                                        <div style="display: flex; justify-content: space-between; font-size: 0.82rem; margin-bottom: 4px;">
                                            <strong style="color: #fff;">@<?= htmlspecialchars($rv['username']) ?></strong>
                                            <span style="color: #F59E0B;">
                                                <?php for ($i=1; $i<=$rv['rating']; $i++): ?><i class="ri-star-fill"></i><?php endfor; ?>
                                            </span>
                                        </div>
                                        <p style="margin: 0; font-size: 0.82rem; color: #ccc;">"<?= htmlspecialchars($rv['review']) ?>"</p>
                                        <span style="font-size: 0.72rem; color: rgba(255,255,255,0.4); display: block; margin-top: 4px;">Track: <em><?= htmlspecialchars($rv['song_title']) ?></em></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </main>
    </div>

    <!-- Chart.js: Local Offline bundle with CDN fallback -->
    <script src="../assets/js/vendor/chart.umd.min.js"></script>
    <script>
        if (typeof Chart === 'undefined') {
            document.write('<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"><\/script>');
        }
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        if (typeof Chart === 'undefined') return;

        Chart.defaults.color = 'rgba(255, 255, 255, 0.65)';
        Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.07)';
        Chart.defaults.font.family = "'Inter', sans-serif";

        var customTooltip = {
            backgroundColor: 'rgba(14, 14, 20, 0.96)',
            borderColor: 'rgba(255, 255, 255, 0.15)',
            borderWidth: 1,
            padding: 10,
            titleFont: { family: "'Unbounded', sans-serif", size: 11 },
            bodyFont: { family: "'Inter', sans-serif", size: 12 }
        };

        // CHART 1: Releases Added Over Time (Magenta & Cyan)
        var timeEl = document.getElementById('chartContentTime');
        if (timeEl) {
            var timeData = <?= json_encode($contentTimeQuery) ?>;
            var labels = timeData.map(function(d) { return d.dt; });
            var songsCount = timeData.map(function(d) { return parseInt(d.songs, 10); });
            var videosCount = timeData.map(function(d) { return parseInt(d.videos, 10); });

            new Chart(timeEl, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [
                        {
                            label: 'Songs',
                            data: songsCount,
                            backgroundColor: 'rgba(255, 46, 147, 0.85)',
                            borderColor: '#FF2E93',
                            borderWidth: 1,
                            borderRadius: 6
                        },
                        {
                            label: 'Videos',
                            data: videosCount,
                            backgroundColor: 'rgba(0, 240, 255, 0.85)',
                            borderColor: '#00F0FF',
                            borderWidth: 1,
                            borderRadius: 6
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { position: 'top', labels: { boxWidth: 12, boxHeight: 12 } },
                        tooltip: customTooltip
                    }
                }
            });
        }

        // CHART 2: Genre Distribution (Coordinated Multi-Color VEBRA Palette)
        var genreEl = document.getElementById('chartGenre');
        if (genreEl) {
            var genreData = <?= json_encode($genreQuery) ?>;
            var gLabels = genreData.map(function(d) { return d.genre_name; });
            var gValues = genreData.map(function(d) { return parseInt(d.total_count, 10); });
            var palette = ['#FF2E93', '#9D4EDD', '#00F0FF', '#F59E0B', '#EC4899', '#EF4444', '#3B82F6', '#10B981'];

            new Chart(genreEl, {
                type: 'doughnut',
                data: {
                    labels: gLabels,
                    datasets: [{
                        data: gValues,
                        backgroundColor: palette.slice(0, gLabels.length),
                        borderColor: '#08080C',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, boxHeight: 12 } },
                        tooltip: customTooltip
                    },
                    cutout: '68%'
                }
            });
        }

        // CHART 3: User Growth Trend (Smooth Electric Cyan Area Line)
        var userEl = document.getElementById('chartUserGrowth');
        if (userEl) {
            var userData = <?= json_encode($userTimeQuery) ?>;
            var uLabels = userData.map(function(d) { return d.dt; });
            var uValues = userData.map(function(d) { return parseInt(d.user_count, 10); });

            new Chart(userEl, {
                type: 'line',
                data: {
                    labels: uLabels,
                    datasets: [{
                        label: 'Member Signups',
                        data: uValues,
                        borderColor: '#00F0FF',
                        backgroundColor: 'rgba(0, 240, 255, 0.15)',
                        borderWidth: 2,
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#00F0FF',
                        pointBorderColor: '#08080C',
                        pointBorderWidth: 2,
                        pointRadius: 6,
                        pointHoverRadius: 8
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    },
                    plugins: {
                        legend: { display: false },
                        tooltip: customTooltip
                    }
                }
            });
        }

        // CHART 4: Content Moderation Status (Emerald, Amber, Crimson)
        var statusEl = document.getElementById('chartStatus');
        if (statusEl) {
            var approved = <?= (int)$approvedCount ?>;
            var pending = <?= (int)$pendingCount ?>;
            var rejected = <?= (int)$rejectedCount ?>;

            new Chart(statusEl, {
                type: 'doughnut',
                data: {
                    labels: ['Approved', 'Pending', 'Rejected'],
                    datasets: [{
                        data: [approved, pending, rejected],
                        backgroundColor: ['#10B981', '#F59E0B', '#EF4444'],
                        borderColor: '#08080C',
                        borderWidth: 2
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'right', labels: { boxWidth: 12, boxHeight: 12 } },
                        tooltip: customTooltip
                    },
                    cutout: '68%'
                }
            });
        }
    });
    </script>
</body>
</html>
