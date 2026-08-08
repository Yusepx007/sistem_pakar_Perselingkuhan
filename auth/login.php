<?php
session_start();
require_once '../includes/config.php';

// Sudah login redirect
if (isAdmin()) {
    header('Location: ../admin/dashboard.php');
    exit;
}
if (isUser()) {
    header('Location: ../index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    // 1. Cek admin dulu
    if ($username === ADMIN_USER && $password === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
        header('Location: ../admin/dashboard.php');
        exit;
    }

    // 2. Cek user di database (bisa login pakai username ATAU email)
    $tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
    if ($tableCheck->num_rows > 0) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE (username = ? OR email = ?) AND status = 'aktif'");
        $stmt->bind_param("ss", $username, $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();

        if ($user && ($user['password'] === md5($password) || $user['password'] === $password || ($user['password_plain'] ?? '') === $password)) {
            $_SESSION['user_logged_in'] = true;
            $_SESSION['user_id']        = $user['id'];
            $_SESSION['user_nama']      = $user['nama'];
            $_SESSION['user_username']  = $user['username'];
            header('Location: ../index.php');
            exit;
        }
    }

    $error = 'Username/email atau password salah, atau akun belum aktif.';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | SiPakar CF</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<div class="login-wrap">
    <div class="login-card animate-up">
        <div class="login-logo">
            <div class="logo-icon" style="background:linear-gradient(135deg,#3b82f6,#8b5cf6);">
                <i class="fas fa-brain" style="color:white;"></i>
            </div>
            <h2>Masuk ke SiPakar CF</h2>
            <p class="login-sub">Login sebagai <strong style="color:#93c5fd;">admin</strong> atau <strong style="color:#6ee7b7;">pengguna</strong> menggunakan satu form.</p>
        </div>

        <?php if ($error): ?>
        <div class="alert alert-danger" style="margin-bottom:20px;">
            <span class="alert-icon"><i class="fas fa-ban"></i></span>
            <div><?= htmlspecialchars($error) ?></div>
        </div>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <div class="form-group">
                <label for="username"><i class="fas fa-user" style="opacity:0.6;margin-right:6px;"></i>Username atau Email</label>
                <input type="text" id="username" name="username" class="form-input"
                       placeholder="Masukkan username atau email" autocomplete="username" required
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label for="password"><i class="fas fa-lock" style="opacity:0.6;margin-right:6px;"></i>Password</label>
                <div style="position:relative;">
                    <input type="password" id="password" name="password" class="form-input"
                           placeholder="Masukkan password" autocomplete="current-password" required
                           style="max-width:100%;padding-right:44px;">
                    <button type="button" onclick="togglePass()"
                        style="position:absolute;right:12px;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-muted);cursor:pointer;font-size:1rem;padding:0;">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn btn-primary btn-lg" style="width:100%;justify-content:center;margin-top:8px;">
                <i class="fas fa-right-to-bracket"></i> Masuk
            </button>
        </form>

        <div style="margin-top:20px;text-align:center;">
            <a href="../index.php" class="btn btn-ghost btn-sm" style="justify-content:center;">
                <i class="fas fa-arrow-left"></i> Kembali ke Beranda
            </a>
        </div>

        <div class="alert alert-info" style="margin-top:20px;margin-bottom:0;font-size:0.82rem;">
            <span class="alert-icon"><i class="fas fa-circle-info"></i></span>
            <div>
                <strong>Admin:</strong> <code>admin</code> / <code>admin123</code><br>
                <strong>Pengguna:</strong> Masuk menggunakan <strong>Username</strong> atau <strong>Email</strong> serta password akun Anda yang terdaftar.
            </div>
        </div>
    </div>
</div>

<script>
function togglePass() {
    const inp = document.getElementById('password');
    const ico = document.getElementById('eyeIcon');
    if (inp.type === 'password') {
        inp.type = 'text';
        ico.className = 'fas fa-eye-slash';
    } else {
        inp.type = 'password';
        ico.className = 'fas fa-eye';
    }
}
</script>
</body>
</html>
