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
-- Table structure for table `cities`
--

DROP TABLE IF EXISTS `cities`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cities` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `region` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cities`
--

LOCK TABLES `cities` WRITE;
/*!40000 ALTER TABLE `cities` DISABLE KEYS */;
INSERT INTO `cities` VALUES (1,'Harare','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(2,'Bulawayo','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(3,'Mutare','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(4,'Bindura','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(5,'Chinhoyi','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(6,'Zvishavane','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(7,'Rusape','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(8,'Marondera','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(9,'Kadoma','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(10,'Kwekwe','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(11,'Gweru','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(12,'Hwange','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(13,'Victoria Falls','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(14,'Gwanda','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(15,'Beitbridge','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(16,'Masvingo','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(17,'Kariba','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(18,'Karoi','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(19,'Chitungwiza','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(20,'Plumtree','West','2026-03-30 13:49:09','2026-03-30 13:49:09'),(21,'Chipinge','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(22,'Norton','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(23,'Bikita','East','2026-03-30 13:49:09','2026-03-30 13:49:09'),(24,'CHEGUTU','East','2026-03-30 13:49:09','2026-03-30 13:49:09');
/*!40000 ALTER TABLE `cities` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-30 16:27:58
