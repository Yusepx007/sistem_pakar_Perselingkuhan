<?php
session_start();
require_once 'includes/config.php';
$pageTitle  = 'Beranda';
$activePage = 'home';
$base       = '';

// Statistik
$totalGejala     = $conn->query("SELECT COUNT(*) AS n FROM gejala")->fetch_assoc()['n'];
$totalKonsultasi = $conn->query("SELECT COUNT(*) AS n FROM konsultasi")->fetch_assoc()['n'];
$totalTinggi     = $conn->query("SELECT COUNT(*) AS n FROM konsultasi WHERE level_risiko IN ('Risiko Tinggi','Risiko Sangat Tinggi')")->fetch_assoc()['n'];
$totalHariIni    = $conn->query("SELECT COUNT(*) AS n FROM konsultasi WHERE DATE(tanggal) = CURDATE()")->fetch_assoc()['n'];

require_once 'includes/header.php';
?>

<!-- HERO -->
<section class="hero animate-up">
    <div class="hero-eyebrow">Sistem Pakar Berbasis Web</div>
    <?php if (isUser()): ?>
    <div style="margin-bottom:12px;">
        <span class="badge-pill" style="background:rgba(59,130,246,0.2);border-color:rgba(59,130,246,0.4);">
            <i class="fas fa-user"></i> Halo, <?= htmlspecialchars($_SESSION['user_nama'] ?? 'Pengguna') ?>!
        </span>
    </div>
    <?php elseif (isAdmin()): ?>
    <div style="margin-bottom:12px;">
        <span class="badge-pill" style="background:rgba(244,63,94,0.2);border-color:rgba(244,63,94,0.4);color:#fda4af;">
            <i class="fas fa-shield-halved"></i> Mode Administrator
        </span>
    </div>
    <?php endif; ?>
    <h1>Identifikasi Risiko<br>Perselingkuhan</h1>
    <p>
        Analisis faktor risiko hubungan secara objektif menggunakan metode
        <strong style="color:#93c5fd;">Certainty Factor</strong> berbasis pengetahuan psikolog.
        Bukan untuk menghakimi &mdash; untuk membantu Anda lebih waspada dan bijaksana.
    </p>
    <div class="hero-badges">
        <span class="badge-pill"><i class="fas fa-brain"></i> Basis Pengetahuan Pakar Psikologi</span>
        <span class="badge-pill"><i class="fas fa-chart-bar"></i> Metode Certainty Factor</span>
        <span class="badge-pill"><i class="fas fa-shield-alt"></i> Privasi Terjaga &amp; Anonim</span>
        <span class="badge-pill"><i class="fas fa-bolt"></i> Analisis Instan</span>
    </div>
    <div style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap;">
        <a href="pages/konsultasi.php" class="btn btn-primary btn-lg">
            <i class="fas fa-stethoscope"></i> Mulai Konsultasi &rarr;
        </a>
        <?php if (!isUser() && !isAdmin()): ?>
        <a href="auth/login.php" class="btn btn-ghost btn-lg">
            <i class="fas fa-right-to-bracket"></i> Login
        </a>
        <?php endif; ?>
    </div>
</section>

<!-- STATISTIK -->
<div class="grid-4">
    <div class="stat-card blue animate-up">
        <div class="stat-icon blue"><i class="fas fa-clipboard-list"></i></div>
        <div class="stat-number"><?= $totalGejala ?></div>
        <div class="stat-label">Indikator Gejala</div>
    </div>
    <div class="stat-card green animate-up" style="animation-delay:0.08s">
        <div class="stat-icon green"><i class="fas fa-stethoscope"></i></div>
        <div class="stat-number"><?= $totalKonsultasi ?></div>
        <div class="stat-label">Total Konsultasi</div>
    </div>
    <div class="stat-card amber animate-up" style="animation-delay:0.16s">
        <div class="stat-icon amber"><i class="fas fa-triangle-exclamation"></i></div>
        <div class="stat-number"><?= $totalTinggi ?></div>
        <div class="stat-label">Risiko Tinggi / S.Tinggi</div>
    </div>
    <div class="stat-card rose animate-up" style="animation-delay:0.24s">
        <div class="stat-icon rose"><i class="fas fa-calendar-day"></i></div>
        <div class="stat-number"><?= $totalHariIni ?></div>
        <div class="stat-label">Konsultasi Hari Ini</div>
    </div>
</div>

<!-- CARA PENGGUNAAN -->
<div class="card animate-up" style="animation-delay:0.1s">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-list-check"></i></span>
        Cara Penggunaan Sistem
    </div>
    <div class="steps">
        <div class="step">
            <div class="step-num">1</div>
            <h4>Pilih Gejala</h4>
            <p>Centang gejala atau perilaku pasangan yang Anda amati dari 33 indikator dalam 6 kategori</p>
        </div>
        <div class="step">
            <div class="step-num">2</div>
            <h4>Analisis CF</h4>
            <p>Sistem menghitung tingkat keyakinan secara otomatis dengan rumus Certainty Factor kombinasi</p>
        </div>
        <div class="step">
            <div class="step-num">3</div>
            <h4>Terima Hasil</h4>
            <p>Dapatkan level risiko dari Sangat Rendah hingga Sangat Tinggi disertai saran tindak lanjut</p>
        </div>
    </div>
</div>

<!-- 6 KATEGORI GEJALA -->
<div class="card animate-up" style="animation-delay:0.15s">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(139,92,246,0.15);"><i class="fas fa-folder-open"></i></span>
        6 Kategori Indikator Gejala
    </div>
    <div class="grid-3" style="margin-bottom:0;">
        <?php
        $kategoriInfo = [
            ['kat' => 'Perubahan Komunikasi',          'g' => 6,  'desc' => 'Pola & kualitas komunikasi sehari-hari'],
            ['kat' => 'Perubahan Perilaku Emosional',  'g' => 6,  'desc' => 'Perubahan mood, emosi & afeksi'],
            ['kat' => 'Perubahan Fisik & Penampilan',  'g' => 6,  'desc' => 'Penampilan, kehadiran & keintiman'],
            ['kat' => 'Perubahan Sosial & Lingkungan', 'g' => 5,  'desc' => 'Interaksi sosial & lingkaran pergaulan'],
            ['kat' => 'Perubahan Finansial',           'g' => 4,  'desc' => 'Pola pengeluaran & keuangan'],
            ['kat' => 'Faktor Predisposisi',           'g' => 6,  'desc' => 'Latar belakang & faktor kepribadian'],
        ];
        foreach ($kategoriInfo as $k):
        ?>
        <div class="card" style="margin-bottom:0;padding:20px;">
            <div style="font-size:1.4rem;margin-bottom:10px;color:#93c5fd;"><?= katIcon($k['kat']) ?></div>
            <div style="font-weight:700;font-size:0.88rem;color:#e8f0fe;margin-bottom:6px;"><?= $k['kat'] ?></div>
            <div style="color:var(--text-muted);font-size:0.80rem;margin-bottom:10px;"><?= $k['desc'] ?></div>
            <span class="<?= katClass($k['kat']) ?>"><?= $k['g'] ?> gejala</span>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- PREVIEW TABEL GEJALA -->
<div class="card animate-up" style="animation-delay:0.2s">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(20,184,166,0.15);"><i class="fas fa-thumbtack"></i></span>
        Preview Indikator Gejala
    </div>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Deskripsi Gejala</th>
                    <th>Kategori</th>
                    <th class="td-center">CF</th>
                    <th class="td-center">Bobot</th>
                </tr>
            </thead>
            <tbody>
            <?php
            $result = $conn->query("SELECT * FROM gejala ORDER BY kode LIMIT 8");
            while ($row = $result->fetch_assoc()):
                $cfVal   = (float)$row['cf'];
                $barW    = max(0, min(100, $cfVal * 100));
                $barCls  = cfBarClass($cfVal);
            ?>
                <tr>
                    <td><strong style="color:#93c5fd;"><?= $row['kode'] ?></strong></td>
                    <td><?= htmlspecialchars($row['nama']) ?></td>
                    <td><span class="<?= katClass($row['kategori']) ?>"><?= htmlspecialchars($row['kategori']) ?></span></td>
                    <td class="td-center"><strong style="color:<?= $cfVal >= 0.7 ? '#6ee7b7' : ($cfVal >= 0.4 ? '#fcd34d' : ($cfVal < 0 ? '#fda4af' : '#93c5fd')) ?>;"><?= number_format($cfVal, 2) ?></strong></td>
                    <td class="td-center">
                        <div style="display:flex;align-items:center;justify-content:center;gap:6px;">
                            <div class="cf-bar-wrap"><div class="<?= $barCls ?>" style="width:<?= $barW ?>%"></div></div>
                        </div>
                    </td>
                </tr>
            <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- INTERPRETASI NILAI CF -->
<div class="card animate-up" style="animation-delay:0.25s">
    <div class="card-title">
        <span class="title-icon" style="background:rgba(245,158,11,0.15);"><i class="fas fa-chart-bar"></i></span>
        Interpretasi Nilai Certainty Factor
    </div>
    <div class="grid-3" style="margin-bottom:0;gap:12px;">
        <?php
        $levels = [
            ['range' => 'CF &le; 0,20',        'level' => 'Risiko Sangat Rendah', 'color' => '#059669', 'rgb' => '5,150,105',   'desc' => 'Tidak ada indikasi signifikan. Hubungan tampak baik.'],
            ['range' => '0,20 &lt; CF &le; 0,40','level' => 'Risiko Rendah',       'color' => '#16a34a', 'rgb' => '22,163,74',   'desc' => 'Indikasi ringan, perlu sedikit perhatian komunikasi.'],
            ['range' => '0,40 &lt; CF &le; 0,60','level' => 'Risiko Sedang',       'color' => '#d97706', 'rgb' => '217,119,6',   'desc' => 'Indikasi cukup kuat, dianjurkan diskusi terbuka.'],
            ['range' => '0,60 &lt; CF &le; 0,80','level' => 'Risiko Tinggi',       'color' => '#ea580c', 'rgb' => '234,88,12',   'desc' => 'Indikasi kuat, perlu tindakan nyata segera.'],
            ['range' => 'CF &gt; 0,80',          'level' => 'Risiko Sangat Tinggi','color' => '#dc2626', 'rgb' => '220,38,38',   'desc' => 'Indikasi sangat dominan, konsultasi psikolog.'],
        ];
        foreach ($levels as $lv):
        ?>
        <div style="background:rgba(<?= $lv['rgb'] ?>,0.1);border:1px solid <?= $lv['color'] ?>33;border-radius:12px;padding:16px;">
            <div style="font-size:0.76rem;font-weight:700;letter-spacing:0.5px;color:<?= $lv['color'] ?>;margin-bottom:6px;text-transform:uppercase;"><?= $lv['range'] ?></div>
            <span class="risk-badge" style="background:<?= $lv['color'] ?>;margin-bottom:8px;"><?= $lv['level'] ?></span>
            <div style="font-size:0.80rem;color:var(--text-muted);margin-top:8px;line-height:1.5;"><?= $lv['desc'] ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>
