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
-- Table structure for table `reasons_for_outages`
--

DROP TABLE IF EXISTS `reasons_for_outages`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `reasons_for_outages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `RFO` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `reasons_for_outages`
--

LOCK TABLES `reasons_for_outages` WRITE;
/*!40000 ALTER TABLE `reasons_for_outages` DISABLE KEYS */;
INSERT INTO `reasons_for_outages` VALUES (1,'2026-03-30 13:30:45','2026-03-30 13:30:45','Power Outage'),(2,'2026-03-30 13:30:45','2026-03-30 13:30:45','No Fx Light'),(3,'2026-03-30 13:30:45','2026-03-30 13:30:45','UTP Fault'),(4,'2026-03-30 13:30:45','2026-03-30 13:30:45','Slow Speeds'),(5,'2026-03-30 13:30:45','2026-03-30 13:30:45','Civil Works'),(6,'2026-03-30 13:30:45','2026-03-30 13:30:45','Packet Losses'),(7,'2026-03-30 13:30:45','2026-03-30 13:30:45','CPE Faulty'),(8,'2026-03-30 13:30:45','2026-03-30 13:30:45','Timeouts'),(9,'2026-03-30 13:30:45','2026-03-30 13:30:45','Faulty Switch'),(10,'2026-03-30 13:30:45','2026-03-30 13:30:45','Converter Faulty'),(11,'2026-03-30 13:30:45','2026-03-30 13:30:45','RLOS'),(12,'2026-03-30 13:30:45','2026-03-30 13:30:45','Low Power Levels'),(13,'2026-03-30 13:30:45','2026-03-30 13:30:45','Degrades'),(14,'2026-03-30 13:30:45','2026-03-30 13:30:45','Maxing'),(15,'2026-03-30 13:30:45','2026-03-30 13:30:45','Cable Fault'),(16,'2026-03-30 13:30:45','2026-03-30 13:30:45','Maintenance'),(17,'2026-03-30 13:30:45','2026-03-30 13:30:45','Configurations'),(18,'2026-03-30 13:30:45','2026-03-30 13:30:45','Connected Without Internet'),(19,'2026-03-30 13:30:45','2026-03-30 13:30:45','Upstream Fault'),(20,'2026-03-30 13:30:45','2026-03-30 13:30:45','Backbone Fault'),(21,'2026-03-30 13:30:45','2026-03-30 13:30:45','Burnt Cables');
/*!40000 ALTER TABLE `reasons_for_outages` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-31 12:10:56
