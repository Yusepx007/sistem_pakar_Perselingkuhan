<?php
session_start();
require_once '../includes/config.php';

// Jika admin, arahkan ke riwayat admin lengkap
if (isAdmin()) {
    header('Location: ../admin/riwayat-admin.php');
    exit;
}

// Privilage: hanya pengguna yang punya akun & login yang bisa melihat riwayat diagnosa
if (!isUser()) {
    $pageTitle  = 'Akses Terbatas';
    $activePage = 'riwayat';
    $base       = '../';
    require_once '../includes/header.php';
    ?>
    <div class="card animate-up" style="max-width:600px;margin:40px auto;text-align:center;padding:48px 32px;">
        <div style="width:72px;height:72px;background:rgba(59,130,246,0.15);border-radius:20px;display:flex;align-items:center;justify-content:center;font-size:2.2rem;margin:0 auto 20px;color:#93c5fd;">
            <i class="fas fa-lock"></i>
        </div>
        <h2 style="font-family:'Space Grotesk',sans-serif;font-size:1.5rem;color:#fff;margin-bottom:10px;">Fitur Khusus Pengguna Terdaftar</h2>
        <p style="color:var(--text-muted);font-size:0.92rem;line-height:1.7;margin-bottom:28px;">
            Riwayat hasil diagnosa tersimpan secara terus-menerus dan hanya dapat diakses oleh pengguna yang memiliki akun resmi.
        </p>
        <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
            <a href="../auth/login.php" class="btn btn-primary btn-lg">
                <i class="fas fa-right-to-bracket"></i> Login Akun Sekarang
            </a>
            <a href="konsultasi.php" class="btn btn-ghost btn-lg">
                <i class="fas fa-stethoscope"></i> Konsultasi Tamu
            </a>
        </div>
    </div>
    <?php
    require_once '../includes/footer.php';
    exit;
}

$pageTitle  = 'Riwayat Diagnosa Saya';
$activePage = 'riwayat';
$base       = '../';

$userName = $_SESSION['user_nama'] ?? '';
$userUser = $_SESSION['user_username'] ?? '';

// Hapus record riwayat sendiri
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $idHapus = (int)$_GET['hapus'];
    $stmtHapus = $conn->prepare("DELETE FROM konsultasi WHERE id = ? AND (nama_pengguna = ? OR nama_pengguna = ?)");
    $stmtHapus->bind_param("iss", $idHapus, $userName, $userUser);
    $stmtHapus->execute();
    header('Location: riwayat.php?pesan=hapus');
    exit;
}

$pesan = $_GET['pesan'] ?? '';

// Query riwayat khusus user ini
$stmtCount = $conn->prepare("SELECT COUNT(*) AS n FROM konsultasi WHERE (nama_pengguna = ? OR nama_pengguna = ?)");
$stmtCount->bind_param("ss", $userName, $userUser);
$stmtCount->execute();
$totalRiwayat = $stmtCount->get_result()->fetch_assoc()['n'];

$stmtAvg = $conn->prepare("SELECT AVG(nilai_cf) AS avg_cf, MAX(nilai_cf) AS max_cf FROM konsultasi WHERE (nama_pengguna = ? OR nama_pengguna = ?)");
$stmtAvg->bind_param("ss", $userName, $userUser);
$stmtAvg->execute();
$statUser = $stmtAvg->get_result()->fetch_assoc();

$stmtList = $conn->prepare("SELECT * FROM konsultasi WHERE (nama_pengguna = ? OR nama_pengguna = ?) ORDER BY tanggal DESC");
$stmtList->bind_param("ss", $userName, $userUser);
$stmtList->execute();
$riwayatList = $stmtList->get_result();

require_once '../includes/header.php';
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div class="page-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Diagnosa Saya</div>
        <div class="page-sub">Data seluruh hasil konsultasi &amp; diagnosa Anda yang tersimpan secara berkelanjutan.</div>
    </div>
    <a href="konsultasi.php" class="btn btn-primary btn-sm"><i class="fas fa-stethoscope"></i> Konsultasi Baru</a>
</div>

<?php if ($pesan === 'hapus'): ?>
<div class="alert alert-success"><span class="alert-icon"><i class="fas fa-circle-check"></i></span><div>Riwayat diagnosa berhasil dihapus.</div></div>
<?php endif; ?>

<!-- STAT KARTU PENGGUNA -->
<div class="grid-3" style="margin-bottom:28px;">
    <div class="stat-card blue animate-up">
        <div class="stat-icon blue"><i class="fas fa-file-medical"></i></div>
        <div class="stat-number"><?= $totalRiwayat ?></div>
        <div class="stat-label">Total Diagnosa Anda</div>
    </div>
    <div class="stat-card amber animate-up" style="animation-delay:0.08s">
        <div class="stat-icon amber"><i class="fas fa-chart-line"></i></div>
        <div class="stat-number"><?= number_format((float)($statUser['avg_cf'] ?? 0), 3) ?></div>
        <div class="stat-label">Rata-rata Nilai CF</div>
    </div>
    <div class="stat-card green animate-up" style="animation-delay:0.16s">
        <div class="stat-icon green"><i class="fas fa-shield-halved"></i></div>
        <div class="stat-number"><?= number_format((float)($statUser['max_cf'] ?? 0), 3) ?></div>
        <div class="stat-label">CF Tertinggi Anda</div>
    </div>
</div>

<!-- TABEL RIWAYAT DIAGNOSA -->
<div class="card animate-up" style="animation-delay:0.2s">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-table-list"></i></span>
        Daftar Hasil Diagnosa
        <span style="margin-left:auto;font-size:0.82rem;font-weight:500;color:var(--text-muted);"><?= $totalRiwayat ?> data tersimpan</span>
    </div>

    <?php if ($totalRiwayat == 0): ?>
    <div class="empty-state" style="padding:60px 20px;">
        <div style="font-size:3rem;margin-bottom:14px;color:var(--text-muted);"><i class="fas fa-stethoscope"></i></div>
        <h3 style="font-size:1.15rem;color:#e2e8f0;margin-bottom:8px;">Belum Ada Riwayat Diagnosa</h3>
        <p style="color:var(--text-muted);font-size:0.88rem;margin-bottom:20px;">Anda belum melakukan konsultasi atau belum menyimpan hasil diagnosa.</p>
        <a href="konsultasi.php" class="btn btn-primary"><i class="fas fa-stethoscope"></i> Mulai Konsultasi Pertama</a>
    </div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="td-center">#</th>
                    <th>Gejala yang Diamati</th>
                    <th class="td-center">Nilai CF</th>
                    <th>Tingkat Risiko</th>
                    <th>Tanggal Diagnosa</th>
                    <th class="td-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = 1;
            while ($row = $riwayatList->fetch_assoc()):
                $kodes = array_filter(explode(',', $row['gejala_dipilih']));
                $cfVal = (float)$row['nilai_cf'];
                $color = riskColor($row['level_risiko']);
            ?>
                <tr>
                    <td class="td-center" style="color:var(--text-muted);font-size:0.85rem;"><?= $no++ ?></td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:320px;">
                        <?php foreach ($kodes as $k): ?>
                            <span style="background:rgba(59,130,246,0.15);color:#93c5fd;padding:2px 8px;border-radius:5px;font-size:0.75rem;font-weight:700;font-family:monospace;"><?= trim($k) ?></span>
                        <?php endforeach; ?>
                        </div>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;"><?= count($kodes) ?> indikator dipilih</div>
                    </td>
                    <td class="td-center">
                        <strong style="font-size:1.1rem;color:<?= $cfVal >= 0.8 ? '#fca5a5' : ($cfVal >= 0.6 ? '#fdba74' : ($cfVal >= 0.4 ? '#fcd34d' : '#6ee7b7')) ?>;">
                            <?= number_format($cfVal, 3) ?>
                        </strong>
                    </td>
                    <td>
                        <span class="risk-badge" style="background:<?= $color ?>;font-size:0.78rem;">
                            <?= htmlspecialchars($row['level_risiko']) ?>
                        </span>
                    </td>
                    <td style="font-size:0.82rem;color:var(--text-muted);white-space:nowrap;">
                        <i class="fas fa-calendar-day" style="opacity:0.6;margin-right:4px;"></i> <?= date('d/m/Y', strtotime($row['tanggal'])) ?><br>
                        <span style="font-size:0.75rem;opacity:0.75;"><i class="fas fa-clock" style="opacity:0.6;margin-right:4px;"></i> <?= date('H:i', strtotime($row['tanggal'])) ?> WIB</span>
                    </td>
                    <td class="td-center">
                        <a href="riwayat.php?hapus=<?= $row['id'] ?>"
                           onclick="return confirm('Hapus hasil diagnosa tanggal <?= date('d/m/Y H:i', strtotime($row['tanggal'])) ?> ini?')"
                           class="btn btn-danger btn-xs" title="Hapus Riwayat">
                            <i class="fas fa-trash"></i>
                        </a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>

<div class="card" style="padding:20px 24px;margin-top:20px;background:rgba(59,130,246,0.06);border-color:rgba(59,130,246,0.2);">
    <div style="display:flex;align-items:center;gap:14px;">
        <div style="width:42px;height:42px;background:rgba(59,130,246,0.2);border-radius:10px;display:flex;align-items:center;justify-content:center;color:#93c5fd;font-size:1.2rem;flex-shrink:0;">
            <i class="fas fa-shield-check"></i>
        </div>
        <div style="flex:1;">
            <div style="font-weight:700;color:#e8f0fe;font-size:0.92rem;margin-bottom:2px;">Privilege Akun Terverifikasi</div>
            <div style="font-size:0.82rem;color:var(--text-muted);">
                Riwayat diagnosa akun <strong><?= htmlspecialchars($userName) ?></strong> tersimpan aman dan dapat Anda pantau kapan saja untuk mengevaluasi perkembangan kondisi hubungan.
            </div>
        </div>
        <a href="konsultasi.php" class="btn btn-primary btn-sm"><i class="fas fa-rotate"></i> Diagnosa Ulang</a>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
