-- phpsampleE 用 データベース初期化
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS=0;

DROP TABLE IF EXISTS `comments`;
DROP TABLE IF EXISTS `messages`;
DROP TABLE IF EXISTS `members`;

CREATE TABLE `members` (
  `mid` int(11) NOT NULL AUTO_INCREMENT,
  `mname` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `pass` varchar(255) NOT NULL,
  `picture` varchar(255) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`mid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `messages` (
  `meid` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(255) NOT NULL,
  `mid` int(11) NOT NULL,
  `mecre` datetime NOT NULL,
  `modified` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`meid`),
  KEY `mid` (`mid`),
  CONSTRAINT `fk_messages_members` FOREIGN KEY (`mid`) REFERENCES `members` (`mid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE `comments` (
  `cid` int(11) NOT NULL AUTO_INCREMENT,
  `meid` int(11) NOT NULL,
  `mid` int(11) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `cocre` datetime DEFAULT NULL,
  PRIMARY KEY (`cid`),
  KEY `meid` (`meid`),
  KEY `mid` (`mid`),
  CONSTRAINT `fk_comments_messages` FOREIGN KEY (`meid`) REFERENCES `messages` (`meid`) ON DELETE CASCADE,
  CONSTRAINT `fk_comments_members` FOREIGN KEY (`mid`) REFERENCES `members` (`mid`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

SET FOREIGN_KEY_CHECKS=1;
