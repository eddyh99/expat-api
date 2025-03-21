-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Mar 21, 2025 at 10:58 AM
-- Server version: 10.11.10-MariaDB
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u890496726_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `assigncabang`
--

CREATE TABLE `assigncabang` (
  `member_id` int(11) NOT NULL,
  `cabang_id` int(11) NOT NULL,
  `is_deleted` enum('yes','no') NOT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cabang`
--

CREATE TABLE `cabang` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `alamat` tinytext NOT NULL,
  `provinsi` varchar(100) NOT NULL,
  `opening` varchar(255) NOT NULL,
  `kontak` varchar(50) NOT NULL,
  `latitude` varchar(50) DEFAULT NULL,
  `longitude` varchar(50) NOT NULL,
  `picture` varchar(100) NOT NULL,
  `is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `cabang`
--

INSERT INTO `cabang` (`id`, `nama`, `alamat`, `provinsi`, `opening`, `kontak`, `latitude`, `longitude`, `picture`, `is_deleted`, `created_at`, `update_at`) VALUES
(1, 'EXPAT. ROASTERS PETITENGET', 'Petitenget St No.1a, Kerobokan Kelod, Kuta Utara, Badung Regency, Bali 80361', 'Bali', 'Monday to Sunday, 7 AM - 7 PM', '+6281246140493', '-8.672028559838537', '115.1606611423292', 'expats_1721795758.png', 'no', '2024-07-24 12:35:58', '2024-08-05 10:27:51'),
(2, 'EXPAT. ROASTERS BEACHWALK', 'Beachwalk Shopping Mall Level 3, Jl. Pantai Kuta No.1, Kuta, Kabupaten Badung, Bali 80361, Indonesia', 'Bali', 'Monday to Sunday, 11am-10pm', '+6281238898406', '-8.716588', '115.169629', 'expats_1722824145.png', 'no', '2024-08-05 10:15:45', '2024-08-05 10:24:46'),
(3, 'EXPAT. ROASTERS WEST SURABAYA', 'Soho Graha Famili PS. 15, Jl. Raya Graha Famili Timur, Surabaya, Jawa Timur 60225', 'Surabaya', 'Monday to Sunday, 6am-9pm', '+6282131618995', '-7.299285', '112.697459', 'expats_1722824669.png', 'no', '2024-08-05 10:24:29', '2024-08-05 10:24:58'),
(4, 'EXPAT. ROASTERS JAKARTA', 'Jakarta Mori Tower, 13th Floor, Jl. Jenderal Sudirman Jakarta Pusat, Jakarta 10210', 'Jakarta', 'Monday to Sunday, 7am-7pm', '+6281138306012', '-6.216306', '106.815322', 'expats_1722824820.png', 'no', '2024-08-05 10:27:00', NULL),
(5, 'test', '1234', 'Banda Aceh', 'test', '111', '41.40338', '2.17403', 'expats_1728568634.png', 'yes', '2024-10-10 21:57:14', NULL),
(6, 'EXPAT. ROASTERS JUANDA INTERNATIONAL AIRPORT', 'Lobby Drop Zone 1A, Terminal 1, Juanda International Airport, Jalan Raya Ir. H.Juanda, Segoro Tambak, T1A, Sidoarjo Regency, East Java 61253', 'Surabaya', 'Monday - Sunday, 5AM - 8PM', '089676327787', '-7.3752169', '112.7974731', 'expats_1731551850.jpeg', 'no', '2024-11-14 10:37:30', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `history_topup`
--

CREATE TABLE `history_topup` (
  `id` int(11) NOT NULL,
  `id_member` int(11) NOT NULL,
  `invoice` varchar(30) NOT NULL,
  `tanggal` datetime NOT NULL,
  `nominal` int(11) NOT NULL,
  `poin` int(11) NOT NULL DEFAULT 0,
  `status` enum('pending','success') NOT NULL DEFAULT 'pending',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `history_topup`
--

INSERT INTO `history_topup` (`id`, `id_member`, `invoice`, `tanggal`, `nominal`, `poin`, `status`, `created_at`, `update_at`) VALUES
(1, 4, '2024072466a090fee82f8', '2024-07-24 13:28:31', 300000, 30, 'pending', '2024-07-24 13:28:31', NULL),
(2, 5, '2024081466bc2ae01746d', '2024-08-14 11:56:16', 200000, 20, 'pending', '2024-08-14 11:56:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `member`
--

CREATE TABLE `member` (
  `id` int(11) NOT NULL,
  `memberid` varchar(20) NOT NULL,
  `picture` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `passwd` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `country` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `dob` date DEFAULT NULL,
  `gender` enum('male','female') NOT NULL,
  `token` varchar(4) DEFAULT NULL,
  `request_time` datetime NOT NULL,
  `pin` varchar(50) NOT NULL,
  `status` enum('new','active','disabled') NOT NULL DEFAULT 'new',
  `role` enum('member','wholesale','pegawai') NOT NULL DEFAULT 'member',
  `is_driver` enum('yes','no') NOT NULL DEFAULT 'no' COMMENT 'untuk karyawan',
  `is_google` enum('yes','no') NOT NULL DEFAULT 'no',
  `plafon` int(11) NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `member`
--

INSERT INTO `member` (`id`, `memberid`, `picture`, `email`, `passwd`, `nama`, `country`, `phone`, `dob`, `gender`, `token`, `request_time`, `pin`, `status`, `role`, `is_driver`, `is_google`, `plafon`, `created_at`, `update_at`) VALUES
(3, '66a07e29cddd1', NULL, 'expatgoogle@gmail.com', '136c57a86be45051a8e52b496fc77ee9ee5ca4c3', '', '', NULL, NULL, 'male', NULL, '2024-07-24 12:08:09', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'active', 'member', 'no', 'no', 0, '2024-07-24 12:08:09', NULL),
(4, '66a07ff787609', NULL, 'yanari0797@gmail.com', '40bd001563085fc35165329ea1ff5c5ecbdbbeef', '', '', NULL, NULL, 'male', '9067', '2024-10-10 17:54:47', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'active', 'member', 'no', '', 0, '2024-07-24 12:15:51', NULL),
(5, '66b01df8abf79', NULL, 'rifat@expatroasters.com', '4f3ce27b2616b8c6c0c5c622ea89a88457e45c0e', 'Rifat Al Kausar', 'Indonesia', '6285171531076', '1999-07-31', 'male', '5409', '2024-08-14 11:55:12', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'active', 'member', 'no', '', 0, '2024-08-05 08:34:00', '2024-08-05 10:53:21'),
(6, '66b0900491fdd', NULL, 'test@expatroasters.com', '2d794cbeaf9f06f075899273cbb12b3d58e7e5f8', 'test', '', NULL, NULL, 'male', NULL, '0000-00-00 00:00:00', '', 'active', 'pegawai', 'yes', '', 1500000, '2024-08-05 16:40:36', '2024-08-05 17:39:56'),
(7, '670797fd2e13a', NULL, 'aripramana574@gmail.com', '947a2f61a43a739c5e1321f38741fe1b3b177da5', '', '', NULL, NULL, 'male', NULL, '2024-10-10 17:01:49', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'active', 'member', 'no', 'yes', 0, '2024-10-10 17:01:49', NULL),
(8, '671ef08c3cfde', NULL, 'goodprasetyaadi@gmail.com', 'abbc6ddc5c0222df566fa834cfd132fee3fd9d51', 'Bagus', 'Indonesia', '082145215884in', '1992-08-10', 'male', NULL, '2024-10-28 10:01:48', '87e64b9e0473c6b9147a76675beb9d06e43805b6', 'active', 'member', 'no', 'no', 0, '2024-10-28 10:01:48', '2024-10-28 10:03:32'),
(9, '671f55c15540d', NULL, 'andrew0293@gmail.com', 'f820858b3d71075d4a5636c835aa66f095219f60', '', '', NULL, NULL, 'male', NULL, '2024-10-28 17:13:37', '7ca6168bdb9f1969d31aa4a13bbc51f15359c931', 'active', 'member', 'no', 'no', 0, '2024-10-28 17:13:37', NULL),
(10, '672c2df836403', 'user_1730948736.jpg', 'rifatalkausar@gmail.com', 'f5aa8ade513b730c32ce2eafb06b7e83277d61ea', 'Rifat Al Kausar', 'Indonesia', '6285171531076', '1999-07-31', 'male', NULL, '2024-11-07 11:03:20', '0d7d81853c2290b44ea9b896d5f18bb89c71acc9', 'active', 'member', 'no', 'no', 0, '2024-11-07 11:03:20', '2024-11-07 11:05:36'),
(11, '66a07e29cddd2', NULL, 'expattesting@gmail.com', '1e120fff23aedaf23ab14fd14e8f2d031593c222', '', '', NULL, NULL, 'male', NULL, '2024-07-24 12:08:09', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'active', 'member', 'no', 'no', 0, '2024-07-24 12:08:09', NULL),
(12, '673597791607a', NULL, 'Christian@expatroasters.com', 'bfba1f3fdf9d61a78aebee6c13fadf94ecce0359', '', '', NULL, NULL, 'male', '1418', '2024-11-18 13:43:00', '612aad94f22730eec3dce1001463580c6588cbaa', 'active', 'member', 'no', 'no', 0, '2024-11-14 14:23:53', NULL),
(16, '673ad3a56324c', NULL, 'christiansantosodeny@gmail.com', 'bfba1f3fdf9d61a78aebee6c13fadf94ecce0359', 'Christian Santoso', 'Indonesia', '081717100088', '1993-12-19', 'male', '9828', '2024-11-18 14:36:19', '612aad94f22730eec3dce1001463580c6588cbaa', 'active', 'member', 'no', 'no', 0, '2024-11-18 13:41:57', '2024-11-18 14:38:39'),
(20, '673ae21896bae', NULL, 'mrsalasanto@gmail.com', '3bbc0b1b76e53b4efe759bb56287c3b288ddfbb4', 'Peter', '', '', '0000-00-00', 'male', NULL, '2024-11-18 14:43:36', '294a4c5be80a3070f1f333fac4e7c037d192ea72', 'active', 'member', 'no', 'no', 0, '2024-11-18 14:43:36', '2024-11-18 14:44:27'),
(21, '674644156a90e', NULL, 'dkdkjbp@gmail.com', '28f6c8ae6c786dfc1913755f58b902d24d347e81', 'Djunaedi Bhakti Pramono ', 'Indonesia', '085854865448', '1988-12-29', 'male', NULL, '2024-11-27 05:56:37', '9dd70124cad86645d983b5cf9b507a468a1e49e3', 'active', 'member', 'no', 'no', 0, '2024-11-27 05:56:37', '2024-11-27 05:59:27'),
(22, '679c6cee4259f', NULL, 'krishnamahendra1234@gmail.com', 'aebd375482d0dea4c67400a83642935ecf917f28', 'Krishna', '', '', '0000-00-00', 'male', NULL, '2025-01-31 14:25:50', 'c933318f11743786e892c10c889a5b3d5c9f549f', 'active', 'member', 'no', 'no', 0, '2025-01-31 14:25:50', '2025-01-31 14:27:20'),
(25, '67c7f3c91ffda', NULL, 'catchonme.riko@gmail.com', '9714738ec50502fa36574fa0ab1f6fb9e5670df9', '', '', NULL, NULL, 'male', '9814', '2025-03-05 14:48:41', '', 'new', 'member', 'no', 'no', 0, '2025-03-05 14:48:41', NULL),
(26, '67cb95e90d041', NULL, 'suksesfajarboga@gmail.com', 'b51bbc50fc873d21f2e944054a27c1d4f8547c2f', '', '', NULL, NULL, 'male', '1623', '2025-03-08 08:57:13', '', 'new', 'member', 'no', 'no', 0, '2025-03-08 08:57:13', NULL),
(27, '67cfb403b2c99', NULL, 'wedar56784@kaiav.com', 'd04cd7781bedffadd930654b54c207350a39d825', '', '', NULL, NULL, 'male', NULL, '2025-03-11 11:54:43', '3d4f2bf07dc1be38b20cd6e46949a1071f9d0e3d', 'active', 'member', 'no', 'no', 0, '2025-03-11 11:54:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `membercard`
--

CREATE TABLE `membercard` (
  `jenis` enum('bronze','silver','gold','platinum') NOT NULL,
  `benefit` tinytext NOT NULL,
  `minimum` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `pengguna`
--

CREATE TABLE `pengguna` (
  `username` varchar(10) NOT NULL,
  `passwd` varchar(100) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `role` enum('admin','kasir','hr','marketing','finance') NOT NULL,
  `is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `pengguna`
--

INSERT INTO `pengguna` (`username`, `passwd`, `nama`, `role`, `is_deleted`, `created_at`, `update_at`) VALUES
('admin', 'f865b53623b121fd34ee5426c792e5c33af8c227', 'admin ganteng', 'admin', 'no', NULL, '2024-06-18 14:52:20'),
('people', '2d794cbeaf9f06f075899273cbb12b3d58e7e5f8', 'people', 'hr', 'no', '2024-08-05 16:39:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `pengiriman`
--

CREATE TABLE `pengiriman` (
  `id` int(11) NOT NULL,
  `id_member` int(11) NOT NULL,
  `title` varchar(50) NOT NULL,
  `alamat` text NOT NULL,
  `phone` varchar(20) NOT NULL,
  `latitude` varchar(50) NOT NULL,
  `longitude` varchar(50) NOT NULL,
  `is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `is_primary` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(255) NOT NULL,
  `picture` varchar(100) NOT NULL,
  `deskripsi` text NOT NULL,
  `sku` varchar(30) NOT NULL,
  `price` int(11) NOT NULL,
  `kategori` enum('food','drink','retail') NOT NULL,
  `subkategori` varchar(50) DEFAULT NULL,
  `favorite` enum('yes','no') NOT NULL DEFAULT 'no',
  `satuan` varchar(255) DEFAULT NULL,
  `additional` varchar(255) DEFAULT NULL,
  `optional` varchar(255) DEFAULT NULL,
  `cabang` varchar(255) NOT NULL,
  `is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `picture`, `deskripsi`, `sku`, `price`, `kategori`, `subkategori`, `favorite`, `satuan`, `additional`, `optional`, `cabang`, `is_deleted`, `created_at`, `update_at`) VALUES
(1, 'Cappuccino', 'produk_1722820137.png', 'Espresso shot with steamed milk and medium foam', 'RF_CFE_01-', 38000, 'drink', 'Coffe Drinks', 'yes', '1,2', NULL, '1,2,3,4,5,7,8', '1,2,3,4', 'no', '2024-08-05 09:08:57', '2024-10-11 09:33:44'),
(2, 'Long Black', 'produk_1723089256.jpeg', 'Double hot of espresso with hot water', 'adfsadadf', 40000, 'drink', NULL, 'yes', '1,2', NULL, '1,2,3,4,5,9,10,11,12,13,14,7,8', '1,2,3,4', 'no', '2024-08-08 11:54:16', NULL),
(3, 'Espresso', 'produk_1723089473.png', 'Single shot of coffee / extraction method', 'RF_CFE_02-', 38200, 'drink', 'Coffe Drinks', 'yes', '1', NULL, NULL, '1,2,3,4', 'no', '2024-08-08 11:57:53', '2024-10-11 09:36:21'),
(4, 'B.O.E', 'produk_1723423942.jpeg', 'Butterscotch Oat Espresso', 'RF_CFE_04-', 60000, 'drink', 'Coffe Drinks', 'no', '2', NULL, NULL, '1,2,3,4', 'no', '2024-08-12 08:52:22', '2024-11-07 13:52:48'),
(5, 'Magic Latte', 'produk_1723424451.jpeg', 'Double shots of espresso with steamed milk', 'RF_CFE_01-', 45000, 'drink', NULL, 'yes', '3', NULL, '1,2,3,4,5,9,10,12,13', '1,2,3,4', 'no', '2024-08-12 09:00:51', NULL),
(6, 'Chocolate', 'produk_1723424717.jpeg', 'Premix chocolate liquid with steamed milk', 'RF_NCF_09-', 38000, 'drink', 'Non Coffee', 'yes', '1,2', NULL, '7,8', '1,2,3,4', 'no', '2024-08-12 09:05:17', '2024-11-07 13:54:36'),
(7, 'Cold Brew Can White', 'produk_1723424927.jpeg', 'Expat. Roasters ready to drink cold brew white coffee using habitat blend', 'RF_CFE_06-', 35000, 'drink', NULL, 'yes', '4', NULL, NULL, '1,2,3,4', 'no', '2024-08-12 09:08:47', '2024-08-12 09:12:16'),
(8, 'Cold Brew Can Butterscotch Oat', 'produk_1723425043.jpeg', 'Expat. Roasters ready to drink cold brew butterscotch oat coffee using nomad blend', 'RF_CFE_06-', 42000, 'drink', NULL, 'yes', '4', NULL, NULL, '1,2,3,4', 'no', '2024-08-12 09:10:43', NULL),
(9, 'Cold Brew Can Black', 'produk_1723425119.jpeg', 'Expat. Roasters ready to drink cold brew black coffee using kintamani natural beans', 'RF_CFE_06-', 32000, 'drink', NULL, 'yes', '4', NULL, NULL, '1,2,3,4', 'no', '2024-08-12 09:11:59', NULL),
(10, 'Machiatto', 'produk_1723425329.jpeg', 'Single shot of coffee with dash of steamed milk, and foam', 'RF_CFE_01-', 35000, 'drink', NULL, 'yes', '1,3', NULL, NULL, '1,2,3,4', 'no', '2024-08-12 09:15:29', NULL),
(11, 'Banana Bread', 'produk_1723425536.jpeg', 'Slice toasted banana bread with espresso butter', 'RF_FDD_05-', 40000, 'food', 'Sweets', 'yes', '6', NULL, NULL, '1,2', 'no', '2024-08-12 09:18:56', '2024-10-11 09:06:22'),
(12, 'Drip bag', 'produk_1723426460.jpeg', 'Expat. Roasters Coffee Drip bag', 'RF_RET_01-', 17500, 'retail', 'Drip Bag', 'yes', '6', NULL, '1,2,3,4,9,10,12,13', '1,2,3,4', 'no', '2024-08-12 09:34:20', '2024-10-11 09:16:19'),
(13, 'Terra Espresso 200gr', 'produk_1728609346.jpeg', '“The foundational ground from which life springs forth”.\r\n\r\nA harmonious combination of Brazilian and Balinese coffee beans, this exceptional medium blend displays  a dark chocolate upfront, sweet peanut, bright pomelo\r\nacidity, velvety texture and fermented cherry aftertaste. This blend is crafted to celebrate the unique characteristics of the land, delivering a balanced and delightful coffee experience.\r\n\r\n?Tasting Notes : Dark chocolate upfront, sweet peanut, bright pomelo acidity, velvety texture, fermented cherry aftertaste.?Process : Natural, Full-Washed\r\nRoast Profile : Medium\r\nAcidity : Low\r\nBody : Medium to High\r\nSweetness : High\r\nAftertaste : Cherries\r\nSpecialty Coffee\r\nEco-friendly\r\nHigh Quality Product :\r\n- Fresh Roasted\r\n- Food Grade Packaging\r\n\r\nAvailable in beans or ground | Recommendations for brewing methods:\r\n- (Espresso /Pour Over: FINE GROUND)\r\n- (Aero Press/Drip Brewer : MEDIUM GROUND)\r\n- (Cold Drip/Cold Brew/French Press : COARSE GROUND)', 'SKU-TEST1234', 177000, 'retail', 'Tin 200gr', 'yes', '6', NULL, '5', '1,2,3,4,5', 'no', '2024-10-11 09:15:46', NULL),
(14, 'Almond Brownie', 'produk_1728609852.png', 'lorem ipsum', 'SKU-TEST9999', 40000, 'food', 'Sweets', 'yes', '6', NULL, NULL, '1,2,3,4,5', 'no', '2024-10-11 09:24:12', NULL),
(15, 'Kapukcino', 'default.png', 'best dabest', '12378', 80000, 'drink', 'Coffe Drinks', 'yes', '7,8,9', NULL, '21,22,23,24,25', '1,2,3,4,6', 'no', '2025-03-19 10:17:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk_additional`
--

CREATE TABLE `produk_additional` (
  `id` int(11) NOT NULL,
  `additional` varchar(100) NOT NULL,
  `additional_group` varchar(50) NOT NULL,
  `sku` varchar(30) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `is_deleted` enum('no','yes') NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `produk_optional`
--

CREATE TABLE `produk_optional` (
  `id` int(11) NOT NULL,
  `optional` varchar(100) NOT NULL,
  `optiongroup` varchar(50) NOT NULL,
  `sku` varchar(30) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `is_deleted` enum('no','yes') NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `produk_optional`
--

INSERT INTO `produk_optional` (`id`, `optional`, `optiongroup`, `sku`, `price`, `is_deleted`, `created_at`, `update_at`) VALUES
(1, 'Nomad', 'Beans', NULL, 0, 'yes', '2024-07-22 09:05:53', NULL),
(2, 'Patria', 'Beans', NULL, 0, 'yes', '2024-07-22 09:06:06', NULL),
(3, 'Habitat', 'Beans', NULL, 0, 'yes', '2024-07-22 09:06:20', NULL),
(4, 'Kintamani Natural', 'Beans', NULL, 0, 'yes', '2024-07-22 09:06:43', NULL),
(5, 'Terra', 'Beans', NULL, 0, 'yes', '2024-07-22 09:07:02', NULL),
(6, 'Fresh Milk', 'Milk', 'sfdgsdfg', 0, 'yes', '2024-08-05 09:04:07', NULL),
(7, 'Hot', 'Type', 'asdfs', 0, 'yes', '2024-08-05 09:12:49', NULL),
(8, 'Ice', 'Type', 'asdf', 10000, 'yes', '2024-08-05 09:13:20', NULL),
(9, 'Omni-B3', 'Beans', 'adasdf', 0, 'yes', '2024-08-05 09:54:24', NULL),
(10, 'Omni-B4', 'Beans', 'asggq', 0, 'yes', '2024-08-05 09:56:00', NULL),
(11, 'Omni-B4', 'Beans', 'sdgdsfg', 0, 'yes', '2024-08-05 09:57:42', NULL),
(12, 'Omni-SO1', 'Beans', 'adfsadf', 0, 'yes', '2024-08-05 09:58:25', NULL),
(13, 'Omni-SO2', 'Beans', 's64fa6sd', 0, 'yes', '2024-08-05 09:58:57', NULL),
(14, 'Decaf', 'Beans', 'qfqerqgw', 0, 'yes', '2024-08-05 09:59:12', NULL),
(15, 'Regular Milk', 'Milk', 'asdfsadf', 0, 'yes', '2024-08-05 10:00:18', NULL),
(16, 'Oat Milk', 'Milk', 'wergdfs', 0, 'yes', '2024-08-05 10:00:32', NULL),
(17, 'Almond Milk', 'Milk', 'sadfas', 12000, 'yes', '2024-08-05 10:01:29', NULL),
(18, 'Coconut Milk', 'Beans', 'sxfbsda', 12000, 'yes', '2024-08-05 10:02:08', NULL),
(19, 'Soy  Milk', 'Milk', 'sadfsdffe', 12000, 'yes', '2024-08-05 10:02:22', NULL),
(20, 'ssdsds', 'Beans', 'sds', 0, 'yes', '2024-11-07 14:03:30', NULL),
(21, 'Nomad', 'Beans', '001', 0, 'no', '2024-11-14 10:44:10', NULL),
(22, 'Patria', 'Beans', '002', 0, 'no', '2024-11-14 10:44:24', NULL),
(23, 'Habitat', 'Beans', '003', 0, 'no', '2024-11-14 10:44:39', NULL),
(24, 'Bali Kintamani Natural', 'Beans', '004', 0, 'no', '2024-11-14 10:45:00', NULL),
(25, 'Omni-SO1', 'Beans', '005', 0, 'no', '2024-11-14 10:45:41', NULL),
(26, 'Omni-SO2', 'Beans', '006', 0, 'no', '2024-11-14 10:46:00', NULL),
(27, 'Omni-BO3', 'Beans', '007', 0, 'no', '2024-11-14 10:46:43', NULL),
(28, 'Omni-BO4', 'Beans', '008', 0, 'no', '2024-11-14 10:47:05', NULL),
(29, 'Bali Kintamani Honey', 'Beans', '009', 0, 'no', '2024-11-14 10:47:38', NULL),
(30, 'West Java', 'Beans', '010', 0, 'no', '2024-11-14 10:48:03', NULL),
(31, 'Terra', 'Beans', '012', 0, 'yes', '2024-11-14 10:48:22', '2024-11-14 10:48:35'),
(32, 'Terra', 'Beans', '011', 0, 'no', '2024-11-14 10:48:57', NULL),
(36, 'Jozo', 'Beans', '013', 30000, 'no', '2024-11-14 10:51:37', NULL),
(37, 'Decaf', 'Beans', '014', 10000, 'no', '2024-11-14 10:51:53', NULL),
(38, 'Ethiopia', 'Beans', '015', 30000, 'no', '2024-11-14 10:53:25', NULL),
(39, 'Bali Baby', 'Beans', '016', 30000, 'no', '2024-12-25 11:58:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk_satuan`
--

CREATE TABLE `produk_satuan` (
  `id` int(11) NOT NULL,
  `satuan` varchar(100) NOT NULL,
  `groupname` varchar(50) NOT NULL,
  `sku` varchar(30) DEFAULT NULL,
  `price` int(11) NOT NULL,
  `is_deleted` enum('no','yes') NOT NULL DEFAULT 'no',
  `created_at` datetime DEFAULT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `produk_satuan`
--

INSERT INTO `produk_satuan` (`id`, `satuan`, `groupname`, `sku`, `price`, `is_deleted`, `created_at`, `update_at`) VALUES
(1, '6oz', 'Size', 'asdfsae', 0, 'yes', '2024-08-05 09:06:15', NULL),
(2, '12oz', 'Size', 'sadfsd', 10000, 'yes', '2024-08-05 09:06:38', NULL),
(3, '4 Oz', 'Size', 'asdkfs', 0, 'yes', '2024-08-12 08:59:43', NULL),
(4, '230 Ml', 'Size', '0', 0, 'yes', '2024-08-12 09:07:35', NULL),
(6, 'Pcs', 'Size', '1', 0, 'yes', '2024-08-12 09:17:27', NULL),
(7, '4 Oz', 'Size', 'SZ-001', 0, 'no', '2024-11-14 11:08:30', NULL),
(8, '6 Oz', 'Size', 'SZ-02', 0, 'no', '2024-11-14 11:09:10', NULL),
(9, '8 Oz', 'Size', 'SZ 001xx', 0, 'yes', '2025-03-19 10:01:35', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `promosi`
--

CREATE TABLE `promosi` (
  `id` int(11) NOT NULL,
  `picture` varchar(100) DEFAULT NULL,
  `deskripsi` text NOT NULL,
  `tipe` enum('online','instore') NOT NULL DEFAULT 'online',
  `milestone` int(11) NOT NULL DEFAULT 0,
  `minimum` int(11) NOT NULL,
  `discount_type` enum('persen','fixed','free item') NOT NULL DEFAULT 'fixed',
  `potongan` decimal(9,2) NOT NULL,
  `promo_id` int(11) DEFAULT NULL,
  `tanggal` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `is_deleted` enum('yes','no') NOT NULL DEFAULT 'no',
  `created_at` datetime NOT NULL,
  `update_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `promosi`
--

INSERT INTO `promosi` (`id`, `picture`, `deskripsi`, `tipe`, `milestone`, `minimum`, `discount_type`, `potongan`, `promo_id`, `tanggal`, `end_date`, `is_deleted`, `created_at`, `update_at`) VALUES
(1, 'promo_1728616625.jpeg', 'test promosi', 'instore', 0, 10000, 'fixed', 1000.00, NULL, '2024-10-11', '2024-10-31', 'no', '2024-10-11 11:14:59', '2024-10-11 11:17:05'),
(2, 'promo_1728624640.png', 'Promo Oktober Bahagai', 'online', 300, 200000, 'persen', 20000.00, NULL, '2024-10-11', '2024-10-31', 'no', '2024-10-11 13:30:40', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
  `id` int(11) NOT NULL,
  `content` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `settings`
--

INSERT INTO `settings` (`id`, `content`, `value`) VALUES
(1, 'poin_calculate', '10000'),
(2, 'delivery_fee', '15000'),
(3, 'max_area', '20'),
(4, 'Bronze', 'BRONZE  Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.'),
(5, 'Silver', 'Silverqueen Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute i'),
(6, 'Gold', 'Gold for everyone Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis '),
(7, 'Platinum', 'Platinum Member Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis au'),
(8, 'poin_bronze', '150'),
(9, 'poin_silver', '200'),
(10, 'poin_gold', '500'),
(11, 'poin_platinum', '800'),
(12, 'step1_bronze', '0'),
(13, 'step2_bronze', '50'),
(14, 'step3_bronze', '75'),
(16, 'step4_bronze', '150'),
(17, 'step5_bronze', '175'),
(18, 'step6_bronze', '199'),
(19, 'step1_silver', '200'),
(20, 'step2_silver', '250'),
(21, 'step3_silver', '300'),
(22, 'step4_silver', '350'),
(23, 'step5_silver', '400'),
(24, 'step6_silver', '500'),
(25, 'step1_gold', '500'),
(26, 'step2_gold', '550'),
(27, 'step3_gold', '600'),
(28, 'step4_gold', '700'),
(29, 'step5_gold', '750'),
(30, 'step6_gold', '800'),
(31, 'step1_platinum', '800'),
(32, 'step2_platinum', '825'),
(33, 'step3_platinum', '875'),
(34, 'step4_platinum', '950'),
(35, 'step5_platinum', '980'),
(36, 'step6_platinum', '1000'),
(37, 'delivery', 'true');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_transaksi` varchar(15) NOT NULL,
  `id_member` int(11) NOT NULL,
  `id_pengiriman` int(11) DEFAULT NULL,
  `alamat` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `note` varchar(200) DEFAULT NULL,
  `tanggal` datetime NOT NULL,
  `cabang` int(11) NOT NULL,
  `carabayar` enum('expatbalance','credit','virtual','wallet','qris') NOT NULL,
  `poin` int(11) NOT NULL,
  `delivery_fee` int(11) NOT NULL,
  `is_paid` enum('yes','no') NOT NULL DEFAULT 'no',
  `is_proses` enum('pending','yes','delivery','complete') NOT NULL DEFAULT 'pending',
  `promotional` varchar(100) DEFAULT NULL,
  `driver` int(11) DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `update_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `transaksi_detail`
--

CREATE TABLE `transaksi_detail` (
  `id_transaksi` int(11) NOT NULL,
  `tipe` enum('optional','additional','satuan','produk') NOT NULL,
  `sku` varchar(10) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `prd_group` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `harga` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `use_coupon`
--

CREATE TABLE `use_coupon` (
  `idmember` int(11) NOT NULL,
  `coupon` int(11) NOT NULL,
  `redeem` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assigncabang`
--
ALTER TABLE `assigncabang`
  ADD PRIMARY KEY (`member_id`,`cabang_id`),
  ADD KEY `assigncabang_ibfk_1` (`cabang_id`);

--
-- Indexes for table `cabang`
--
ALTER TABLE `cabang`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history_topup`
--
ALTER TABLE `history_topup`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `generateid` (`invoice`),
  ADD KEY `id_member` (`id_member`);

--
-- Indexes for table `member`
--
ALTER TABLE `member`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `memberid` (`memberid`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `membercard`
--
ALTER TABLE `membercard`
  ADD PRIMARY KEY (`jenis`);

--
-- Indexes for table `pengguna`
--
ALTER TABLE `pengguna`
  ADD PRIMARY KEY (`username`);

--
-- Indexes for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_member` (`id_member`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `produk_additional`
--
ALTER TABLE `produk_additional`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `produk_optional`
--
ALTER TABLE `produk_optional`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `produk_satuan`
--
ALTER TABLE `produk_satuan`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sku` (`sku`);

--
-- Indexes for table `promosi`
--
ALTER TABLE `promosi`
  ADD PRIMARY KEY (`id`),
  ADD KEY `promosi_ibfk_1` (`promo_id`);

--
-- Indexes for table `settings`
--
ALTER TABLE `settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `content` (`content`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `id_transaksi` (`id_transaksi`),
  ADD KEY `id_member` (`id_member`),
  ADD KEY `cabang` (`cabang`),
  ADD KEY `transaksi_ibfk_3` (`id_pengiriman`),
  ADD KEY `driver` (`driver`);

--
-- Indexes for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD KEY `id_transaksi` (`id_transaksi`);

--
-- Indexes for table `use_coupon`
--
ALTER TABLE `use_coupon`
  ADD PRIMARY KEY (`idmember`,`coupon`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `cabang`
--
ALTER TABLE `cabang`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `history_topup`
--
ALTER TABLE `history_topup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `member`
--
ALTER TABLE `member`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `pengiriman`
--
ALTER TABLE `pengiriman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `produk_additional`
--
ALTER TABLE `produk_additional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `produk_optional`
--
ALTER TABLE `produk_optional`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT for table `produk_satuan`
--
ALTER TABLE `produk_satuan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `promosi`
--
ALTER TABLE `promosi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `settings`
--
ALTER TABLE `settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=38;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `assigncabang`
--
ALTER TABLE `assigncabang`
  ADD CONSTRAINT `assigncabang_ibfk_1` FOREIGN KEY (`cabang_id`) REFERENCES `cabang` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `assigncabang_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `member` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `history_topup`
--
ALTER TABLE `history_topup`
  ADD CONSTRAINT `history_topup_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE NO ACTION;

--
-- Constraints for table `pengiriman`
--
ALTER TABLE `pengiriman`
  ADD CONSTRAINT `pengiriman_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`);

--
-- Constraints for table `promosi`
--
ALTER TABLE `promosi`
  ADD CONSTRAINT `promosi_ibfk_1` FOREIGN KEY (`promo_id`) REFERENCES `produk` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD CONSTRAINT `transaksi_ibfk_1` FOREIGN KEY (`id_member`) REFERENCES `member` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_2` FOREIGN KEY (`cabang`) REFERENCES `cabang` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_3` FOREIGN KEY (`id_pengiriman`) REFERENCES `pengiriman` (`id`),
  ADD CONSTRAINT `transaksi_ibfk_4` FOREIGN KEY (`driver`) REFERENCES `member` (`id`);

--
-- Constraints for table `transaksi_detail`
--
ALTER TABLE `transaksi_detail`
  ADD CONSTRAINT `transaksi_detail_ibfk_1` FOREIGN KEY (`id_transaksi`) REFERENCES `transaksi` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
