-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 18, 2026 at 08:31 PM
-- Server version: 9.6.0
-- PHP Version: 8.5.5

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `kong_safaris`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int UNSIGNED NOT NULL,
  `customer_id` int UNSIGNED DEFAULT NULL,
  `vehicle_id` int UNSIGNED NOT NULL,
  `driver_id` int UNSIGNED NOT NULL,
  `pickup_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `dropoff_address` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `pickup_latitude` decimal(10,8) NOT NULL,
  `pickup_longitude` decimal(11,8) NOT NULL,
  `dropoff_latitude` decimal(10,8) NOT NULL,
  `dropoff_longitude` decimal(11,8) NOT NULL,
  `distance_km` decimal(10,2) NOT NULL,
  `base_booking_fee` decimal(10,2) NOT NULL,
  `per_km_fuel_cost` decimal(10,2) NOT NULL,
  `maintenance_reserve` decimal(10,2) NOT NULL,
  `driver_allowance` decimal(10,2) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `payment_status` varchar(25) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `trip_status` enum('pending','active','completed','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `paystack_reference` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `vehicle_id`, `driver_id`, `pickup_address`, `dropoff_address`, `pickup_latitude`, `pickup_longitude`, `dropoff_latitude`, `dropoff_longitude`, `distance_km`, `base_booking_fee`, `per_km_fuel_cost`, `maintenance_reserve`, `driver_allowance`, `total_price`, `payment_status`, `trip_status`, `paystack_reference`, `created_at`, `updated_at`) VALUES
(5, 4, 1, 1, 'Imara Daima, Nairobi, Kenya', 'Maasai Mara National Reserve, Kenya', -1.32843600, 36.88064990, -1.48213240, 35.12998960, 272.25, 50.00, 49.34, 136.12, 50.00, 693.84, 'paid', 'completed', 'KONG_M5_d8263fbc', '2026-06-17 13:50:30', '2026-06-17 15:32:41'),
(16, 4, 1, 1, 'Imara Daima, Nairobi, Kenya', 'Ngong Hills, Kenya', -1.32843600, 36.88064990, -1.40000000, 36.63805560, 41.02, 50.00, 7.44, 20.51, 50.00, 189.48, 'paid', 'completed', 'KONG_P_f667dce3be00', '2026-06-17 16:58:39', '2026-06-17 17:39:42'),
(17, 4, 1, 1, 'Imara Daima, Nairobi, Kenya', 'Naivasha Town, Kenya', -1.32843600, 36.88064990, -0.70571660, 36.42561340, 108.90, 50.00, 19.74, 54.45, 50.00, 337.54, '', 'cancelled', 'KONG_P_29d32570519e', '2026-06-17 17:41:47', '2026-06-17 17:42:04'),
(18, 4, 1, 1, 'Imara Daima, Nairobi, Kenya', 'Naivasha, Kenya', -1.32843600, 36.88064990, -0.71936370, 36.43302100, 106.00, 50.00, 19.21, 53.00, 50.00, 331.21, 'refunded', 'cancelled', 'KONG_P_b4e9c9fc38da', '2026-06-17 17:51:37', '2026-06-17 17:52:29'),
(19, 4, 1, 1, 'Pinned Location (-1.25331, 36.73967)', 'Pinned Location (-1.29999, 36.85331)', -1.25331410, 36.73967420, -1.29999440, 36.85331403, 21.88, 50.00, 3.96, 10.94, 50.00, 147.71, 'refunded', 'cancelled', 'KONG_P_b6a59ccc92a8', '2026-06-17 17:54:46', '2026-06-17 17:55:45'),
(20, 4, 1, 1, 'Imara Daima, Nairobi, Kenya', 'Ngong Hills, Kenya', -1.32843600, 36.88064990, -1.40000000, 36.63805560, 41.02, 50.00, 7.44, 20.51, 50.00, 189.48, 'paid', 'completed', 'KONG_P_8f7341c463b6', '2026-06-17 19:19:14', '2026-06-17 19:35:12'),
(21, 4, 2, 1, 'Imara Daima, Nairobi, Kenya', 'Ruiru, Kenya', -1.32843600, 36.88064990, -1.14837810, 36.96057810, 28.86, 50.00, 5.98, 17.31, 50.00, 175.24, 'paid', 'completed', 'KONG_P_49cf50555605', '2026-06-17 19:37:02', '2026-06-18 20:22:42'),
(22, 4, 1, 1, 'imara', 'ruiru', 1.23000000, 1.45000000, 1.50000000, 1.50000000, 789.00, 0.00, 0.00, 0.00, 0.00, 789.00, 'pending', 'cancelled', '', '2026-06-17 19:40:26', '2026-06-17 21:18:25'),
(23, 4, 2, 1, 'Pinned Location (-1.25577, 36.83167)', 'Pinned Location (-1.28598, 36.85743)', -1.25577408, 36.83166504, -1.28597905, 36.85743391, 6.87, 0.00, 0.00, 0.00, 0.00, 117.90, 'refunded', 'cancelled', '', '2026-06-17 20:20:47', '2026-06-18 17:24:44'),
(24, 4, 1, 1, 'Pinned Location (-1.27151, 36.83546)', 'Pinned Location (-1.28798, 36.84748)', -1.27150579, 36.83546125, -1.28798117, 36.84747755, 3.38, 0.00, 0.00, 0.00, 0.00, 107.36, 'paid', 'completed', 'KONG_P_b05aa5de84ac', '2026-06-17 20:38:57', '2026-06-18 20:22:47'),
(25, 4, 1, 1, 'Pinned Location (-1.27219, 36.88250)', 'Pinned Location (-1.30308, 36.86018)', -1.27219227, 36.88249647, -1.30308351, 36.86018049, 8.92, 0.00, 0.00, 0.00, 0.00, 119.45, 'refunded', 'cancelled', '', '2026-06-17 20:39:23', '2026-06-18 17:24:48'),
(26, 4, 1, 1, 'Pinned Location (-1.23135, 36.82413)', 'Pinned Location (-1.30583, 36.86739)', -1.23134661, 36.82413160, -1.30582938, 36.86739026, 14.72, 0.00, 0.00, 0.00, 0.00, 132.10, 'refunded', 'cancelled', '', '2026-06-17 20:39:51', '2026-06-18 17:24:50'),
(27, 4, 1, 1, 'Pinned Location (-1.27151, 36.83546)', 'Pinned Location (-1.28798, 36.84748)', -1.27150579, 36.83546125, -1.28798117, 36.84747755, 3.38, 0.00, 0.00, 0.00, 0.00, 107.36, 'paid', 'completed', 'KONG_P_b9ecb1c1b8b7', '2026-06-17 21:11:12', '2026-06-18 20:22:34'),
(28, 4, 1, 1, 'imara', 'ruiru', 1.23000000, 1.45000000, 1.50000000, 1.50000000, 789.00, 0.00, 0.00, 0.00, 0.00, 789.00, 'paid', 'completed', 'KONG_P_5078f26ad4a1', '2026-06-17 21:11:54', '2026-06-18 20:22:31'),
(29, 4, 1, 1, 'imara', 'ruiru', 1.23000000, 1.45000000, 1.50000000, 1.50000000, 789.00, 0.00, 0.00, 0.00, 0.00, 789.00, 'refunded', 'cancelled', 'KONG_P_1c7bc194d829', '2026-06-17 21:12:59', '2026-06-18 17:24:55'),
(30, 4, 2, 1, 'Pinned Location (-1.26945, 36.86155)', 'Pinned Location (-1.29073, 36.84610)', -1.26944636, 36.86155378, -1.29072706, 36.84610425, 5.11, 0.00, 0.00, 0.00, 0.00, 113.31, 'paid', 'cancelled', 'KONG_P_7df362376a1f', '2026-06-17 21:13:45', '2026-06-17 21:17:53'),
(31, 4, 1, 1, 'Pinned Location (-1.26498, 36.74929)', 'Pinned Location (-1.28489, 36.84988)', -1.26498426, 36.74928724, -1.28489205, 36.84988080, 14.40, 50.00, 2.61, 7.20, 50.00, 131.40, 'paid', 'completed', 'KONG_P_359421c9558d', '2026-06-18 19:44:30', '2026-06-18 20:12:02'),
(32, 4, 2, 1, 'Pinned Location (-1.26910, 36.78637)', 'Pinned Location (-1.29450, 36.86293)', -1.26910312, 36.78636609, -1.29450265, 36.86292707, 13.09, 50.00, 2.71, 7.85, 50.00, 134.12, 'paid', 'pending', 'KONG_P_748c1780792e', '2026-06-18 20:24:46', '2026-06-18 20:24:46');

-- --------------------------------------------------------

--
-- Table structure for table `ci_sessions`
--

CREATE TABLE `ci_sessions` (
  `id` varchar(128) COLLATE utf8mb4_general_ci NOT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_general_ci NOT NULL,
  `timestamp` int UNSIGNED NOT NULL DEFAULT '0',
  `data` mediumblob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `id` int UNSIGNED NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `license_number` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `allowance_flat_rate` decimal(10,2) NOT NULL,
  `status` enum('available','on_trip','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'available',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `drivers`
--

INSERT INTO `drivers` (`id`, `user_id`, `license_number`, `allowance_flat_rate`, `status`, `created_at`, `updated_at`) VALUES
(1, 3, 'DL-998877', 50.00, 'on_trip', '2026-06-17 13:02:00', '2026-06-18 20:24:46');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_rates`
--

CREATE TABLE `fuel_rates` (
  `id` int UNSIGNED NOT NULL,
  `price_per_liter` decimal(10,2) NOT NULL,
  `updated_by` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `fuel_type` enum('petrol','diesel') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'petrol'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_rates`
--

INSERT INTO `fuel_rates` (`id`, `price_per_liter`, `updated_by`, `created_at`, `fuel_type`) VALUES
(1, 1.45, 2, '2026-06-17 13:02:00', 'petrol');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` bigint UNSIGNED NOT NULL,
  `version` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `class` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `group` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `namespace` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `time` int NOT NULL,
  `batch` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `version`, `class`, `group`, `namespace`, `time`, `batch`) VALUES
(1, '2026-06-17-000000', 'App\\Database\\Migrations\\CreateSystemTables', 'default', 'App', 1781701250, 1),
(2, '2026-06-17-010000', 'App\\Database\\Migrations\\AlterBookingsPaymentStatus', 'default', 'App', 1781718541, 2),
(3, '2026-06-17-020000', 'App\\Database\\Migrations\\AlterUsersAddAuthFields', 'default', 'App', 1781723042, 3),
(4, '2026-06-18-000000', 'App\\Database\\Migrations\\AddFuelTypeSupport', 'default', 'App', 1781813153, 4);

-- --------------------------------------------------------

--
-- Table structure for table `trip_coordinates`
--

CREATE TABLE `trip_coordinates` (
  `id` int UNSIGNED NOT NULL,
  `booking_id` int UNSIGNED NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `trip_coordinates`
--

INSERT INTO `trip_coordinates` (`id`, `booking_id`, `latitude`, `longitude`, `created_at`) VALUES
(1, 5, -1.32389021, 36.87806424, '2026-06-17 13:53:41'),
(2, 5, -1.32390313, 36.87803922, '2026-06-17 13:58:22'),
(3, 5, -1.32393425, 36.87801977, '2026-06-17 14:03:25'),
(4, 5, -1.32391728, 36.87803691, '2026-06-17 14:04:25'),
(5, 5, -1.32389021, 36.87806424, '2026-06-17 14:11:39'),
(6, 5, -1.32388059, 36.87805880, '2026-06-17 14:12:51'),
(7, 5, -1.32390723, 36.87801621, '2026-06-17 14:17:12'),
(8, 5, -1.32385511, 36.87804829, '2026-06-17 14:18:22'),
(9, 5, -1.32384756, 36.87811300, '2026-06-17 14:37:06'),
(10, 5, -1.32386136, 36.87807122, '2026-06-17 14:38:54'),
(11, 5, -1.32388378, 36.87805206, '2026-06-17 14:41:01'),
(12, 5, -1.32389391, 36.87803562, '2026-06-17 14:50:35'),
(13, 5, -1.32394469, 36.87795430, '2026-06-17 15:25:10'),
(14, 5, -1.32393682, 36.87800257, '2026-06-17 15:29:29'),
(15, 5, -1.32390775, 36.87798891, '2026-06-17 15:32:38'),
(16, 16, -1.32386943, 36.87805141, '2026-06-17 17:10:53'),
(17, 16, -1.32381655, 36.87813565, '2026-06-17 17:17:40'),
(18, 16, -1.32386365, 36.87796176, '2026-06-17 17:19:25'),
(19, 16, -1.32386670, 36.87806444, '2026-06-17 17:23:15'),
(20, 16, -1.32386657, 36.87805868, '2026-06-17 17:24:15'),
(21, 16, -1.32388138, 36.87808565, '2026-06-17 17:38:46'),
(22, 20, -1.32394877, 36.87793856, '2026-06-17 19:35:10'),
(23, 31, -1.32388191, 36.87803503, '2026-06-18 20:12:01'),
(24, 28, -1.32386123, 36.87814071, '2026-06-18 20:13:08'),
(25, 28, -1.32386129, 36.87814085, '2026-06-18 20:14:30'),
(26, 28, -1.32381657, 36.87817252, '2026-06-18 20:17:17'),
(27, 28, -1.32381657, 36.87817253, '2026-06-18 20:20:58'),
(28, 28, -1.32390227, 36.87801256, '2026-06-18 20:22:25');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `email_verified_at` datetime DEFAULT NULL,
  `verification_token` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token` varchar(64) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `reset_token_expires_at` datetime DEFAULT NULL,
  `first_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `last_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `role` enum('admin','manager','driver','customer') COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `deleted_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `email`, `password_hash`, `email_verified_at`, `verification_token`, `reset_token`, `reset_token_expires_at`, `first_name`, `last_name`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin@kongsafaris.com', '$2y$12$r8gEQEvUJC.iPol1SY.hFu7U5v9.vh7ypoBqeOiGwGpDkU.uO/Q6e', NULL, NULL, NULL, NULL, 'David', 'Kiprotich', 'admin', '2026-06-17 13:01:59', '2026-06-17 13:01:59', NULL),
(2, 'manager@kongsafaris.com', '$2y$12$r9oeqQw8rxkqjtjgzOQDGuRAUlp1qpqrPoxzD6HeAbfjwp9uDNKrS', NULL, NULL, NULL, NULL, 'Sarah', 'Wanjiku', 'manager', '2026-06-17 13:01:59', '2026-06-17 13:01:59', NULL),
(3, 'driver@kongsafaris.com', '$2y$12$ccnax7xwLA0Co86.tmfCkuZiPsuECtNOUH57iIHbL42FxGjIctbde', NULL, NULL, NULL, NULL, 'John', 'Ouma', 'driver', '2026-06-17 13:02:00', '2026-06-17 13:02:00', NULL),
(4, 'customer@kongsafaris.com', '$2y$12$hqZs/9gnfoGq7E07.RH80uKzvmBWzYAJPQfi2sJO4gQxxONp.Z9py', NULL, NULL, NULL, NULL, 'Mark', 'Smith', 'customer', '2026-06-17 13:02:00', '2026-06-17 13:02:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `id` int UNSIGNED NOT NULL,
  `plate_number` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `model` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `fuel_efficiency` decimal(10,2) NOT NULL,
  `target_profit_margin_per_km` decimal(10,2) NOT NULL,
  `maintenance_reserve_per_km` decimal(10,2) NOT NULL,
  `status` enum('active','maintenance','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `fuel_type` enum('petrol','diesel') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'petrol'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `plate_number`, `model`, `fuel_efficiency`, `target_profit_margin_per_km`, `maintenance_reserve_per_km`, `status`, `created_at`, `updated_at`, `fuel_type`) VALUES
(1, 'KAA 123A', 'Toyota Land Cruiser Safari 4x4', 8.00, 1.50, 0.50, 'active', '2026-06-17 13:02:00', '2026-06-17 13:02:00', 'petrol'),
(2, 'KAB 456B', 'Nissan Patrol Safari Caravan', 7.00, 1.80, 0.60, 'active', '2026-06-17 13:02:00', '2026-06-17 13:02:00', 'petrol');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `vehicle_id` (`vehicle_id`),
  ADD KEY `driver_id` (`driver_id`),
  ADD KEY `payment_status` (`payment_status`),
  ADD KEY `trip_status` (`trip_status`),
  ADD KEY `paystack_reference` (`paystack_reference`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `ci_sessions`
--
ALTER TABLE `ci_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `timestamp` (`timestamp`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `license_number` (`license_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `fuel_rates`
--
ALTER TABLE `fuel_rates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `updated_by` (`updated_by`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `trip_coordinates`
--
ALTER TABLE `trip_coordinates`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`),
  ADD KEY `booking_id_created_at` (`booking_id`,`created_at`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `role` (`role`),
  ADD KEY `created_at` (`created_at`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plate_number` (`plate_number`),
  ADD KEY `status` (`status`),
  ADD KEY `created_at` (`created_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `fuel_rates`
--
ALTER TABLE `fuel_rates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `trip_coordinates`
--
ALTER TABLE `trip_coordinates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `bookings_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE SET NULL,
  ADD CONSTRAINT `bookings_driver_id_foreign` FOREIGN KEY (`driver_id`) REFERENCES `drivers` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `bookings_vehicle_id_foreign` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `fuel_rates`
--
ALTER TABLE `fuel_rates`
  ADD CONSTRAINT `fuel_rates_updated_by_foreign` FOREIGN KEY (`updated_by`) REFERENCES `users` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Constraints for table `trip_coordinates`
--
ALTER TABLE `trip_coordinates`
  ADD CONSTRAINT `trip_coordinates_booking_id_foreign` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`) ON DELETE RESTRICT ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
