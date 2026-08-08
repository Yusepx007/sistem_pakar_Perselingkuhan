<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SiPakar CF — Sistem Pakar Identifikasi Faktor Risiko Perselingkuhan menggunakan metode Certainty Factor.">
    <meta name="author" content="Ela Amelia — STMIK DCI Tasikmalaya">
    <meta name="theme-color" content="#0f172a">
    <title><?= htmlspecialchars($pageTitle ?? 'Halaman') ?> | SiPakar CF</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= $base ?? '' ?>assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%230f172a'/><text y='.9em' font-size='75' x='12' fill='%2393c5fd' font-family='sans-serif'>S</text></svg>">
</head>
<body>

<button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
    <i class="fas fa-bars"></i>
</button>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-layout">
<!-- ══ SIDEBAR ══ -->
<aside class="sidebar" id="sidebar">
    <!-- Brand -->
    <a class="sidebar-brand" href="<?= $base ?? '' ?>index.php">
        <div class="sidebar-brand-icon"><i class="fas fa-brain"></i></div>
        <div class="sidebar-brand-text">
            <span class="sidebar-brand-name">SiPakar CF</span>
            <span class="sidebar-brand-sub">Sistem Pakar · v2.0</span>
        </div>
    </a>

    <?php if (isAdmin()): ?>
    <!-- Admin User Info -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar admin-av"><i class="fas fa-shield-halved"></i></div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name">Administrator</div>
            <span class="sidebar-user-role admin">Admin</span>
        </div>
    </div>

    <!-- Admin Nav -->
    <nav class="sidebar-nav">
        <div class="sidebar-nav-section">
            <span class="sidebar-nav-label">Dashboard</span>
            <a href="<?= $base ?? '' ?>admin/dashboard.php" class="<?= ($activePage ?? '') === 'admin-dashboard' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-gauge-high"></i></span> Dashboard Admin
            </a>
        </div>
        <div class="sidebar-nav-section">
            <span class="sidebar-nav-label">Manajemen</span>
            <a href="<?= $base ?? '' ?>admin/kelola-gejala.php" class="<?= ($activePage ?? '') === 'admin-gejala' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-gear"></i></span> Kelola Gejala
            </a>
            <a href="<?= $base ?? '' ?>admin/kelola-user.php" class="<?= ($activePage ?? '') === 'admin-user' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-users-gear"></i></span> Kelola User
            </a>
            <a href="<?= $base ?? '' ?>admin/riwayat-admin.php" class="<?= ($activePage ?? '') === 'admin-riwayat' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-clock-rotate-left"></i></span> Riwayat Konsultasi
            </a>
        </div>
        <div class="sidebar-nav-section">
            <span class="sidebar-nav-label">Sistem</span>
            <a href="<?= $base ?? '' ?>pages/konsultasi.php" class="<?= ($activePage ?? '') === 'konsultasi' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-stethoscope"></i></span> Coba Konsultasi
            </a>
            <a href="<?= $base ?? '' ?>admin/logout.php" class="nav-danger">
                <span class="nav-icon"><i class="fas fa-right-from-bracket"></i></span> Logout Admin
            </a>
        </div>
    </nav>

    <?php elseif (isUser()): ?>
    <!-- User Info -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar user-av"><?= strtoupper(substr($_SESSION['user_nama'] ?? 'U', 0, 1)) ?></div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['user_nama'] ?? 'Pengguna') ?></div>
            <span class="sidebar-user-role user">User</span>
        </div>
    </div>

    <!-- User Nav -->
    <nav class="sidebar-nav">
        <div class="sidebar-nav-section">
            <span class="sidebar-nav-label">Menu</span>
            <a href="<?= $base ?? '' ?>index.php" class="<?= ($activePage ?? '') === 'home' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-house"></i></span> Beranda
            </a>
            <a href="<?= $base ?? '' ?>pages/konsultasi.php" class="<?= ($activePage ?? '') === 'konsultasi' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-stethoscope"></i></span> Konsultasi
            </a>
            <a href="<?= $base ?? '' ?>pages/riwayat.php" class="<?= ($activePage ?? '') === 'riwayat' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-clock-rotate-left"></i></span> Riwayat Diagnosa
            </a>
            <a href="<?= $base ?? '' ?>pages/gejala.php" class="<?= ($activePage ?? '') === 'gejala' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Data Gejala
            </a>
            <a href="<?= $base ?? '' ?>pages/tentang.php" class="<?= ($activePage ?? '') === 'tentang' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-circle-info"></i></span> Tentang
            </a>
        </div>
        <div class="sidebar-nav-section">
            <span class="sidebar-nav-label">Akun</span>
            <a href="<?= $base ?? '' ?>user/logout.php" class="nav-danger">
                <span class="nav-icon"><i class="fas fa-right-from-bracket"></i></span> Logout
            </a>
        </div>
    </nav>

    <?php else: ?>
    <!-- Guest Nav -->
    <nav class="sidebar-nav">
        <div class="sidebar-nav-section">
            <span class="sidebar-nav-label">Menu</span>
            <a href="<?= $base ?? '' ?>index.php" class="<?= ($activePage ?? '') === 'home' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-house"></i></span> Beranda
            </a>
            <a href="<?= $base ?? '' ?>pages/konsultasi.php" class="<?= ($activePage ?? '') === 'konsultasi' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-stethoscope"></i></span> Konsultasi
            </a>
            <a href="<?= $base ?? '' ?>pages/gejala.php" class="<?= ($activePage ?? '') === 'gejala' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-clipboard-list"></i></span> Data Gejala
            </a>
            <a href="<?= $base ?? '' ?>pages/tentang.php" class="<?= ($activePage ?? '') === 'tentang' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-circle-info"></i></span> Tentang
            </a>
        </div>
    </nav>
    <?php endif; ?>

    <div class="sidebar-footer">
        <div>SiPakar CF &copy; 2026</div>
        <div style="margin-top:2px;">Ela Amelia &mdash; STMIK DCI</div>
    </div>
</aside>

<!-- ══ MAIN PANEL ══ -->
<div class="main-panel">

<!-- Topbar -->
<div class="topbar">
    <div class="topbar-title">
        <?php
        $topbarTitles = [
            'home'           => '<i class="fas fa-house"></i> Beranda',
            'konsultasi'     => '<i class="fas fa-stethoscope"></i> Konsultasi',
            'gejala'         => '<i class="fas fa-clipboard-list"></i> Data Gejala',
            'tentang'        => '<i class="fas fa-circle-info"></i> Tentang',
            'admin-dashboard'=> '<i class="fas fa-gauge-high"></i> Dashboard Admin',
            'admin-gejala'   => '<i class="fas fa-gear"></i> Kelola Gejala',
            'admin-user'     => '<i class="fas fa-users-gear"></i> Kelola User',
            'admin-riwayat'  => '<i class="fas fa-clock-rotate-left"></i> Riwayat Konsultasi',
        ];
        echo $topbarTitles[$activePage ?? ''] ?? '<i class="fas fa-brain"></i> ' . htmlspecialchars($pageTitle ?? 'SiPakar CF');
        ?>
    </div>
    <div class="topbar-actions">
        <?php if (isAdmin()): ?>
            <a href="<?= $base ?? '' ?>admin/logout.php" class="btn btn-sm btn-danger" style="padding:6px 12px;">
                <i class="fas fa-right-from-bracket"></i> Logout
            </a>
        <?php elseif (isUser()): ?>
            <span style="font-size:0.82rem;color:var(--text-muted);">
                <i class="fas fa-user"></i> <?= htmlspecialchars($_SESSION['user_nama'] ?? '') ?>
            </span>
            <a href="<?= $base ?? '' ?>user/logout.php" class="btn btn-sm btn-ghost" style="padding:6px 12px;">
                <i class="fas fa-right-from-bracket"></i> Logout
            </a>
        <?php else: ?>
            <a href="<?= $base ?? '' ?>auth/login.php" class="btn btn-sm btn-ghost" style="padding:6px 12px;">
                <i class="fas fa-right-to-bracket"></i> Login
            </a>
        <?php endif; ?>
    </div>
</div>

<main class="main-content">

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
}
function closeSidebar() {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
}
</script>
