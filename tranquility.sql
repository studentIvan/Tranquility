-- MySQL dump 10.13  Distrib 5.5.16, for Win32 (x86)
--
-- Host: localhost    Database: tranquility
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (4,'Тестовая запись','Lorem ipsum dolor sit amet, consectetur adipiscing elit. Duis ut lacus nisi. In cursus imperdiet massa, ut imperdiet nulla fringilla a. Mauris varius adipiscing lacinia. Donec fermentum fermentum dolor eu tincidunt. Donec ultricies molestie purus vitae scelerisque. Proin vulputate mi et lectus faucibus ac placerat leo sagittis. Nulla non nulla nec erat mollis varius. Donec vestibulum tincidunt lacus sed dictum. Integer mattis tempus eleifend. Aliquam erat volutpat. Curabitur molestie vulputate orci a mollis. Pellentesque feugiat ipsum in urna fringilla rhoncus. Maecenas ut felis sapien.\r\n\r\nCras eu erat tristique quam ultricies luctus. Maecenas egestas ipsum sit amet eros aliquam at rhoncus nisl gravida. Vestibulum vitae turpis nulla. Fusce at ultricies urna. Curabitur id ipsum nec erat pulvinar luctus. Integer interdum tincidunt rhoncus. Morbi porttitor, dolor nec aliquet fermentum, libero nunc pellentesque libero, quis consectetur est nibh eget mauris.\r\n\r\nAliquam in erat sed nulla pretium feugiat sed et odio. In semper, justo sed mollis feugiat, magna ante ultrices nisi, a adipiscing lacus mauris eu odio. Sed nulla diam, tristique nec euismod non, ultricies sed sem. Nulla blandit pulvinar elit, pretium tristique sem viverra eu. Vestibulum at dolor vel augue hendrerit facilisis. Fusce vitae massa id quam bibendum condimentum. Phasellus nunc lectus, semper vitae malesuada sit amet, aliquet mollis libero. Praesent porttitor purus non dolor eleifend non pellentesque quam fermentum. In tellus turpis, faucibus et condimentum vitae, imperdiet sed leo. Duis mollis lacus vel dui lobortis tincidunt. Ut eget sapien eu est convallis viverra. Sed bibendum, neque in scelerisque molestie, nisi erat condimentum erat, vitae elementum odio elit vel metus. Maecenas a ornare purus. Fusce ac eros quis lorem mattis condimentum.\r\n\r\nIn velit turpis, blandit eget vehicula in, varius eget turpis. Nulla facilisi. Morbi porta sollicitudin tempus. Vivamus ultricies metus vitae dui molestie accumsan. Morbi adipiscing venenatis commodo. Aliquam facilisis turpis a tortor vestibulum ac fermentum sem rhoncus. Quisque et nibh lorem, id dapibus sem. Pellentesque suscipit, augue vel varius scelerisque, nulla nisl ornare nibh, eget lobortis diam odio vulputate dui.\r\n\r\nAliquam a eros ligula, sed elementum nibh. Vivamus in leo justo, vel mattis lectus. Nam ut nisi id sem iaculis porttitor eu in metus. Cras interdum enim vitae augue vulputate pulvinar. Sed scelerisque ultrices nulla, ut mattis tortor consectetur non. Suspendisse sapien velit, sodales non euismod at, imperdiet et odio. Etiam quis magna velit, sed hendrerit ligula. Suspendisse pretium elit sed orci venenatis placerat nec ac nulla. ','','2012-04-25 20:04:12',1);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referers`
--

DROP TABLE IF EXISTS `referers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referers` (
  `timepoint` datetime NOT NULL,
  `token` char(32) NOT NULL,
  `url` varchar(200) NOT NULL,
  KEY `token` (`token`),
  KEY `created_at` (`timepoint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referers`
--

LOCK TABLES `referers` WRITE;
/*!40000 ALTER TABLE `referers` DISABLE KEYS */;
INSERT INTO `referers` VALUES ('2012-04-24 13:14:24','339e19fa0609188fe48fb72a5f72e5a5','http://turbo.local/admin'),('2012-04-24 14:26:29','872c4f8cb4402dc6e1877379e27ad0db','http://turbo.local/admin'),('2012-04-24 17:00:31','fc200d6a326d757580c1d401f5eac13e','http://turbo.local/admin/manager/'),('2012-04-25 18:22:58','8cbe8803f8e7da46e5f5478cef6b9d0d','http://turbo.local/admin/');
/*!40000 ALTER TABLE `referers` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Администратор'),(2,'Модератор'),(3,'Пользователь'),(4,'Гость'),(5,'Бот');
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
  `useragent` varchar(110) NOT NULL,
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
INSERT INTO `sessions` VALUES ('54d04b71bbbac7b116872336cbd6e1b2',1,1,2130706433,'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:11.0) Gecko/20100101 Firefox/11.0','2012-04-25 21:10:11',NULL);
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-04-25 21:11:27
