<?php
session_start();
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$pageTitle  = 'Dashboard Admin';
$activePage = 'admin-dashboard';
$base       = '../';

$totalGejala     = $conn->query("SELECT COUNT(*) AS n FROM gejala")->fetch_assoc()['n'];
$totalKonsultasi = $conn->query("SELECT COUNT(*) AS n FROM konsultasi")->fetch_assoc()['n'];
$totalTinggi     = $conn->query("SELECT COUNT(*) AS n FROM konsultasi WHERE level_risiko IN ('Risiko Tinggi','Risiko Sangat Tinggi')")->fetch_assoc()['n'];
$avgCF           = $conn->query("SELECT AVG(nilai_cf) AS avg FROM konsultasi")->fetch_assoc()['avg'] ?? 0;
$totalHariIni    = $conn->query("SELECT COUNT(*) AS n FROM konsultasi WHERE DATE(tanggal) = CURDATE()")->fetch_assoc()['n'];

// Jumlah user
$tableCheck = $conn->query("SHOW TABLES LIKE 'users'");
$totalUsers = 0;
if ($tableCheck->num_rows > 0) {
    $totalUsers = $conn->query("SELECT COUNT(*) AS n FROM users")->fetch_assoc()['n'];
}

$distribusi = [];
$qDist = $conn->query("SELECT level_risiko, COUNT(*) AS jumlah FROM konsultasi GROUP BY level_risiko ORDER BY jumlah DESC");
while ($r = $qDist->fetch_assoc()) {
    $distribusi[] = $r;
}

$recentQ = $conn->query("SELECT * FROM konsultasi ORDER BY tanggal DESC LIMIT 8");

require_once '../includes/header.php';
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div class="page-title"><i class="fas fa-gauge-high"></i> Dashboard Admin</div>
        <div class="page-sub">Panel administrasi SiPakar CF &mdash; pantau statistik dan kelola sistem.</div>
    </div>
</div>

<!-- STAT CARDS -->
<div class="grid-4">
    <div class="stat-card blue animate-up">
        <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-number"><?= $totalGejala ?></div>
        <div class="stat-label">Indikator Gejala</div>
    </div>
    <div class="stat-card green animate-up" style="animation-delay:0.07s">
        <div class="stat-icon green"><i class="fas fa-stethoscope"></i></div>
        <div class="stat-number"><?= $totalKonsultasi ?></div>
        <div class="stat-label">Total Konsultasi</div>
    </div>
    <div class="stat-card amber animate-up" style="animation-delay:0.14s">
        <div class="stat-icon amber"><i class="fas fa-users"></i></div>
        <div class="stat-number"><?= $totalUsers ?></div>
        <div class="stat-label">Total User</div>
    </div>
    <div class="stat-card rose animate-up" style="animation-delay:0.21s">
        <div class="stat-icon rose"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="stat-number"><?= $totalTinggi ?></div>
        <div class="stat-label">Risiko Tinggi / S.Tinggi</div>
    </div>
</div>

<!-- SECOND ROW STATS -->
<div class="grid-3" style="margin-bottom:28px;">
    <div class="stat-card blue" style="padding:20px;">
        <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Rata-rata CF</div>
        <div class="stat-number" style="font-size:1.8rem;"><?= number_format((float)$avgCF, 3) ?></div>
    </div>
    <div class="stat-card green" style="padding:20px;">
        <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Konsultasi Hari Ini</div>
        <div class="stat-number" style="font-size:1.8rem;"><?= $totalHariIni ?></div>
    </div>
    <div class="stat-card amber" style="padding:20px;">
        <div style="font-size:0.78rem;color:var(--text-muted);margin-bottom:6px;font-weight:600;text-transform:uppercase;letter-spacing:0.5px;">Level Distribusi</div>
        <div class="stat-number" style="font-size:1.8rem;"><?= count($distribusi) ?></div>
    </div>
</div>

<div class="grid-2">
<!-- DISTRIBUSI LEVEL RISIKO -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(245,158,11,0.15);"><i class="fas fa-chart-pie"></i></span>
        Distribusi Level Risiko
    </div>
    <?php if (empty($distribusi)): ?>
    <div class="empty-state" style="padding:40px 20px;">
        <div style="font-size:2.5rem;margin-bottom:12px;color:var(--text-muted);"><i class="fas fa-inbox"></i></div>
        <p>Belum ada data konsultasi.</p>
    </div>
    <?php else: ?>
    <?php foreach ($distribusi as $d):
        $pct = $totalKonsultasi > 0 ? round($d['jumlah'] / $totalKonsultasi * 100) : 0;
        $bg  = riskColor($d['level_risiko']);
    ?>
    <div style="margin-bottom:16px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span class="risk-badge" style="background:<?= $bg ?>"><?= htmlspecialchars($d['level_risiko']) ?></span>
            <span style="font-weight:700;color:#e2e8f0;"><?= $d['jumlah'] ?> <span style="color:var(--text-muted);font-weight:400;">(<?= $pct ?>%)</span></span>
        </div>
        <div style="background:rgba(255,255,255,0.1);border-radius:20px;height:8px;overflow:hidden;">
            <div style="width:<?= $pct ?>%;height:100%;background:<?= $bg ?>;border-radius:20px;transition:width 0.8s ease;"></div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- AKSI CEPAT -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-bolt"></i></span>
        Aksi Cepat Admin
    </div>
    <div class="quick-action-grid">
        <a href="kelola-gejala.php" class="quick-action-card">
            <div class="quick-action-icon" style="background:rgba(59,130,246,0.15);color:#93c5fd;">
                <i class="fas fa-gear"></i>
            </div>
            <div>
                <div class="quick-action-label">Kelola Gejala</div>
                <div class="quick-action-desc">Tambah / ubah indikator</div>
            </div>
        </a>
        <a href="kelola-user.php" class="quick-action-card">
            <div class="quick-action-icon" style="background:rgba(16,185,129,0.15);color:#6ee7b7;">
                <i class="fas fa-user-plus"></i>
            </div>
            <div>
                <div class="quick-action-label">Tambah User</div>
                <div class="quick-action-desc">Buat akun pengguna baru</div>
            </div>
        </a>
        <a href="riwayat-admin.php" class="quick-action-card">
            <div class="quick-action-icon" style="background:rgba(139,92,246,0.15);color:#c4b5fd;">
                <i class="fas fa-clock-rotate-left"></i>
            </div>
            <div>
                <div class="quick-action-label">Riwayat</div>
                <div class="quick-action-desc">Semua hasil konsultasi</div>
            </div>
        </a>
        <a href="../pages/konsultasi.php" class="quick-action-card">
            <div class="quick-action-icon" style="background:rgba(20,184,166,0.15);color:#5eead4;">
                <i class="fas fa-stethoscope"></i>
            </div>
            <div>
                <div class="quick-action-label">Coba Konsultasi</div>
                <div class="quick-action-desc">Tes sistem pakar</div>
            </div>
        </a>
    </div>
</div>
</div>

<!-- 8 KONSULTASI TERBARU -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(16,185,129,0.15);"><i class="fas fa-clock"></i></span>
        Konsultasi Terbaru
        <a href="riwayat-admin.php" class="btn btn-ghost btn-sm" style="margin-left:auto;">
            Lihat Semua <i class="fas fa-arrow-right"></i>
        </a>
    </div>
    <?php if ($totalKonsultasi == 0): ?>
    <div class="empty-state" style="padding:40px 20px;">
        <p>Belum ada data konsultasi.</p>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table class="mini-table">
            <thead>
                <tr>
                    <th>Nama</th>
                    <th>Gejala</th>
                    <th class="td-center">CF</th>
                    <th>Level</th>
                    <th>Waktu</th>
                    <th class="td-center">Hapus</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = $recentQ->fetch_assoc()):
                $kodes = array_filter(explode(',', $row['gejala_dipilih']));
                $cfVal = (float)$row['nilai_cf'];
            ?>
                <tr>
                    <td style="font-weight:600;"><?= htmlspecialchars($row['nama_pengguna']) ?></td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:3px;max-width:180px;">
                        <?php foreach (array_slice($kodes, 0, 4) as $k): ?>
                            <span style="background:rgba(59,130,246,0.15);color:#93c5fd;padding:2px 7px;border-radius:4px;font-size:0.72rem;font-family:monospace;"><?= trim($k) ?></span>
                        <?php endforeach; ?>
                        <?php if (count($kodes) > 4): ?>
                            <span style="color:var(--text-muted);font-size:0.72rem;">+<?= count($kodes)-4 ?></span>
                        <?php endif; ?>
                        </div>
                    </td>
                    <td class="td-center">
                        <strong style="color:<?= $cfVal >= 0.8 ? '#fca5a5' : ($cfVal >= 0.6 ? '#fdba74' : ($cfVal >= 0.4 ? '#fcd34d' : '#6ee7b7')) ?>;">
                            <?= number_format($cfVal, 3) ?>
                        </strong>
                    </td>
                    <td><span class="risk-badge" style="background:<?= riskColor($row['level_risiko']) ?>;font-size:0.72rem;"><?= htmlspecialchars($row['level_risiko']) ?></span></td>
                    <td style="font-size:0.78rem;color:var(--text-muted);"><?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?></td>
                    <td class="td-center">
                        <a href="riwayat-admin.php?hapus=<?= $row['id'] ?>"
                           onclick="return confirm('Hapus data ini?')"
                           class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
