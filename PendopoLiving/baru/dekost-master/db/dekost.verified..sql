-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jul 12, 2022 at 07:24 PM
-- Server version: 10.4.22-MariaDB
-- PHP Version: 8.1.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `dekost`
--

-- --------------------------------------------------------

--
-- Table structure for table `fasilitas`
--

CREATE TABLE `fasilitas` (
  `id` int(2) NOT NULL,
  `nama` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fasilitas`
--

INSERT INTO `fasilitas` (`id`, `nama`) VALUES
(1, 'AC'),
(2, 'Kamar Mandi Dalam'),
(3, 'Wifi'),
(4, 'Air'),
(5, 'Listrik'),
(6, 'Kasur'),
(7, 'Lemari'),
(8, 'Meja');

-- --------------------------------------------------------

--
-- Table structure for table `fasil_kost`
--

CREATE TABLE `fasil_kost` (
  `id_kost` int(11) NOT NULL,
  `id_fasil` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `fasil_kost`
--

INSERT INTO `fasil_kost` (`id_kost`, `id_fasil`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),
(2, 6),
(2, 7),
(2, 8),
(3, 2),
(3, 3),
(3, 4),
(3, 6),
(3, 7),
(4, 3),
(4, 4),
(4, 5);

-- --------------------------------------------------------

--
-- Table structure for table `kamar`
--

CREATE TABLE `kamar` (
  `id_kost` int(6) DEFAULT NULL,
  `status` varchar(6) DEFAULT NULL,
  `lebar` float DEFAULT NULL,
  `panjang` float DEFAULT NULL,
  `idKamar` varchar(15) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kamar`
--

INSERT INTO `kamar` (`id_kost`, `status`, `lebar`, `panjang`, `idKamar`) VALUES
(5, 'TERISI', 3, 4, '62cb6b0bce83e'),
(5, 'TERISI', 3, 4, '62cb6b0bcfb3b'),
(5, 'KOSONG', 3, 4, '62cb6b0bd0e70'),
(5, 'KOSONG', 3, 4, '62cb6b0bd468f'),
(5, 'KOSONG', 3, 4, '62cb6b0bd595f'),
(5, 'KOSONG', 3, 4, '62cb6b0bd6fa2'),
(5, 'TERISI', 3, 4, '62cb6b0bd84c0'),
(5, 'KOSONG', 3, 4, '62cb6b0bd99cc'),
(5, 'KOSONG', 3, 4, '62cb6b0bdae42'),
(5, 'KOSONG', 3, 4, '62cb6b0bdc3ab'),
(5, 'KOSONG', 3, 4, '62cb6b0bdd8fd'),
(5, 'KOSONG', 3, 4, '62cb6b0bded87'),
(5, 'KOSONG', 3, 4, '62cb6b0be02a6'),
(5, 'KOSONG', 3, 4, '62cb6b0be1736'),
(5, 'TERISI', 3, 4, '62cb6b0be2a5b'),
(5, 'KOSONG', 3, 4, '62cb6b0be3c5b'),
(5, 'TERISI', 3, 4, '62cb6b0be4ea0'),
(5, 'KOSONG', 3, 4, '62cb6b0be612d'),
(5, 'KOSONG', 3, 4, '62cb6b0be7349'),
(5, 'KOSONG', 3, 4, '62cb6b0be84d9'),
(3, 'KOSONG', 3, 4, '62cb6bb97d831'),
(3, 'KOSONG', 3, 4, '62cb6bb9807c8'),
(3, 'KOSONG', 3, 4, '62cb6bb983217'),
(3, 'TERISI', 3, 4, '62cb6bb9868e2'),
(3, 'KOSONG', 3, 4, '62cb6bb98905d'),
(3, 'KOSONG', 3, 4, '62cb6bb98cdfa'),
(3, 'TERISI', 3, 4, '62cb6bb98e245'),
(3, 'KOSONG', 3, 4, '62cb6bb98f107'),
(3, 'KOSONG', 3, 4, '62cb6bb98ffc5'),
(3, 'KOSONG', 3, 4, '62cb6bb9922b8'),
(3, 'TERISI', 3, 4, '62cb6bb993545'),
(3, 'KOSONG', 3, 4, '62cb6bb9947f3'),
(3, 'KOSONG', 3, 4, '62cb6bb9959af'),
(3, 'KOSONG', 3, 4, '62cb6bb996c62'),
(3, 'KOSONG', 3, 4, '62cb6bb997ecc'),
(3, 'KOSONG', 3, 4, '62cb6bb99923e'),
(3, 'KOSONG', 3, 4, '62cb6bb99a3da'),
(3, 'KOSONG', 3, 4, '62cb6bb99b858'),
(3, 'KOSONG', 3, 4, '62cb6bb99cc94'),
(3, 'TERISI', 3, 4, '62cb6bb99e105'),
(3, 'KOSONG', 3, 4, '62cb6bb99f5a6'),
(6, 'KOSONG', 3, 4, '62cd168236bc2'),
(6, 'KOSONG', 3, 4, '62cd168237e89'),
(6, 'KOSONG', 3, 4, '62cd1682390ed'),
(6, 'KOSONG', 3, 4, '62cd16823a3d7'),
(6, 'KOSONG', 3, 4, '62cd16823b6a7'),
(6, 'KOSONG', 3, 4, '62cd16823c936'),
(6, 'KOSONG', 3, 4, '62cd16823dbdd'),
(6, 'KOSONG', 3, 4, '62cd16824245e'),
(6, 'KOSONG', 3, 4, '62cd1682436f5'),
(6, 'KOSONG', 3, 4, '62cd168244ad2'),
(6, 'KOSONG', 3, 4, '62cd168245db2'),
(6, 'KOSONG', 3, 4, '62cd1682470e6'),
(6, 'KOSONG', 3, 4, '62cd168248412'),
(6, 'KOSONG', 3, 4, '62cd168249604'),
(6, 'KOSONG', 3, 4, '62cd16824a782'),
(6, 'KOSONG', 3, 4, '62cd16824beab'),
(6, 'KOSONG', 3, 4, '62cd16824d138'),
(6, 'TERISI', 3, 4, '62cd16824e3eb'),
(6, 'KOSONG', 3, 4, '62cd16824f58e'),
(6, 'KOSONG', 3, 4, '62cd16825076b'),
(6, 'KOSONG', 3, 4, '62cd168251990'),
(8, 'KOSONG', 3, 4, '62cd1a914e759'),
(8, 'KOSONG', 3, 4, '62cd1a9151182'),
(8, 'KOSONG', 3, 4, '62cd1a9154027'),
(8, 'KOSONG', 3, 4, '62cd1a91552d5'),
(8, 'KOSONG', 3, 4, '62cd1a9156587'),
(8, 'KOSONG', 3, 4, '62cd1a91577ec'),
(8, 'KOSONG', 3, 4, '62cd1a9158a59'),
(8, 'KOSONG', 3, 4, '62cd1a9159d7c'),
(8, 'KOSONG', 3, 4, '62cd1a915b0c5'),
(8, 'KOSONG', 3, 4, '62cd1a915c3c3'),
(8, 'KOSONG', 3, 4, '62cd1a915fa99'),
(8, 'KOSONG', 3, 4, '62cd1a9160d04'),
(8, 'KOSONG', 3, 4, '62cd1a9161f82'),
(8, 'KOSONG', 3, 4, '62cd1a9163228'),
(8, 'KOSONG', 3, 4, '62cd1a916443a'),
(8, 'TERISI', 3, 4, '62cd1a91656f8'),
(8, 'KOSONG', 3, 4, '62cd1a9166b68'),
(8, 'KOSONG', 3, 4, '62cd1a9167f91'),
(8, 'KOSONG', 3, 4, '62cd1a916945b'),
(8, 'KOSONG', 3, 4, '62cd1a916a705'),
(8, 'KOSONG', 3, 4, '62cd1a916ba1e'),
(9, 'KOSONG', 3, 4, '62cd7a47dd4dc'),
(9, 'KOSONG', 3, 4, '62cd7a47deab1'),
(9, 'KOSONG', 3, 4, '62cd7a47e0066'),
(9, 'KOSONG', 3, 4, '62cd7a47e154d'),
(9, 'KOSONG', 3, 4, '62cd7a47e2b34'),
(9, 'KOSONG', 3, 4, '62cd7a47e3ffa'),
(9, 'KOSONG', 3, 4, '62cd7a47e541d'),
(9, 'KOSONG', 3, 4, '62cd7a47e6831'),
(9, 'KOSONG', 3, 4, '62cd7a47e7c8e'),
(9, 'KOSONG', 3, 4, '62cd7a47e9091'),
(9, 'KOSONG', 3, 4, '62cd7a47ea5e2'),
(9, 'KOSONG', 3, 4, '62cd7a47eba23'),
(9, 'KOSONG', 3, 4, '62cd7a47ef4ec'),
(9, 'KOSONG', 3, 4, '62cd7a47f09f5'),
(9, 'KOSONG', 3, 4, '62cd7a47f1f21'),
(9, 'KOSONG', 3, 4, '62cd7a47f348c'),
(9, 'KOSONG', 3, 4, '62cd7a48007cc'),
(9, 'KOSONG', 3, 4, '62cd7a4801d9d'),
(9, 'KOSONG', 3, 4, '62cd7a480336e'),
(9, 'KOSONG', 3, 4, '62cd7a4804864'),
(10, 'KOSONG', 3, 4, '62cd7ad905871'),
(10, 'KOSONG', 3, 4, '62cd7ad906e29'),
(10, 'KOSONG', 3, 4, '62cd7ad9082b1'),
(10, 'KOSONG', 3, 4, '62cd7ad9096ef'),
(10, 'KOSONG', 3, 4, '62cd7ad90cc99'),
(10, 'KOSONG', 3, 4, '62cd7ad90ded3'),
(10, 'KOSONG', 3, 4, '62cd7ad90f091'),
(10, 'KOSONG', 3, 4, '62cd7ad910315'),
(10, 'KOSONG', 3, 4, '62cd7ad9114ba'),
(10, 'KOSONG', 3, 4, '62cd7ad912771'),
(10, 'KOSONG', 3, 4, '62cd7ad91399a'),
(10, 'KOSONG', 3, 4, '62cd7ad914bd1');

-- --------------------------------------------------------

--
-- Table structure for table `kost`
--

CREATE TABLE `kost` (
  `id` int(6) NOT NULL,
  `alamat` varchar(120) NOT NULL,
  `nama` varchar(60) DEFAULT NULL,
  `jumlahKamar` int(3) DEFAULT 0,
  `NIK_Pemilik` char(16) DEFAULT NULL,
  `harga` int(7) NOT NULL,
  `jenis` enum('Putra','Putri','Campur') DEFAULT NULL,
  `gambar_preview` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `kost`
--

INSERT INTO `kost` (`id`, `alamat`, `nama`, `jumlahKamar`, `NIK_Pemilik`, `harga`, `jenis`, `gambar_preview`) VALUES
(1, 'Jl. Raya Tajem Gg Manduro No.km 6', 'Kost Ali', 35, NULL, 950000, 'Putra', NULL),
(2, 'Jl. Kemuning Salam No.52, Sanggrahan, Condongcatur, Kec. Depok', 'Kos Putra Nusantara', 25, NULL, 730000, 'Putra', NULL),
(3, ' Jl. Raya Manukan, Mladangan', 'Kost Mawar', 21, '1234890723456789', 430000, 'Putri', '62c833dc576a1.jpg'),
(4, 'Jl. kaliurang, Krawitan, Umbulmartani, Ngemplak', 'bebas', 10, '12378678362736', 700000, 'Putra', '62c978ced7192.jpg'),
(5, 'Cost Putra Sadewa Pugeran, Maguwoharjo, Depok, Sleman', 'Kost Sadewa', 20, '8907134586971234', 560000, 'Putra', '62cb6b0bcd1c1.jpg'),
(6, 'dekost', 'dekost', 21, '1234523476123467', 234000, 'Putra', '62cd1682354d4.jpg'),
(8, 'Jl.Kaliurang Km 13.5', 'Elhaus', 21, '1234890723456789', 234000, 'Putra', '62cd1a914b59b.jpg'),
(9, 'dsadsa', 'dads', 20, '8955577332255771', 450000, 'Putra', '62cd7a47db5b4.jpg'),
(10, 'Jalan Gejayan, Km 12', 'Dara kost', 12, '1234890723456789', 467000, 'Putri', '62cd7ad901636.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `pemilik`
--

CREATE TABLE `pemilik` (
  `NIK` char(16) NOT NULL,
  `nama` varchar(120) DEFAULT NULL,
  `noTelp` varchar(15) DEFAULT NULL,
  `alamat` varchar(255) NOT NULL,
  `email` varchar(60) NOT NULL,
  `keypassword` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pemilik`
--

INSERT INTO `pemilik` (`NIK`, `nama`, `noTelp`, `alamat`, `email`, `keypassword`) VALUES
('1234096789123456', 'test test', '123456789123', 'test ', 'test@gmail.com', '$2y$10$ym51t82wfJFxA79UYaK4c.RRONg1VVPuNU9Y6i0U9iv8F/tt9zoH6'),
('1234123412341234', 'corn wall', '081234561234', 'jl. kaliurang km 12.5', 'corn@gmail.com', '$2y$10$kBiuyx229Si5Ecx23wXqweF4KrxXjHlBr2YIAIs2J1fTokRungcPu'),
('1234523476123467', 'halo bandung', '123498567123', 'jl bandung', 'bandung@gmail.com', '$2y$10$dBrFJYFigbGU2k5R/O0OruPynUBAP.cQ1sB3BTKw/AATAmxgYQ/9u'),
('1234716432199011', 'ghost rider', '122344661177', 'Texas, South America', 'ghost@gmail.com', '$2y$10$GmHOdaH6eOcVvwJ3WCisqOQIL.kgltwYT9YLmMkaBp6EZp4iVzgsi'),
('1234890723456789', 'dara zara', '091234567890', 'Tanggerang Selatan', 'dara@gmail.com', '$2y$10$WlOtfRF8zfHkEsVGanoP8eFDPCnnfEzQ7AfUNd3W1fRlrcQHRsANq'),
('1235908712345678', 'corn wall', '081234567895', 'corn field', 'corn@gmail.com', '$2y$10$gZ1WNbAeRNJSxYdMaBTNFurNAZYBQONLEwKHKkYxSNwPJ3NYN7pH.'),
('12378678362736', 'budi cek', '081227875674', 'kaliurang', 'cek@gmail.com', '$2y$10$HNqdJ1ZMRFsSQSQwUgY0/OxHU.FCcg8mmF6cmqlRJOL94mcfK1KgS'),
('8907134586971234', 'ilham Rizqyakbar', '081323465789', 'Sanggrahan, Sleman, Yogyakarta', 'ilham@gmail.com', '$2y$10$a4D5EWpUzq7kyXp8GGvtfO.PjZcFqAzwamZdUOC0P7FAQN0/agwrG'),
('8909186957867890', 'dalas nasyar', '081234567890', 'Pogung, Yogyakarta', 'dalas@gmail.com', '$2y$10$jbo2q/pff0qsf7OY2mWLYufUdEx5WxQbxQOGlF7EWLJ.SOr827msi'),
('8955577332255771', 'dekost dekost', '095783461789', 'Jalan Magelang, Yogyakarta', 'dekost@gmail.com', '$2y$10$5HttCp/UmEx16yCMLOETLOU1cQ4f8wmAOM2wKcpxe0H43R3sTlvVe'),
('9856784358681234', 'ilham  Rizqyakbar', '081989096789', 'Sanggrahan, Sleman, Kalasan', 'ilham@gmail.com', '$2y$10$662vGTft3cThKvmWsXdowOH3PGAUhRJtkjsqNscZwcyrPpK6drWmC');

-- --------------------------------------------------------

--
-- Table structure for table `penyewaan`
--

CREATE TABLE `penyewaan` (
  `id` int(6) NOT NULL,
  `NIK_penyewa` char(16) DEFAULT NULL,
  `tannggal_mulai` date NOT NULL,
  `tanggal_akhir` date NOT NULL,
  `idKamar` varchar(15) DEFAULT NULL,
  `idKost` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `penyewaan`
--

INSERT INTO `penyewaan` (`id`, `NIK_penyewa`, `tannggal_mulai`, `tanggal_akhir`, `idKamar`, `idKost`) VALUES
(2, '1989403989087564', '2022-07-11', '2023-07-11', '62cb6b0bcfb3b', 5),
(3, '1989403989087564', '2022-07-11', '2023-07-11', '62cb6bb98e245', 3),
(4, '1989403989087564', '2022-07-26', '2022-10-25', '62cb6bb993545', 3),
(5, '1989403989087564', '2022-07-11', '2023-07-11', '62cb6b0bd595f', NULL),
(6, '1989403989087564', '2022-07-11', '2023-07-11', '62cb6b0bd84c0', 3),
(7, '1989403989087564', '2022-07-11', '2023-07-11', '62cb6b0bce83e', 3),
(8, '1989403989087564', '2022-07-26', '2022-10-25', '62cb6bb99e105', 3),
(9, '1989403989087564', '2022-07-26', '2022-10-25', '62cb6b0be4ea0', 3),
(10, '1989403989087564', '2022-07-11', '2023-07-11', '62cb6b0be2a5b', 3),
(11, '1989403989087564', '2022-07-25', '2023-07-25', '62cd1a91656f8', 3),
(12, '1989403989087564', '2022-07-26', '2022-10-25', '62cb6bb9868e2', 3),
(13, '1989403989087564', '2022-07-01', '2023-07-01', '62cd16824e3eb', 3);

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `idPesanan` varchar(13) NOT NULL,
  `idPemesan` varchar(16) DEFAULT NULL,
  `idKost` int(6) DEFAULT NULL,
  `tglPemesanan` date DEFAULT NULL,
  `mulaiSewa` date DEFAULT NULL,
  `akhirSewa` date DEFAULT NULL,
  `totalPembayaran` bigint(12) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`idPesanan`, `idPemesan`, `idKost`, `tglPemesanan`, `mulaiSewa`, `akhirSewa`, `totalPembayaran`) VALUES
('62cba2a107af6', '1989403989087564', 5, '2022-07-11', '2022-07-11', '2023-01-09', 3360000),
('62cbbb718761f', '1989403989087564', 4, '2022-07-11', '2022-07-18', '2024-07-17', 16800000);

-- --------------------------------------------------------

--
-- Table structure for table `rekening`
--

CREATE TABLE `rekening` (
  `NoRekening` varchar(20) NOT NULL,
  `bank` varchar(50) DEFAULT NULL,
  `NIK_Pemilik` varchar(16) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `rekening`
--

INSERT INTO `rekening` (`NoRekening`, `bank`, `NIK_Pemilik`) VALUES
('2345095482', 'BCA', '1234890723456789'),
('345691096905', 'Bank Central Asia', '8955577332255771');

-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE `review` (
  `id` int(6) NOT NULL,
  `komen` varchar(512) DEFAULT '',
  `nilai` float NOT NULL,
  `tanggal` datetime NOT NULL,
  `NIK_Penyewa` char(16) DEFAULT NULL,
  `id_kost` int(6) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `NIK` char(16) NOT NULL,
  `firstName` varchar(60) DEFAULT NULL,
  `lastName` varchar(60) DEFAULT NULL,
  `email` varchar(60) NOT NULL,
  `jenisKelamin` char(1) DEFAULT NULL,
  `keypassword` varchar(64) NOT NULL,
  `no_telepon` varchar(13) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`NIK`, `firstName`, `lastName`, `email`, `jenisKelamin`, `keypassword`, `no_telepon`) VALUES
('1111111111111111', 'Dekost', 'Admin', 'dekost@gmail.com', 'L', '$2y$10$O40p37QwaOKMrgNawOorou2HuAi5XO2zyqHA9xsaPUL3OXPwBbgiW', '222222222222'),
('1989403989087564', 'Lala', 'Poo', 'lala@gmail.com', 'P', '$2y$10$k6cR1REVbQ3ORdL7GvHIQO7PSND244r0rIAP99r/7HeVhOQWCn1dK', '81245630989'),
('2299008844771657', 'Ilham', 'Rizqyakbar', 'irizqy@gmail.com', 'L', '$2y$10$GK5hXNK.lB2jjv3LD1KhgOk2nug5uC90UKKb/l7ughfEPSA9SV.Ze', '81328903457'),
('4481984578123478', 'Novid', 'Romadhoni', 'novid@gmail.com', 'L', '$2y$10$2C1pxr6qfoK2K/h82e9/de1c19G6Qsgds8VsuL4X4iXV6LXMejLIy', '81189045678'),
('5599001123457891', 'Intan', 'Nabila', 'intan@gmail.com', 'P', '$2y$10$Cw9QB9BtzFCyQ3vb4M5XMeCepcPGYpcIOYcKHLnGmQgO3iY9fbRse', '89790678954'),
('7788332211778844', 'Fajrun', 'Shubhi', 'fajrun@gmail.com', 'L', '$2y$10$W57WufwHjgkgoIcbggeRmO0oUsGKSGUWwUR8zEty1KwZNPoVaESEe', '82378567834'),
('9044852369875896', 'Ajrun', 'Hasan', 'ajrun@gmail.com', 'L', '$2y$10$Tlbrk2LjtmMdLjk0/XwadOSs62YB4NNAWlBSnHhg96yjbgxojA6KW', '89712345678');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `fasilitas`
--
ALTER TABLE `fasilitas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `fasil_kost`
--
ALTER TABLE `fasil_kost`
  ADD PRIMARY KEY (`id_kost`,`id_fasil`),
  ADD KEY `id_fasil` (`id_fasil`);

--
-- Indexes for table `kamar`
--
ALTER TABLE `kamar`
  ADD PRIMARY KEY (`idKamar`),
  ADD KEY `fk_id_kamar` (`id_kost`);

--
-- Indexes for table `kost`
--
ALTER TABLE `kost`
  ADD PRIMARY KEY (`id`),
  ADD KEY `NIK_Pemilik` (`NIK_Pemilik`);

--
-- Indexes for table `pemilik`
--
ALTER TABLE `pemilik`
  ADD PRIMARY KEY (`NIK`);

--
-- Indexes for table `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `NIK_penyewa` (`NIK_penyewa`),
  ADD KEY `penyewaan` (`idKamar`),
  ADD KEY `fk_penyewaan_kost` (`idKost`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`idPesanan`),
  ADD KEY `idPemesan` (`idPemesan`),
  ADD KEY `fkKostId` (`idKost`);

--
-- Indexes for table `rekening`
--
ALTER TABLE `rekening`
  ADD PRIMARY KEY (`NoRekening`),
  ADD KEY `NIK_Pemilik` (`NIK_Pemilik`);

--
-- Indexes for table `review`
--
ALTER TABLE `review`
  ADD PRIMARY KEY (`id`),
  ADD KEY `NIK_Penyewa` (`NIK_Penyewa`),
  ADD KEY `id_kost` (`id_kost`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`NIK`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `kost`
--
ALTER TABLE `kost`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `penyewaan`
--
ALTER TABLE `penyewaan`
  MODIFY `id` int(6) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `fasil_kost`
--
ALTER TABLE `fasil_kost`
  ADD CONSTRAINT `fasil_kost_ibfk_1` FOREIGN KEY (`id_kost`) REFERENCES `kost` (`id`),
  ADD CONSTRAINT `fasil_kost_ibfk_2` FOREIGN KEY (`id_fasil`) REFERENCES `fasilitas` (`id`);

--
-- Constraints for table `kamar`
--
ALTER TABLE `kamar`
  ADD CONSTRAINT `fk_id_kamar` FOREIGN KEY (`id_kost`) REFERENCES `kost` (`id`);

--
-- Constraints for table `kost`
--
ALTER TABLE `kost`
  ADD CONSTRAINT `Kost_ibfk_1` FOREIGN KEY (`NIK_Pemilik`) REFERENCES `pemilik` (`NIK`);

--
-- Constraints for table `penyewaan`
--
ALTER TABLE `penyewaan`
  ADD CONSTRAINT `Penyewaan_ibfk_1` FOREIGN KEY (`NIK_penyewa`) REFERENCES `users` (`NIK`),
  ADD CONSTRAINT `fk_penyewaan_kost` FOREIGN KEY (`idKost`) REFERENCES `kost` (`id`),
  ADD CONSTRAINT `penyewaan` FOREIGN KEY (`idKamar`) REFERENCES `kamar` (`idKamar`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `Pesanan_ibfk_1` FOREIGN KEY (`idPemesan`) REFERENCES `users` (`NIK`),
  ADD CONSTRAINT `fkKostId` FOREIGN KEY (`idKost`) REFERENCES `kost` (`id`);

--
-- Constraints for table `rekening`
--
ALTER TABLE `rekening`
  ADD CONSTRAINT `Rekening_ibfk_1` FOREIGN KEY (`NIK_Pemilik`) REFERENCES `pemilik` (`NIK`);

--
-- Constraints for table `review`
--
ALTER TABLE `review`
  ADD CONSTRAINT `Review_ibfk_1` FOREIGN KEY (`NIK_Penyewa`) REFERENCES `users` (`NIK`),
  ADD CONSTRAINT `Review_ibfk_2` FOREIGN KEY (`id_kost`) REFERENCES `kost` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
