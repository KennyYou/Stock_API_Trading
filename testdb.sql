-- MySQL dump 10.13  Distrib 5.7.27, for Linux (x86_64)
--
-- Host: localhost    Database: testdb
-- ------------------------------------------------------
-- Server version	5.7.27-0ubuntu0.18.04.1

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
-- Table structure for table `He_stocks`
--

DROP TABLE IF EXISTS `He_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `He_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `He_stocks`
--

LOCK TABLES `He_stocks` WRITE;
/*!40000 ALTER TABLE `He_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `He_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Hemanth_stocks`
--

DROP TABLE IF EXISTS `Hemanth_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Hemanth_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Hemanth_stocks`
--

LOCK TABLES `Hemanth_stocks` WRITE;
/*!40000 ALTER TABLE `Hemanth_stocks` DISABLE KEYS */;
INSERT INTO `Hemanth_stocks` VALUES ('A',0),('B',0);
/*!40000 ALTER TABLE `Hemanth_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `JO_stocks`
--

DROP TABLE IF EXISTS `JO_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `JO_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `JO_stocks`
--

LOCK TABLES `JO_stocks` WRITE;
/*!40000 ALTER TABLE `JO_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `JO_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Ken_stocks`
--

DROP TABLE IF EXISTS `Ken_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ken_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Ken_stocks`
--

LOCK TABLES `Ken_stocks` WRITE;
/*!40000 ALTER TABLE `Ken_stocks` DISABLE KEYS */;
INSERT INTO `Ken_stocks` VALUES ('GOOG',12),('A',1),('K',2),('F',5),('G',1),('T',2);
/*!40000 ALTER TABLE `Ken_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Kenny_stocks`
--

DROP TABLE IF EXISTS `Kenny_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Kenny_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Kenny_stocks`
--

LOCK TABLES `Kenny_stocks` WRITE;
/*!40000 ALTER TABLE `Kenny_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `Kenny_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `LOL_stocks`
--

DROP TABLE IF EXISTS `LOL_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LOL_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `LOL_stocks`
--

LOCK TABLES `LOL_stocks` WRITE;
/*!40000 ALTER TABLE `LOL_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `LOL_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `OK_stocks`
--

DROP TABLE IF EXISTS `OK_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OK_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `OK_stocks`
--

LOCK TABLES `OK_stocks` WRITE;
/*!40000 ALTER TABLE `OK_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `OK_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `bobby_stocks`
--

DROP TABLE IF EXISTS `bobby_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `bobby_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `bobby_stocks`
--

LOCK TABLES `bobby_stocks` WRITE;
/*!40000 ALTER TABLE `bobby_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `bobby_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `eoifvheovn_stocks`
--

DROP TABLE IF EXISTS `eoifvheovn_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `eoifvheovn_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `eoifvheovn_stocks`
--

LOCK TABLES `eoifvheovn_stocks` WRITE;
/*!40000 ALTER TABLE `eoifvheovn_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `eoifvheovn_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `qw_stocks`
--

DROP TABLE IF EXISTS `qw_stocks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `qw_stocks` (
  `symbol` varchar(15) DEFAULT NULL,
  `amt` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `qw_stocks`
--

LOCK TABLES `qw_stocks` WRITE;
/*!40000 ALTER TABLE `qw_stocks` DISABLE KEYS */;
/*!40000 ALTER TABLE `qw_stocks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `students`
--

DROP TABLE IF EXISTS `students`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `students` (
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT NULL,
  `userid` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) DEFAULT NULL,
  `start_bal` decimal(12,2) DEFAULT NULL,
  `bal` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`userid`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `students`
--

LOCK TABLES `students` WRITE;
/*!40000 ALTER TABLE `students` DISABLE KEYS */;
INSERT INTO `students` VALUES ('Bill','Beerisnice',1,NULL,NULL,NULL),('Tom','tomcat',2,NULL,NULL,NULL),('Joe','coffeelover',3,NULL,NULL,NULL),('test3432','efwrfwrf',4,NULL,NULL,NULL),('Tom1234','456',5,NULL,NULL,NULL),('iuhi','uihiuhn',6,NULL,NULL,NULL),('Hello','Hello1',7,NULL,NULL,NULL),('eoifvheovn','weuirh',8,NULL,10000.00,10000.00),('bobby','123',9,NULL,10000.00,10000.00),('OK','ok',10,NULL,10000.00,10000.00),('qw','qw',11,NULL,10000.00,10000.00),('Ken','1',12,NULL,10000.00,680586.20),('He','her',13,NULL,10000.00,10000.00),('LOL','lol',14,NULL,10000.00,10000.00),('JO','JO',15,NULL,10000.00,10000.00),('Hemanth','123',16,NULL,10000.00,7070.03),('Kenny','2',17,NULL,10000.00,10000.00);
/*!40000 ALTER TABLE `students` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `trading`
--

DROP TABLE IF EXISTS `trading`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `trading` (
  `username` varchar(255) NOT NULL,
  `type` enum('buying','selling') DEFAULT NULL,
  `symbol` varchar(15) DEFAULT NULL,
  `shares` int(12) DEFAULT NULL,
  `date` datetime NOT NULL,
  `cost` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`username`,`date`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `trading`
--

LOCK TABLES `trading` WRITE;
/*!40000 ALTER TABLE `trading` DISABLE KEYS */;
INSERT INTO `trading` VALUES ('Hemanth','buying','G',100,'2019-10-31 21:29:30',3916.00),('Hemanth','selling','A',10,'2019-10-31 22:33:49',758.00),('Hemanth','buying','A',10,'2019-10-31 22:36:55',757.10),('Hemanth','selling','A',10,'2019-10-31 22:37:16',758.00),('Hemanth','selling','Z',10,'2019-10-31 22:37:31',326.00),('Hemanth','selling','G',10,'2019-10-31 22:39:40',392.10),('Hemanth','selling','A',10,'2019-10-31 22:47:55',758.00),('Hemanth','selling','AA',10,'2019-10-31 22:48:04',208.00),('Hemanth','buying','A',10,'2019-10-31 22:55:32',757.20),('Hemanth','buying','A',10,'2019-10-31 23:06:47',757.10),('Hemanth','buying','A',10,'2019-10-31 23:08:08',757.20),('Hemanth','buying','A',10,'2019-10-31 23:09:05',757.10),('Hemanth','buying','B',10,'2019-10-31 23:10:02',584.20),('Hemanth','buying','A',10,'2019-10-31 23:22:16',757.20),('Hemanth','selling','A',30,'2019-11-13 15:34:00',2313.08),('Hemanth','selling','B',10,'2019-11-13 15:34:17',599.95),('Ken','buying','B',12,'2019-10-31 21:12:21',701.04),('Ken','buying','K',1,'2019-10-31 21:58:07',63.41),('Ken','buying','A',10,'2019-10-31 22:07:40',757.10),('Ken','buying','A',10,'2019-10-31 22:11:58',757.10),('Ken','buying','A',10,'2019-10-31 22:12:21',757.10),('Ken','buying','A',5,'2019-10-31 22:13:07',378.55),('Ken','selling','A',10,'2019-10-31 22:14:14',758.00),('Ken','selling','G',10,'2019-10-31 22:14:44',392.10),('Ken','selling','D',10,'2019-10-31 22:15:33',826.00),('Ken','selling','Z',10,'2019-10-31 22:15:44',325.90),('Ken','selling','VGLT',100,'2019-10-31 22:16:25',8662.00),('Ken','selling','A',100,'2019-10-31 22:20:00',7580.00),('Ken','selling','A',1000,'2019-10-31 22:23:41',75800.00),('Ken','selling','B',10000,'2019-10-31 22:29:44',585000.00),('Ken','selling','Y',10,'2019-10-31 22:33:32',7829.30),('Ken','selling','GOOGL',1,'2019-10-31 22:33:51',1259.52),('Ken','selling','O',10,'2019-10-31 22:39:25',818.50),('Ken','buying','A',10,'2019-10-31 23:27:55',757.10),('Ken','selling','A',4,'2019-10-31 23:28:07',303.20),('Ken','selling','A',6,'2019-10-31 23:28:27',454.80),('Ken','buying','GOOG',100,'2019-10-31 23:29:32',125829.00),('Ken','selling','GOOG',100,'2019-10-31 23:29:51',126040.00),('Ken','buying','A',10,'2019-10-31 23:50:38',757.10),('Ken','selling','A',10,'2019-10-31 23:51:15',758.00),('Ken','buying','GOOG',12,'2019-11-01 01:05:09',15099.48),('Ken','buying','A',1,'2019-11-01 01:08:43',75.72),('Ken','buying','K',2,'2019-11-01 01:14:23',126.82),('Ken','buying','F',5,'2019-11-01 01:14:28',42.90),('Ken','buying','G',1,'2019-11-07 12:59:54',40.34),('Ken','selling','G',1,'2019-11-07 13:00:12',40.36),('Ken','buying','G',1,'2019-11-07 13:00:49',40.34),('Ken','buying','G',1,'2019-11-07 13:29:34',40.30),('Ken','selling','G',2,'2019-11-07 13:29:41',80.60),('Ken','buying','G',1,'2019-11-07 13:38:37',40.36),('Ken','buying','T',2,'2019-11-13 15:47:42',78.32);
/*!40000 ALTER TABLE `trading` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2019-11-20 15:47:24
