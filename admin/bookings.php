<?php
require_once __DIR__ . '/../includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Strict Admin Authorization Guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php?redirect=' . urlencode('../admin/bookings.php'));
    exit;
}

$notice = '';
$error = '';

// Handle Status Updates / Actions (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $bookingId = (int)($_POST['booking_id'] ?? 0);

    if ($bookingId > 0) {
        if ($action === 'update_status') {
            $newStatus = trim($_POST['status'] ?? 'confirmed');
            if (in_array($newStatus, ['confirmed', 'pending', 'cancelled'], true)) {
                $stmt = mysqli_prepare($conn, "UPDATE ticket_bookings SET status = ? WHERE id = ?");
                if ($stmt) {
                    mysqli_stmt_bind_param($stmt, "si", $newStatus, $bookingId);
                    if (mysqli_stmt_execute($stmt)) {
                        $notice = "Booking #{$bookingId} status updated to " . ucfirst($newStatus) . ".";
                    } else {
                        $error = "Failed to update booking status.";
                    }
                    mysqli_stmt_close($stmt);
                }
            } else {
                $error = "Invalid booking status.";
            }
        } elseif ($action === 'delete') {
            $stmt = mysqli_prepare($conn, "DELETE FROM ticket_bookings WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $bookingId);
                if (mysqli_stmt_execute($stmt)) {
                    $notice = "Booking #{$bookingId} record removed.";
                } else {
                    $error = "Failed to remove booking record.";
                }
                mysqli_stmt_close($stmt);
            }
        }
    }
}

// Fetch all bookings
$bookings = [];
$res = mysqli_query($conn, "SELECT * FROM ticket_bookings ORDER BY created_at DESC, id DESC LIMIT 100");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $bookings[] = $row;
    }
}

// Summary statistics
$totalCount = count($bookings);
$totalRevenue = 0.0;
$totalTickets = 0;
foreach ($bookings as $b) {
    if ($b['status'] === 'confirmed') {
        $totalRevenue += (float)$b['total_price'];
        $totalTickets += (int)$b['quantity'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Ticket Bookings &mdash; VEBRA Admin</title>
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
        .form-select { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.18); border-radius: 6px; color: #fff; padding: 4px 8px; font-size: 0.8rem; outline: none; }
        .form-select option { background: #121218; color: #fff; }
        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 14px 16px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.88rem; vertical-align: middle; }
        th { color: rgba(255,255,255,0.45); font-weight: 600; font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; }
        tbody tr:hover { background: rgba(255,255,255,0.02); }
        .badge-status { display: inline-flex; align-items: center; gap: 4px; padding: 3px 8px; border-radius: 999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; }
        .badge--confirmed { background: rgba(16, 185, 129, 0.15); color: #10B981; border: 1px solid rgba(16, 185, 129, 0.3); }
        .badge--pending { background: rgba(245, 158, 11, 0.15); color: #F59E0B; border: 1px solid rgba(245, 158, 11, 0.3); }
        .badge--cancelled { background: rgba(239, 68, 68, 0.15); color: #EF4444; border: 1px solid rgba(239, 68, 68, 0.3); }
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
                        <li><a href="artists.php"><i class="ri-user-star-line"></i> Artists</a></li>
                        <li><a href="artist-requests.php"><i class="ri-user-follow-line"></i> Requests</a></li>
                        <li><a href="bookings.php" class="is-active"><i class="ri-ticket-line"></i> Bookings</a></li>
                        <li><a href="orders.php"><i class="ri-shopping-bag-3-line"></i> Orders</a></li>
                        <li><a href="reviews.php"><i class="ri-chat-smile-2-line"></i> Reviews</a></li>
                        <li><a href="users.php"><i class="ri-user-settings-line"></i> Users</a></li>
                    </ul>
                </div>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <a href="../events.php" class="btn btn--glass btn--sm" target="_blank"><span class="btn__label"><i class="ri-external-link-line"></i> View Events</span></a>
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
                        Ticket <em>Bookings</em>
                    </h1>
                    <p style="color: rgba(255,255,255,0.5); font-size: 0.9rem; margin: 0;">Tour ticket reservations, guest lists, customer contact info, and booking statuses.</p>
                </div>
            </div>

            <!-- STATS ROW -->
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 16px; margin-bottom: 24px;">
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 18px;">
                    <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.04em; color:rgba(255,255,255,0.5);">Total Bookings</span>
                    <strong style="display:block; font-size:1.6rem; font-weight:800; font-family:'Unbounded', sans-serif; margin-top:6px;"><?= $totalCount ?></strong>
                </div>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 18px;">
                    <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.04em; color:rgba(255,255,255,0.5);">Confirmed Tickets</span>
                    <strong style="display:block; font-size:1.6rem; font-weight:800; font-family:'Unbounded', sans-serif; margin-top:6px; color:#10B981;"><?= $totalTickets ?></strong>
                </div>
                <div style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.08); border-radius: 12px; padding: 18px;">
                    <span style="font-size:0.75rem; text-transform:uppercase; letter-spacing:0.04em; color:rgba(255,255,255,0.5);">Booked Value</span>
                    <strong style="display:block; font-size:1.6rem; font-weight:800; font-family:'Unbounded', sans-serif; margin-top:6px; color:var(--accent, #FF2E93);">&euro;<?= number_format($totalRevenue, 2) ?></strong>
                </div>
            </div>

            <?php if ($notice !== ''): ?>
                <div style="background: rgba(16, 185, 129, 0.12); border: 1px solid #10B981; color: #fff; padding: 12px 18px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                    <i class="ri-checkbox-circle-line" style="color: #10B981; font-size: 1.2rem;"></i>
                    <span><?= htmlspecialchars($notice) ?></span>
                </div>
            <?php endif; ?>

            <?php if ($error !== ''): ?>
                <div style="background: rgba(239, 68, 68, 0.12); border: 1px solid #EF4444; color: #fff; padding: 12px 18px; border-radius: 8px; margin-bottom: 24px; display: flex; align-items: center; gap: 10px;">
                    <i class="ri-error-warning-line" style="color: #EF4444; font-size: 1.2rem;"></i>
                    <span><?= htmlspecialchars($error) ?></span>
                </div>
            <?php endif; ?>

            <div class="admin-card">
                <h2 class="admin-card__title"><i class="ri-ticket-2-line"></i> Reservation Records</h2>
                
                <?php if (empty($bookings)): ?>
                    <p style="color: rgba(255,255,255,0.4); text-align: center; padding: 24px 0; margin: 0;">No ticket bookings recorded in the system yet.</p>
                <?php else: ?>
                    <div style="overflow-x: auto;">
                        <table>
                            <thead>
                                <tr>
                                    <th>Ref #</th>
                                    <th>Customer</th>
                                    <th>Event &amp; Venue</th>
                                    <th>Date</th>
                                    <th>Qty</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                    <th style="text-align: right;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($bookings as $b): 
                                    $st = $b['status'] ?? 'confirmed';
                                    $badgeClass = $st === 'confirmed' ? 'badge--confirmed' : ($st === 'pending' ? 'badge--pending' : 'badge--cancelled');
                                ?>
                                <tr>
                                    <td>
                                        <strong style="font-family:'Unbounded', monospace; color:var(--accent, #FF2E93); font-size:0.85rem; letter-spacing:0.5px;">
                                            <?= htmlspecialchars($b['booking_reference']) ?>
                                        </strong>
                                        <div style="font-size:0.72rem; color:rgba(255,255,255,0.35); margin-top:2px;">
                                            <?= htmlspecialchars(date('M j, Y H:i', strtotime($b['created_at']))) ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($b['customer_name']) ?></strong>
                                        <div style="font-size:0.78rem; color:rgba(255,255,255,0.5);">
                                            <?= htmlspecialchars($b['customer_email']) ?>
                                            <?= !empty($b['customer_phone']) ? ' &bull; ' . htmlspecialchars($b['customer_phone']) : '' ?>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= htmlspecialchars($b['event_city']) ?></strong>
                                        <div style="font-size:0.78rem; color:rgba(255,255,255,0.5);"><?= htmlspecialchars($b['event_venue']) ?></div>
                                    </td>
                                    <td style="color:rgba(255,255,255,0.7); font-size:0.82rem;">
                                        <?= htmlspecialchars($b['event_date']) ?>
                                    </td>
                                    <td>
                                        <strong style="font-size:0.95rem;"><?= (int)$b['quantity'] ?></strong>
                                    </td>
                                    <td>
                                        <strong style="color:#fff;"><?= htmlspecialchars($b['currency']) ?><?= number_format((float)$b['total_price'], 2) ?></strong>
                                    </td>
                                    <td>
                                        <span class="badge-status <?= $badgeClass ?>"><?= ucfirst($st) ?></span>
                                    </td>
                                    <td style="text-align: right; white-space: nowrap;">
                                        <form method="POST" style="display:inline-flex; gap:6px; align-items:center;">
                                            <input type="hidden" name="booking_id" value="<?= (int)$b['id'] ?>">
                                            <input type="hidden" name="action" value="update_status">
                                            <select name="status" class="form-select" onchange="this.form.submit()">
                                                <option value="confirmed" <?= $st === 'confirmed' ? 'selected' : '' ?>>Confirmed</option>
                                                <option value="pending" <?= $st === 'pending' ? 'selected' : '' ?>>Pending</option>
                                                <option value="cancelled" <?= $st === 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                            </select>
                                        </form>
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
