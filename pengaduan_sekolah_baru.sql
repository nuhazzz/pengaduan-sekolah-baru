-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 07 Feb 2026 pada 17.04
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
-- Database: `pengaduan_sekolah_baru`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admin`
--

CREATE TABLE `admin` (
  `id_admin` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `admin`
--

INSERT INTO `admin` (`id_admin`, `username`, `password_hash`) VALUES
(1, 'admin', '$2y$10$xzsKQfd3P/CV0Rm5pBH5ZeUDR/CoXJump2gxbvgAKPTiVR/is8OFa');

-- --------------------------------------------------------

--
-- Struktur dari tabel `aspirasi`
--

CREATE TABLE `aspirasi` (
  `id_aspirasi` int(5) NOT NULL,
  `id_pelaporan` int(11) NOT NULL,
  `id_kategori` int(5) NOT NULL,
  `status` enum('Menunggu','Proses','Selesai') NOT NULL DEFAULT 'Menunggu',
  `feedback` text DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `aspirasi`
--

INSERT INTO `aspirasi` (`id_aspirasi`, `id_pelaporan`, `id_kategori`, `status`, `feedback`, `updated_at`) VALUES
(1, 1, 4, 'Proses', 'oke bro lgsg aja', '2026-02-05 13:59:44'),
(2, 2, 3, 'Proses', '', '2026-02-07 05:08:35'),
(3, 3, 2, 'Selesai', 'done', '2026-02-07 06:18:20'),
(4, 4, 4, 'Selesai', '', '2026-02-07 07:22:20'),
(5, 5, 1, 'Proses', 'oke', '2026-02-07 07:35:20'),
(6, 7, 16, 'Proses', 'oke', '2026-02-07 15:42:31'),
(7, 8, 6, 'Proses', 'oke', '2026-02-07 15:42:22'),
(8, 9, 10, 'Menunggu', NULL, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `input_aspirasi`
--

CREATE TABLE `input_aspirasi` (
  `id_pelaporan` int(5) NOT NULL,
  `nis` int(10) NOT NULL,
  `id_kategori` int(5) NOT NULL,
  `lokasi` varchar(50) NOT NULL,
  `ket` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `input_aspirasi`
--

INSERT INTO `input_aspirasi` (`id_pelaporan`, `nis`, `id_kategori`, `lokasi`, `ket`, `created_at`) VALUES
(1, 123123, 4, 'Bagian Deket Kantor BK', 'Ring Basket Rusak', '2026-02-05 13:59:08'),
(2, 123123, 3, 'LANTAI 2', 'lampu mati', '2026-02-07 04:55:43'),
(3, 34567, 2, 'LAB MIPA', 'Kotor', '2026-02-07 05:38:47'),
(4, 123123, 4, 'Lapangan depan kantor guru', 'Ada yg bolong', '2026-02-07 07:00:02'),
(5, 34567, 1, 'Kelas X TKJ 1', 'Lantai Rusak', '2026-02-07 07:16:37'),
(6, 34567, 1, 'XII BC 2', 'Papan Tulis Rusak', '2026-01-14 07:36:26'),
(7, 321321, 16, 'Uks', 'Obat obatan sedikit', '2026-02-07 09:17:07'),
(8, 1231234, 6, 'XII BC 2', 'Kelas Kotor belum di bersihkan', '2026-02-07 15:41:57'),
(9, 1231234, 10, 'Lab Komputer', 'PC rusak', '2026-02-07 15:51:17'),
(50, 1001, 1, 'Kelas XII RPL 1', 'Lantai kelas kotor, minta dijadwalkan piket lebih ', '2026-01-10 01:15:00'),
(51, 1002, 3, 'Kelas XII RPL 1', 'Proyektor sering mati sendiri, kemungkinan kabel l', '2026-01-12 03:20:00'),
(52, 1003, 6, 'Toilet Timur', 'Keran bocor, air terus mengalir.', '2026-01-15 02:05:00'),
(53, 1004, 7, 'Kantin', 'Tempat sampah kurang, jadi menumpuk.', '2026-01-18 04:30:00'),
(54, 1005, 8, 'Lab Komputer', 'WiFi sering putus, butuh pengecekan router.', '2026-02-01 00:50:00'),
(55, 1001, 9, 'Koridor', 'Plafon ada yang retak, takut jatuh.', '2026-02-03 05:10:00'),
(56, 1002, 2, 'Parkiran', 'Lampu malam kurang terang.', '2026-02-05 11:40:00'),
(57, 1003, 4, 'Laboratorium', 'Beberapa alat praktikum rusak dan belum diganti.', '2026-02-06 02:25:00'),
(58, 1004, 5, 'Perpustakaan', 'AC kurang dingin dan berisik.', '2026-02-07 06:00:00'),
(59, 1005, 10, 'Sekolah', 'Saran: buat kotak aspirasi digital di website.', '2026-02-07 07:15:00');

-- --------------------------------------------------------

--
-- Struktur dari tabel `kategori`
--

CREATE TABLE `kategori` (
  `id_kategori` int(5) NOT NULL,
  `ket_kategori` varchar(30) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `kategori`
--

INSERT INTO `kategori` (`id_kategori`, `ket_kategori`) VALUES
(1, 'Kelas'),
(2, 'Laboratorium'),
(3, 'Toilet'),
(4, 'Lapangan'),
(5, 'Lainnya'),
(6, 'Kebersihan'),
(7, 'Keamanan'),
(8, 'Kedisiplinan'),
(9, 'Fasilitas Kelas'),
(10, 'Laboratorium'),
(11, 'Perpustakaan'),
(12, 'Toilet'),
(13, 'Kantin'),
(14, 'Parkir'),
(15, 'Lapangan/Olahraga'),
(16, 'UKS/Kesehatan'),
(17, 'Sarana IT/Internet'),
(18, 'Kerusakan Bangunan'),
(19, 'Pelayanan Tata Usaha'),
(20, 'Lainnya');

-- --------------------------------------------------------

--
-- Struktur dari tabel `siswa`
--

CREATE TABLE `siswa` (
  `nis` int(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kelas` varchar(10) NOT NULL,
  `password_hash` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data untuk tabel `siswa`
--

INSERT INTO `siswa` (`nis`, `nama`, `kelas`, `password_hash`) VALUES
(567, 'Udin', 'XII BC 1', '$2y$10$q3TIHp42Q9ExQSrEuX/qQud1iwP98HhOuY6SFK9y1qOrpju7IFR5u'),
(987, 'Denis', 'XII RPL 1', '$2y$10$m6WavY2R1Kfh3NiTCA4eweX/UIc.gQATwiBMlNZTYboS2srIIYHra'),
(1001, '', 'XII RPL 1', '$2y$10$REPLACE_WITH_YOUR_HASH'),
(1002, '', 'XII RPL 1', '$2y$10$REPLACE_WITH_YOUR_HASH'),
(1003, '', 'XI IPA 2', '$2y$10$REPLACE_WITH_YOUR_HASH'),
(1004, '', 'XI IPS 1', '$2y$10$REPLACE_WITH_YOUR_HASH'),
(1005, '', 'X TKJ 1', '$2y$10$REPLACE_WITH_YOUR_HASH'),
(34567, '', 'XII RPL 2', '$2y$10$bTam8zSeuDVOO4AIISOnleVA0ULq4mCCcxnPPI3ecTJ8BZAH8sU/W'),
(123123, '', 'XI RPL 1', '$2y$10$bxwaX/jqDhiU2/Q18FV3HuM/iz4u5rZj2xFuHlXV0HJ.pBf77fcDW'),
(321321, 'Nuhaz', 'XII RPL 2', '$2y$10$NV6tltSYkmFcEGPqsEdXh.dupkjjbqmMwYMyl2S23BXg6BIrKewcG'),
(1231234, 'Budi', 'XI TKJ 2', '$2y$10$9qJPRr6mmvdCvofpS5fnGOGSRWJ5pQ.5l/bFj22y5Dt0bRzXLR9Ri');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id_admin`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indeks untuk tabel `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD PRIMARY KEY (`id_aspirasi`),
  ADD UNIQUE KEY `id_pelaporan` (`id_pelaporan`),
  ADD KEY `fk_aspirasi_kategori` (`id_kategori`),
  ADD KEY `id_pelaporan_2` (`id_pelaporan`);

--
-- Indeks untuk tabel `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  ADD PRIMARY KEY (`id_pelaporan`),
  ADD KEY `fk_input_siswa` (`nis`),
  ADD KEY `fk_input_kategori` (`id_kategori`);

--
-- Indeks untuk tabel `kategori`
--
ALTER TABLE `kategori`
  ADD PRIMARY KEY (`id_kategori`);

--
-- Indeks untuk tabel `siswa`
--
ALTER TABLE `siswa`
  ADD PRIMARY KEY (`nis`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admin`
--
ALTER TABLE `admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `aspirasi`
--
ALTER TABLE `aspirasi`
  MODIFY `id_aspirasi` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  MODIFY `id_pelaporan` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT untuk tabel `kategori`
--
ALTER TABLE `kategori`
  MODIFY `id_kategori` int(5) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `aspirasi`
--
ALTER TABLE `aspirasi`
  ADD CONSTRAINT `fk_aspirasi_input` FOREIGN KEY (`id_pelaporan`) REFERENCES `input_aspirasi` (`id_pelaporan`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aspirasi_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_aspirasi_pelaporan` FOREIGN KEY (`id_pelaporan`) REFERENCES `input_aspirasi` (`id_pelaporan`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `input_aspirasi`
--
ALTER TABLE `input_aspirasi`
  ADD CONSTRAINT `fk_input_kategori` FOREIGN KEY (`id_kategori`) REFERENCES `kategori` (`id_kategori`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_input_siswa` FOREIGN KEY (`nis`) REFERENCES `siswa` (`nis`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
