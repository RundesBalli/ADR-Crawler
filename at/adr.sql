-- Adminer 4.8.1 MySQL 5.5.5-10.1.48-MariaDB-0+deb9u2 dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP DATABASE IF EXISTS `adr`;
CREATE DATABASE `adr` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `adr`;

DROP TABLE IF EXISTS `entries`;
CREATE TABLE `entries` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `stationId` int(10) unsigned NOT NULL COMMENT 'stations.id',
  `timestamp` datetime NOT NULL COMMENT 'Time of the measurement',
  `value` double(9,1) unsigned NOT NULL COMMENT 'The measured value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stationId_timestamp` (`stationId`,`timestamp`),
  KEY `timestamp` (`timestamp`),
  KEY `value` (`value`),
  CONSTRAINT `entries_ibfk_1` FOREIGN KEY (`stationId`) REFERENCES `stations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Entries';


DROP TABLE IF EXISTS `stations`;
CREATE TABLE `stations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `identifier` int(10) unsigned NOT NULL COMMENT 'Identifier of the measuring station',
  `name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name of the measuring station',
  PRIMARY KEY (`id`),
  UNIQUE KEY `identifier` (`identifier`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Measuring stations';


-- 2022-06-25 22:15:22
