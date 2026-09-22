<?php
session_start();
require_once '../includes/config.php';

if (!isAdmin()) {
    header('Location: login.php');
    exit;
}

$pageTitle   = 'Kelola Data Gejala';
$activePage  = 'admin-gejala';
$base        = '../';

$pesan = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'tambah') {
    $kode     = strtoupper(trim($_POST['kode'] ?? ''));
    $nama     = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $mb       = (float)($_POST['mb'] ?? 0);
    $md       = (float)($_POST['md'] ?? 0);
    $cf       = round($mb - $md, 2);

    if (!$kode || !$nama || !$kategori) {
        $error = 'Kode, nama, dan kategori wajib diisi.';
    } elseif ($mb < 0 || $mb > 1 || $md < 0 || $md > 1) {
        $error = 'Nilai MB dan MD harus berada antara 0 dan 1.';
    } else {
        $stmt = $conn->prepare("INSERT INTO gejala (kode, nama, kategori, mb, md, cf) VALUES (?,?,?,?,?,?)");
        $stmt->bind_param("sssddd", $kode, $nama, $kategori, $mb, $md, $cf);
        if ($stmt->execute()) {
            $pesan = "Gejala $kode berhasil ditambahkan (CF = $cf).";
        } else {
            $error = 'Gagal menambahkan gejala. Kode mungkin sudah ada.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['aksi']) && $_POST['aksi'] === 'update') {
    $id       = (int)$_POST['id'];
    $nama     = trim($_POST['nama'] ?? '');
    $kategori = trim($_POST['kategori'] ?? '');
    $mb       = (float)($_POST['mb'] ?? 0);
    $md       = (float)($_POST['md'] ?? 0);
    $cf       = round($mb - $md, 2);

    if (!$nama || !$kategori) {
        $error = 'Nama dan kategori wajib diisi.';
    } elseif ($mb < 0 || $mb > 1 || $md < 0 || $md > 1) {
        $error = 'Nilai MB dan MD harus berada antara 0 dan 1.';
    } else {
        $stmt = $conn->prepare("UPDATE gejala SET nama=?, kategori=?, mb=?, md=?, cf=? WHERE id=?");
        $stmt->bind_param("ssdddi", $nama, $kategori, $mb, $md, $cf, $id);
        if ($stmt->execute()) {
            $pesan = "Gejala berhasil diperbarui (CF = $cf).";
        } else {
            $error = 'Gagal memperbarui gejala.';
        }
    }
}

if (isset($_GET['hapus']) && is_numeric($_GET['hapus'])) {
    $id = (int)$_GET['hapus'];
    $conn->query("DELETE FROM gejala WHERE id = $id");
    header('Location: kelola-gejala.php?pesan=hapus');
    exit;
}
if (($_GET['pesan'] ?? '') === 'hapus') {
    $pesan = 'Gejala berhasil dihapus.';
}

$editData = null;
if (isset($_GET['edit']) && is_numeric($_GET['edit'])) {
    $editData = $conn->query("SELECT * FROM gejala WHERE id = " . (int)$_GET['edit'])->fetch_assoc();
}

$filter  = $_GET['kategori'] ?? 'semua';
$where   = $filter !== 'semua' ? "WHERE kategori = '" . $conn->real_escape_string($filter) . "'" : '';
$allGejala = $conn->query("SELECT * FROM gejala $where ORDER BY kode");
$total   = $conn->query("SELECT COUNT(*) AS n FROM gejala $where")->fetch_assoc()['n'];

$allKat = [
    'Perubahan Komunikasi',
    'Perubahan Perilaku Emosional',
    'Perubahan Fisik & Penampilan',
    'Perubahan Sosial & Lingkungan',
    'Perubahan Finansial',
    'Faktor Predisposisi',
];

require_once '../includes/header.php';
?>

<div class="page-header" style="display:flex;align-items:flex-start;justify-content:space-between;flex-wrap:wrap;gap:16px;">
    <div>
        <div class="page-title"><i class="fas fa-gear"></i> Kelola Data Gejala</div>
        <div class="page-sub">Tambah, ubah, atau hapus indikator gejala beserta nilai MB, MD, dan CF.</div>
    </div>
    <a href="dashboard.php" class="btn btn-ghost btn-sm"><i class="fas fa-arrow-left"></i> Kembali ke Dashboard</a>
</div>

<?php if ($pesan): ?>
<div class="alert alert-success"><span class="alert-icon"><i class="fas fa-circle-check"></i></span><div><?= htmlspecialchars($pesan) ?></div></div>
<?php endif; ?>
<?php if ($error): ?>
<div class="alert alert-danger"><span class="alert-icon"><i class="fas fa-ban"></i></span><div><?= htmlspecialchars($error) ?></div></div>
<?php endif; ?>

<div class="grid-2">
<!-- FORM TAMBAH / EDIT -->
<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(<?= $editData ? '245,158,11' : '16,185,129' ?>,0.15);">
            <i class="fas <?= $editData ? 'fa-pen' : 'fa-plus' ?>"></i>
        </span>
        <?= $editData ? 'Edit Gejala &mdash; ' . htmlspecialchars($editData['kode']) : 'Tambah Gejala Baru' ?>
    </div>

    <form method="POST" id="gejalForm">
        <input type="hidden" name="aksi" value="<?= $editData ? 'update' : 'tambah' ?>">
        <?php if ($editData): ?>
        <input type="hidden" name="id" value="<?= $editData['id'] ?>">
        <?php endif; ?>

        <?php if (!$editData): ?>
        <div class="form-group">
            <label>Kode Gejala <span style="color:#fda4af;">*</span></label>
            <input type="text" name="kode" class="form-input" placeholder="G34" maxlength="10" required
                   style="text-transform:uppercase;max-width:160px;" value="<?= htmlspecialchars($_POST['kode'] ?? '') ?>">
        </div>
        <?php endif; ?>

        <div class="form-group">
            <label>Deskripsi Gejala <span style="color:#fda4af;">*</span></label>
            <textarea name="nama" class="form-input" rows="3" placeholder="Deskripsikan gejala secara jelas..." required
                style="resize:vertical;"><?= htmlspecialchars($editData['nama'] ?? ($_POST['nama'] ?? '')) ?></textarea>
        </div>

        <div class="form-group">
            <label>Kategori <span style="color:#fda4af;">*</span></label>
            <select name="kategori" class="form-input" required>
                <option value="">-- Pilih Kategori --</option>
                <?php foreach ($allKat as $kat): ?>
                <option value="<?= htmlspecialchars($kat) ?>"
                    <?= ($editData['kategori'] ?? ($_POST['kategori'] ?? '')) === $kat ? 'selected' : '' ?>>
                    <?= htmlspecialchars($kat) ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:12px;">
            <div class="form-group" style="margin-bottom:0;">
                <label>MB (0&ndash;1) <span style="color:#fda4af;">*</span></label>
                <input type="number" name="mb" class="form-input" step="0.1" min="0" max="1" required
                       placeholder="0.0" id="mbInput" oninput="calcCF()"
                       value="<?= $editData['mb'] ?? ($_POST['mb'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>MD (0&ndash;1) <span style="color:#fda4af;">*</span></label>
                <input type="number" name="md" class="form-input" step="0.1" min="0" max="1" required
                       placeholder="0.0" id="mdInput" oninput="calcCF()"
                       value="<?= $editData['md'] ?? ($_POST['md'] ?? '') ?>">
            </div>
            <div class="form-group" style="margin-bottom:0;">
                <label>CF (auto)</label>
                <div class="form-input" id="cfPreview" style="text-align:center;font-weight:700;cursor:default;font-family:monospace;color:#93c5fd;">
                    <?= $editData ? number_format((float)$editData['cf'], 2) : '&mdash;' ?>
                </div>
            </div>
        </div>

        <div style="display:flex;gap:10px;margin-top:20px;">
            <button type="submit" class="btn <?= $editData ? 'btn-purple' : 'btn-success' ?> btn-lg" style="flex:1;justify-content:center;">
                <i class="fas <?= $editData ? 'fa-floppy-disk' : 'fa-plus' ?>"></i>
                <?= $editData ? 'Simpan Perubahan' : 'Tambah Gejala' ?>
            </button>
            <?php if ($editData): ?>
            <a href="kelola-gejala.php" class="btn btn-ghost btn-lg">Batal</a>
            <?php endif; ?>
        </div>
    </form>
</div>

<!-- DAFTAR GEJALA -->
<div>
    <div class="card" style="padding:14px 18px;margin-bottom:16px;">
        <div class="filter-bar" style="flex-wrap:wrap;">
            <a href="kelola-gejala.php?kategori=semua" class="btn btn-xs <?= $filter==='semua'?'btn-primary':'btn-ghost' ?>">Semua</a>
            <?php foreach ($allKat as $kat): ?>
            <a href="kelola-gejala.php?kategori=<?= urlencode($kat) ?>"
               class="btn btn-xs <?= $filter===$kat?'btn-primary':'btn-ghost' ?>">
                <?= katIcon($kat) ?>
            </a>
            <?php endforeach; ?>
        </div>
    </div>

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-list"></i></span>
            Daftar Gejala
            <span style="margin-left:auto;font-size:0.82rem;font-weight:500;color:var(--text-muted);"><?= $total ?> gejala</span>
        </div>
        <div class="table-wrap" style="max-height:500px;overflow-y:auto;">
            <table class="mini-table">
                <thead style="position:sticky;top:0;z-index:10;">
                    <tr>
                        <th>Kode</th>
                        <th>Gejala</th>
                        <th class="td-center">MB</th>
                        <th class="td-center">MD</th>
                        <th class="td-center">CF</th>
                        <th class="td-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($row = $allGejala->fetch_assoc()):
                    $cfVal = (float)$row['cf'];
                    $cfColor = $cfVal >= 0.7 ? '#6ee7b7' : ($cfVal >= 0.4 ? '#fcd34d' : ($cfVal < 0 ? '#fda4af' : '#93c5fd'));
                ?>
                    <tr style="<?= isset($_GET['edit']) && $_GET['edit'] == $row['id'] ? 'background:rgba(139,92,246,0.08);' : '' ?>">
                        <td><span style="font-family:monospace;font-weight:700;font-size:0.82rem;color:#93c5fd;"><?= $row['kode'] ?></span></td>
                        <td style="font-size:0.78rem;max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="<?= htmlspecialchars($row['nama']) ?>">
                            <?= htmlspecialchars(mb_strimwidth($row['nama'], 0, 45, '...')) ?>
                        </td>
                        <td class="td-center" style="font-size:0.82rem;"><?= number_format((float)$row['mb'],1) ?></td>
                        <td class="td-center" style="font-size:0.82rem;"><?= number_format((float)$row['md'],1) ?></td>
                        <td class="td-center">
                            <strong style="color:<?= $cfColor ?>;font-size:0.9rem;"><?= number_format($cfVal,2) ?></strong>
                        </td>
                        <td class="td-center" style="white-space:nowrap;">
                            <a href="kelola-gejala.php?edit=<?= $row['id'] ?>" class="btn btn-ghost btn-xs" style="margin-right:4px;"><i class="fas fa-pen"></i></a>
                            <a href="kelola-gejala.php?hapus=<?= $row['id'] ?>"
                               onclick="return confirm('Hapus gejala <?= $row['kode'] ?>?')"
                               class="btn btn-danger btn-xs"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<script>
function calcCF() {
    const mb = parseFloat(document.getElementById('mbInput').value) || 0;
    const md = parseFloat(document.getElementById('mdInput').value) || 0;
    const cf = (mb - md).toFixed(2);
    const el = document.getElementById('cfPreview');
    el.textContent = cf;
    el.style.color = cf >= 0.7 ? '#6ee7b7' : (cf >= 0.4 ? '#fcd34d' : (cf < 0 ? '#fda4af' : '#93c5fd'));
}
calcCF();
</script>

<?php require_once '../includes/footer.php'; ?>
