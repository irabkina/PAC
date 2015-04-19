-- phpMyAdmin SQL Dump
-- version 4.2.7.1
-- http://www.phpmyadmin.net
--
-- Host: localhost:3306
-- Generation Time: Dec 10, 2014 at 01:33 PM
-- Server version: 5.5.39
-- PHP Version: 5.4.32

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `pac'
--

-- --------------------------------------------------------

--
-- Table structure for table `patient'
CREATE TABLE IF NOT EXISTS `patient` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
  	`firstName` varchar(200) NOT NULL,
  	`lastName` varchar(200) NOT NULL,
  	PRIMARY KEY (ID)
) ;

-- Table structure for table `user'
CREATE TABLE IF NOT EXISTS `user` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
  	`firstName` varchar(200) NOT NULL,
  	`lastName` varchar(200) NOT NULL,
  	PRIMARY KEY (ID)
) ;

-- Table structure for table `patientAccess'
CREATE TABLE IF NOT EXISTS `patientAccess` (
	`patientId` int(11) NOT NULL,
  	`userId` int(11) NOT NULL
    
) ;

-- Table structure for table `datatypeUser'
CREATE TABLE IF NOT EXISTS `datatypeUser` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
  	`name` varchar(200) NOT NULL,
  	`userId` int(11) NOT NULL,
  	PRIMARY KEY (ID)
)  ;

-- Table structure for table `dataset`
CREATE TABLE IF NOT EXISTS `dataset` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`patientId` int(11) NOT NULL,
	`deviceLocation` varchar(200) NOT NULL,
	PRIMARY KEY (ID)
) ;

-- Table structure for table `activity`
CREATE TABLE IF NOT EXISTS `activity` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`activity` varchar(200) NOT NULL,
	`userId` int(11) NOT NULL,
	PRIMARY KEY (ID)
	)  ;

CREATE TABLE IF NOT EXISTS `activityDataset` (
	`ID` int(11) NOT NULL AUTO_INCREMENT,
	`activityID` int(11) NOT NULL,
	`startTime` datetime(3) NOT NULL,
	`endTime` datetime(3) NOT NULL,
	`datasetId` int(11) NOT NULL,
	PRIMARY KEY (ID)
) ;






