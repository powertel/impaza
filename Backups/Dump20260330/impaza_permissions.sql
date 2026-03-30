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
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=97 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES (1,'user-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(2,'user-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(3,'user-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(4,'user-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(5,'role-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(6,'role-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(7,'role-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(8,'role-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(9,'fault-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(10,'fault-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(11,'fault-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(12,'fault-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(13,'link-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(14,'link-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(15,'link-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(16,'link-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(17,'customer-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(18,'customer-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(19,'customer-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(20,'customer-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(21,'account-manager-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(22,'account-manager-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(23,'account-manager-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(24,'account-manager-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(25,'department-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(26,'department-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(27,'department-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(28,'department-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(29,'city-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(30,'city-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(31,'city-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(32,'city-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(33,'pop-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(34,'pop-create','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(35,'pop-edit','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(36,'pop-delete','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(37,'location-list','web','2026-03-30 13:30:43','2026-03-30 13:30:43'),(38,'location-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(39,'location-edit','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(40,'location-delete','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(41,'my-fault-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(42,'my-fault-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(43,'my-fault-edit','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(44,'my-fault-delete','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(45,'department-faults-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(46,'department-faults-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(47,'department-faults-edit','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(48,'department-faults-delete','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(49,'assigned-fault-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(50,'assessment-fault-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(51,'assessment-fault-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(52,'assessment-fault-edit','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(53,'assessment-fault-delete','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(54,'noc-clear-faults-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(55,'noc-clear-faults-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(56,'chief-tech-clear-faults-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(57,'chief-tech-clear-faults-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(58,'re-assign-fault','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(59,'remark-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(60,'remark-view','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(61,'clear-fault','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(62,'request-material','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(63,'rectify-fault','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(64,'rectify-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(65,'rectify-create','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(66,'rectify-edit','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(67,'rectify-delete','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(68,'refer-fault','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(69,'request-permit','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(70,'approve-permit','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(71,'fault-assessment','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(72,'permissions','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(73,'finance','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(74,'finance-link-update','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(75,'permit-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(76,'materials','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(77,'material','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(78,'reports','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(79,'resolved-faults-list','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(80,'referred-faults','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(81,'call-centre-reports','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(82,'performance-reports','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(83,'noc-clear-faults-clear','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(84,'noc-clear-faults-delete','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(85,'chief-tech-clear-faults-clear','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(86,'chief-tech-clear-faults-delete','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(87,'technician-configuration','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(88,'assign-fault','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(89,'manage-faults','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(90,'chief-tech-escalate','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(91,'chief-tech-return-to-technician','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(92,'manager-return-to-chief-tech','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(93,'dashboard-open-faults','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(94,'dashboard-fault-age','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(95,'dashboard-resolution-metrics','web','2026-03-30 13:30:44','2026-03-30 13:30:44'),(96,'dashboard-recent-faults','web','2026-03-30 13:30:44','2026-03-30 13:30:44');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-30 16:08:23
