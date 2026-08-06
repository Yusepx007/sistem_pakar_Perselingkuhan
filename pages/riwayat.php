<?php
session_start();
require_once '../includes/config.php';
$pageTitle  = 'Riwayat Konsultasi';
$activePage = 'riwayat';
$base       = '../';

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM konsultasi WHERE id = $id");
    header('Location: riwayat.php?pesan=hapus');
    exit;
}

$pesan = $_GET['pesan'] ?? '';

$perPage   = 12;
$page      = max(1, (int)($_GET['p'] ?? 1));
$offset    = ($page - 1) * $perPage;
$total     = $conn->query("SELECT COUNT(*) AS n FROM konsultasi")->fetch_assoc()['n'];
$totalPage = max(1, ceil($total / $perPage));

$result = $conn->query("SELECT * FROM konsultasi ORDER BY tanggal DESC LIMIT $perPage OFFSET $offset");

$avgCF     = $conn->query("SELECT AVG(nilai_cf) AS avg FROM konsultasi")->fetch_assoc()['avg'] ?? 0;
$totalHigh = $conn->query("SELECT COUNT(*) AS n FROM konsultasi WHERE level_risiko IN ('Risiko Tinggi','Risiko Sangat Tinggi')")->fetch_assoc()['n'];

require_once '../includes/header.php';
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div class="page-title"><i class="fas fa-clock-rotate-left"></i> Riwayat Konsultasi</div>
        <div class="page-sub">Data seluruh hasil konsultasi yang pernah dilakukan melalui sistem ini.</div>
    </div>
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <a href="konsultasi.php" class="btn btn-primary btn-sm"><i class="fas fa-stethoscope"></i> Konsultasi Baru</a>
    </div>
</div>

<?php if ($pesan === 'hapus'): ?>
<div class="alert alert-success"><span class="alert-icon"><i class="fas fa-circle-check"></i></span><div>Data berhasil dihapus dari riwayat.</div></div>
<?php endif; ?>

<?php if ($total === 0): ?>
<div class="card">
    <div class="empty-state">
        <div class="empty-icon"><i class="fas fa-inbox" style="font-size:3rem;color:var(--text-muted);"></i></div>
        <h3>Belum ada riwayat konsultasi</h3>
        <p>Mulai konsultasi pertama Anda dan hasilnya akan tersimpan di sini.</p>
        <a href="konsultasi.php" class="btn btn-primary"><i class="fas fa-stethoscope"></i> Mulai Konsultasi</a>
    </div>
</div>
<?php else: ?>

<!-- STATISTIK RINGKAS -->
<div class="grid-3" style="margin-bottom:24px;">
    <div class="stat-card blue" style="padding:20px;">
        <div class="stat-number"><?= $total ?></div>
        <div class="stat-label">Total Konsultasi</div>
    </div>
    <div class="stat-card amber" style="padding:20px;">
        <div class="stat-number"><?= number_format((float)$avgCF, 3) ?></div>
        <div class="stat-label">Rata-rata Nilai CF</div>
    </div>
    <div class="stat-card rose" style="padding:20px;">
        <div class="stat-number"><?= $totalHigh ?></div>
        <div class="stat-label">Risiko Tinggi / S.Tinggi</div>
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
                    <th>Tanggal &amp; Waktu</th>
                    <th class="td-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $no = $offset + 1;
            while ($row = $result->fetch_assoc()):
                $kodelist = array_filter(explode(',', $row['gejala_dipilih']));
                $color    = riskColor($row['level_risiko']);
                $cfVal    = (float)$row['nilai_cf'];
            ?>
                <tr>
                    <td class="td-center" style="color:var(--text-muted);font-size:0.82rem;"><?= $no++ ?></td>
                    <td>
                        <span style="font-weight:600;color:#e2e8f0;"><?= htmlspecialchars($row['nama_pengguna']) ?></span>
                    </td>
                    <td>
                        <div style="display:flex;flex-wrap:wrap;gap:4px;max-width:220px;">
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
                    <td>
                        <span class="risk-badge" style="background:<?= $color ?>;"><?= htmlspecialchars($row['level_risiko']) ?></span>
                    </td>
                    <td style="font-size:0.82rem;color:var(--text-muted);white-space:nowrap;">
                        <?= date('d/m/Y', strtotime($row['tanggal'])) ?><br>
                        <span style="font-size:0.75rem;opacity:0.7;"><?= date('H:i', strtotime($row['tanggal'])) ?> WIB</span>
                    </td>
                    <td class="td-center">
                        <a href="riwayat.php?hapus=<?= $row['id'] ?>"
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
            <a href="riwayat.php?p=<?= $page - 1 ?>" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-left"></i></a>
        <?php endif; ?>
        <?php for ($i = max(1, $page-2); $i <= min($totalPage, $page+2); $i++): ?>
            <a href="riwayat.php?p=<?= $i ?>"
               class="btn btn-sm <?= $i === $page ? 'btn-primary' : 'btn-ghost' ?>"><?= $i ?></a>
        <?php endfor; ?>
        <?php if ($page < $totalPage): ?>
            <a href="riwayat.php?p=<?= $page + 1 ?>" class="btn btn-ghost btn-sm"><i class="fas fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>

<?php require_once '../includes/footer.php'; ?>
