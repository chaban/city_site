/*
SQLyog Ultimate v9.10 
MySQL - 5.5.8 : Database - balash_phalcon
*********************************************************************
*/

/*!40101 SET NAMES utf8 */;

/*!40101 SET SQL_MODE=''*/;

/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;
CREATE DATABASE /*!32312 IF NOT EXISTS*/`balash_phalcon` /*!40100 DEFAULT CHARACTER SET utf8 */;

USE `balash_phalcon`;

/*Table structure for table `adverts` */

DROP TABLE IF EXISTS `adverts`;

CREATE TABLE `adverts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `category` mediumint(9) unsigned DEFAULT '0',
  `body` text,
  `status` tinyint(3) unsigned DEFAULT NULL,
  `price_range` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `price` varchar(255) DEFAULT NULL,
  `buy_sell` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `create_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `update_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_insertion_user_create` (`create_user_id`),
  KEY `fk_insertion_user_update` (`update_user_id`),
  CONSTRAINT `fk_insertion_user_create` FOREIGN KEY (`create_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_insertion_user_update` FOREIGN KEY (`update_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `adverts` */

/*Table structure for table `article_categories` */

DROP TABLE IF EXISTS `article_categories`;

CREATE TABLE `article_categories` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(40) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

/*Data for the table `article_categories` */

insert  into `article_categories`(`id`,`name`) values (1,'ÐžÐ±Ñ‰Ð°Ñ'),(2,'Ð¡Ð¿ÐµÑ†Ð¸Ð°Ð»ÑŒÐ½Ð°Ñ');

/*Table structure for table `articles` */

DROP TABLE IF EXISTS `articles`;

CREATE TABLE `articles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `create_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `update_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `category_id` tinyint(4) NOT NULL,
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_article_user_create` (`create_user_id`),
  KEY `fk_article_user_update` (`update_user_id`),
  CONSTRAINT `fk_article_user_create` FOREIGN KEY (`create_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_article_user_update` FOREIGN KEY (`update_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `articles` */

insert  into `articles`(`id`,`title`,`body`,`status`,`create_time`,`update_time`,`create_user_id`,`update_user_id`,`category_id`,`slug`) values (1,'some title','some body',0,'2013-06-05 15:06:40','2013-06-05 15:06:40',1,1,1,'some-title'),(2,'some second title','some second text',0,'2013-06-05 15:06:40','2013-06-05 15:06:40',1,1,1,'some-second-title'),(4,'Ð§ÐµÑ‚Ð²ÐµÑ€Ñ‚Ð°Ñ ÑÑ‚Ð°Ñ‚ÑŒÑ','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.  <img style=\"width:463px;height:309px\" src=\"/public/files/1247146409_pod_000061.jpg\">',0,'2013-06-14 10:50:08','2013-06-14 11:05:47',1,1,1,'%d1%87%d0%b5%d1%82%d0%b2%d0%b5%d1%80%d1%82%d0%b0%d1%8f-%d1%81%d1%82%d0%b0%d1%82%d1%8c%d1%8f');

/*Table structure for table `banners` */

DROP TABLE IF EXISTS `banners`;

CREATE TABLE `banners` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT '',
  `url` varchar(255) DEFAULT NULL,
  `desc_r` varchar(255) DEFAULT NULL,
  `desc_l` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `create_user_id` int(11) unsigned DEFAULT NULL,
  `update_user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_banner_user_create` (`create_user_id`),
  KEY `fk_banner_user_update` (`update_user_id`),
  CONSTRAINT `fk_banner_user_create` FOREIGN KEY (`create_user_id`) REFERENCES `users` (`id`),
  CONSTRAINT `fk_banner_user_update` FOREIGN KEY (`update_user_id`) REFERENCES `users` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `banners` */

/*Table structure for table `comments` */

DROP TABLE IF EXISTS `comments`;

CREATE TABLE `comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `parent_comment_id` int(11) NOT NULL DEFAULT '0',
  `class_name` varchar(100) COLLATE utf8_bin NOT NULL,
  `object_pk` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '1',
  `email` varchar(255) COLLATE utf8_bin NOT NULL,
  `name` varchar(50) COLLATE utf8_bin NOT NULL,
  `text` text COLLATE utf8_bin NOT NULL,
  `created` datetime NOT NULL,
  `updated` datetime NOT NULL,
  `ip_address` varchar(255) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  KEY `class_name_index` (`class_name`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

/*Data for the table `comments` */

insert  into `comments`(`id`,`user_id`,`parent_comment_id`,`class_name`,`object_pk`,`status`,`email`,`name`,`text`,`created`,`updated`,`ip_address`) values (1,1,0,'News',1,1,'demo@phalconphp.com','Demo Demovski','some test text','2013-06-13 18:05:17','2013-06-13 18:05:17','127.0.0.1'),(2,1,0,'News',1,1,'demo@phalconphp.com','Demo Demovski','anothe comment','2013-06-13 18:10:25','2013-06-13 18:10:25','127.0.0.1'),(3,1,0,'News',1,1,'demo@phalconphp.com','Demo Demovski','third comment','2013-06-13 18:24:30','2013-06-13 18:24:30','127.0.0.1'),(4,1,0,'News',1,1,'demo@phalconphp.com','Demo Demovski','forth comment','2013-06-13 18:25:59','2013-06-13 18:25:59','127.0.0.1'),(5,1,0,'News',1,1,'demo@phalconphp.com','Demo Demovski','fifth comment','2013-06-13 18:26:43','2013-06-13 18:26:43','127.0.0.1'),(6,1,0,'News',1,1,'demo@phalconphp.com','Demo Demovski','sixth comment','2013-06-13 18:41:19','2013-06-13 18:41:19','127.0.0.1'),(7,1,0,'News',2,1,'demo@phalconphp.com','Demo Demovski','comment','2013-06-13 18:53:05','2013-06-13 18:53:05','127.0.0.1'),(8,1,0,'News',7,1,'demo@phalconphp.com','Demo Demovski','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.','2013-06-14 07:28:58','2013-06-14 07:28:58','127.0.0.1');

/*Table structure for table `comments_back` */

DROP TABLE IF EXISTS `comments_back`;

CREATE TABLE `comments_back` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `author` int(11) unsigned DEFAULT NULL,
  `create_time` datetime NOT NULL,
  `name` varchar(150) NOT NULL,
  `email` varchar(150) NOT NULL,
  `url` varchar(150) DEFAULT NULL,
  `content` text NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `parent_id` int(11) unsigned DEFAULT NULL,
  `owner` varchar(128) NOT NULL,
  `owner_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `status` (`status`),
  KEY `userId` (`author`),
  CONSTRAINT `fk_comment_author` FOREIGN KEY (`author`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `comments_back` */

/*Table structure for table `email_confirmations` */

DROP TABLE IF EXISTS `email_confirmations`;

CREATE TABLE `email_confirmations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `code` char(32) NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  `modifiedAt` int(10) unsigned DEFAULT NULL,
  `confirmed` char(1) DEFAULT 'N',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `email_confirmations` */

/*Table structure for table `failed_logins` */

DROP TABLE IF EXISTS `failed_logins`;

CREATE TABLE `failed_logins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned DEFAULT NULL,
  `ipAddress` char(15) NOT NULL,
  `attempted` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `failed_logins` */

insert  into `failed_logins`(`id`,`usersId`,`ipAddress`,`attempted`) values (1,0,'127.0.0.1',1370504644);

/*Table structure for table `interviews` */

DROP TABLE IF EXISTS `interviews`;

CREATE TABLE `interviews` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `respondent` varchar(255) DEFAULT NULL,
  `phrase` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `status` tinyint(3) unsigned DEFAULT NULL,
  `create_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `update_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_inter_user_create` (`create_user_id`),
  KEY `fk_inter_user_update` (`update_user_id`),
  CONSTRAINT `fk_inter_user_create` FOREIGN KEY (`create_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_inter_user_update` FOREIGN KEY (`update_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

/*Data for the table `interviews` */

insert  into `interviews`(`id`,`title`,`respondent`,`phrase`,`body`,`status`,`create_user_id`,`update_user_id`,`create_time`,`update_time`,`slug`) values (1,'Lorem ipsum dolor sit amet, consectetur adipisicing ','Lorem ipsum dolor sit amet, con','Lorem ipsum dolor sit amet, consectetur adipisicing elit, s','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.  <img style=\"width:463px;height:617px\" src=\"/public/files/iqHLK.jpg\">',0,1,1,'2013-06-14 12:28:28','2013-06-14 12:36:58','lorem-ipsum-dolor-sit-amet-consectetur-adipisicing');

/*Table structure for table `lookup` */

DROP TABLE IF EXISTS `lookup`;

CREATE TABLE `lookup` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `code` int(11) NOT NULL,
  `type` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `position` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `lookup` */

/*Table structure for table `news` */

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `status` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `create_time` datetime NOT NULL,
  `update_time` datetime NOT NULL,
  `create_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `update_user_id` int(11) unsigned NOT NULL DEFAULT '0',
  `slug` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_new_user_update` (`update_user_id`),
  KEY `fk_new_user_create` (`create_user_id`),
  CONSTRAINT `fk_new_user_create` FOREIGN KEY (`create_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_new_user_update` FOREIGN KEY (`update_user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

/*Data for the table `news` */

insert  into `news`(`id`,`title`,`body`,`status`,`create_time`,`update_time`,`create_user_id`,`update_user_id`,`slug`) values (1,'adsfsd','asdfasd',0,'2013-06-10 16:42:18','2013-06-10 17:45:42',1,1,'adsfsd'),(2,'new test','new body',0,'2013-06-10 17:03:55','2013-06-10 17:14:42',1,1,'new-test'),(4,'third news title with new condetions 3 and number','body',0,'2013-06-10 18:22:55','2013-06-10 18:22:55',1,1,'third-news-title-with-new-condetions-3-and-number'),(5,'third news title with new condetions 3 and number','new body',0,'2013-06-10 18:23:58','2013-06-10 18:23:58',1,1,'third-news-title-with-new-condetions-3-and-number'),(6,'Ñ‚ÐµÑÑ‚','Ñ‚ÐµÑÑ‚ ÑÐ¾Ð´ÐµÑ€Ð¶Ð°Ð½Ð¸Ðµ<br><img style=\"width:442px;height:600px\" src=\"/public/files/1247146296_pod_000021.jpg\">',0,'2013-06-11 11:13:38','2013-06-11 11:13:38',1,1,'%d1%82%d0%b5%d1%81%d1%82'),(7,'Lorem ipsum dolor sit ame','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.  <img style=\"width:463px;height:337px\" src=\"/public/files/1247146372_pod_000078.jpg\"><br>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.  <br><img style=\"width:463px;height:324px\" src=\"/public/files/1247146380_pod_000048.jpg\">',0,'2013-06-14 08:27:50','2013-06-14 08:27:50',1,1,'lorem-ipsum-dolor-sit-ame');

/*Table structure for table `password_changes` */

DROP TABLE IF EXISTS `password_changes`;

CREATE TABLE `password_changes` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `ipAddress` char(15) NOT NULL,
  `userAgent` varchar(48) NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `password_changes` */

/*Table structure for table `permissions` */

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `profilesId` int(10) unsigned NOT NULL,
  `resource` varchar(16) NOT NULL,
  `action` varchar(16) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`profilesId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `permissions` */

/*Table structure for table `posts` */

DROP TABLE IF EXISTS `posts`;

CREATE TABLE `posts` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `body` text COLLATE utf8_unicode_ci NOT NULL,
  `tags` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `status` tinyint(1) NOT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `author_id` int(11) unsigned NOT NULL DEFAULT '0',
  `slug` tinytext COLLATE utf8_unicode_ci,
  PRIMARY KEY (`id`),
  KEY `fk_post_user` (`author_id`),
  CONSTRAINT `fk_post_user` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `posts` */

insert  into `posts`(`id`,`title`,`body`,`tags`,`status`,`create_time`,`update_time`,`author_id`,`slug`) values (4,'test post 4','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.<br><img style=\"width:463px;height:617px\" src=\"/public/files/iqHLK.jpg\">','test,Ð¾Ñ‚Ð±Ð°Ð»Ð´Ñ‹',0,'2013-06-18 10:56:00','2013-06-18 10:56:00',1,'test-post-4'),(5,'Ð´Ð»Ñ ÑƒÐ´Ð°Ð»ÐµÐ½Ð¸Ñ','Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod\r\n tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim \r\nveniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea \r\ncommodo consequat. Duis aute irure dolor in reprehenderit in voluptate \r\nvelit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint \r\noccaecat cupidatat non proident, sunt in culpa qui officia deserunt \r\nmollit anim id est laborum.','Ð¾Ñ‚Ð±Ð°Ð»Ð´Ñ‹,Ð´Ð»Ñ ÑƒÐ´Ð°Ð»ÐµÐ½Ð¸Ñ',0,'2013-06-18 10:57:58','2013-06-18 10:57:58',1,'%d0%b4%d0%bb%d1%8f-%d1%83%d0%b4%d0%b0%d0%bb%d0%b5%d0%bd%d0%b8%d1%8f');

/*Table structure for table `remember_tokens` */

DROP TABLE IF EXISTS `remember_tokens`;

CREATE TABLE `remember_tokens` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `token` char(32) NOT NULL,
  `userAgent` varchar(120) NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `token` (`token`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `remember_tokens` */

insert  into `remember_tokens`(`id`,`usersId`,`token`,`userAgent`,`createdAt`) values (1,4,'e97bb81b7f8986883cf33df5578ed860','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1',1370509939),(2,4,'e97bb81b7f8986883cf33df5578ed860','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1',1370510939),(3,4,'c0d7cc1c65b4b68d817d47e65c7567c8','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0',1370512523),(4,1,'1e9520421d0c55360389f21dcfc100b4','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1',1370514166),(5,1,'b79a8f37204eb5fdaaa03fca6cc1f64d','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0',1370585942),(6,1,'b79a8f37204eb5fdaaa03fca6cc1f64d','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0',1370586021),(7,1,'1e9520421d0c55360389f21dcfc100b4','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1',1370869456),(8,1,'b79a8f37204eb5fdaaa03fca6cc1f64d','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0',1371135985);

/*Table structure for table `reset_passwords` */

DROP TABLE IF EXISTS `reset_passwords`;

CREATE TABLE `reset_passwords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `code` varchar(48) NOT NULL,
  `createdAt` int(10) unsigned NOT NULL,
  `modifiedAt` int(10) unsigned DEFAULT NULL,
  `reset` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

/*Data for the table `reset_passwords` */

/*Table structure for table `roles` */

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(64) NOT NULL,
  `active` char(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `active` (`active`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

/*Data for the table `roles` */

insert  into `roles`(`id`,`name`,`active`) values (1,'Administrators','Y'),(2,'Editors','Y'),(3,'Guest','Y');

/*Table structure for table `storage` */

DROP TABLE IF EXISTS `storage`;

CREATE TABLE `storage` (
  `key` varchar(40) DEFAULT NULL,
  `value` tinytext
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

/*Data for the table `storage` */

insert  into `storage`(`key`,`value`) values ('c6e260532b012ca606388edb025bf7fb','\"{\\\"0\\\":1370873682,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_2\\\\\\/1247146296_pod_000021.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('ad2f83b1fabdd08f126f1e267b342889','\"{\\\"0\\\":1370873682,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_2\\\\\\/1247146313_pod_000003.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('4fa1372f915ad683ad478aa05bdb4cb3','\"{\\\"0\\\":1370875507,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_1\\\\\\/X5Aq.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('61ac94c6722e418fd26a23f52f1b3c6c','\"{\\\"0\\\":1370875507,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_1\\\\\\/wAP9l.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('0e79de98342688e07e82a3be9f9be83a','\"{\\\"0\\\":1370875524,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_1\\\\\\/oAcW.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('659579558c9dc0d06d329d21da0759ea','\"{\\\"0\\\":1370938418,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_6\\\\\\/1247146313_pod_000003.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('d0619f8377fd1136565adcbde4954e50','\"{\\\"0\\\":1370875524,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_1\\\\\\/oAcW.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('48fc5a10172ed18307a81ca191755b9d','\"{\\\"0\\\":1370873682,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_2\\\\\\/1247146296_pod_000021.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('bf724d5f398bd102b90d13ada6c1ed31','\"{\\\"0\\\":1370877775,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_4\\\\\\/1247146374_pod_000025.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('2571f32898b86f1c9793d79b7235dc80','\"{\\\"0\\\":1370877838,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_5\\\\\\/1247146380_pod_000048.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('86e8293a27b756a1bfcf6e06eefebfa9','\"{\\\"0\\\":1370938418,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_6\\\\\\/1247146313_pod_000003.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('47eda64b73f84718385e01d5d5740e28','\"{\\\"0\\\":1370875524,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_1\\\\\\/oAcW.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('90c9c83152ad0fe5f72527b994d4bfbc','\"{\\\"0\\\":1370875507,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_1\\\\\\/wAP9l.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('0ef2900db01d34442f790dc7efac5762','\"{\\\"0\\\":1370873682,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_2\\\\\\/1247146296_pod_000021.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('51e774b13b5f6f6d95948a287da49e5e','\"{\\\"0\\\":1370873682,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_2\\\\\\/1247146313_pod_000003.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('c1bd3e97fe585af12d0571293d09da65','\"{\\\"0\\\":1370938418,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_6\\\\\\/1247146313_pod_000003.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('1b63e4339146df79e5fb5b52e69850a3','\"{\\\"0\\\":1370877838,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_5\\\\\\/1247146380_pod_000048.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('38bf482afcda01d4d04f6ff61f43ef5d','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/FrUTj.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('8df52d32349e0b19de901200be880034','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/iBr1.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('b91ab1756302316bfa5016778121677b','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/iqHLK.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('36d70fc5161e31cef37b40d78adb63f2','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/p59ze.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('309bb3d8af8eb243dd48de4fdcb15bb1','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/FrUTj.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('b8fb2606e1eaee6d633aa38ae9786f05','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/FrUTj.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('17852a773851126f579d5394de262e16','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/iBr1.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('d041fc3565a484fda3624678893b4201','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/iqHLK.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('dc50169ce91fa2399708846f90efa445','\"{\\\"0\\\":1371187670,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_7\\\\\\/p59ze.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('a6a9e746e72f7dc38d0fbaf763836f4d','\"{\\\"0\\\":1371197146,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/articles\\\\\\/images_id_4\\\\\\/MeH7g.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('4a3a5d3a73d3eda24d5c462814da9080','\"{\\\"0\\\":1371197146,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/articles\\\\\\/images_id_4\\\\\\/iqHLK.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('b8b61a805b96c41289f592f87419b257','\"{\\\"0\\\":1370877775,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_4\\\\\\/1247146374_pod_000025.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('b32874b3598dab283985a5d91548d34f','\"{\\\"0\\\":1370877775,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_4\\\\\\/1247146380_pod_000048.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('e714bb4a9709bc81732b825ff547cd54','\"{\\\"0\\\":1371202108,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_1\\\\\\/1247146374_pod_000025.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('dd18a8b50171162dde7ea6ca63a041a8','\"{\\\"0\\\":1371202108,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_1\\\\\\/1247146380_pod_000048.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('00698cbe9d686a85b23aa12d6bdb6f47','\"{\\\"0\\\":1371202618,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_1\\\\\\/FrUTj.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('3b002a03e76b44acf427cff850a54de8','\"{\\\"0\\\":1371451045,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_2\\\\\\/iqHLK.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('86b2ae7c929a6bf79c698a71d7c3bccf','\"{\\\"0\\\":1371451045,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_2\\\\\\/oAcW.jpg\\\",\\\"resize\\\":{\\\"width\\\":120,\\\"height\\\":120,\\\"master\\\":1}}\"'),('fcc0b6cd9ed2e8b4939db56c5bc0849a','\"{\\\"0\\\":1371197146,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/articles\\\\\\/images_id_4\\\\\\/MeH7g.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('b7ec4468a07a99910a4f1485f56cdabe','\"{\\\"0\\\":1371197146,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/articles\\\\\\/images_id_4\\\\\\/MeH7g.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('1ed0d9b58a2cccdb2c7d171bd8d2fee8','\"{\\\"0\\\":1371202108,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_1\\\\\\/1247146374_pod_000025.jpg\\\",\\\"resize\\\":{\\\"width\\\":128,\\\"height\\\":128,\\\"master\\\":1}}\"'),('899de21bee67aeba5c62f10a61cfa382','\"{\\\"0\\\":1371202108,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_1\\\\\\/1247146374_pod_000025.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('ddd9db6fa2a16db297213aeac82b37f4','\"{\\\"0\\\":1371202108,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/interviews\\\\\\/images_id_1\\\\\\/1247146380_pod_000048.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('2f921c9a825c5456f08fc1579d784387','\"{\\\"0\\\":1370877775,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_4\\\\\\/1247146374_pod_000025.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"'),('37ab969ce4f0f8cc2229716e3d5ddb77','\"{\\\"0\\\":1370877775,\\\"1\\\":\\\"D:\\\\\\/xampp\\\\\\/htdocs\\\\\\/balash_phalcon\\\\\\/public\\\\\\/uploads\\\\\\/news\\\\\\/images_id_4\\\\\\/1247146380_pod_000048.jpg\\\",\\\"resize\\\":{\\\"width\\\":560,\\\"height\\\":315,\\\"master\\\":1}}\"');

/*Table structure for table `success_logins` */

DROP TABLE IF EXISTS `success_logins`;

CREATE TABLE `success_logins` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `usersId` int(10) unsigned NOT NULL,
  `ipAddress` char(15) NOT NULL,
  `userAgent` varchar(120) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `usersId` (`usersId`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

/*Data for the table `success_logins` */

insert  into `success_logins`(`id`,`usersId`,`ipAddress`,`userAgent`) values (1,4,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1'),(2,4,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1'),(3,4,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0'),(4,1,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1'),(5,1,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0'),(6,1,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0'),(7,1,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0 FirePHP/0.7.1'),(8,1,'127.0.0.1','Mozilla/5.0 (Windows NT 6.1; WOW64; rv:20.0) Gecko/20100101 Firefox/20.0');

/*Table structure for table `tags` */

DROP TABLE IF EXISTS `tags`;

CREATE TABLE `tags` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(128) COLLATE utf8_unicode_ci NOT NULL,
  `frequency` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

/*Data for the table `tags` */

insert  into `tags`(`id`,`name`,`frequency`) values (1,'some',6),(2,'anothe',6),(3,'third',6),(4,'forth',6),(5,'fifth',6),(6,'sixth',6),(7,'newtag',4),(8,'andthis',3),(9,'test',2),(10,'Ð¾Ñ‚Ð±Ð°Ð»Ð´Ñ‹',2),(11,'Ð´Ð»Ñ ÑƒÐ´Ð°Ð»ÐµÐ½Ð¸Ñ',1);

/*Table structure for table `users` */

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `password` varchar(40) NOT NULL DEFAULT '',
  `role_id` int(11) unsigned NOT NULL,
  `active` char(1) DEFAULT NULL,
  `last_login_time` datetime DEFAULT NULL,
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL,
  `mustChangePassword` char(1) DEFAULT NULL,
  `banned` char(1) DEFAULT NULL,
  `suspended` char(1) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

/*Data for the table `users` */

insert  into `users`(`id`,`name`,`email`,`password`,`role_id`,`active`,`last_login_time`,`create_time`,`update_time`,`mustChangePassword`,`banned`,`suspended`) values (1,'Demo Demovski','demo@phalconphp.com','$2a$08$MtaK0TBGL0Wh4IVpMYMwlu.SmRpxja3r5',1,'Y',NULL,NULL,NULL,'N','N','N'),(4,'Alex Nikitin','alex@mail.com','$2a$08$MtaK0TBGL0Wh4IVpMYMwlu.SmRpxja3r5',2,'Y',NULL,NULL,NULL,'N','N','N');

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
