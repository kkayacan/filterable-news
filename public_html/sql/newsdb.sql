
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

CREATE TABLE `aliases` (
  `id` int(10) UNSIGNED NOT NULL,
  `sourceId` int(10) UNSIGNED NOT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `categories` (`id`, `text`) VALUES
(1, 'NATION'),
(2, 'WORLD'),
(3, 'BUSINESS'),
(4, 'SCIENCE'),
(5, 'TECHNOLOGY'),
(6, 'ENTERTAINMENT'),
(7, 'SPORTS'),
(8, 'HEALTH');

CREATE TABLE `execution` (
  `id` int(10) UNSIGNED NOT NULL,
  `start` datetime NOT NULL,
  `finish` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `groups` (
  `id` int(10) UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `items` (
  `id` int(10) UNSIGNED NOT NULL,
  `pubDate` datetime NOT NULL,
  `media` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `firstSeen` datetime NOT NULL,
  `lastSeen` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `item_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `itemId` int(10) UNSIGNED NOT NULL,
  `categoryId` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `links` (
  `id` int(10) UNSIGNED NOT NULL,
  `itemId` int(10) UNSIGNED NOT NULL,
  `url` varchar(1000) COLLATE utf8_unicode_ci NOT NULL,
  `title` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `excerpt` varchar(1000) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `sourceId` int(10) UNSIGNED NOT NULL,
  `sourceAlias` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `redundant` (
  `id` int(10) UNSIGNED NOT NULL,
  `text` varchar(100) COLLATE utf8_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `redundant` (`id`, `text`) VALUES
(1, ' - Son Dakika'),
(2, ' - BLOOMBERG HT'),
(3, 'Son dakika! '),
(4, 'SON DAKİKA! '),
(5, ' - GÜNCEL Haberleri'),
(6, 'Son Dakika haberler: '),
(7, ' - Sondakika Ekonomi Haberleri'),
(8, ' - Son Dakika Haberler'),
(9, 'Son dakika haberleri... '),
(10, ' - HABERLER'),
(11, ' - Haberler'),
(12, ' - F5Haber'),
(13, ' - Son Haberler'),
(14, 'SON DAKİKA HABERLERİ! '),
(15, 'Son dakika haberi: '),
(16, 'Son dakika: '),
(17, 'Son Dakika: '),
(18, ' | Transfer Haberleri'),
(19, ' - Son Dakika Haberleri'),
(20, ' - Son Dakika Spor'),
(21, ' - Son Dakika Magazin'),
(22, ' - Son dakika Trabzonspor haberleri'),
(23, ' - Fotomaç'),
(24, ' - Spor Haberleri'),
(25, ' - Son Dakika Ekonomi'),
(26, ' - OTOMOBİL Haberleri'),
(27, 'Son Dakika… '),
(28, 'Son dakika | '),
(29, 'Son dakika haberleri: '),
(30, 'Cumhuriyet MOBIL - '),
(31, ' - Spor'),
(32, ' - Fotoğraf Galerisi'),
(33, ' - Galeri'),
(34, ' Son Dakika Haberleri'),
(36, 'Son dakika… '),
(38, 'Gazetesi - (Video) '),
(39, 'Son dakika| '),
(40, 'Son dakika transfer haberleri! '),
(41, 'Son dakika... '),
(42, 'Son dakika haberi... '),
(43, 'KORKUNÇ! '),
(44, ' | NTV'),
(45, 'SON DAKİKA: '),
(46, ' - SİYASET HABERİ'),
(47, 'Son dakika haberi! '),
(48, 'SON DAKİKA | '),
(49, 'SON DAKİKA... '),
(50, ' - Dünyadan Haberler'),
(51, ' - YENİ ASYA'),
(52, ' - Ekonomi'),
(53, 'DİKKAT! ');

CREATE TABLE `sources` (
  `id` int(10) UNSIGNED NOT NULL,
  `groupId` int(10) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE `source_categories` (
  `id` int(10) UNSIGNED NOT NULL,
  `sourceId` int(10) UNSIGNED NOT NULL,
  `categoryId` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

ALTER TABLE `aliases`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `text` (`text`);

ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `execution`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `groups`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pubDate` (`pubDate`);

ALTER TABLE `item_categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `INDEX_URL` (`itemId`,`url`),
  ADD KEY `INDEX` (`itemId`) USING BTREE,
  ADD KEY `INDEX_SOURCE` (`sourceId`);

ALTER TABLE `redundant`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `sources`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `source_categories`
  ADD PRIMARY KEY (`id`);

ALTER TABLE `aliases`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

ALTER TABLE `execution`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `groups`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `items`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `item_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `links`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `redundant`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

ALTER TABLE `sources`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `source_categories`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

ALTER TABLE `links`
  ADD CONSTRAINT `links_ibfk_1` FOREIGN KEY (`itemId`) REFERENCES `items` (`id`),
  ADD CONSTRAINT `links_ibfk_2` FOREIGN KEY (`sourceId`) REFERENCES `sources` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
