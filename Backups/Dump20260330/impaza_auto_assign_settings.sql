-- MySQL dump 10.13  Distrib 8.0.45, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: impaza
-- ------------------------------------------------------
-- Server version	8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `auto_assign_settings`
--

DROP TABLE IF EXISTS `auto_assign_settings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `auto_assign_settings` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `standby_start_time` time NOT NULL DEFAULT '04:30:00',
  `standby_end_time` time NOT NULL DEFAULT '08:00:00',
  `weekend_standby_enabled` tinyint(1) NOT NULL DEFAULT '1',
  `consider_leave` tinyint(1) NOT NULL DEFAULT '1',
  `consider_region` tinyint(1) NOT NULL DEFAULT '1',
  `consider_zones` tinyint(1) NOT NULL DEFAULT '0',
  `scope_section_id` int unsigned DEFAULT NULL,
  `scope_region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `auto_assign_enabled` tinyint(1) NOT NULL DEFAULT '0',
  `updated_by` int unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `auto_assign_settings`
--

LOCK TABLES `auto_assign_settings` WRITE;
/*!40000 ALTER TABLE `auto_assign_settings` DISABLE KEYS */;
INSERT INTO `auto_assign_settings` VALUES (1,'16:30:00','06:00:00',1,1,1,0,NULL,NULL,0,NULL,'2026-03-30 13:30:44','2026-03-30 13:30:44');
/*!40000 ALTER TABLE `auto_assign_settings` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-30 16:27:59
