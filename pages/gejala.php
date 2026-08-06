<?php
session_start();
require_once '../includes/config.php';
$pageTitle  = 'Data Gejala';
$activePage = 'gejala';
$base       = '../';

$allKat = [
    'semua',
    'Perubahan Komunikasi',
    'Perubahan Perilaku Emosional',
    'Perubahan Fisik & Penampilan',
    'Perubahan Sosial & Lingkungan',
    'Perubahan Finansial',
    'Faktor Predisposisi',
];
$filter = $_GET['kategori'] ?? 'semua';
if (!in_array($filter, $allKat)) $filter = 'semua';

$where = '';
if ($filter !== 'semua') {
    $f     = $conn->real_escape_string($filter);
    $where = "WHERE kategori = '$f'";
}

$result  = $conn->query("SELECT * FROM gejala $where ORDER BY kode");
$total   = $conn->query("SELECT COUNT(*) AS n FROM gejala $where")->fetch_assoc()['n'];

$statByKat = [];
$qStat = $conn->query("SELECT kategori, COUNT(*) AS jumlah, AVG(cf) AS avg_cf, MAX(cf) AS max_cf FROM gejala GROUP BY kategori ORDER BY kategori");
while ($r = $qStat->fetch_assoc()) {
    $statByKat[$r['kategori']] = $r;
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <div class="page-title"><i class="fas fa-clipboard-list"></i> Data Gejala</div>
    <div class="page-sub">Daftar lengkap 33 indikator gejala risiko perselingkuhan beserta nilai MB, MD, dan CF yang digunakan sistem pakar.</div>
</div>

<!-- RINGKASAN STATISTIK KATEGORI -->
<div class="grid-3" style="margin-bottom:24px;">
    <?php foreach ($statByKat as $kat => $stat): ?>
    <a href="gejala.php?kategori=<?= urlencode($kat) ?>" style="text-decoration:none;">
        <div class="card" style="margin-bottom:0;padding:18px;<?= $filter === $kat ? 'border-color:rgba(59,130,246,0.5);background:rgba(59,130,246,0.08);' : '' ?>cursor:pointer;">
            <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px;">
                <span style="font-size:1.2rem;color:#93c5fd;"><?= katIcon($kat) ?></span>
                <span class="<?= katClass($kat) ?>" style="font-size:0.72rem;"><?= $kat ?></span>
            </div>
            <div style="font-size:1.5rem;font-weight:800;color:#e2e8f0;"><?= $stat['jumlah'] ?></div>
            <div style="font-size:0.78rem;color:var(--text-muted);margin-top:4px;">Avg CF: <strong style="color:#93c5fd;"><?= number_format((float)$stat['avg_cf'], 2) ?></strong></div>
        </div>
    </a>
    <?php endforeach; ?>
</div>

<!-- FILTER BAR -->
<div class="card" style="padding:16px 20px;margin-bottom:20px;">
    <div class="filter-bar">
        <span class="filter-label">Filter:</span>
        <a href="gejala.php?kategori=semua"
           class="btn btn-sm <?= $filter === 'semua' ? 'btn-primary' : 'btn-ghost' ?>">Semua (33)</a>
        <?php foreach (array_slice($allKat, 1) as $kat): ?>
        <a href="gejala.php?kategori=<?= urlencode($kat) ?>"
           class="btn btn-sm <?= $filter === $kat ? 'btn-primary' : 'btn-ghost' ?>">
            <?= katIcon($kat) ?> <?= htmlspecialchars($kat) ?> (<?= $statByKat[$kat]['jumlah'] ?? 0 ?>)
        </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- TABEL GEJALA -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-chart-bar"></i></span>
        <?= $filter === 'semua' ? 'Semua Gejala' : htmlspecialchars($filter) ?>
        <span style="margin-left:auto;font-size:0.82rem;font-weight:500;color:var(--text-muted);"><?= $total ?> gejala</span>
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi Gejala</th>
                    <th>Kategori</th>
                    <th class="td-center">MB</th>
                    <th class="td-center">MD</th>
                    <th class="td-center">CF</th>
                    <th class="td-center">Bobot Visual</th>
                </tr>
            </thead>
            <tbody>
            <?php
            while ($row = $result->fetch_assoc()):
                $cfVal  = (float)$row['cf'];
                $barW   = max(0, $cfVal * 100);
                $barCls = cfBarClass($cfVal);
                $cfColor = $cfVal >= 0.7 ? '#6ee7b7' : ($cfVal >= 0.4 ? '#fcd34d' : ($cfVal < 0 ? '#fda4af' : '#93c5fd'));
            ?>
                <tr>
                    <td>
                        <span style="background:rgba(59,130,246,0.15);color:#93c5fd;padding:3px 9px;border-radius:6px;font-size:0.82rem;font-weight:700;font-family:monospace;">
                            <?= $row['kode'] ?>
                        </span>
                    </td>
                    <td style="font-size:0.875rem;max-width:320px;"><?= htmlspecialchars($row['nama']) ?></td>
                    <td><span class="<?= katClass($row['kategori']) ?>"><?= htmlspecialchars($row['kategori']) ?></span></td>
                    <td class="td-center"><?= number_format((float)$row['mb'], 2) ?></td>
                    <td class="td-center"><?= number_format((float)$row['md'], 2) ?></td>
                    <td class="td-center">
                        <strong style="color:<?= $cfColor ?>;font-size:1rem;"><?= number_format($cfVal, 2) ?></strong>
                    </td>
                    <td class="td-center">
                        <div style="display:flex;align-items:center;gap:6px;justify-content:center;">
                            <div class="cf-bar-wrap" style="width:80px;">
                                <div class="<?= $barCls ?>" style="width:<?= abs($barW) ?>%"></div>
                            </div>
                            <span style="font-size:0.72rem;color:var(--text-muted);min-width:36px;"><?= number_format(abs($cfVal)*100) ?>%</span>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- KETERANGAN CF -->
<div class="grid-2">
    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(20,184,166,0.15);"><i class="fas fa-circle-info"></i></span>
            Keterangan Formula CF
        </div>
        <div class="formula-box">CF(H, E) = MB(H, E) &minus; MD(H, E)</div>
        <div class="formula-box" style="margin-top:8px;">CF_kom = CF_lama + CF_baru &times; (1 &minus; CF_lama)</div>
        <ul class="tentang-list" style="padding-left:20px;margin-top:16px;">
            <li><strong>MB</strong> &mdash; Measure of Belief: ukuran kepercayaan pakar (0&ndash;1)</li>
            <li><strong>MD</strong> &mdash; Measure of Disbelief: ukuran ketidakpercayaan pakar (0&ndash;1)</li>
            <li><strong>CF</strong> &mdash; Certainty Factor: selisih MB dikurangi MD</li>
            <li>MB dan MD bersifat mutually exclusive (tidak keduanya &gt; 0)</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(245,158,11,0.15);"><i class="fas fa-chart-bar"></i></span>
            Interpretasi Nilai CF
        </div>
        <table class="mini-table">
            <thead><tr><th>Nilai CF</th><th>Level Risiko</th></tr></thead>
            <tbody>
                <tr><td>CF &le; 0,20</td><td><span class="risk-badge" style="background:#059669;">Sangat Rendah</span></td></tr>
                <tr><td>0,20 &lt; CF &le; 0,40</td><td><span class="risk-badge" style="background:#16a34a;">Rendah</span></td></tr>
                <tr><td>0,40 &lt; CF &le; 0,60</td><td><span class="risk-badge" style="background:#d97706;">Sedang</span></td></tr>
                <tr><td>0,60 &lt; CF &le; 0,80</td><td><span class="risk-badge" style="background:#ea580c;">Tinggi</span></td></tr>
                <tr><td>CF &gt; 0,80</td><td><span class="risk-badge" style="background:#dc2626;">Sangat Tinggi</span></td></tr>
            </tbody>
        </table>
        <div style="margin-top:16px;text-align:center;">
            <a href="konsultasi.php" class="btn btn-primary btn-sm"><i class="fas fa-stethoscope"></i> Mulai Konsultasi</a>
        </div>
    </div>
</div>

<?php require_once '../includes/footer.php'; ?>
