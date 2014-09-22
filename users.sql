-- phpMyAdmin SQL Dump
-- version 4.1.4
-- http://www.phpmyadmin.net
--
-- Хост: 127.0.0.1
-- Время создания: Сен 22 2014 г., 10:05
-- Версия сервера: 5.6.15-log
-- Версия PHP: 5.4.24

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- База данных: `users`
-- 

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `login` varchar(100) NOT NULL,
  `nick` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=21 ;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `login`, `nick`, `email`) VALUES
(1, 'user0', 'userNick0', 'user0@mail.ru'),
(2, 'user1', 'userNick1', 'user1@mail.ru'),
(3, 'user2', 'userNick2', 'user2@mail.ru'),
(4, 'user3', 'userNick3', 'user3@mail.ru'),
(5, 'user4', 'userNick4', 'user4@mail.ru'),
(6, 'user5', 'userNick5', 'user5@mail.ru'),
(7, 'user6', 'userNick6', 'user6@mail.ru'),
(8, 'user7', 'userNick7', 'user7@mail.ru'),
(9, 'user8', 'userNick8', 'user8@mail.ru'),
(10, 'user9', 'userNick9', 'user9@mail.ru'),
(11, 'user10', 'userNick10', 'user10@mail.ru'),
(12, 'user11', 'userNick11', 'user11@mail.ru'),
(13, 'user12', 'userNick12', 'user12@mail.ru'),
(14, 'user13', 'userNick13', 'user13@mail.ru'),
(15, 'user14', 'userNick14', 'user14@mail.ru'),
(16, 'user15', 'userNick15', 'user15@mail.ru'),
(17, 'user16', 'userNick16', 'user16@mail.ru'),
(18, 'user17', 'userNick17', 'user17@mail.ru'),
(19, 'user18', 'userNick18', 'user18@mail.ru'),
(20, 'user19', 'userNick19', 'user19@mail.ru');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
