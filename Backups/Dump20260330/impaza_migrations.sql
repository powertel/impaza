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
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'2014_10_12_000000_create_users_table',1),(2,'2014_10_12_100000_create_password_resets_table',1),(3,'2019_08_19_000000_create_failed_jobs_table',1),(4,'2019_12_14_000001_create_personal_access_tokens_table',1),(5,'2022_06_23_072204_create_permission_tables',1),(6,'2022_06_24_104202_create_customers_table',1),(7,'2022_06_24_105024_create_cities_table',1),(8,'2022_06_24_105159_create_suburbs_table',1),(9,'2022_06_24_105169_create_links_table',1),(10,'2022_06_24_105212_create_pops_table',1),(11,'2022_06_24_105215_create_faults_table',1),(12,'2022_07_02_201203_create_remarks_table',1),(13,'2022_07_03_143034_create_account_managers_table',1),(14,'2022_07_04_081601_create_departments_table',1),(15,'2022_07_06_131410_create_permits_table',1),(16,'2022_07_13_141710_create_statuses_table',1),(17,'2022_07_14_070433_create_sections_table',1),(18,'2022_07_14_070518_create_positions_table',1),(19,'2022_07_18_063644_create_fault_section',1),(20,'2022_07_27_083849_create_section_users_table',1),(21,'2022_07_27_091300_create_fault_section_users_table',1),(22,'2022_07_28_133931_create_link_statuses_table',1),(23,'2022_10_06_080014_create_user_statuses_table',1),(24,'2023_05_29_071421_create_link_types_table',1),(25,'2023_05_29_104834_create_stores_table',1),(26,'2023_05_30_074345_create_materials_table',1),(27,'2023_06_01_133936_create_reasons_for_outages_table',1),(28,'2023_06_21_133956_create_remark_activities_table',1),(29,'2025_10_11_000001_add_remark_activity_id_to_remarks_table',1),(30,'2025_10_11_000002_add_file_path_to_remarks_table',1),(31,'2025_10_14_000001_add_region_to_cities_table',1),(32,'2025_10_14_074118_add_account_number_to_customers_table',1),(33,'2025_10_14_120001_add_account_manager_id_to_customers_table',1),(34,'2025_10_14_120010_add_user_id_to_account_managers_table',1),(35,'2025_10_15_120100_create_fault_stage_logs_table',1),(36,'2025_10_15_120200_create_fault_assignments_table',1),(37,'2025_10_15_120210_add_region_to_users_table',1),(38,'2025_10_15_120220_create_auto_assign_settings_table',1),(39,'2025_10_15_130000_add_standby_flags_to_users_table',1),(40,'2025_10_15_135500_make_file_path_nullable_on_remarks_table',1),(41,'2025_10_16_120000_add_jcc_service_capacity_to_links_table',1),(42,'2025_10_16_131000_make_contact_email_nullable_on_faults_table',1),(43,'2025_10_17_100000_add_auto_assign_enabled_to_auto_assign_settings',1),(44,'2025_10_22_120000_update_customers_table_structure',1),(45,'2025_10_22_120100_update_links_table_add_fields',1),(46,'2025_10_22_130500_reorder_links_table_columns',1),(47,'2025_10_22_140500_change_phonenumber_type_on_users_table',1),(48,'2025_10_23_143849_change_phone_number_to_string_in_faults_table',1),(49,'2025_10_23_144522_change_phonenumber_to_string_in_users_table',1),(50,'2025_10_23_152000_reorder_phone_numbers',1),(51,'2025_10_24_090000_add_account_number_to_links_table',1),(52,'2025_10_24_090100_add_contract_number_to_customers_table',1),(53,'2025_10_24_150000_make_faults_columns_nullable',1),(54,'2025_10_28_075625_make_address_nullable_on_table_faults',1),(55,'2025_11_06_120000_add_is_access_to_users_table',1),(56,'2025_11_09_120000_add_scope_to_auto_assign_settings',1),(57,'2025_11_13_120300_create_fault_referrals_table',1),(58,'2025_11_20_120000_add_assessed_by_to_faults_table',1),(59,'2025_11_21_000001_add_customer_status_and_create_audits_table',1),(60,'2026_01_10_101622_make_confirmed_rfo_id_nullable_in_faults_table',1),(61,'2026_02_06_094908_create_zones_table',1),(62,'2026_02_06_094943_add_zone_id_to_suburbs_table',1),(63,'2026_02_06_094946_create_technician_zone_table',1),(64,'2026_02_06_094956_add_consider_zones_to_auto_assign_settings_table',1),(65,'2026_02_06_124652_add_zone_id_to_pops_table',1),(66,'2026_02_06_124855_remove_zone_id_from_suburbs_and_add_to_pops',1),(67,'2026_03_13_000001_create_notifications_table',1),(68,'2026_03_13_000002_create_user_push_tokens_table',1);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
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
