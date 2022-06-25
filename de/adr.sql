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
  `startMeasure` datetime NOT NULL COMMENT 'Start time of the measurement',
  `endMeasure` datetime NOT NULL COMMENT 'Ending time of the measurement',
  `value` double(7,3) unsigned NOT NULL COMMENT 'The measured value',
  `valueCosmic` double(7,3) unsigned NOT NULL COMMENT 'Cosmic part of the measured value',
  `valueTerrestrial` double(7,3) unsigned NOT NULL COMMENT 'Terrestrial part of the measured value',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stationId_startMeasure_endMeasure` (`stationId`,`startMeasure`,`endMeasure`),
  KEY `startMeasure` (`startMeasure`),
  KEY `endMeasure` (`endMeasure`),
  KEY `value` (`value`),
  KEY `valueCosmic` (`valueCosmic`),
  KEY `valueTerrestrial` (`valueTerrestrial`),
  CONSTRAINT `entries_ibfk_1` FOREIGN KEY (`stationId`) REFERENCES `stations` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Entries';


DROP TABLE IF EXISTS `stations`;
CREATE TABLE `stations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID',
  `intId` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'International ID of the measuring point',
  `kenn` int(9) unsigned zerofill NOT NULL COMMENT 'Measuring point identifier',
  `plz` int(5) unsigned zerofill NOT NULL COMMENT 'Zip code of the measuring point',
  `name` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'Name/place of the measuring point',
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '0=inactive;1=active;2=out of service;3=testing',
  `height` int(10) unsigned NOT NULL COMMENT 'Height above sea',
  PRIMARY KEY (`id`),
  UNIQUE KEY `kenn` (`kenn`),
  UNIQUE KEY `intId` (`intId`),
  KEY `plz` (`plz`),
  KEY `status` (`status`),
  KEY `height` (`height`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Measuring stations';


-- 2022-03-12 23:34:19
