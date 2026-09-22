<?php
session_start();
require_once '../includes/config.php';
$pageTitle  = 'Konsultasi';
$activePage = 'konsultasi';
$base       = '../';

$hasil = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $gejalaIds    = $_POST['gejala'] ?? [];
    $namaPengguna = trim($_POST['nama_pengguna'] ?? '');
    if ($namaPengguna === '') {
        $namaPengguna = isUser() ? ($_SESSION['user_nama'] ?? 'Pengguna') : 'Anonim';
    } else {
        $namaPengguna = htmlspecialchars($namaPengguna);
    }

    if (empty($gejalaIds)) {
        $error = 'Silakan pilih minimal satu gejala terlebih dahulu sebelum menganalisis.';
    } else {
        $ids = implode(',', array_map('intval', $gejalaIds));
        $q   = $conn->query("SELECT * FROM gejala WHERE id IN ($ids) ORDER BY kode");

        $cfList        = [];
        $gejalaDipilih = [];
        while ($row = $q->fetch_assoc()) {
            $cfList[]        = (float)$row['cf'];
            $gejalaDipilih[] = $row;
        }

        $cfFinal = hitungCF($cfList);
        $info    = levelRisiko($cfFinal);

        $gejalaStr = implode(',', array_column($gejalaDipilih, 'kode'));
        // Simpan user_id jika login, NULL jika tamu/anonim
        $userId = isUser() ? (int)($_SESSION['user_id'] ?? 0) : null;
        if ($userId) {
            $stmt = $conn->prepare("INSERT INTO konsultasi (nama_pengguna, gejala_dipilih, nilai_cf, level_risiko, user_id) VALUES (?,?,?,?,?)");
            $stmt->bind_param("ssdsi", $namaPengguna, $gejalaStr, $cfFinal, $info['level'], $userId);
        } else {
            $stmt = $conn->prepare("INSERT INTO konsultasi (nama_pengguna, gejala_dipilih, nilai_cf, level_risiko) VALUES (?,?,?,?)");
            $stmt->bind_param("ssds", $namaPengguna, $gejalaStr, $cfFinal, $info['level']);
        }
        $stmt->execute();

        $hasil = [
            'cf'            => $cfFinal,
            'info'          => $info,
            'gejala'        => $gejalaDipilih,
            'nama'          => $namaPengguna,
            'jumlah_gejala' => count($gejalaDipilih),
        ];
    }
}

$semuaGejala = [];
$q2 = $conn->query("SELECT * FROM gejala ORDER BY kategori, kode");
while ($row = $q2->fetch_assoc()) {
    $semuaGejala[$row['kategori']][] = $row;
}

require_once '../includes/header.php';
?>

<div class="page-header">
    <div class="page-title"><i class="fas fa-stethoscope"></i> Konsultasi Sistem Pakar</div>
    <div class="page-sub">Pilih gejala atau perilaku pasangan yang Anda amati. Sistem akan menghitung tingkat risiko menggunakan metode Certainty Factor secara otomatis.</div>
</div>

<?php if ($error): ?>
<div class="alert alert-danger">
    <span class="alert-icon"><i class="fas fa-ban"></i></span>
    <div><?= $error ?></div>
</div>
<?php endif; ?>

<?php if ($hasil): ?>
<div class="hasil-box animate-up" style="background:<?= $hasil['info']['bg'] ?>;">
    <div class="hasil-icon"><?= $hasil['info']['icon'] ?></div>
    <div class="hasil-cf">CF = <?= number_format($hasil['cf'], 3) ?></div>
    <div class="hasil-level"><?= $hasil['info']['level'] ?></div>
    <div class="risk-meter">
        <div class="risk-meter-fill" style="width:<?= min(100, $hasil['cf'] * 100) ?>%; background:<?= $hasil['info']['accent'] ?>;"></div>
    </div>
    <div class="hasil-meta"><?= $hasil['jumlah_gejala'] ?> gejala dipilih &bull; Analisis untuk: <strong><?= $hasil['nama'] ?></strong></div>
</div>

<div class="saran-box">
    <div class="saran-label"><i class="fas fa-lightbulb"></i> Saran &amp; Rekomendasi</div>
    <?= $hasil['info']['saran'] ?>
</div>

<div class="card">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(139,92,246,0.15);"><i class="fas fa-ruler-combined"></i></span>
        Detail Perhitungan Certainty Factor
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th class="td-center">No</th>
                    <th>Kode</th>
                    <th>Deskripsi Gejala</th>
                    <th>Kategori</th>
                    <th class="td-center">MB</th>
                    <th class="td-center">MD</th>
                    <th class="td-center">CF</th>
                    <th class="td-center">CF Kombinasi</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $cfKombinasi = 0.0;
            foreach ($hasil['gejala'] as $i => $g):
                $cfNeu = (float)$g['cf'];
                if ($i === 0) {
                    $cfKombinasi = $cfNeu;
                } elseif ($cfKombinasi >= 0 && $cfNeu >= 0) {
                    $cfKombinasi = $cfKombinasi + $cfNeu * (1 - $cfKombinasi);
                } elseif ($cfKombinasi < 0 && $cfNeu < 0) {
                    $cfKombinasi = $cfKombinasi + $cfNeu * (1 + $cfKombinasi);
                } else {
                    $denom = 1 - min(abs($cfKombinasi), abs($cfNeu));
                    $cfKombinasi = ($denom != 0) ? ($cfKombinasi + $cfNeu) / $denom : 0.0;
                }
                $cfKombinasi = round(max(-1.0, min(1.0, $cfKombinasi)), 4);
                $cfGVal = (float)$g['cf'];
            ?>
                <tr>
                    <td class="td-center" style="color:var(--text-muted)"><?= $i + 1 ?></td>
                    <td><strong style="color:#93c5fd;"><?= $g['kode'] ?></strong></td>
                    <td style="font-size:0.83rem"><?= htmlspecialchars($g['nama']) ?></td>
                    <td><span class="<?= katClass($g['kategori']) ?>" style="font-size:0.72rem;"><?= htmlspecialchars($g['kategori']) ?></span></td>
                    <td class="td-center"><?= number_format((float)$g['mb'], 2) ?></td>
                    <td class="td-center"><?= number_format((float)$g['md'], 2) ?></td>
                    <td class="td-center">
                        <strong style="color:<?= $cfGVal >= 0.7 ? '#6ee7b7' : ($cfGVal >= 0.4 ? '#fcd34d' : ($cfGVal < 0 ? '#fda4af' : '#93c5fd')) ?>;">
                            <?= number_format($cfGVal, 2) ?>
                        </strong>
                    </td>
                    <td class="td-center">
                        <strong style="color:<?= $hasil['info']['color'] ?>;"><?= number_format($cfKombinasi, 3) ?></strong>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="alert alert-info" style="margin-top:16px;margin-bottom:0;">
        <span class="alert-icon"><i class="fas fa-ruler-combined"></i></span>
        <div>
            <strong>Formula Kombinasi:</strong> CF_baru = CF_lama + CF_gejala &times; (1 &minus; CF_lama) &bull;
            <strong>Nilai CF Akhir: <?= number_format($hasil['cf'], 3) ?></strong> &bull;
            <span class="risk-badge" style="background:<?= riskColor($hasil['info']['level']) ?>;"><?= $hasil['info']['level'] ?></span>
        </div>
    </div>
</div>

<div style="text-align:center;margin-top:8px;display:flex;justify-content:center;gap:12px;flex-wrap:wrap;">
    <a href="konsultasi.php" class="btn btn-primary"><i class="fas fa-rotate"></i> Konsultasi Ulang</a>
    <a href="../index.php"   class="btn btn-ghost"><i class="fas fa-house"></i> Beranda</a>
</div>

<?php else: ?>
<form method="POST" class="konsultasi-form" id="formKonsultasi">

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-user"></i></span>
            Identitas Pengguna (Opsional)
        </div>
        <div class="nama-input">
            <label for="nama_pengguna">Nama Anda <span style="color:var(--text-muted);font-weight:400;"><?= isUser() ? '(otomatis terisi nama akun Anda)' : '(boleh dikosongkan, default: Anonim)' ?></span></label>
            <input type="text" id="nama_pengguna" name="nama_pengguna" class="form-input"
                   placeholder="<?= isUser() ? htmlspecialchars($_SESSION['user_nama'] ?? '') : 'Contoh: Anonim' ?>"
                   value="<?= isUser() ? htmlspecialchars($_SESSION['user_nama'] ?? '') : '' ?>"
                   maxlength="100" autocomplete="off">
        </div>
    </div>

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(245,158,11,0.15);"><i class="fas fa-square-check"></i></span>
            Pilih Gejala yang Anda Amati dari Pasangan
        </div>

        <div class="counter-bar" id="counterBar">
            <span class="counter-num" id="counterNum">0</span>
            <span>gejala dipilih dari <strong><?= array_sum(array_map('count', $semuaGejala)) ?></strong> indikator yang tersedia</span>
            <span id="estCF" style="margin-left:auto;color:#93c5fd;font-weight:600;display:none;"></span>
        </div>

        <?php foreach ($semuaGejala as $kategori => $gejalas): ?>
        <div class="kategori-section">
            <div class="kategori-title">
                <span><?= katIcon($kategori) ?></span>
                <?= htmlspecialchars($kategori) ?>
                <span style="margin-left:auto;font-size:0.78rem;font-weight:500;opacity:0.7;"><?= count($gejalas) ?> gejala</span>
            </div>
            <div class="gejala-grid">
                <?php foreach ($gejalas as $g):
                    $cfG = (float)$g['cf'];
                    $cfColor = $cfG >= 0.7 ? '#6ee7b7' : ($cfG >= 0.4 ? '#fcd34d' : ($cfG < 0 ? '#fda4af' : '#93c5fd'));
                ?>
                <div class="gejala-item" id="item-<?= $g['id'] ?>">
                    <input type="checkbox" id="g<?= $g['id'] ?>" name="gejala[]" value="<?= $g['id'] ?>"
                           data-cf="<?= $cfG ?>" onchange="updateCounter(this)">
                    <label for="g<?= $g['id'] ?>">
                        <span style="color:rgba(226,232,240,0.5);font-size:0.75rem;font-weight:600;"><?= $g['kode'] ?></span>
                        <?= htmlspecialchars($g['nama']) ?>
                        <span class="cf-text">CF = <span style="color:<?= $cfColor ?>;font-weight:600;"><?= number_format($cfG, 2) ?></span></span>
                    </label>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endforeach; ?>

        <hr class="divider">
        <div class="form-actions">
            <button type="submit" class="btn btn-primary btn-lg" id="btnSubmit">
                <i class="fas fa-magnifying-glass"></i> Analisis Sekarang
            </button>
            <button type="button" class="btn btn-ghost btn-sm" onclick="resetForm()" style="margin-left:10px;">
                <i class="fas fa-xmark"></i> Reset Pilihan
            </button>
            <div style="margin-top:12px;color:var(--text-muted);font-size:0.83rem;">
                Pilih minimal 1 gejala untuk memulai analisis &bull; Semakin banyak gejala dipilih, semakin akurat hasilnya
            </div>
        </div>
    </div>

</form>

<div class="alert alert-warning">
    <span class="alert-icon"><i class="fas fa-triangle-exclamation"></i></span>
    <div>
        <strong>Perhatian:</strong> Hasil analisis sistem ini bersifat <em>indikatif</em> — merupakan alat bantu identifikasi awal, bukan penentu keputusan akhir. Jangan mengambil kesimpulan sepihak hanya berdasarkan hasil ini. Segera konsultasikan kepada <strong>psikolog atau konselor hubungan</strong> yang profesional apabila diperlukan.
    </div>
</div>
<?php endif; ?>

<script>
let count = 0;
let selectedCFs = {};

function updateCounter(cb) {
    const id  = cb.value;
    const cf  = parseFloat(cb.getAttribute('data-cf') || 0);
    const item = document.getElementById('item-' + id);

    if (cb.checked) {
        count++;
        selectedCFs[id] = cf;
        item.classList.add('checked');
    } else {
        count--;
        delete selectedCFs[id];
        item.classList.remove('checked');
    }

    document.getElementById('counterNum').textContent = count;

    const cfList = Object.values(selectedCFs);
    if (cfList.length > 0) {
        // Fungsi kombinasi CF: 3 kondisi Shortliffe & Buchanan
        function combineCF(old, neu) {
            if (old >= 0 && neu >= 0) {
                return old + neu * (1 - old);
            } else if (old < 0 && neu < 0) {
                return old + neu * (1 + old);
            } else {
                const denom = 1 - Math.min(Math.abs(old), Math.abs(neu));
                return denom !== 0 ? (old + neu) / denom : 0;
            }
        }
        let est = cfList[0];
        for (let i = 1; i < cfList.length; i++) {
            est = combineCF(est, cfList[i]);
            est = Math.max(-1, Math.min(1, est));
        }
        const estEl = document.getElementById('estCF');
        estEl.style.display = 'inline';
        estEl.textContent = '≈ CF ' + est.toFixed(4);
    } else {
        document.getElementById('estCF').style.display = 'none';
    }
}

function resetForm() {
    document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
        const item = document.getElementById('item-' + cb.value);
        if (item) item.classList.remove('checked');
    });
    count = 0;
    selectedCFs = {};
    document.getElementById('counterNum').textContent = '0';
    document.getElementById('estCF').style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function() {
    const fill = document.querySelector('.risk-meter-fill');
    if (fill) {
        const target = fill.style.width;
        fill.style.width = '0%';
        setTimeout(() => { fill.style.width = target; }, 300);
    }
});
</script>

<?php require_once '../includes/footer.php'; ?>
