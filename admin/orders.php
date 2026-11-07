<?php
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php?redirect=' . urlencode('../admin/orders.php'));
    exit;
}

$notice = '';
$error = '';
$statuses = ['pending', 'processing', 'completed', 'cancelled'];

if (empty($_SESSION['admin_orders_csrf'])) {
    $_SESSION['admin_orders_csrf'] = bin2hex(random_bytes(32));
}

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['update_order'])) {
    $submittedCsrf = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['admin_orders_csrf'], $submittedCsrf)) {
        $error = 'Invalid or expired session token. Please try again.';
    } else {
        $orderId = (int)($_POST['order_id'] ?? 0);
        $status = trim($_POST['status'] ?? 'pending');
        $shippingText = trim($_POST['shipping_amount'] ?? '');

        if (!in_array($status, $statuses, true)) {
            $error = 'Invalid order status selected.';
        } else {
            $shippingAmount = $shippingText === '' ? null : number_format((float)$shippingText, 2, '.', '');
            
            $prevStmt = mysqli_prepare($conn, 'SELECT order_number, customer_email, status, shipping_amount, subtotal FROM store_orders WHERE id = ?');
            if ($prevStmt) {
                mysqli_stmt_bind_param($prevStmt, "i", $orderId);
                mysqli_stmt_execute($prevStmt);
                $resPrev = mysqli_stmt_get_result($prevStmt);
                $previousOrder = mysqli_fetch_assoc($resPrev);
                mysqli_stmt_close($prevStmt);

                if (!$previousOrder) {
                    $error = 'Order not found.';
                } else {
                    $shipVal = $shippingAmount === null ? null : (float)$shippingAmount;
                    $subtotalVal = (float)($previousOrder['subtotal'] ?? 0);
                    $newTotal = $subtotalVal + ($shipVal !== null ? $shipVal : 0);
                    $upStmt = mysqli_prepare($conn, "UPDATE store_orders SET status = ?, shipping_amount = ?, total_amount = ? WHERE id = ?");
                    if ($upStmt) {
                        mysqli_stmt_bind_param($upStmt, "sddi", $status, $shipVal, $newTotal, $orderId);
                        mysqli_stmt_execute($upStmt);
                        mysqli_stmt_close($upStmt);
                        $notice = 'Order #' . e($previousOrder['order_number']) . ' successfully updated.';
                    }
                }
            }
        }
    }
}

$orders = [];
$resOrders = mysqli_query($conn, 'SELECT * FROM store_orders ORDER BY created_at DESC, id DESC LIMIT 100');
if ($resOrders) {
    while ($row = mysqli_fetch_assoc($resOrders)) {
        $orders[] = $row;
    }
}

$orderItems = [];
if (!empty($orders)) {
    $orderIds = array_map(function($o) { return (int)$o['id']; }, $orders);
    $placeholders = implode(',', array_fill(0, count($orderIds), '?'));
    $types = str_repeat('i', count($orderIds));
    $itemStmt = mysqli_prepare($conn, "SELECT * FROM store_order_items WHERE order_id IN ($placeholders) ORDER BY order_id DESC, id ASC");
    if ($itemStmt) {
        mysqli_stmt_bind_param($itemStmt, $types, ...$orderIds);
        mysqli_stmt_execute($itemStmt);
        $resItems = mysqli_stmt_get_result($itemStmt);
        while ($item = mysqli_fetch_assoc($resItems)) {
            $orderItems[(int)$item['order_id']][] = $item;
        }
        mysqli_stmt_close($itemStmt);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Store Orders &mdash; VEBRA Admin</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="../favicon.svg" rel="icon" type="image/svg+xml">
    <link rel="stylesheet" href="../assets/css/vendor/bootstrap-grid.min.css">
    <link rel="stylesheet" href="../assets/icon-fonts/remixicon/remixicon.css">
    <link rel="stylesheet" href="../assets/css/main.css">
</head>
<body class="theme-magenta" style="background:#08080C; margin:0; padding:0; color:#fff;">
    <header style="background: rgba(14, 14, 20, 0.96); border-bottom: 1px solid rgba(255,255,255,0.08); padding: 14px 0; position: sticky; top: 0; z-index: 100; backdrop-filter: blur(12px);">
        <div class="container-vebra">
            <div style="display: flex; align-items: center; justify-content: space-between; width: 100%; flex-wrap: wrap; gap: 16px;">
                <div style="display: flex; align-items: center; gap: 28px; flex-wrap: wrap;">
                    <a href="index.php" style="font-family: 'Unbounded', sans-serif; font-weight: 800; font-size: 1.2rem; color: #fff; text-decoration: none;">
                        vebra<span style="color:var(--accent, #FF2E93);">.</span> <span style="font-size:0.7rem; font-weight:600; opacity:0.6; text-transform:uppercase; letter-spacing:1px; margin-left:6px; vertical-align: middle;">Control</span>
                    </a>
                    <ul style="display: flex; gap: 20px; align-items: center; list-style: none; margin: 0; padding: 0; flex-wrap: wrap;">
                        <li><a href="index.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-dashboard-line"></i> Dashboard</a></li>
                        <li><a href="songs.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-music-2-line"></i> Songs</a></li>
                        <li><a href="videos.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-video-line"></i> Videos</a></li>
                        <li><a href="artists.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-user-star-line"></i> Artists</a></li>
                        <li><a href="artist-requests.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-user-follow-line"></i> Requests</a></li>
                        <li><a href="bookings.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-ticket-line"></i> Bookings</a></li>
                        <li><a href="orders.php" style="color: var(--accent, #FF2E93); text-decoration: none; font-size: 0.88rem; font-weight: 600;"><i class="ri-shopping-bag-3-line"></i> Orders</a></li>
                        <li><a href="reviews.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-chat-smile-2-line"></i> Reviews</a></li>
                        <li><a href="users.php" style="color: #888; text-decoration: none; font-size: 0.88rem; font-weight: 500;"><i class="ri-user-settings-line"></i> Users</a></li>
                    </ul>
                </div>
                <div style="display: flex; align-items: center; gap: 14px;">
                    <a href="../shop-page.php" class="btn btn--glass btn--sm" target="_blank"><span class="btn__label"><i class="ri-external-link-line"></i> View Shop</span></a>
                    <a href="../auth/logout.php" class="btn btn--solid btn--sm" style="background: rgba(255,255,255,0.08);"><span class="btn__label"><i class="ri-logout-box-r-line"></i> Logout</span></a>
                </div>
            </div>
        </div>
    </header>

    <main class="container-vebra" style="padding-top:36px; padding-bottom:64px;">
        <div style="display:flex; justify-content:space-between; align-items:center; gap:16px; flex-wrap:wrap; margin-bottom:28px;">
            <div>
                <h1 style="margin:0; font-size: 1.8rem; font-family:'Unbounded', sans-serif; font-weight:800;">Store Orders</h1>
                <p style="margin:4px 0 0; color:rgba(255,255,255,0.6); font-size:0.9rem;">Review verified customer purchases from MySQL</p>
            </div>
        </div>

        <?php if ($notice !== ''): ?><p role="status" style="color:#b6f3cc;"><?= e($notice) ?></p><?php endif; ?>
        <?php if ($error !== ''): ?><p role="alert" style="color:#ff8aa8;"><?= e($error) ?></p><?php endif; ?>

        <?php if (!$orders): ?>
            <p>No store orders have been placed yet.</p>
        <?php else: ?>
            <div style="display:grid; gap:18px;">
                <?php foreach ($orders as $order): ?>
                    <article style="padding:22px; border:1px solid rgba(255,255,255,.12); border-radius:12px; background:rgba(255,255,255,.025);">
                        <div style="display:flex; justify-content:space-between; gap:16px; flex-wrap:wrap;">
                            <div>
                                <h2 style="margin:0 0 8px;"><?= e($order['order_number']) ?></h2>
                                <p style="margin:0;"><?= e($order['created_at']) ?> &bull; <?= e(ucfirst($order['status'])) ?> &bull; Cash on delivery</p>
                            </div>
                            <strong>Items $<?= e(number_format((float)$order['subtotal'], 2)) ?></strong>
                        </div>

                        <div class="row g-4" style="margin-top:12px;">
                            <div class="col-lg-6">
                                <h3>Customer & delivery</h3>
                                <p>
                                    <?= e($order['customer_name']) ?><br>
                                    <?= e($order['customer_email']) ?><br>
                                    <?= e($order['customer_phone']) ?><br>
                                    <?= e($order['address_line1']) ?><br>
                                    <?php if ($order['address_line2']): ?><?= e($order['address_line2']) ?><br><?php endif; ?>
                                    <?= e($order['city']) ?><?= $order['region'] ? ', ' . e($order['region']) : '' ?>
                                    <?= $order['postal_code'] ? ' ' . e($order['postal_code']) : '' ?><br>
                                    <?= e($order['country']) ?>
                                </p>
                                <?php if ($order['customer_note']): ?><p>Note: <?= nl2br(e($order['customer_note'])) ?></p><?php endif; ?>
                            </div>
                            <div class="col-lg-6">
                                <h3>Items</h3>
                                <ul>
                                    <?php foreach ($orderItems[(int)$order['id']] ?? [] as $item): ?>
                                        <li>
                                            <?= e($item['product_name']) ?>
                                            <?= $item['product_size'] ? '(' . e($item['product_size']) . ')' : '' ?>
                                            &times; <?= (int)$item['quantity'] ?> &mdash;
                                            $<?= e(number_format((float)$item['line_total'], 2)) ?>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                                <form method="post" action="orders.php" style="display:flex; gap:10px; align-items:flex-end; flex-wrap:wrap;">
                                    <input type="hidden" name="csrf_token" value="<?= e($_SESSION['admin_orders_csrf']) ?>">
                                    <input type="hidden" name="order_id" value="<?= (int)$order['id'] ?>">
                                    <label>Status
                                        <select name="status">
                                            <?php foreach ($statuses as $status): ?>
                                                <option value="<?= e($status) ?>" <?= $order['status'] === $status ? 'selected' : '' ?>><?= e(ucfirst($status)) ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                    </label>
                                    <label>Shipping (blank = not confirmed)
                                        <input type="number" name="shipping_amount" min="0" max="999999.99" step="0.01"
                                            value="<?= $order['shipping_amount'] === null ? '' : e(number_format((float)$order['shipping_amount'], 2, '.', '')) ?>">
                                    </label>
                                    <button type="submit" class="btn btn--solid btn--sm"><span class="btn__label">Save</span></button>
                                </form>
                                <p>Total due: $<?= e(number_format((float)$order['total_amount'], 2)) ?>
                                    <?= $order['shipping_amount'] === null ? '(shipping not confirmed)' : '(including shipping)' ?>
                                </p>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>
