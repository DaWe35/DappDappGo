SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


CREATE TABLE `skylinks` (
  `skypath` varchar(255) COLLATE utf8_bin NOT NULL,
  `title` varchar(127) COLLATE utf8_bin DEFAULT NULL,
  `filename` varchar(127) COLLATE utf8_bin DEFAULT NULL,
  `lastupdate` int(11) DEFAULT NULL,
  `insertion_date` int(11) NOT NULL,
  `filedate` int(11) DEFAULT NULL,
  `content-type` varchar(31) COLLATE utf8_bin DEFAULT NULL,
  `content-length` int(11) DEFAULT NULL,
  `content` text COLLATE utf8_bin
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;


ALTER TABLE `skylinks`
  ADD UNIQUE KEY `skypath` (`skypath`);
ALTER TABLE `skylinks` ADD FULLTEXT KEY `title` (`title`,`filename`,`content`);


ALTER TABLE `skylinks` ADD `description` VARCHAR(255) NULL DEFAULT NULL AFTER `content`; 

ALTER TABLE `skylinks` CHANGE `insertion_date` `insertion_date` INT(11) UNSIGNED NOT NULL;
ALTER TABLE `skylinks` ADD `insert_ip` INT(11) UNSIGNED NULL DEFAULT NULL AFTER `insertion_date`;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
