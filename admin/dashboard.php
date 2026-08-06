<?php
session_start();
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$pageTitle   = 'Dashboard Admin';
$activePage  = 'admin';
$base        = '../';
$isAdminPage = true;

$totalGejala     = $conn->query("SELECT COUNT(*) AS n FROM gejala")->fetch_assoc()['n'];
$totalKonsultasi = $conn->query("SELECT COUNT(*) AS n FROM konsultasi")->fetch_assoc()['n'];
$totalTinggi     = $conn->query("SELECT COUNT(*) AS n FROM konsultasi WHERE level_risiko IN ('Risiko Tinggi','Risiko Sangat Tinggi')")->fetch_assoc()['n'];
$avgCF           = $conn->query("SELECT AVG(nilai_cf) AS avg FROM konsultasi")->fetch_assoc()['avg'] ?? 0;

$distribusi = [];
$qDist = $conn->query("SELECT level_risiko, COUNT(*) AS jumlah FROM konsultasi GROUP BY level_risiko ORDER BY jumlah DESC");
while ($r = $qDist->fetch_assoc()) {
    $distribusi[] = $r;
}

$recentQ = $conn->query("SELECT * FROM konsultasi ORDER BY tanggal DESC LIMIT 10");

require_once '../includes/header.php';
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div class="page-title"><i class="fas fa-gauge-high"></i> Dashboard Admin</div>
        <div class="page-sub">Panel administrasi SiPakar CF &mdash; kelola data gejala dan pantau aktivitas sistem.</div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="kelola-gejala.php" class="btn btn-primary btn-sm"><i class="fas fa-gear"></i> Kelola Gejala</a>
        <a href="logout.php"        class="btn btn-danger btn-sm"><i class="fas fa-right-from-bracket"></i> Logout</a>
    </div>
</div>

<!-- STAT CARDS -->
<div class="grid-4">
    <div class="stat-card blue">
        <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-number"><?= $totalGejala ?></div>
        <div class="stat-label">Indikator Gejala</div>
    </div>
    <div class="stat-card green">
        <div class="stat-icon green"><i class="fas fa-stethoscope"></i></div>
        <div class="stat-number"><?= $totalKonsultasi ?></div>
        <div class="stat-label">Total Konsultasi</div>
    </div>
    <div class="stat-card amber">
        <div class="stat-icon amber"><i class="fas fa-chart-line"></i></div>
        <div class="stat-number"><?= number_format((float)$avgCF, 3) ?></div>
        <div class="stat-label">Rata-rata Nilai CF</div>
    </div>
    <div class="stat-card rose">
        <div class="stat-icon rose"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="stat-number"><?= $totalTinggi ?></div>
        <div class="stat-label">Risiko Tinggi / S.Tinggi</div>
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
    <div style="margin-bottom:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:6px;">
            <span class="risk-badge" style="background:<?= $bg ?>;"><?= htmlspecialchars($d['level_risiko']) ?></span>
            <span style="font-weight:700;color:#e2e8f0;"><?= $d['jumlah'] ?> <span style="color:var(--text-muted);font-weight:400;">(<?= $pct ?>%)</span></span>
        </div>
        <div style="background:rgba(255,255,255,0.1);border-radius:20px;height:8px;overflow:hidden;">
            <div style="width:<?= $pct ?>%;height:100%;background:<?= $bg ?>;border-radius:20px;transition:width 0.8s ease;"></div>
        </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
</div>

<!-- QUICK LINKS -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-bolt"></i></span>
        Aksi Cepat Admin
    </div>
    <div style="display:grid;gap:12px;">
        <a href="kelola-gejala.php" class="btn btn-primary" style="justify-content:flex-start;">
            <i class="fas fa-gear"></i> Kelola Data Gejala (MB/MD/CF)
        </a>
        <a href="../pages/riwayat.php" class="btn btn-ghost" style="justify-content:flex-start;">
            <i class="fas fa-clock-rotate-left"></i> Lihat Semua Riwayat Konsultasi
        </a>
        <a href="../pages/konsultasi.php" class="btn btn-ghost" style="justify-content:flex-start;">
            <i class="fas fa-stethoscope"></i> Coba Konsultasi
        </a>
        <a href="../pages/gejala.php" class="btn btn-ghost" style="justify-content:flex-start;">
            <i class="fas fa-list"></i> Lihat Data Gejala Publik
        </a>
        <a href="logout.php" class="btn btn-danger" style="justify-content:flex-start;">
            <i class="fas fa-right-from-bracket"></i> Logout dari Admin Panel
        </a>
    </div>
</div>
</div>

<!-- 10 KONSULTASI TERBARU -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(16,185,129,0.15);"><i class="fas fa-clock"></i></span>
        10 Konsultasi Terbaru
    </div>
    <?php if ($totalKonsultasi === 0): ?>
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
                        <div style="display:flex;flex-wrap:wrap;gap:3px;max-width:200px;">
                        <?php foreach (array_slice($kodes, 0, 5) as $k): ?>
                            <span style="background:rgba(59,130,246,0.15);color:#93c5fd;padding:2px 7px;border-radius:4px;font-size:0.72rem;font-family:monospace;"><?= trim($k) ?></span>
                        <?php endforeach; ?>
                        <?php if (count($kodes) > 5): ?>
                            <span style="color:var(--text-muted);font-size:0.72rem;">+<?= count($kodes)-5 ?></span>
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
                        <a href="../pages/riwayat.php?hapus=<?= $row['id'] ?>"
                           onclick="return confirm('Hapus data ini?')"
                           class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <div style="text-align:center;margin-top:16px;">
        <a href="../pages/riwayat.php" class="btn btn-ghost btn-sm">Lihat Semua Riwayat <i class="fas fa-arrow-right"></i></a>
    </div>
    <?php endif; ?>
</div>

<?php require_once '../includes/footer.php'; ?>
