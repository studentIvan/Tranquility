-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: turbo
-- ------------------------------------------------------
-- Server version	5.5.16

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `news`
--

DROP TABLE IF EXISTS `news`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `news` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(150) NOT NULL,
  `content` text NOT NULL,
  `tags` varchar(150) NOT NULL,
  `created_at` datetime NOT NULL,
  `posted_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `tags` (`tags`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `roles` (
  `id` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(20) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Администратор'),(2,'Модератор'),(3,'Пользователь'),(4,'Гость');
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sessions` (
  `token` char(32) NOT NULL,
  `uid` int(10) unsigned NOT NULL DEFAULT '0',
  `role` tinyint(3) unsigned NOT NULL,
  `ip` int(10) unsigned NOT NULL,
  `useragent` varchar(100) NOT NULL,
  `uptime` datetime NOT NULL,
  `data` varchar(150) DEFAULT NULL,
  PRIMARY KEY (`token`),
  KEY `uptime` (`uptime`),
  KEY `uid` (`uid`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('f7380c80a2d2c4f841f923481406ecc7',1,1,2130706433,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko/20100101 Firefox/11.0','2012-04-21 00:30:31',NULL),('7f0ca6286ee53cbd27714459db28c4aa',1,1,2130706433,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko/20100101 Firefox/11.0','2012-04-21 00:18:08',NULL);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `login` varchar(50) NOT NULL,
  `password` char(32) NOT NULL,
  `role` tinyint(3) unsigned NOT NULL,
  `registered_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `login` (`login`),
  KEY `password` (`password`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (1,'Admin','48fa05e2e7c221665db8c9d8f6980919',1,'2012-04-17 00:00:00');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary table structure for view `users monitor v1`
--

DROP TABLE IF EXISTS `users monitor v1`;
/*!50001 DROP VIEW IF EXISTS `users monitor v1`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `users monitor v1` (
  `token` char(32),
  `uid` int(10) unsigned,
  `role` tinyint(3) unsigned,
  `ip` varchar(31),
  `browser` varchar(100),
  `last update time` datetime
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;

--
-- Final view structure for view `users monitor v1`
--

/*!50001 DROP TABLE IF EXISTS `users monitor v1`*/;
/*!50001 DROP VIEW IF EXISTS `users monitor v1`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `users monitor v1` AS select `sessions`.`token` AS `token`,`sessions`.`uid` AS `uid`,`sessions`.`role` AS `role`,inet_ntoa(`sessions`.`ip`) AS `ip`,`sessions`.`useragent` AS `browser`,`sessions`.`uptime` AS `last update time` from `sessions` order by `sessions`.`uptime` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-04-21  1:04:53
