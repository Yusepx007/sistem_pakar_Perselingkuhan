<?php
session_start();
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$pageTitle  = 'Riwayat Konsultasi';
$activePage = 'admin-riwayat';
$base       = '../';

// Hapus record
if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM konsultasi WHERE id = $id");
    header('Location: riwayat-admin.php?pesan=hapus');
    exit;
}

// Hapus semua
if (isset($_GET['hapus_semua']) && $_GET['hapus_semua'] === 'ya') {
    $conn->query("DELETE FROM konsultasi");
    header('Location: riwayat-admin.php?pesan=hapus_semua');
    exit;
}

$pesan = $_GET['pesan'] ?? '';

// Filter level
$filterLevel = $_GET['level'] ?? 'semua';
$allLevels = ['semua','Risiko Sangat Rendah','Risiko Rendah','Risiko Sedang','Risiko Tinggi','Risiko Sangat Tinggi'];
if (!in_array($filterLevel, $allLevels)) $filterLevel = 'semua';

$where = $filterLevel !== 'semua' ? "WHERE level_risiko = '" . $conn->real_escape_string($filterLevel) . "'" : '';

$perPage   = 15;
$page      = max(1, (int)($_GET['p'] ?? 1));
$offset    = ($page - 1) * $perPage;
$total     = $conn->query("SELECT COUNT(*) AS n FROM konsultasi $where")->fetch_assoc()['n'];
$totalPage = max(1, ceil($total / $perPage));

$result = $conn->query("SELECT * FROM konsultasi $where ORDER BY tanggal DESC LIMIT $perPage OFFSET $offset");
$anonMap = getAnonMap($conn);

$avgCF     = $conn->query("SELECT AVG(nilai_cf) AS avg FROM konsultasi")->fetch_assoc()['avg'] ?? 0;
$totalHigh = $conn->query("SELECT COUNT(*) AS n FROM konsultasi WHERE level_risiko IN ('Risiko Tinggi','Risiko Sangat Tinggi')")->fetch_assoc()['n'];
$totalAll  = $conn->query("SELECT COUNT(*) AS n FROM konsultasi")->fetch_assoc()['n'];

require_once '../includes/header.php';
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div class="page-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Konsultasi</div>
        <div class="page-sub">Seluruh data hasil konsultasi pengguna sistem SiPakar CF.</div>
    </div>
    <div style="display:flex;gap:8px;flex-wrap:wrap;">
        <?php if ($totalAll > 0): ?>
        <a href="riwayat-admin.php?hapus_semua=ya"
           onclick="return confirm('Hapus SEMUA riwayat konsultasi? Tindakan ini tidak dapat dibatalkan!')"
           class="btn btn-danger btn-sm">
            <i class="fas fa-trash-can"></i> Hapus Semua
        </a>
        <?php endif; ?>
        <a href="dashboard.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Dashboard</a>
    </div>
</div>

<?php if ($pesan === 'hapus'): ?>
<div class="alert alert-success"><span class="alert-icon"><i class="fas fa-circle-check"></i></span><div>Data berhasil dihapus.</div></div>
<?php elseif ($pesan === 'hapus_semua'): ?>
<div class="alert alert-success"><span class="alert-icon"><i class="fas fa-circle-check"></i></span><div>Semua riwayat berhasil dihapus.</div></div>
<?php endif; ?>

<!-- STATISTIK RINGKAS -->
<div class="grid-3" style="margin-bottom:24px;">
    <div class="stat-card blue" style="padding:20px;">
        <div class="stat-number" style="font-size:1.8rem;"><?= $totalAll ?></div>
        <div class="stat-label">Total Konsultasi</div>
    </div>
    <div class="stat-card amber" style="padding:20px;">
        <div class="stat-number" style="font-size:1.8rem;"><?= number_format((float)$avgCF, 3) ?></div>
        <div class="stat-label">Rata-rata Nilai CF</div>
    </div>
    <div class="stat-card rose" style="padding:20px;">
        <div class="stat-number" style="font-size:1.8rem;"><?= $totalHigh ?></div>
        <div class="stat-label">Risiko Tinggi / S.Tinggi</div>
    </div>
</div>

<?php if ($totalAll === 0): ?>
<div class="card">
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-inbox"></i></div>
        <h3>Belum ada riwayat konsultasi</h3>
        <p>Data akan muncul setelah pengguna melakukan konsultasi.</p>
        <a href="../pages/konsultasi.php" class="btn btn-primary"><i class="fas fa-stethoscope"></i> Coba Konsultasi</a>
    </div>
</div>
<?php else: ?>

<!-- FILTER LEVEL -->
<div class="card" style="padding:16px 20px;margin-bottom:20px;">
    <div class="filter-bar">
        <span class="filter-label">Filter Level:</span>
        <a href="riwayat-admin.php" class="btn btn-sm <?= $filterLevel === 'semua' ? 'btn-primary' : 'btn-ghost' ?>">
            Semua (<?= $totalAll ?>)
        </a>
        <?php foreach (array_slice($allLevels, 1) as $lv): ?>
        <a href="riwayat-admin.php?level=<?= urlencode($lv) ?>"
           class="btn btn-sm <?= $filterLevel === $lv ? 'btn-primary' : 'btn-ghost' ?>"
           style="<?= $filterLevel === $lv ? '' : 'border-color:' . riskColor($lv) . '33;' ?>">
            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:<?= riskColor($lv) ?>;margin-right:4px;"></span>
            <?= str_replace('Risiko ', '', $lv) ?>
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- TABEL RIWAYAT -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-table-list"></i></span>
        Data Riwayat
        <span style="margin-left:auto;font-size:0.82rem;font-weight:500;color:var(--text-muted);">
            Halaman <?= $page ?> / <?= $totalPage ?> &bull; <?= $total ?> entri
        </span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="td-center">#</th>
                    <th>Nama</th>
                    <th>Gejala Dipilih</th>
                    <th class="td-center">Nilai CF</th>
                    <th>Level Risiko</th>
                    <th>Tanggal & Waktu</th>
                    <th class="td-center">Hapus</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = $offset + 1;
            while ($row = $result->fetch_assoc()):
                $kodelist = array_filter(explode(',', $row['gejala_dipilih']));
                $color    = riskColor($row['level_risiko']);
                $cfVal    = (float)$row['nilai_cf'];
                $namaDisplay = (strcasecmp(trim($row['nama_pengguna'] ?? ''), 'anonim') === 0 || trim($row['nama_pengguna'] ?? '') === '')
                    ? ($anonMap[$row['id']] ?? 'Anonim')
                    : $row['nama_pengguna'];
            ?>
                <tr>
                    <td class="td-center" style="color:var(--text-muted);font-size:0.82rem;"><?= $no++ ?></td>
                    <td><span style="font-weight:600;color:#e2e8f0;"><?= htmlspecialchars($namaDisplay) ?></span></td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:200px;">
                        <?php foreach ($kodelist as $kode): ?>
                            <span style="background:rgba(59,130,246,0.15);color:#93c5fd;padding:2px 8px;border-radius:5px;font-size:0.75rem;font-weight:600;font-family:monospace;"><?= trim($kode) ?></span>
                        <?php endforeach; ?>
                        </div>
                        <div style="font-size:0.75rem;color:var(--text-muted);margin-top:4px;"><?= count($kodelist) ?> gejala</div>
                    </td>
                    <td class="td-center">
                        <strong style="font-size:1.05rem;color:<?= $cfVal >= 0.8 ? '#fca5a5' : ($cfVal >= 0.6 ? '#fdba74' : ($cfVal >= 0.4 ? '#fcd34d' : '#6ee7b7')) ?>;">
                            <?= number_format($cfVal, 3) ?>
                        </strong>
                    </td>
                    <td><span class="risk-badge" style="background:<?= $color ?>;"><?= htmlspecialchars($row['level_risiko']) ?></span></td>
                    <td style="font-size:0.82rem;color:var(--text-muted);white-space:nowrap;">
                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?><br>
                        <span style="font-size:0.75rem;opacity:0.7;"><?= date('H:i', strtotime($row['tanggal'])) ?> WIB</span>
                    </td>
                    <td class="td-center">
                        <a href="riwayat-admin.php?hapus=<?= $row['id'] ?><?= $filterLevel !== 'semua' ? '&level=' . urlencode($filterLevel) : '' ?>"
                           onclick="return confirm('Hapus data konsultasi ini?')"
                           class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></a>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <?php if ($totalPage > 1): ?>
    <div class="pagination">
        <?php if ($page > 1): ?>
            <a href="riwayat-admin.php?p=<?= $page - 1 ?><?= $filterLevel !== 'semua' ? '&level=' . urlencode($filterLevel) : '' ?>" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($i = max(1, $page-2); $i <= min($totalPage, $page+2); $i++): ?>
            <a href="riwayat-admin.php?p=<?= $i ?><?= $filterLevel !== 'semua' ? '&level=' . urlencode($filterLevel) : '' ?>"
               class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-ghost' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPage): ?>
            <a href="riwayat-admin.php?p=<?= $page + 1 ?><?= $filterLevel !== 'semua' ? '&level=' . urlencode($filterLevel) : '' ?>" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
