-- ============================================================
--  UPDATE GEJALA: 33 Indikator Sesuai Skripsi Ela Amelia
--  Sistem Pakar Identifikasi Risiko Perselingkuhan — CF Method
--  Pakar: Kania Hidayah, M.Psi., Psikolog
--  STMIK DCI Tasikmalaya, 2026
-- ============================================================

-- 1. Tambah kolom user_id ke tabel konsultasi (jika belum ada)
ALTER TABLE konsultasi
  ADD COLUMN IF NOT EXISTS `user_id` INT DEFAULT NULL AFTER `id`,
  ADD INDEX IF NOT EXISTS `idx_user_id` (`user_id`);

-- 2. Hapus semua gejala lama, ganti dengan 33 gejala baku sesuai skripsi
TRUNCATE TABLE gejala;

-- ─────────────────────────────────────────────────────────────
--  KATEGORI 1: Perubahan Komunikasi (G01–G06)
-- ─────────────────────────────────────────────────────────────
INSERT INTO gejala (kode, nama, kategori, mb, md, cf) VALUES
('G01',
 'Jarang berkomunikasi atau menghindari percakapan sehari-hari dengan pasangan',
 'Perubahan Komunikasi', 0.10, 0.00,  0.10),

('G02',
 'Sangat protektif terhadap ponsel (mengunci layar, membalik ponsel, menjauhkan ponsel saat bersama pasangan)',
 'Perubahan Komunikasi', 0.40, 0.00,  0.40),

('G03',
 'Sering menghapus riwayat chat, panggilan, atau pesan dari ponsel',
 'Perubahan Komunikasi', 0.80, 0.00,  0.80),

('G04',
 'Menggunakan aplikasi tersembunyi atau akun media sosial yang tidak diketahui pasangan',
 'Perubahan Komunikasi', 0.80, 0.00,  0.80),

('G05',
 'Berbicara tentang seseorang tertentu secara berlebihan atau justru sama sekali menghindari menyebut nama tersebut',
 'Perubahan Komunikasi', 0.60, 0.00,  0.60),

('G06',
 'Nada bicara lebih dingin, defensif, atau mudah tersinggung saat ditanya tentang aktivitas harian',
 'Perubahan Komunikasi', 0.40, 0.00,  0.40);

-- ─────────────────────────────────────────────────────────────
--  KATEGORI 2: Perubahan Perilaku Emosional (G07–G12)
-- ─────────────────────────────────────────────────────────────
INSERT INTO gejala (kode, nama, kategori, mb, md, cf) VALUES
('G07',
 'Menjadi lebih perhatian dan romantis secara tiba-tiba tanpa alasan yang jelas (perilaku kompensasi rasa bersalah)',
 'Perubahan Perilaku Emosional', 0.00, 0.20, -0.20),

('G08',
 'Lebih sering marah atau tidak sabar tanpa alasan yang jelas kepada pasangan',
 'Perubahan Perilaku Emosional', 0.20, 0.00,  0.20),

('G09',
 'Tampak bersalah atau cemas berlebihan saat ditanya tentang aktivitas atau keberadaannya',
 'Perubahan Perilaku Emosional', 0.70, 0.00,  0.70),

('G10',
 'Menarik diri secara emosional — tidak lagi berbagi cerita, masalah, atau perasaan dengan pasangan',
 'Perubahan Perilaku Emosional', 0.40, 0.00,  0.40),

('G11',
 'Membandingkan pasangan dengan orang lain secara negatif dan berulang',
 'Perubahan Perilaku Emosional', 0.60, 0.00,  0.60),

('G12',
 'Kehilangan minat pada rencana masa depan bersama pasangan (pernikahan, liburan, investasi bersama, dll.)',
 'Perubahan Perilaku Emosional', 0.80, 0.00,  0.80);

-- ─────────────────────────────────────────────────────────────
--  KATEGORI 3: Perubahan Fisik & Penampilan (G13–G18)
-- ─────────────────────────────────────────────────────────────
INSERT INTO gejala (kode, nama, kategori, mb, md, cf) VALUES
('G13',
 'Tiba-tiba sangat memperhatikan penampilan fisik tanpa alasan jelas (gaya berpakaian baru, wewangian baru, olahraga intensif)',
 'Perubahan Fisik & Penampilan', 0.20, 0.00,  0.20),

('G14',
 'Perubahan jadwal tidur atau rutinitas harian yang mendadak dan tidak dapat dijelaskan secara logis',
 'Perubahan Fisik & Penampilan', 0.00, 0.10, -0.10),

('G15',
 'Penurunan atau peningkatan gairah seksual yang signifikan tanpa alasan medis yang jelas',
 'Perubahan Fisik & Penampilan', 0.10, 0.00,  0.10),

('G16',
 'Menghindari keintiman fisik dengan pasangan (pelukan, ciuman, hubungan intim)',
 'Perubahan Fisik & Penampilan', 0.70, 0.00,  0.70),

('G17',
 'Sering pulang terlambat dari kerja atau kegiatan tanpa penjelasan yang dapat diverifikasi',
 'Perubahan Fisik & Penampilan', 0.80, 0.00,  0.80),

('G18',
 'Sering keluar rumah dengan alasan yang tidak dapat diverifikasi atau berubah-ubah',
 'Perubahan Fisik & Penampilan', 0.70, 0.00,  0.70);

-- ─────────────────────────────────────────────────────────────
--  KATEGORI 4: Perubahan Sosial & Lingkungan (G19–G23)
-- ─────────────────────────────────────────────────────────────
INSERT INTO gejala (kode, nama, kategori, mb, md, cf) VALUES
('G19',
 'Menyebut atau secara konsisten menghindari menyebut nama seseorang tertentu dalam percakapan',
 'Perubahan Sosial & Lingkungan', 0.80, 0.00,  0.80),

('G20',
 'Menghindari acara atau pertemuan sosial bersama pasangan tanpa alasan yang jelas',
 'Perubahan Sosial & Lingkungan', 0.30, 0.00,  0.30),

('G21',
 'Memiliki teman baru yang sering dihubungi namun tidak pernah diperkenalkan kepada pasangan',
 'Perubahan Sosial & Lingkungan', 0.80, 0.00,  0.80),

('G22',
 'Menghabiskan lebih banyak waktu di luar tanpa kejelasan lokasi atau kegiatan',
 'Perubahan Sosial & Lingkungan', 0.20, 0.00,  0.20),

('G23',
 'Menjaga jarak atau menghindari interaksi dengan keluarga inti pasangan',
 'Perubahan Sosial & Lingkungan', 0.70, 0.00,  0.70);

-- ─────────────────────────────────────────────────────────────
--  KATEGORI 5: Perubahan Finansial (G24–G27)
-- ─────────────────────────────────────────────────────────────
INSERT INTO gejala (kode, nama, kategori, mb, md, cf) VALUES
('G24',
 'Terdapat pengeluaran yang tidak dapat dijelaskan atau tidak sesuai dengan pendapatan yang diketahui',
 'Perubahan Finansial', 0.60, 0.00,  0.60),

('G25',
 'Menyembunyikan tagihan, rekening bank, atau riwayat transaksi keuangan dari pasangan',
 'Perubahan Finansial', 0.70, 0.00,  0.70),

('G26',
 'Memberikan hadiah atau mentransfer uang kepada orang lain tanpa alasan yang jelas kepada pasangan',
 'Perubahan Finansial', 0.70, 0.00,  0.70),

('G27',
 'Perubahan kebiasaan finansial yang mendadak dan signifikan (lebih boros di luar rumah atau sebaliknya sangat pelit di rumah)',
 'Perubahan Finansial', 0.40, 0.00,  0.40);

-- ─────────────────────────────────────────────────────────────
--  KATEGORI 6: Faktor Predisposisi (G28–G33)
-- ─────────────────────────────────────────────────────────────
INSERT INTO gejala (kode, nama, kategori, mb, md, cf) VALUES
('G28',
 'Memiliki riwayat perselingkuhan sebelumnya (pernah selingkuh dalam hubungan terdahulu)',
 'Faktor Predisposisi', 0.80, 0.00,  0.80),

('G29',
 'Menunjukkan kepribadian narsistik atau kecenderungan sensation-seeking yang tinggi',
 'Faktor Predisposisi', 0.60, 0.00,  0.60),

('G30',
 'Mengungkapkan atau menunjukkan ketidakpuasan yang mendalam terhadap hubungan saat ini',
 'Faktor Predisposisi', 0.70, 0.00,  0.70),

('G31',
 'Terpapar lingkungan sosial atau pergaulan yang permisif dan mentoleransi perselingkuhan',
 'Faktor Predisposisi', 0.50, 0.00,  0.50),

('G32',
 'Mengalami tekanan kerja atau stres berat yang tidak pernah dikomunikasikan kepada pasangan',
 'Faktor Predisposisi', 0.30, 0.00,  0.30),

('G33',
 'Memiliki riwayat perselingkuhan dalam keluarga asal (orang tua atau saudara kandung pernah berselingkuh)',
 'Faktor Predisposisi', 0.40, 0.00,  0.40);

-- ─────────────────────────────────────────────────────────────
--  VERIFIKASI: harus ada 33 baris
-- ─────────────────────────────────────────────────────────────
SELECT COUNT(*) AS total_gejala FROM gejala;
-- Expected: 33
