<?php
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3307);
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'sistem_pakar_cf');
define('APP_NAME', 'SiPakar CF');
define('APP_VERSION', '1.0.0');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($conn->connect_error) {
    die('<div style="font-family:\'Inter\',sans-serif;padding:48px 40px;color:#fda4af;background:rgba(15,23,42,0.95);border-radius:16px;margin:60px auto;max-width:560px;border:1px solid rgba(244,63,94,0.3);text-align:center;">
        <div style="font-size:3rem;margin-bottom:20px;">⚠️</div>
        <h3 style="margin-bottom:12px;font-size:1.2rem;">Koneksi Database Gagal</h3>
        <p style="opacity:0.75;margin-bottom:16px;">Pastikan XAMPP sudah aktif dan database <strong>sistem_pakar_cf</strong> sudah diimport.</p>
        <code style="background:rgba(255,255,255,0.08);padding:10px 16px;border-radius:8px;font-size:0.82rem;display:block;">' . $conn->connect_error . '</code>
    </div>');
}

$conn->set_charset("utf8");

// ── Helper: hitung CF kombinasi (sequential) ──
function hitungCF(array $cfList): float {
    if (empty($cfList)) return 0.0;
    $hasil = (float)array_shift($cfList);
    foreach ($cfList as $cf) {
        $hasil = $hasil + (float)$cf * (1 - $hasil);
    }
    return round($hasil, 3);
}

// ── Helper: tentukan level risiko & metadata ──
function levelRisiko(float $cf): array {
    if ($cf <= 0.20) return [
        'level'     => 'Risiko Sangat Rendah',
        'bg'        => 'linear-gradient(135deg, #064e3b, #065f46)',
        'accent'    => '#10b981',
        'color'     => '#6ee7b7',
        'icon'      => '<i class="fas fa-circle-check" style="color:#10b981"></i>',
        'saran'     => 'Tidak terdeteksi indikasi signifikan. Hubungan Anda tampak dalam kondisi baik. Terus jaga komunikasi terbuka dan kepercayaan bersama pasangan.',
        'class'     => 'very-low',
    ];
    if ($cf <= 0.40) return [
        'level'     => 'Risiko Rendah',
        'bg'        => 'linear-gradient(135deg, #14532d, #166534)',
        'accent'    => '#22c55e',
        'color'     => '#86efac',
        'icon'      => '<i class="fas fa-circle-check" style="color:#22c55e"></i>',
        'saran'     => 'Terdapat beberapa indikasi ringan. Coba tingkatkan kualitas komunikasi dengan pasangan secara terbuka dan tanpa prasangka. Diskusikan kekhawatiran Anda dengan tenang.',
        'class'     => 'low',
    ];
    if ($cf <= 0.60) return [
        'level'     => 'Risiko Sedang',
        'bg'        => 'linear-gradient(135deg, #78350f, #92400e)',
        'accent'    => '#f59e0b',
        'color'     => '#fcd34d',
        'icon'      => '<i class="fas fa-triangle-exclamation" style="color:#f59e0b"></i>',
        'saran'     => 'Indikasi cukup kuat ditemukan. Sebaiknya ajak pasangan bicara dari hati ke hati dalam suasana yang kondusif. Jika komunikasi terasa sulit, pertimbangkan bantuan konselor hubungan.',
        'class'     => 'medium',
    ];
    if ($cf <= 0.80) return [
        'level'     => 'Risiko Tinggi',
        'bg'        => 'linear-gradient(135deg, #7c2d12, #9a3412)',
        'accent'    => '#f97316',
        'color'     => '#fdba74',
        'icon'      => '<i class="fas fa-circle-exclamation" style="color:#f97316"></i>',
        'saran'     => 'Indikasi kuat terdeteksi dari kombinasi gejala yang Anda pilih. Sangat disarankan untuk segera melakukan diskusi serius dengan pasangan atau mencari bantuan profesional seperti psikolog atau konselor pernikahan.',
        'class'     => 'high',
    ];
    return [
        'level'     => 'Risiko Sangat Tinggi',
        'bg'        => 'linear-gradient(135deg, #7f1d1d, #991b1b)',
        'accent'    => '#ef4444',
        'color'     => '#fca5a5',
        'icon'      => '<i class="fas fa-skull-crossbones" style="color:#ef4444"></i>',
        'saran'     => 'Indikasi sangat dominan terdeteksi. Jangan menunda — segera konsultasikan kondisi ini kepada psikolog atau konselor hubungan yang terpercaya. Sistem ini hanya alat bantu identifikasi, bukan penentu keputusan akhir.',
        'class'     => 'very-high',
    ];
}

// ── Helper: CSS class badge kategori ──
function katClass(string $kat): string {
    $map = [
        'Perubahan Komunikasi'          => 'kat-komunikasi',
        'Perubahan Perilaku Emosional'  => 'kat-emosional',
        'Perubahan Fisik & Penampilan'  => 'kat-fisik',
        'Perubahan Sosial & Lingkungan' => 'kat-sosial',
        'Perubahan Finansial'           => 'kat-finansial',
        'Faktor Predisposisi'           => 'kat-predis',
    ];
    return $map[$kat] ?? 'kat-komunikasi';
}

// ── Helper: icon kategori (Font Awesome HTML) ──
function katIcon(string $kat): string {
    $map = [
        'Perubahan Komunikasi'          => '<i class="fas fa-comments"></i>',
        'Perubahan Perilaku Emosional'  => '<i class="fas fa-heart-pulse"></i>',
        'Perubahan Fisik & Penampilan'  => '<i class="fas fa-eye"></i>',
        'Perubahan Sosial & Lingkungan' => '<i class="fas fa-users"></i>',
        'Perubahan Finansial'           => '<i class="fas fa-wallet"></i>',
        'Faktor Predisposisi'           => '<i class="fas fa-triangle-exclamation"></i>',
    ];
    return $map[$kat] ?? '<i class="fas fa-thumbtack"></i>';
}

// ── Helper: warna CF bar berdasarkan nilai ──
function cfBarClass(float $cf): string {
    if ($cf <= 0) return 'cf-bar negative';
    if ($cf >= 0.70) return 'cf-bar green';
    if ($cf >= 0.40) return 'cf-bar amber';
    return 'cf-bar';
}

// ── Helper: risk badge background color ──
function riskColor(string $level): string {
    $map = [
        'Risiko Sangat Rendah' => '#059669',
        'Risiko Rendah'        => '#16a34a',
        'Risiko Sedang'        => '#d97706',
        'Risiko Tinggi'        => '#ea580c',
        'Risiko Sangat Tinggi' => '#dc2626',
    ];
    return $map[$level] ?? '#475569';
}

// ── Session helper: cek login admin ──
function isAdmin(): bool {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}
?>
