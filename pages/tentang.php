<?php
session_start();
require_once '../includes/config.php';
$pageTitle  = 'Tentang Sistem';
$activePage = 'tentang';
$base       = '../';
require_once '../includes/header.php';
?>

<div class="page-header">
    <div class="page-title"><i class="fas fa-circle-info"></i> Tentang Sistem</div>
    <div class="page-sub">Informasi mengenai sistem, metode Certainty Factor, penelitian, dan pengembangnya.</div>
</div>

<div class="grid-2">
<!-- KIRI -->
<div>
    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-bullseye"></i></span>
            Tentang SiPakar CF
        </div>
        <p class="tentang-p">
            <strong style="color:#93c5fd;">SiPakar CF</strong> adalah sistem pakar berbasis web yang dikembangkan untuk membantu proses identifikasi faktor risiko perselingkuhan dalam suatu hubungan romantis secara lebih objektif, terstruktur, dan dapat diakses kapan saja.
        </p>
        <br>
        <p class="tentang-p">
            Sistem memanfaatkan metode <strong style="color:#c4b5fd;">Certainty Factor (CF)</strong> dalam mengolah ketidakpastian dari berbagai gejala atau indikator perilaku yang diamati pengguna, menghasilkan tingkat keyakinan terhadap risiko perselingkuhan yang terukur dan dapat diinterpretasikan.
        </p>
        <br>
        <p class="tentang-p">
            Basis pengetahuan sistem diperoleh melalui wawancara terstruktur dengan <strong style="color:#fcd34d;">Kania Hidayah, M.Psi., Psikolog</strong> dari Sahabat Insan Selaras, Tasikmalaya. Terdapat <strong>33 indikator gejala</strong> dalam <strong>6 kategori</strong> dengan nilai MB dan MD yang ditetapkan berdasarkan penilaian pakar.
        </p>
    </div>

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(139,92,246,0.15);"><i class="fas fa-ruler-combined"></i></span>
            Metode Certainty Factor
        </div>
        <p class="tentang-p">Certainty Factor dikembangkan oleh <strong style="color:#c4b5fd;">Shortliffe &amp; Buchanan (1976)</strong> dalam sistem pakar medis MYCIN di Stanford University. Digunakan untuk merepresentasikan tingkat keyakinan pakar terhadap suatu hipotesis dalam kondisi ketidakpastian.</p>
        <br>
        <div class="info-label">Formula Dasar</div>
        <div class="formula-box">CF(H, E) = MB(H, E) &minus; MD(H, E)</div>
        <div class="info-label" style="margin-top:12px;">Formula Kombinasi (Sequential)</div>
        <div class="formula-box">CF_kom = CF_lama + CF_baru &times; (1 &minus; CF_lama)</div>
        <ul class="tentang-list" style="padding-left:20px;margin-top:16px;list-style:disc;">
            <li><strong>MB</strong> = Measure of Belief &mdash; ukuran kepercayaan pakar (0 s.d. 1)</li>
            <li><strong>MD</strong> = Measure of Disbelief &mdash; ukuran ketidakpercayaan pakar (0 s.d. 1)</li>
            <li><strong>CF</strong> = Certainty Factor = MB &minus; MD (bernilai -1 s.d. 1)</li>
            <li>MB dan MD bersifat <em>mutually exclusive</em>: tidak keduanya &gt; 0</li>
            <li>Formula kombinasi diterapkan iteratif untuk setiap gejala yang dipilih</li>
        </ul>
    </div>

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(20,184,166,0.15);"><i class="fas fa-desktop"></i></span>
            Teknologi yang Digunakan
        </div>
        <ul class="tentang-list" style="padding-left:20px;list-style:disc;">
            <li><strong>PHP Native</strong> &mdash; bahasa pemrograman server-side</li>
            <li><strong>MySQL</strong> &mdash; sistem manajemen basis data relasional</li>
            <li><strong>HTML5 &amp; CSS3</strong> &mdash; antarmuka pengguna (Vanilla CSS)</li>
            <li><strong>JavaScript</strong> &mdash; interaktivitas real-time (estimasi CF)</li>
            <li><strong>XAMPP</strong> &mdash; web server lokal (Apache + MySQL)</li>
            <li><strong>Visual Studio Code</strong> &mdash; code editor utama</li>
        </ul>
    </div>
</div>

<!-- KANAN -->
<div>
    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(245,158,11,0.15);"><i class="fas fa-chart-bar"></i></span>
            Interpretasi Level Risiko
        </div>
        <table>
            <thead>
                <tr><th>Nilai CF</th><th>Level Risiko</th><th>Keterangan</th></tr>
            </thead>
            <tbody>
                <tr>
                    <td style="font-family:monospace;font-weight:600;">&le; 0,20</td>
                    <td><span class="risk-badge" style="background:#059669;">Sangat Rendah</span></td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">Tidak ada indikasi signifikan</td>
                </tr>
                <tr>
                    <td style="font-family:monospace;font-weight:600;">0,21&ndash;0,40</td>
                    <td><span class="risk-badge" style="background:#16a34a;">Rendah</span></td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">Indikasi ringan, tingkatkan komunikasi</td>
                </tr>
                <tr>
                    <td style="font-family:monospace;font-weight:600;">0,41&ndash;0,60</td>
                    <td><span class="risk-badge" style="background:#d97706;">Sedang</span></td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">Indikasi cukup kuat, diskusi terbuka</td>
                </tr>
                <tr>
                    <td style="font-family:monospace;font-weight:600;">0,61&ndash;0,80</td>
                    <td><span class="risk-badge" style="background:#ea580c;">Tinggi</span></td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">Indikasi kuat, cari bantuan profesional</td>
                </tr>
                <tr>
                    <td style="font-family:monospace;font-weight:600;">&gt; 0,80</td>
                    <td><span class="risk-badge" style="background:#dc2626;">Sangat Tinggi</span></td>
                    <td style="font-size:0.8rem;color:var(--text-muted);">Segera konsultasi ke psikolog/konselor</td>
                </tr>
            </tbody>
        </table>
        

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(59,130,246,0.15);"><i class="fas fa-user-pen"></i></span>
            Profil Pengembang
        </div>
        <div class="profil-card">
            <div class="profil-avatar"><i class="fas fa-user-graduate" style="font-size:2.5rem;color:#93c5fd;"></i></div>
            <div class="profil-info">
                <h3>Ela Amelia</h3>
                <p>
                    NIM: 11220017<br>
                    Program Studi Teknik Informatika<br>
                    <strong style="color:#93c5fd;">STMIK DCI Tasikmalaya</strong><br>
                    Tugas Akhir &mdash; 2026
                </p>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-title">
            <span class="title-icon" style="background:rgba(16,185,129,0.15);"><i class="fas fa-graduation-cap"></i></span>
            Informasi Penelitian
        </div>
        <div style="display:grid;gap:14px;">
            <div>
                <div class="info-label">Judul Penelitian</div>
                <div class="info-value" style="font-size:0.88rem;line-height:1.6;">Sistem Pakar Identifikasi Faktor Risiko Perselingkuhan Menggunakan Metode Certainty Factor Berbasis Web</div>
            </div>
            <div>
                <div class="info-label">Pakar Narasumber</div>
                <div class="info-value">Kania Hidayah, M.Psi., Psikolog</div>
                <div style="font-size:0.80rem;color:var(--text-muted);">Sahabat Insan Selaras, Tasikmalaya</div>
            </div>
            <div>
                <div class="info-label">Metode Analisis</div>
                <div class="info-value">Certainty Factor (CF) &mdash; Sequential Combination</div>
            </div>
            <div>
                <div class="info-label">Basis Pengetahuan</div>
                <div class="info-value">33 Gejala dalam 6 Kategori</div>
            </div>
            <div>
                <div class="info-label">Platform</div>
                <div class="info-value">Web Application (PHP + MySQL)</div>
            </div>
        </div>
    </div>
</div>
</div>

<?php require_once '../includes/footer.php'; ?>
