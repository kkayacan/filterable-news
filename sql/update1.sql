SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

ALTER TABLE `categories` ADD `newsapi_cat` VARCHAR(15) NULL AFTER `text`;

UPDATE `categories` SET `newsapi_cat` = 'general' WHERE `categories`.`id` = 1; UPDATE `categories` SET `newsapi_cat` = 'business' WHERE `categories`.`id` = 3; UPDATE `categories` SET `newsapi_cat` = 'science' WHERE `categories`.`id` = 4; UPDATE `categories` SET `newsapi_cat` = 'technology' WHERE `categories`.`id` = 5; UPDATE `categories` SET `newsapi_cat` = '' WHERE `categories`.`id` = 6; UPDATE `categories` SET `newsapi_cat` = 'sports' WHERE `categories`.`id` = 7; UPDATE `categories` SET `newsapi_cat` = 'health' WHERE `categories`.`id` = 8;