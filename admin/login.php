<?php
session_start();
require_once '../includes/config.php';

if (isAdmin()) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username'] ?? '');
    $pass = trim($_POST['password'] ?? '');
    if ($user === ADMIN_USER && $pass === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: dashboard.php');
        exit;
    } else {
        $error = 'Username atau password salah. Silakan coba lagi.';
    }
}

$pageTitle = 'Login Admin';
$activePage = '';
$base = '../';
require_once '../includes/header.php';
?>

<div class="login-wrap">
    <div class="login-card animate-up">
        <div class="login-logo">
            <div class="logo-icon"><i class="fas fa-shield-halved"></i></div>
            <h2>Login Admin</h2>
            <p class="login-sub">Masuk untuk mengelola data gejala dan riwayat konsultasi sistem pakar.</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom:20px;">
            <span class="alert-icon"><i class="fas fa-ban"></i></span>
            <div><?= $error ?></div>
        </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" class="form-input"
                       placeholder="Masukkan username" autocomplete="username" required
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" class="form-input"
                       placeholder="Masukkan password" autocomplete="current-password" required>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:8px;">
                <i class="fas fa-right-to-bracket"></i> Masuk ke Panel Admin
            </button>
        </form>

        <div style="margin-top:24px;text-align:center;">
            <a href="../index.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Kembali ke Beranda</a>
        </div>

        <div class="alert alert-info" style="margin-top:20px;margin-bottom:0;font-size:0.82rem;">
            <span class="alert-icon"><i class="fas fa-circle-info"></i></span>
            <div>Default: <code>admin</code> / <code>admin123</code> &mdash; ubah di <code>includes/config.php</code></div>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
