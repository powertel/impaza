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
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `positions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `section_id` int unsigned NOT NULL,
  `position` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `positions_section_id_foreign` (`section_id`),
  CONSTRAINT `positions_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `positions`
--

LOCK TABLES `positions` WRITE;
/*!40000 ALTER TABLE `positions` DISABLE KEYS */;
INSERT INTO `positions` VALUES (1,1,'Network Controller','2026-03-30 13:35:16','2026-03-30 13:35:16'),(2,1,'NOC Engineer','2026-03-30 13:35:16','2026-03-30 13:35:16'),(3,1,'Technician','2026-03-30 13:35:16','2026-03-30 13:35:16'),(4,2,'Chief Technician','2026-03-30 13:35:16','2026-03-30 13:35:16'),(5,2,'Technician','2026-03-30 13:35:16','2026-03-30 13:35:16'),(6,2,'Network Operations Manager','2026-03-30 13:35:16','2026-03-30 13:35:16'),(7,4,'Network Planning Manager','2026-03-30 13:35:16','2026-03-30 13:35:16'),(8,6,'Customer Experience Manager','2026-03-30 13:35:16','2026-03-30 13:35:16'),(9,6,'Call Centre Agent','2026-03-30 13:35:16','2026-03-30 13:35:16'),(10,5,'IT Manager','2026-03-30 13:35:16','2026-03-30 13:35:16'),(11,5,'Systems Enginerr','2026-03-30 13:35:16','2026-03-30 13:35:16'),(12,5,'Systems Support Technician','2026-03-30 13:35:16','2026-03-30 13:35:16'),(13,5,'Software Development Engineer','2026-03-30 13:35:16','2026-03-30 13:35:16'),(14,7,'Account Manager','2026-03-30 13:35:16','2026-03-30 13:35:16'),(15,7,'Marketing & Corporate Sales Manager','2026-03-30 13:35:16','2026-03-30 13:35:16'),(16,2,'Principal Engineer','2026-03-30 13:35:16','2026-03-30 13:35:16'),(17,3,'Chief Technician','2026-03-30 13:35:16','2026-03-30 13:35:16'),(18,3,'Technician','2026-03-30 13:35:16','2026-03-30 13:35:16'),(19,3,'Linesman','2026-03-30 13:35:16','2026-03-30 13:35:16'),(20,2,'Technical Director','2026-03-30 13:35:16','2026-03-30 13:35:16'),(21,11,'Business Development','2026-03-30 13:35:16','2026-03-30 13:35:16'),(22,10,'Cost & Management Accountant','2026-03-30 13:35:16','2026-03-30 13:35:16');
/*!40000 ALTER TABLE `positions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-31 10:18:01
