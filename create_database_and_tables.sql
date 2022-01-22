-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 22, 2022 at 10:57 AM
-- Server version: 5.7.34
-- PHP Version: 8.0.8
SET
    SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

START TRANSACTION;

SET
    time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */
;

/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */
;

/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */
;

/*!40101 SET NAMES utf8mb4 */
;

--
-- Database: `flexnet`
--
CREATE DATABASE IF NOT EXISTS `flexnet` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `flexnet`;

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
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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
    `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `initials` varchar(8) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `password_hash` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
    `deleted` tinyint(1) UNSIGNED NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `edited_at` timestamp NULL DEFAULT NULL
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

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

-- --------------------------------------------------------
--
-- Table structure for table `articles`
--
DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles` (
    `id` mediumint(8) UNSIGNED NOT NULL,
    `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `subtitle` text COLLATE utf8mb4_unicode_ci,
    `bodytext` mediumtext COLLATE utf8mb4_unicode_ci,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `edited_at` timestamp NULL DEFAULT NULL,
    `created_by` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
    `edited_by` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `articles`
--
INSERT INTO
    `articles` (
        `id`,
        `title`,
        `subtitle`,
        `bodytext`,
        `created_at`,
        `edited_at`,
        `created_by`,
        `edited_by`
    )
VALUES
    (
        1,
        'This is the title',
        'This is the subtitle',
        '<p>\r\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n	</p>\r\n	<p>\r\n	Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.\r\n	</p>',
        '2022-01-22 09:46:38',
        NULL,
        2,
        2
    );

-- --------------------------------------------------------
--
-- Table structure for table `article_category_rel`
--
DROP TABLE IF EXISTS `article_category_rel`;

CREATE TABLE `article_category_rel` (
    `id` mediumint(8) UNSIGNED NOT NULL,
    `article_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
    `category_id` mediumint(8) UNSIGNED NOT NULL DEFAULT '0',
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `article_category_rel`
--
INSERT INTO
    `article_category_rel` (`id`, `article_id`, `category_id`, `created_at`)
VALUES
    (8, 1, 5, '2022-01-22 10:43:26'),
    (9, 1, 1, '2022-01-22 10:43:30');

-- --------------------------------------------------------
--
-- Table structure for table `categories`
--
DROP TABLE IF EXISTS `categories`;

CREATE TABLE `categories` (
    `id` mediumint(8) UNSIGNED NOT NULL,
    `name` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `description` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
    `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
    `created_by` mediumint(8) UNSIGNED NOT NULL DEFAULT '0'
) ENGINE = InnoDB DEFAULT CHARSET = utf8mb4 COLLATE = utf8mb4_unicode_ci;

--
-- Dumping data for table `categories`
--
INSERT INTO
    `categories` (
        `id`,
        `name`,
        `description`,
        `created_at`,
        `created_by`
    )
VALUES
    (
        1,
        'A kind of magic',
        'important',
        '2022-01-22 09:44:53',
        2
    ),
    (
        2,
        'The second category',
        NULL,
        '2022-01-22 09:45:35',
        2
    ),
    (
        5,
        'The fifth category',
        'low key',
        '2022-01-22 09:45:35',
        2
    ),
    (
        9,
        'Some category',
        'important',
        '2022-01-22 09:44:53',
        2
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
-- Indexes for table `articles`
--
ALTER TABLE
    `articles`
ADD
    PRIMARY KEY (`id`);

--
-- Indexes for table `article_category_rel`
--
ALTER TABLE
    `article_category_rel`
ADD
    PRIMARY KEY (`id`);

--
-- Indexes for table `categories`
--
ALTER TABLE
    `categories`
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

--
-- AUTO_INCREMENT for table `articles`
--
ALTER TABLE
    `articles`
MODIFY
    `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 2;

--
-- AUTO_INCREMENT for table `article_category_rel`
--
ALTER TABLE
    `article_category_rel`
MODIFY
    `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 10;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE
    `categories`
MODIFY
    `id` mediumint(8) UNSIGNED NOT NULL AUTO_INCREMENT,
    AUTO_INCREMENT = 10;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */
;

/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */
;

/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */
;