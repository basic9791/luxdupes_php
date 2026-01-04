-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- 主机： 127.0.0.1:3306
-- 生成日期： 2025-09-09 08:03:31
-- 服务器版本： 10.11.10-MariaDB-log
-- PHP 版本： 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 数据库： `u962307395_luxdupes`
--

-- --------------------------------------------------------

--
-- 表的结构 `luxdupes_website_tag`
--

CREATE TABLE `luxdupes_website_tag` (
  `tagid` int(11) UNSIGNED NOT NULL,
  `name` char(32) NOT NULL DEFAULT '',
  `count` int(11) NOT NULL DEFAULT 0,
  `icon` int(11) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

--
-- 转存表中的数据 `luxdupes_website_tag`
--

INSERT INTO `luxdupes_website_tag` (`tagid`, `name`, `count`, `icon`) VALUES
(1, 'Burberry', 15484, 0),
(2, 'Gucci', 30578, 0),
(3, 'Dior', 30659, 0),
(4, 'Celine', 11356, 0),
(5, 'Hermès', 21571, 0),
(6, 'Chanel', 35421, 0),
(7, 'Bottega Veneta', 5526, 0),
(8, 'Louis Vuitton/LV', 35718, 0),
(9, 'YSL', 12604, 0),
(10, 'Goyard', 720, 0),
(11, 'JAEGERLECOULTRE', 180, 0),
(12, 'FRANCK MULLER', 118, 0),
(13, 'LONGINES', 299, 0),
(14, 'Loewe', 10287, 0),
(15, 'Prada', 17829, 0),
(16, 'Chloé', 878, 0),
(17, 'Balenciaga', 12935, 0),
(18, 'Fendi', 12939, 0),
(19, 'PATEK PHILIPPE', 631, 0),
(20, 'Panerai', 617, 0),
(21, 'Blancpain', 138, 0),
(22, 'Vacheron Constantin', 207, 0),
(23, 'Audemars Piguet', 638, 0),
(24, 'Delvaux', 432, 0),
(25, 'Valentino', 8024, 0),
(26, 'IWC', 527, 0),
(27, 'Ladies', 639, 0),
(28, 'Miu Miu', 8991, 0),
(29, 'TAG Heuer', 57, 0),
(30, 'ROLEX', 977, 0),
(31, 'OMEGA', 662, 0),
(32, 'Roger Dubuis', 92, 0),
(33, 'Chopard', 129, 0),
(34, 'Bulgari', 1419, 0),
(35, 'SevenFriday', 191, 0),
(36, 'Breitling', 192, 0),
(37, 'BVLGARI', 272, 0),
(38, 'TUDOR', 109, 0),
(39, 'Qeelin', 897, 0),
(40, 'Chrome Hearts', 4517, 0),
(41, 'Cartier', 1247, 0),
(42, 'Ferragamo', 3896, 0),
(43, 'Niche', 1294, 0),
(44, 'Montblanc', 649, 0),
(45, 'Versace', 5604, 0),
(46, 'Maurice Lacroix', 113, 0),
(47, 'Breguet', 92, 0),
(48, 'PIAGET', 129, 0),
(49, 'Van CleefArpels', 1538, 0),
(50, 'TiffanyCo', 679, 0),
(51, 'McQueen', 1900, 0),
(52, 'Jimmy Choo', 1330, 0),
(53, 'TODS', 1833, 0),
(54, 'Roger Vivier', 1478, 0),
(55, 'Alexander Wang', 2136, 0),
(56, 'Yeezy', 866, 0),
(57, 'Dunk', 2755, 0),
(58, 'Brunello Cucinelli', 319, 0),
(59, 'Maison Margiela', 914, 0),
(60, 'Christian Louboutin', 288, 0),
(61, 'AIR JORDAN', 3697, 0),
(62, 'Rick Owens', 116, 0),
(63, 'Balmain', 2800, 0),
(64, 'Nike', 4969, 0),
(65, 'Marni', 61, 0),
(66, 'Loro piana', 719, 0),
(67, 'Sergio', 295, 0),
(68, 'Golden Goose', 328, 0),
(69, 'HOGAN', 125, 0),
(70, 'UGG', 800, 0),
(71, 'Manolo Blahnik', 59, 0),
(72, 'Graff', 569, 0),
(73, 'Bally', 2252, 0),
(74, 'JIL SANDER', 64, 0),
(75, 'Givenchy', 5489, 0),
(76, 'New Balance', 1201, 0),
(77, 'Adidas', 3189, 0),
(78, 'Basketball Shoes', 399, 0),
(79, 'HUBLOT', 180, 0),
(80, 'Joker', 4, 0),
(81, 'Richard Mille', 173, 0),
(82, 'Glashütte Original', 23, 0),
(83, 'Thom Browne', 3657, 0),
(84, 'ZENITH', 16, 0),
(85, 'The North Face', 514, 0),
(86, 'OffWhite', 788, 0),
(87, 'Moncler', 6240, 0),
(88, 'Kenzo', 1090, 0),
(89, 'Zimmermann', 522, 0),
(90, 'MaxMara', 1123, 0),
(91, 'Mastermind Japan', 500, 0),
(92, 'Cashmere Coats', 499, 0),
(94, 'Other Brands of', 2571, 0),
(95, 'Moschino', 828, 0),
(96, 'Armani', 1988, 0),
(97, 'Acne Studios', 633, 0),
(98, 'Premium Jackets', 1246, 0),
(99, 'Zegna', 3062, 0),
(100, 'Canada Goose', 138, 0),
(101, 'Berluti', 491, 0),
(102, 'MCM', 725, 0),
(103, 'Shawl', 43, 0),
(105, 'Vivienne Westwood', 1054, 0),
(106, 'Blanket', 45, 0),
(107, 'Summer Shorts', 2229, 0),
(108, 'Asics', 112, 0),
(109, 'Puma', 181, 0),
(110, 'Ecco', 102, 0),
(111, 'Sandals and Slippers', 446, 0),
(112, 'Vans', 159, 0),
(113, 'Fred', 27, 0),
(114, 'GGCC', 138, 0),
(115, 'Salomon', 57, 0),
(116, 'Dsquared', 9, 0),
(117, 'Mihara Yasuhiro', 13, 0),
(118, 'Converse', 75, 0),
(119, 'MLB', 202, 0),
(120, 'Timberland', 23, 0),
(121, 'ORIS', 13, 0),
(122, 'GirardPerregaux', 3, 0),
(124, 'AIMER MEN', 7975, 0),
(93, 'DolceGabbana/DG', 4186, 0),
(126, 'CLARINS', 1184, 0),
(127, 'margiela', 31, 0),
(128, 'AIGLE', 624, 0),
(129, 'HUGO Boss', 170, 0),
(130, 'LI NING', 149, 0),
(131, 'Off White', 92, 0),
(132, 'Gina', 613, 0),
(133, 'TheNorthFace', 230, 0),
(134, 'Lanvin', 41, 0),
(135, 'Michael Kors', 88, 0),
(136, 'NY', 139, 0),
(137, 'Essentials', 50, 0),
(138, 'CASIO', 30, 0),
(139, 'TOM FORD', 161, 0),
(140, 'STUSSY', 72, 0),
(141, 'FOG Essential', 165, 0),
(142, 'Stone Island', 99, 0),
(143, 'Evisu', 83, 0),
(144, 'Amiri', 52, 0),
(145, 'Boy London', 59, 0),
(146, 'master mind', 14, 0),
(147, 'Bottega', 156, 0),
(148, 'Parma', 155, 0),
(149, 'RayBan', 55, 0),
(150, 'Maison Michel', 154, 0),
(151, 'MiuMiu', 105, 0),
(152, 'WellDone', 170, 0),
(153, 'Carhartt', 63, 0),
(154, 'Birkenstock', 108, 0),
(155, 'RagBone', 22, 0),
(156, 'Dickies', 36, 0),
(157, 'Jean Paul Gaultier', 20, 0),
(158, 'Lola Rose', 17, 0),
(159, 'Alexander McQueen', 522, 0),
(160, 'Calvin Klein', 14, 0),
(161, 'Descente', 12, 0),
(162, 'Palm Angels', 13, 0),
(163, 'Amina Muaddi', 60, 0),
(164, 'Movado', 7, 0),
(165, 'Jordan', 10, 0),
(166, 'Daniel Wellington', 79, 0),
(167, 'Elizabeth Arden', 7, 0),
(168, 'MachMach', 20, 0),
(169, 'kolon sport', 53, 0),
(170, 'ACOLDWALL', 2, 0),
(171, 'Snow Peak', 8, 0),
(172, 'Under Armour', 11, 0),
(173, 'COSME DECORTE 黛珂', 3, 0),
(174, 'Christopher Kane', 17, 0),
(175, 'Tory Burch', 15, 0),
(176, 'Swarovski', 1, 0),
(177, 'POLO', 42, 0),
(178, 'Kelly', 8, 0),
(179, 'GENTLE MONSTER/GM', 164, 0),
(180, 'MARIE MAGE', 19, 0),
(181, 'Apm monaco', 11, 0),
(182, 'Salvatore Ferragamo', 3, 0),
(183, 'Nicholas Kirkwood', 2, 0),
(184, 'Jacquemus', 4, 0),
(185, 'Yohji Yamamoto', 2, 0),
(186, 'PREMIATA', 1, 0),
(187, 'Grand Seiko', 2, 0),
(188, 'Champion', 6, 0),
(189, 'TISSOT', 6, 0),
(190, 'MIDO', 10, 0),
(191, 'NOMOS', 2, 0),
(192, 'LangeSöhne', 4, 0),
(193, 'Corum', 3, 0),
(194, 'PARMIGIANI', 2, 0),
(195, 'BellRoss', 19, 0),
(196, 'ULYSSE NARDIN', 16, 0),
(197, 'Lange', 2, 0),
(198, 'Matthew Williamson', 1, 0),
(199, 'Polo ralph lauren', 3, 0);

--
-- 转储表的索引
--

--
-- 表的索引 `luxdupes_website_tag`
--
ALTER TABLE `luxdupes_website_tag`
  ADD PRIMARY KEY (`tagid`),
  ADD UNIQUE KEY `name_2` (`name`),
  ADD KEY `name` (`name`);

--
-- 在导出的表使用AUTO_INCREMENT
--

--
-- 使用表AUTO_INCREMENT `luxdupes_website_tag`
--
ALTER TABLE `luxdupes_website_tag`
  MODIFY `tagid` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=200;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
