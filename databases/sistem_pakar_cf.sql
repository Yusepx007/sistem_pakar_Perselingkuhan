-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3307
-- Waktu pembuatan: 06 Agu 2026 pada 17.40
-- Versi server: 10.4.32-MariaDB
-- Versi PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sistem_pakar_cf`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `gejala`
--

CREATE TABLE `gejala` (
  `id` int(11) NOT NULL,
  `kode` varchar(10) NOT NULL,
  `nama` text NOT NULL,
  `kategori` varchar(80) NOT NULL,
  `mb` decimal(3,2) NOT NULL COMMENT 'Measure of Belief (0-1)',
  `md` decimal(3,2) NOT NULL COMMENT 'Measure of Disbelief (0-1)',
  `cf` decimal(4,2) NOT NULL COMMENT 'Certainty Factor = MB - MD'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `gejala`
--

INSERT INTO `gejala` (`id`, `kode`, `nama`, `kategori`, `mb`, `md`, `cf`) VALUES
(1, 'G01', 'Jarang berkomunikasi atau menghindari percakapan dengan pasangan', 'Perubahan Komunikasi', 0.60, 0.50, 0.10),
(2, 'G02', 'Sangat protektif terhadap ponsel (mengunci, mengubah password)', 'Perubahan Komunikasi', 0.50, 0.10, 0.40),
(3, 'G03', 'Sering menghapus riwayat chat, panggilan, atau notifikasi', 'Perubahan Komunikasi', 0.90, 0.10, 0.80),
(4, 'G04', 'Menggunakan aplikasi tersembunyi atau akun media sosial rahasia', 'Perubahan Komunikasi', 0.80, 0.00, 0.80),
(5, 'G05', 'Menghindari pertanyaan tentang aktivitas atau keberadaan', 'Perubahan Komunikasi', 1.00, 0.00, 1.00),
(6, 'G06', 'Nada bicara lebih dingin, defensif, atau mudah tersinggung', 'Perubahan Komunikasi', 0.50, 0.10, 0.40),
(7, 'G07', 'Perubahan mood yang drastis dan tidak dapat dijelaskan', 'Perubahan Perilaku Emosional', 0.20, 0.30, -0.10),
(8, 'G08', 'Lebih sering marah atau tidak sabar tanpa alasan jelas', 'Perubahan Perilaku Emosional', 0.40, 0.20, 0.20),
(9, 'G09', 'Tampak bersalah atau cemas berlebihan saat ditanya rutinitas', 'Perubahan Perilaku Emosional', 0.70, 0.00, 0.70),
(10, 'G10', 'Menarik diri secara emosional ??? tidak berbagi perasaan', 'Perubahan Perilaku Emosional', 0.50, 0.10, 0.40),
(11, 'G11', 'Ketidakpuasan emosional yang diungkapkan berulang kali', 'Perubahan Perilaku Emosional', 1.00, 0.00, 1.00),
(12, 'G12', 'Kehilangan minat pada masa depan bersama pasangan', 'Perubahan Perilaku Emosional', 0.90, 0.10, 0.80),
(13, 'G13', 'Tiba-tiba sangat memperhatikan penampilan tanpa alasan jelas', 'Perubahan Fisik & Penampilan', 0.20, 0.00, 0.20),
(14, 'G14', 'Tercium aroma parfum atau produk yang tidak biasa digunakan', 'Perubahan Fisik & Penampilan', 0.00, 0.50, -0.50),
(15, 'G15', 'Penurunan atau peningkatan gairah seksual yang signifikan', 'Perubahan Fisik & Penampilan', 0.20, 0.10, 0.10),
(16, 'G16', 'Menghindari keintiman fisik dengan pasangan', 'Perubahan Fisik & Penampilan', 0.80, 0.10, 0.70),
(17, 'G17', 'Sering pulang terlambat tanpa penjelasan yang konsisten', 'Perubahan Fisik & Penampilan', 0.80, 0.00, 0.80),
(18, 'G18', 'Sering keluar rumah dengan alasan yang tidak jelas', 'Perubahan Fisik & Penampilan', 0.70, 0.00, 0.70),
(19, 'G19', 'Menyebut/menghindari nama orang tertentu secara berulang', 'Perubahan Sosial & Lingkungan', 0.80, 0.00, 0.80),
(20, 'G20', 'Menghindari pertemuan sosial bersama pasangan', 'Perubahan Sosial & Lingkungan', 0.40, 0.10, 0.30),
(21, 'G21', 'Memiliki teman baru yang sering dihubungi namun tidak diperkenalkan', 'Perubahan Sosial & Lingkungan', 0.90, 0.10, 0.80),
(22, 'G22', 'Menghabiskan lebih banyak waktu di luar tanpa alasan produktif', 'Perubahan Sosial & Lingkungan', 0.30, 0.10, 0.20),
(23, 'G23', 'Menjaga jarak dari keluarga inti pasangan', 'Perubahan Sosial & Lingkungan', 0.70, 0.00, 0.70),
(24, 'G24', 'Pengeluaran yang tidak dapat dijelaskan atau transaksi mencurigakan', 'Perubahan Finansial', 0.90, 0.00, 0.90),
(25, 'G25', 'Membuat rekening atau kartu kredit baru secara rahasia', 'Perubahan Finansial', 0.90, 0.00, 0.90),
(26, 'G26', 'Memberikan hadiah kepada orang lain tanpa alasan yang jelas', 'Perubahan Finansial', 0.80, 0.10, 0.70),
(27, 'G27', 'Pengeluaran meningkat untuk keperluan tidak dikenal pasangan', 'Perubahan Finansial', 0.90, 0.10, 0.80),
(28, 'G28', 'Riwayat perselingkuhan sebelumnya (diri sendiri)', 'Faktor Predisposisi', 0.90, 0.00, 0.90),
(29, 'G29', 'Riwayat perselingkuhan dalam keluarga asal (orang tua)', 'Faktor Predisposisi', 0.50, 0.20, 0.30),
(30, 'G30', 'Ketidakpuasan berkepanjangan yang tidak pernah diselesaikan', 'Faktor Predisposisi', 1.00, 0.00, 1.00),
(31, 'G31', 'Pernah dikhianati dalam hubungan sebelumnya (trauma attachment)', 'Faktor Predisposisi', 0.80, 0.10, 0.70),
(32, 'G32', 'Kecenderungan narsistik atau sensation-seeking yang tinggi', 'Faktor Predisposisi', 0.90, 0.10, 0.80),
(33, 'G33', 'Kurangnya komitmen yang diungkapkan dalam hubungan', 'Faktor Predisposisi', 1.00, 0.00, 1.00);

-- --------------------------------------------------------

--
-- Struktur dari tabel `konsultasi`
--

CREATE TABLE `konsultasi` (
  `id` int(11) NOT NULL,
  `nama_pengguna` varchar(100) DEFAULT 'Anonim',
  `gejala_dipilih` text NOT NULL COMMENT 'Kode gejala dipisah koma: G01,G03,...',
  `nilai_cf` decimal(5,3) NOT NULL,
  `level_risiko` varchar(50) NOT NULL,
  `tanggal` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;

--
-- Dumping data untuk tabel `konsultasi`
--

INSERT INTO `konsultasi` (`id`, `nama_pengguna`, `gejala_dipilih`, `nilai_cf`, `level_risiko`, `tanggal`) VALUES
(2, 'Entang Sudrajat', 'G20,G29', 0.510, 'Risiko Sedang', '2026-05-01 14:11:42'),
(3, 'Silfa Fuji', 'G05,G09,G18,G24,G31', 1.000, 'Risiko Sangat Tinggi', '2026-05-02 00:36:03'),
(4, 'Mia Amelia', 'G03,G04,G16,G24,G28,G32', 1.000, 'Risiko Sangat Tinggi', '2026-05-03 00:44:09'),
(5, 'Ahmad Sopandi', 'G08,G22', 0.360, 'Risiko Rendah', '2026-05-03 17:22:52'),
(6, 'Silfa Fuji', 'G07,G08,G13', 0.296, 'Risiko Rendah', '2026-05-04 18:19:57'),
(7, 'Gilang Permana', 'G13,G22', 0.360, 'Risiko Rendah', '2026-05-04 19:20:13'),
(8, 'Ahmad Sopandi', 'G07', -0.100, 'Risiko Sangat Rendah', '2026-05-06 01:33:28'),
(9, 'Fikri Maulana', 'G08', 0.200, 'Risiko Sangat Rendah', '2026-05-06 05:41:37'),
(10, 'Popon Sopiah', 'G01,G08,G15', 0.352, 'Risiko Rendah', '2026-05-07 01:12:10'),
(11, 'Salsa Nurjanah', 'G02,G06', 0.640, 'Risiko Tinggi', '2026-05-08 19:49:53'),
(12, 'Selvia', 'G06,G10', 0.640, 'Risiko Tinggi', '2026-05-10 06:53:06'),
(13, 'Wawan Setiadi', 'G08,G20', 0.440, 'Risiko Sedang', '2026-05-11 12:16:51'),
(14, 'Nana', 'G02,G06', 0.640, 'Risiko Tinggi', '2026-05-12 05:47:59'),
(15, 'Rina Marlina', 'G29', 0.300, 'Risiko Rendah', '2026-05-12 22:30:25'),
(16, 'Gilang Permana', 'G08,G14', -0.200, 'Risiko Sangat Rendah', '2026-05-13 04:19:54'),
(17, 'Sri Wulandari', 'G29', 0.300, 'Risiko Rendah', '2026-05-13 08:44:46'),
(18, 'Rangga Saputra', 'G02,G03,G17,G24,G26,G31', 1.000, 'Risiko Sangat Tinggi', '2026-05-14 13:37:03'),
(19, 'Yuda', 'G08,G15,G29', 0.496, 'Risiko Sedang', '2026-05-15 05:53:01'),
(20, 'Dadang Hermawan', 'G07,G08,G20', 0.384, 'Risiko Rendah', '2026-05-16 01:44:40'),
(21, 'Nana', 'G07,G20', 0.230, 'Risiko Rendah', '2026-05-16 21:36:07'),
(22, 'Asep Suryana', 'G02,G10', 0.640, 'Risiko Tinggi', '2026-05-18 01:25:49'),
(23, 'Imas Masitoh', 'G29', 0.300, 'Risiko Rendah', '2026-05-18 02:39:13'),
(24, 'Tati Rohaeti', 'G10,G22', 0.520, 'Risiko Sedang', '2026-05-18 03:11:51'),
(25, 'Yeni Yulianti', 'G06,G10,G16,G21,G28', 0.998, 'Risiko Sangat Tinggi', '2026-05-19 10:12:57'),
(26, 'Indriani', 'G01,G10,G15', 0.514, 'Risiko Sedang', '2026-05-22 03:31:29'),
(27, 'Elin Herlina', 'G08,G14,G15', -0.080, 'Risiko Sangat Rendah', '2026-05-22 22:51:26'),
(28, 'Siti Komariah', 'G23,G25,G33', 1.000, 'Risiko Sangat Tinggi', '2026-05-22 23:53:33'),
(29, 'Winda Lestari', 'G06,G10', 0.640, 'Risiko Tinggi', '2026-05-23 01:33:46'),
(30, 'Ade Rachman', 'G22', 0.200, 'Risiko Sangat Rendah', '2026-05-24 00:43:23'),
(31, 'Elin Herlina', 'G08,G13,G14', 0.040, 'Risiko Sangat Rendah', '2026-05-24 05:44:33'),
(32, 'Ujang Supriatna', 'G01,G07', 0.010, 'Risiko Sangat Rendah', '2026-05-24 20:27:52'),
(33, 'Silfa Fuji', 'G10,G32', 0.880, 'Risiko Sangat Tinggi', '2026-05-26 10:33:20'),
(34, 'Cucu Rohayati', 'G15', 0.100, 'Risiko Sangat Rendah', '2026-05-26 15:11:33'),
(35, 'Tatang Suhendar', 'G02,G15', 0.460, 'Risiko Sedang', '2026-05-27 01:17:58'),
(36, 'Fikri Maulana', 'G07,G10,G22', 0.472, 'Risiko Sedang', '2026-05-27 18:37:58'),
(37, 'Selvia', 'G02,G15', 0.460, 'Risiko Sedang', '2026-05-28 01:14:59'),
(38, 'Iis Sumiati', 'G01,G14', -0.350, 'Risiko Sangat Rendah', '2026-05-28 20:22:17'),
(39, 'Yuda', 'G04,G16,G18,G19,G31,G32', 1.000, 'Risiko Sangat Tinggi', '2026-05-28 22:50:18'),
(40, 'Salsa Nurjanah', 'G10,G19,G25', 0.988, 'Risiko Sangat Tinggi', '2026-05-30 21:05:40'),
(41, 'Aan Anwar', 'G01,G08', 0.280, 'Risiko Rendah', '2026-05-31 23:51:32'),
(42, 'Popon Sopiah', 'G02,G06', 0.640, 'Risiko Tinggi', '2026-06-04 15:29:47'),
(43, 'Rizki Ramdani', 'G03,G12,G32', 0.992, 'Risiko Sangat Tinggi', '2026-06-04 16:57:01'),
(44, 'Ai Kurniasih', 'G02,G10', 0.640, 'Risiko Tinggi', '2026-06-05 04:34:25'),
(45, 'Aan Anwar', 'G01,G06,G13', 0.568, 'Risiko Sedang', '2026-06-05 12:53:43'),
(46, 'Dedi Mulyadi', 'G02,G10', 0.640, 'Risiko Tinggi', '2026-06-07 10:31:18'),
(47, 'Euis Nuraeni', 'G07,G22', 0.120, 'Risiko Sangat Rendah', '2026-06-11 14:10:45'),
(48, 'Enung Nurhayati', 'G01', 0.100, 'Risiko Sangat Rendah', '2026-06-11 23:07:55'),
(49, 'Nana Suryana', 'G15,G20', 0.370, 'Risiko Rendah', '2026-06-12 12:34:50'),
(50, 'Sri Wulandari', 'G01,G20,G22', 0.496, 'Risiko Sedang', '2026-06-12 14:56:56'),
(51, 'Asep Suryana', 'G13,G14', -0.200, 'Risiko Sangat Rendah', '2026-06-13 02:20:03'),
(52, 'Nana', 'G01,G07,G14', -0.485, 'Risiko Sangat Rendah', '2026-06-13 04:52:55'),
(53, 'Nabila Putri', 'G20', 0.300, 'Risiko Rendah', '2026-06-14 07:02:13'),
(54, 'Imas Masitoh', 'G15,G22,G29', 0.496, 'Risiko Sedang', '2026-06-14 08:48:57'),
(55, 'Ade Rachman', 'G07,G13,G29', 0.384, 'Risiko Rendah', '2026-06-17 19:43:37'),
(56, 'Cecep Setiawan', 'G06,G10', 0.640, 'Risiko Tinggi', '2026-06-18 19:33:13'),
(57, 'Nina', 'G06,G08', 0.520, 'Risiko Sedang', '2026-06-19 02:17:22'),
(58, 'Tati Rohaeti', 'G02,G06', 0.640, 'Risiko Tinggi', '2026-06-19 18:11:15'),
(59, 'Nabila Putri', 'G02,G06', 0.640, 'Risiko Tinggi', '2026-06-21 00:17:18'),
(60, 'Yayat Ruhiat', 'G29', 0.300, 'Risiko Rendah', '2026-06-22 07:39:37'),
(61, 'Lilis Suryani', 'G03,G28', 0.980, 'Risiko Sangat Tinggi', '2026-06-23 07:37:02'),
(62, 'Neneng Kartika', 'G09,G25,G27', 0.994, 'Risiko Sangat Tinggi', '2026-06-23 17:37:24'),
(63, 'Cucu Rohayati', 'G02,G06', 0.640, 'Risiko Tinggi', '2026-06-24 23:37:43'),
(64, 'Fikri Maulana', 'G06,G10', 0.640, 'Risiko Tinggi', '2026-06-26 17:01:02'),
(65, 'Rizki Ramdani', 'G01,G08,G14', -0.080, 'Risiko Sangat Rendah', '2026-06-27 01:47:20'),
(66, 'Iwan Setiawan', 'G14,G20', -0.050, 'Risiko Sangat Rendah', '2026-06-27 18:18:33'),
(67, 'Lilis Suryani', 'G01,G13,G20', 0.496, 'Risiko Sedang', '2026-06-30 04:03:08'),
(68, 'Yusup Maulana', 'G03,G06,G28,G31', 0.996, 'Risiko Sangat Tinggi', '2026-07-05 22:05:05'),
(69, 'Aan Anwar', 'G14,G15', -0.350, 'Risiko Sangat Rendah', '2026-07-06 19:05:42'),
(70, 'Cecep Setiawan', 'G04,G33', 1.000, 'Risiko Sangat Tinggi', '2026-07-06 20:33:42'),
(71, 'Rangga Saputra', 'G06,G10', 0.640, 'Risiko Tinggi', '2026-07-07 19:32:03'),
(72, 'Nana Suryana', 'G02,G06', 0.640, 'Risiko Tinggi', '2026-07-08 00:36:31'),
(73, 'Fajar Nugraha', 'G05,G11,G16,G28', 1.000, 'Risiko Sangat Tinggi', '2026-07-08 12:22:25'),
(74, 'Nina', 'G06,G08', 0.520, 'Risiko Sedang', '2026-07-09 21:02:43'),
(75, 'Euis Nuraeni', 'G02,G10', 0.640, 'Risiko Tinggi', '2026-07-09 21:22:14'),
(76, 'Iwan Setiawan', 'G06,G10', 0.640, 'Risiko Tinggi', '2026-07-11 14:46:49'),
(77, 'Mia Amelia', 'G29', 0.300, 'Risiko Rendah', '2026-07-12 04:34:24'),
(78, 'Wawan Setiadi', 'G08,G20', 0.440, 'Risiko Sedang', '2026-07-12 12:02:50'),
(79, 'Dewi Anggraeni', 'G02,G13', 0.520, 'Risiko Sedang', '2026-07-12 17:07:40'),
(80, 'Indriani', 'G13,G22,G29', 0.552, 'Risiko Sedang', '2026-07-13 04:56:57'),
(81, 'Winda Lestari', 'G02,G10', 0.640, 'Risiko Tinggi', '2026-07-13 12:24:51'),
(82, 'Ai Kurniasih', 'G06,G13', 0.520, 'Risiko Sedang', '2026-07-14 09:31:43'),
(83, 'Yusup Maulana', 'G01,G08,G13', 0.424, 'Risiko Sedang', '2026-07-14 21:41:29'),
(84, 'Yayat Ruhiat', 'G01,G29', 0.370, 'Risiko Rendah', '2026-07-15 13:43:36'),
(85, 'Fajar Nugraha', 'G08,G15', 0.280, 'Risiko Rendah', '2026-07-16 08:30:41'),
(86, 'Tatang Suhendar', 'G17,G18,G21,G23,G25', 1.000, 'Risiko Sangat Tinggi', '2026-07-17 22:10:49'),
(87, 'Entang Sudrajat', 'G08', 0.200, 'Risiko Sangat Rendah', '2026-07-18 04:20:24'),
(88, 'Indriani', 'G01,G06,G15', 0.514, 'Risiko Sedang', '2026-07-20 19:04:28'),
(89, 'Yayat Ruhiat', 'G06,G20', 0.580, 'Risiko Sedang', '2026-07-22 11:20:13'),
(90, 'Dewi Anggraeni', 'G12,G17,G28', 0.996, 'Risiko Sangat Tinggi', '2026-07-22 12:00:36'),
(91, 'Wawan Setiadi', 'G15,G22', 0.280, 'Risiko Rendah', '2026-07-24 10:20:15'),
(92, 'Enung Nurhayati', 'G14', -0.500, 'Risiko Sangat Rendah', '2026-07-26 18:03:05'),
(93, 'Rina Marlina', 'G03,G16,G30,G31', 1.000, 'Risiko Sangat Tinggi', '2026-07-27 19:05:59'),
(94, 'Yeni Yulianti', 'G20', 0.300, 'Risiko Rendah', '2026-07-28 09:01:19'),
(95, 'Neneng Kartika', 'G12,G17,G32', 0.992, 'Risiko Sangat Tinggi', '2026-07-28 11:59:30'),
(96, 'Selvia', 'G04,G10,G27,G31,G32', 0.999, 'Risiko Sangat Tinggi', '2026-07-28 15:43:33'),
(97, 'Dedi Mulyadi', 'G02,G10', 0.640, 'Risiko Tinggi', '2026-07-29 20:19:10'),
(98, 'Dadang Hermawan', 'G02,G19', 0.880, 'Risiko Sangat Tinggi', '2026-08-01 06:41:20'),
(99, 'Siti Komariah', 'G01,G10', 0.460, 'Risiko Sedang', '2026-08-01 13:39:38'),
(100, 'Iis Sumiati', 'G01,G29', 0.370, 'Risiko Rendah', '2026-08-03 02:00:42'),
(101, 'Ujang Supriatna', 'G16,G21,G28,G32,G33', 1.000, 'Risiko Sangat Tinggi', '2026-08-04 06:41:30');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `gejala`
--
ALTER TABLE `gejala`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `kode` (`kode`);

--
-- Indeks untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `gejala`
--
ALTER TABLE `gejala`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT untuk tabel `konsultasi`
--
ALTER TABLE `konsultasi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
