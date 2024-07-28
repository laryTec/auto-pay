-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- 主機： localhost:3306
-- 產生時間： 2022 年 08 月 29 日 22:24
-- 伺服器版本： 5.6.41-84.1
-- PHP 版本： 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `autopay`
--

-- --------------------------------------------------------

--
-- 資料表結構 `record`
--

CREATE TABLE `record` (
  `loginId` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `MerchantTradeNo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `TradeNo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `money` int(8) NOT NULL,
  `gamemoney` int(8) NOT NULL,
  `payway` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `bank_num` varchar(4) COLLATE utf8_unicode_ci NOT NULL,
  `bank_account` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `cvs_num` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `char_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `db_name` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `pay_status` int(1) NOT NULL,
  `upd_date` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
