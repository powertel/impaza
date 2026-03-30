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
-- Table structure for table `citiess`
--

DROP TABLE IF EXISTS `citiess`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `citiess` (
  `id` int DEFAULT NULL,
  `city` text,
  `region` text,
  `created_at` text,
  `updated_at` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `citiess`
--

LOCK TABLES `citiess` WRITE;
/*!40000 ALTER TABLE `citiess` DISABLE KEYS */;
INSERT INTO `citiess` VALUES (1,'Harare','East','2025-10-30 12:02:20','2025-10-30 12:02:20'),(2,'Bulawayo','West','2025-10-30 12:02:20','2025-10-30 12:02:20'),(3,'Mutare','East','2025-10-30 12:14:44','2025-10-30 12:14:44'),(4,'Bindura','East','2025-10-30 12:14:44','2025-10-30 12:14:44'),(5,'Chinhoyi','East','2025-10-30 12:14:44','2025-10-30 12:14:44'),(6,'Zvishavane','West','2025-10-30 12:14:44','2025-10-30 12:14:44'),(7,'Rusape','East','2025-10-30 12:14:44','2025-10-30 12:14:44'),(8,'Marondera','East','2025-10-30 12:14:44','2025-10-30 12:14:44'),(9,'Kadoma','East','2025-10-30 12:14:44','2025-10-30 12:14:44'),(10,'Kwekwe','East','2025-10-30 12:14:44','2025-10-30 12:14:44'),(11,'Gweru','West','2025-10-30 12:14:44','2025-10-30 12:14:44'),(12,'Hwange','West','2025-10-30 12:14:44','2025-10-30 12:14:44'),(13,'Victoria Falls','West','2025-10-30 12:14:44','2025-10-30 12:14:44'),(14,'Gwanda','West','2025-10-30 12:14:44','2025-10-30 12:14:44'),(15,'Beitbridge','West','2025-10-30 12:14:44','2025-10-30 12:14:44'),(16,'Masvingo','West','2025-10-30 12:18:39','2025-10-30 12:18:39'),(17,'Kariba','East','2025-10-30 13:07:49','2025-10-30 13:07:49'),(18,'Karoi','East','2025-10-30 13:08:04','2025-10-30 13:08:04'),(19,'Chitungwiza','East','2025-11-06 09:12:33','2025-11-06 09:12:33'),(20,'Plumtree','West','2025-11-06 09:12:45','2025-11-06 09:12:45'),(21,'Chipinge','East','2025-11-06 10:02:23','2025-11-06 10:02:23'),(22,'Norton','East','2025-11-07 14:06:36','2025-11-10 11:25:18'),(23,'Bikita','East','2025-11-10 10:58:14','2025-11-10 11:25:03'),(24,'CHEGUTU','East','2025-11-10 14:16:02','2025-11-10 14:16:02');
/*!40000 ALTER TABLE `citiess` ENABLE KEYS */;
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
