-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 05 Mar 2026 pada 10.55
-- Versi server: 10.4.28-MariaDB
-- Versi PHP: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sole_globalindo`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `carts`
--

CREATE TABLE `carts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `carts`
--

INSERT INTO `carts` (`id`, `user_id`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 5, 0, '2025-11-22 06:48:53', '2025-11-22 14:16:10'),
(2, 5, 0, '2025-11-22 14:16:12', '2025-11-22 14:30:03'),
(3, 5, 0, '2025-11-22 14:30:05', '2025-11-22 14:40:42'),
(4, 5, 0, '2025-11-22 14:40:43', '2025-11-22 14:52:16'),
(5, 5, 0, '2025-11-22 14:52:17', '2025-11-26 11:26:58'),
(6, 6, 0, '2025-11-22 15:00:14', '2025-11-22 15:01:02'),
(7, 6, 0, '2025-11-22 15:01:03', '2025-11-23 09:12:49'),
(8, 6, 0, '2025-11-23 09:12:51', '2025-11-24 04:25:08'),
(9, 6, 1, '2025-11-24 04:25:10', '2025-11-24 04:25:10'),
(10, 5, 1, '2025-11-26 11:26:59', '2025-11-26 11:26:59');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cart_items`
--

CREATE TABLE `cart_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `cart_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `quantity` int(11) NOT NULL DEFAULT 1,
  `price` decimal(12,2) NOT NULL,
  `size` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `length` varchar(255) DEFAULT NULL,
  `width` varchar(255) DEFAULT NULL,
  `height` varchar(255) DEFAULT NULL,
  `bahan` varchar(255) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `rotan_color` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cart_items`
--

INSERT INTO `cart_items` (`id`, `cart_id`, `product_id`, `custom_product_id`, `quantity`, `price`, `size`, `created_at`, `updated_at`, `length`, `width`, `height`, `bahan`, `color`, `rotan_color`) VALUES
(1, 1, 1, NULL, 1, 349800.00, '57x61x89 cm', '2025-11-22 09:20:34', '2025-11-22 09:20:34', '57', '61', '89', 'Kayu Jati', 'Natural Jati', NULL),
(10, 1, 13, NULL, 1, 610600.00, '140x50x77 cm', '2025-11-22 14:10:35', '2025-11-22 14:10:35', '160', '70', '99', 'Kayu Jati', 'Abu-Abu', NULL),
(11, 2, 1, NULL, 1, 349800.00, '57x61x89 cm', '2025-11-22 14:28:25', '2025-11-22 14:28:25', '57', '61', '89', 'Kayu Jati', 'Natural Jati', NULL),
(13, 2, 13, NULL, 1, 523800.00, '140x50x77 cm', '2025-11-22 14:29:30', '2025-11-22 14:29:30', '140', '50', '77', 'Kayu Jati', 'Abu-Abu', NULL),
(14, 3, 1, NULL, 1, 349800.00, '57x61x89 cm', '2025-11-22 14:39:38', '2025-11-22 14:39:38', '57', '61', '89', 'Kayu Jati', 'Natural Jati', NULL),
(15, 3, 13, NULL, 1, 610600.00, '140x50x77 cm', '2025-11-22 14:40:01', '2025-11-22 14:40:01', '160', '70', '99', 'Kayu Jati', 'Abu-Abu', NULL),
(17, 4, 1, NULL, 1, 349800.00, '57x61x89 cm', '2025-11-22 14:49:03', '2025-11-22 14:49:03', '57', '61', '89', 'Kayu Jati', 'Natural Jati', NULL),
(20, 4, 13, NULL, 1, 840800.00, '140x50x77 cm', '2025-11-22 14:50:16', '2025-11-22 14:50:16', '140', '50', '77', 'Kayu Jati dan Rotan', 'Abu-Abu', 'Putih'),
(22, 7, 13, NULL, 1, 962800.00, '140x50x77 cm', '2025-11-23 09:09:29', '2025-11-23 09:09:29', '150', '60', '87', 'Kayu Jati dan Rotan', 'walnut', 'Putih'),
(23, 7, 14, NULL, 3, 800800.00, '140x50x77 cm', '2025-11-23 09:09:51', '2025-11-23 09:09:51', '140', '50', '77', 'Kayu Jati dan Rotan', 'Walnut Brown', 'Coklat'),
(25, 8, 13, NULL, 1, 494000.00, '140x50x77 cm', '2025-11-24 04:24:25', '2025-11-24 04:24:25', '100', '50', '60', 'Kayu Jati', 'Natural', NULL),
(26, 9, 13, NULL, 1, 573800.00, '140x50x77 cm', '2025-11-24 07:25:51', '2025-11-24 07:25:51', '140', '50', '77', 'Kayu Jati', 'Natural', NULL),
(27, 5, 19, NULL, 1, 662000.00, '60x70x80 cm', '2025-11-26 11:25:32', '2025-11-26 11:25:32', '80', '60', '90', 'Kayu Jati dan Rotan', 'Natural', 'Putih');

-- --------------------------------------------------------

--
-- Struktur dari tabel `cities`
--

CREATE TABLE `cities` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kabupaten` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `lat` decimal(10,7) DEFAULT NULL,
  `lng` decimal(10,7) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cities`
--

INSERT INTO `cities` (`id`, `kabupaten`, `province`, `lat`, `lng`, `created_at`, `updated_at`) VALUES
(1, 'Kabupaten Jepara', 'Jawa Tengah', -6.5841000, 110.6700000, '2025-11-21 17:17:41', '2025-11-21 17:17:41'),
(2, 'Kabupaten Jepara', 'Jawa Tengah', -6.5841000, 110.6700000, '2025-11-21 17:18:40', '2025-11-21 17:18:40'),
(3, 'Karawang', 'Jawa Barat', -6.3021906, 107.3046116, '2025-11-22 11:41:29', '2025-11-22 11:41:29'),
(4, 'Sleman', 'DI Yogyakarta', -7.6894175, 110.3812904, '2025-11-22 15:00:57', '2025-11-22 15:00:57');

-- --------------------------------------------------------

--
-- Struktur dari tabel `colors`
--

CREATE TABLE `colors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` enum('wood','rattan') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `colors`
--

INSERT INTO `colors` (`id`, `name`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Natural Jati', 'wood', '2025-11-22 06:46:21', '2025-11-22 06:46:21'),
(2, 'Walnut Brown', 'wood', '2025-11-22 06:46:21', '2025-11-22 06:46:21'),
(3, 'Coklat Salak', 'wood', '2025-11-22 06:46:21', '2025-11-22 06:46:21'),
(4, 'Putih', 'rattan', '2025-11-22 06:46:21', '2025-11-22 06:46:21'),
(5, 'Merah', 'rattan', '2025-11-22 06:46:21', '2025-11-22 06:46:21'),
(6, 'Coklat', 'rattan', '2025-11-22 06:46:21', '2025-11-22 06:46:21'),
(7, 'Abu-Abu', 'rattan', '2025-11-22 08:30:02', '2025-11-22 08:30:02'),
(8, 'Natural', 'wood', '2025-11-22 09:56:45', '2025-11-22 09:56:45'),
(9, 'walnut', 'wood', '2025-11-22 09:56:45', '2025-11-22 09:56:45'),
(10, 'Abu-Abu', 'wood', '2025-11-22 11:24:13', '2025-11-22 11:24:13'),
(11, 'Brown', 'wood', '2025-11-26 11:24:32', '2025-11-26 11:24:32');

-- --------------------------------------------------------

--
-- Struktur dari tabel `color_product`
--

CREATE TABLE `color_product` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `color_id` bigint(20) UNSIGNED NOT NULL,
  `extra_price` int(11) NOT NULL DEFAULT 0,
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `color_product`
--

INSERT INTO `color_product` (`id`, `product_id`, `color_id`, `extra_price`, `is_default`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 60000, 1, '2025-11-22 06:46:21', '2025-11-22 08:30:02'),
(2, 1, 2, 80000, 0, '2025-11-22 06:46:21', '2025-11-22 08:30:02'),
(3, 1, 3, 90000, 0, '2025-11-22 06:46:21', '2025-11-22 08:30:02'),
(4, 1, 4, 50000, 0, '2025-11-22 06:46:21', '2025-11-22 08:30:02'),
(5, 1, 5, 50000, 0, '2025-11-22 06:46:21', '2025-11-22 08:30:02'),
(6, 1, 6, 50000, 0, '2025-11-22 06:46:21', '2025-11-22 08:30:02'),
(11, 3, 1, 180000, 1, '2025-11-22 06:51:53', '2025-11-22 14:19:38'),
(12, 3, 2, 150000, 0, '2025-11-22 06:51:53', '2025-11-22 14:19:38'),
(13, 3, 4, 50000, 1, '2025-11-22 06:51:53', '2025-11-22 14:19:38'),
(14, 3, 5, 70000, 0, '2025-11-22 06:51:53', '2025-11-22 14:19:38'),
(15, 1, 7, 40000, 0, '2025-11-22 08:30:02', '2025-11-22 08:30:02'),
(47, 13, 8, 200000, 1, '2025-11-22 11:16:51', '2025-11-22 11:35:42'),
(48, 13, 4, 50000, 0, '2025-11-22 11:16:51', '2025-11-22 11:35:42'),
(49, 13, 9, 200000, 0, '2025-11-22 11:24:13', '2025-11-22 11:35:42'),
(50, 13, 10, 150000, 0, '2025-11-22 11:24:13', '2025-11-22 11:35:42'),
(51, 14, 1, 200000, 1, '2025-11-23 08:45:07', '2025-11-26 11:26:19'),
(52, 14, 2, 100000, 0, '2025-11-23 08:45:07', '2025-11-26 11:26:19'),
(53, 14, 4, 50000, 0, '2025-11-23 08:45:07', '2025-11-26 11:26:19'),
(54, 14, 6, 60000, 0, '2025-11-23 08:45:07', '2025-11-26 11:26:19'),
(59, 14, 10, 120000, 0, '2025-11-23 08:49:04', '2025-11-26 11:26:19'),
(68, 19, 8, 100000, 1, '2025-11-26 11:22:08', '2025-11-26 11:25:03'),
(69, 19, 11, 50000, 0, '2025-11-26 11:24:32', '2025-11-26 11:25:03'),
(70, 19, 4, 10000, 0, '2025-11-26 11:25:03', '2025-11-26 11:25:03');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL,
  `address` text DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type` enum('online','offline') NOT NULL DEFAULT 'online',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `address`, `city_id`, `type`, `created_at`, `updated_at`) VALUES
(1, 'Konsumen Offline 1', '081234567101', 'Jl. Pelanggan Offline 1 No. 10', 1, 'offline', '2025-11-21 17:17:41', '2025-11-21 17:17:41'),
(2, 'Konsumen Online 1', '081234567102', 'Jl. Pelanggan Online 1 No. 2', 1, 'online', '2025-11-21 17:17:41', '2025-11-21 17:17:41'),
(3, 'Sheilla Nandya', '081564321876', 'Jln Mlonggo-Bondo', 3, 'offline', '2025-11-22 11:41:47', '2025-11-22 11:41:47'),
(4, 'Sheilla Nandya', '081564321876', 'Apartemen Urban Tower', 3, 'offline', '2025-11-22 13:12:54', '2025-11-22 13:12:54');

-- --------------------------------------------------------

--
-- Struktur dari tabel `custom_products`
--

CREATE TABLE `custom_products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `material` varchar(255) NOT NULL,
  `material_price` decimal(12,2) NOT NULL,
  `wood_color` varchar(255) DEFAULT NULL,
  `wood_color_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `rattan_color` varchar(255) DEFAULT NULL,
  `rattan_color_price` decimal(12,2) NOT NULL DEFAULT 0.00,
  `length` decimal(8,2) NOT NULL,
  `width` decimal(8,2) NOT NULL,
  `height` decimal(8,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(3, '2014_10_12_100000_create_password_resets_table', 1),
(4, '2019_08_19_000000_create_failed_jobs_table', 1),
(5, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(6, '2025_05_18_071541_create_products_table', 1),
(7, '2025_05_18_071551_create_product_variants_table', 1),
(8, '2025_05_18_071557_create_carts_table', 1),
(9, '2025_05_18_071615_create_custom_products_table', 1),
(10, '2025_05_18_071620_create_orders_table', 1),
(11, '2025_05_18_071627_create_order_items_table', 1),
(12, '2025_05_18_072150_create_cart_items_table', 1),
(13, '2025_06_04_213337_alter_products', 1),
(14, '2025_06_04_214208_alter_order_table', 1),
(15, '2025_06_05_062536_alter_carts_items', 1),
(16, '2025_06_21_214208_alter_order_table', 1),
(17, '2025_11_14_020922_create_colors_table', 1),
(18, '2025_11_14_021302_create_color_product_table', 1),
(19, '2025_11_15_132556_create_cities_table', 1),
(20, '2025_11_21_160809_create_customers_table', 1),
(21, '2025_11_21_182728_add_customer_id_to_orders_table', 1),
(22, '2025_11_21_183629_rename_cities_name_to_kabupaten', 1),
(23, '2025_11_21_190321_create_shipping_costs_table', 1),
(24, '2025_11_21_191944_add_volume_columns_to_shipping_costs_table', 1),
(25, '2025_11_21_192636_rename_shipping_costs_price_to_total_ongkir', 1),
(26, '2025_11_21_192738_add_summary_column_to_shipping_costs_table', 1),
(27, '2025_11_21_193247_drop_fleet_and_notes_from_shipping_costs_table', 1),
(28, '2025_11_21_193339_drop_notes_from_customers_table', 1),
(29, '2025_11_21_194020_add_total_ongkir_to_orders_table', 1),
(30, '2025_11_21_195944_add_city_id_to_shipping_costs_table', 1),
(31, '2025_11_21_210620_drop_email_from_customers_table', 1),
(32, '2025_11_21_213945_drop_session_id_from_carts_table', 1),
(33, '2025_11_21_214213_drop_is_customizable_from_products_table', 1),
(34, '2025_11_21_235921_create_shipping_origins_table', 1),
(35, '2025_11_22_120022_create_shipping_rates_table', 2),
(36, '2025_11_22_120042_create_shipping_settings_table', 2),
(37, '2025_11_22_124925_add_tarif_snapshot_to_shipping_costs_table', 3),
(38, '2025_11_22_125300_create_shipping_config_table', 4),
(39, '2025_11_22_125513_drop_shipping_rates_and_settings_tables', 5),
(40, '2025_11_22_132232_add_relations_to_shipping_costs_table', 6),
(41, '2025_11_22_135739_drop_unused_columns_from_users_table', 7),
(42, '2025_11_22_144233_add_price_columns_to_product_variants_table', 8),
(43, '2025_11_22_192841_update_shipping_costs_table_rename_and_add_fields', 9),
(44, '2025_11_22_194257_update_shipping_costs_table_rename_and_add_calculated_fields', 9),
(45, '2025_11_22_200505_fix_shipping_costs_table_and_update_data', 10);

-- --------------------------------------------------------

--
-- Struktur dari tabel `orders`
--

CREATE TABLE `orders` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `customer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `order_date` datetime NOT NULL,
  `payment_method` enum('cash','transfer','credit_card') NOT NULL,
  `status` enum('in_cart','processing','received','in_progress','sending','completed','cancelled') NOT NULL,
  `type` enum('online','offline') NOT NULL,
  `payment_status` enum('waiting_payment','paid') DEFAULT NULL,
  `payment_time` datetime DEFAULT NULL,
  `total_amount` decimal(12,2) NOT NULL,
  `total_ongkir` decimal(12,2) NOT NULL DEFAULT 0.00,
  `payment_proof` varchar(255) DEFAULT NULL,
  `name` varchar(255) DEFAULT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `tracking_number` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `customer_id`, `order_date`, `payment_method`, `status`, `type`, `payment_status`, `payment_time`, `total_amount`, `total_ongkir`, `payment_proof`, `name`, `phone`, `address`, `created_at`, `updated_at`, `tracking_number`) VALUES
(2, NULL, 4, '2025-11-22 20:12:54', 'cash', 'received', 'offline', 'paid', '2025-11-22 20:12:54', 1592174.00, 1242374.00, NULL, 'Sheilla Nandya', '081564321876', 'Apartemen Urban Tower', '2025-11-22 13:12:54', '2025-11-22 13:12:54', '20092657'),
(6, 5, NULL, '2025-11-22 21:52:16', 'transfer', 'completed', 'online', 'paid', '2025-11-22 21:53:32', 3101853.00, 1387453.00, 'payment_proofs/fpG9d28qvQ6l7wM8jgSLJ8O2mLISuT9DrBJLeize.png', 'tatapuspita', '089654321345', 'jln Mlonggo-Bondo, Jawa Tengah, jepara, Mlonggo, Jambu Tembiluk, 59452', '2025-11-22 14:52:16', '2025-11-23 09:15:21', '49273229'),
(8, 6, NULL, '2025-11-23 16:12:49', 'transfer', 'in_progress', 'online', 'paid', '2025-11-23 16:13:22', 6082616.00, 2717416.00, 'payment_proofs/Yv777ESI9f44WD5qRTGj4GCMAVfYgZEWYEDPzhRO.png', 'handinimaharani', '089654321345', 'Gang Matoa, DI Yogyakarta, Sleman, Ngaglik, sardonoharjo, 55581', '2025-11-23 09:12:49', '2025-11-23 09:13:22', '10842994'),
(10, 5, NULL, '2025-11-26 18:26:58', 'transfer', 'completed', 'online', 'paid', '2025-11-26 18:27:19', 1411416.00, 749416.00, 'payment_proofs/E1HxgOgsLBj6kVzKF54h1Bx7ODskuqFUooXiobtz.png', 'tatapuspita', '089765123456', 'jssnw, DI Yogyakarta, Sleman, Ngaglik, sardonoharjo, 55581', '2025-11-26 11:26:58', '2025-11-26 11:27:46', '58571245');

-- --------------------------------------------------------

--
-- Struktur dari tabel `order_items`
--

CREATE TABLE `order_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `custom_product_id` bigint(20) UNSIGNED DEFAULT NULL,
  `size` varchar(255) DEFAULT NULL,
  `quantity` int(11) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `material` varchar(255) DEFAULT NULL,
  `wood_color` varchar(255) DEFAULT NULL,
  `rattan_color` varchar(255) DEFAULT NULL,
  `length` decimal(8,2) DEFAULT NULL,
  `width` decimal(8,2) DEFAULT NULL,
  `height` decimal(8,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `order_items`
--

INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `custom_product_id`, `size`, `quantity`, `price`, `material`, `wood_color`, `rattan_color`, `length`, `width`, `height`, `created_at`, `updated_at`) VALUES
(2, 2, 1, NULL, '57x61x89 cm', 1, 349800.00, 'Kayu Jati', 'Natural Jati', NULL, 57.00, 61.00, 89.00, '2025-11-22 13:12:54', '2025-11-22 13:12:54'),
(12, 6, 1, NULL, '57x61x89 cm', 1, 349800.00, 'Kayu Jati', 'Natural Jati', NULL, 57.00, 61.00, 89.00, '2025-11-22 14:52:16', '2025-11-22 14:52:16'),
(14, 6, 13, NULL, '140x50x77 cm', 1, 840800.00, 'Kayu Jati dan Rotan', 'Abu-Abu', 'Putih', 140.00, 50.00, 77.00, '2025-11-22 14:52:16', '2025-11-22 14:52:16'),
(16, 8, 13, NULL, '140x50x77 cm', 1, 962800.00, 'Kayu Jati dan Rotan', 'walnut', 'Putih', 150.00, 60.00, 87.00, '2025-11-23 09:12:49', '2025-11-23 09:12:49'),
(17, 8, 14, NULL, '140x50x77 cm', 3, 800800.00, 'Kayu Jati dan Rotan', 'Walnut Brown', 'Coklat', 140.00, 50.00, 77.00, '2025-11-23 09:12:49', '2025-11-23 09:12:49'),
(19, 10, 19, NULL, '60x70x80 cm', 1, 662000.00, 'Kayu Jati dan Rotan', 'Natural', 'Putih', 80.00, 60.00, 90.00, '2025-11-26 11:26:58', '2025-11-26 11:26:58');

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `size` varchar(255) NOT NULL,
  `display_image` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `default_length` varchar(255) DEFAULT NULL,
  `default_width` varchar(255) DEFAULT NULL,
  `default_height` varchar(255) DEFAULT NULL,
  `default_bahan` varchar(255) DEFAULT NULL,
  `default_color` varchar(255) DEFAULT NULL,
  `default_rotan_color` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `title`, `description`, `price`, `size`, `display_image`, `created_at`, `updated_at`, `default_length`, `default_width`, `default_height`, `default_bahan`, `default_color`, `default_rotan_color`) VALUES
(1, 'Folding Armchair', 'Kursi ini terbuat dari kayu jati yang kuat dan tahan lama, dengan desain sederhana namun tetap menarik secara visual. Kursi ini dapat dilipat, sehingga mudah disimpan dan dipindahkan untuk penggunaan indoor maupun outdoor. Warna alami dari kayu jati memberikan kesan hangat dan estetik, cocok ditempatkan di teras, taman, atau ruang makan. Tersedia juga opsi custom dengan anyaman rotan pada bagian dudukan dan sandaran untuk tampilan yang lebih artistik, tetap mengandalkan rangka jati yang kuat, membuat tampilannya lebih unik. Gabungan antara kayu jati dan rotan ini juga memberikan kenyamanan lebih dan cocok bagi Anda yang menyukai suasana alami di rumah.', 349800.00, '57x61x89 cm', 'products/ppxk3GL1Co6WQGsyOqXIkIJ3hoymYGi43YC8iWYw.png', '2025-11-22 06:46:21', '2025-11-22 08:30:02', '57', '61', '89', 'Kayu Jati', 'Natural Jati', NULL),
(3, 'Lounger', 'Kursi santai ini dirancang dari kayu jati berkualitas tinggi yang tahan terhadap cuaca ekstrem dan sangat ideal untuk penggunaan outdoor seperti di tepi kolam, taman, atau balkon. Dilengkapi dengan sandaran yang dapat diatur kemiringannya, kursi ini memberikan kenyamanan untuk bersantai, berjemur, atau membaca buku. Terdapat roda di bagian belakang memudahkan pemindahan kursi tanpa harus mengangkat seluruh rangka. Terdapat juga meja kecil tarik di bagian samping sebagai tempat praktis untuk menaruh minuman, buku, atau aksesori.Tampilan kayu jati alami menambah kesan mewah dan tropis. Tersedia opsi kustom seperti finishing warna sesuai selera.', 644400.00, '198x36x62 cm', 'products/2vZ25JXVhGzWa6fivUJ6wre35Fbt38zUKcmpcpFB.png', '2025-11-22 06:51:53', '2025-11-22 14:19:38', '198', '36', '62', 'Kayu Jati', 'Natural Jati', 'Putih'),
(13, 'Console 3 Drawers', 'Meja console ini terbuat dari kayu jati pilihan yang kokoh dan tahan lama, sehingga aman untuk penggunaan jangka panjang. Desainnya minimalis dengan garis tegas dan finishing warna natural washed yang memberi kesan bersih, hangat, dan elegan—mudah dipadukan dengan berbagai gaya interior, dari klasik hingga modern. Bagian atasnya dilengkapi tiga laci serbaguna dengan handle metal yang rapi, pas untuk menyimpan barang kecil seperti kunci, dokumen, atau aksesori rumah agar area tetap terlihat simpel. Di bagian bawah terdapat rak tambahan model bilah yang lebar, ideal untuk menata keranjang, pajangan, atau perlengkapan lain sehingga ruang terlihat lebih tertata. Meja ini cocok ditempatkan di ruang tamu, lorong, foyer, atau area dekat pintu masuk sebagai meja dekorasi sekaligus penyimpanan. Tersedia opsi custom ukuran maupun warna finishing sesuai kebutuhan ruangan, tanpa menghilangkan karakter kuat dan alami dari kayu jati.', 573800.00, '140x50x77 cm', 'products/0x8HcWq1IlkOQx6z31FV3YAMFwyJMe8DZzmR4wZ8.png', '2025-11-22 11:16:51', '2025-11-22 11:35:42', '140', '50', '77', 'Kayu Jati', 'Natural', NULL),
(14, 'Balcony Table', 'Meja ini terbuat dari kayu solid dengan finishing natural yang menonjolkan serat kayu sehingga terlihat hangat dan rapi. Bagian top berbentuk persegi panjang dengan desain bilah-bilah (slatted) yang memberi kesan ringan sekaligus membantu aliran air ketika digunakan di area outdoor. Konstruksi kakinya menggunakan rangka silang di bagian tengah dengan penopang yang kokoh, membuat meja stabil namun tetap hemat tempat. Desainnya yang simpel dan fungsional cocok untuk konsep balkon, teras, atau taman, serta bisa menjadi meja makan santai atau meja ngopi di ruang semi-outdoor. Tampilan naturalnya juga mudah dipadukan dengan berbagai gaya kursi dan dekorasi, dari minimalis hingga rustic.', 573800.00, '140x50x77 cm', 'products/TAzotC4kORmst786yk3F4JmfMHw1iT09D5ajcUxC.png', '2025-11-23 08:45:07', '2025-11-26 11:26:19', '140', '50', '77', 'Kayu Jati', 'Natural Jati', NULL),
(19, 'hedh', 'dhchde', 394000.00, '60x70x80 cm', 'products/tlGQVkIg21mrMEBR8Nk8bbQViuexsT0aWlZZnPlC.png', '2025-11-26 11:22:08', '2025-11-26 11:25:03', '60', '70', '80', 'Kayu Jati', 'Natural', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `product_variants`
--

CREATE TABLE `product_variants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `product_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('material','wood_color','rattan_color') NOT NULL,
  `name` varchar(255) NOT NULL,
  `price_per_10cm` decimal(10,2) DEFAULT NULL,
  `length_price` decimal(10,2) DEFAULT NULL,
  `width_price` decimal(10,2) DEFAULT NULL,
  `height_price` decimal(10,2) DEFAULT NULL,
  `price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `product_variants`
--

INSERT INTO `product_variants` (`id`, `product_id`, `type`, `name`, `price_per_10cm`, `length_price`, `width_price`, `height_price`, `price`, `created_at`, `updated_at`) VALUES
(13, 1, 'material', 'Kayu Jati', NULL, 14000.00, 14000.00, 14000.00, 0.00, '2025-11-22 08:30:02', '2025-11-22 08:30:02'),
(14, 1, 'material', 'Kayu Jati dan Rotan', NULL, 24000.00, 24000.00, 24000.00, 0.00, '2025-11-22 08:30:02', '2025-11-22 08:30:02'),
(70, 13, 'material', 'Kayu Jati', NULL, 14000.00, 14000.00, 14000.00, 0.00, '2025-11-22 11:35:42', '2025-11-22 11:35:42'),
(71, 13, 'material', 'Kayu Jati dan Rotan', NULL, 24000.00, 24000.00, 24000.00, 0.00, '2025-11-22 11:35:42', '2025-11-22 11:35:42'),
(74, 3, 'material', 'Kayu Jati', NULL, 14000.00, 14000.00, 14000.00, 0.00, '2025-11-22 14:19:38', '2025-11-22 14:19:38'),
(75, 3, 'material', 'Kayu Jati dan Rotan', NULL, 24000.00, 24000.00, 24000.00, 0.00, '2025-11-22 14:19:38', '2025-11-22 14:19:38'),
(94, 19, 'material', 'Kayu Jati', NULL, 14000.00, 14000.00, 14000.00, 0.00, '2025-11-26 11:25:03', '2025-11-26 11:25:03'),
(95, 19, 'material', 'Kayu Jati dan Rotan', NULL, 24000.00, 24000.00, 24000.00, 0.00, '2025-11-26 11:25:03', '2025-11-26 11:25:03'),
(96, 14, 'material', 'Kayu Jati', NULL, 14000.00, 14000.00, 14000.00, 0.00, '2025-11-26 11:26:19', '2025-11-26 11:26:19'),
(97, 14, 'material', 'Kayu Jati dan Rotan', NULL, 24000.00, 24000.00, 24000.00, 0.00, '2025-11-26 11:26:19', '2025-11-26 11:26:19');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shipping_config`
--

CREATE TABLE `shipping_config` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('rate','setting') NOT NULL COMMENT 'rate = tarif per kg tier, setting = konfigurasi global',
  `key` varchar(255) DEFAULT NULL COMMENT 'Untuk setting: key (tarif_per_km, volume_divisor). Untuk rate: null',
  `min_weight_kg` decimal(10,2) DEFAULT NULL COMMENT 'Berat minimum (kg) - hanya untuk type=rate',
  `max_weight_kg` decimal(10,2) DEFAULT NULL COMMENT 'Berat maksimum (kg), null = unlimited - hanya untuk type=rate',
  `tarif_per_kg` decimal(12,2) DEFAULT NULL COMMENT 'Tarif per kg - hanya untuk type=rate',
  `order` int(11) NOT NULL DEFAULT 0 COMMENT 'Urutan untuk sorting - hanya untuk type=rate',
  `value` text DEFAULT NULL COMMENT 'Nilai setting - hanya untuk type=setting',
  `description` varchar(255) DEFAULT NULL COMMENT 'Deskripsi',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shipping_config`
--

INSERT INTO `shipping_config` (`id`, `type`, `key`, `min_weight_kg`, `max_weight_kg`, `tarif_per_kg`, `order`, `value`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'rate', NULL, 0.00, 500.00, 6000.00, 1, NULL, 'Tarif untuk berat 0-500 kg', 1, '2025-11-22 06:04:29', '2025-11-22 06:04:29'),
(2, 'rate', NULL, 500.00, 2000.00, 4000.00, 2, NULL, 'Tarif untuk berat 500-2000 kg', 1, '2025-11-22 06:04:29', '2025-11-22 06:04:29'),
(3, 'rate', NULL, 2000.00, NULL, 2000.00, 3, NULL, 'Tarif untuk berat >2000 kg', 1, '2025-11-22 06:04:29', '2025-11-22 06:04:29'),
(4, 'setting', 'tarif_per_km', NULL, NULL, NULL, 0, '2500', 'Tarif per kilometer dalam rupiah', 1, '2025-11-22 06:04:29', '2025-11-22 06:04:29'),
(5, 'setting', 'volume_divisor', NULL, NULL, NULL, 0, '6000', 'Pembagi berat volumetrik (cm³ → kg), standar domestik 6000', 1, '2025-11-22 06:04:29', '2025-11-22 06:04:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `shipping_costs`
--

CREATE TABLE `shipping_costs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `order_id` bigint(20) UNSIGNED NOT NULL,
  `total_ongkir` decimal(12,2) NOT NULL,
  `distance_km` decimal(10,2) NOT NULL DEFAULT 0.00,
  `biaya_berat` decimal(12,2) DEFAULT NULL COMMENT 'Biaya berat = berat_volume × tarif_per_kg (calculated)',
  `biaya_jarak` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Biaya jarak = distance × tarif_per_km',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `total_volume_cm3` decimal(14,2) NOT NULL DEFAULT 0.00,
  `berat_volume` decimal(12,2) NOT NULL DEFAULT 0.00 COMMENT 'Berat volume = total_volume_cm3 / divisor (calculated)',
  `total_items` int(11) NOT NULL DEFAULT 0,
  `item_summary` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`item_summary`)),
  `total_summary` text DEFAULT NULL,
  `city_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shipping_origin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `shipping_config_rate_id` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shipping_costs`
--

INSERT INTO `shipping_costs` (`id`, `order_id`, `total_ongkir`, `distance_km`, `biaya_berat`, `biaya_jarak`, `created_at`, `updated_at`, `total_volume_cm3`, `berat_volume`, `total_items`, `item_summary`, `total_summary`, `city_id`, `shipping_origin_id`, `shipping_config_rate_id`) VALUES
(2, 2, 1242374.00, 373.17, 309453.00, 932920.84, '2025-11-22 13:12:54', '2025-11-22 13:12:54', 309453.00, 51.58, 1, '[{\"length_cm\":57,\"width_cm\":61,\"height_cm\":89,\"qty\":1}]', 'Items: 1 · Total Volume: 309453 cm³ · Berat Volume: 51.58 kg', 3, 1, 1),
(4, 6, 1387453.00, 0.00, 1387453.00, 0.00, '2025-11-22 14:52:16', '2025-11-22 14:55:29', 1387453.00, 231.24, 3, '[{\"length_cm\":57,\"width_cm\":61,\"height_cm\":89,\"qty\":1},{\"length_cm\":140,\"width_cm\":50,\"height_cm\":77,\"qty\":1},{\"length_cm\":140,\"width_cm\":50,\"height_cm\":77,\"qty\":1}]', 'Items: 3 · Total Volume: 1387453 cm³ · Berat Volume: 231.24 kg', 1, 1, 1),
(6, 8, 2717416.00, 126.97, 2400000.00, 317416.09, '2025-11-23 09:12:49', '2025-11-23 09:12:49', 2400000.00, 400.00, 4, '[{\"length_cm\":150,\"width_cm\":60,\"height_cm\":87,\"qty\":1},{\"length_cm\":140,\"width_cm\":50,\"height_cm\":77,\"qty\":3}]', 'Items: 4 · Total Volume: 2400000 cm³ · Berat Volume: 400 kg', 4, 1, 1),
(8, 10, 749416.00, 126.97, 432000.00, 317416.09, '2025-11-26 11:26:58', '2025-11-26 11:26:58', 432000.00, 72.00, 1, '[{\"length_cm\":80,\"width_cm\":60,\"height_cm\":90,\"qty\":1}]', 'Items: 1 · Total Volume: 432000 cm³ · Berat Volume: 72 kg', 4, 1, 1);

-- --------------------------------------------------------

--
-- Struktur dari tabel `shipping_origins`
--

CREATE TABLE `shipping_origins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `lat` decimal(10,7) NOT NULL,
  `lng` decimal(10,7) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `shipping_origins`
--

INSERT INTO `shipping_origins` (`id`, `name`, `lat`, `lng`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Gudang Jepara', -6.5841000, 110.6700000, 1, '2025-11-21 17:17:41', '2025-11-21 17:32:26');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','customer','owner') NOT NULL DEFAULT 'customer',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@gmail.com', '$2y$10$rJ2/Je3WUXukTCJyy/8r9ONQsN.k8YHTwnDSjatIPrJNw2jGeHyGe', 'admin', '2025-11-21 17:17:41', '2025-11-21 17:17:41'),
(2, 'Owner', 'owner@gmail.com', '$2y$10$yNXI9dkbPtPl5HH69vpNAOp/nMSBkl/Tkd6mmU4tgdXDMY1T.D/MK', 'owner', '2025-11-21 17:17:41', '2025-11-21 17:17:41'),
(5, 'tatapuspita', 'tatapuspita222@gmail.com', '$2y$10$BxTCXWHK4HHikZE2Fh1HiOtLbUlOBtw0fRQPpLRunIP0Q54plQFxy', 'customer', '2025-11-22 06:48:51', '2025-11-22 06:48:51'),
(6, 'handinimaharani', 'handinimaharani@gmail.com', '$2y$10$L2vJ/.PLJEezGkx6Fa3G/eO3PXTHCJp/HS/dTuYgw/fiuTijKLxcm', 'customer', '2025-11-22 15:00:12', '2025-11-22 15:00:12');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `carts_user_id_foreign` (`user_id`);

--
-- Indeks untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cart_items_cart_id_foreign` (`cart_id`),
  ADD KEY `cart_items_product_id_foreign` (`product_id`),
  ADD KEY `cart_items_custom_product_id_foreign` (`custom_product_id`);

--
-- Indeks untuk tabel `cities`
--
ALTER TABLE `cities`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `colors`
--
ALTER TABLE `colors`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `color_product`
--
ALTER TABLE `color_product`
  ADD PRIMARY KEY (`id`),
  ADD KEY `color_product_product_id_foreign` (`product_id`),
  ADD KEY `color_product_color_id_foreign` (`color_id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customers_city_id_foreign` (`city_id`);

--
-- Indeks untuk tabel `custom_products`
--
ALTER TABLE `custom_products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `orders_user_id_foreign` (`user_id`),
  ADD KEY `orders_customer_id_foreign` (`customer_id`);

--
-- Indeks untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_items_order_id_foreign` (`order_id`),
  ADD KEY `order_items_product_id_foreign` (`product_id`),
  ADD KEY `order_items_custom_product_id_foreign` (`custom_product_id`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indeks untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `product_variants`
--
ALTER TABLE `product_variants`
  ADD PRIMARY KEY (`id`),
  ADD KEY `product_variants_product_id_foreign` (`product_id`);

--
-- Indeks untuk tabel `shipping_config`
--
ALTER TABLE `shipping_config`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `shipping_config_type_key_unique` (`type`,`key`),
  ADD KEY `shipping_config_type_is_active_index` (`type`,`is_active`),
  ADD KEY `shipping_config_type_min_weight_kg_max_weight_kg_index` (`type`,`min_weight_kg`,`max_weight_kg`);

--
-- Indeks untuk tabel `shipping_costs`
--
ALTER TABLE `shipping_costs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `shipping_costs_order_id_foreign` (`order_id`),
  ADD KEY `shipping_costs_city_id_foreign` (`city_id`),
  ADD KEY `shipping_costs_shipping_origin_id_foreign` (`shipping_origin_id`),
  ADD KEY `shipping_costs_shipping_config_rate_id_foreign` (`shipping_config_rate_id`);

--
-- Indeks untuk tabel `shipping_origins`
--
ALTER TABLE `shipping_origins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `carts`
--
ALTER TABLE `carts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT untuk tabel `cities`
--
ALTER TABLE `cities`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `colors`
--
ALTER TABLE `colors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT untuk tabel `color_product`
--
ALTER TABLE `color_product`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `custom_products`
--
ALTER TABLE `custom_products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT untuk tabel `orders`
--
ALTER TABLE `orders`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT untuk tabel `order_items`
--
ALTER TABLE `order_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT untuk tabel `product_variants`
--
ALTER TABLE `product_variants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT untuk tabel `shipping_config`
--
ALTER TABLE `shipping_config`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `shipping_costs`
--
ALTER TABLE `shipping_costs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `shipping_origins`
--
ALTER TABLE `shipping_origins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `cart_items`
--
ALTER TABLE `cart_items`
  ADD CONSTRAINT `cart_items_cart_id_foreign` FOREIGN KEY (`cart_id`) REFERENCES `carts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_custom_product_id_foreign` FOREIGN KEY (`custom_product_id`) REFERENCES `custom_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `color_product`
--
ALTER TABLE `color_product`
  ADD CONSTRAINT `color_product_color_id_foreign` FOREIGN KEY (`color_id`) REFERENCES `colors` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `color_product_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD CONSTRAINT `customers_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Ketidakleluasaan untuk tabel `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `orders_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `order_items`
--
ALTER TABLE `order_items`
  ADD CONSTRAINT `order_items_custom_product_id_foreign` FOREIGN KEY (`custom_product_id`) REFERENCES `custom_products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `product_variants`
--
ALTER TABLE `product_variants`
  ADD CONSTRAINT `product_variants_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `shipping_costs`
--
ALTER TABLE `shipping_costs`
  ADD CONSTRAINT `shipping_costs_city_id_foreign` FOREIGN KEY (`city_id`) REFERENCES `cities` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shipping_costs_order_id_foreign` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `shipping_costs_shipping_config_rate_id_foreign` FOREIGN KEY (`shipping_config_rate_id`) REFERENCES `shipping_config` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `shipping_costs_shipping_origin_id_foreign` FOREIGN KEY (`shipping_origin_id`) REFERENCES `shipping_origins` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
