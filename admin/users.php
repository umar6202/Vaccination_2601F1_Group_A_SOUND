<?php
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Admin security guard
if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header('Location: ../auth/login.php?redirect=../admin/users.php');
    exit;
}

$notice = '';

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $uId = intval($_POST['user_id'] ?? 0);
    
    if ($action === 'delete') {
        if ($uId === (int)$_SESSION['user_id']) {
            $notice = 'You cannot delete your own active admin account.';
        } else {
            $stmt = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $uId);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                $notice = 'User account deleted.';
            }
        }
    }
}

$allUsers = [];
$res = mysqli_query($conn, "SELECT id, username, name, email, phone, address, role, created_at FROM users ORDER BY id DESC");
if ($res) {
    while ($row = mysqli_fetch_assoc($res)) {
        $allUsers[] = $row;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Users &mdash; VEBRA Admin</title>
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
        th, td { padding: 12px 14px; text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06); font-size: 0.9rem; }
        th { color: var(--ink-40); font-weight: 500; font-size: 0.8rem; text-transform: uppercase; letter-spacing: 0.5px; }
        .badge { display: inline-block; padding: 4px 10px; border-radius: 20px; font-size: 0.75rem; font-weight: 600; }
        .badge--admin { background: rgba(var(--accent-rgb), 0.2); color: var(--accent); border: 1px solid var(--accent); }
        .badge--user { background: rgba(255,255,255,0.1); color: #fff; }
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
                        <li><a href="reviews.php">Reviews</a></li>
                        <li><a href="users.php" class="is-active">Users</a></li>
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

            <div style="margin-bottom: 32px;">
                <h1 style="font-family: 'Unbounded', sans-serif; font-size: 1.8rem; margin-bottom: 8px;">
                    Registered <em>User Accounts</em>
                </h1>
                <p style="color: var(--ink-40); margin: 0;">Manage community members, contact details, and administrative privileges.</p>
            </div>

            <?php if ($notice): ?>
                <div style="background: rgba(var(--accent-rgb), 0.15); border: 1px solid var(--accent); padding: 14px 18px; border-radius: 6px; margin-bottom: 24px; color: #fff;">
                    <i class="ri-checkbox-circle-fill" style="color:var(--accent); margin-right:8px;"></i> <?= htmlspecialchars($notice) ?>
                </div>
            <?php endif; ?>

            <div class="admin-card">
                <h2 style="font-family: 'Unbounded', sans-serif; font-size: 1.15rem; margin-bottom: 16px;">
                    All Users (<?= count($allUsers) ?>)
                </h2>
                <table>
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Contact</th>
                            <th>Address</th>
                            <th>Role</th>
                            <th>Joined</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($allUsers)): ?>
                            <tr><td colspan="6" style="text-align: center; color: var(--ink-40); padding: 30px;">No registered accounts in database.</td></tr>
                        <?php else: ?>
                            <?php foreach ($allUsers as $u): ?>
                                <tr>
                                    <td>
                                        <strong><?= htmlspecialchars($u['name'] ?: $u['username']) ?></strong>
                                        <div style="font-size:0.75rem; color:var(--ink-40);">@<?= htmlspecialchars($u['username']) ?></div>
                                    </td>
                                    <td>
                                        <div><?= htmlspecialchars($u['email']) ?></div>
                                        <div style="font-size:0.75rem; color:var(--ink-40);"><?= htmlspecialchars($u['phone'] ?: 'No phone') ?></div>
                                    </td>
                                    <td style="font-size:0.85rem; color:#ccc;">
                                        <?= htmlspecialchars($u['address'] ?: '&mdash;') ?>
                                    </td>
                                    <td>
                                        <?php if ($u['role'] === 'admin'): ?>
                                            <span class="badge badge--admin">Admin</span>
                                        <?php else: ?>
                                            <span class="badge badge--user">Member</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="font-size:0.8rem; color:var(--ink-40);">
                                        <?= htmlspecialchars(substr($u['created_at'], 0, 10)) ?>
                                    </td>
                                    <td>
                                        <div style="display: flex; gap: 8px;">
                                            <form method="POST" action="" style="display:inline;">
                                                <input type="hidden" name="action" value="change_role">
                                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                <input type="hidden" name="new_role" value="<?= $u['role'] === 'admin' ? 'user' : 'admin' ?>">
                                                <button type="submit" class="btn btn--glass btn--sm" style="padding: 4px 10px; font-size: 0.75rem;">
                                                    Make <?= $u['role'] === 'admin' ? 'Member' : 'Admin' ?>
                                                </button>
                                            </form>

                                            <?php if ($u['id'] !== intval($_SESSION['user_id'])): ?>
                                                <form method="POST" action="" style="display:inline;" onsubmit="return confirm('Delete this account permanently?');">
                                                    <input type="hidden" name="action" value="delete">
                                                    <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                                    <button type="submit" class="btn btn--solid btn--sm" style="padding: 4px 10px; font-size: 0.75rem; background: rgba(255,60,60,0.3); border-color:#ff5c5c;">
                                                        Delete
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>
    </main>

</body>
</html>
