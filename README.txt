============================================================
  SiPakar CF — Sistem Pakar Identifikasi Risiko Perselingkuhan
  Metode Certainty Factor | Tugas Akhir 2026
  Ela Amelia (11220017) — Teknik Informatika — STMIK DCI Tasikmalaya
============================================================

DESKRIPSI
---------
SiPakar CF adalah aplikasi sistem pakar berbasis web yang membantu
mengidentifikasi faktor risiko perselingkuhan dalam hubungan romantis
menggunakan metode Certainty Factor (CF).

Pakar: Kania Hidayah, M.Psi., Psikolog — Sahabat Insan Selaras, Tasikmalaya
Basis pengetahuan: 33 indikator gejala dalam 6 kategori


PERSYARATAN SISTEM
------------------
- XAMPP (Apache + MySQL) v7.4 atau lebih baru
- PHP 7.4+
- MySQL 5.7+ / MariaDB 10.3+
- Browser modern (Chrome, Firefox, Edge)


STRUKTUR DIREKTORI
------------------
sistem_pakar_ela/
├── index.php                 ← Halaman Beranda
├── database.sql              ← Script SQL (import di phpMyAdmin)
├── README.txt                ← File ini
│
├── assets/
│   └── css/
│       └── style.css         ← Stylesheet utama (premium dark design)
│
├── includes/
│   ├── config.php            ← Koneksi DB + helper functions
│   ├── header.php            ← Template header (navbar)
│   └── footer.php            ← Template footer
│
├── pages/
│   ├── konsultasi.php        ← Form konsultasi + hasil analisis CF
│   ├── gejala.php            ← Tabel 33 gejala beserta MB/MD/CF
│   ├── riwayat.php           ← Riwayat konsultasi tersimpan
│   └── tentang.php           ← Tentang sistem & pengembang
│
└── admin/
    ├── login.php             ← Login admin
    ├── dashboard.php         ← Dashboard statistik admin
    ├── kelola-gejala.php     ← CRUD data gejala (MB/MD/CF)
    └── logout.php            ← Logout admin


LANGKAH INSTALASI
-----------------
1. Copy seluruh folder ke: C:\xampp\htdocs\sistem_pakar_ela\

2. Jalankan XAMPP → Start Apache dan MySQL

3. Buka phpMyAdmin: http://localhost/phpmyadmin
   (Sesuaikan port jika perlu, default 3307)

4. Buat database baru bernama: sistem_pakar_cf

5. Import file: database.sql
   (Klik tab Import → Choose File → sistem_pakar_ela/database.sql → Go)

6. Buka aplikasi di browser: http://localhost/sistem_pakar_ela/

7. Login Admin: http://localhost/sistem_pakar_ela/admin/login.php
   Username: admin
   Password: admin123


KONFIGURASI DATABASE (includes/config.php)
------------------------------------------
define('DB_HOST', '127.0.0.1');
define('DB_PORT', 3307);        ← Sesuaikan port MySQL Anda
define('DB_USER', 'root');
define('DB_PASS', '');          ← Password MySQL (default kosong)
define('DB_NAME', 'sistem_pakar_cf');
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'admin123');


FITUR SISTEM
------------
✅ Konsultasi identifikasi risiko perselingkuhan (33 gejala, 6 kategori)
✅ Perhitungan Certainty Factor kombinasi secara real-time
✅ Estimasi CF saat memilih gejala (sebelum submit)
✅ Hasil analisis: level risiko + saran + tabel detail perhitungan
✅ Riwayat konsultasi dengan pagination
✅ Data gejala dengan filter per kategori
✅ Panel Admin: login, dashboard statistik, kelola gejala (CRUD)
✅ Desain premium glassmorphism dark mode
✅ Responsive mobile


METODE CERTAINTY FACTOR
------------------------
Formula dasar:   CF(H,E) = MB(H,E) - MD(H,E)
Formula kombinasi: CF_kom = CF_lama + CF_baru × (1 - CF_lama)

Level Risiko:
  CF ≤ 0,20  → Risiko Sangat Rendah
  CF ≤ 0,40  → Risiko Rendah
  CF ≤ 0,60  → Risiko Sedang
  CF ≤ 0,80  → Risiko Tinggi
  CF > 0,80  → Risiko Sangat Tinggi


REFERENSI
---------
- Shortliffe, E.H. (1976). Computer-Based Medical Consultations: MYCIN.
- Buss, D.M. & Shackelford, T.K. (1997). Cues to Infidelity.
- Giarratano, J. & Riley, G. (2004). Expert Systems.
- Suliati, Achmadi, & Rudhistiar (2023). Sistem Pakar Mental Illness dengan CF.
- Pratama & Prasetyaningrum (2024). CF Nomophobia berbasis Web.
- Alamsyah & Mardika (2024). Quarter Life Crisis dengan CF.

============================================================
