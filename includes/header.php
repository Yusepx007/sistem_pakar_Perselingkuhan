<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="SiPakar CF — Sistem Pakar Identifikasi Faktor Risiko Perselingkuhan menggunakan metode Certainty Factor. Analisis berbasis pengetahuan psikologi hubungan.">
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

<?php if (($isAdminPage ?? false) && isAdmin()): ?>
<div class="admin-nav">
    <i class="fas fa-shield-halved"></i> <strong>Mode Admin</strong> — Anda sedang masuk sebagai administrator &nbsp;|&nbsp;
    <a href="<?= $base ?? '' ?>admin/logout.php" style="color:#fda4af;font-weight:600;">Keluar</a>
</div>
<?php endif; ?>

<nav class="navbar">
    <div class="nav-container">
        <a class="nav-brand" href="<?= $base ?? '' ?>index.php">
            <span class="nav-brand-icon"><i class="fas fa-brain"></i></span>
            SiPakar CF
        </a>
        <ul class="nav-menu">
            <li><a href="<?= $base ?? '' ?>index.php"             class="<?= ($activePage ?? '') === 'home'      ? 'active' : '' ?>">Beranda</a></li>
            <li><a href="<?= $base ?? '' ?>pages/konsultasi.php"  class="<?= ($activePage ?? '') === 'konsultasi'? 'active' : '' ?>">Konsultasi</a></li>
            <li><a href="<?= $base ?? '' ?>pages/gejala.php"      class="<?= ($activePage ?? '') === 'gejala'    ? 'active' : '' ?>">Data Gejala</a></li>
            <li><a href="<?= $base ?? '' ?>pages/riwayat.php"     class="<?= ($activePage ?? '') === 'riwayat'   ? 'active' : '' ?>">Riwayat</a></li>
            <li><a href="<?= $base ?? '' ?>pages/tentang.php"     class="<?= ($activePage ?? '') === 'tentang'   ? 'active' : '' ?>">Tentang</a></li>
            <?php if (isAdmin()): ?>
            <li><a href="<?= $base ?? '' ?>admin/dashboard.php"   class="nav-admin-btn <?= ($activePage ?? '') === 'admin' ? 'active' : '' ?>"><i class="fas fa-shield-halved"></i> Admin</a></li>
            <?php else: ?>
            <li><a href="<?= $base ?? '' ?>admin/login.php"       class="nav-admin-btn"><i class="fas fa-lock"></i> Admin</a></li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<main class="main-content">
