-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 17, 2026 at 02:44 PM
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
  `payment_status` enum('pending','paid','failed','manual_verified') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `trip_status` enum('pending','active','completed','cancelled') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `paystack_reference` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `customer_id`, `vehicle_id`, `driver_id`, `pickup_address`, `dropoff_address`, `pickup_latitude`, `pickup_longitude`, `dropoff_latitude`, `dropoff_longitude`, `distance_km`, `base_booking_fee`, `per_km_fuel_cost`, `maintenance_reserve`, `driver_allowance`, `total_price`, `payment_status`, `trip_status`, `paystack_reference`, `created_at`, `updated_at`) VALUES
(5, 4, 1, 1, 'Imara Daima, Nairobi, Kenya', 'Maasai Mara National Reserve, Kenya', -1.32843600, 36.88064990, -1.48213240, 35.12998960, 272.25, 50.00, 49.34, 136.12, 50.00, 693.84, 'paid', 'active', 'KONG_M5_d8263fbc', '2026-06-17 13:50:30', '2026-06-17 13:53:37');

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

--
-- Dumping data for table `ci_sessions`
--

INSERT INTO `ci_sessions` (`id`, `ip_address`, `timestamp`, `data`) VALUES
('ci_session:2865dd2b029a982c2f3bfabebf0ca1d4', '::1', 4294967295, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313730363030363b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73743a383038352f696e6465782e7068702f74726970732f71756f7465223b7573657249647c693a343b656d61696c7c733a32343a22637573746f6d6572406b6f6e67736166617269732e636f6d223b66697273745f6e616d657c733a343a224d61726b223b6c6173745f6e616d657c733a353a22536d697468223b726f6c657c733a383a22637573746f6d6572223b69734c6f67676564496e7c623a313b5f5f63695f766172737c613a303a7b7d),
('ci_session:70332a33dc0cd97c95b47b3cab5e4b74', '::1', 4294967295, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313730323334373b5f63695f70726576696f75735f75726c7c733a34323a22687474703a2f2f6c6f63616c686f73743a383038352f696e6465782e7068702f617574682f6c6f67696e223b7573657249647c693a323b656d61696c7c733a32333a226d616e61676572406b6f6e67736166617269732e636f6d223b66697273745f6e616d657c733a353a225361726168223b6c6173745f6e616d657c733a373a2257616e6a696b75223b726f6c657c733a373a226d616e61676572223b69734c6f67676564496e7c623a313b),
('ci_session:e37bce7507ec378897e42a096c682976', '::1', 4294967295, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313730373136353b5f63695f70726576696f75735f75726c7c733a34343a22687474703a2f2f6c6f63616c686f73743a383038352f696e6465782e7068702f74726970732f647269766572223b7573657249647c693a333b656d61696c7c733a32323a22647269766572406b6f6e67736166617269732e636f6d223b66697273745f6e616d657c733a343a224a6f686e223b6c6173745f6e616d657c733a343a224f756d61223b726f6c657c733a363a22647269766572223b69734c6f67676564496e7c623a313b5f5f63695f766172737c613a303a7b7d),
('ci_session:e4cc81f703a2b93aae25aab2cf550684', '::1', 4294967295, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313730333038373b5f63695f70726576696f75735f75726c7c733a34333a22687474703a2f2f6c6f63616c686f73743a383038352f696e6465782e7068702f74726970732f71756f7465223b7573657249647c693a343b656d61696c7c733a32343a22637573746f6d6572406b6f6e67736166617269732e636f6d223b66697273745f6e616d657c733a343a224d61726b223b6c6173745f6e616d657c733a353a22536d697468223b726f6c657c733a383a22637573746f6d6572223b69734c6f67676564496e7c623a313b),
('ci_session:f9e8039be88ae93b93c332f95581c207', '::1', 4294967295, 0x5f5f63695f6c6173745f726567656e65726174657c693a313738313730343638383b5f63695f70726576696f75735f75726c7c733a34343a22687474703a2f2f6c6f63616c686f73743a383038352f696e6465782e7068702f74726970732f647269766572223b7573657249647c693a333b656d61696c7c733a32323a22647269766572406b6f6e67736166617269732e636f6d223b66697273745f6e616d657c733a343a224a6f686e223b6c6173745f6e616d657c733a343a224f756d61223b726f6c657c733a363a22647269766572223b69734c6f67676564496e7c623a313b5f5f63695f766172737c613a303a7b7d);

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
(1, 3, 'DL-998877', 50.00, 'on_trip', '2026-06-17 13:02:00', '2026-06-17 13:50:30');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_rates`
--

CREATE TABLE `fuel_rates` (
  `id` int UNSIGNED NOT NULL,
  `price_per_liter` decimal(10,2) NOT NULL,
  `updated_by` int UNSIGNED NOT NULL,
  `created_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `fuel_rates`
--

INSERT INTO `fuel_rates` (`id`, `price_per_liter`, `updated_by`, `created_at`) VALUES
(1, 1.45, 2, '2026-06-17 13:02:00');

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
(1, '2026-06-17-000000', 'App\\Database\\Migrations\\CreateSystemTables', 'default', 'App', 1781701250, 1);

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
(11, 5, -1.32388378, 36.87805206, '2026-06-17 14:41:01');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int UNSIGNED NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password_hash` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
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

INSERT INTO `users` (`id`, `email`, `password_hash`, `first_name`, `last_name`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin@kongsafaris.com', '$2y$12$r8gEQEvUJC.iPol1SY.hFu7U5v9.vh7ypoBqeOiGwGpDkU.uO/Q6e', 'David', 'Kiprotich', 'admin', '2026-06-17 13:01:59', '2026-06-17 13:01:59', NULL),
(2, 'manager@kongsafaris.com', '$2y$12$r9oeqQw8rxkqjtjgzOQDGuRAUlp1qpqrPoxzD6HeAbfjwp9uDNKrS', 'Sarah', 'Wanjiku', 'manager', '2026-06-17 13:01:59', '2026-06-17 13:01:59', NULL),
(3, 'driver@kongsafaris.com', '$2y$12$ccnax7xwLA0Co86.tmfCkuZiPsuECtNOUH57iIHbL42FxGjIctbde', 'John', 'Ouma', 'driver', '2026-06-17 13:02:00', '2026-06-17 13:02:00', NULL),
(4, 'customer@kongsafaris.com', '$2y$12$hqZs/9gnfoGq7E07.RH80uKzvmBWzYAJPQfi2sJO4gQxxONp.Z9py', 'Mark', 'Smith', 'customer', '2026-06-17 13:02:00', '2026-06-17 13:02:00', NULL);

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
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `plate_number`, `model`, `fuel_efficiency`, `target_profit_margin_per_km`, `maintenance_reserve_per_km`, `status`, `created_at`, `updated_at`) VALUES
(1, 'KAA 123A', 'Toyota Land Cruiser Safari 4x4', 8.00, 1.50, 0.50, 'active', '2026-06-17 13:02:00', '2026-06-17 13:02:00'),
(2, 'KAB 456B', 'Nissan Patrol Safari Caravan', 7.00, 1.80, 0.60, 'active', '2026-06-17 13:02:00', '2026-06-17 13:02:00');

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `trip_coordinates`
--
ALTER TABLE `trip_coordinates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

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
