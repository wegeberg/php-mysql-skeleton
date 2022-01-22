-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 22, 2022 at 07:59 AM
-- Server version: 5.7.34
-- PHP Version: 8.0.8
SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

--
-- Database: `flexnet`
--
-- --------------------------------------------------------
--
-- Table structure for table `admin_rights`
--
DROP TABLE IF EXISTS `admin_rights`;

CREATE TABLE `admin_rights` (
    `id` mediumint(8) UNSIGNED NOT NULL,
    `role_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
    `user_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
    `access_right` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

--
-- Dumping data for table `admin_rights`
--
INSERT INTO
    `admin_rights` (
        `id`,
        `role_id`,
        `user_id`,
        `access_right`,
        `created_at`
    )
VALUES
    (1, 1, 1, 0, '2022-01-22 07:45:35'),
    (2, 2, 2, 0, '2022-01-22 07:45:35');

-- --------------------------------------------------------
--
-- Table structure for table `admin_roles`
--
DROP TABLE IF EXISTS `admin_roles`;

CREATE TABLE `admin_roles` (
    `id` mediumint(8) UNSIGNED NOT NULL,
    `name` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_roles`
--
INSERT INTO
    `admin_roles` (`id`, `name`)
VALUES
    (1, 'admin'),
    (2, 'journalist');

-- --------------------------------------------------------
--
-- Table structure for table `admin_users`
--
DROP TABLE IF EXISTS `admin_users`;

CREATE TABLE `admin_users` (
    `id` mediumint(8) UNSIGNED NOT NULL,
    `name` varchar(255) DEFAULT NULL,
    `email` varchar(255) DEFAULT NULL,
    `initials` varchar(8) DEFAULT NULL,
    `phone` varchar(20) DEFAULT NULL,
    `password_hash` varchar(255) DEFAULT NULL,
    `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `edited_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8;

--
-- Dumping data for table `admin_users`
--
INSERT INTO
    `admin_users` (
        `id`,
        `name`,
        `email`,
        `initials`,
        `phone`,
        `password_hash`,
        `deleted`,
        `created_at`,
        `edited_at`
    )
VALUES
    (
        1,
        'Flexnet',
        'mail@flexnet.dk',
        'FA',
        '12345678',
        '$2y$10$2QwQVnwHKWwFXhcAAyE2COJbiTh7d1DUnFECgIo6VrHdegPT2PidG',
        0,
        '2022-01-22 07:43:21',
        NULL
    ),
    (
        2,
        'Journalist',
        'journalist@flexnet.dk',
        'FJ',
        '12345678',
        '$2y$10$2QwQVnwHKWwFXhcAAyE2COJbiTh7d1DUnFECgIo6VrHdegPT2PidG',
        0,
        '2022-01-22 07:46:08',
        NULL
    );

--
-- Indexes for dumped tables
--
--
-- Indexes for table `admin_rights`
--
ALTER TABLE
    `admin_rights`
ADD
    PRIMARY KEY (`id`);

--
-- Indexes for table `admin_roles`
--
ALTER TABLE
    `admin_roles`
ADD
    PRIMARY KEY (`id`);

--
-- Indexes for table `admin_users`
--
ALTER TABLE
    `admin_users`
ADD
    PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--
--
-- AUTO_INCREMENT for table `admin_rights`
--
ALTER TABLE
    `admin_rights`
MODIFY
    `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 3;

--
-- AUTO_INCREMENT for table `admin_roles`
--
ALTER TABLE
    `admin_roles`
MODIFY
    `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 3;

--
-- AUTO_INCREMENT for table `admin_users`
--
ALTER TABLE
    `admin_users`
MODIFY
    `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 3;

COMMIT;