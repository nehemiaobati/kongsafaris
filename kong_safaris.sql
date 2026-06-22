-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jun 22, 2026 at 04:20 PM
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
(1, 3, 'DL-998877', 50.00, 'available', '2026-06-22 15:50:52', '2026-06-22 15:50:52'),
(2, 6, 'zxcvbnmmnbvqaz', 500.00, 'available', '2026-06-22 16:05:37', '2026-06-22 16:06:29');

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
(1, 1.45, 2, '2026-06-22 15:50:52', 'petrol');

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
(1, '2026-06-17-000000', 'App\\Database\\Migrations\\CreateSystemTables', 'default', 'App', 1782143311, 1),
(2, '2026-06-17-010000', 'App\\Database\\Migrations\\AlterBookingsPaymentStatus', 'default', 'App', 1782143311, 1),
(3, '2026-06-17-020000', 'App\\Database\\Migrations\\AlterUsersAddAuthFields', 'default', 'App', 1782143311, 1),
(4, '2026-06-18-000000', 'App\\Database\\Migrations\\AddFuelTypeSupport', 'default', 'App', 1782143311, 1),
(5, '2026-06-19-000000', 'App\\Database\\Migrations\\CreateSystemSettings', 'default', 'App', 1782143311, 1),
(6, '2026-06-22-000000', 'App\\Database\\Migrations\\AddVehicleCapacity', 'default', 'App', 1782144996, 2);

-- --------------------------------------------------------

--
-- Table structure for table `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int UNSIGNED NOT NULL,
  `setting_key` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `setting_value` text COLLATE utf8mb4_general_ci,
  `updated_by` int UNSIGNED NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `updated_by`, `updated_at`) VALUES
(1, 'base_booking_fee', '50.00', 1, '2026-06-22 15:48:31'),
(2, 'system_name', 'Kong Safaris', 1, '2026-06-22 15:48:31');

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
(1, 'admin@kongsafaris.com', '$2y$12$ourN6hrx4/Ydq8L/aNctDOOnaISaZDb3fPLA0Fk7Y.8IPpPiphkk6', NULL, NULL, NULL, NULL, 'David', 'Kiprotich', 'admin', '2026-06-22 15:50:51', '2026-06-22 15:50:51', NULL),
(2, 'manager@kongsafaris.com', '$2y$12$.dJ2CBqhly7ZgrkQtQ2Ppuwfzpx3EbKBKx3kFAY6Otniyvc.ZYRo.', NULL, NULL, NULL, NULL, 'Sarah', 'Wanjiku', 'manager', '2026-06-22 15:50:52', '2026-06-22 15:50:52', NULL),
(3, 'driver@kongsafaris.com', '$2y$12$JyXBvJ/eC1bgw/iJn2FUz.MHC5yTkuTO97kv3N77XfsK4niAPII2q', NULL, NULL, NULL, NULL, 'John', 'Ouma', 'driver', '2026-06-22 15:50:52', '2026-06-22 15:50:52', NULL),
(4, 'customer@kongsafaris.com', '$2y$12$zaWnFKh9oxn6AHvbleRO/.dO8IhjFjEC0KfUYbrMqvmTqFopcRfO2', NULL, NULL, NULL, NULL, 'Mark', 'Smith', 'customer', '2026-06-22 15:50:52', '2026-06-22 15:50:52', NULL),
(5, 'test@mail.com', '$2y$12$zaWnFKh9oxn6AHvbleRO/.dO8IhjFjEC0KfUYbrMqvmTqFopcRfO2', NULL, NULL, NULL, NULL, 'test', 'test', 'customer', '2026-06-22 15:52:13', '2026-06-22 16:06:14', '2026-06-22 16:06:14'),
(6, 'testd@mail.com', '$2y$12$TAWQJxDQEHMzg85c94AWbO.Y9EqPFtNOR0rSU/abbTDtm.0xgE7qm', NULL, NULL, NULL, NULL, 'test', 'test', 'driver', '2026-06-22 16:05:37', '2026-06-22 16:05:54', '2026-06-22 16:05:54');

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
  `capacity` int UNSIGNED DEFAULT '4',
  `status` enum('active','maintenance','inactive') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'active',
  `created_at` datetime DEFAULT NULL,
  `updated_at` datetime DEFAULT NULL,
  `fuel_type` enum('petrol','diesel') COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'petrol'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`id`, `plate_number`, `model`, `fuel_efficiency`, `target_profit_margin_per_km`, `maintenance_reserve_per_km`, `capacity`, `status`, `created_at`, `updated_at`, `fuel_type`) VALUES
(1, 'KAA 123A', 'Toyota Land Cruiser Safari 4x4', 8.00, 1.50, 0.50, 4, 'active', '2026-06-22 15:50:52', '2026-06-22 15:50:52', 'petrol'),
(2, 'KAB 456B', 'Nissan Patrol Safari Caravan', 7.00, 1.80, 0.60, 4, 'active', '2026-06-22 15:50:52', '2026-06-22 15:50:52', 'petrol'),
(3, 'KCC', 'BMW', 1.00, 2.00, 3.00, 4, 'active', '2026-06-22 16:03:27', '2026-06-22 16:19:35', 'petrol');

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
-- Indexes for table `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `updated_by` (`updated_by`);

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `fuel_rates`
--
ALTER TABLE `fuel_rates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `trip_coordinates`
--
ALTER TABLE `trip_coordinates`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
