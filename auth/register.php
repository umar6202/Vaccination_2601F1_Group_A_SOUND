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
$usernameVal = '';
$nameVal = '';
$addressVal = '';
$phoneVal = '';
$emailVal = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameVal = trim($_POST['username'] ?? '');
    $nameVal = trim($_POST['name'] ?? '');
    $addressVal = trim($_POST['address'] ?? '');
    $phoneVal = trim($_POST['phone'] ?? '');
    $emailVal = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    if (empty($usernameVal) || empty($nameVal) || empty($addressVal) || empty($phoneVal) || empty($emailVal) || empty($password)) {
        $error = 'All fields (Username, Full Name, Address, Phone, Email, Password) are required.';
    } elseif (!filter_var($emailVal, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please provide a valid email address.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } elseif ($password !== $confirmPassword) {
        $error = 'Passwords do not match.';
    } else {
        try {
            $checkStmt = mysqli_prepare($conn, "
                SELECT id 
                FROM users 
                WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) 
                   OR LOWER(TRIM(username)) = LOWER(TRIM(?)) 
                LIMIT 1
            ");
            if ($checkStmt) {
                $cleanEmail = strtolower(trim($emailVal));
                $cleanUser = strtolower(trim($usernameVal));
                mysqli_stmt_bind_param($checkStmt, "ss", $cleanEmail, $cleanUser);
                mysqli_stmt_execute($checkStmt);
                $checkRes = mysqli_stmt_get_result($checkStmt);
                $existingUser = mysqli_fetch_assoc($checkRes);
                mysqli_stmt_close($checkStmt);

                if ($existingUser) {
                    $error = 'That email address or username is already registered. Please log in.';
                } else {
                    $hashed = password_hash($password, PASSWORD_BCRYPT);
                    $insStmt = mysqli_prepare($conn, "
                        INSERT INTO users (username, name, address, phone, email, password, role) 
                        VALUES (?, ?, ?, ?, ?, ?, 'user')
                    ");
                    if ($insStmt) {
                        mysqli_stmt_bind_param($insStmt, "ssssss", $usernameVal, $nameVal, $addressVal, $phoneVal, $emailVal, $hashed);
                        mysqli_stmt_execute($insStmt);
                        mysqli_stmt_close($insStmt);

                        header('Location: login.php?registered=1');
                        exit;
                    } else {
                        $error = 'Failed to prepare registration statement.';
                    }
                }
            } else {
                $error = 'Database statement preparation failed.';
            }
        } catch (Exception $e) {
            $error = 'An error occurred during registration. Please try again.';
        }
    }
}

$pageTitle = 'Join VEBRA — Register Account';
$basePath = '../';
$bodyClass = 'theme-magenta';
$activePage = 'account';
$activeSub = 'register';
require_once __DIR__ . '/../includes/header.php';
?>

<main id="main">
    <section class="auth-split-section">
        <div class="container-vebra">
            <div class="auth-split-card auth-split-card--register" data-reveal="up">
                
                <!-- LEFT PANEL: Large Cinematic Visual Showcase -->
                <div class="auth-split__visual">
                    <img src="../assets/images/artists-page-cover.jpg" alt="VEBRA Stage and Community" class="auth-split__visual-img">
                    <div class="auth-split__visual-overlay"></div>
                    <div class="auth-split__visual-content">
                        <div class="auth-split__pill">
                            <span class="auth-split__pill-dot"></span>
                            <span>VEBRA Collective &bull; Community</span>
                        </div>
                        <h2 class="auth-split__visual-heading">Join the <em>frequency</em>.</h2>
                        <p class="auth-split__visual-desc">
                            Create a verified listener account. Post track impressions, rate singles and albums, and engage directly with the VEBRA artist lineup.
                        </p>
                        <div class="auth-split__meta-tags">
                            <div class="auth-split__meta-item">
                                <strong>Community</strong>
                                <span>Verified Members</span>
                            </div>
                            <div class="auth-split__meta-item">
                                <strong>Reviews</strong>
                                <span>1 to 5 Stars</span>
                            </div>
                            <div class="auth-split__meta-item">
                                <strong>Backstage</strong>
                                <span>Exclusive Drops</span>
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
                        <span aria-current="page">Register</span>
                    </nav>

                    <!-- 2. Heading & 3. Description -->
                    <div class="auth-form-header">
                        <h1 class="auth-form-title">Create Account</h1>
                        <p class="auth-form-desc">
                            Enter your details below to set up your VEBRA member profile.
                        </p>
                    </div>

                    <!-- 4. Alerts -->
                    <?php if ($error): ?>
                        <div class="vebra-auth-alert vebra-auth-alert--danger" role="alert">
                            <i class="ri-error-warning-fill"></i>
                            <span><?= htmlspecialchars($error) ?></span>
                        </div>
                    <?php endif; ?>

                    <!-- 5. Form labels and inputs -->
                    <form method="POST" action="" novalidate>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field">
                                    <input type="text" name="username" id="reg-username" class="field__control" placeholder=" " value="<?= htmlspecialchars($usernameVal) ?>" autocomplete="username" autocapitalize="none" spellcheck="false" required>
                                    <label for="reg-username" class="field__label">Username</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field">
                                    <input type="text" name="name" id="reg-name" class="field__control" placeholder=" " value="<?= htmlspecialchars($nameVal) ?>" autocomplete="name" required>
                                    <label for="reg-name" class="field__label">Full Name</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                        </div>

                        <div class="field">
                            <input type="email" name="email" id="reg-email" class="field__control" placeholder=" " value="<?= htmlspecialchars($emailVal) ?>" autocomplete="email" required>
                            <label for="reg-email" class="field__label">Email Address</label>
                            <span class="field__line" aria-hidden="true"></span>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field">
                                    <input type="tel" name="phone" id="reg-phone" class="field__control" placeholder=" " value="<?= htmlspecialchars($phoneVal) ?>" autocomplete="tel" required>
                                    <label for="reg-phone" class="field__label">Phone Number</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field">
                                    <input type="text" name="address" id="reg-address" class="field__control" placeholder=" " value="<?= htmlspecialchars($addressVal) ?>" autocomplete="street-address" required>
                                    <label for="reg-address" class="field__label">Address / City</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="field">
                                    <input type="password" name="password" id="reg-password" class="field__control" placeholder=" " autocomplete="new-password" required minlength="6">
                                    <label for="reg-password" class="field__label">Password (min 6)</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="field">
                                    <input type="password" name="confirm_password" id="reg-confirm-password" class="field__control" placeholder=" " autocomplete="new-password" required minlength="6">
                                    <label for="reg-confirm-password" class="field__label">Confirm Password</label>
                                    <span class="field__line" aria-hidden="true"></span>
                                </div>
                            </div>
                        </div>

                        <!-- 6. Submit button -->
                        <button type="submit" class="btn btn--solid btn--block vebra-auth-btn" data-magnetic="0.2">
                            <span class="btn__label">Complete Registration <i class="ri-user-add-line"></i></span>
                        </button>

                        <!-- 7. Account link -->
                        <div class="vebra-auth-switch">
                            Already have an account? <a href="login.php" class="vebra-auth-link">Log in here</a>
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
