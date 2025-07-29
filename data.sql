-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 28 juil. 2025 à 20:19
-- Version du serveur : 8.0.40
-- Version de PHP : 8.2.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `bdd_fidelem`
--

-- --------------------------------------------------------

--
-- Structure de la table `credit_requests`
--

CREATE TABLE `credit_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `duration` int NOT NULL,
  `purpose` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `additional_details` text COLLATE utf8mb4_unicode_ci,
  `status` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'En attente',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `funding_requests`
--

CREATE TABLE `funding_requests` (
  `id` bigint UNSIGNED NOT NULL,
  `companyName` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `mission` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `vision` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `sector` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `productDescription` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `productStatus` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `amountRequested` decimal(10,2) NOT NULL,
  `useOfFunds` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `businessPlan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(13, '2014_10_12_000000_create_users_table', 1),
(14, '2014_10_12_100000_create_password_reset_tokens_table', 1),
(15, '2019_08_19_000000_create_failed_jobs_table', 1),
(16, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(17, '2025_05_14_114226_add_created_by_to_users_table', 2),
(18, '2025_05_26_162120_create_credit_requests_table', 3),
(19, '2025_07_14_144031_create_funding_requests_table', 4);

-- --------------------------------------------------------

--
-- Structure de la table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Structure de la table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text COLLATE utf8mb4_unicode_ci,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `personal_access_tokens`
--

INSERT INTO `personal_access_tokens` (`id`, `tokenable_type`, `tokenable_id`, `name`, `token`, `abilities`, `last_used_at`, `expires_at`, `created_at`, `updated_at`) VALUES
(1, 'App\\Models\\User', 1, 'AuthToken', '677ed4818251ffc7b7de1806a518eec5280174789ebf52f6ddbce04695e2e4d7', '[\"*\"]', NULL, NULL, '2025-05-12 16:33:12', '2025-05-12 16:33:12'),
(2, 'App\\Models\\User', 1, 'AuthToken', 'b198449e508afe7ff945ae8a81f25c7fcb66b88a5f5ef1faf05c6b5a64766a83', '[\"*\"]', NULL, NULL, '2025-05-13 08:48:31', '2025-05-13 08:48:31'),
(3, 'App\\Models\\User', 1, 'AuthToken', '0e5621e426048adcb833425ea6dd41b23ad39d8481354246b0f0e0e697a444f8', '[\"*\"]', NULL, NULL, '2025-05-13 09:09:18', '2025-05-13 09:09:18'),
(4, 'App\\Models\\User', 1, 'AuthToken', 'f3a48a403df382ffe6ddef66d0d29ce4e3b99ac9b18f3938c29d3254963cf06d', '[\"*\"]', NULL, NULL, '2025-05-13 09:12:58', '2025-05-13 09:12:58'),
(5, 'App\\Models\\User', 1, 'AuthToken', '25362f3088a34a3182bb8991844269eeb77df36ecfcedc324fa714ed626db224', '[\"*\"]', NULL, NULL, '2025-05-13 09:32:45', '2025-05-13 09:32:45'),
(6, 'App\\Models\\User', 1, 'AuthToken', 'f3e9858995dd03c69f6d74c4777b1c36ffa1d4096e9ec0fbfc622c1201bfd317', '[\"*\"]', NULL, NULL, '2025-05-13 09:44:13', '2025-05-13 09:44:13'),
(7, 'App\\Models\\User', 1, 'AuthToken', '7adaee9af460302761aa0b65a71ef14950f2bee6aff6bff9a7de54c6a03158cb', '[\"*\"]', NULL, NULL, '2025-05-13 10:33:18', '2025-05-13 10:33:18'),
(8, 'App\\Models\\User', 1, 'AuthToken', '7c8bb80eaa67228cfc0e8819817a1004da7e680a8291b977ab4816059037f12a', '[\"*\"]', NULL, NULL, '2025-05-13 10:56:24', '2025-05-13 10:56:24'),
(9, 'App\\Models\\User', 1, 'AuthToken', '069d7cdaf239518194791b7805aa71a5fac874cf6358a526232c45cfafcf2f74', '[\"*\"]', NULL, NULL, '2025-05-13 10:56:50', '2025-05-13 10:56:50'),
(10, 'App\\Models\\User', 1, 'AuthToken', '3237cbc2d607fa073ca056b4d6b0e329dcd8b2cffae05e22f237d4a8c6a7579f', '[\"*\"]', NULL, NULL, '2025-05-13 11:11:35', '2025-05-13 11:11:35'),
(11, 'App\\Models\\User', 1, 'AuthToken', '13aca1a9b6680fa9627b78c05725bfcc792a9a9ddb43e4fc4f3547d7d35e0602', '[\"*\"]', NULL, NULL, '2025-05-13 11:26:30', '2025-05-13 11:26:30'),
(12, 'App\\Models\\User', 1, 'AuthToken', 'dedf22e06f071d0863983d33f46bcf21278cd486649798d5c22ee1ae06f98d51', '[\"*\"]', NULL, NULL, '2025-05-13 11:50:34', '2025-05-13 11:50:34'),
(13, 'App\\Models\\User', 1, 'AuthToken', 'a3a874ffb8e70077eeec712e3d5e321d04c1048788f05a0a01e911942b3bfcc9', '[\"*\"]', NULL, NULL, '2025-05-13 11:53:54', '2025-05-13 11:53:54'),
(14, 'App\\Models\\User', 1, 'AuthToken', '38c85b8f7b92d7889d7bb51e4166bf3312ba58d1133d49a66fbbde4e5287bc72', '[\"*\"]', NULL, NULL, '2025-05-13 12:02:34', '2025-05-13 12:02:34'),
(15, 'App\\Models\\User', 1, 'AuthToken', 'cd2b368ddba023f47fbabb7f7e767574c57d66b05d4a1fa368a7b85c713296a6', '[\"*\"]', NULL, NULL, '2025-05-13 12:05:29', '2025-05-13 12:05:29'),
(16, 'App\\Models\\User', 1, 'AuthToken', '0092be8caec818506a7ffb5cbc7e1c2b6d0aa86e34ff69287a4ce319c0ac7b84', '[\"*\"]', NULL, NULL, '2025-05-13 12:19:13', '2025-05-13 12:19:13'),
(17, 'App\\Models\\User', 1, 'AuthToken', 'a91764e0ab6b84a7cc8e461bd39ee3d90f36f8cae3bac40dc9be6e17a8d4f04f', '[\"*\"]', NULL, NULL, '2025-05-13 12:32:11', '2025-05-13 12:32:11'),
(18, 'App\\Models\\User', 1, 'AuthToken', '3a6b0f92bb29437ff6ddbfeda07df5c24c183354e2dd8af25371fdb32d039207', '[\"*\"]', NULL, NULL, '2025-05-13 12:35:48', '2025-05-13 12:35:48'),
(19, 'App\\Models\\User', 1, 'AuthToken', 'cf5332f9bf9f50fa2fdea07a5b95884e1396c110b8c3c90b99437ce5b9c18f96', '[\"*\"]', NULL, NULL, '2025-05-13 12:55:12', '2025-05-13 12:55:12'),
(20, 'App\\Models\\User', 1, 'AuthToken', '411de03df44290eaa4519d8633a7027c3db28b782b7b239b9b513bad933b4793', '[\"*\"]', NULL, NULL, '2025-05-13 13:31:12', '2025-05-13 13:31:12'),
(21, 'App\\Models\\User', 1, 'AuthToken', 'c65dd3b4bca20c9d5362caab68b57c7210aaa6b53cf26e29b845c5d1947b41f4', '[\"*\"]', NULL, NULL, '2025-05-13 14:45:15', '2025-05-13 14:45:15'),
(22, 'App\\Models\\User', 1, 'AuthToken', '759599407470eca7493a2297a3641652639c8ef365697e24f23d4fe2cfea122c', '[\"*\"]', NULL, NULL, '2025-05-14 09:08:58', '2025-05-14 09:08:58'),
(23, 'App\\Models\\User', 1, 'AuthToken', '0230ef51909324e16e8dc80a7f1c5ac9396edf4bbe1ea7679320efcadcf86cf5', '[\"*\"]', NULL, NULL, '2025-05-14 09:25:55', '2025-05-14 09:25:55'),
(24, 'App\\Models\\User', 1, 'AuthToken', '22a514285f0488b8f3ee607509aa6068faaa316b796627b7b35ff6d2a22f6198', '[\"*\"]', NULL, NULL, '2025-05-14 09:44:32', '2025-05-14 09:44:32'),
(25, 'App\\Models\\User', 1, 'AuthToken', '529dae5e7bf30e14c1b6beb7e104fb3ca73e13a47090dab17c075526f7758fc5', '[\"*\"]', NULL, NULL, '2025-05-14 10:05:21', '2025-05-14 10:05:21'),
(26, 'App\\Models\\User', 1, 'AuthToken', 'a0724e40125a37329f29a9dfc427fce89721f9ec48ce1c9701a60a5c16203164', '[\"*\"]', NULL, NULL, '2025-05-14 11:14:36', '2025-05-14 11:14:36'),
(27, 'App\\Models\\User', 1, 'AuthToken', '2590d6140852f04bf9487afae1fa65c8de72055f29384eda8919d24b61ba0555', '[\"*\"]', NULL, NULL, '2025-05-14 11:25:39', '2025-05-14 11:25:39'),
(28, 'App\\Models\\User', 1, 'AuthToken', '9fcc2c79ade65593b0b4f906e34565cac924fcbde5c2d36b5133db9bc2dbd77c', '[\"*\"]', NULL, NULL, '2025-05-14 11:31:30', '2025-05-14 11:31:30'),
(29, 'App\\Models\\User', 1, 'AuthToken', '9448a2035691515cfb290f9565ac0b9c94934d67fc83f87d8a6f7eac855defca', '[\"*\"]', NULL, NULL, '2025-05-14 11:35:23', '2025-05-14 11:35:23'),
(30, 'App\\Models\\User', 1, 'AuthToken', 'a5ca63355ea07efce86b5689b77959e9808c139c1369ee47023bc6dc3ad58958', '[\"*\"]', NULL, NULL, '2025-05-14 12:06:01', '2025-05-14 12:06:01'),
(31, 'App\\Models\\User', 1, 'AuthToken', '327c378a66fde6afe005d42bd7ec5d9a5bb7bf74efaa71d419638956e3dfde1b', '[\"*\"]', NULL, NULL, '2025-05-14 12:11:12', '2025-05-14 12:11:12'),
(32, 'App\\Models\\User', 1, 'AuthToken', '75390937899e879e7598b93d6941a6759e0dd2b15c700503e69921b252cce327', '[\"*\"]', NULL, NULL, '2025-05-14 12:14:24', '2025-05-14 12:14:24'),
(33, 'App\\Models\\User', 1, 'AuthToken', 'e57acb2088dc72a20d0bdf51ad9a6c73f43f1e06f767457320611ffbf1a41254', '[\"*\"]', NULL, NULL, '2025-05-14 12:23:39', '2025-05-14 12:23:39'),
(34, 'App\\Models\\User', 1, 'AuthToken', '23e03bdd8fb7313980d6322512f138de629e5dcfd036129c60c705262606a3e3', '[\"*\"]', NULL, NULL, '2025-05-14 12:26:08', '2025-05-14 12:26:08'),
(35, 'App\\Models\\User', 1, 'AuthToken', '81fa7193716cd188d686be109995a6bea7b5353d1d174eab65e5447853941d77', '[\"*\"]', NULL, NULL, '2025-05-14 14:42:33', '2025-05-14 14:42:33'),
(36, 'App\\Models\\User', 1, 'AuthToken', '59d84a7d2253072c5bd27fabd2dc2ec1166abb8907d9694702bc92fedbe128e0', '[\"*\"]', NULL, NULL, '2025-05-14 14:46:18', '2025-05-14 14:46:18'),
(37, 'App\\Models\\User', 1, 'AuthToken', '6da873027993195a43bcf9814acf7158524e28d6e95151c6dc0d4535666d6999', '[\"*\"]', NULL, NULL, '2025-05-14 14:50:59', '2025-05-14 14:50:59'),
(38, 'App\\Models\\User', 1, 'AuthToken', '85a9c8ef18bfb08a7be1cb393a2cd0fc11908723a822d9c7e219176f400b93b1', '[\"*\"]', NULL, NULL, '2025-05-14 15:28:09', '2025-05-14 15:28:09'),
(39, 'App\\Models\\User', 1, 'AuthToken', '3c979be16ed52f1cdd69416b72988b2ce56017ddf348604e3211736adbb0c90c', '[\"*\"]', NULL, NULL, '2025-05-14 15:30:30', '2025-05-14 15:30:30'),
(40, 'App\\Models\\User', 1, 'AuthToken', 'a67541901922a88941270baa194c850333dffce696163bd3c8300dad232c1701', '[\"*\"]', NULL, NULL, '2025-05-14 15:31:22', '2025-05-14 15:31:22'),
(41, 'App\\Models\\User', 1, 'AuthToken', '935955581e5b9b193d8a4a7ab68433d22100bad5b169b22921efade133ae4b86', '[\"*\"]', NULL, NULL, '2025-05-14 15:35:09', '2025-05-14 15:35:09'),
(42, 'App\\Models\\User', 1, 'AuthToken', '736a8c267a480dab8fba9c41c147f441c5203c9ae468c38d7cb2471e25278399', '[\"*\"]', NULL, NULL, '2025-05-14 15:38:38', '2025-05-14 15:38:38'),
(43, 'App\\Models\\User', 1, 'AuthToken', '32f1aac25a601c3bd9b21799e39ea7c5892e45587d1b031a656a2ed75dea524a', '[\"*\"]', NULL, NULL, '2025-05-14 15:39:44', '2025-05-14 15:39:44'),
(44, 'App\\Models\\User', 1, 'AuthToken', '50a032c03f3555400b4d0b08236e8ba4893bb3f3aa3db4cb008af71f6aac52f8', '[\"*\"]', NULL, NULL, '2025-05-14 15:52:51', '2025-05-14 15:52:51'),
(45, 'App\\Models\\User', 1, 'AuthToken', '85fcdd8c9ffab64d195966a083741d0d24e55725d86e17a7dc4f8c100cbbce5b', '[\"*\"]', NULL, NULL, '2025-05-14 15:54:08', '2025-05-14 15:54:08'),
(46, 'App\\Models\\User', 1, 'AuthToken', '574fd41e85d1567ed2ddf61714bed71e3b21fa763dfab48a11c4f5063f1caa47', '[\"*\"]', NULL, NULL, '2025-05-14 15:56:04', '2025-05-14 15:56:04'),
(47, 'App\\Models\\User', 1, 'AuthToken', 'a6da14e2b420dcdf3fc9b71cd136c896a89ae5a0b4c65ff567d8ae5fcdabadb8', '[\"*\"]', NULL, NULL, '2025-05-14 16:12:41', '2025-05-14 16:12:41'),
(48, 'App\\Models\\User', 1, 'AuthToken', 'c3245d427ec06fcedc51928dd5e13e7d085e2c458315ba483a31e9f3aee7dcdf', '[\"*\"]', NULL, NULL, '2025-05-14 16:31:24', '2025-05-14 16:31:24'),
(49, 'App\\Models\\User', 1, 'AuthToken', 'e775dccbb0e857efb9a9b2e3741ebe8664a5dbe65006ef742d5d4d9379920cca', '[\"*\"]', NULL, NULL, '2025-05-14 16:34:42', '2025-05-14 16:34:42'),
(50, 'App\\Models\\User', 1, 'AuthToken', '3fe612736b4965457d34310279b7911a29dfa7c8ca122ea2eb3d166ca9d5c982', '[\"*\"]', NULL, NULL, '2025-05-14 16:38:01', '2025-05-14 16:38:01'),
(51, 'App\\Models\\User', 1, 'AuthToken', '33bdfb4a5b953ef2ab1ecfcbaab40c6921058cfa54c7d5213584c2bd022e2e6d', '[\"*\"]', NULL, NULL, '2025-05-14 17:34:18', '2025-05-14 17:34:18'),
(52, 'App\\Models\\User', 1, 'AuthToken', '82a694a19e2ad3b44cf0fd5f9caa0b2c1e3faa8e2d0d5e99cd37cca60bc20140', '[\"*\"]', NULL, NULL, '2025-05-14 17:48:44', '2025-05-14 17:48:44'),
(53, 'App\\Models\\User', 1, 'AuthToken', '37d1836b5a16f41ea9dbf93e42df766de3053393c1ef1e2efe6a0035c44ca072', '[\"*\"]', NULL, NULL, '2025-05-14 17:55:49', '2025-05-14 17:55:49'),
(54, 'App\\Models\\User', 1, 'AuthToken', 'aaa313ccb6be42307c52ced031c9baa714efee8db65c4f72899b07162112abb9', '[\"*\"]', NULL, NULL, '2025-05-14 18:05:10', '2025-05-14 18:05:10'),
(55, 'App\\Models\\User', 1, 'AuthToken', '657701e2c22f3e92117268ab4e6150400dc63c422498c9e6fd6e16fc27c6da2b', '[\"*\"]', NULL, NULL, '2025-05-14 18:15:08', '2025-05-14 18:15:08'),
(56, 'App\\Models\\User', 1, 'AuthToken', 'd8a3b215ff88f087cba8b260777d2dfef225baa129f88f19cacb7c916c21c857', '[\"*\"]', NULL, NULL, '2025-05-14 18:15:52', '2025-05-14 18:15:52'),
(57, 'App\\Models\\User', 1, 'AuthToken', 'a7959069b5ee06473618c3131f3760f465892778bb3361ca27235a9d7c43ec50', '[\"*\"]', NULL, NULL, '2025-05-14 18:24:40', '2025-05-14 18:24:40'),
(58, 'App\\Models\\User', 1, 'AuthToken', '1a8eec9ef0cdca44b0d28e50e4fca9cefa5c4e297a6c957e2c68e76f2874e50b', '[\"*\"]', NULL, NULL, '2025-05-14 18:25:18', '2025-05-14 18:25:18'),
(59, 'App\\Models\\User', 1, 'AuthToken', 'a19521f1d4b63fb98ba09b1b651cd812e8ad91749c43b26ec42bced662234772', '[\"*\"]', NULL, NULL, '2025-05-16 12:59:15', '2025-05-16 12:59:15'),
(60, 'App\\Models\\User', 1, 'AuthToken', 'e1c305391d6db01728c993aa9a2a9fbf315a0b4931ca640815fa7215a1516294', '[\"*\"]', NULL, NULL, '2025-05-16 13:00:18', '2025-05-16 13:00:18'),
(61, 'App\\Models\\User', 1, 'AuthToken', 'eb17aa49235244bb2aaabcc4888e849d465cb20cd15b3271bf28401fee08c815', '[\"*\"]', NULL, NULL, '2025-05-16 13:07:34', '2025-05-16 13:07:34'),
(62, 'App\\Models\\User', 1, 'AuthToken', '6f831d82df5976df1ec9e958f37fa020e3e7f2b1a3f6b2ca1232503ae826be47', '[\"*\"]', NULL, NULL, '2025-05-16 13:16:18', '2025-05-16 13:16:18'),
(63, 'App\\Models\\User', 1, 'AuthToken', 'd481f37205d078a2f341cdef8a66e75aee505059a4f54d9a0c848289e159ddff', '[\"*\"]', NULL, NULL, '2025-05-26 09:17:20', '2025-05-26 09:17:20'),
(64, 'App\\Models\\User', 1, 'AuthToken', 'd125a47aee24c5d548c997a777c9ce6a704f0a8d9b392eb064c227a68b6003b0', '[\"*\"]', NULL, NULL, '2025-05-26 09:42:14', '2025-05-26 09:42:14'),
(65, 'App\\Models\\User', 1, 'AuthToken', 'e67c2d641abc09af234b9735f97e93c60094d7f93a55a781d2ab42823784b840', '[\"*\"]', NULL, NULL, '2025-05-26 10:06:05', '2025-05-26 10:06:05'),
(66, 'App\\Models\\User', 1, 'AuthToken', '4f2a8022eddff09aaf6ddc3ee25451fd0e289c1d6d19617ab89960c5764463f7', '[\"*\"]', NULL, NULL, '2025-05-26 10:08:56', '2025-05-26 10:08:56'),
(67, 'App\\Models\\User', 10, 'AuthToken', '5726ed571ff455b7e76d1072fdbcca514995b450a9aa5027d921c2d2f4ab9c7d', '[\"*\"]', NULL, NULL, '2025-05-26 10:10:52', '2025-05-26 10:10:52'),
(68, 'App\\Models\\User', 10, 'AuthToken', '6da14eb49fed1eda9b9a42bcb774fa0cb6e848a3612512a9e7f8b86ce6a09628', '[\"*\"]', NULL, NULL, '2025-05-26 10:34:00', '2025-05-26 10:34:00'),
(69, 'App\\Models\\User', 1, 'AuthToken', 'ed08a20072caa068b86fd4005a0c3a3fe45e412b4594f59ac03d0ce442646f44', '[\"*\"]', NULL, NULL, '2025-05-26 10:35:06', '2025-05-26 10:35:06'),
(70, 'App\\Models\\User', 1, 'AuthToken', 'c6817e1f0a72574c8e4b18d13d4a0e081fa7d37100d0c29e12ee9c6c925fbf93', '[\"*\"]', NULL, NULL, '2025-05-26 10:39:38', '2025-05-26 10:39:38'),
(71, 'App\\Models\\User', 10, 'AuthToken', 'a010fb914b9ea59d4b7c3789d82e63bdcc5ed653419d2b5c86c4f12ee490d4f3', '[\"*\"]', NULL, NULL, '2025-05-26 10:44:44', '2025-05-26 10:44:44'),
(72, 'App\\Models\\User', 10, 'AuthToken', '360aedf72220c14966f5c79f423b18c70cc0e5f1ea059661db071a07882da5a4', '[\"*\"]', NULL, NULL, '2025-05-26 10:53:29', '2025-05-26 10:53:29'),
(73, 'App\\Models\\User', 10, 'AuthToken', '1007e1cb8af1fb0c513a72bd5a22a263285bdfcc370bda06578634d9d40c5122', '[\"*\"]', NULL, NULL, '2025-05-26 10:56:16', '2025-05-26 10:56:16'),
(74, 'App\\Models\\User', 1, 'AuthToken', '7ceae2c395901dea049d6e519f239ee6e26f377ea962842c55657a69bf3064c0', '[\"*\"]', NULL, NULL, '2025-05-26 10:58:34', '2025-05-26 10:58:34'),
(75, 'App\\Models\\User', 1, 'AuthToken', 'e25588859738a5b48436a3eedc4e03ebda937f56dce0eed87be343aefa58a2f2', '[\"*\"]', NULL, NULL, '2025-05-26 11:00:22', '2025-05-26 11:00:22'),
(76, 'App\\Models\\User', 14, 'AuthToken', '1dce50551719f0a7bb6ece5cba64bc009ea675c5d607fbbc27b910e5337409d6', '[\"*\"]', NULL, NULL, '2025-05-26 11:01:22', '2025-05-26 11:01:22'),
(77, 'App\\Models\\User', 10, 'AuthToken', 'fb16385164a7f9f7a2f6234f1ba38bbea7aff783a7c2cb42c585452e0c19113d', '[\"*\"]', NULL, NULL, '2025-05-26 11:03:02', '2025-05-26 11:03:02'),
(78, 'App\\Models\\User', 11, 'AuthToken', 'e2b3178ce1ff1c80f3196a96421ca5562415944f0807a134afca0558904ae3cd', '[\"*\"]', NULL, NULL, '2025-05-26 12:44:35', '2025-05-26 12:44:35'),
(79, 'App\\Models\\User', 11, 'AuthToken', 'cdb4a8fc464be61c6676d54c097a0b61e647f5ee22415c5438d4508732824d39', '[\"*\"]', NULL, NULL, '2025-05-26 14:40:54', '2025-05-26 14:40:54'),
(80, 'App\\Models\\User', 11, 'AuthToken', 'fbc29fa3065338e05236b08d52ccd6b1009dfd03209238c88de6bfb4948c9d84', '[\"*\"]', NULL, NULL, '2025-05-26 15:51:35', '2025-05-26 15:51:35'),
(81, 'App\\Models\\User', 11, 'AuthToken', '8ba52894fc88f9dfb4ba474ceb37206ec27ab12947c75202cc6765d0e83640b9', '[\"*\"]', NULL, NULL, '2025-05-26 15:52:20', '2025-05-26 15:52:20'),
(82, 'App\\Models\\User', 11, 'AuthToken', 'e09886db07185ae143f23f3a3e6fb0252c58b9cda14315f35b2b1ac4c1d17027', '[\"*\"]', NULL, NULL, '2025-05-26 15:53:06', '2025-05-26 15:53:06'),
(83, 'App\\Models\\User', 11, 'AuthToken', '808c8ce580dfc815cbd4f67d9d33e5cdca0095dbb1f3e2bd2a7f0dfa69468f86', '[\"*\"]', '2025-05-26 16:11:07', NULL, '2025-05-26 16:05:43', '2025-05-26 16:11:07'),
(84, 'App\\Models\\User', 11, 'AuthToken', 'd728e5f7c023592b7c793abd7fc9fb624a38ad23dcfd7f15ff7d0bdc28c9c07c', '[\"*\"]', '2025-05-26 16:21:27', NULL, '2025-05-26 16:21:08', '2025-05-26 16:21:27'),
(85, 'App\\Models\\User', 11, 'AuthToken', 'fc8253021023d0ca5bb9088f7f3ce8c5d28f1254d1581f75bf4230de1374985a', '[\"*\"]', '2025-05-26 17:03:27', NULL, '2025-05-26 16:26:26', '2025-05-26 17:03:27'),
(86, 'App\\Models\\User', 11, 'AuthToken', 'ba5b8c197dff5f36a5dc8b43722a8fd7b1d0036ce83cc70e4d02f8f7177206e4', '[\"*\"]', '2025-05-26 17:31:44', NULL, '2025-05-26 17:31:14', '2025-05-26 17:31:44'),
(87, 'App\\Models\\User', 11, 'AuthToken', 'd75ee87e52942d4709eabfcf8163a29f2f58f3c003737a3e82dd9cf07698edfd', '[\"*\"]', '2025-05-27 08:48:05', NULL, '2025-05-26 17:39:25', '2025-05-27 08:48:05'),
(88, 'App\\Models\\User', 11, 'AuthToken', 'f902ab121e78413aebaa24642fc39382f766a76b78ce93f1bd8dc07580c62c8b', '[\"*\"]', '2025-05-27 08:51:09', NULL, '2025-05-27 08:50:48', '2025-05-27 08:51:09'),
(89, 'App\\Models\\User', 11, 'AuthToken', '77e4f8b074ebf6e983e964f94942a460e0b081d96f88be129e379d042c480646', '[\"*\"]', '2025-05-27 09:02:17', NULL, '2025-05-27 09:01:53', '2025-05-27 09:02:17'),
(90, 'App\\Models\\User', 11, 'AuthToken', '3e65aede6bf3d1722cef834ff648c0d5d36965e392c4e6403c2124de3784557d', '[\"*\"]', '2025-05-27 09:23:45', NULL, '2025-05-27 09:17:27', '2025-05-27 09:23:45'),
(91, 'App\\Models\\User', 11, 'AuthToken', '90c913138e947baecbe6a14744d89113e7a05f18feb692fdb7c380fe7466a067', '[\"*\"]', '2025-05-27 09:46:51', NULL, '2025-05-27 09:38:41', '2025-05-27 09:46:51'),
(92, 'App\\Models\\User', 11, 'AuthToken', 'eaf1bacd0b3e1542db826cea6629dbbbf6c78eae4495b0f37a5f5d4828917a7f', '[\"*\"]', '2025-05-27 10:53:43', NULL, '2025-05-27 09:47:18', '2025-05-27 10:53:43'),
(93, 'App\\Models\\User', 11, 'AuthToken', '2cd6ecfe05598016e042dabd691270bd7b7485abd0b68bd2ce6d23021c3041c4', '[\"*\"]', '2025-05-27 10:54:10', NULL, '2025-05-27 10:54:05', '2025-05-27 10:54:10'),
(94, 'App\\Models\\User', 11, 'AuthToken', '43774051265c143611847c681336dddb65731c7bb778c59a9e8c477d4b4964ec', '[\"*\"]', '2025-05-27 10:54:55', NULL, '2025-05-27 10:54:48', '2025-05-27 10:54:55'),
(95, 'App\\Models\\User', 11, 'AuthToken', 'cf5d008ab2f7758c14cc85786cd20af3520f4ee6d45895e92aa35ebe5997f838', '[\"*\"]', '2025-05-27 10:56:40', NULL, '2025-05-27 10:56:04', '2025-05-27 10:56:40'),
(96, 'App\\Models\\User', 11, 'AuthToken', '4b676e3d80068d52f7c45ca936030f8307a4b4a93f9c3956830b22bac37eb226', '[\"*\"]', '2025-05-27 11:48:59', NULL, '2025-05-27 11:48:51', '2025-05-27 11:48:59'),
(97, 'App\\Models\\User', 11, 'AuthToken', 'b931cdaea358c36b9fb9563edb40bc93a52e1cbc23dfc1df16b9b885a22d8d9c', '[\"*\"]', '2025-05-27 11:51:23', NULL, '2025-05-27 11:51:14', '2025-05-27 11:51:23'),
(98, 'App\\Models\\User', 11, 'AuthToken', 'd22e3b750c479b9af90f0f7b8ad6c43b272b7a9d43cb674c9db841f70196fd2c', '[\"*\"]', '2025-05-27 12:18:18', NULL, '2025-05-27 12:00:24', '2025-05-27 12:18:18'),
(99, 'App\\Models\\User', 11, 'AuthToken', '63d791b4edf5e2b6dec967539d6dc02377ffa377e84a934577f392a5ad1c22a4', '[\"*\"]', '2025-05-27 12:39:42', NULL, '2025-05-27 12:27:46', '2025-05-27 12:39:42'),
(100, 'App\\Models\\User', 11, 'AuthToken', '66a637676f2bf0e1eb85c72ef89cea1c5eb03a4182003d821f0a11b8f66bb2a2', '[\"*\"]', '2025-05-27 12:42:31', NULL, '2025-05-27 12:41:11', '2025-05-27 12:42:31'),
(101, 'App\\Models\\User', 11, 'AuthToken', '3ea174616d42aee7fc9d8e23a402f36024b2354e0170f1b03b12f76f71124f71', '[\"*\"]', '2025-05-27 12:45:40', NULL, '2025-05-27 12:45:34', '2025-05-27 12:45:40'),
(102, 'App\\Models\\User', 11, 'AuthToken', 'ead430e5f4e58311a1cd120f2642f7bed7b7967a4db7ce1956f14050cd0c1f50', '[\"*\"]', '2025-05-30 08:23:36', NULL, '2025-05-27 12:46:38', '2025-05-30 08:23:36'),
(103, 'App\\Models\\User', 11, 'AuthToken', 'd6c64e2fb2695fccb7df6230b98e7e8a0e74161f78e77e223c757faefdf4fe26', '[\"*\"]', '2025-05-30 08:36:22', NULL, '2025-05-30 08:23:47', '2025-05-30 08:36:22'),
(104, 'App\\Models\\User', 11, 'AuthToken', '729c40609acb378ca76d686a150be3b2cc99af9383536ee69be83fcc8c65e21a', '[\"*\"]', '2025-05-30 08:55:37', NULL, '2025-05-30 08:37:23', '2025-05-30 08:55:37'),
(105, 'App\\Models\\User', 11, 'AuthToken', '7910b21cc385effa045e8af61920220ac31b3d187da5642750847ec15e2dd275', '[\"*\"]', '2025-05-30 08:57:45', NULL, '2025-05-30 08:57:18', '2025-05-30 08:57:45'),
(106, 'App\\Models\\User', 11, 'AuthToken', 'c897d7190f281ba6033a0a92d04fdce3c6a5306bf40303314a707fbb57509853', '[\"*\"]', '2025-05-30 08:59:30', NULL, '2025-05-30 08:58:05', '2025-05-30 08:59:30'),
(107, 'App\\Models\\User', 11, 'AuthToken', '8831ad917099fad597fbe807aabdaf4fd05bb2ced655b6d0be79b40cbadc05d8', '[\"*\"]', '2025-05-30 09:33:46', NULL, '2025-05-30 09:33:27', '2025-05-30 09:33:46'),
(108, 'App\\Models\\User', 11, 'AuthToken', '0f9f109d2eceba6d2eff35503831464afc0baded94b0fa00b72a83c137638f22', '[\"*\"]', '2025-05-30 09:35:27', NULL, '2025-05-30 09:35:22', '2025-05-30 09:35:27'),
(109, 'App\\Models\\User', 11, 'AuthToken', '3985d1f751d7e57884d2a749d4dfa82521b4304b34c6523aad8c55952b09f2db', '[\"*\"]', '2025-05-30 09:41:06', NULL, '2025-05-30 09:40:20', '2025-05-30 09:41:06'),
(110, 'App\\Models\\User', 11, 'AuthToken', 'dcace574ca7a9235c6fffdb1ee1d51ff8a61a8965ee4c1e2b7727e6b5305d87c', '[\"*\"]', '2025-05-30 09:43:40', NULL, '2025-05-30 09:41:29', '2025-05-30 09:43:40'),
(111, 'App\\Models\\User', 10, 'AuthToken', 'e1ba4c704b6d8b2912f050a4a2f837cd0e499d9b7e32a019fb03328869092d3e', '[\"*\"]', '2025-05-30 09:50:19', NULL, '2025-05-30 09:48:44', '2025-05-30 09:50:19'),
(112, 'App\\Models\\User', 10, 'AuthToken', 'be5974d1c029937f31f0002b7fcb9a3092fda8393a677b5d2ae51c070aac7a98', '[\"*\"]', NULL, NULL, '2025-05-30 09:54:03', '2025-05-30 09:54:03'),
(113, 'App\\Models\\User', 10, 'AuthToken', '94f4d66e02c060677719e30ad6b18e7082770b438f2486c55ec7b92e017acd18', '[\"*\"]', '2025-05-30 10:00:03', NULL, '2025-05-30 09:58:34', '2025-05-30 10:00:03'),
(114, 'App\\Models\\User', 10, 'AuthToken', '11d3d09074ed0c28a10f18c013925e2d2060398381686e47eb25abbb0adfedee', '[\"*\"]', '2025-05-30 10:01:29', NULL, '2025-05-30 10:00:45', '2025-05-30 10:01:29'),
(115, 'App\\Models\\User', 10, 'AuthToken', '7dc263d6a4f43e4b21dcc7188bb8e238a224172a8115f631ae65651c74eb4a3a', '[\"*\"]', NULL, NULL, '2025-05-30 10:02:00', '2025-05-30 10:02:00'),
(116, 'App\\Models\\User', 1, 'AuthToken', 'bce37e8610ce2f1a7ca4bbb065f2f2a88aac8c7d5c9affc070eb079f7bb60593', '[\"*\"]', NULL, NULL, '2025-05-30 10:13:34', '2025-05-30 10:13:34'),
(117, 'App\\Models\\User', 10, 'AuthToken', 'f99e98bf2323608f6f6e49c49dd4f79821d3ab489f12147e8956759dc0a2e6ce', '[\"*\"]', NULL, NULL, '2025-05-30 10:18:54', '2025-05-30 10:18:54'),
(118, 'App\\Models\\User', 1, 'AuthToken', '3dc41d483bcf416d10b1abf61b3f4d37cbcdd871e608167101b52b33426e8ff4', '[\"*\"]', NULL, NULL, '2025-05-30 10:21:56', '2025-05-30 10:21:56'),
(119, 'App\\Models\\User', 11, 'AuthToken', 'd987aa4bac9d3a8eda4d92cb6db1ef864bfbd4612431fc75f40cd7fec5c1b253', '[\"*\"]', '2025-05-30 10:26:33', NULL, '2025-05-30 10:25:54', '2025-05-30 10:26:33'),
(120, 'App\\Models\\User', 11, 'AuthToken', '49bf637f2960642bbf033f1f01d515609af9f895d79507be6017cf222d0c3dee', '[\"*\"]', '2025-05-30 10:27:17', NULL, '2025-05-30 10:27:11', '2025-05-30 10:27:17'),
(121, 'App\\Models\\User', 11, 'AuthToken', '4f0aae948cd15877547b889d990a0ea2f6d97e4f86ddfcf7942866ceb64a2589', '[\"*\"]', '2025-05-30 10:28:11', NULL, '2025-05-30 10:28:05', '2025-05-30 10:28:11'),
(122, 'App\\Models\\User', 1, 'AuthToken', '516c50a2fb8d4be408ec773b698502914f3c08df23f66d26d77e4f0713e72b20', '[\"*\"]', NULL, NULL, '2025-05-30 10:40:38', '2025-05-30 10:40:38'),
(123, 'App\\Models\\User', 1, 'AuthToken', '893659beaf3ec0fda3ce6a7c36324f9d4d52fc40a2d3b6fa34c32de5d16f8498', '[\"*\"]', NULL, NULL, '2025-05-30 11:28:02', '2025-05-30 11:28:02'),
(124, 'App\\Models\\User', 10, 'AuthToken', '1f4916db3a233ed6121e599b4b7d35153da895d700c12055016ab8472e5e3a09', '[\"*\"]', NULL, NULL, '2025-05-30 11:36:54', '2025-05-30 11:36:54'),
(125, 'App\\Models\\User', 11, 'AuthToken', 'dd9d5ee646fe2d53a4db0f6ae25f87525d3303af5f8204ef33d1457b9f5ad4d5', '[\"*\"]', '2025-05-30 11:42:12', NULL, '2025-05-30 11:40:28', '2025-05-30 11:42:12'),
(126, 'App\\Models\\User', 14, 'AuthToken', '23dc71029f00f6f0b9c6774d29e57cf3a91c1d6846c3a2962c4cf05962d3cf1e', '[\"*\"]', '2025-06-03 11:43:48', NULL, '2025-06-03 11:15:53', '2025-06-03 11:43:48'),
(127, 'App\\Models\\User', 14, 'AuthToken', 'df5f59da0a9f68be745c1ab0643608391379a58c80edb7beb3a74a1d5ec26e56', '[\"*\"]', NULL, NULL, '2025-06-03 11:52:45', '2025-06-03 11:52:45'),
(128, 'App\\Models\\User', 14, 'AuthToken', '96756ede45de1d097f16a02be8dcb86cba0e5308109ab699e61ae68edf13b459', '[\"*\"]', '2025-06-03 12:09:02', NULL, '2025-06-03 12:05:46', '2025-06-03 12:09:02'),
(129, 'App\\Models\\User', 14, 'AuthToken', '626b678622bc625d062b62eabce6daa261bdf779b68ef82d2d10a36a037aab4a', '[\"*\"]', '2025-06-03 12:10:47', NULL, '2025-06-03 12:10:40', '2025-06-03 12:10:47'),
(130, 'App\\Models\\User', 14, 'AuthToken', '1a96e415cdf23a137713cd228dd3d7b9988dba370912ddaa325bbe07442160f4', '[\"*\"]', NULL, NULL, '2025-06-03 12:19:13', '2025-06-03 12:19:13'),
(131, 'App\\Models\\User', 14, 'AuthToken', '6a63d69cb1b7e40abab9540def8435f39905a32862de62233f139c7a70e3015b', '[\"*\"]', NULL, NULL, '2025-06-03 12:52:57', '2025-06-03 12:52:57'),
(132, 'App\\Models\\User', 14, 'AuthToken', 'a0e0cdbec8bbefda9c2b17b93084348f5131892cb568b682f7158263a5e939ee', '[\"*\"]', NULL, NULL, '2025-06-03 13:03:23', '2025-06-03 13:03:23'),
(133, 'App\\Models\\User', 14, 'AuthToken', '070c3555c0fdf5efbd35267472c1c90f42151c2bee063a0f35f34f5c9597a713', '[\"*\"]', NULL, NULL, '2025-06-03 13:41:01', '2025-06-03 13:41:01'),
(134, 'App\\Models\\User', 14, 'AuthToken', 'ed8f534a83609b37582693e498355bc3791d0a784a9add0adfbed6e1df25a887', '[\"*\"]', NULL, NULL, '2025-06-03 14:06:09', '2025-06-03 14:06:09'),
(135, 'App\\Models\\User', 10, 'AuthToken', 'dcfc57badbd4e9426b6511f6a703067bb0c785054c5f898578d95eb9c144be4c', '[\"*\"]', NULL, NULL, '2025-06-03 14:06:54', '2025-06-03 14:06:54'),
(136, 'App\\Models\\User', 10, 'AuthToken', '50453f3686d0cde559eb609397218f9d618e4a98ebf8670a398d6ab84bfd0058', '[\"*\"]', NULL, NULL, '2025-06-03 14:07:46', '2025-06-03 14:07:46'),
(137, 'App\\Models\\User', 14, 'AuthToken', '9bbef785dc9db79979fd49690ad0e80f4c4834ec6ca2c033858ffa4cac14d36f', '[\"*\"]', NULL, NULL, '2025-06-03 14:19:57', '2025-06-03 14:19:57'),
(138, 'App\\Models\\User', 14, 'AuthToken', '53cdde182320e15709ded9277e820234706daa802a45b7c2cb8252207ac72c11', '[\"*\"]', NULL, NULL, '2025-06-03 14:31:48', '2025-06-03 14:31:48'),
(139, 'App\\Models\\User', 14, 'AuthToken', '0d7915efbb39a6ff1b0d5b47980f6cb3a80bbeb0ab21be40d7e496404a1e95f3', '[\"*\"]', NULL, NULL, '2025-06-03 14:59:25', '2025-06-03 14:59:25'),
(140, 'App\\Models\\User', 14, 'AuthToken', '93b11f8d87d7b1881b82901266750c4438fda391c5829d7f6766399d4a229d16', '[\"*\"]', NULL, NULL, '2025-06-03 15:04:52', '2025-06-03 15:04:52'),
(141, 'App\\Models\\User', 1, 'AuthToken', '627d0ba4e46b8ad8f484c31b06639afa8a29fda897992a3eadf14b6934bd46e0', '[\"*\"]', NULL, NULL, '2025-06-03 15:06:56', '2025-06-03 15:06:56'),
(142, 'App\\Models\\User', 14, 'AuthToken', 'd4e3b095e67b068ffae1812c39e41932f553191cea4226d02770550d701e4a73', '[\"*\"]', NULL, NULL, '2025-06-04 10:15:05', '2025-06-04 10:15:05'),
(143, 'App\\Models\\User', 14, 'AuthToken', '4d81a0efa83e6944354fa8add2be19314731c9fd8a927faa25cf96b4020e1393', '[\"*\"]', NULL, NULL, '2025-06-04 11:41:13', '2025-06-04 11:41:13'),
(144, 'App\\Models\\User', 14, 'AuthToken', '1dfab8ddc08fa1622bb46fda0f14ac67f5f5039ad56edfd9e23de12015ef0abb', '[\"*\"]', '2025-06-04 12:43:52', NULL, '2025-06-04 12:34:01', '2025-06-04 12:43:52'),
(145, 'App\\Models\\User', 14, 'AuthToken', 'b44391070913f991b96cd0d87bd81a4fe43bf1d1c97b0af4d6c2c3b5543d0414', '[\"*\"]', '2025-06-04 12:52:00', NULL, '2025-06-04 12:44:20', '2025-06-04 12:52:00'),
(146, 'App\\Models\\User', 14, 'AuthToken', '624c428929576cc0f9b0bf0df29d5a0fa0348800255d369272a7b14011381f4e', '[\"*\"]', '2025-06-04 12:54:49', NULL, '2025-06-04 12:52:06', '2025-06-04 12:54:49'),
(147, 'App\\Models\\User', 14, 'AuthToken', '38e2e848f0d3104b095016245cabeb232db0c0c814b6ef7f1b977cdf667f66c2', '[\"*\"]', NULL, NULL, '2025-06-04 12:56:31', '2025-06-04 12:56:31'),
(148, 'App\\Models\\User', 14, 'AuthToken', '57645782d078459981909eebbf9972e47abe3a2f8c594c432a53c9526aae029f', '[\"*\"]', '2025-06-04 14:18:20', NULL, '2025-06-04 13:03:35', '2025-06-04 14:18:20'),
(149, 'App\\Models\\User', 14, 'AuthToken', '4c5808d974d043f91148fca04ac1edf7e6f9f5dcba59389075a0f41907d550f0', '[\"*\"]', '2025-06-04 14:29:29', NULL, '2025-06-04 14:29:21', '2025-06-04 14:29:29'),
(150, 'App\\Models\\User', 14, 'AuthToken', '416b9fe1368b8c6492b024b16f67addd1d0a1926100e2dadbfd53a421a69f06f', '[\"*\"]', '2025-06-04 14:32:53', NULL, '2025-06-04 14:32:46', '2025-06-04 14:32:53'),
(151, 'App\\Models\\User', 10, 'AuthToken', 'dbd0dc7e11cfd2d8964160a89a493d91310bebc7158dc05af89e9365c4ac554d', '[\"*\"]', '2025-06-04 15:28:00', NULL, '2025-06-04 15:27:53', '2025-06-04 15:28:00'),
(152, 'App\\Models\\User', 14, 'AuthToken', '6a13de591b7531b60bad63ea28cf96ee7877b7370f79d3b3651293d917430e11', '[\"*\"]', '2025-06-04 15:30:48', NULL, '2025-06-04 15:28:37', '2025-06-04 15:30:48'),
(153, 'App\\Models\\User', 10, 'AuthToken', 'ed014bb5c8a3215b6682d5ae2afb8ddf7f1740487c7c632b3a43f65ccde9cc46', '[\"*\"]', '2025-06-04 15:36:41', NULL, '2025-06-04 15:36:34', '2025-06-04 15:36:41'),
(154, 'App\\Models\\User', 14, 'AuthToken', 'b3d49c0a1eba3827cd15b4bbd160eb95f3101984d7ff0808f40f78e26d3fe57e', '[\"*\"]', '2025-06-04 15:37:20', NULL, '2025-06-04 15:37:13', '2025-06-04 15:37:20'),
(155, 'App\\Models\\User', 14, 'AuthToken', '15e961fbf071dac9a4b55f452e851fb74db9a9ffa66ee9e1a643325f2a5ec499', '[\"*\"]', '2025-06-04 15:40:32', NULL, '2025-06-04 15:40:27', '2025-06-04 15:40:32'),
(156, 'App\\Models\\User', 14, 'AuthToken', '270ea8b6b776495ac4390afecf3834cd2cbcf46da2ac623c3ce896fa4d991dde', '[\"*\"]', '2025-06-05 07:40:17', NULL, '2025-06-05 07:35:47', '2025-06-05 07:40:17'),
(157, 'App\\Models\\User', 14, 'AuthToken', 'd4563310456e5be915f92504b3124f751a92f34908e14d9fb1191970abf68407', '[\"*\"]', '2025-06-05 07:43:33', NULL, '2025-06-05 07:43:26', '2025-06-05 07:43:33'),
(158, 'App\\Models\\User', 1, 'AuthToken', 'ffc330cbc765305460d64a9fd88ebd9b34c3fd383fb28daacb47e61e45a57aed', '[\"*\"]', NULL, NULL, '2025-06-05 07:47:53', '2025-06-05 07:47:53'),
(159, 'App\\Models\\User', 1, 'AuthToken', 'aa323ed553f7b01d53e2b36c6c58afd774273d7825d20279f69289b97988594f', '[\"*\"]', NULL, NULL, '2025-06-05 08:11:34', '2025-06-05 08:11:34'),
(160, 'App\\Models\\User', 1, 'AuthToken', 'e15f1f5f595716d37694fd6959fe0f249088c4fedab3677caff47e3c8ee54bce', '[\"*\"]', NULL, NULL, '2025-06-05 08:16:14', '2025-06-05 08:16:14'),
(161, 'App\\Models\\User', 1, 'AuthToken', 'f6383cf681e7059608b22a3ac27317e9d174dc88b7a9c8244281469aa27f8e66', '[\"*\"]', NULL, NULL, '2025-06-05 08:20:00', '2025-06-05 08:20:00'),
(162, 'App\\Models\\User', 14, 'AuthToken', '8d8656563b6b5a9ee4effea8f829e12df3906792346cbba3ee0464eacf78ca4a', '[\"*\"]', '2025-06-05 08:37:03', NULL, '2025-06-05 08:36:56', '2025-06-05 08:37:03'),
(163, 'App\\Models\\User', 11, 'AuthToken', '7f76111dd62ff6adf671c38d3c1d91c2e5c44e963dd8e3b88f669f26417af8fe', '[\"*\"]', '2025-06-05 08:41:47', NULL, '2025-06-05 08:41:27', '2025-06-05 08:41:47'),
(164, 'App\\Models\\User', 11, 'AuthToken', '2c3c0f9cf8fbb651e154d9da148b6fee04d39fd5a2957b7b8b8be4b1e4225a69', '[\"*\"]', '2025-06-05 08:46:47', NULL, '2025-06-05 08:46:43', '2025-06-05 08:46:47'),
(165, 'App\\Models\\User', 11, 'AuthToken', 'c98363035143f0e6ccbc7bc38a0b60d5d5c0b2ca7d84fc195143df91ff234f17', '[\"*\"]', NULL, NULL, '2025-06-05 08:55:24', '2025-06-05 08:55:24'),
(166, 'App\\Models\\User', 11, 'AuthToken', '2afcbebe89f7927aeacb7719634cf91f78428090b72eb4c88219f050e47dafd5', '[\"*\"]', '2025-06-05 09:08:36', NULL, '2025-06-05 09:01:32', '2025-06-05 09:08:36'),
(167, 'App\\Models\\User', 10, 'AuthToken', 'b530291c6fbe299cd4e2491a0560c315fa529b0b3e8d0ab2f0bab4f127b4dd0b', '[\"*\"]', '2025-06-05 09:14:57', NULL, '2025-06-05 09:14:50', '2025-06-05 09:14:57'),
(168, 'App\\Models\\User', 1, 'AuthToken', '7b61b6c3c7428e25b7969cb0b95161d52fefc3cbc8e77deb6a43fa03822ffbb4', '[\"*\"]', NULL, NULL, '2025-06-05 09:24:08', '2025-06-05 09:24:08'),
(169, 'App\\Models\\User', 1, 'AuthToken', 'ceac4f8b665603a43f9daa8049c16bdf14ed26580db720e149058e1bf9b58156', '[\"*\"]', NULL, NULL, '2025-06-05 10:05:37', '2025-06-05 10:05:37'),
(170, 'App\\Models\\User', 1, 'AuthToken', '209aaded2c7ae57f5335f189df6074eb47bdf8f9d7f188abe0d18fe6af4855a9', '[\"*\"]', NULL, NULL, '2025-06-05 10:12:02', '2025-06-05 10:12:02'),
(171, 'App\\Models\\User', 1, 'AuthToken', 'b9ba8f0ecaac0a4544c88d80f2776c95d6bec18bfd4cd2ac7a6bbb4d6adcd4ac', '[\"*\"]', NULL, NULL, '2025-06-05 10:13:41', '2025-06-05 10:13:41'),
(172, 'App\\Models\\User', 1, 'AuthToken', '4949c633ff58560bcaf59a77124836602db5ce10982785b85c5ace51610ddff8', '[\"*\"]', NULL, NULL, '2025-06-05 10:22:51', '2025-06-05 10:22:51'),
(173, 'App\\Models\\User', 1, 'AuthToken', '46446e62b6b4d102da174d1bb5d5b4d816bf458fe79cef1f706c889daff59e9a', '[\"*\"]', NULL, NULL, '2025-06-05 10:25:54', '2025-06-05 10:25:54'),
(174, 'App\\Models\\User', 1, 'AuthToken', 'e6890b0185861d3293d628d3bffb8ede60de2225c8a325682c4f38ecb2aa004e', '[\"*\"]', NULL, NULL, '2025-06-05 10:27:57', '2025-06-05 10:27:57'),
(175, 'App\\Models\\User', 1, 'AuthToken', 'cf57d24605c747fc41c10a42d4c666d21c356cc11b3ea612b0ff7f860cb92bf9', '[\"*\"]', NULL, NULL, '2025-06-05 10:36:20', '2025-06-05 10:36:20'),
(176, 'App\\Models\\User', 1, 'AuthToken', 'd75baa2792c5202a71cd3602d9608c424799d61e7db9aa33e2343edf8b2463dd', '[\"*\"]', NULL, NULL, '2025-06-05 10:38:43', '2025-06-05 10:38:43'),
(177, 'App\\Models\\User', 1, 'AuthToken', 'd145e5e408745c55253222490f5821ae24bd33cfaeea985647b0344cb852a6f1', '[\"*\"]', NULL, NULL, '2025-06-05 10:40:43', '2025-06-05 10:40:43'),
(178, 'App\\Models\\User', 1, 'AuthToken', '9bcd44d90eb39b24364aae1bae608596a7fc710a5e2f8198aa6120a9bc17b1df', '[\"*\"]', NULL, NULL, '2025-06-05 10:59:03', '2025-06-05 10:59:03'),
(179, 'App\\Models\\User', 10, 'AuthToken', 'fd2e2585ff0c95b69ff950ca221f3b7670c66fa5ccd000e459b111121f690d57', '[\"*\"]', '2025-06-10 09:10:38', NULL, '2025-06-10 09:10:32', '2025-06-10 09:10:38'),
(180, 'App\\Models\\User', 11, 'AuthToken', 'fc7376eb0a40ba66cdf5c5afaaa8bcb23b87b1105a591ad9ede761343b3cd5b7', '[\"*\"]', '2025-06-10 09:23:21', NULL, '2025-06-10 09:23:15', '2025-06-10 09:23:21'),
(181, 'App\\Models\\User', 1, 'AuthToken', '7417d5502880a48ec3a7caeabd3d04d7b812a1fff53a00e3853583d16b953e61', '[\"*\"]', NULL, NULL, '2025-06-10 09:24:08', '2025-06-10 09:24:08'),
(182, 'App\\Models\\User', 1, 'AuthToken', 'acfac95ace5bfc437e91f1991ed9e2929b54b211ad24b96b8cfbfe2b1ff26777', '[\"*\"]', NULL, NULL, '2025-06-10 09:53:36', '2025-06-10 09:53:36'),
(183, 'App\\Models\\User', 1, 'AuthToken', 'eca706bb572ed5592b21dd80d0d92d2b3eb097d41eb005c44d4e0fb4054049c2', '[\"*\"]', NULL, NULL, '2025-06-10 09:54:50', '2025-06-10 09:54:50'),
(184, 'App\\Models\\User', 1, 'AuthToken', '6b07731effeee2fccf0a9259eb5f6b33f17d2fbf7955075c5f497021cad652be', '[\"*\"]', NULL, NULL, '2025-06-10 10:05:39', '2025-06-10 10:05:39'),
(185, 'App\\Models\\User', 1, 'AuthToken', '5f928850f3305e3ae18094bb3ba097bc061f0d224f3a04b154170e21c0fdbe26', '[\"*\"]', NULL, NULL, '2025-06-10 10:08:30', '2025-06-10 10:08:30'),
(186, 'App\\Models\\User', 1, 'AuthToken', '646a82103ed913bddefde11719070b415bca11319b6073dde8894342101ffff9', '[\"*\"]', NULL, NULL, '2025-06-10 10:22:32', '2025-06-10 10:22:32'),
(187, 'App\\Models\\User', 1, 'AuthToken', '9205bef62afaa89fc0cf5f6cb56a9309f4cae8689065bc1ee6f9914a74795af7', '[\"*\"]', NULL, NULL, '2025-06-10 10:36:33', '2025-06-10 10:36:33'),
(188, 'App\\Models\\User', 1, 'AuthToken', '6d64aff74e64c49964a1a16c1609fbbd27b126b2d0aedea06c038b25ba58e671', '[\"*\"]', NULL, NULL, '2025-06-10 10:43:35', '2025-06-10 10:43:35'),
(189, 'App\\Models\\User', 1, 'AuthToken', '94a09c3bf7f5169599f0c3844f025a3ad047eec1115106d8be3743ba476b314b', '[\"*\"]', NULL, NULL, '2025-06-10 11:14:12', '2025-06-10 11:14:12'),
(190, 'App\\Models\\User', 1, 'AuthToken', '1e3bfe37466c55b50aa8904745b7b1e97cd17f3ff7e3388fd0a235f9a293e611', '[\"*\"]', NULL, NULL, '2025-06-10 11:15:59', '2025-06-10 11:15:59'),
(191, 'App\\Models\\User', 1, 'AuthToken', 'f4f10736fc806316567ed52d98dad42e8f62831573b6f50fce3447ebc14e8e4f', '[\"*\"]', NULL, NULL, '2025-06-10 11:20:22', '2025-06-10 11:20:22'),
(192, 'App\\Models\\User', 1, 'AuthToken', '62446908dab1faa006d1e562b0ecb85706afd23afeb4ab83d73e64aa66a0ee03', '[\"*\"]', '2025-06-10 11:34:53', NULL, '2025-06-10 11:32:52', '2025-06-10 11:34:53'),
(193, 'App\\Models\\User', 1, 'AuthToken', 'e95e9ddcd6936b083f4088fccff6efa5a44c17ed37c3c2c10122fe999fc074d5', '[\"*\"]', '2025-06-10 11:46:06', NULL, '2025-06-10 11:35:49', '2025-06-10 11:46:06'),
(194, 'App\\Models\\User', 1, 'AuthToken', '39106d67f99dff571c17afa5165f8745a4d5938cb965f6171295100ce2152bee', '[\"*\"]', NULL, NULL, '2025-06-10 11:47:13', '2025-06-10 11:47:13'),
(195, 'App\\Models\\User', 10, 'AuthToken', '1e16758dc5b649e635fb38dee003414acfadeb3b3bf5152976e72d8e758df51d', '[\"*\"]', '2025-06-10 11:50:31', NULL, '2025-06-10 11:50:25', '2025-06-10 11:50:31'),
(196, 'App\\Models\\User', 10, 'AuthToken', 'c674d54b6e5a4e31159c05defb2dc729c063245138509481ec1844ae40fd86d1', '[\"*\"]', '2025-06-10 11:51:55', NULL, '2025-06-10 11:51:49', '2025-06-10 11:51:55'),
(197, 'App\\Models\\User', 10, 'AuthToken', 'a22083213bf67ae897dd17c32f744daa0c8c627ccbc9844d7ff0c7773da731bc', '[\"*\"]', '2025-06-10 11:53:48', NULL, '2025-06-10 11:53:42', '2025-06-10 11:53:48'),
(198, 'App\\Models\\User', 10, 'AuthToken', '99a7f281cca5fa11123f1525e44a8b80ad596f95bcd6cb38ce3e22c53eea7c1b', '[\"*\"]', '2025-06-10 11:54:15', NULL, '2025-06-10 11:54:09', '2025-06-10 11:54:15'),
(199, 'App\\Models\\User', 10, 'AuthToken', '74c86d7dbb9445c3d33781a5a7295895e04c807cd8e3c4822f721bc023287ff4', '[\"*\"]', '2025-06-10 12:03:13', NULL, '2025-06-10 11:56:43', '2025-06-10 12:03:13'),
(200, 'App\\Models\\User', 10, 'AuthToken', 'd44ba0bc336637953c80be7396118ac30ffb5be0a2a21ae398dc1fb652c2b916', '[\"*\"]', '2025-06-10 12:38:14', NULL, '2025-06-10 12:38:06', '2025-06-10 12:38:14'),
(201, 'App\\Models\\User', 10, 'AuthToken', '396efee279e3bc619a7b88052c7e97c2fbae2cba40103357ff3a7ca5d58d7528', '[\"*\"]', '2025-06-10 12:39:48', NULL, '2025-06-10 12:39:41', '2025-06-10 12:39:48'),
(202, 'App\\Models\\User', 10, 'AuthToken', '14ec97978faef6dc3ee689edc5debfe2b6e6b69bbc5323464652e56ee41d077b', '[\"*\"]', '2025-06-10 12:41:04', NULL, '2025-06-10 12:40:56', '2025-06-10 12:41:04'),
(203, 'App\\Models\\User', 10, 'AuthToken', 'e45d73b365280ee518d8614d4860457785823c944ed28304658616eb2dabe533', '[\"*\"]', '2025-06-10 12:41:49', NULL, '2025-06-10 12:41:41', '2025-06-10 12:41:49'),
(204, 'App\\Models\\User', 1, 'AuthToken', '16f2b2503240f3977e40fd5526ded328986180fbc7f684dbaff79295be38a86c', '[\"*\"]', '2025-06-10 12:57:56', NULL, '2025-06-10 12:56:46', '2025-06-10 12:57:56'),
(205, 'App\\Models\\User', 1, 'AuthToken', '809e8808ec8ad8d4214960fedef6739340a04dd9656d390cedbaf1518a291435', '[\"*\"]', NULL, NULL, '2025-06-10 13:00:30', '2025-06-10 13:00:30'),
(206, 'App\\Models\\User', 1, 'AuthToken', '30dedadd23e5a03eff9708488bc291f04b103dc45cfc001bd6ff7df9211b808b', '[\"*\"]', '2025-06-10 13:10:24', NULL, '2025-06-10 13:04:27', '2025-06-10 13:10:24'),
(207, 'App\\Models\\User', 1, 'AuthToken', '4378cf53b1cddcd4fd3fee473ca5450e08676066c4984e093834a9df4cd47bc1', '[\"*\"]', NULL, NULL, '2025-06-10 13:26:00', '2025-06-10 13:26:00'),
(208, 'App\\Models\\User', 11, 'AuthToken', '3868b077126a2af0d1fa2dd50ebbe00be3a3ba87c48a9de570ff0777a373d6d4', '[\"*\"]', '2025-06-13 13:58:11', NULL, '2025-06-13 13:55:26', '2025-06-13 13:58:11'),
(209, 'App\\Models\\User', 10, 'AuthToken', '6694786612dcdd481c60445b02cdfa200ca2170f4d74bd39f1be0a2f1194c7db', '[\"*\"]', '2025-06-13 14:01:11', NULL, '2025-06-13 14:00:58', '2025-06-13 14:01:11'),
(210, 'App\\Models\\User', 1, 'AuthToken', 'e2c129fddbce0b6bfd02db68411f750361e589b485537c429c248aea82ca9d6c', '[\"*\"]', '2025-06-13 14:14:17', NULL, '2025-06-13 14:08:34', '2025-06-13 14:14:17'),
(211, 'App\\Models\\User', 10, 'AuthToken', 'd9fe53ce261e3c8697d3fb644b907df73b175f45098b219bab8778ad5cd27aa0', '[\"*\"]', '2025-06-16 09:13:59', NULL, '2025-06-16 09:13:50', '2025-06-16 09:13:59'),
(212, 'App\\Models\\User', 1, 'AuthToken', '50899c5b022928d7f67c7bfecd4a6d35836804aed20f1c5110d8c0beb6e9fd0d', '[\"*\"]', NULL, NULL, '2025-06-16 09:15:27', '2025-06-16 09:15:27'),
(213, 'App\\Models\\User', 20, 'AuthToken', '844f4d5896b0a5b8112645c17f8418495aa435d45f513ffd80103175b54dd9de', '[\"*\"]', '2025-06-16 09:25:45', NULL, '2025-06-16 09:15:59', '2025-06-16 09:25:45'),
(214, 'App\\Models\\User', 10, 'AuthToken', 'e8a76d6acc79baa16e4e24180203cf85b7e8a7d92934f6873938fe65993fd6b1', '[\"*\"]', '2025-06-16 09:26:21', NULL, '2025-06-16 09:26:12', '2025-06-16 09:26:21'),
(215, 'App\\Models\\User', 1, 'AuthToken', '982b575dc5bddaddc88c83b7758de51f967c81f5945e3ff3a99d3d958a6a7c75', '[\"*\"]', '2025-06-16 09:27:04', NULL, '2025-06-16 09:26:56', '2025-06-16 09:27:04'),
(216, 'App\\Models\\User', 10, 'AuthToken', 'fce7a1b1703a3032820e9c15f6cf4a51379f65a280c87a801af36b8607d10c1d', '[\"*\"]', '2025-06-20 15:00:32', NULL, '2025-06-20 15:00:22', '2025-06-20 15:00:32'),
(217, 'App\\Models\\User', 10, 'AuthToken', '61bf37c38e9b63f152f2abf0c0cf384a4d9789ee07ecc76df26358929f25050a', '[\"*\"]', '2025-06-20 15:08:31', NULL, '2025-06-20 15:08:23', '2025-06-20 15:08:31'),
(218, 'App\\Models\\User', 1, 'AuthToken', 'c23b797f1efb3ce0575ef00b3931bd7a34c13c0e7a504ebd1f66e892a7c9d69f', '[\"*\"]', NULL, NULL, '2025-06-20 15:10:53', '2025-06-20 15:10:53'),
(219, 'App\\Models\\User', 10, 'AuthToken', '752ee34fb4553d7c428815daed2570fa95fc452515b800defbfb9cbf6eda08ae', '[\"*\"]', '2025-06-20 15:11:31', NULL, '2025-06-20 15:11:24', '2025-06-20 15:11:31'),
(220, 'App\\Models\\User', 21, 'AuthToken', 'bf017466eeee1eab75421e2d07b91f09702603c60282b416964ef9ab907b2722', '[\"*\"]', '2025-06-20 15:13:15', NULL, '2025-06-20 15:12:23', '2025-06-20 15:13:15'),
(221, 'App\\Models\\User', 21, 'AuthToken', '44617dcf3aae8585d5bc164db78c17549ce64ecdeef6d62a68628063f91a145f', '[\"*\"]', '2025-06-20 15:14:11', NULL, '2025-06-20 15:14:01', '2025-06-20 15:14:11'),
(222, 'App\\Models\\User', 10, 'AuthToken', 'bd57e970b64edaaf032b71c9546225b3184719d4df20385bbaaf140bfb63cc10', '[\"*\"]', '2025-06-20 15:15:15', NULL, '2025-06-20 15:15:08', '2025-06-20 15:15:15'),
(223, 'App\\Models\\User', 1, 'AuthToken', '6a1b97649dd3c5421a40f8480aa23bbd3c7b07ae26981ce36cc8358184ccaf62', '[\"*\"]', '2025-06-20 15:16:26', NULL, '2025-06-20 15:16:06', '2025-06-20 15:16:26'),
(224, 'App\\Models\\User', 21, 'AuthToken', 'd3df36d52166e544e68db65fa3e6cd87bcadb18d44365f9a0877971580899bd5', '[\"*\"]', '2025-06-20 15:16:46', NULL, '2025-06-20 15:16:39', '2025-06-20 15:16:46'),
(225, 'App\\Models\\User', 21, 'AuthToken', 'a9b5be450922bf04590eff683f77857c2ae7729f6641ae1d012e14e0d8fc4767', '[\"*\"]', '2025-06-20 15:17:29', NULL, '2025-06-20 15:17:23', '2025-06-20 15:17:29'),
(226, 'App\\Models\\User', 1, 'AuthToken', 'b07575cc05c6d823f75e67611d2f14034e01651554be6d626795055647c8d25e', '[\"*\"]', '2025-06-20 15:20:04', NULL, '2025-06-20 15:19:28', '2025-06-20 15:20:04'),
(227, 'App\\Models\\User', 21, 'AuthToken', 'a4daf51e5d8fed82b2877593bd4ee7989b770582b9a00a984d0d85718a64f16f', '[\"*\"]', '2025-06-23 10:15:48', NULL, '2025-06-23 09:48:54', '2025-06-23 10:15:48'),
(228, 'App\\Models\\User', 21, 'AuthToken', '76d40cc319d362b12b18d21df3809ecf632b901ce0ed80f0659a2b8bd43a5240', '[\"*\"]', '2025-06-23 10:57:57', NULL, '2025-06-23 10:57:45', '2025-06-23 10:57:57'),
(229, 'App\\Models\\User', 10, 'AuthToken', 'a6f38535df46d46fce9afd8c4d7a0e0ce20e298dba4c619ae2fc7f582ec02621', '[\"*\"]', '2025-06-23 10:59:45', NULL, '2025-06-23 10:59:35', '2025-06-23 10:59:45'),
(230, 'App\\Models\\User', 14, 'AuthToken', 'cbdd3c5be3df9931b6eeee631f59fb977ddcb6e6ec1fdf0227638f3900cde6bc', '[\"*\"]', '2025-06-23 11:00:46', NULL, '2025-06-23 11:00:37', '2025-06-23 11:00:46'),
(231, 'App\\Models\\User', 10, 'AuthToken', '65353a59ef8f55568586b562b70aa0a438646bd4780c9beea0a43d42e3b5441c', '[\"*\"]', '2025-06-23 11:17:46', NULL, '2025-06-23 11:10:09', '2025-06-23 11:17:46'),
(232, 'App\\Models\\User', 21, 'AuthToken', '158168cee5a751f342ecb3ab5e6b0dec0a43474bea5b19ce616cbb4e7c0e4d07', '[\"*\"]', '2025-06-23 11:18:12', NULL, '2025-06-23 11:18:05', '2025-06-23 11:18:12'),
(233, 'App\\Models\\User', 14, 'AuthToken', 'cc8ae2d03a368201abd2845de030d65cfef7257fef4b70ee1fc8af59d8233382', '[\"*\"]', '2025-06-23 11:19:48', NULL, '2025-06-23 11:19:40', '2025-06-23 11:19:48'),
(234, 'App\\Models\\User', 21, 'AuthToken', '94a56175249fb41b912c06577fd4b53d0ea74aded9e0c39c455207a269f2ebcd', '[\"*\"]', '2025-06-23 11:23:00', NULL, '2025-06-23 11:22:49', '2025-06-23 11:23:00'),
(235, 'App\\Models\\User', 21, 'AuthToken', 'dbc40ef1759893e42e8957e271fc07788052c2a40c6c4269c108a4d3254858da', '[\"*\"]', '2025-06-23 11:30:20', NULL, '2025-06-23 11:28:07', '2025-06-23 11:30:20'),
(236, 'App\\Models\\User', 21, 'AuthToken', '64261516a78c778e4dd45b29d2ca318f284abb487fef26b61e03f25897005973', '[\"*\"]', NULL, NULL, '2025-06-23 12:09:06', '2025-06-23 12:09:06'),
(237, 'App\\Models\\User', 21, 'AuthToken', 'cf45e21f2d5b00e922a7e1f3f553945a7575cc5eb10ac7b3b2026638a00355b9', '[\"*\"]', '2025-06-23 12:14:36', NULL, '2025-06-23 12:13:26', '2025-06-23 12:14:36'),
(238, 'App\\Models\\User', 10, 'AuthToken', '92864748246e8902ca87acca2e265ceae85676877f4ccab911d651e3e3f4d5fa', '[\"*\"]', '2025-06-23 12:16:44', NULL, '2025-06-23 12:16:33', '2025-06-23 12:16:44'),
(239, 'App\\Models\\User', 10, 'AuthToken', '2adb42ecb29293db2d991cb9d0424bdd64cab39a53f034a4355e68310d791ebf', '[\"*\"]', '2025-06-23 12:18:22', NULL, '2025-06-23 12:18:02', '2025-06-23 12:18:22'),
(240, 'App\\Models\\User', 21, 'AuthToken', '8e44eb23b3881112d5222e6415c0d646e8a9b553e472e0594ca5b34a4a27a7c5', '[\"*\"]', '2025-06-23 12:18:54', NULL, '2025-06-23 12:18:50', '2025-06-23 12:18:54'),
(241, 'App\\Models\\User', 10, 'AuthToken', 'cc617e42a57777646e7d790de9e60d6813c4e20982c1e5d02511247eca3837a5', '[\"*\"]', '2025-06-23 13:00:42', NULL, '2025-06-23 13:00:35', '2025-06-23 13:00:42'),
(242, 'App\\Models\\User', 10, 'AuthToken', '83140d10eec5c077c999522502e187023b92e46b48f13191637bad34885c8047', '[\"*\"]', '2025-06-23 13:01:49', NULL, '2025-06-23 13:01:44', '2025-06-23 13:01:49'),
(243, 'App\\Models\\User', 1, 'AuthToken', 'a5bd28c8a38550518e943282e988ad0e1810c4af6a06962e11885387d30f72e8', '[\"*\"]', '2025-06-26 11:44:36', NULL, '2025-06-23 14:35:08', '2025-06-26 11:44:36'),
(244, 'App\\Models\\User', 10, 'AuthToken', '4929efef1615ac37ec6006a37788db705602fdb4f670e7eec2b9495aecb1f972', '[\"*\"]', NULL, NULL, '2025-06-26 11:44:48', '2025-06-26 11:44:48'),
(245, 'App\\Models\\User', 11, 'AuthToken', '26c79a43ee89260c504c0ce2460234faaf6fba5807e5303593356e82c4cb650c', '[\"*\"]', '2025-07-14 10:21:57', NULL, '2025-07-14 09:45:30', '2025-07-14 10:21:57'),
(246, 'App\\Models\\User', 21, 'AuthToken', '1e946226d42342b874bac59f85d3199ef6441b8e35a0f7b6c23f75fd42297781', '[\"*\"]', NULL, NULL, '2025-07-14 14:26:40', '2025-07-14 14:26:40'),
(247, 'App\\Models\\User', 21, 'AuthToken', 'ad0cc6ae4938d0a0dc6f7ff49fd9304fadfcbac943d0804577b7e194b6089f9c', '[\"*\"]', NULL, NULL, '2025-07-16 12:46:50', '2025-07-16 12:46:50'),
(248, 'App\\Models\\User', 21, 'AuthToken', '2933359e5383a74c54db98ad76851a7f8cf12fbd3f02f1228d172513ca736676', '[\"*\"]', '2025-07-16 12:48:02', NULL, '2025-07-16 12:47:48', '2025-07-16 12:48:02'),
(249, 'App\\Models\\User', 21, 'AuthToken', 'cbc54b138889518d1aea6bbc28c620327ccb210fe6df7fc216b97e13ba8e3eec', '[\"*\"]', '2025-07-16 12:50:47', NULL, '2025-07-16 12:49:27', '2025-07-16 12:50:47'),
(250, 'App\\Models\\User', 21, 'AuthToken', '55412b6604d2078132a5623a18bff10f9ddd49fa3950245db4de9cc7bf5d4a0e', '[\"*\"]', NULL, NULL, '2025-07-16 13:04:49', '2025-07-16 13:04:49'),
(251, 'App\\Models\\User', 21, 'AuthToken', '323fbcadc96c865303f31ebf70432641b5b9aa8fa48da0ddd2b48b4a01899250', '[\"*\"]', NULL, NULL, '2025-07-16 13:10:46', '2025-07-16 13:10:46'),
(252, 'App\\Models\\User', 21, 'AuthToken', 'fba83cdf52d2c126ca26b8a99c277dd1d11d60d896ed732cbafcdef6bf340666', '[\"*\"]', '2025-07-16 13:19:05', NULL, '2025-07-16 13:11:57', '2025-07-16 13:19:05'),
(253, 'App\\Models\\User', 21, 'AuthToken', '7f5521ed0d61116d0725d6b72fa9dbe85c446d965ab3829e2de4a19509a1bc31', '[\"*\"]', NULL, NULL, '2025-07-16 13:21:08', '2025-07-16 13:21:08'),
(254, 'App\\Models\\User', 21, 'AuthToken', '22d8d930e1fe6e048199e598c584f02686514ddd21340ce915a20a42ea586a86', '[\"*\"]', NULL, NULL, '2025-07-16 13:23:24', '2025-07-16 13:23:24'),
(255, 'App\\Models\\User', 21, 'AuthToken', '7a72dcb742a9faf32535a04bb717ca78a513c57b70f3ebef8553e11c0ae92232', '[\"*\"]', '2025-07-16 13:32:36', NULL, '2025-07-16 13:27:27', '2025-07-16 13:32:36'),
(256, 'App\\Models\\User', 21, 'AuthToken', 'c724e6070c59386cee6303456c68e368192a7923c5ce666f24047aa033103b98', '[\"*\"]', '2025-07-16 13:41:44', NULL, '2025-07-16 13:40:27', '2025-07-16 13:41:44'),
(257, 'App\\Models\\User', 21, 'AuthToken', 'c7425dbc37baf6122b6e925391b5ab4247fe256a211c8307d2e3950f2d3b9171', '[\"*\"]', NULL, NULL, '2025-07-28 19:12:53', '2025-07-28 19:12:53');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `type_compte` enum('user','advisor','manager') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'user',
  `statut` enum('En attente','Actif','Rejeté') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'En attente',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_by` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `name`, `last_name`, `email`, `phone`, `address`, `type_compte`, `statut`, `email_verified_at`, `password`, `remember_token`, `created_by`, `created_at`, `updated_at`) VALUES
(21, 'pio', 'pio', 'pio@gmail.com', '12345678923', 'QSDFGHJqsdfghj', 'manager', 'Actif', NULL, '$2y$12$9o75AdWpcoJ3F2Lnfb/zTOtULNLt.nxJE73.SQWp7zugsGwqItKw2', NULL, NULL, '2025-06-20 15:01:29', '2025-06-20 15:11:09');

--
-- Index pour les tables déchargées
--

--
-- Index pour la table `credit_requests`
--
ALTER TABLE `credit_requests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `credit_requests_user_id_foreign` (`user_id`);

--
-- Index pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Index pour la table `funding_requests`
--
ALTER TABLE `funding_requests`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Index pour la table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Index pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Index pour la table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_created_by_foreign` (`created_by`);

--
-- AUTO_INCREMENT pour les tables déchargées
--

--
-- AUTO_INCREMENT pour la table `credit_requests`
--
ALTER TABLE `credit_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT pour la table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT pour la table `funding_requests`
--
ALTER TABLE `funding_requests`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT pour la table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT pour la table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=258;

--
-- AUTO_INCREMENT pour la table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `credit_requests`
--
ALTER TABLE `credit_requests`
  ADD CONSTRAINT `credit_requests_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Contraintes pour la table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
