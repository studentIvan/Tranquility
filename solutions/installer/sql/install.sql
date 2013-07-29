-- MySQL dump 10.13  Distrib 5.5.24, for Win64 (x86)
--
-- Host: localhost    Database: tranquility
-- ------------------------------------------------------
-- Server version	5.5.24

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
-- Table structure for table `captcha`
--

DROP TABLE IF EXISTS `captcha`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `captcha` (
  `token` char(32) NOT NULL,
  `phrase` varchar(10) NOT NULL,
  PRIMARY KEY (`token`),
  KEY `phrase` (`phrase`)
) ENGINE=MEMORY DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `captcha`
--

LOCK TABLES `captcha` WRITE;
/*!40000 ALTER TABLE `captcha` DISABLE KEYS */;
/*!40000 ALTER TABLE `captcha` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `news`
--

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;
INSERT INTO `news` VALUES (4,'Тестовая запись','<p><strong>Lorem ipsum dolor sit amet</strong>, consectetur adipiscing elit. Duis ut lacus nisi. In cursus imperdiet massa, ut imperdiet nulla fringilla a. Mauris varius adipiscing lacinia. Donec fermentum fermentum dolor eu tincidunt. Donec ultricies molestie purus vitae scelerisque. Proin vulputate mi et lectus faucibus ac placerat leo sagittis. Nulla non nulla nec erat mollis varius. Donec vestibulum tincidunt lacus sed dictum. Integer mattis tempus eleifend. Aliquam erat volutpat. Curabitur molestie vulputate orci a mollis. Pellentesque feugiat ipsum in urna fringilla rhoncus. Maecenas ut felis sapien. Cras eu erat tristique quam ultricies luctus. Maecenas egestas ipsum sit amet eros aliquam at rhoncus nisl gravida. Vestibulum vitae turpis nulla. Fusce at ultricies urna. Curabitur id ipsum nec erat pulvinar luctus. Integer interdum tincidunt rhoncus. Morbi porttitor, dolor nec aliquet fermentum, libero nunc pellentesque libero, quis consectetur est nibh eget mauris. Aliquam in erat sed nulla pretium feugiat sed et odio. In semper, justo sed mollis feugiat, magna ante ultrices nisi, a adipiscing lacus mauris eu odio. Sed nulla diam, tristique nec euismod non, ultricies sed sem. Nulla blandit pulvinar elit, pretium tristique sem viverra eu. Vestibulum at dolor vel augue hendrerit facilisis. Fusce vitae massa id quam bibendum condimentum. Phasellus nunc lectus, semper vitae malesuada sit amet, aliquet mollis libero. Praesent porttitor purus non dolor eleifend non pellentesque quam fermentum. In tellus turpis, faucibus et condimentum vitae, imperdiet sed leo. Duis mollis lacus vel dui lobortis tincidunt. Ut eget sapien eu est convallis viverra. Sed bibendum, neque in scelerisque molestie, nisi erat condimentum erat, vitae elementum odio elit vel metus. Maecenas a ornare purus. Fusce ac eros quis lorem mattis condimentum. In velit turpis, blandit eget vehicula in, varius eget turpis. Nulla facilisi. Morbi porta sollicitudin tempus. Vivamus ultricies metus vitae dui molestie accumsan. Morbi adipiscing venenatis commodo. Aliquam facilisis turpis a tortor vestibulum ac fermentum sem rhoncus. Quisque et nibh lorem, id dapibus sem. Pellentesque suscipit, augue vel varius scelerisque, nulla nisl ornare nibh, eget lobortis diam odio vulputate dui! Aliquam a eros ligula, sed elementum nibh. Vivamus in leo justo, vel mattis lectus. Nam ut nisi id sem iaculis porttitor eu in metus. Cras interdum enim vitae augue vulputate pulvinar. Sed scelerisque ultrices nulla, ut mattis tortor consectetur non. Suspendisse sapien velit, sodales non euismod at, imperdiet et odio. Etiam quis magna velit, sed hendrerit ligula. Suspendisse pretium elit sed orci venenatis placerat nec ac nulla.</p>','Aliquam, eros','2012-04-25 20:04:12',1),(5,'Превед пацанчеги','<p>Йцукен шоваыщваощшощшо овышмщтывшщт шщыт шщыв ьшыщ ьвшыщвь вшщыьш вщьышщв ьшщ</p>\r\n<p>Сдест был Vostrecov!</p>','йцукен','2012-04-27 18:23:22',1),(6,'Basic linked lists','List views\r\n\r\n    Basics\r\n    Options\r\n    Methods\r\n    Events\r\n\r\nBasic linked lists\r\n\r\nA list view is coded as a simple unordered list containing linked list items with a data-role=\"listview\" attribute. jQuery Mobile will apply all the necessary styles to transform the list into a mobile-friendly list view with right arrow indicator that fills the full width of the browser window. When you tap on the list item, the framework will trigger a click on the first link inside the list item, issue an AJAX request for the URL in the link, create the new page in the DOM, then kick off a page transition. View the data- attribute reference to see all the possible attributes you can add to listviews.','','2012-04-27 18:23:48',1),(7,'Яндекс разрешил шифрованный контент','Хабражители!\r\n\r\nСегодня Яндекс снял запрет на размещение шифрованных файлов на своих сервисах.\r\n\r\nТаким образом с сегодняшнего дня мы можем не нарушая пункта\r\n\r\n6.1. Все объекты, доступные при помощи сервисов Яндекса, в том числе элементы дизайна, текст, графические изображения, иллюстрации, видео, программы для ЭВМ, базы данных, музыка, звуки и другие объекты (далее – содержание сервисов), а также любой контент, размещенный на сервисах Яндекса, являются объектами исключительных прав Яндекса, Пользователей и других правообладателей.\r\n\r\nзащищать свою информацию от Яндекса и «других правообладателей» путем шифрования.\r\n\r\nДругие пункты лицензионного соглашения Яндекс пока не только не прокомментировал, но даже и не объяснил их содержание.\r\n\r\nЭто наша маленькая, но все же победа. Хочу поблагодарить всех, кто принял участие в этом, каждого, кто твиттнул, кто сделал пост в блог, кто комментировал и т.д. Только благодаря активному вовлечению людей в обсуждение этой насущной проблемы нам с вами удалось «продавить» хоть и маленькое, но все же очень важное изменение в пользовательское соглашение Яндекса.\r\n\r\nPS: Наш успех немного омрачает тот факт, что Яндекс внес это изменение потихому, без оповещения своих пользователей по электронной почте (как это часто, но не всегда делает Google).','яндекс, копирайт, пользовательское соглашение','2012-04-27 18:25:04',1),(8,'Drive eRazer Ultra надежно сотрет любую информацию с жесткого диска','Причем устройство это автономное, подключение к ПК не требуется. По словам представителей компании Wiebtech, устройство забивает нулями каждый бит доступного файлового объема диска. При этом перезапись происходит несколько раз, так что данные восстановлению не подлежат. По мнению разработчиков, устройство подходит даже для стандартов военных организаций, и его можно безопасно продавать или использовать вновь после стирания всех данных.\r\n\r\nДевайс работает достаточно быстро, скорость работы — 7 ГБ/мин с новыми моделями жестких дисков и 2 ГБ/мин с моделями старых образцов. При этом, как уже говорилось, подключение к ПК не требуется, Drive eRazer Ultra и жесткий диск работают в этом случае автономно. Устройство поддерживает работу со многими типами носителей, включая любые типы SATA-дисков (2.5\" и 3.5\"), плюс 3.5\"-дюймовые модели IDE/PATA. Работает девайс и другими типами носителей, включая 2,5 и 1,8-дюймовые.\r\n\r\nУстройство позволяет подключать и ПК (с любой ОС) для того, чтобы можно было просмотреть содержимое жесткого диска до уничтожения всех данных. Т.е. диск подключается к «стирателю», а сам «стиратель», через USB-порт подключается к компьютеру или ноутбуку. Данные затираются во всех областях жесткого диска, включая Host Protected Areas и любые другие секторы, которые закрыты для просмотра в любых ОС','уничтожение данных','2012-04-27 18:25:53',1),(9,'Аутентификация на Asp.net сайтах с помощью Rutoken WEB','Решение Рутокен WEB позволяет реализовать строгую аутентификацию для web-ресурсов, используя электронную подпись по ГОСТ Р 34-10.2001. Более подробно про алгоритмы можно прочитать в этой статье. Здесь покажем как сделан действующий вариант использования Рутокен WEB на сайтах под управлением Asp.net и приведем инструкцию по сборке.\r\n\r\nСделать так, чтобы все работало, действительно просто.','Рутокен WEB','2012-04-27 18:26:33',1);
/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `referrers`
--

DROP TABLE IF EXISTS `referrers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `referrers` (
  `timepoint` datetime NOT NULL,
  `token` char(32) NOT NULL,
  `url` varchar(200) NOT NULL,
  KEY `token` (`token`),
  KEY `created_at` (`timepoint`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `referrers`
--

LOCK TABLES `referrers` WRITE;
/*!40000 ALTER TABLE `referrers` DISABLE KEYS */;
/*!40000 ALTER TABLE `referrers` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
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
-- Table structure for table `users_data`
--

DROP TABLE IF EXISTS `users_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `users_data` (
  `user_id` int(10) unsigned NOT NULL,
  `nickname` varchar(20) NOT NULL DEFAULT '',
  `full_name` varchar(100) NOT NULL DEFAULT '',
  `email` varchar(50) DEFAULT NULL,
  `photo` varchar(255) DEFAULT NULL,
  `gender` enum('m','w') DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `non_indexed_data` text,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`),
  KEY `birthday` (`birthday`),
  KEY `nickname` (`nickname`),
  KEY `gender` (`gender`),
  KEY `full_name` (`full_name`),
  CONSTRAINT `users_data_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users_data`
--

LOCK TABLES `users_data` WRITE;
/*!40000 ALTER TABLE `users_data` DISABLE KEYS */;
INSERT INTO `users_data` VALUES (1,'','',NULL,NULL,'m',NULL,NULL);
/*!40000 ALTER TABLE `users_data` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2013-07-29 17:31:02