-- phpMyAdmin SQL Dump
-- version 4.0.10.7
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Oct 30, 2015 at 09:39 AM
-- Server version: 5.5.42-cll-lve
-- PHP Version: 5.4.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `AureliaSkinDB`
--

-- --------------------------------------------------------

--
-- Table structure for table `emails`
--

CREATE TABLE IF NOT EXISTS `emails` (
  `id` int(64) NOT NULL AUTO_INCREMENT,
  `address` varchar(64) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=222 ;

--
-- Dumping data for table `emails`
--

INSERT INTO `emails` (`id`, `address`) VALUES
(1, 'all@heyimrm.co.uk'),
(2, 'romeromckaydesigns@gmail.com'),
(3, 'romeromckaydesigns@gmail.com'),
(4, 'romeromckaydesigns@gmail.com'),
(5, 'romero@wallacehealth.co.uk'),
(6, 'all@heyimrm.co.uk'),
(7, 'romeromckaydesigns@gmail.com'),
(8, 'romeromckaydesigns@gmail.com'),
(9, 'priya@wallacehealth.co.uk'),
(10, 'all@heyimrm.co.uk'),
(11, 'romeromckaydesigns@gmail.com'),
(12, 'romeromckaydesigns@gmail.com'),
(13, 'romeromckaydesigns@gmail.com'),
(14, 'all@heyimrm.co.uk'),
(15, 'romeromckaydesigns@gmail.com'),
(16, 'romeromckaydesigns@gmail.com'),
(17, 'romeromckaydesigns@gmail.com'),
(18, 'romeromckaydesigns@gmail.com'),
(19, 'romeromckaydesigns@gmail.com'),
(20, 'romeromckaydesigns@gmail.com'),
(21, 'romeromckaydesigns@gmail.com'),
(22, 'priya@wallacehealth.co.uk'),
(23, 'victoria@wallacehealth.co.uk'),
(24, 'romeromckaydesigns@gmail.com'),
(25, 'romeromckaydesigns@gmail.com'),
(26, 'romeromckaydesigns@gmail.com'),
(27, 'romeromckaydesigns@gmail.com'),
(28, 'romeromckaydesigns@gmail.com'),
(29, 'romeromckaydesigns@gmail.com'),
(30, 'romeromckaydesigns@gmail.com'),
(31, 'antonia@aureliaskincare.com'),
(32, 'antonia_knox@hotmail.co.uk'),
(33, 'antonia@aureliaskincare.com'),
(34, 'romeromckaydesigns@gmail.com'),
(35, 'romeromckaydesigns@gmail.com'),
(36, 'romeromckaydesigns@gmail.com'),
(37, 'romeromckaydesigns@gmail.com'),
(38, 'romeromckaydesigns@gmail.com'),
(39, 'romeromckaydesigns@gmail.com'),
(40, 'romeromckaydesigns@gmail.com'),
(41, 'romeromckaydesigns@gmail.com'),
(42, 'romeromckaydesigns@gmail.com'),
(43, 'priya@wallacehealth.co.uk'),
(44, 'romeromckaydesigns@gmail.com'),
(45, 'priya@wallacehealth.co.uk'),
(46, 'priya@wallacehealth.co.uk'),
(47, 'jess@aureliaskincare.com'),
(48, 'priya@wallacehealth.co.uk'),
(49, 'romeromckaydesigns@gmail.com'),
(50, 'romeromckaydesigns@gmail.com'),
(51, 'romeromckaydesigns@gmail.com'),
(52, 'romeromckaydesigns@gmail.com'),
(53, 'romeromckaydesigns@gmail.com'),
(54, 'romeromckaydesigns@gmail.com'),
(55, 'priya@wallacehealth.co.uk'),
(56, 'priya@wallacehealth.co.uk'),
(57, 'romeromckaydesigns@gmail.com'),
(58, 'romeromckaydesigns@gmail.com'),
(59, 'priya@wallacehealth.co.uk'),
(60, 'victoria@wallacehealth.co.uk'),
(61, 'eastonvictoria@gmail.com'),
(62, 'priya@wallacehealth.co.uk'),
(63, 'p'),
(64, 'priya@wallacehealth.co.uk'),
(65, 'romeromckaydesigns@gmail.com'),
(66, 'romeromckaydesigns@gmail.com'),
(67, 'romeromckaydesigns@gmail.com'),
(68, 'romeromckaydesigns@gmail.com'),
(69, 'romeromckaydesigns@gmail.com'),
(70, 'romeromckaydesigns@gmail.com'),
(71, 'romeromckaydesigns@gmail.com'),
(72, 'romeromckaydesigns@gmail.com'),
(73, 'romeromckaydesigns@gmail.com'),
(74, 'romeromckaydesigns@gmail.com'),
(75, 'romeromckaydesigns@gmail.com'),
(76, 'romeromckaydesigns@gmail.com'),
(77, 'priya@wallacehealth.co.uk'),
(78, 'romeromckaydesigns@gmail.com'),
(79, 'romeromckaydesigns@gmail.com'),
(80, 'romeromckaydesigns@gmail.com'),
(81, 'romeromckaydesigns@gmail.com'),
(82, 'romeromckaydesigns@gmail.com'),
(83, 'romeromckaydesigns@gmail.com'),
(84, 'romeromckaydesigns@gmail.com'),
(85, 'romeromckaydesigns@gmail.com'),
(86, 'romeromckaydesigns@gmail.com'),
(87, 'romeromckaydesigns@gmail.com'),
(88, 'romeromckaydesigns@gmail.com'),
(89, 'romeromckaydesigns@gmail.com'),
(90, 'romeromckaydesigns@gmail.com'),
(91, 'romeromckaydesigns@gmail.com'),
(92, 'romeromckaydesigns@gmail.com'),
(93, 'romeromckaydesigns@gmail.com'),
(94, 'romeromckaydesigns@gmail.com'),
(95, 'romeromckaydesigns@gmail.com'),
(96, 'romeromckaydesigns@gmail.com'),
(97, 'romeromckaydesigns@gmail.com'),
(98, 'romeromckaydesigns@gmail.com'),
(99, 'romeromckaydesigns@gmail.com'),
(100, 'romeromckaydesigns@gmail.com'),
(101, 'romeromckaydesigns@gmail.com'),
(102, 'romeromckaydesigns@gmail.com'),
(103, 'romeromckaydesigns@gmail.com'),
(104, 'romeromckaydesigns@gmail.com'),
(105, 'romeromckaydesigns@gmail.com'),
(106, 'romeromckaydesigns@gmail.com'),
(107, 'romeromckaydesigns@gmail.com'),
(108, 'priya@wallacehealth.co.uk'),
(109, 'romeromckaydesigns@gmail.com'),
(110, 'romeromckaydesigns@gmail.com'),
(111, 'romeromckaydesigns@gmail.com'),
(112, 'romeromckaydesigns@gmail.com'),
(113, 'romeromckaydesigns@gmail.com'),
(114, 'romeromckaydesigns@gmail.com'),
(115, 'romeromckaydesigns@gmail.com'),
(116, 'romeromckaydesigns@gmail.com'),
(117, 'antonia@aureliaskincare.com'),
(118, 'romeromckaydesigns@gmail.com'),
(119, 'priya@wallacehealth.co.uk'),
(120, 'romeromckaydesigns@gmail.com'),
(121, 'romeromckaydesigns@gmail.com'),
(122, 'romero@wallacehealth.co.uk'),
(123, 'romero@wallacehealth.co.uk'),
(124, 'romero@wallacehealth.co.uk'),
(125, 'romero@wallacehealth.co.uk'),
(126, 'priya@wallacehealth.co.uk'),
(127, 'pramanah88@gmail.com'),
(128, 'romeromckaydesigns@gmail.com'),
(129, 'pramanah88@gmail.com'),
(130, 'romeromckaydesigns@gmail.com'),
(131, 'romeromckaydesigns@gmail.com'),
(132, 'romeromckaydesigns@gmail.com'),
(133, 'romeromckaydesigns@gmail.com'),
(134, 'romeromckaydesigns@gmail.com'),
(135, 'romeromckaydesigns@gmail.com'),
(136, ''),
(137, 'romeromckaydesigns@gmail.com'),
(138, 'pramanah88@gmail.com'),
(139, 'pramanah88@gmail.com'),
(140, 'romeromckaydesigns@gmail.com'),
(141, 'romeromckaydesigns@gmail.com'),
(142, 'romeromckaydesigns@gmail.com'),
(143, 'romeromckaydesigns@gmail.com'),
(144, 'victoria@wallacehealth.co.uk'),
(145, 'pramanah88@gmail.com'),
(146, 'romeromckaydesigns@gmail.com'),
(147, 'victoria.'),
(148, 'all@heyimrm.co.uk'),
(149, 'romeromckaydesigns@gmail.com'),
(150, 'romeromckaydesigns@gmail.com'),
(151, 'eastonvictoria@gmail.com'),
(152, 'pramanah88@gmail.com'),
(153, 'romeromckaydesigns@gmail.com'),
(154, 'victoria@wallacehealth.co.uk'),
(155, 'jess.palmertomkinson@gmail.com'),
(156, 'romeromckaydesigns@gmail.com'),
(157, 'romeromckaydesigns@gmail.com'),
(158, 'romeromckaydesigns@gmail.com'),
(159, 'romeromckaydesigns@gmail.com'),
(160, 'romeromckaydesigns@gmail.com'),
(161, 'romeromckaydesigns@gmail.com'),
(162, 'romeromckaydesigns@gmail.com'),
(163, 'priya@wallacehealth.co.uk'),
(164, 'priya@wallacehealth.co.uk'),
(165, 'pramanah88@gmail.com'),
(166, 'romeromckaydesigns@gmail.com'),
(167, 'pramanah88@gmail.com'),
(168, 'pramanah88@gmail.com'),
(169, 'pramanah88@gmail.com'),
(170, 'romeromckaydesigns@gmail.com'),
(171, 'pramanah88@gmail.com'),
(172, 'romeromckaydesigns@gmail.com'),
(173, 'romeromckaydesigns@gmail.com'),
(174, 'victoria@wallacehealth.co.uk'),
(175, 'romeromckaydesigns@gmail.com'),
(176, 'romeromckaydesigns@gmail.com'),
(177, 'romeromckaydesigns@gmail.com'),
(178, 'romeromckaydesigns@gmail.com'),
(179, 'romeromckaydesigns@gmail.com'),
(180, 'romeromckaydesigns@gmail.com'),
(181, 'romeromckaydesigns@gmail.com'),
(182, 'romeromckaydesigns@gmail.com'),
(183, 'romeromckaydesigns@gmail.com'),
(184, 'romero@wallacehealth.co.uk'),
(185, 'romeromckaydesigns@gmail.com'),
(186, 'victoria@wallacehealth.co.uk'),
(187, 'romeromckaydesigns@gmail.com'),
(188, 'romeromckaydesigns@gmail.com'),
(189, 'romeromckaydesigns@gmail.com'),
(190, 'romeromckaydesigns@gmail.com'),
(191, 'romeromckaydesigns@gmail.com'),
(192, 'romeromckaydesigns@gmail.com'),
(193, 'romeromckaydesigns@gmail.com'),
(194, 'eastonvictoria@gmail.com'),
(195, 'romeromckaydesigns@gmail.com'),
(196, 'romeromckaydesigns@gmail.com'),
(197, 'romeromckaydesigns@gmail.com'),
(198, 'romeromckaydesigns@gmail.com'),
(199, 'pramanah88@gmail.com'),
(200, 'romeromckaydesigns@gmail.com'),
(201, 'romeromckaydesigns@gmail.com'),
(202, 'romeromckaydesigns@gmail.com'),
(203, 'pramanah88@gmail.com'),
(204, 'romeromckaydesigns@gmail.com'),
(205, 'romeromckaydesigns@gmail.com'),
(206, 'romeromckaydesigns@gmail.com'),
(207, 'romeromckaydesigns@gmail.com'),
(208, 'romeromckaydesigns@gmail.com'),
(209, 'romeromckaydesigns@gmail.com'),
(210, 'romeromckaydesigns@gmail.com'),
(211, 'romeromckaydesigns@gmail.com'),
(212, 'romeromckaydesigns@gmail.com'),
(213, 'pramanah88@gmail.com'),
(214, 'romeromckaydesigns@gmail.com'),
(215, 'romeromckaydesigns@gmail.com'),
(216, 'romeromckaydesigns@gmail.com'),
(217, 'romeromckaydesigns@gmail.com'),
(218, 'romeromckaydesigns@gmail.com'),
(219, 'romeromckaydesigns@gmail.com'),
(220, 'pramanah88@gmail.com'),
(221, '');

-- --------------------------------------------------------

--
-- Table structure for table `questionsdata`
--

CREATE TABLE IF NOT EXISTS `questionsdata` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `question1` varchar(200) NOT NULL,
  `question2` varchar(200) NOT NULL,
  `question3` varchar(200) NOT NULL,
  `question4` varchar(200) NOT NULL,
  `question5` varchar(200) NOT NULL,
  `question6` varchar(200) NOT NULL,
  `question7` varchar(200) NOT NULL,
  `question8` varchar(200) NOT NULL,
  `question9` varchar(200) NOT NULL,
  `question10` varchar(200) NOT NULL,
  `question11` varchar(200) NOT NULL,
  `question12` varchar(200) NOT NULL,
  `question13` varchar(200) NOT NULL,
  `question14` varchar(200) NOT NULL,
  `question15` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=554 ;

--
-- Dumping data for table `questionsdata`
--

INSERT INTO `questionsdata` (`id`, `question1`, `question2`, `question3`, `question4`, `question5`, `question6`, `question7`, `question8`, `question9`, `question10`, `question11`, `question12`, `question13`, `question14`, `question15`) VALUES
(0, 'wallace', 'excercise', '1', 'regime1', 'never', '\n', 'asyoung', '0', '-1', '', '1', '', 'beauty', 'awake', '0'),
(1, 'wallace', 'coffee', '2', 'regime2', 'sometimes', '', 'natural', '1', '-1', 'travelling', '33', '', 'ingredients', 'wine', '100'),
(2, 'wallace', 'social_media', '3', 'regime3', 'always', '', '', '2', '-1', 'work', '66', '', 'trust', 'sleep', '-1'),
(3, 'wallace', 'breakfast', '', 'regime4', '', '', 'fresh', '3', '-1', 'children_2', '-1', '', 'null', 'null', '-1'),
(4, 'wallace', 'children', '', '', '', '', '', '4', '-1', 'somethingelse', '-1', '', 'null', 'null', '-1'),
(5, 'wallace', 'house_work', '', '', '', '', 'thebest', '5', '-1', 'relaxing', '-1', '', 'null', 'null', '-1'),
(6, 'wallace', 'shower', '', '', '', '', '', '-1', '-1', 'shopping', '-1', '', 'null', 'null', '-1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data' AUTO_INCREMENT=2 ;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `user_name`, `user_password_hash`, `user_email`) VALUES
(1, 'hellodemo', '$2y$10$XMVTUJJNhqmAbcM0kwE1/.BIaGg8PvI4e4KUtGnmrWE7h.uVJRqVW', 'hellodemo@hellodemo.com');

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
