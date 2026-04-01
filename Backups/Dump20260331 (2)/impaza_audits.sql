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
-- Table structure for table `audits`
--

DROP TABLE IF EXISTS `audits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `audits` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `entity_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `entity_id` int unsigned NOT NULL,
  `action` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int unsigned DEFAULT NULL,
  `notes` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `audits`
--

LOCK TABLES `audits` WRITE;
/*!40000 ALTER TABLE `audits` DISABLE KEYS */;
INSERT INTO `audits` VALUES (1,'link',1286,'link_decommission',1,'Link decommissioned','2026-03-30 16:37:43'),(2,'link',1286,'link_reconnect',1,'Link reconnected','2026-03-30 16:37:51'),(3,'customer',290,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:38:18'),(4,'link',1289,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:38:18'),(5,'link',1290,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:38:18'),(6,'link',1291,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:38:18'),(7,'link',1292,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:38:18'),(8,'customer',290,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:38:21'),(9,'link',1289,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:38:21'),(10,'link',1290,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:38:21'),(11,'link',1291,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:38:21'),(12,'link',1292,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:38:21'),(13,'customer',22,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:38:45'),(14,'link',1080,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:38:45'),(15,'link',1287,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:38:45'),(16,'customer',22,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:38:47'),(17,'link',1080,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:38:47'),(18,'link',1287,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:38:47'),(19,'customer',270,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:39:10'),(20,'link',1306,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(21,'link',1307,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(22,'link',1308,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(23,'link',1309,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(24,'link',1310,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(25,'link',1311,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(26,'link',1312,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(27,'link',1313,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(28,'link',1314,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(29,'link',1315,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:10'),(30,'customer',270,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:39:12'),(31,'link',1306,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(32,'link',1307,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(33,'link',1308,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(34,'link',1309,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(35,'link',1310,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(36,'link',1311,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(37,'link',1312,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(38,'link',1313,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(39,'link',1314,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(40,'link',1315,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:12'),(41,'customer',310,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:39:33'),(42,'link',1288,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:33'),(43,'customer',310,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:39:35'),(44,'link',1288,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:35'),(45,'customer',299,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:39:47'),(46,'link',1293,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:39:47'),(47,'customer',299,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:39:51'),(48,'link',1293,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:39:51'),(49,'customer',287,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:40:06'),(50,'link',1294,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:40:06'),(51,'customer',287,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:40:08'),(52,'link',1294,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:40:08'),(53,'customer',276,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:40:31'),(54,'link',1295,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:40:31'),(55,'customer',276,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:40:33'),(56,'link',1295,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:40:33'),(57,'customer',282,'customer_disconnect',1,'Customer disconnected','2026-03-30 16:41:00'),(58,'link',1296,'link_disconnect',1,'Link disconnected due to customer disconnect','2026-03-30 16:41:00'),(59,'customer',282,'customer_reconnect',1,'Customer reconnected','2026-03-30 16:41:03'),(60,'link',1296,'link_reconnect',1,'Link reconnected due to customer reconnect','2026-03-30 16:41:03');
/*!40000 ALTER TABLE `audits` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-03-31 12:11:00
