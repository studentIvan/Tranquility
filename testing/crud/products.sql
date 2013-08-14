-- phpMyAdmin SQL Dump
-- version 4.0.4.1
-- http://www.phpmyadmin.net
--
-- Хост: localhost
-- Время создания: Авг 14 2013 г., 13:24
-- Версия сервера: 5.5.24
-- Версия PHP: 5.3.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `tranquility`
--

-- --------------------------------------------------------

--
-- Структура таблицы `products`
--

CREATE TABLE IF NOT EXISTS `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `added` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `added` (`added`),
  KEY `price` (`price`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=17 ;

--
-- Дамп данных таблицы `products`
--

INSERT INTO `products` (`id`, `title`, `price`, `added`) VALUES
(1, 'Яблоко Красное (Краснодар)', '10.25', '2012-08-13 19:42:41'),
(2, 'Яблоко Желтое (Краснодар)', '10.00', '2013-07-13 19:45:16'),
(3, 'Яблоко Зеленое (Сарапул)', '8.00', '2013-06-13 19:45:51'),
(4, 'Помидор Огородный (Сарапул)', '25.00', '2013-05-13 19:46:17'),
(5, 'Арбуз (Астрахань)', '7.90', '2013-05-13 19:46:47'),
(6, 'Шоколад Alpen Gold (Казань)', '500.00', '2013-07-13 19:48:42'),
(7, 'Морковь (Сарапул)', '18.00', '2013-06-05 19:49:10'),
(8, 'Яблоко Сладкое Большое (Краснодар)', '32.00', '2013-08-13 19:49:30'),
(9, 'Картошка (Сарапул)', '12.00', '2013-08-13 19:50:03'),
(10, 'Яблоко Антоновка (Сарапул)', '4.00', '2013-04-08 19:51:56'),
(11, 'Капуста (Сарапул)', '8.50', '2013-08-13 19:53:46'),
(12, 'Картошка (Шевырялово)', '11.78', '2013-08-13 19:54:10'),
(13, 'Картошка (Краснодар)', '37.00', '2013-08-13 19:55:21'),
(14, 'Арбуз (Краснодар)', '15.00', '2013-08-13 19:55:44'),
(15, 'Морковь (Краснодар)', '10.00', '2013-08-13 19:56:20'),
(16, 'Мука (Шевырялово)', '25.00', '2013-08-13 19:56:39');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
