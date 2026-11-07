<?php
require_once __DIR__ . '/../includes/db.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if (($_SESSION['role'] ?? '') === 'admin') {
        header('Location: ../admin/index.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

$error = '';
$registeredMsg = '';
if (isset($_GET['registered'])) {
    $registeredMsg = 'Registration successful! You can now log in.';
} elseif (isset($_GET['msg']) && $_GET['msg'] === 'login_required') {
    $registeredMsg = 'Please log in or register before adding products to your cart.';
}
$rawRedirect = $_GET['redirect'] ?? $_GET['ref'] ?? '../index.php';
if (!preg_match('#^(\.\./|/|https?://)#i', $rawRedirect)) {
    $redirectUrl = '../' . ltrim($rawRedirect, '/');
} else {
    $redirectUrl = $rawRedirect;
}
$loginIdentifier = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Genuinely accept either username or email under identifier, username, or email
    $loginIdentifier = trim($_POST['identifier'] ?? $_POST['username'] ?? $_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($loginIdentifier) || empty($password)) {
        $error = 'Please enter both your email or username and password.';
    } else {
        try {
            // Case-insensitive trimmed lookup matching either email OR username
            $stmt = mysqli_prepare($conn, "
                SELECT id, username, name, email, password, role 
                FROM users 
                WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) 
                   OR LOWER(TRIM(username)) = LOWER(TRIM(?)) 
                LIMIT 1
            ");
            if ($stmt) {
                $cleanId = strtolower(trim($loginIdentifier));
                mysqli_stmt_bind_param($stmt, "ss", $cleanId, $cleanId);
                mysqli_stmt_execute($stmt);
                $res = mysqli_stmt_get_result($stmt);
                $user = mysqli_fetch_assoc($res);
                mysqli_stmt_close($stmt);

                if ($user && password_verify($password, $user['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['name'] = $user['name'];
                    $_SESSION['role'] = $user['role'];

                    if ($user['role'] === 'admin') {
                        header('Location: ../admin/index.php');
                    } else {
                        header('Location: ' . $redirectUrl);
                    }
                    exit;
                } else {
                    $error = 'Invalid email/username or password.';
                }
            } else {
                $error = 'Database statement preparation failed.';
            }
        } catch (Exception $e) {
            $error = 'A database error occurred. Please try again.';
        }
    }
}

$pageTitle = 'Log In — VEBRA';
$basePath = '../';
$bodyClass = 'theme-magenta';
$activePage = 'account';
$activeSub = 'login';
require_once __DIR__ . '/../includes/header.php';
?>

<main id="main">
    <section class="auth-split-section">
        <div class="container-vebra">
            <div class="auth-split-card" data-reveal="up">
                
                <!-- LEFT PANEL: Large Cinematic Visual Showcase -->
                <div class="auth-split__visual">
                    <img src="../assets/images/hero-1.jpg" alt="VEBRA Studio Performance" class="auth-split__visual-img">
                    <div class="auth-split__visual-overlay"></div>
                    <div class="auth-split__visual-content">
                        <div class="auth-split__pill">
                            <span class="auth-split__pill-dot"></span>
                            <span>VEBRA Collective &bull; Brooklyn, NY</span>
                        </div>
                        <h2 class="auth-split__visual-heading">Sound in its <em>purest form</em>.</h2>
                        <p class="auth-split__visual-desc">
                            Enter the VEBRA listening space. Discover new album cuts, stream master-quality audio, and contribute verified community ratings.
                        </p>
                        <div class="auth-split__meta-tags">
                            <div class="auth-split__meta-item">
                                <strong>Nightform</strong>
                                <span>Featured Master</span>
                            </div>
                            <div class="auth-split__meta-item">
                                <strong>2026</strong>
                                <span>Tour Season</span>
                            </div>
                            <div class="auth-split__meta-item">
                                <strong>Lossless</strong>
                                <span>Audio Stream</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT PANEL: Aligned Form Interface -->
                <div class="auth-split__form">
                    
                    <!-- 1. Breadcrumb -->
                    <nav class="breadcrumb-vebra" aria-label="Breadcrumb">
                        <a href="../index.php">Home</a>
                        <span class="breadcrumb-vebra__sep">/</span>
                        <span aria-current="page">Log In</span>
                    </nav>

                    <!-- 2. Heading & 3. Description -->
                    <div class="auth-form-header">
                        <h1 class="auth-form-title">Welcome back</h1>
                        <p class="auth-form-desc">
                            Log in with your username or email to rate tracks, submit reviews, and access your account.
                        </p>
                    </div>

                    <!-- 4. Alerts -->
                    <?php if ($registeredMsg): ?>
                        <div class="vebra-auth-alert vebra-auth-alert--success" role="status">
                            <i class="ri-checkbox-circle-fill"></i>
                            <span><?= htmlspecialchars($registeredMsg) ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="vebra-auth-alert vebra-auth-alert--danger" role="alert">
                            <i class="ri-error-warning-fill"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- 5. Form labels and inputs -->
                    <form method="POST" action="" novalidate>
                        <div class="field">
                            <input type="text" name="identifier" id="login-identifier" class="field__control" placeholder=" " value="<?= htmlspecialchars($loginIdentifier) ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required autofocus>
                            <label for="login-identifier" class="field__label">Email or Username</label>
                            <span class="field__line" aria-hidden="true"></span>
                        </div>

                        <div class="field">
                            <input type="password" name="password" id="login-password" class="field__control" placeholder=" " autocomplete="current-password" required>
                            <label for="login-password" class="field__label">Password</label>
                            <span class="field__line" aria-hidden="true"></span>
                        </div>

                        <!-- 6. Submit button -->
                        <button type="submit" class="btn btn--solid btn--block vebra-auth-btn" data-magnetic="0.2">
                            <span class="btn__label">Log In to VEBRA <i class="ri-arrow-right-line"></i></span>
                        </button>

                        <!-- 7. Account link -->
                        <div class="vebra-auth-switch">
                            Don't have an account yet? <a href="register.php" class="vebra-auth-link">Create one here</a>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </section>
</main>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>
