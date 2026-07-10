/*M!999999\- enable the sandbox mode */ 
-- MariaDB dump 10.19  Distrib 10.11.14-MariaDB, for debian-linux-gnu (x86_64)
--
-- Host: 127.0.0.1    Database: laravel
-- ------------------------------------------------------
-- Server version	10.11.14-MariaDB-0ubuntu0.24.04.1

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `announcements` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL COMMENT 'Identifiant unique de Moodle',
  `course_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `subject` varchar(255) NOT NULL,
  `message` longtext NOT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1=visible, 0=hidden',
  `published_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `announcements_moodle_id_unique` (`moodle_id`),
  KEY `announcements_user_id_foreign` (`user_id`),
  KEY `announcements_course_id_status_index` (`course_id`,`status`),
  KEY `announcements_moodle_id_index` (`moodle_id`),
  KEY `announcements_sync_status_index` (`sync_status`),
  KEY `announcements_dirty_index` (`dirty`),
  CONSTRAINT `announcements_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `announcements_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `announcements`
--

LOCK TABLES `announcements` WRITE;
/*!40000 ALTER TABLE `announcements` DISABLE KEYS */;
INSERT INTO `announcements` VALUES
(15,NULL,133,61,'evaluation','demain matin',1,NULL,'pending','create',NULL,1,'2026-05-29 01:28:41','2026-05-29 01:28:41'),
(16,NULL,133,61,'evaluation','demain matin',1,NULL,'pending','create',NULL,1,'2026-05-29 01:33:44','2026-05-29 01:33:44'),
(17,NULL,135,61,'hello','test',1,'2026-05-29 01:36:42','pending','create',NULL,1,'2026-05-29 01:36:42','2026-05-29 01:36:42'),
(18,NULL,139,61,'evaluation suprise','preparer vous pour une evaluation surprise',1,'2026-05-29 16:14:49','pending','create',NULL,1,'2026-05-29 16:14:49','2026-05-29 16:14:49'),
(19,NULL,141,61,'Reunion avec l\'equipe de projet','vous avez rendez vous ce soir , soyez presents.',1,'2026-05-29 20:01:47','pending','create',NULL,1,'2026-05-29 20:01:47','2026-05-29 20:01:47'),
(20,NULL,150,61,'prise de contact','rendez vous dans le hall',1,'2026-05-30 15:56:43','pending','create',NULL,1,'2026-05-30 15:56:43','2026-05-30 15:56:43'),
(21,NULL,157,58,'reunion urgente 1','message 1',1,'2026-07-02 11:41:11','pending','create',NULL,1,'2026-07-02 11:41:11','2026-07-02 11:41:11'),
(22,NULL,157,58,'reunion urgente 2','message 2',1,'2026-07-02 11:41:31','pending','create',NULL,1,'2026-07-02 11:41:31','2026-07-02 11:41:31');
/*!40000 ALTER TABLE `announcements` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `assignments`
--

DROP TABLE IF EXISTS `assignments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `assignments` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `duedate` timestamp NOT NULL,
  `attemptnumber` int(11) NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT 0,
  `module_id` bigint(20) unsigned NOT NULL,
  `created_by` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assignments_module_id_foreign` (`module_id`),
  KEY `assignments_created_by_foreign` (`created_by`),
  CONSTRAINT `assignments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `assignments_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `assignments`
--

LOCK TABLES `assignments` WRITE;
/*!40000 ALTER TABLE `assignments` DISABLE KEYS */;
/*!40000 ALTER TABLE `assignments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
INSERT INTO `cache` VALUES
('spatie.permission.cache','a:3:{s:5:\"alias\";a:4:{s:1:\"a\";s:2:\"id\";s:1:\"b\";s:4:\"name\";s:1:\"c\";s:10:\"guard_name\";s:1:\"r\";s:5:\"roles\";}s:11:\"permissions\";a:31:{i:0;a:4:{s:1:\"a\";i:1;s:1:\"b\";s:18:\"view_announcements\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:1;a:4:{s:1:\"a\";i:2;s:1:\"b\";s:19:\"create_announcement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:2;a:4:{s:1:\"a\";i:3;s:1:\"b\";s:17:\"edit_announcement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:3;a:4:{s:1:\"a\";i:4;s:1:\"b\";s:19:\"delete_announcement\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:4;a:4:{s:1:\"a\";i:5;s:1:\"b\";s:14:\"view_documents\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:5;a:4:{s:1:\"a\";i:6;s:1:\"b\";s:15:\"upload_document\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:6;a:4:{s:1:\"a\";i:7;s:1:\"b\";s:13:\"edit_document\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:7;a:4:{s:1:\"a\";i:8;s:1:\"b\";s:15:\"delete_document\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:8;a:4:{s:1:\"a\";i:9;s:1:\"b\";s:17:\"view_participants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:9;a:4:{s:1:\"a\";i:10;s:1:\"b\";s:19:\"manage_participants\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:10;a:4:{s:1:\"a\";i:11;s:1:\"b\";s:10:\"enrol_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:11;a:4:{s:1:\"a\";i:12;s:1:\"b\";s:12:\"unenrol_user\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:12;a:4:{s:1:\"a\";i:13;s:1:\"b\";s:11:\"view_grades\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:13;a:4:{s:1:\"a\";i:14;s:1:\"b\";s:15:\"view_own_grades\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:14;a:4:{s:1:\"a\";i:15;s:1:\"b\";s:12:\"create_grade\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:15;a:4:{s:1:\"a\";i:16;s:1:\"b\";s:10:\"edit_grade\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:16;a:4:{s:1:\"a\";i:17;s:1:\"b\";s:12:\"delete_grade\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:17;a:4:{s:1:\"a\";i:18;s:1:\"b\";s:17:\"view_competencies\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:18;a:4:{s:1:\"a\";i:19;s:1:\"b\";s:19:\"manage_competencies\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:19;a:4:{s:1:\"a\";i:20;s:1:\"b\";s:21:\"view_own_competencies\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}i:20;a:4:{s:1:\"a\";i:21;s:1:\"b\";s:24:\"mark_competency_complete\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:21;a:3:{s:1:\"a\";i:22;s:1:\"b\";s:11:\"manage_site\";s:1:\"c\";s:3:\"web\";}i:22;a:3:{s:1:\"a\";i:23;s:1:\"b\";s:12:\"manage_users\";s:1:\"c\";s:3:\"web\";}i:23;a:3:{s:1:\"a\";i:24;s:1:\"b\";s:11:\"manage_sync\";s:1:\"c\";s:3:\"web\";}i:24;a:4:{s:1:\"a\";i:25;s:1:\"b\";s:14:\"manage_courses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:25;a:4:{s:1:\"a\";i:26;s:1:\"b\";s:12:\"view_courses\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:3:{i:0;i:1;i:1;i:2;i:2;i:3;}}i:26;a:4:{s:1:\"a\";i:27;s:1:\"b\";s:15:\"manage_sections\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:27;a:4:{s:1:\"a\";i:28;s:1:\"b\";s:17:\"create_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:28;a:4:{s:1:\"a\";i:29;s:1:\"b\";s:15:\"edit_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:29;a:4:{s:1:\"a\";i:30;s:1:\"b\";s:17:\"delete_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:3;}}i:30;a:4:{s:1:\"a\";i:31;s:1:\"b\";s:17:\"submit_assignment\";s:1:\"c\";s:3:\"web\";s:1:\"r\";a:1:{i:0;i:2;}}}s:5:\"roles\";a:3:{i:0;a:3:{s:1:\"a\";i:1;s:1:\"b\";s:9:\"ROLE_USER\";s:1:\"c\";s:3:\"web\";}i:1;a:3:{s:1:\"a\";i:2;s:1:\"b\";s:12:\"ROLE_STUDENT\";s:1:\"c\";s:3:\"web\";}i:2;a:3:{s:1:\"a\";i:3;s:1:\"b\";s:12:\"ROLE_TEACHER\";s:1:\"c\";s:3:\"web\";}}}',1783164753);
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
INSERT INTO `cache_locks` VALUES
('auto_sync_lock','M1YZXDPSRA49YzSk',1777464356);
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL,
  `parent_id` bigint(20) unsigned DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `idnumber` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `descriptionformat` tinyint(4) NOT NULL DEFAULT 1,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `categories_moodle_id_unique` (`moodle_id`),
  KEY `categories_sync_status_index` (`sync_status`),
  KEY `categories_dirty_index` (`dirty`),
  KEY `categories_parent_id_foreign` (`parent_id`),
  CONSTRAINT `categories_parent_id_foreign` FOREIGN KEY (`parent_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES
(17,5,NULL,'Catégorie 1',NULL,'',0,'synced',NULL,'2026-07-07 08:18:26',0,'2026-05-27 22:44:11','2026-07-07 08:18:26'),
(18,6,NULL,'Mathematiques','','<p>Unite d\'enseigment de mathematiques</p>',1,'synced',NULL,'2026-07-07 08:18:26',0,'2026-05-27 22:44:11','2026-07-07 08:18:26'),
(19,7,NULL,'Chimie Organique','','',1,'synced','create','2026-07-07 08:18:26',0,'2026-05-28 06:33:28','2026-07-07 08:18:26'),
(20,8,NULL,'Informatique','INF-3003','decouvrez les bases de la programmation',1,'synced','create','2026-07-07 08:18:26',0,'2026-05-29 01:06:18','2026-07-07 08:18:26'),
(21,NULL,NULL,'Science de donnees','INF-2005','manipuler les donnees efficacement',1,'pending','delete',NULL,1,'2026-05-29 06:19:32','2026-05-30 11:03:26'),
(22,9,NULL,'Reseaux Informatique','INF-20041','connexion des equipements',1,'synced','create','2026-07-07 08:18:26',0,'2026-05-29 06:22:05','2026-07-07 08:18:26'),
(23,10,NULL,'Humanite Numerique','HUM-1234','description',1,'synced','delete','2026-07-07 08:18:26',0,'2026-05-30 10:11:25','2026-07-07 08:18:26'),
(24,11,NULL,'soft skill','HUM-0009','travailler vos soft skill',1,'synced','create','2026-07-07 08:18:26',0,'2026-05-30 11:04:42','2026-07-07 08:18:26'),
(25,12,NULL,'Philosophie Humaine','Philo','comprennez la pensee humaine',1,'synced','create','2026-07-07 08:18:26',0,'2026-05-30 11:25:13','2026-07-07 08:18:26'),
(26,NULL,20,'Genie des logiciel','si-inf-4044','description',1,'pending','create',NULL,1,'2026-07-02 04:06:36','2026-07-02 04:06:36'),
(27,NULL,20,'gestion','si-ing','description',1,'pending','create',NULL,1,'2026-07-02 08:02:51','2026-07-02 08:02:51'),
(28,NULL,NULL,'aaa','aa','a',1,'synced','delete','2026-07-03 12:23:35',0,'2026-07-02 14:46:32','2026-07-03 12:23:35'),
(29,NULL,NULL,'droit','d','d',1,'pending','create',NULL,1,'2026-07-02 14:50:44','2026-07-02 14:50:44'),
(30,NULL,NULL,'categorie test synchro',NULL,NULL,1,'pending','create',NULL,1,'2026-07-03 06:44:54','2026-07-03 06:44:54'),
(31,NULL,NULL,'test synchro 2',NULL,NULL,1,'pending','create',NULL,1,'2026-07-03 06:48:26','2026-07-03 06:48:26'),
(32,13,NULL,'test synchro 3','','',1,'synced','create','2026-07-07 08:18:26',0,'2026-07-03 06:50:52','2026-07-07 08:18:26'),
(33,14,NULL,'test synchro 4','','description',1,'synced','create','2026-07-07 08:18:26',0,'2026-07-03 11:43:30','2026-07-07 08:18:26'),
(34,15,NULL,'test synchro 5','','',1,'synced','create','2026-07-07 08:18:26',0,'2026-07-03 11:46:28','2026-07-07 08:18:26'),
(35,16,NULL,'info','','',1,'synced','create','2026-07-07 08:18:26',0,'2026-07-03 12:03:27','2026-07-07 08:18:26'),
(36,17,NULL,'categorie test 5','','',1,'synced','create','2026-07-07 08:18:26',0,'2026-07-03 12:24:55','2026-07-07 08:18:26');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `competencies`
--

DROP TABLE IF EXISTS `competencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `competencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL COMMENT 'Identifiant unique de Moodle',
  `shortname` varchar(255) NOT NULL,
  `idnumber` varchar(255) DEFAULT NULL COMMENT 'Numéro d''identité personnalisé',
  `description` varchar(255) DEFAULT NULL,
  `description_long` text DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1=active, 0=archived',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `competencies_shortname_unique` (`shortname`),
  UNIQUE KEY `competencies_moodle_id_unique` (`moodle_id`),
  KEY `competencies_moodle_id_index` (`moodle_id`),
  KEY `competencies_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `competencies`
--

LOCK TABLES `competencies` WRITE;
/*!40000 ALTER TABLE `competencies` DISABLE KEYS */;
INSERT INTO `competencies` VALUES
(12,NULL,'a','a','a',NULL,1,'2026-05-29 03:33:37','2026-05-29 03:33:37');
/*!40000 ALTER TABLE `competencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_competencies`
--

DROP TABLE IF EXISTS `course_competencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_competencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL COMMENT 'Identifiant unique de Moodle',
  `course_id` bigint(20) unsigned NOT NULL,
  `competency_id` bigint(20) unsigned NOT NULL,
  `sort_order` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_competencies_course_id_competency_id_unique` (`course_id`,`competency_id`),
  UNIQUE KEY `course_competencies_moodle_id_unique` (`moodle_id`),
  KEY `course_competencies_competency_id_foreign` (`competency_id`),
  KEY `course_competencies_moodle_id_index` (`moodle_id`),
  CONSTRAINT `course_competencies_competency_id_foreign` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_competencies_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_competencies`
--

LOCK TABLES `course_competencies` WRITE;
/*!40000 ALTER TABLE `course_competencies` DISABLE KEYS */;
INSERT INTO `course_competencies` VALUES
(16,NULL,133,12,NULL,'2026-05-29 03:33:37','2026-05-29 03:33:37'),
(17,NULL,150,12,NULL,'2026-05-30 15:57:39','2026-05-30 15:57:39'),
(18,NULL,157,12,NULL,'2026-07-02 13:21:21','2026-07-02 13:21:21');
/*!40000 ALTER TABLE `course_competencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_join_requests`
--

DROP TABLE IF EXISTS `course_join_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_join_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `student_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_join_requests_course_id_student_id_unique` (`course_id`,`student_id`),
  KEY `course_join_requests_student_id_foreign` (`student_id`),
  CONSTRAINT `course_join_requests_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_join_requests_student_id_foreign` FOREIGN KEY (`student_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_join_requests`
--

LOCK TABLES `course_join_requests` WRITE;
/*!40000 ALTER TABLE `course_join_requests` DISABLE KEYS */;
INSERT INTO `course_join_requests` VALUES
(1,132,63,'accepted','j\'aimerais s\'il vous plait rejoindre ce cours','2026-05-29 07:16:43','2026-05-29 07:17:44');
/*!40000 ALTER TABLE `course_join_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_user`
--

DROP TABLE IF EXISTS `course_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_user` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `course_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `course_user_user_id_course_id_unique` (`user_id`,`course_id`),
  KEY `course_user_course_id_foreign` (`course_id`),
  CONSTRAINT `course_user_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `course_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_user`
--

LOCK TABLES `course_user` WRITE;
/*!40000 ALTER TABLE `course_user` DISABLE KEYS */;
INSERT INTO `course_user` VALUES
(25,59,135,'2026-05-28 07:25:01','2026-05-28 07:25:01'),
(26,59,133,'2026-05-29 01:11:12','2026-05-29 01:11:12'),
(27,60,133,'2026-05-29 01:14:33','2026-05-29 01:14:33'),
(28,59,137,'2026-05-29 06:31:21','2026-05-29 06:31:21'),
(29,64,137,'2026-05-29 06:31:53','2026-05-29 06:31:53'),
(30,62,137,'2026-05-29 06:32:00','2026-05-29 06:32:00'),
(31,63,132,'2026-05-29 07:17:44','2026-05-29 07:17:44'),
(32,63,139,'2026-05-29 16:14:23','2026-05-29 16:14:23'),
(33,63,140,'2026-05-29 19:58:13','2026-05-29 19:58:13'),
(34,62,140,'2026-05-29 19:58:21','2026-05-29 19:58:21'),
(35,63,141,'2026-05-29 20:01:04','2026-05-29 20:01:04'),
(36,62,141,'2026-05-29 20:01:10','2026-05-29 20:01:10'),
(37,59,150,'2026-05-30 15:56:19','2026-05-30 15:56:19'),
(38,59,155,'2026-07-02 08:31:23','2026-07-02 08:31:23'),
(39,59,157,'2026-07-02 11:39:36','2026-07-02 11:39:36'),
(40,60,157,'2026-07-02 11:39:45','2026-07-02 11:39:45'),
(41,59,138,'2026-07-03 10:32:38','2026-07-03 10:32:38'),
(42,69,138,'2026-07-03 12:12:59','2026-07-03 12:12:59'),
(43,69,166,'2026-07-03 13:11:50','2026-07-03 13:11:50');
/*!40000 ALTER TABLE `course_user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_user_requests`
--

DROP TABLE IF EXISTS `course_user_requests`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `course_user_requests` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `course_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `course_user_requests_course_id_index` (`course_id`),
  KEY `course_user_requests_user_id_index` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_user_requests`
--

LOCK TABLES `course_user_requests` WRITE;
/*!40000 ALTER TABLE `course_user_requests` DISABLE KEYS */;
INSERT INTO `course_user_requests` VALUES
(1,146,63,'pending',NULL,'2026-05-30 15:40:47','2026-05-30 15:40:47');
/*!40000 ALTER TABLE `course_user_requests` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `courses`
--

DROP TABLE IF EXISTS `courses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `courses` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL,
  `fullname` varchar(255) NOT NULL,
  `shortname` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `numsections` int(11) NOT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `startdate` timestamp NULL DEFAULT NULL,
  `enddate` timestamp NULL DEFAULT NULL,
  `teacher_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `idnumber` varchar(255) DEFAULT NULL,
  `format` varchar(255) NOT NULL DEFAULT 'topics',
  `hiddensections` tinyint(4) NOT NULL DEFAULT 0,
  `coursedisplay` tinyint(4) NOT NULL DEFAULT 0,
  `lang` varchar(255) DEFAULT NULL,
  `newsitems` int(11) NOT NULL DEFAULT 5,
  `showgrades` tinyint(1) NOT NULL DEFAULT 1,
  `showreports` tinyint(1) NOT NULL DEFAULT 0,
  `showactivitydates` tinyint(1) NOT NULL DEFAULT 1,
  `maxbytes` bigint(20) NOT NULL DEFAULT 0,
  `enablecompletion` tinyint(1) NOT NULL DEFAULT 1,
  `showcompletionconditions` tinyint(1) NOT NULL DEFAULT 1,
  `groupmode` tinyint(4) NOT NULL DEFAULT 0,
  `groupmodeforce` tinyint(1) NOT NULL DEFAULT 0,
  `defaultgroupingid` int(11) NOT NULL DEFAULT 0,
  `tags` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `courses_moodle_id_unique` (`moodle_id`),
  KEY `courses_teacher_id_foreign` (`teacher_id`),
  KEY `courses_category_id_foreign` (`category_id`),
  KEY `courses_sync_status_index` (`sync_status`),
  KEY `courses_dirty_index` (`dirty`),
  CONSTRAINT `courses_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `courses_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=167 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `courses`
--

LOCK TABLES `courses` WRITE;
/*!40000 ALTER TABLE `courses` DISABLE KEYS */;
INSERT INTO `courses` VALUES
(132,17,'Analyse relle 1','ANA1','',4,'synced','update','2026-07-07 08:18:26',0,'2026-05-26 23:00:00','2026-06-05 23:00:00',NULL,'2026-05-27 22:45:15','2026-07-07 08:18:26',18,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,0,1,0,0,0,NULL),
(133,18,'Probabilite avancee','Proba-A','etude probabiliste et theorie du hasard',4,'synced','create','2026-07-07 08:18:27',0,'2026-05-27 23:00:00','2026-06-04 23:00:00',NULL,'2026-05-28 04:42:01','2026-07-07 08:18:27',18,NULL,1,'MATH-3002','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(134,19,'Analyse Atomique d\'un systeme Organique','AASO','comprendre le fonctionnement de l\'atome dans la chimie organique',4,'synced','create','2026-07-07 08:18:28',0,'2026-05-27 23:00:00','2026-06-05 23:00:00',NULL,'2026-05-28 06:35:38','2026-07-07 08:18:28',19,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(135,20,'Algebre lineaire 1','ALIN1','algebre lineaire',4,'synced','create','2026-07-07 08:18:28',0,'2026-05-27 23:00:00','2026-05-31 23:00:00',NULL,'2026-05-28 06:39:36','2026-07-07 08:18:28',18,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(136,21,'Algorithmique','ALgo','',4,'synced','update','2026-07-07 08:18:29',0,'2026-05-28 23:00:00','2026-06-04 23:00:00',61,'2026-05-29 01:27:03','2026-07-07 08:18:29',20,'courses/images/6TpP4HkxGttw1DyDs3mnujl8BSLgwLvVXqmkZ9Dp.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(137,22,'Administration reseaux','Admin-res','description1',4,'synced','create','2026-07-07 08:18:30',0,'2026-05-28 23:00:00','2026-06-05 23:00:00',61,'2026-05-29 06:24:25','2026-07-07 08:18:30',22,NULL,1,'INF-1001','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(138,23,'reseaux mobiles','res-mobi','<p>description 1</p>',4,'synced','update','2026-07-07 08:18:30',0,'2026-05-29 22:00:00','2027-09-29 22:00:00',64,'2026-05-29 06:26:13','2026-07-07 08:18:30',22,'courses/images/1jgXndxDSjoppjsvOhHFSKiJYOMRDroTyfQgD9sX.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(139,24,'Creation digitale','crea-dig','creer des contenu de presentation pertinent',4,'synced','create','2026-07-07 08:18:32',0,'2026-05-28 23:00:00','2026-07-30 23:00:00',61,'2026-05-29 16:13:52','2026-07-07 08:18:32',20,NULL,1,'INF-1234','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(140,25,'Analyse des espaces vectoriel normes','Ana-vec','ceci est un cours visant a donner les bases pour maitriser les espaces vectoriels.',4,'synced','create','2026-07-07 08:18:32',0,'2026-05-28 23:00:00','2026-07-30 23:00:00',61,'2026-05-29 19:57:47','2026-07-07 08:18:32',18,NULL,1,'MAT-2341','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(141,26,'Geometrie Affine','Geo-Aff','comprendre le fonctionnement des espaces affine',4,'synced','create','2026-07-07 08:18:33',0,'2026-05-28 23:00:00','2026-07-02 23:00:00',61,'2026-05-29 20:00:35','2026-07-07 08:18:33',18,NULL,1,'MAT-3478','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(142,27,'ab','kj','',4,'synced','create','2026-07-07 08:18:34',0,'2026-05-29 23:00:00','2026-07-30 23:00:00',61,'2026-05-30 09:05:03','2026-07-07 08:18:34',18,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(143,28,'kj bh','jbhn','',4,'synced','create','2026-07-07 08:18:35',0,'2026-05-29 23:00:00','2026-07-23 23:00:00',61,'2026-05-30 09:05:30','2026-07-07 08:18:35',17,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(144,29,'ads','adw','',4,'synced','create','2026-07-07 08:18:35',0,'2026-05-29 23:00:00','2026-08-13 23:00:00',61,'2026-05-30 09:08:29','2026-07-07 08:18:35',17,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(145,30,'asdwe','qwe','',4,'synced','create','2026-07-07 08:18:36',0,'2026-05-29 23:00:00','2026-07-16 23:00:00',61,'2026-05-30 09:19:20','2026-07-07 08:18:36',17,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(146,NULL,'asdwe','qwe',NULL,4,'pending','create',NULL,1,'2026-05-29 23:00:00','2026-07-16 23:00:00',61,'2026-05-30 09:22:30','2026-05-30 09:22:30',17,'courses/images/i2IwqXYwwOJCJF7y9IWhcYXYRp0GIxSicEiFI6wg.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(147,31,'ppoiiu','ybvjj','',4,'synced','create','2026-07-07 08:18:37',0,'2026-05-29 23:00:00','2026-07-01 23:00:00',61,'2026-05-30 10:10:37','2026-07-07 08:18:37',17,'courses/images/Dea4vO1tlCE3wCT2DvJaMNRSyLgPtyzCf5SCDlJw.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(148,32,'linsdfkjks','jdjdjd','',4,'synced','create','2026-07-07 08:18:37',0,'2026-05-29 23:00:00','2026-07-28 23:00:00',61,'2026-05-30 14:18:14','2026-07-07 08:18:37',17,'courses/images/H3uX2dOjnVRvlfRrCx8u6jnRPXi4W9Ggtq9Z1zDa.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(149,33,'essai philosophique','ep1','description du cours',5,'synced','create','2026-07-07 08:18:38',0,'2026-05-29 23:00:00','2026-07-30 23:00:00',61,'2026-05-30 15:54:42','2026-07-07 08:18:38',25,'courses/images/SQRyr7zxR9VIRvcWqB5eOtGC8rGIAIpGmmTE3Rpl.png',1,'PHI-2387','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(150,34,'proposition de these','pt','description du cours',4,'synced','create','2026-07-07 08:18:39',0,'2026-05-29 23:00:00','2026-07-30 23:00:00',61,'2026-05-30 15:55:54','2026-07-07 08:18:39',24,'courses/images/8853wR2fcMt3dfjl6u6PXbbtgYeFM9Fc9zDMNfQw.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(151,35,'cours 1','c1','description',4,'synced','create','2026-07-07 08:18:39',0,'2026-05-29 23:00:00','2026-07-29 23:00:00',61,'2026-05-30 16:11:31','2026-07-07 08:18:39',24,'courses/images/ai6u19w1nRuPxdbVYKPSjOjz0I22qCLXDTbuwngy.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(152,36,'t','t','t',4,'synced','create','2026-07-07 08:18:40',0,'2026-05-29 23:00:00','2026-07-02 23:00:00',61,'2026-05-30 16:13:49','2026-07-07 08:18:40',17,'courses/images/SXruh8PX1WjGx8AluESdIf6OAIQEKEu5j6Qla33T.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(153,37,'jfnjf','ifogog','deecrio',4,'synced','create','2026-07-07 08:18:41',0,'2026-05-29 23:00:00','2026-07-30 23:00:00',61,'2026-05-30 17:15:07','2026-07-07 08:18:41',22,'courses/images/XsRHx5wDRBhiqcgeRQTCEIZt4nKGO73SGxxkeSuF.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(154,NULL,'smaa','smaa',NULL,4,'pending','create',NULL,1,'2026-06-30 23:00:00',NULL,61,'2026-07-01 22:05:03','2026-07-01 22:05:03',20,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(155,NULL,'creation d\'entreprise','crea-start','description du cours',4,'pending','create',NULL,1,'2026-07-01 23:00:00','2026-07-31 23:00:00',61,'2026-07-02 08:04:16','2026-07-02 08:04:16',20,'courses/images/8drXkjU5LIjsmS9XyfcQaIG8yXg901cTWCGx5H65.png',1,'si-inf-4044s','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(156,NULL,'cours 2','c2',NULL,4,'pending','create',NULL,1,'2026-07-01 23:00:00','2026-07-22 23:00:00',61,'2026-07-02 09:04:31','2026-07-02 09:04:31',17,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(157,NULL,'Ingenierie dirige par les modeles','idm','description',4,'pending','create',NULL,1,'2026-07-01 23:00:00','2026-07-23 23:00:00',58,'2026-07-02 11:37:02','2026-07-02 11:37:02',20,'courses/images/FM18zjXctPXLr379pLDbf8CvvPZF9hczWX6cUCiE.png',0,'idm','weeks',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(158,NULL,'tech co','tc','description',4,'pending','create',NULL,1,'2026-07-01 23:00:00','2026-07-15 23:00:00',58,'2026-07-02 14:36:48','2026-07-02 14:36:48',26,'courses/images/zpGoyGHHMerHoTfPh8iW22vds1iTsEUupnLo3Kii.png',1,'adda','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(159,NULL,'asp juri','aj','description 1',4,'pending','create',NULL,1,'2026-07-01 23:00:00','2026-07-21 23:00:00',58,'2026-07-02 14:39:25','2026-07-02 14:39:25',17,'courses/images/RwByyp9kEnub1XNHE1EVVhexXcFFHrdMGRNKXhn6.png',1,'aj','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(160,38,'cours test 1','ct11','description',4,'synced','create','2026-07-07 08:18:41',0,'2026-07-02 23:00:00','2026-07-21 23:00:00',67,'2026-07-03 06:52:45','2026-07-07 08:18:41',32,'courses/images/SXF2ZoloNNZSozWZeDQSBPAtNZQqKcHWDbpDwNja.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(161,39,'cours test 3','ct3','',4,'synced','create','2026-07-07 08:18:42',0,'2026-07-02 23:00:00','2026-07-28 23:00:00',67,'2026-07-03 07:30:24','2026-07-07 08:18:42',32,'courses/images/ghD4Q5wjwLUtTc85tCeEQSm9RD8azEhTZoFw8Otc.png',1,'ct3','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(162,40,'cours test 4','ct4','<p>description</p>',4,'synced',NULL,'2026-07-07 08:18:43',0,'2026-07-03 22:00:00','2027-07-03 22:00:00',NULL,'2026-07-03 10:25:18','2026-07-07 08:18:43',NULL,NULL,1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(163,41,'cours test 5','ct5','',4,'synced','create','2026-07-07 08:18:44',0,'2026-07-02 23:00:00','2026-07-20 23:00:00',68,'2026-07-03 11:47:35','2026-07-07 08:18:44',34,'courses/images/FavhwnLGVJXai7ZvIyOZ5hyhcnGFc2KZJCCLYVXQ.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(164,42,'c','c',NULL,4,'synced','create','2026-07-03 12:23:35',0,'2026-07-02 23:00:00',NULL,64,'2026-07-03 12:04:21','2026-07-03 12:23:35',17,'courses/images/ZRCCxD1t8wq9lUfEEfjwQ6wgEm6ByczPYyqhPp4I.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(165,43,'cours de test 6','ct6','',4,'synced','create','2026-07-07 08:18:44',0,'2026-07-02 23:00:00','2026-07-20 23:00:00',68,'2026-07-03 12:29:54','2026-07-07 08:18:44',36,'courses/images/QJQQnawWYAPQfQDV0qXVpxduhOm5oCdAbENfCFxR.png',1,NULL,'topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL),
(166,44,'mathemartique','math','decription du cours',4,'synced','create','2026-07-07 08:18:46',0,'2026-07-02 23:00:00','2026-07-28 23:00:00',64,'2026-07-03 13:11:30','2026-07-07 08:18:46',18,'courses/images/WFbpWbRH9R5DhxdHuIFue8qwcwLYVCLNckKh2SE2.png',1,'mat-001','topics',0,0,NULL,5,1,0,1,0,1,1,0,0,0,NULL);
/*!40000 ALTER TABLE `courses` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documents`
--

DROP TABLE IF EXISTS `documents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `documents` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL COMMENT 'Identifiant unique de Moodle (file id)',
  `course_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `filename` varchar(255) NOT NULL,
  `filepath` varchar(255) NOT NULL COMMENT 'Chemin virtuel dans Moodle',
  `mimetype` varchar(255) DEFAULT NULL,
  `filesize` bigint(20) NOT NULL DEFAULT 0,
  `file_url` varchar(255) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1=visible, 0=hidden',
  `file_date` timestamp NULL DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `documents_moodle_id_unique` (`moodle_id`),
  KEY `documents_user_id_foreign` (`user_id`),
  KEY `documents_course_id_status_index` (`course_id`,`status`),
  KEY `documents_moodle_id_index` (`moodle_id`),
  KEY `documents_sync_status_index` (`sync_status`),
  KEY `documents_dirty_index` (`dirty`),
  CONSTRAINT `documents_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `documents_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documents`
--

LOCK TABLES `documents` WRITE;
/*!40000 ALTER TABLE `documents` DISABLE KEYS */;
INSERT INTO `documents` VALUES
(28,NULL,133,61,'document11','courses/documents/1780027464_Screenshot from 2026-01-21 14-58-26.png','image/png',105232,'http://127.0.0.1:8000/storage/courses/documents/1780027464_Screenshot from 2026-01-21 14-58-26.png',1,NULL,'synced',NULL,'2026-05-29 03:04:54',0,'2026-05-29 03:04:24','2026-05-29 03:04:54'),
(29,NULL,133,61,'d2','courses/documents/1780027971_Screenshot from 2026-02-02 10-46-54.png','image/png',194308,'http://127.0.0.1:8000/storage/courses/documents/1780027971_Screenshot from 2026-02-02 10-46-54.png',1,NULL,'pending',NULL,NULL,1,'2026-05-29 03:12:51','2026-05-29 03:12:51'),
(30,NULL,134,61,'a','courses/documents/1780028386_exercice3_SMA.pdf','application/pdf',154478,'http://127.0.0.1:8000/storage/courses/documents/1780028386_exercice3_SMA.pdf',1,NULL,'pending',NULL,NULL,1,'2026-05-29 03:19:46','2026-05-29 03:19:46'),
(31,NULL,135,61,'as','courses/documents/1780028452_exercice3_SMA.pdf','application/pdf',154478,'http://127.0.0.1:8000/storage/courses/documents/1780028452_exercice3_SMA.pdf',1,NULL,'pending',NULL,NULL,1,'2026-05-29 03:20:52','2026-05-29 03:20:52'),
(32,NULL,139,61,'td 1','courses/documents/1780074911_exercice3_SMA.pdf','application/pdf',154478,'http://127.0.0.1:8000/storage/courses/documents/1780074911_exercice3_SMA.pdf',1,NULL,'pending',NULL,NULL,1,'2026-05-29 16:15:11','2026-05-29 16:15:11'),
(33,NULL,136,61,'intro','courses/documents/1780144057_SmartFood.pdf','application/pdf',303120,'http://127.0.0.1:8000/storage/courses/documents/1780144057_SmartFood.pdf',1,NULL,'pending',NULL,NULL,1,'2026-05-30 11:27:37','2026-05-30 11:27:37'),
(35,NULL,155,61,'m','courses/documents/1782984703_API_DOCUMENTATION.md','text/plain',50336,'http://127.0.0.1:8000/storage/courses/documents/1782984703_API_DOCUMENTATION.md',1,NULL,'pending',NULL,NULL,1,'2026-07-02 08:31:43','2026-07-02 08:31:43'),
(36,NULL,155,61,'desi','courses/documents/1782987700_sbom_minimum_elements_report_0.pdf','application/pdf',634140,'http://127.0.0.1:8000/storage/courses/documents/1782987700_sbom_minimum_elements_report_0.pdf',1,NULL,'pending',NULL,NULL,1,'2026-07-02 09:21:40','2026-07-02 09:21:40'),
(37,NULL,160,67,'document test 1','courses/documents/1783065299_API_DOCUMENTATION.md','text/plain',50336,'http://127.0.0.1:8000/storage/courses/documents/1783065299_API_DOCUMENTATION.md',1,NULL,'pending',NULL,NULL,1,'2026-07-03 06:54:59','2026-07-03 06:54:59'),
(38,NULL,160,67,'document test 2','courses/documents/1783065413_sbom_minimum_elements_report_0.pdf','application/pdf',634140,'http://127.0.0.1:8000/storage/courses/documents/1783065413_sbom_minimum_elements_report_0.pdf',1,NULL,'pending',NULL,NULL,1,'2026-07-03 06:56:53','2026-07-03 06:56:53'),
(39,NULL,161,67,'document test 3','courses/documents/1783067443_CARI_2026_paper_29 (1).pdf','application/pdf',376531,'http://127.0.0.1:8000/storage/courses/documents/1783067443_CARI_2026_paper_29 (1).pdf',1,NULL,'pending','create',NULL,1,'2026-07-03 07:30:43','2026-07-03 07:30:43'),
(40,NULL,166,64,'chapitre 1','courses/documents/1783087938_CARI_2026_paper_29 (1).pdf','application/pdf',376531,'http://127.0.0.1:8000/storage/courses/documents/1783087938_CARI_2026_paper_29 (1).pdf',1,NULL,'pending','create',NULL,1,'2026-07-03 13:12:18','2026-07-03 13:12:18');
/*!40000 ALTER TABLE `documents` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `events` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `date` datetime NOT NULL,
  `type` enum('utilisateur','cours','categorie','site') NOT NULL DEFAULT 'utilisateur',
  `description` text DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `duration_type` enum('none','until','minutes') NOT NULL DEFAULT 'none',
  `end_date` datetime DEFAULT NULL,
  `duration_minutes` int(11) DEFAULT NULL,
  `repeat_event` tinyint(1) NOT NULL DEFAULT 0,
  `repeat_count` int(11) DEFAULT NULL,
  `course_id` bigint(20) unsigned DEFAULT NULL,
  `module_id` bigint(20) unsigned DEFAULT NULL,
  `category_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `events_course_id_foreign` (`course_id`),
  KEY `events_category_id_foreign` (`category_id`),
  KEY `events_module_id_foreign` (`module_id`),
  KEY `events_user_id_foreign` (`user_id`),
  CONSTRAINT `events_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `events_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `events`
--

LOCK TABLES `events` WRITE;
/*!40000 ALTER TABLE `events` DISABLE KEYS */;
INSERT INTO `events` VALUES
(17,60,'Correction du controle continu','2026-05-07 10:00:00','site','correction de votre epreuve de controle continu de l\'annee academique 2025-2026','Bloc pedagogique 2- e101','none',NULL,NULL,0,1,NULL,NULL,17,'2026-05-28 04:22:21','2026-05-28 04:22:21'),
(18,61,'Prise de contact','2026-05-04 08:00:00','categorie','description des objectifs du cours','Salle de master','none',NULL,NULL,0,1,NULL,NULL,18,'2026-05-28 04:35:19','2026-05-28 04:35:19'),
(19,61,'Arriver de la delegation etrangere','2026-05-01 14:00:00','site','delegation d\'une ecole partenaire','Salle des actes','none',NULL,NULL,0,1,NULL,NULL,17,'2026-05-28 04:36:21','2026-05-28 04:36:21'),
(20,NULL,'Devoir : devoir 1','2026-05-18 10:00:00','cours','description',NULL,'none',NULL,NULL,0,1,135,NULL,NULL,'2026-05-29 05:31:52','2026-05-29 05:31:52'),
(21,NULL,'Devoir : Fiche de td 1','2026-05-12 13:05:00','cours','description',NULL,'none',NULL,NULL,0,1,137,NULL,NULL,'2026-05-29 06:30:40','2026-05-29 06:30:40'),
(22,NULL,'Quiz : quiz suprise 1','2026-05-29 18:00:00','cours','description 1',NULL,'none',NULL,NULL,0,1,137,36,NULL,'2026-05-29 06:34:34','2026-05-29 06:34:34'),
(23,61,'presentation en groupe','2026-05-30 10:00:00','cours','description','bloc 2','none',NULL,NULL,0,1,133,NULL,17,'2026-05-29 06:36:25','2026-05-29 06:36:25'),
(24,59,'remise de mon expose','2026-05-29 12:54:00','utilisateur','description','e101','none',NULL,NULL,0,1,133,NULL,17,'2026-05-29 06:39:34','2026-05-29 06:39:34'),
(25,NULL,'Devoir : Devoir surveiller n1','2026-05-30 12:00:00','cours','optionnel',NULL,'none',NULL,NULL,0,1,139,NULL,NULL,'2026-05-29 16:19:43','2026-05-29 16:19:43'),
(26,NULL,'Quiz : quiz de remise a niveau','2026-05-29 20:00:00','cours','description',NULL,'none',NULL,NULL,0,1,139,38,NULL,'2026-05-29 16:21:01','2026-05-29 16:21:01'),
(27,NULL,'Devoir : Devoir surveiller n1','2026-05-30 12:00:00','cours','a faire en groupe de 6',NULL,'none',NULL,NULL,0,1,141,NULL,NULL,'2026-05-29 20:02:58','2026-05-29 20:02:58'),
(28,NULL,'Quiz : Quiz de controle de connaissance','2026-07-30 00:00:00','cours','vous allez evaluer votre niveau',NULL,'none',NULL,NULL,0,1,141,40,NULL,'2026-05-29 20:05:12','2026-05-29 20:05:12'),
(29,NULL,'Devoir : devoir 1','2026-05-30 12:00:00','cours','description',NULL,'none',NULL,NULL,0,1,150,NULL,NULL,'2026-05-30 15:59:22','2026-05-30 15:59:22'),
(30,NULL,'Devoir : devoir 1','2026-01-10 14:00:00','cours','description',NULL,'none',NULL,NULL,0,1,157,NULL,NULL,'2026-07-02 13:07:18','2026-07-02 13:07:18'),
(31,NULL,'Devoir : devoir test 1','2026-07-21 11:11:00','cours','instruction 1',NULL,'none',NULL,NULL,0,1,160,NULL,NULL,'2026-07-03 07:14:01','2026-07-03 07:14:01'),
(32,68,'evenement 1','2026-07-15 10:02:00','utilisateur','description','bp1','none',NULL,NULL,0,1,NULL,NULL,17,'2026-07-03 11:42:38','2026-07-03 11:42:38'),
(33,NULL,'Devoir : devoir 5','2026-07-28 11:01:00','cours','instructiions 5',NULL,'none',NULL,NULL,0,1,163,NULL,NULL,'2026-07-03 12:26:18','2026-07-03 12:26:18'),
(34,68,'expose','2026-08-22 12:00:00','utilisateur',NULL,'e101','none',NULL,NULL,0,1,163,NULL,17,'2026-07-03 12:56:22','2026-07-03 12:56:22'),
(35,NULL,'Devoir : analyse numerique','2026-07-12 12:00:00','cours','faire le quiz',NULL,'none',NULL,NULL,0,1,163,NULL,NULL,'2026-07-03 12:59:35','2026-07-03 12:59:35'),
(36,NULL,'Devoir : devoir 1','2026-07-28 12:00:00','cours','description',NULL,'none',NULL,NULL,0,1,166,NULL,NULL,'2026-07-03 13:14:04','2026-07-03 13:14:04'),
(37,NULL,'Quiz : quiz 1','2026-07-31 11:01:00','cours','8 files changed\r\n+131\r\n-30\r\nKeep\r\nUndo\r\nCourseController.phpMoodle-client • app/Http/Controllers\r\n+1\r\n-1\r\nModuleController.phpMoodle-client • app/Http/Controllers\r\n+49\r\n-12\r\nModule.phpMoodle-client • app/Models\r\n+2\r\n-0\r\nSyncService.phpMoodle-client • app/Services\r\n+35\r\n-8\r\n2026_07_03_113545_add_moodle_file_url_to_modules_table.phpMoodle-client • database/migrations\r\n+2\r\n-2\r\nshow.blade.phpMoodle-client • resources/views/courses\r\n+12\r\n-5\r\nd',NULL,'none',NULL,NULL,0,1,166,70,NULL,'2026-07-03 13:14:56','2026-07-03 13:14:56');
/*!40000 ALTER TABLE `events` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grade_items`
--

DROP TABLE IF EXISTS `grade_items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `grade_items` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL COMMENT 'Identifiant unique de Moodle',
  `course_id` bigint(20) unsigned NOT NULL,
  `item_name` varchar(255) NOT NULL COMMENT 'Nom du critère d''évaluation',
  `item_type` varchar(255) NOT NULL COMMENT 'assignment, quiz, forum, etc.',
  `grade_max` decimal(10,2) NOT NULL DEFAULT 100.00 COMMENT 'Note maximale',
  `sort_order` int(11) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1=visible, 0=hidden',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `grade_items_moodle_id_unique` (`moodle_id`),
  KEY `grade_items_course_id_status_index` (`course_id`,`status`),
  KEY `grade_items_moodle_id_index` (`moodle_id`),
  CONSTRAINT `grade_items_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grade_items`
--

LOCK TABLES `grade_items` WRITE;
/*!40000 ALTER TABLE `grade_items` DISABLE KEYS */;
/*!40000 ALTER TABLE `grade_items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grades`
--

DROP TABLE IF EXISTS `grades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `grades` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `grade` int(11) NOT NULL,
  `comment` text DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `submission_id` bigint(20) unsigned NOT NULL,
  `teacher_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `grades_submission_id_foreign` (`submission_id`),
  KEY `grades_teacher_id_foreign` (`teacher_id`),
  KEY `grades_sync_status_index` (`sync_status`),
  KEY `grades_dirty_index` (`dirty`),
  CONSTRAINT `grades_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `grades_teacher_id_foreign` FOREIGN KEY (`teacher_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grades`
--

LOCK TABLES `grades` WRITE;
/*!40000 ALTER TABLE `grades` DISABLE KEYS */;
/*!40000 ALTER TABLE `grades` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) unsigned NOT NULL,
  `reserved_at` int(10) unsigned DEFAULT NULL,
  `available_at` int(10) unsigned NOT NULL,
  `created_at` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
INSERT INTO `jobs` VALUES
(3,'default','{\"uuid\":\"0dd5c187-4db7-4827-b495-4e1d33415536\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:92;}\"}}',0,NULL,1779780141,1779780141),
(4,'default','{\"uuid\":\"4599200d-01be-4503-aaaf-dd8fbc6a0592\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:92;}\"}}',0,NULL,1779782055,1779782055),
(5,'default','{\"uuid\":\"a482656d-3bed-471d-b52f-dc36995b67dc\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:92;}\"}}',0,NULL,1779782118,1779782118),
(6,'default','{\"uuid\":\"5998a6ef-ee46-4045-9dce-1529d058dc16\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779782118,1779782118),
(7,'default','{\"uuid\":\"c855970d-63ec-4da7-aadc-42794739440a\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:92;}\"}}',0,NULL,1779782170,1779782170),
(8,'default','{\"uuid\":\"438ae303-a17c-4a1c-aa0d-e570c03f0e8d\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779782170,1779782170),
(9,'default','{\"uuid\":\"53aaef49-e744-4dec-8d55-08e48c89e1d9\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:92;}\"}}',0,NULL,1779783583,1779783583),
(10,'default','{\"uuid\":\"0a550580-15fb-475c-ac06-156e67cf91ed\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779783583,1779783583),
(11,'default','{\"uuid\":\"2b1fc899-dc46-48cb-9ca3-86bcc375da07\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779785849,1779785849),
(12,'default','{\"uuid\":\"fd5ee9b0-31d6-47b1-ab56-d4e13a954f77\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779785849,1779785849),
(13,'default','{\"uuid\":\"b983aa90-e925-4256-a4e5-011942cf1569\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779785856,1779785856),
(14,'default','{\"uuid\":\"b5b6f30f-8272-4124-8eb0-b672b3c3428c\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779785856,1779785856),
(15,'default','{\"uuid\":\"1cf78cc0-5d6b-4a98-8f7b-fbbe18520cfa\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779785867,1779785867),
(16,'default','{\"uuid\":\"150ef0d9-bc2b-4baa-9035-8c853713e28d\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779785867,1779785867),
(17,'default','{\"uuid\":\"efe783f4-c0f3-4a74-926c-1daa383aa387\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779785887,1779785887),
(18,'default','{\"uuid\":\"652cc28d-5fd7-44a2-82ae-06b983ee40ec\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779785887,1779785887),
(19,'default','{\"uuid\":\"eecfd1a4-ebbe-4db8-aad0-554c2e221cbd\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779785912,1779785912),
(20,'default','{\"uuid\":\"f892a99f-c061-49fb-b8ae-6411d0369aea\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779785912,1779785912),
(21,'default','{\"uuid\":\"b06d27ac-e034-4f0a-b3d2-6dea8b8e62f3\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779786308,1779786308),
(22,'default','{\"uuid\":\"a9475f75-bb65-42bb-a18f-4cd5961284d8\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779786308,1779786308),
(23,'default','{\"uuid\":\"52b9e79d-ca8b-47f1-9ec3-0e7c09b9574e\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779786492,1779786492),
(24,'default','{\"uuid\":\"d1ea56c1-5838-4101-abd5-4c9afef07266\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779786492,1779786492),
(25,'default','{\"uuid\":\"7d0cdfcd-9686-417a-9550-2362a1dc58e4\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:97;}\"}}',0,NULL,1779786492,1779786492),
(26,'default','{\"uuid\":\"9972945c-aae9-46f0-8d99-1f2401efe337\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779786537,1779786537),
(27,'default','{\"uuid\":\"9a0e1eb6-5c25-4606-9896-53902da97ca3\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779786537,1779786537),
(28,'default','{\"uuid\":\"7ff9423a-dc72-4a7c-b6fc-08747251017a\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:97;}\"}}',0,NULL,1779786537,1779786537),
(29,'default','{\"uuid\":\"33e3b86d-6314-44a6-b4c1-cfd680424946\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:94;}\"}}',0,NULL,1779786540,1779786540),
(30,'default','{\"uuid\":\"d23bbd1d-1630-4e62-8e0b-a46f463b115d\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:96;}\"}}',0,NULL,1779786540,1779786540),
(31,'default','{\"uuid\":\"3714c049-bfbf-40fb-9d90-7fa5e8b7a93f\",\"displayName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"job\":\"Illuminate\\\\Queue\\\\CallQueuedHandler@call\",\"maxTries\":null,\"maxExceptions\":null,\"failOnTimeout\":false,\"backoff\":null,\"timeout\":null,\"retryUntil\":null,\"data\":{\"commandName\":\"App\\\\Jobs\\\\ProcessSyncOperationJob\",\"command\":\"O:32:\\\"App\\\\Jobs\\\\ProcessSyncOperationJob\\\":1:{s:14:\\\"\\u0000*\\u0000operationId\\\";i:97;}\"}}',0,NULL,1779786540,1779786540);
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES
(1,'0001_01_01_000000_create_users_table',1),
(2,'0001_01_01_000001_create_cache_table',1),
(3,'0001_01_01_000002_create_jobs_table',1),
(4,'2025_01_07_041227_add_profile_picture_to_users_table',1),
(5,'2025_01_07_051748_create_permission_tables',1),
(6,'2025_01_10_030148_create_courses_table',1),
(7,'2025_01_10_030205_create_sections_table',1),
(8,'2025_01_10_030219_create_modules_table',1),
(9,'2025_01_10_030239_create_assignments_table',1),
(10,'2025_01_10_030247_create_submissions_table',1),
(11,'2025_01_10_030310_create_grades_table',1),
(12,'2025_01_26_140632_create_categories_table',1),
(13,'2025_01_26_140825_add_category_id_to_courses_table',1),
(14,'2025_01_27_042849_add_image_to_courses_table',1),
(15,'2025_02_01_135135_create_events_table',1),
(16,'2025_06_14_014532_add_assignment_fields_to_modules_table',1),
(17,'2025_12_24_191300_add_extra_fields_to_events_table',1),
(18,'2026_01_14_155113_create_questions_table',2),
(19,'2026_01_22_173217_update_submissions_table',2),
(20,'2026_01_22_195026_add_grade_to_submissions_table',2),
(21,'2026_01_24_create_course_user_table',2),
(22,'2026_01_24_create_announcements_table',3),
(23,'2026_01_24_create_competencies_table',3),
(24,'2026_01_24_create_course_competencies_table',3),
(25,'2026_01_24_create_documents_table',3),
(26,'2026_01_24_create_grade_items_table',3),
(27,'2026_01_24_create_participants_table',3),
(28,'2026_01_24_create_user_competencies_table',3),
(29,'2026_01_25_122353_add_moodle_fields_to_users_table',4),
(30,'2026_03_23_200225_add_moduleid_to_events_table',5),
(31,'2026_04_02_093702_add_quiz_columns_to_modules_table',5),
(32,'2026_04_02_093810_create_quiz_questions_table',5),
(33,'2026_04_02_093938_create_quiz_answers_table',5),
(34,'2026_04_02_094032_create_quiz_attempts_table',5),
(35,'2026_04_02_094132_create_quiz_attempt_answers_table',5),
(36,'2026_04_07_090000_add_sync_columns_to_courses_table',6),
(37,'2026_04_07_090100_add_sync_columns_to_sections_table',7),
(38,'2026_04_07_090200_add_sync_columns_to_modules_table',8),
(39,'2026_04_07_090300_add_sync_columns_to_participants_table',9),
(40,'2026_04_07_090400_add_sync_columns_to_documents_table',10),
(41,'2026_04_07_090500_add_sync_columns_to_announcements_table',11),
(42,'2026_04_07_090600_add_sync_columns_to_grades_table',12),
(43,'2026_04_07_090700_add_sync_columns_to_submissions_table',13),
(44,'2026_04_07_090800_add_sync_columns_to_quiz_attempts_table',14),
(45,'2026_04_07_091000_add_missing_columns_to_sections_table',15),
(46,'2026_04_07_091100_add_missing_columns_to_modules_table',16),
(47,'2026_04_07_091200_create_sync_queue_table',16),
(48,'2026_04_07_110000_add_sync_columns_to_categories_table',17),
(49,'2026_04_07_142500_add_sync_to_announcements',18),
(50,'2026_04_08_000000_add_sync_columns_to_users_table',19),
(51,'2026_04_08_110000_mark_pulled_entities_as_synced',20),
(52,'2026_04_27_093937_add_moodle_token_to_users_table',21),
(53,'2026_04_28_062759_add_lock_fields_to_sync_queue',22),
(54,'2026_05_06_122651_add_username_to_users_table',23),
(55,'2026_04_09_144855_add_module_id_to_submissions_table',24),
(56,'2026_04_10_100147_add_user_id_to_events_table',24),
(57,'2026_05_26_082842_make_assignment_id_nullable_in_submissions_table',25),
(58,'2026_05_26_120000_create_notification_reads_table',25),
(59,'2026_05_27_232820_add_moodle_fields_to_courses_table',26),
(60,'2026_05_28_060000_add_extra_fields_to_categories_table',27),
(61,'2026_05_29_000000_create_course_join_requests_table',28),
(62,'2026_05_30_162200_add_request_status_to_participants',29),
(63,'2026_05_30_170000_create_course_user_requests_table',30),
(64,'2026_07_03_113545_add_moodle_file_url_to_modules_table',31);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_permissions`
--

DROP TABLE IF EXISTS `model_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_permissions`
--

LOCK TABLES `model_has_permissions` WRITE;
/*!40000 ALTER TABLE `model_has_permissions` DISABLE KEYS */;
/*!40000 ALTER TABLE `model_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `model_has_roles`
--

DROP TABLE IF EXISTS `model_has_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `model_has_roles` (
  `role_id` bigint(20) unsigned NOT NULL,
  `model_type` varchar(255) NOT NULL,
  `model_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`),
  CONSTRAINT `model_has_roles_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `model_has_roles`
--

LOCK TABLES `model_has_roles` WRITE;
/*!40000 ALTER TABLE `model_has_roles` DISABLE KEYS */;
INSERT INTO `model_has_roles` VALUES
(1,'App\\Models\\User',6),
(1,'App\\Models\\User',12),
(1,'App\\Models\\User',13),
(1,'App\\Models\\User',14),
(1,'App\\Models\\User',33),
(1,'App\\Models\\User',34),
(1,'App\\Models\\User',35),
(1,'App\\Models\\User',36),
(1,'App\\Models\\User',37),
(1,'App\\Models\\User',38),
(1,'App\\Models\\User',39),
(1,'App\\Models\\User',40),
(1,'App\\Models\\User',41),
(1,'App\\Models\\User',42),
(1,'App\\Models\\User',43),
(1,'App\\Models\\User',44),
(1,'App\\Models\\User',45),
(1,'App\\Models\\User',46),
(1,'App\\Models\\User',47),
(1,'App\\Models\\User',48),
(1,'App\\Models\\User',49),
(1,'App\\Models\\User',50),
(1,'App\\Models\\User',51),
(1,'App\\Models\\User',52),
(1,'App\\Models\\User',53),
(1,'App\\Models\\User',54),
(1,'App\\Models\\User',55),
(1,'App\\Models\\User',56),
(1,'App\\Models\\User',57),
(2,'App\\Models\\User',8),
(2,'App\\Models\\User',9),
(2,'App\\Models\\User',11),
(2,'App\\Models\\User',12),
(2,'App\\Models\\User',13),
(2,'App\\Models\\User',14),
(2,'App\\Models\\User',17),
(2,'App\\Models\\User',19),
(2,'App\\Models\\User',21),
(2,'App\\Models\\User',22),
(2,'App\\Models\\User',24),
(2,'App\\Models\\User',25),
(2,'App\\Models\\User',27),
(2,'App\\Models\\User',28),
(2,'App\\Models\\User',30),
(2,'App\\Models\\User',33),
(2,'App\\Models\\User',34),
(2,'App\\Models\\User',35),
(2,'App\\Models\\User',36),
(2,'App\\Models\\User',37),
(2,'App\\Models\\User',38),
(2,'App\\Models\\User',39),
(2,'App\\Models\\User',40),
(2,'App\\Models\\User',41),
(2,'App\\Models\\User',42),
(2,'App\\Models\\User',43),
(2,'App\\Models\\User',44),
(2,'App\\Models\\User',45),
(2,'App\\Models\\User',46),
(2,'App\\Models\\User',47),
(2,'App\\Models\\User',48),
(2,'App\\Models\\User',49),
(2,'App\\Models\\User',50),
(2,'App\\Models\\User',51),
(2,'App\\Models\\User',52),
(2,'App\\Models\\User',53),
(2,'App\\Models\\User',54),
(2,'App\\Models\\User',55),
(2,'App\\Models\\User',56),
(2,'App\\Models\\User',57),
(2,'App\\Models\\User',59),
(2,'App\\Models\\User',60),
(2,'App\\Models\\User',63),
(2,'App\\Models\\User',69),
(3,'App\\Models\\User',7),
(3,'App\\Models\\User',10),
(3,'App\\Models\\User',15),
(3,'App\\Models\\User',16),
(3,'App\\Models\\User',18),
(3,'App\\Models\\User',20),
(3,'App\\Models\\User',23),
(3,'App\\Models\\User',26),
(3,'App\\Models\\User',29),
(3,'App\\Models\\User',31),
(3,'App\\Models\\User',58),
(3,'App\\Models\\User',61),
(3,'App\\Models\\User',62),
(3,'App\\Models\\User',64),
(3,'App\\Models\\User',65),
(3,'App\\Models\\User',66),
(3,'App\\Models\\User',67),
(3,'App\\Models\\User',68);
/*!40000 ALTER TABLE `model_has_roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `modules`
--

DROP TABLE IF EXISTS `modules`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `modules` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `modname` varchar(255) NOT NULL,
  `modplural` varchar(255) NOT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(4) NOT NULL DEFAULT 1,
  `completion` tinyint(4) NOT NULL DEFAULT 0,
  `downloadcontent` tinyint(1) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `moodle_file_url` text DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `section_id` bigint(20) unsigned NOT NULL,
  `assignment_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `intro` text DEFAULT NULL,
  `activity` text DEFAULT NULL,
  `duedate` timestamp NULL DEFAULT NULL,
  `timeopen` timestamp NULL DEFAULT NULL,
  `timeclose` timestamp NULL DEFAULT NULL,
  `timelimit` int(10) unsigned DEFAULT NULL,
  `attempts` int(10) unsigned NOT NULL DEFAULT 1,
  `grademethod` tinyint(3) unsigned NOT NULL DEFAULT 0,
  `shuffleanswers` tinyint(1) NOT NULL DEFAULT 1,
  `questionsperpage` int(10) unsigned NOT NULL DEFAULT 0,
  `allowsubmissionsfromdate` timestamp NULL DEFAULT NULL,
  `cutoffdate` timestamp NULL DEFAULT NULL,
  `gradingduedate` timestamp NULL DEFAULT NULL,
  `pdf_filename` varchar(255) DEFAULT NULL,
  `pdf_url` text DEFAULT NULL,
  `maxattempts` int(11) NOT NULL DEFAULT 1,
  `grade` int(11) NOT NULL DEFAULT 100,
  PRIMARY KEY (`id`),
  UNIQUE KEY `modules_moodle_id_unique` (`moodle_id`),
  KEY `modules_section_id_foreign` (`section_id`),
  KEY `modules_sync_status_index` (`sync_status`),
  KEY `modules_dirty_index` (`dirty`),
  CONSTRAINT `modules_section_id_foreign` FOREIGN KEY (`section_id`) REFERENCES `sections` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `modules`
--

LOCK TABLES `modules` WRITE;
/*!40000 ALTER TABLE `modules` DISABLE KEYS */;
INSERT INTO `modules` VALUES
(27,16,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=16','','synced',NULL,'2026-07-07 08:18:26',0,54,NULL,'2026-05-28 06:18:28','2026-07-07 08:18:26','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(28,17,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=17','','synced',NULL,'2026-07-07 08:18:27',0,60,NULL,'2026-05-28 06:33:44','2026-07-07 08:18:27','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(29,18,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=18','','synced',NULL,'2026-07-07 08:18:28',0,66,NULL,'2026-05-28 14:06:38','2026-07-07 08:18:28','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(30,19,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=19','','synced',NULL,'2026-07-07 08:18:28',0,71,NULL,'2026-05-28 14:06:38','2026-07-07 08:18:28','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(31,20,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=20','','synced',NULL,'2026-07-07 08:18:29',0,76,NULL,'2026-05-29 01:35:54','2026-07-07 08:18:29','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(32,NULL,'devoir 1','assign','Devoirs',0,1,0,0,'images/pdf/1780036312_exercice3_SMA.pdf',NULL,'synced',NULL,'2026-05-29 06:01:23',0,71,NULL,'2026-05-29 05:31:52','2026-05-29 06:01:23','consignes 1','description','2026-05-18 09:00:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1780036312_exercice3_SMA.pdf','/images/pdf/1780036312_exercice3_SMA.pdf',1,100),
(33,21,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=21','','synced',NULL,'2026-07-07 08:18:30',0,81,NULL,'2026-05-29 06:26:12','2026-07-07 08:18:30','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(34,22,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=22','','synced',NULL,'2026-07-07 08:18:30',0,86,NULL,'2026-05-29 06:26:13','2026-07-07 08:18:30','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(35,NULL,'Fiche de td 1','assign','Devoirs',0,1,0,0,'images/pdf/1780039840_preview(1).pdf',NULL,'synced',NULL,'2026-05-29 07:20:09',0,81,NULL,'2026-05-29 06:30:40','2026-05-29 07:20:09','instructions','description','2026-05-12 12:05:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1780039840_preview(1).pdf','/images/pdf/1780039840_preview(1).pdf',1,40),
(36,NULL,'quiz suprise 1','quiz','Quiz',0,1,0,0,'',NULL,'synced',NULL,'2026-05-29 07:20:09',0,81,NULL,'2026-05-29 06:34:34','2026-05-29 07:20:09','description 1',NULL,NULL,'2026-05-29 05:30:00','2026-05-29 17:00:00',1800,3,0,1,0,NULL,NULL,NULL,NULL,NULL,1,20),
(37,NULL,'Devoir surveiller n1','assign','Devoirs',0,1,0,0,'images/pdf/1780075183_preview.pdf',NULL,'synced',NULL,'2026-05-29 20:05:56',0,91,NULL,'2026-05-29 16:19:43','2026-05-29 20:05:56','vous devez livre une maquette en 2 heures','optionnel','2026-05-30 11:00:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1780075183_preview.pdf','/images/pdf/1780075183_preview.pdf',1,100),
(38,NULL,'quiz de remise a niveau','quiz','Quiz',0,1,0,0,'',NULL,'synced',NULL,'2026-05-29 20:05:56',0,91,NULL,'2026-05-29 16:21:01','2026-05-29 20:05:56','description',NULL,NULL,'2026-05-29 09:00:00','2026-05-29 19:00:00',600,3,0,1,0,NULL,NULL,NULL,NULL,NULL,1,20),
(39,NULL,'Devoir surveiller n1','assign','Devoirs',0,1,0,0,'images/pdf/1780088578_preview(2).pdf',NULL,'synced',NULL,'2026-05-30 06:39:03',0,92,NULL,'2026-05-29 20:02:58','2026-05-30 06:39:03','Nous attendons de vous de pouvoir rendre un travail coherent.','a faire en groupe de 6','2026-05-30 11:00:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1780088578_preview(2).pdf','/images/pdf/1780088578_preview(2).pdf',1,20),
(40,NULL,'Quiz de controle de connaissance','quiz','Quiz',0,1,0,0,'',NULL,'synced',NULL,'2026-05-30 06:39:03',0,92,NULL,'2026-05-29 20:05:12','2026-05-30 06:39:03','vous allez evaluer votre niveau',NULL,NULL,'2026-05-30 11:02:00','2026-07-29 23:00:00',3600,3,0,1,0,NULL,NULL,NULL,NULL,NULL,1,20),
(41,23,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=23','','synced',NULL,'2026-07-07 08:18:32',0,93,NULL,'2026-05-29 20:05:55','2026-07-07 08:18:32','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(42,24,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=24','','synced',NULL,'2026-07-07 08:18:32',0,98,NULL,'2026-05-30 06:46:37','2026-07-07 08:18:32','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(43,25,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=25','','synced',NULL,'2026-07-07 08:18:33',0,103,NULL,'2026-05-30 06:46:37','2026-07-07 08:18:33','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(44,26,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=26','','synced',NULL,'2026-07-07 08:18:34',0,110,NULL,'2026-05-30 09:06:28','2026-07-07 08:18:34','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(45,27,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=27','','synced',NULL,'2026-07-07 08:18:35',0,115,NULL,'2026-05-30 09:06:28','2026-07-07 08:18:35','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(46,28,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=28','','synced',NULL,'2026-07-07 08:18:35',0,120,NULL,'2026-05-30 10:09:08','2026-07-07 08:18:35','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(47,29,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=29','','synced',NULL,'2026-07-07 08:18:36',0,125,NULL,'2026-05-30 10:09:09','2026-07-07 08:18:36','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(48,30,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=30','','synced',NULL,'2026-07-07 08:18:37',0,130,NULL,'2026-05-30 10:12:23','2026-07-07 08:18:37','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(49,31,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=31','','synced',NULL,'2026-07-07 08:18:37',0,135,NULL,'2026-05-30 14:58:43','2026-07-07 08:18:37','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(50,NULL,'devoir 1','assign','Devoirs',0,1,0,0,'images/pdf/1780160362_exercice3_SMA.pdf',NULL,'synced',NULL,'2026-05-30 16:23:21',0,140,NULL,'2026-05-30 15:59:22','2026-05-30 16:23:21','instructions claire','description','2026-05-30 11:00:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1780160362_exercice3_SMA.pdf','/images/pdf/1780160362_exercice3_SMA.pdf',1,100),
(51,32,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=32','','synced',NULL,'2026-07-07 08:18:38',0,142,NULL,'2026-05-30 16:23:26','2026-07-07 08:18:38','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(52,33,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=33','','synced',NULL,'2026-07-07 08:18:39',0,148,NULL,'2026-05-30 16:23:27','2026-07-07 08:18:39','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(53,34,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=34','','synced',NULL,'2026-07-07 08:18:39',0,153,NULL,'2026-05-30 16:23:27','2026-07-07 08:18:39','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(54,35,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=35','','synced',NULL,'2026-07-07 08:18:40',0,158,NULL,'2026-05-30 16:23:27','2026-07-07 08:18:40','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(55,NULL,'devoir 1','assign','Devoirs',0,1,0,0,'images/pdf/1783001238_SBOM Framing Software Component Transparency 2024.pdf',NULL,'pending',NULL,NULL,1,165,NULL,'2026-07-02 13:07:18','2026-07-02 13:07:18','instructions','description','2026-01-10 13:00:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1783001238_SBOM Framing Software Component Transparency 2024.pdf','/images/pdf/1783001238_SBOM Framing Software Component Transparency 2024.pdf',1,100),
(56,36,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=36','','synced',NULL,'2026-07-07 08:18:41',0,167,NULL,'2026-07-03 06:48:43','2026-07-07 08:18:41','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(57,37,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=37','','synced',NULL,'2026-07-07 08:18:41',0,173,NULL,'2026-07-03 06:55:32','2026-07-07 08:18:41','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(58,NULL,'devoir test 1','assign','Devoirs',0,1,0,0,'images/pdf/1783066441_CARI_2026_paper_29 (1).pdf',NULL,'synced',NULL,'2026-07-03 07:15:54',0,173,NULL,'2026-07-03 07:14:01','2026-07-03 07:15:54','instruction 1',NULL,'2026-07-21 10:11:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1783066441_CARI_2026_paper_29 (1).pdf','/images/pdf/1783066441_CARI_2026_paper_29 (1).pdf',1,65),
(59,38,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=38','','synced',NULL,'2026-07-07 08:18:42',0,178,NULL,'2026-07-03 07:32:36','2026-07-07 08:18:42','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(60,39,'fichier','resource','Files',0,1,0,1,'moodle_files/courses/23/modules/39/fichier.png','http://http://localhost/webservice/pluginfile.php/138/mod_resource/content/1/fichier.png?forcedownload=1','synced',NULL,'2026-07-07 08:18:30',0,86,NULL,'2026-07-03 08:39:13','2026-07-07 08:18:30','<div class=\"no-overflow\"><p>description</p></div>',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(61,40,'fichier 2','resource','Files',0,1,0,1,'moodle_files/courses/23/modules/40/fichier 2.png','http://http://localhost/webservice/pluginfile.php/139/mod_resource/content/1/fichier%202.png?forcedownload=1','synced',NULL,'2026-07-07 08:18:30',0,86,NULL,'2026-07-03 08:41:36','2026-07-07 08:18:30','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(62,41,'fichier 4','resource','Files',0,1,0,1,'moodle_files/courses/23/modules/41/d.png','http://http://localhost/webservice/pluginfile.php/140/mod_resource/content/1/d.png?forcedownload=1','synced',NULL,'2026-07-07 08:18:30',0,86,NULL,'2026-07-03 09:08:28','2026-07-07 08:18:30','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(63,42,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=42','','synced',NULL,'2026-07-07 08:18:43',0,183,NULL,'2026-07-03 10:25:18','2026-07-07 08:18:43','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(64,43,'fichier 5','resource','Files',0,1,0,1,'moodle_files/courses/23/modules/43/cover 4.png','http://http://localhost/webservice/pluginfile.php/143/mod_resource/content/1/cover%204.png?forcedownload=1','synced',NULL,'2026-07-07 08:18:30',0,86,NULL,'2026-07-03 10:41:26','2026-07-07 08:18:30','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(65,44,'a','resource','Files',0,1,0,1,'moodle_files/courses/23/modules/44/Gemini_Generated_Image_gb6kn9gb6kn9gb6k.png','http://http://localhost/webservice/pluginfile.php/146/mod_resource/content/1/Gemini_Generated_Image_gb6kn9gb6kn9gb6k.png?forcedownload=1','synced',NULL,'2026-07-07 08:18:30',0,87,NULL,'2026-07-03 12:07:29','2026-07-07 08:18:30','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(66,45,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=45','','synced',NULL,'2026-07-07 08:18:44',0,190,NULL,'2026-07-03 12:23:34','2026-07-07 08:18:44','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(67,NULL,'devoir 5','assign','Devoirs',0,1,0,0,'images/pdf/1783085178_CARI_2026_paper_29 (1).pdf',NULL,'synced',NULL,'2026-07-03 12:26:36',0,190,NULL,'2026-07-03 12:26:18','2026-07-03 12:26:36','instructiions 5',NULL,'2026-07-28 10:01:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1783085178_CARI_2026_paper_29 (1).pdf','/images/pdf/1783085178_CARI_2026_paper_29 (1).pdf',1,100),
(68,NULL,'analyse numerique','assign','Devoirs',0,1,0,0,'images/pdf/1783087175_sbom_minimum_elements_report_0.pdf',NULL,'synced',NULL,'2026-07-03 13:19:21',0,190,NULL,'2026-07-03 12:59:35','2026-07-03 13:19:21','faire le quiz',NULL,'2026-07-12 11:00:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1783087175_sbom_minimum_elements_report_0.pdf','/images/pdf/1783087175_sbom_minimum_elements_report_0.pdf',1,60),
(69,NULL,'devoir 1','assign','Devoirs',0,1,0,0,'images/pdf/1783088044_CARI_2026_paper_29 (1).pdf',NULL,'synced',NULL,'2026-07-03 13:19:21',0,195,NULL,'2026-07-03 13:14:04','2026-07-03 13:19:21',NULL,'description','2026-07-28 11:00:00',NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,'1783088044_CARI_2026_paper_29 (1).pdf','/images/pdf/1783088044_CARI_2026_paper_29 (1).pdf',1,20),
(70,NULL,'quiz 1','quiz','Quiz',0,1,0,0,'',NULL,'synced',NULL,'2026-07-03 13:19:21',0,195,NULL,'2026-07-03 13:14:56','2026-07-03 13:19:21','8 files changed\r\n+131\r\n-30\r\nKeep\r\nUndo\r\nCourseController.phpMoodle-client • app/Http/Controllers\r\n+1\r\n-1\r\nModuleController.phpMoodle-client • app/Http/Controllers\r\n+49\r\n-12\r\nModule.phpMoodle-client • app/Models\r\n+2\r\n-0\r\nSyncService.phpMoodle-client • app/Services\r\n+35\r\n-8\r\n2026_07_03_113545_add_moodle_file_url_to_modules_table.phpMoodle-client • database/migrations\r\n+2\r\n-2\r\nshow.blade.phpMoodle-client • resources/views/courses\r\n+12\r\n-5\r\nd',NULL,NULL,'2026-07-20 10:01:00','2026-07-31 10:01:00',NULL,0,0,1,0,NULL,NULL,NULL,NULL,NULL,1,20),
(71,47,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=47','','synced',NULL,'2026-07-07 08:18:44',0,196,NULL,'2026-07-03 13:19:20','2026-07-07 08:18:44','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100),
(72,48,'Announcements','forum','Forums',0,1,0,1,'http://localhost/mod/forum/view.php?id=48','','synced',NULL,'2026-07-07 08:18:46',0,201,NULL,'2026-07-07 08:18:45','2026-07-07 08:18:46','',NULL,NULL,NULL,NULL,NULL,1,0,1,0,NULL,NULL,NULL,NULL,NULL,1,100);
/*!40000 ALTER TABLE `modules` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `notification_reads`
--

DROP TABLE IF EXISTS `notification_reads`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification_reads` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` bigint(20) unsigned NOT NULL,
  `notification_key` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `notification_reads_user_id_notification_key_unique` (`user_id`,`notification_key`),
  CONSTRAINT `notification_reads_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `notification_reads`
--

LOCK TABLES `notification_reads` WRITE;
/*!40000 ALTER TABLE `notification_reads` DISABLE KEYS */;
INSERT INTO `notification_reads` VALUES
(1,61,'document_31','2026-05-29 03:32:33','2026-05-29 03:32:33'),
(2,61,'announcement_16','2026-05-29 03:32:56','2026-05-29 03:32:56'),
(3,61,'document_29','2026-05-29 03:33:06','2026-05-29 03:33:06'),
(4,61,'document_28','2026-05-29 03:33:06','2026-05-29 03:33:06'),
(5,61,'announcement_15','2026-05-29 03:33:06','2026-05-29 03:33:06'),
(6,61,'document_30','2026-05-29 03:34:09','2026-05-29 03:34:09'),
(7,61,'announcement_17','2026-05-29 03:34:09','2026-05-29 03:34:09'),
(8,59,'announcement_16','2026-05-29 03:35:49','2026-05-29 03:35:49'),
(9,59,'assign_32','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(10,59,'document_31','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(11,59,'document_29','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(12,59,'document_28','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(13,59,'announcement_17','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(14,59,'announcement_15','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(15,59,'enrollment_133','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(16,59,'enrollment_135','2026-05-29 06:02:28','2026-05-29 06:02:28'),
(17,61,'quiz_36','2026-05-29 06:35:06','2026-05-29 06:35:06'),
(18,59,'quiz_36','2026-05-29 06:38:59','2026-05-29 06:38:59'),
(19,59,'enrollment_137','2026-05-29 06:38:59','2026-05-29 06:38:59'),
(20,59,'assign_35','2026-05-29 06:38:59','2026-05-29 06:38:59'),
(21,58,'join_request_1','2026-05-29 07:17:33','2026-05-29 07:17:33'),
(22,63,'quiz_38','2026-05-29 16:22:20','2026-05-29 16:22:20'),
(23,63,'assign_37','2026-05-29 16:22:20','2026-05-29 16:22:20'),
(24,63,'document_32','2026-05-29 16:22:20','2026-05-29 16:22:20'),
(25,63,'announcement_18','2026-05-29 16:22:20','2026-05-29 16:22:20'),
(26,63,'enrollment_139','2026-05-29 16:22:20','2026-05-29 16:22:20'),
(27,63,'enrollment_132','2026-05-29 16:22:20','2026-05-29 16:22:20'),
(28,61,'quiz_38','2026-05-29 19:59:34','2026-05-29 19:59:34'),
(29,61,'assign_37','2026-05-29 19:59:34','2026-05-29 19:59:34'),
(30,61,'document_32','2026-05-29 19:59:34','2026-05-29 19:59:34'),
(31,61,'announcement_18','2026-05-29 19:59:34','2026-05-29 19:59:34'),
(32,61,'assign_35','2026-05-29 19:59:34','2026-05-29 19:59:34'),
(33,61,'assign_32','2026-05-29 19:59:34','2026-05-29 19:59:34'),
(34,61,'document_33','2026-05-30 15:17:09','2026-05-30 15:17:09'),
(35,61,'quiz_40','2026-05-30 15:17:14','2026-05-30 15:17:14'),
(36,61,'assign_39','2026-05-30 15:17:19','2026-05-30 15:17:19'),
(37,61,'announcement_19','2026-05-30 15:17:22','2026-05-30 15:17:22'),
(38,63,'quiz_40','2026-05-30 15:47:03','2026-05-30 15:47:03');
/*!40000 ALTER TABLE `notification_reads` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `participants`
--

DROP TABLE IF EXISTS `participants`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_enrolment_id` int(11) DEFAULT NULL COMMENT 'Identifiant unique de l''enrôlement Moodle',
  `course_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `role` varchar(255) NOT NULL COMMENT 'ROLE_TEACHER, ROLE_STUDENT, ROLE_USER',
  `status` int(11) NOT NULL DEFAULT 1 COMMENT '1=active, 0=suspended',
  `request_status` varchar(255) DEFAULT NULL COMMENT 'pending, approved, rejected',
  `enrolled_at` timestamp NULL DEFAULT NULL,
  `unenrolled_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_course_id_user_id_unique` (`course_id`,`user_id`),
  UNIQUE KEY `participants_moodle_enrolment_id_unique` (`moodle_enrolment_id`),
  KEY `participants_user_id_foreign` (`user_id`),
  KEY `participants_moodle_enrolment_id_index` (`moodle_enrolment_id`),
  KEY `participants_course_id_role_status_index` (`course_id`,`role`,`status`),
  KEY `participants_sync_status_index` (`sync_status`),
  KEY `participants_dirty_index` (`dirty`),
  CONSTRAINT `participants_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE,
  CONSTRAINT `participants_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=1134 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `participants`
--

LOCK TABLES `participants` WRITE;
/*!40000 ALTER TABLE `participants` DISABLE KEYS */;
INSERT INTO `participants` VALUES
(32,18,135,59,'student',1,NULL,'2026-05-28 07:25:01',NULL,'synced','create','2026-07-07 08:18:29',0,'2026-05-28 07:25:01','2026-07-07 08:18:29'),
(33,NULL,133,59,'ROLE_STUDENT',1,NULL,'2026-05-29 01:11:12',NULL,'synced','create','2026-05-29 01:14:59',0,'2026-05-29 01:11:12','2026-05-29 01:14:59'),
(35,NULL,133,60,'ROLE_STUDENT',1,NULL,'2026-05-29 01:14:33',NULL,'synced','create','2026-05-29 01:14:59',0,'2026-05-29 01:14:33','2026-05-29 01:14:59'),
(36,20,136,61,'teacher',1,NULL,NULL,NULL,'synced',NULL,'2026-07-07 08:18:29',0,'2026-05-29 01:35:55','2026-07-07 08:18:29'),
(38,2,138,64,'editingteacher',1,NULL,NULL,NULL,'synced',NULL,'2026-07-07 08:18:31',0,'2026-05-29 06:26:14','2026-07-07 08:18:31'),
(39,NULL,137,59,'ROLE_STUDENT',1,NULL,'2026-05-29 06:31:21',NULL,'synced','create','2026-05-29 07:20:09',0,'2026-05-29 06:31:21','2026-05-29 07:20:09'),
(40,NULL,137,64,'ROLE_STUDENT',1,NULL,'2026-05-29 06:31:53',NULL,'synced','create','2026-05-29 07:20:09',0,'2026-05-29 06:31:53','2026-05-29 07:20:09'),
(41,NULL,137,62,'ROLE_STUDENT',1,NULL,'2026-05-29 06:32:00',NULL,'synced','create','2026-05-29 07:20:09',0,'2026-05-29 06:32:00','2026-05-29 07:20:09'),
(42,22,132,63,'student',1,NULL,'2026-05-29 07:17:44',NULL,'synced','create','2026-07-07 08:18:27',0,'2026-05-29 07:17:44','2026-07-07 08:18:27'),
(44,NULL,139,63,'ROLE_STUDENT',1,NULL,'2026-05-29 16:14:23',NULL,'synced','create','2026-05-29 16:16:24',0,'2026-05-29 16:14:23','2026-05-29 16:16:24'),
(45,NULL,140,63,'ROLE_STUDENT',1,NULL,'2026-05-29 19:58:13',NULL,'synced','create','2026-05-30 06:39:03',0,'2026-05-29 19:58:13','2026-05-30 06:39:03'),
(46,NULL,140,62,'ROLE_STUDENT',1,NULL,'2026-05-29 19:58:21',NULL,'synced','create','2026-05-30 06:39:03',0,'2026-05-29 19:58:21','2026-05-30 06:39:03'),
(47,NULL,141,63,'ROLE_STUDENT',1,NULL,'2026-05-29 20:01:04',NULL,'synced','create','2026-05-30 06:39:03',0,'2026-05-29 20:01:04','2026-05-30 06:39:03'),
(48,NULL,141,62,'ROLE_STUDENT',1,NULL,'2026-05-29 20:01:10',NULL,'synced','create','2026-05-30 06:39:03',0,'2026-05-29 20:01:10','2026-05-30 06:39:03'),
(185,NULL,150,59,'ROLE_STUDENT',1,NULL,'2026-05-30 15:56:19',NULL,'synced','create','2026-05-30 16:23:21',0,'2026-05-30 15:56:19','2026-05-30 16:23:21'),
(252,NULL,155,59,'ROLE_STUDENT',1,NULL,'2026-07-02 08:31:23',NULL,'pending','create',NULL,1,'2026-07-02 08:31:23','2026-07-02 08:31:23'),
(253,NULL,157,59,'ROLE_USER',1,NULL,'2026-07-02 11:39:36',NULL,'pending','create',NULL,1,'2026-07-02 11:39:36','2026-07-02 11:39:36'),
(254,NULL,157,60,'ROLE_STUDENT',1,NULL,'2026-07-02 11:39:45',NULL,'pending','create',NULL,1,'2026-07-02 11:39:45','2026-07-02 11:39:45'),
(320,26,160,67,'teacher',1,NULL,NULL,NULL,'synced',NULL,'2026-07-07 08:18:42',0,'2026-07-03 06:55:33','2026-07-07 08:18:42'),
(572,NULL,138,59,'ROLE_STUDENT',1,NULL,'2026-07-03 10:32:38',NULL,'pending','create',NULL,1,'2026-07-03 10:32:38','2026-07-03 10:32:38'),
(993,NULL,138,69,'ROLE_STUDENT',1,NULL,'2026-07-03 12:12:59',NULL,'pending','create',NULL,1,'2026-07-03 12:12:59','2026-07-03 12:12:59'),
(1039,27,163,68,'teacher',1,NULL,NULL,NULL,'synced',NULL,'2026-07-07 08:18:44',0,'2026-07-03 12:23:35','2026-07-07 08:18:44'),
(1100,NULL,166,69,'ROLE_STUDENT',1,NULL,'2026-07-03 13:11:50',NULL,'synced','create','2026-07-03 13:19:21',0,'2026-07-03 13:11:50','2026-07-03 13:19:21');
/*!40000 ALTER TABLE `participants` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `permissions`
--

DROP TABLE IF EXISTS `permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `permissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `permissions`
--

LOCK TABLES `permissions` WRITE;
/*!40000 ALTER TABLE `permissions` DISABLE KEYS */;
INSERT INTO `permissions` VALUES
(1,'view_announcements','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(2,'create_announcement','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(3,'edit_announcement','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(4,'delete_announcement','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(5,'view_documents','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(6,'upload_document','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(7,'edit_document','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(8,'delete_document','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(9,'view_participants','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(10,'manage_participants','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(11,'enrol_user','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(12,'unenrol_user','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(13,'view_grades','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(14,'view_own_grades','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(15,'create_grade','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(16,'edit_grade','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(17,'delete_grade','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(18,'view_competencies','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(19,'manage_competencies','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(20,'view_own_competencies','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(21,'mark_competency_complete','web','2026-01-24 16:01:42','2026-01-24 16:01:42'),
(22,'manage_site','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(23,'manage_users','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(24,'manage_sync','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(25,'manage_courses','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(26,'view_courses','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(27,'manage_sections','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(28,'create_assignment','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(29,'edit_assignment','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(30,'delete_assignment','web','2026-04-27 08:31:41','2026-04-27 08:31:41'),
(31,'submit_assignment','web','2026-04-27 08:31:41','2026-04-27 08:31:41');
/*!40000 ALTER TABLE `permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `questions`
--

DROP TABLE IF EXISTS `questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `content` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'text',
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `answer` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `questions`
--

LOCK TABLES `questions` WRITE;
/*!40000 ALTER TABLE `questions` DISABLE KEYS */;
/*!40000 ALTER TABLE `questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_answers`
--

DROP TABLE IF EXISTS `quiz_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` bigint(20) unsigned NOT NULL,
  `answer` text NOT NULL,
  `fraction` decimal(10,7) NOT NULL DEFAULT 0.0000000,
  `feedback` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_answers_question_id_foreign` (`question_id`),
  CONSTRAINT `quiz_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_answers`
--

LOCK TABLES `quiz_answers` WRITE;
/*!40000 ALTER TABLE `quiz_answers` DISABLE KEYS */;
INSERT INTO `quiz_answers` VALUES
(1,1,'reponse 1',1.0000000,NULL,'2026-05-29 06:34:34','2026-05-29 06:34:34'),
(2,1,'reponse 2',0.0000000,NULL,'2026-05-29 06:34:34','2026-05-29 06:34:34'),
(3,2,'Reponse 1',0.0000000,NULL,'2026-05-29 06:34:34','2026-05-29 06:34:34'),
(4,2,'Reponse 2',1.0000000,NULL,'2026-05-29 06:34:34','2026-05-29 06:34:34'),
(5,3,'madrid',1.0000000,NULL,'2026-05-29 16:21:01','2026-05-29 16:21:01'),
(6,3,'barcelone',0.0000000,NULL,'2026-05-29 16:21:01','2026-05-29 16:21:01'),
(7,4,'une reprensatation dans un espace en 0 dimensions',1.0000000,NULL,'2026-05-29 20:05:12','2026-05-29 20:05:12'),
(8,4,'une quantite infini',0.0000000,NULL,'2026-05-29 20:05:12','2026-05-29 20:05:12'),
(9,5,'une ligne infinie',1.0000000,NULL,'2026-05-29 20:05:12','2026-05-29 20:05:12'),
(10,5,'un trait',0.0000000,NULL,'2026-05-29 20:05:12','2026-05-29 20:05:12');
/*!40000 ALTER TABLE `quiz_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_attempt_answers`
--

DROP TABLE IF EXISTS `quiz_attempt_answers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_attempt_answers` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `attempt_id` bigint(20) unsigned NOT NULL,
  `question_id` bigint(20) unsigned NOT NULL,
  `answer_id` bigint(20) unsigned DEFAULT NULL,
  `fraction` decimal(10,7) NOT NULL DEFAULT 0.0000000,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_attempt_answers_attempt_id_foreign` (`attempt_id`),
  KEY `quiz_attempt_answers_question_id_foreign` (`question_id`),
  KEY `quiz_attempt_answers_answer_id_foreign` (`answer_id`),
  CONSTRAINT `quiz_attempt_answers_answer_id_foreign` FOREIGN KEY (`answer_id`) REFERENCES `quiz_answers` (`id`) ON DELETE SET NULL,
  CONSTRAINT `quiz_attempt_answers_attempt_id_foreign` FOREIGN KEY (`attempt_id`) REFERENCES `quiz_attempts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempt_answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `quiz_questions` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_attempt_answers`
--

LOCK TABLES `quiz_attempt_answers` WRITE;
/*!40000 ALTER TABLE `quiz_attempt_answers` DISABLE KEYS */;
INSERT INTO `quiz_attempt_answers` VALUES
(1,1,1,1,1.0000000,'2026-05-29 06:38:19','2026-05-29 06:38:19'),
(2,1,2,3,0.0000000,'2026-05-29 06:38:19','2026-05-29 06:38:19'),
(3,2,3,5,1.0000000,'2026-05-29 16:22:07','2026-05-29 16:22:07'),
(4,3,4,7,1.0000000,'2026-05-30 15:47:13','2026-05-30 15:47:13'),
(5,3,5,10,0.0000000,'2026-05-30 15:47:13','2026-05-30 15:47:13');
/*!40000 ALTER TABLE `quiz_attempt_answers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_attempts`
--

DROP TABLE IF EXISTS `quiz_attempts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_attempts` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `attempt` int(10) unsigned NOT NULL DEFAULT 1,
  `state` varchar(20) NOT NULL DEFAULT 'inprogress',
  `sumgrades` decimal(10,5) DEFAULT NULL,
  `timestart` timestamp NULL DEFAULT NULL,
  `timefinish` timestamp NULL DEFAULT NULL,
  `moodle_attempt_id` bigint(20) unsigned DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_attempts_module_id_foreign` (`module_id`),
  KEY `quiz_attempts_user_id_foreign` (`user_id`),
  KEY `quiz_attempts_sync_status_index` (`sync_status`),
  KEY `quiz_attempts_dirty_index` (`dirty`),
  CONSTRAINT `quiz_attempts_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `quiz_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_attempts`
--

LOCK TABLES `quiz_attempts` WRITE;
/*!40000 ALTER TABLE `quiz_attempts` DISABLE KEYS */;
INSERT INTO `quiz_attempts` VALUES
(1,36,59,1,'finished',10.00000,'2026-05-29 06:37:59','2026-05-29 06:38:19',NULL,'synced',NULL,'2026-05-29 07:20:09',0,'2026-05-29 06:37:59','2026-05-29 07:20:09'),
(2,38,63,1,'finished',20.00000,'2026-05-29 16:22:01','2026-05-29 16:22:07',NULL,'synced',NULL,'2026-05-30 06:39:02',0,'2026-05-29 16:22:01','2026-05-30 06:39:02'),
(3,40,63,1,'finished',10.00000,'2026-05-30 15:47:05','2026-05-30 15:47:13',NULL,'synced',NULL,'2026-05-30 15:47:54',0,'2026-05-30 15:47:05','2026-05-30 15:47:54');
/*!40000 ALTER TABLE `quiz_attempts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `quiz_questions`
--

DROP TABLE IF EXISTS `quiz_questions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `quiz_questions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) unsigned NOT NULL,
  `qtype` varchar(50) NOT NULL DEFAULT 'multichoice',
  `questiontext` text NOT NULL,
  `defaultmark` int(10) unsigned NOT NULL DEFAULT 1,
  `slot` int(10) unsigned NOT NULL DEFAULT 1,
  `moodle_question_id` bigint(20) unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `quiz_questions_module_id_foreign` (`module_id`),
  CONSTRAINT `quiz_questions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `quiz_questions`
--

LOCK TABLES `quiz_questions` WRITE;
/*!40000 ALTER TABLE `quiz_questions` DISABLE KEYS */;
INSERT INTO `quiz_questions` VALUES
(1,36,'multichoice','question 1',10,1,NULL,'2026-05-29 06:34:34','2026-05-29 06:34:34'),
(2,36,'multichoice','question 2',10,2,NULL,'2026-05-29 06:34:34','2026-05-29 06:34:34'),
(3,38,'multichoice','quel est la capitale de l\'espagne',20,1,NULL,'2026-05-29 16:21:01','2026-05-29 16:21:01'),
(4,40,'multichoice','qu\'est ce qu\'un point',10,1,NULL,'2026-05-29 20:05:12','2026-05-29 20:05:12'),
(5,40,'multichoice','qu\'est ce qu\'une droite',10,2,NULL,'2026-05-29 20:05:12','2026-05-29 20:05:12');
/*!40000 ALTER TABLE `quiz_questions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `role_has_permissions`
--

DROP TABLE IF EXISTS `role_has_permissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `role_has_permissions` (
  `permission_id` bigint(20) unsigned NOT NULL,
  `role_id` bigint(20) unsigned NOT NULL,
  PRIMARY KEY (`permission_id`,`role_id`),
  KEY `role_has_permissions_role_id_foreign` (`role_id`),
  CONSTRAINT `role_has_permissions_permission_id_foreign` FOREIGN KEY (`permission_id`) REFERENCES `permissions` (`id`) ON DELETE CASCADE,
  CONSTRAINT `role_has_permissions_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `role_has_permissions`
--

LOCK TABLES `role_has_permissions` WRITE;
/*!40000 ALTER TABLE `role_has_permissions` DISABLE KEYS */;
INSERT INTO `role_has_permissions` VALUES
(1,1),
(1,2),
(1,3),
(2,3),
(3,3),
(4,3),
(5,1),
(5,2),
(5,3),
(6,3),
(7,3),
(8,3),
(9,1),
(9,2),
(9,3),
(10,3),
(11,3),
(12,3),
(13,3),
(14,2),
(15,3),
(16,3),
(17,3),
(18,1),
(18,2),
(18,3),
(19,3),
(20,2),
(21,3),
(25,3),
(26,1),
(26,2),
(26,3),
(27,3),
(28,3),
(29,3),
(30,3),
(31,2);
/*!40000 ALTER TABLE `role_has_permissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `guard_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES
(1,'ROLE_USER','web',NULL,NULL),
(2,'ROLE_STUDENT','web',NULL,NULL),
(3,'ROLE_TEACHER','web',NULL,NULL);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sections`
--

DROP TABLE IF EXISTS `sections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sections` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `summary` text DEFAULT NULL,
  `position` int(11) NOT NULL DEFAULT 0,
  `visible` tinyint(4) NOT NULL DEFAULT 1,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `course_id` bigint(20) unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sections_moodle_id_unique` (`moodle_id`),
  KEY `sections_course_id_foreign` (`course_id`),
  KEY `sections_sync_status_index` (`sync_status`),
  KEY `sections_dirty_index` (`dirty`),
  CONSTRAINT `sections_course_id_foreign` FOREIGN KEY (`course_id`) REFERENCES `courses` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=206 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sections`
--

LOCK TABLES `sections` WRITE;
/*!40000 ALTER TABLE `sections` DISABLE KEYS */;
INSERT INTO `sections` VALUES
(53,NULL,'Introduction a la theorie des ensembles finis',NULL,0,1,'pending','create',NULL,1,132,'2026-05-28 04:16:39','2026-05-28 04:16:39'),
(54,28,'General','',0,1,'synced',NULL,'2026-07-07 08:18:26',0,132,'2026-05-28 06:18:28','2026-07-07 08:18:26'),
(55,29,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:26',0,132,'2026-05-28 06:18:28','2026-07-07 08:18:26'),
(56,30,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:26',0,132,'2026-05-28 06:18:28','2026-07-07 08:18:26'),
(57,31,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:26',0,132,'2026-05-28 06:18:28','2026-07-07 08:18:26'),
(58,32,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:26',0,132,'2026-05-28 06:18:28','2026-07-07 08:18:26'),
(59,NULL,'intro',NULL,0,1,'pending','create',NULL,1,133,'2026-05-28 06:22:27','2026-05-28 06:22:27'),
(60,33,'General','',0,1,'synced',NULL,'2026-07-07 08:18:27',0,133,'2026-05-28 06:33:44','2026-07-07 08:18:27'),
(61,34,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:27',0,133,'2026-05-28 06:33:44','2026-07-07 08:18:27'),
(62,35,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:27',0,133,'2026-05-28 06:33:44','2026-07-07 08:18:27'),
(63,36,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:27',0,133,'2026-05-28 06:33:44','2026-07-07 08:18:27'),
(64,37,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:27',0,133,'2026-05-28 06:33:44','2026-07-07 08:18:27'),
(65,NULL,'introduction',NULL,0,1,'pending','create',NULL,1,135,'2026-05-28 06:39:56','2026-05-28 06:39:56'),
(66,38,'General','',0,1,'synced',NULL,'2026-07-07 08:18:28',0,134,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(67,39,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:28',0,134,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(68,40,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:28',0,134,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(69,41,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:28',0,134,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(70,42,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:28',0,134,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(71,43,'General','',0,1,'synced',NULL,'2026-07-07 08:18:28',0,135,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(72,44,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:28',0,135,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(73,45,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:28',0,135,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(74,46,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:28',0,135,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(75,47,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:28',0,135,'2026-05-28 14:06:38','2026-07-07 08:18:28'),
(76,48,'General','',0,1,'synced',NULL,'2026-07-07 08:18:29',0,136,'2026-05-29 01:35:54','2026-07-07 08:18:29'),
(77,49,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:29',0,136,'2026-05-29 01:35:54','2026-07-07 08:18:29'),
(78,50,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:29',0,136,'2026-05-29 01:35:54','2026-07-07 08:18:29'),
(79,51,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:29',0,136,'2026-05-29 01:35:54','2026-07-07 08:18:29'),
(80,52,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:29',0,136,'2026-05-29 01:35:54','2026-07-07 08:18:29'),
(81,53,'General','',0,1,'synced',NULL,'2026-07-07 08:18:30',0,137,'2026-05-29 06:26:12','2026-07-07 08:18:30'),
(82,54,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:30',0,137,'2026-05-29 06:26:12','2026-07-07 08:18:30'),
(83,55,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:30',0,137,'2026-05-29 06:26:12','2026-07-07 08:18:30'),
(84,56,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:30',0,137,'2026-05-29 06:26:12','2026-07-07 08:18:30'),
(85,57,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:30',0,137,'2026-05-29 06:26:12','2026-07-07 08:18:30'),
(86,58,'General','',0,1,'synced',NULL,'2026-07-07 08:18:30',0,138,'2026-05-29 06:26:13','2026-07-07 08:18:30'),
(87,59,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:30',0,138,'2026-05-29 06:26:13','2026-07-07 08:18:30'),
(88,60,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:30',0,138,'2026-05-29 06:26:13','2026-07-07 08:18:30'),
(89,61,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:30',0,138,'2026-05-29 06:26:13','2026-07-07 08:18:30'),
(90,62,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:30',0,138,'2026-05-29 06:26:13','2026-07-07 08:18:30'),
(91,NULL,'Introduction aux notions cles',NULL,0,1,'pending','create',NULL,1,139,'2026-05-29 16:14:08','2026-05-29 16:14:08'),
(92,NULL,'introduction aux theories abstraites',NULL,0,1,'pending','create',NULL,1,141,'2026-05-29 20:00:54','2026-05-29 20:00:54'),
(93,63,'General','',0,1,'synced',NULL,'2026-07-07 08:18:32',0,139,'2026-05-29 20:05:55','2026-07-07 08:18:32'),
(94,64,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:32',0,139,'2026-05-29 20:05:55','2026-07-07 08:18:32'),
(95,65,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:32',0,139,'2026-05-29 20:05:55','2026-07-07 08:18:32'),
(96,66,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:32',0,139,'2026-05-29 20:05:55','2026-07-07 08:18:32'),
(97,67,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:32',0,139,'2026-05-29 20:05:55','2026-07-07 08:18:32'),
(98,68,'General','',0,1,'synced',NULL,'2026-07-07 08:18:32',0,140,'2026-05-30 06:46:37','2026-07-07 08:18:32'),
(99,69,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:32',0,140,'2026-05-30 06:46:37','2026-07-07 08:18:32'),
(100,70,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:32',0,140,'2026-05-30 06:46:37','2026-07-07 08:18:32'),
(101,71,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:32',0,140,'2026-05-30 06:46:37','2026-07-07 08:18:32'),
(102,72,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:32',0,140,'2026-05-30 06:46:37','2026-07-07 08:18:32'),
(103,73,'General','',0,1,'synced',NULL,'2026-07-07 08:18:33',0,141,'2026-05-30 06:46:37','2026-07-07 08:18:33'),
(104,74,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:33',0,141,'2026-05-30 06:46:37','2026-07-07 08:18:33'),
(105,75,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:33',0,141,'2026-05-30 06:46:37','2026-07-07 08:18:33'),
(106,76,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:33',0,141,'2026-05-30 06:46:37','2026-07-07 08:18:33'),
(107,77,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:33',0,141,'2026-05-30 06:46:37','2026-07-07 08:18:33'),
(108,NULL,'a',NULL,5,1,'pending','create',NULL,1,133,'2026-05-30 07:23:21','2026-05-30 07:23:21'),
(109,NULL,'n',NULL,0,1,'synced','create','2026-05-30 09:06:07',0,143,'2026-05-30 09:05:50','2026-05-30 09:06:07'),
(110,78,'General','',0,1,'synced',NULL,'2026-07-07 08:18:34',0,142,'2026-05-30 09:06:28','2026-07-07 08:18:34'),
(111,79,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:34',0,142,'2026-05-30 09:06:28','2026-07-07 08:18:34'),
(112,80,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:34',0,142,'2026-05-30 09:06:28','2026-07-07 08:18:34'),
(113,81,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:34',0,142,'2026-05-30 09:06:28','2026-07-07 08:18:34'),
(114,82,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:34',0,142,'2026-05-30 09:06:28','2026-07-07 08:18:34'),
(115,83,'General','',0,1,'synced',NULL,'2026-07-07 08:18:35',0,143,'2026-05-30 09:06:28','2026-07-07 08:18:35'),
(116,84,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:35',0,143,'2026-05-30 09:06:28','2026-07-07 08:18:35'),
(117,85,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:35',0,143,'2026-05-30 09:06:28','2026-07-07 08:18:35'),
(118,86,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:35',0,143,'2026-05-30 09:06:28','2026-07-07 08:18:35'),
(119,87,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:35',0,143,'2026-05-30 09:06:28','2026-07-07 08:18:35'),
(120,88,'General','',0,1,'synced',NULL,'2026-07-07 08:18:35',0,144,'2026-05-30 10:09:08','2026-07-07 08:18:35'),
(121,89,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:35',0,144,'2026-05-30 10:09:08','2026-07-07 08:18:35'),
(122,90,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:35',0,144,'2026-05-30 10:09:08','2026-07-07 08:18:35'),
(123,91,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:35',0,144,'2026-05-30 10:09:08','2026-07-07 08:18:35'),
(124,92,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:35',0,144,'2026-05-30 10:09:08','2026-07-07 08:18:35'),
(125,93,'General','',0,1,'synced',NULL,'2026-07-07 08:18:36',0,145,'2026-05-30 10:09:09','2026-07-07 08:18:36'),
(126,94,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:36',0,145,'2026-05-30 10:09:09','2026-07-07 08:18:36'),
(127,95,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:36',0,145,'2026-05-30 10:09:09','2026-07-07 08:18:36'),
(128,96,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:36',0,145,'2026-05-30 10:09:09','2026-07-07 08:18:36'),
(129,97,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:36',0,145,'2026-05-30 10:09:09','2026-07-07 08:18:36'),
(130,98,'General','',0,1,'synced',NULL,'2026-07-07 08:18:37',0,147,'2026-05-30 10:12:23','2026-07-07 08:18:37'),
(131,99,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:37',0,147,'2026-05-30 10:12:23','2026-07-07 08:18:37'),
(132,100,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:37',0,147,'2026-05-30 10:12:23','2026-07-07 08:18:37'),
(133,101,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:37',0,147,'2026-05-30 10:12:23','2026-07-07 08:18:37'),
(134,102,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:37',0,147,'2026-05-30 10:12:23','2026-07-07 08:18:37'),
(135,103,'General','',0,1,'synced',NULL,'2026-07-07 08:18:37',0,148,'2026-05-30 14:58:43','2026-07-07 08:18:37'),
(136,104,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:37',0,148,'2026-05-30 14:58:43','2026-07-07 08:18:37'),
(137,105,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:37',0,148,'2026-05-30 14:58:43','2026-07-07 08:18:37'),
(138,106,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:37',0,148,'2026-05-30 14:58:43','2026-07-07 08:18:37'),
(139,107,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:37',0,148,'2026-05-30 14:58:43','2026-07-07 08:18:37'),
(140,NULL,'intro',NULL,0,1,'pending','create',NULL,1,150,'2026-05-30 15:56:11','2026-05-30 15:56:11'),
(141,NULL,'intro',NULL,0,1,'pending','create',NULL,1,151,'2026-05-30 16:13:25','2026-05-30 16:13:25'),
(142,108,'General','',0,1,'synced',NULL,'2026-07-07 08:18:38',0,149,'2026-05-30 16:23:26','2026-07-07 08:18:38'),
(143,109,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:38',0,149,'2026-05-30 16:23:26','2026-07-07 08:18:38'),
(144,110,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:38',0,149,'2026-05-30 16:23:26','2026-07-07 08:18:38'),
(145,111,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:38',0,149,'2026-05-30 16:23:26','2026-07-07 08:18:38'),
(146,112,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:38',0,149,'2026-05-30 16:23:27','2026-07-07 08:18:38'),
(147,113,'New section','',5,1,'synced',NULL,'2026-07-07 08:18:38',0,149,'2026-05-30 16:23:27','2026-07-07 08:18:38'),
(148,114,'General','',0,1,'synced',NULL,'2026-07-07 08:18:39',0,150,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(149,115,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:39',0,150,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(150,116,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:39',0,150,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(151,117,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:39',0,150,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(152,118,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:39',0,150,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(153,119,'General','',0,1,'synced',NULL,'2026-07-07 08:18:39',0,151,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(154,120,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:39',0,151,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(155,121,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:39',0,151,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(156,122,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:39',0,151,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(157,123,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:39',0,151,'2026-05-30 16:23:27','2026-07-07 08:18:39'),
(158,124,'General','',0,1,'synced',NULL,'2026-07-07 08:18:40',0,152,'2026-05-30 16:23:27','2026-07-07 08:18:40'),
(159,125,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:40',0,152,'2026-05-30 16:23:27','2026-07-07 08:18:40'),
(160,126,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:40',0,152,'2026-05-30 16:23:27','2026-07-07 08:18:40'),
(161,127,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:40',0,152,'2026-05-30 16:23:27','2026-07-07 08:18:40'),
(162,128,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:40',0,152,'2026-05-30 16:23:27','2026-07-07 08:18:40'),
(163,NULL,'a',NULL,5,1,'pending',NULL,NULL,1,136,'2026-05-30 16:29:41','2026-05-30 16:29:41'),
(164,NULL,'introduction',NULL,0,1,'pending','create',NULL,1,155,'2026-07-02 08:04:47','2026-07-02 08:04:47'),
(165,NULL,'presentation d\'un modele oriente',NULL,0,1,'pending','update',NULL,1,157,'2026-07-02 11:37:36','2026-07-02 11:37:48'),
(167,129,'General','',0,1,'synced',NULL,'2026-07-07 08:18:41',0,153,'2026-07-03 06:48:43','2026-07-07 08:18:41'),
(168,130,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:41',0,153,'2026-07-03 06:48:43','2026-07-07 08:18:41'),
(169,131,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:41',0,153,'2026-07-03 06:48:43','2026-07-07 08:18:41'),
(170,132,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:41',0,153,'2026-07-03 06:48:43','2026-07-07 08:18:41'),
(171,133,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:41',0,153,'2026-07-03 06:48:43','2026-07-07 08:18:41'),
(172,NULL,'introduction test 1',NULL,0,1,'pending','create',NULL,1,160,'2026-07-03 06:54:08','2026-07-03 06:54:08'),
(173,134,'General','',0,1,'synced',NULL,'2026-07-07 08:18:41',0,160,'2026-07-03 06:55:32','2026-07-07 08:18:41'),
(174,135,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:42',0,160,'2026-07-03 06:55:32','2026-07-07 08:18:42'),
(175,136,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:42',0,160,'2026-07-03 06:55:32','2026-07-07 08:18:42'),
(176,137,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:42',0,160,'2026-07-03 06:55:32','2026-07-07 08:18:42'),
(177,138,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:42',0,160,'2026-07-03 06:55:32','2026-07-07 08:18:42'),
(178,139,'General','',0,1,'synced',NULL,'2026-07-07 08:18:42',0,161,'2026-07-03 07:32:36','2026-07-07 08:18:42'),
(179,140,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:42',0,161,'2026-07-03 07:32:36','2026-07-07 08:18:42'),
(180,141,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:42',0,161,'2026-07-03 07:32:36','2026-07-07 08:18:42'),
(181,142,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:42',0,161,'2026-07-03 07:32:36','2026-07-07 08:18:42'),
(182,143,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:42',0,161,'2026-07-03 07:32:36','2026-07-07 08:18:42'),
(183,144,'General','',0,1,'synced',NULL,'2026-07-07 08:18:43',0,162,'2026-07-03 10:25:18','2026-07-07 08:18:43'),
(184,145,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:43',0,162,'2026-07-03 10:25:18','2026-07-07 08:18:43'),
(185,146,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:43',0,162,'2026-07-03 10:25:18','2026-07-07 08:18:43'),
(186,147,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:43',0,162,'2026-07-03 10:25:18','2026-07-07 08:18:43'),
(187,148,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:43',0,162,'2026-07-03 10:25:18','2026-07-07 08:18:43'),
(188,NULL,'intro',NULL,0,1,'pending','create',NULL,1,163,'2026-07-03 11:50:34','2026-07-03 11:50:34'),
(189,NULL,'intro',NULL,0,1,'pending','create',NULL,1,164,'2026-07-03 12:04:34','2026-07-03 12:04:34'),
(190,149,'General','',0,1,'synced',NULL,'2026-07-07 08:18:44',0,163,'2026-07-03 12:23:34','2026-07-07 08:18:44'),
(191,150,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:44',0,163,'2026-07-03 12:23:34','2026-07-07 08:18:44'),
(192,151,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:44',0,163,'2026-07-03 12:23:34','2026-07-07 08:18:44'),
(193,152,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:44',0,163,'2026-07-03 12:23:34','2026-07-07 08:18:44'),
(194,153,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:44',0,163,'2026-07-03 12:23:34','2026-07-07 08:18:44'),
(195,NULL,'introduction au math',NULL,0,1,'pending','create',NULL,1,166,'2026-07-03 13:13:15','2026-07-03 13:13:15'),
(196,159,'General','',0,1,'synced',NULL,'2026-07-07 08:18:44',0,165,'2026-07-03 13:19:20','2026-07-07 08:18:44'),
(197,160,'New section','',1,1,'synced',NULL,'2026-07-07 08:18:44',0,165,'2026-07-03 13:19:20','2026-07-07 08:18:44'),
(198,161,'New section','',2,1,'synced',NULL,'2026-07-07 08:18:44',0,165,'2026-07-03 13:19:20','2026-07-07 08:18:44'),
(199,162,'New section','',3,1,'synced',NULL,'2026-07-07 08:18:44',0,165,'2026-07-03 13:19:20','2026-07-07 08:18:44'),
(200,163,'New section','',4,1,'synced',NULL,'2026-07-07 08:18:44',0,165,'2026-07-03 13:19:20','2026-07-07 08:18:44'),
(201,164,'General','',0,1,'pending',NULL,'2026-07-07 08:18:45',1,166,'2026-07-07 08:18:45','2026-07-07 08:18:45'),
(202,165,'New section','',1,1,'pending',NULL,'2026-07-07 08:18:45',1,166,'2026-07-07 08:18:45','2026-07-07 08:18:45'),
(203,166,'New section','',2,1,'pending',NULL,'2026-07-07 08:18:45',1,166,'2026-07-07 08:18:45','2026-07-07 08:18:45'),
(204,167,'New section','',3,1,'pending',NULL,'2026-07-07 08:18:45',1,166,'2026-07-07 08:18:45','2026-07-07 08:18:45'),
(205,168,'New section','',4,1,'pending',NULL,'2026-07-07 08:18:45',1,166,'2026-07-07 08:18:45','2026-07-07 08:18:45');
/*!40000 ALTER TABLE `sections` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES
('rfUwSCaExop2sTnDn3flpuHzNS6JtgNAHAfEhJKA',NULL,'127.0.0.1','Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Code/1.122.1 Chrome/142.0.7444.265 Electron/39.8.8 Safari/537.36','YTozOntzOjY6Il90b2tlbiI7czo0MDoiV2R6NmczVWVWRFowb2ZxbkFBdDd1QUVaeXZFZDM4Y2ZTQmJrd0RWViI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zeW5jL3BpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX19',1783415500),
('WNXuPRTa0Ivtx9dDeY6ql9K03U9coh2e4GhrXK7v',64,'127.0.0.1','Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:151.0) Gecko/20100101 Firefox/151.0','YTo1OntzOjY6Il90b2tlbiI7czo0MDoiZ1VEOUFQS1JEWlo2Q0Z1VGY0WHFQV1k5dzhZRmxOeWZrWkhUU0RJWSI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9zeW5jL3BpbmciO31zOjY6Il9mbGFzaCI7YToyOntzOjM6Im9sZCI7YTowOnt9czozOiJuZXciO2E6MDp7fX1zOjM6InVybCI7YTowOnt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6NjQ7fQ==',1783418781);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `submissions`
--

DROP TABLE IF EXISTS `submissions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `module_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'draft',
  `content` text DEFAULT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `grade` int(10) unsigned DEFAULT NULL,
  `graded_at` timestamp NULL DEFAULT NULL,
  `graded_by` bigint(20) unsigned DEFAULT NULL,
  `attempt_number` int(11) NOT NULL DEFAULT 0,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(4) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `submissions_user_id_foreign` (`user_id`),
  KEY `submissions_graded_by_index` (`graded_by`),
  KEY `submissions_module_id_foreign` (`module_id`),
  KEY `submissions_sync_status_index` (`sync_status`),
  KEY `submissions_dirty_index` (`dirty`),
  CONSTRAINT `submissions_module_id_foreign` FOREIGN KEY (`module_id`) REFERENCES `modules` (`id`) ON DELETE CASCADE,
  CONSTRAINT `submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `submissions`
--

LOCK TABLES `submissions` WRITE;
/*!40000 ALTER TABLE `submissions` DISABLE KEYS */;
INSERT INTO `submissions` VALUES
(14,37,63,'submitted','devoir rendu dans les temps','submissions/1780075312_comedie_adaptee(1)(1).pdf',NULL,NULL,NULL,0,'2026-05-29 16:21:52','pending',NULL,NULL,1,'2026-05-29 16:21:52','2026-05-29 16:21:52');
/*!40000 ALTER TABLE `submissions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sync_queue`
--

DROP TABLE IF EXISTS `sync_queue`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `sync_queue` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `operation` enum('CREATE','UPDATE','DELETE') NOT NULL,
  `entity_type` varchar(50) NOT NULL,
  `entity_id` bigint(20) unsigned NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `status` enum('pending','processing','done','error') NOT NULL DEFAULT 'pending',
  `processed_by` varchar(255) DEFAULT NULL,
  `locked_at` timestamp NULL DEFAULT NULL,
  `attempts` int(10) unsigned NOT NULL DEFAULT 0,
  `error_msg` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `sync_queue_entity_type_entity_id_operation_status_unique` (`entity_type`,`entity_id`,`operation`,`status`),
  KEY `sync_queue_status_created_at_index` (`status`,`created_at`),
  KEY `sync_queue_entity_type_entity_id_index` (`entity_type`,`entity_id`),
  KEY `sync_queue_operation_index` (`operation`),
  KEY `sync_queue_entity_type_index` (`entity_type`),
  KEY `sync_queue_entity_id_index` (`entity_id`),
  KEY `sync_queue_status_index` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=615 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sync_queue`
--

LOCK TABLES `sync_queue` WRITE;
/*!40000 ALTER TABLE `sync_queue` DISABLE KEYS */;
INSERT INTO `sync_queue` VALUES
(433,'CREATE','documents',36,'{\"filepath\":\"courses\\/documents\\/1782987700_sbom_minimum_elements_report_0.pdf\",\"mimetype\":\"application\\/pdf\",\"filesize\":634140,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/courses\\/documents\\/1782987700_sbom_minimum_elements_report_0.pdf\",\"course_id\":155,\"user_id\":61,\"filename\":\"desi\",\"status\":1,\"updated_at\":\"2026-07-02T10:21:40.000000Z\",\"created_at\":\"2026-07-02T10:21:40.000000Z\",\"id\":36}','error',NULL,NULL,1,'Cours non synchronisé, impossible d\'ajouter le document','2026-07-02 09:21:40',NULL),
(434,'CREATE','courses',157,'{\"fullname\":\"Ingenierie dirige par les modeles\",\"shortname\":\"idm\",\"summary\":\"description\",\"numsections\":\"4\",\"startdate\":\"2026-07-02T00:00:00.000000Z\",\"enddate\":\"2026-07-24T00:00:00.000000Z\",\"teacher_id\":58,\"category_id\":\"20\",\"image\":\"courses\\/images\\/FM18zjXctPXLr379pLDbf8CvvPZF9hczWX6cUCiE.png\",\"moodle_id\":null,\"visible\":false,\"idnumber\":\"idm\",\"format\":\"weeks\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T12:37:02.000000Z\",\"created_at\":\"2026-07-02T12:37:02.000000Z\",\"id\":157}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\moodle_exception (code: invalidtoken) - Jeton non valide (introuvable)','2026-07-02 11:37:02',NULL),
(436,'CREATE','sections',165,'{\"name\":\"presentation d\'un modele\",\"course_id\":157,\"summary\":null,\"position\":0,\"visible\":1,\"moodle_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T12:37:36.000000Z\",\"created_at\":\"2026-07-02T12:37:36.000000Z\",\"id\":165}','error',NULL,NULL,1,'Cours parente non synchronisée, impossible de créer la section','2026-07-02 11:37:36',NULL),
(438,'UPDATE','sections',165,'{\"name\":\"presentation d\'un modele oriente\",\"summary\":null,\"position\":0,\"visible\":1,\"old_values\":{\"name\":\"presentation d\'un modele\",\"summary\":null,\"position\":0,\"visible\":1}}','error',NULL,NULL,1,'Cours parente non synchronisée, impossible de mettre à jour la section','2026-07-02 11:37:48',NULL),
(439,'CREATE','sections',166,'{\"name\":\"introduction\",\"course_id\":157,\"summary\":null,\"position\":1,\"visible\":1,\"moodle_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T12:37:59.000000Z\",\"created_at\":\"2026-07-02T12:37:59.000000Z\",\"id\":166}','error',NULL,NULL,1,'CREATE de sections#166 impossible: entité introuvable en local','2026-07-02 11:37:59',NULL),
(441,'DELETE','sections',166,'{\"id\":166,\"moodle_id\":null,\"name\":\"introduction\",\"summary\":null,\"position\":1,\"visible\":1,\"sync_status\":\"pending\",\"sync_action\":\"delete\",\"synced_at\":null,\"dirty\":1,\"course_id\":157,\"created_at\":\"2026-07-02T12:37:59.000000Z\",\"updated_at\":\"2026-07-02T12:38:08.000000Z\"}','done',NULL,NULL,0,NULL,'2026-07-02 13:05:59','2026-07-02 13:39:04'),
(443,'CREATE','participants',253,'{\"course_id\":157,\"user_id\":\"59\",\"role\":\"ROLE_USER\",\"status\":1,\"enrolled_at\":\"2026-07-02T12:39:36.000000Z\",\"moodle_enrolment_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T12:39:36.000000Z\",\"created_at\":\"2026-07-02T12:39:36.000000Z\",\"id\":253}','error',NULL,NULL,1,'Cours non synchronisé, impossible d\'enrôler l\'utilisateur','2026-07-02 11:39:36',NULL),
(445,'CREATE','participants',254,'{\"course_id\":157,\"user_id\":\"60\",\"role\":\"ROLE_STUDENT\",\"status\":1,\"enrolled_at\":\"2026-07-02T12:39:45.000000Z\",\"moodle_enrolment_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T12:39:45.000000Z\",\"created_at\":\"2026-07-02T12:39:45.000000Z\",\"id\":254}','error',NULL,NULL,1,'Cours non synchronisé, impossible d\'enrôler l\'utilisateur','2026-07-02 11:39:45',NULL),
(447,'UPDATE','users',58,'{\"id\":58,\"moodle_id\":17,\"moodle_token\":\"6ac7e8510f6ea4dc54c32aee212638b6\",\"must_change_password\":false,\"name\":\"mason danielle\",\"email\":\"mason@email.test\",\"username\":\"mason\",\"email_verified_at\":null,\"created_at\":\"2026-05-25T13:52:11.000000Z\",\"updated_at\":\"2026-07-02T12:39:59.000000Z\",\"profile_picture\":\"profile_pictures\\/XZ8RhvrKtZVtCNpzS4A2Djo5GJw2KpB6ZiuauGN8.png\",\"sync_status\":\"pending\",\"sync_action\":null,\"synced_at\":\"2026-05-28 05:18:13\",\"dirty\":true}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\moodle_exception (code: invalidtoken) - Jeton non valide (introuvable)','2026-07-02 11:39:59',NULL),
(449,'CREATE','announcements',21,'{\"subject\":\"reunion urgente 1\",\"message\":\"message 1\",\"user_id\":58,\"status\":1,\"course_id\":157,\"published_at\":\"2026-07-02T12:41:11.000000Z\",\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T12:41:11.000000Z\",\"created_at\":\"2026-07-02T12:41:11.000000Z\",\"id\":21}','error',NULL,NULL,1,'Cours non synchronisé, impossible d\'ajouter l\'annonce','2026-07-02 11:41:11',NULL),
(450,'CREATE','announcements',22,'{\"subject\":\"reunion urgente 2\",\"message\":\"message 2\",\"user_id\":58,\"status\":1,\"course_id\":157,\"published_at\":\"2026-07-02T12:41:31.000000Z\",\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T12:41:31.000000Z\",\"created_at\":\"2026-07-02T12:41:31.000000Z\",\"id\":22}','error',NULL,NULL,1,'Cours non synchronisé, impossible d\'ajouter l\'annonce','2026-07-02 11:41:31',NULL),
(452,'CREATE','modules',55,'{\"name\":\"devoir 1\",\"modplural\":\"Devoirs\",\"downloadcontent\":false,\"modname\":\"assign\",\"section_id\":165,\"intro\":\"instructions\",\"activity\":\"description\",\"duedate\":\"2026-01-10T14:00:00.000000Z\",\"grade\":\"100\",\"pdf_filename\":\"1783001238_SBOM Framing Software Component Transparency 2024.pdf\",\"pdf_url\":\"\\/images\\/pdf\\/1783001238_SBOM Framing Software Component Transparency 2024.pdf\",\"file_path\":\"images\\/pdf\\/1783001238_SBOM Framing Software Component Transparency 2024.pdf\",\"updated_at\":\"2026-07-02T14:07:18.000000Z\",\"created_at\":\"2026-07-02T14:07:18.000000Z\",\"id\":55}','error',NULL,NULL,1,'Cours parente non synchronisé, impossible de créer le module','2026-07-02 13:07:18',NULL),
(453,'CREATE','courses',158,'{\"fullname\":\"tech co\",\"shortname\":\"tc\",\"summary\":\"description\",\"numsections\":\"4\",\"startdate\":\"2026-07-02T00:00:00.000000Z\",\"enddate\":\"2026-07-16T00:00:00.000000Z\",\"teacher_id\":58,\"category_id\":\"26\",\"image\":\"courses\\/images\\/zpGoyGHHMerHoTfPh8iW22vds1iTsEUupnLo3Kii.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":\"adda\",\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T15:36:48.000000Z\",\"created_at\":\"2026-07-02T15:36:48.000000Z\",\"id\":158}','error',NULL,NULL,1,'Catégorie parente non synchronisée, impossible de créer le cours','2026-07-02 14:36:48',NULL),
(455,'CREATE','courses',159,'{\"fullname\":\"asp juri\",\"shortname\":\"aj\",\"summary\":\"description 1\",\"numsections\":\"4\",\"startdate\":\"2026-07-02T00:00:00.000000Z\",\"enddate\":\"2026-07-22T00:00:00.000000Z\",\"teacher_id\":58,\"category_id\":\"17\",\"image\":\"courses\\/images\\/RwByyp9kEnub1XNHE1EVVhexXcFFHrdMGRNKXhn6.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":\"aj\",\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-02T15:39:25.000000Z\",\"created_at\":\"2026-07-02T15:39:25.000000Z\",\"id\":159}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\moodle_exception (code: invalidtoken) - Jeton non valide (introuvable)','2026-07-02 14:39:25',NULL),
(457,'CREATE','categories',28,'{\"name\":\"aaa\",\"parent_id\":null,\"idnumber\":\"aa\",\"description\":\"a\",\"descriptionformat\":1}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\required_capability_exception (code: nopermissions) - Désolé, vous n’avez actuellement pas les permissions requises pour effectuer ceci (Gérer les catégories)','2026-07-02 14:46:32',NULL),
(458,'CREATE','users',66,'{\"name\":\"jeremy renner\",\"username\":\"jeremy\",\"email\":\"jeremy@email.test\",\"moodle_id\":25,\"moodle_token\":\"37856b88a6b2ca69dee19e92ad53e48c\",\"must_change_password\":false,\"profile_picture\":\"images\\/default-profile-picture.png\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-02T15:48:20.471962Z\",\"dirty\":false,\"updated_at\":\"2026-07-02T15:48:20.000000Z\",\"created_at\":\"2026-07-02T15:48:20.000000Z\",\"id\":66}','done',NULL,NULL,0,NULL,'2026-07-02 14:48:21','2026-07-02 14:48:51'),
(459,'CREATE','categories',29,'{\"name\":\"droit\",\"parent_id\":null,\"idnumber\":\"d\",\"description\":\"d\",\"descriptionformat\":1}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\required_capability_exception (code: nopermissions) - Désolé, vous n’avez actuellement pas les permissions requises pour effectuer ceci (Gérer les catégories)','2026-07-02 14:50:44',NULL),
(460,'CREATE','users',67,'{\"name\":\"moise michel\",\"username\":\"moise\",\"email\":\"moise@email.test\",\"moodle_id\":26,\"moodle_token\":\"a0c926124e36a042779d5e6728ce586a\",\"must_change_password\":false,\"profile_picture\":\"images\\/default-profile-picture.png\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:39:35.620153Z\",\"dirty\":false,\"updated_at\":\"2026-07-03T07:39:36.000000Z\",\"created_at\":\"2026-07-03T07:39:36.000000Z\",\"id\":67}','done',NULL,NULL,0,NULL,'2026-07-03 06:39:36','2026-07-03 06:39:46'),
(461,'CREATE','categories',30,'{\"name\":\"categorie test synchro\",\"parent_id\":null,\"idnumber\":null,\"description\":null,\"descriptionformat\":1}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\required_capability_exception (code: nopermissions) - Désolé, vous n’avez actuellement pas les permissions requises pour effectuer ceci (Gérer les catégories)','2026-07-03 06:44:54',NULL),
(462,'CREATE','categories',31,'{\"name\":\"test synchro 2\",\"parent_id\":null,\"idnumber\":null,\"description\":null,\"descriptionformat\":1}','error',NULL,NULL,1,'Moodle Exception: webservice_access_exception (code: accessexception) - Access control exception','2026-07-03 06:48:26',NULL),
(463,'UPDATE','modules',27,'{\"id\":27,\"moodle_id\":16,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=16\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:30.738809Z\",\"dirty\":0,\"section_id\":54,\"assignment_id\":null,\"created_at\":\"2026-05-28T07:18:28.000000Z\",\"updated_at\":\"2026-07-03T07:48:30.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:30','2026-07-03 06:48:44'),
(464,'UPDATE','modules',28,'{\"id\":28,\"moodle_id\":17,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=17\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:31.447062Z\",\"dirty\":0,\"section_id\":60,\"assignment_id\":null,\"created_at\":\"2026-05-28T07:33:44.000000Z\",\"updated_at\":\"2026-07-03T07:48:31.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:31','2026-07-03 06:48:44'),
(465,'UPDATE','modules',29,'{\"id\":29,\"moodle_id\":18,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=18\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:32.117687Z\",\"dirty\":0,\"section_id\":66,\"assignment_id\":null,\"created_at\":\"2026-05-28T15:06:38.000000Z\",\"updated_at\":\"2026-07-03T07:48:32.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:32','2026-07-03 06:48:44'),
(466,'UPDATE','modules',30,'{\"id\":30,\"moodle_id\":19,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=19\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:32.219116Z\",\"dirty\":0,\"section_id\":71,\"assignment_id\":null,\"created_at\":\"2026-05-28T15:06:38.000000Z\",\"updated_at\":\"2026-07-03T07:48:32.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:32','2026-07-03 06:48:44'),
(467,'UPDATE','modules',31,'{\"id\":31,\"moodle_id\":20,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=20\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:32.896726Z\",\"dirty\":0,\"section_id\":76,\"assignment_id\":null,\"created_at\":\"2026-05-29T02:35:54.000000Z\",\"updated_at\":\"2026-07-03T07:48:32.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:32','2026-07-03 06:48:44'),
(468,'UPDATE','modules',33,'{\"id\":33,\"moodle_id\":21,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=21\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:33.548654Z\",\"dirty\":0,\"section_id\":81,\"assignment_id\":null,\"created_at\":\"2026-05-29T07:26:12.000000Z\",\"updated_at\":\"2026-07-03T07:48:33.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:33','2026-07-03 06:48:44'),
(469,'UPDATE','modules',34,'{\"id\":34,\"moodle_id\":22,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=22\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:34.254621Z\",\"dirty\":0,\"section_id\":86,\"assignment_id\":null,\"created_at\":\"2026-05-29T07:26:13.000000Z\",\"updated_at\":\"2026-07-03T07:48:34.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:34','2026-07-03 06:48:44'),
(470,'UPDATE','modules',41,'{\"id\":41,\"moodle_id\":23,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=23\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:34.914292Z\",\"dirty\":0,\"section_id\":93,\"assignment_id\":null,\"created_at\":\"2026-05-29T21:05:55.000000Z\",\"updated_at\":\"2026-07-03T07:48:34.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:34','2026-07-03 06:48:44'),
(471,'UPDATE','modules',42,'{\"id\":42,\"moodle_id\":24,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=24\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:35.581643Z\",\"dirty\":0,\"section_id\":98,\"assignment_id\":null,\"created_at\":\"2026-05-30T07:46:37.000000Z\",\"updated_at\":\"2026-07-03T07:48:35.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:35','2026-07-03 06:48:44'),
(472,'UPDATE','modules',43,'{\"id\":43,\"moodle_id\":25,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=25\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:36.264974Z\",\"dirty\":0,\"section_id\":103,\"assignment_id\":null,\"created_at\":\"2026-05-30T07:46:37.000000Z\",\"updated_at\":\"2026-07-03T07:48:36.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:36','2026-07-03 06:48:44'),
(473,'UPDATE','modules',44,'{\"id\":44,\"moodle_id\":26,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=26\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:36.933781Z\",\"dirty\":0,\"section_id\":110,\"assignment_id\":null,\"created_at\":\"2026-05-30T10:06:28.000000Z\",\"updated_at\":\"2026-07-03T07:48:36.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:36','2026-07-03 06:48:44'),
(474,'UPDATE','modules',45,'{\"id\":45,\"moodle_id\":27,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=27\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:37.598943Z\",\"dirty\":0,\"section_id\":115,\"assignment_id\":null,\"created_at\":\"2026-05-30T10:06:28.000000Z\",\"updated_at\":\"2026-07-03T07:48:37.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:37','2026-07-03 06:48:44'),
(475,'UPDATE','modules',46,'{\"id\":46,\"moodle_id\":28,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=28\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:38.247660Z\",\"dirty\":0,\"section_id\":120,\"assignment_id\":null,\"created_at\":\"2026-05-30T11:09:08.000000Z\",\"updated_at\":\"2026-07-03T07:48:38.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:38','2026-07-03 06:48:44'),
(476,'UPDATE','modules',47,'{\"id\":47,\"moodle_id\":29,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=29\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:38.905725Z\",\"dirty\":0,\"section_id\":125,\"assignment_id\":null,\"created_at\":\"2026-05-30T11:09:09.000000Z\",\"updated_at\":\"2026-07-03T07:48:38.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:38','2026-07-03 06:48:44'),
(477,'UPDATE','modules',48,'{\"id\":48,\"moodle_id\":30,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=30\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:39.574069Z\",\"dirty\":0,\"section_id\":130,\"assignment_id\":null,\"created_at\":\"2026-05-30T11:12:23.000000Z\",\"updated_at\":\"2026-07-03T07:48:39.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:39','2026-07-03 06:48:44'),
(478,'UPDATE','modules',49,'{\"id\":49,\"moodle_id\":31,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=31\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:40.226234Z\",\"dirty\":0,\"section_id\":135,\"assignment_id\":null,\"created_at\":\"2026-05-30T15:58:43.000000Z\",\"updated_at\":\"2026-07-03T07:48:40.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:40','2026-07-03 06:48:44'),
(479,'UPDATE','modules',51,'{\"id\":51,\"moodle_id\":32,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=32\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:40.886092Z\",\"dirty\":0,\"section_id\":142,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:26.000000Z\",\"updated_at\":\"2026-07-03T07:48:40.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:40','2026-07-03 06:48:44'),
(480,'UPDATE','modules',52,'{\"id\":52,\"moodle_id\":33,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=33\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:41.543723Z\",\"dirty\":0,\"section_id\":148,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:27.000000Z\",\"updated_at\":\"2026-07-03T07:48:41.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:41','2026-07-03 06:48:44'),
(481,'UPDATE','modules',53,'{\"id\":53,\"moodle_id\":34,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=34\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:42.205228Z\",\"dirty\":0,\"section_id\":153,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:27.000000Z\",\"updated_at\":\"2026-07-03T07:48:42.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:42','2026-07-03 06:48:44'),
(482,'UPDATE','modules',54,'{\"id\":54,\"moodle_id\":35,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=35\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T07:48:42.866222Z\",\"dirty\":0,\"section_id\":158,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:27.000000Z\",\"updated_at\":\"2026-07-03T07:48:42.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:42','2026-07-03 06:48:44'),
(483,'CREATE','sections',167,'{\"moodle_id\":129,\"course_id\":153,\"name\":\"General\",\"summary\":\"\",\"position\":0,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:48:43.506671Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:48:43.000000Z\",\"created_at\":\"2026-07-03T07:48:43.000000Z\",\"id\":167}','error',NULL,NULL,1,'Moodle Exception: webservice_access_exception (code: accessexception) - Access control exception','2026-07-03 06:48:43',NULL),
(484,'CREATE','modules',56,'{\"moodle_id\":36,\"section_id\":167,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=36\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:48:43.519195Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:48:43.000000Z\",\"created_at\":\"2026-07-03T07:48:43.000000Z\",\"id\":56}','done',NULL,NULL,0,NULL,'2026-07-03 06:48:43','2026-07-03 06:48:44'),
(485,'CREATE','sections',168,'{\"moodle_id\":130,\"course_id\":153,\"name\":\"New section\",\"summary\":\"\",\"position\":1,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:48:43.529021Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:48:43.000000Z\",\"created_at\":\"2026-07-03T07:48:43.000000Z\",\"id\":168}','error',NULL,NULL,1,'Moodle Exception: webservice_access_exception (code: accessexception) - Access control exception','2026-07-03 06:48:43',NULL),
(486,'CREATE','sections',169,'{\"moodle_id\":131,\"course_id\":153,\"name\":\"New section\",\"summary\":\"\",\"position\":2,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:48:43.538017Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:48:43.000000Z\",\"created_at\":\"2026-07-03T07:48:43.000000Z\",\"id\":169}','error',NULL,NULL,1,'Moodle Exception: webservice_access_exception (code: accessexception) - Access control exception','2026-07-03 06:48:43',NULL),
(487,'CREATE','sections',170,'{\"moodle_id\":132,\"course_id\":153,\"name\":\"New section\",\"summary\":\"\",\"position\":3,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:48:43.547819Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:48:43.000000Z\",\"created_at\":\"2026-07-03T07:48:43.000000Z\",\"id\":170}','error',NULL,NULL,1,'Moodle Exception: webservice_access_exception (code: accessexception) - Access control exception','2026-07-03 06:48:43',NULL),
(488,'CREATE','sections',171,'{\"moodle_id\":133,\"course_id\":153,\"name\":\"New section\",\"summary\":\"\",\"position\":4,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:48:43.556565Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:48:43.000000Z\",\"created_at\":\"2026-07-03T07:48:43.000000Z\",\"id\":171}','error',NULL,NULL,1,'Moodle Exception: webservice_access_exception (code: accessexception) - Access control exception','2026-07-03 06:48:43',NULL),
(489,'CREATE','categories',32,'{\"name\":\"test synchro 3\",\"parent_id\":null,\"idnumber\":null,\"description\":null,\"descriptionformat\":1}','done',NULL,NULL,0,NULL,'2026-07-03 06:50:52','2026-07-03 06:51:11'),
(490,'UPDATE','categories',32,'{\"id\":32,\"moodle_id\":13,\"parent_id\":null,\"name\":\"test synchro 3\",\"idnumber\":null,\"description\":null,\"descriptionformat\":1,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T07:51:11.000000Z\",\"dirty\":0,\"created_at\":\"2026-07-03T07:50:52.000000Z\",\"updated_at\":\"2026-07-03T07:51:11.000000Z\"}','done',NULL,NULL,0,NULL,'2026-07-03 06:51:11','2026-07-03 06:53:16'),
(491,'CREATE','courses',160,'{\"fullname\":\"cours test 1\",\"shortname\":\"ct11\",\"summary\":\"description\",\"numsections\":\"4\",\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-22T00:00:00.000000Z\",\"teacher_id\":67,\"category_id\":\"32\",\"image\":\"courses\\/images\\/SXF2ZoloNNZSozWZeDQSBPAtNZQqKcHWDbpDwNja.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T07:52:45.000000Z\",\"created_at\":\"2026-07-03T07:52:45.000000Z\",\"id\":160}','done',NULL,NULL,0,NULL,'2026-07-03 06:52:45','2026-07-03 06:53:16'),
(493,'UPDATE','courses',160,'{\"id\":160,\"moodle_id\":38,\"fullname\":\"cours test 1\",\"shortname\":\"ct11\",\"summary\":\"description\",\"numsections\":4,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T07:53:16.284918Z\",\"dirty\":0,\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-22T00:00:00.000000Z\",\"teacher_id\":67,\"created_at\":\"2026-07-03T07:52:45.000000Z\",\"updated_at\":\"2026-07-03T07:53:16.000000Z\",\"category_id\":32,\"image\":\"courses\\/images\\/SXF2ZoloNNZSozWZeDQSBPAtNZQqKcHWDbpDwNja.png\",\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":0,\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null}','done',NULL,NULL,0,NULL,'2026-07-03 06:53:16','2026-07-03 06:55:33'),
(494,'CREATE','sections',172,'{\"name\":\"introduction test 1\",\"course_id\":160,\"summary\":null,\"position\":0,\"visible\":1,\"moodle_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T07:54:08.000000Z\",\"created_at\":\"2026-07-03T07:54:08.000000Z\",\"id\":172}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 06:54:08',NULL),
(496,'CREATE','documents',37,'{\"filepath\":\"courses\\/documents\\/1783065299_API_DOCUMENTATION.md\",\"mimetype\":\"text\\/plain\",\"filesize\":50336,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/courses\\/documents\\/1783065299_API_DOCUMENTATION.md\",\"course_id\":160,\"user_id\":67,\"filename\":\"document test 1\",\"status\":1,\"updated_at\":\"2026-07-03T07:54:59.000000Z\",\"created_at\":\"2026-07-03T07:54:59.000000Z\",\"id\":37}','error',NULL,NULL,1,'Moodle Exception: dml_missing_record_exception (code: invalidrecord) - Can\'t find data record in database table external_functions.','2026-07-03 06:54:59',NULL),
(497,'CREATE','sections',173,'{\"moodle_id\":134,\"course_id\":160,\"name\":\"General\",\"summary\":\"\",\"position\":0,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:55:32.881622Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:55:32.000000Z\",\"created_at\":\"2026-07-03T07:55:32.000000Z\",\"id\":173}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 06:55:32',NULL),
(498,'CREATE','modules',57,'{\"moodle_id\":37,\"section_id\":173,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=37\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:55:32.907267Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:55:32.000000Z\",\"created_at\":\"2026-07-03T07:55:32.000000Z\",\"id\":57}','done',NULL,NULL,0,NULL,'2026-07-03 06:55:32','2026-07-03 06:55:33'),
(499,'CREATE','sections',174,'{\"moodle_id\":135,\"course_id\":160,\"name\":\"New section\",\"summary\":\"\",\"position\":1,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:55:32.922086Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:55:32.000000Z\",\"created_at\":\"2026-07-03T07:55:32.000000Z\",\"id\":174}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 06:55:32',NULL),
(500,'CREATE','sections',175,'{\"moodle_id\":136,\"course_id\":160,\"name\":\"New section\",\"summary\":\"\",\"position\":2,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:55:32.935641Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:55:32.000000Z\",\"created_at\":\"2026-07-03T07:55:32.000000Z\",\"id\":175}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 06:55:32',NULL),
(501,'CREATE','sections',176,'{\"moodle_id\":137,\"course_id\":160,\"name\":\"New section\",\"summary\":\"\",\"position\":3,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:55:32.950093Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:55:32.000000Z\",\"created_at\":\"2026-07-03T07:55:32.000000Z\",\"id\":176}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 06:55:32',NULL),
(502,'CREATE','sections',177,'{\"moodle_id\":138,\"course_id\":160,\"name\":\"New section\",\"summary\":\"\",\"position\":4,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T07:55:32.965624Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T07:55:32.000000Z\",\"created_at\":\"2026-07-03T07:55:32.000000Z\",\"id\":177}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 06:55:32',NULL),
(503,'CREATE','documents',38,'{\"filepath\":\"courses\\/documents\\/1783065413_sbom_minimum_elements_report_0.pdf\",\"mimetype\":\"application\\/pdf\",\"filesize\":634140,\"file_url\":\"http:\\/\\/127.0.0.1:8000\\/storage\\/courses\\/documents\\/1783065413_sbom_minimum_elements_report_0.pdf\",\"course_id\":160,\"user_id\":67,\"filename\":\"document test 2\",\"status\":1,\"updated_at\":\"2026-07-03T07:56:53.000000Z\",\"created_at\":\"2026-07-03T07:56:53.000000Z\",\"id\":38}','error',NULL,NULL,1,'Moodle Exception: dml_missing_record_exception (code: invalidrecord) - Can\'t find data record in database table external_functions.','2026-07-03 06:56:53',NULL),
(504,'CREATE','modules',58,'{\"name\":\"devoir test 1\",\"modplural\":\"Devoirs\",\"downloadcontent\":false,\"modname\":\"assign\",\"section_id\":173,\"intro\":\"instruction 1\",\"activity\":null,\"duedate\":\"2026-07-21T11:11:00.000000Z\",\"grade\":\"65\",\"pdf_filename\":\"1783066441_CARI_2026_paper_29 (1).pdf\",\"pdf_url\":\"\\/images\\/pdf\\/1783066441_CARI_2026_paper_29 (1).pdf\",\"file_path\":\"images\\/pdf\\/1783066441_CARI_2026_paper_29 (1).pdf\",\"updated_at\":\"2026-07-03T08:14:01.000000Z\",\"created_at\":\"2026-07-03T08:14:01.000000Z\",\"id\":58}','done',NULL,NULL,0,NULL,'2026-07-03 07:14:01','2026-07-03 07:15:54'),
(505,'CREATE','courses',161,'{\"fullname\":\"cours test 3\",\"shortname\":\"ct3\",\"summary\":null,\"numsections\":\"4\",\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-29T00:00:00.000000Z\",\"teacher_id\":67,\"category_id\":\"32\",\"image\":\"courses\\/images\\/ghD4Q5wjwLUtTc85tCeEQSm9RD8azEhTZoFw8Otc.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":\"ct3\",\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T08:30:24.000000Z\",\"created_at\":\"2026-07-03T08:30:24.000000Z\",\"id\":161}','done',NULL,NULL,0,NULL,'2026-07-03 07:30:24','2026-07-03 07:31:07'),
(507,'CREATE','documents',39,'{\"course_id\":161,\"filename\":\"document test 3\",\"filepath\":\"courses\\/documents\\/1783067443_CARI_2026_paper_29 (1).pdf\",\"filesize\":376531}','error',NULL,NULL,1,'Moodle Exception: dml_missing_record_exception (code: invalidrecord) - Can\'t find data record in database table external_functions.','2026-07-03 07:30:43',NULL),
(508,'UPDATE','courses',161,'{\"id\":161,\"moodle_id\":39,\"fullname\":\"cours test 3\",\"shortname\":\"ct3\",\"summary\":null,\"numsections\":4,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T08:31:07.309441Z\",\"dirty\":0,\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-29T00:00:00.000000Z\",\"teacher_id\":67,\"created_at\":\"2026-07-03T08:30:24.000000Z\",\"updated_at\":\"2026-07-03T08:31:07.000000Z\",\"category_id\":32,\"image\":\"courses\\/images\\/ghD4Q5wjwLUtTc85tCeEQSm9RD8azEhTZoFw8Otc.png\",\"visible\":true,\"idnumber\":\"ct3\",\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":0,\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null}','done',NULL,NULL,0,NULL,'2026-07-03 07:31:07','2026-07-03 07:32:37'),
(509,'CREATE','sections',178,'{\"moodle_id\":139,\"course_id\":161,\"name\":\"General\",\"summary\":\"\",\"position\":0,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T08:32:36.161571Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T08:32:36.000000Z\",\"created_at\":\"2026-07-03T08:32:36.000000Z\",\"id\":178}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 07:32:36',NULL),
(510,'CREATE','modules',59,'{\"moodle_id\":38,\"section_id\":178,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=38\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T08:32:36.175982Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T08:32:36.000000Z\",\"created_at\":\"2026-07-03T08:32:36.000000Z\",\"id\":59}','done',NULL,NULL,0,NULL,'2026-07-03 07:32:36','2026-07-03 07:32:37'),
(511,'CREATE','sections',179,'{\"moodle_id\":140,\"course_id\":161,\"name\":\"New section\",\"summary\":\"\",\"position\":1,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T08:32:36.190622Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T08:32:36.000000Z\",\"created_at\":\"2026-07-03T08:32:36.000000Z\",\"id\":179}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 07:32:36',NULL),
(512,'CREATE','sections',180,'{\"moodle_id\":141,\"course_id\":161,\"name\":\"New section\",\"summary\":\"\",\"position\":2,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T08:32:36.208751Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T08:32:36.000000Z\",\"created_at\":\"2026-07-03T08:32:36.000000Z\",\"id\":180}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 07:32:36',NULL),
(513,'CREATE','sections',181,'{\"moodle_id\":142,\"course_id\":161,\"name\":\"New section\",\"summary\":\"\",\"position\":3,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T08:32:36.227863Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T08:32:36.000000Z\",\"created_at\":\"2026-07-03T08:32:36.000000Z\",\"id\":181}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 07:32:36',NULL),
(514,'CREATE','sections',182,'{\"moodle_id\":143,\"course_id\":161,\"name\":\"New section\",\"summary\":\"\",\"position\":4,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T08:32:36.242075Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T08:32:36.000000Z\",\"created_at\":\"2026-07-03T08:32:36.000000Z\",\"id\":182}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 07:32:36',NULL),
(515,'CREATE','modules',60,'{\"moodle_id\":39,\"section_id\":86,\"name\":\"fichier\",\"modname\":\"resource\",\"modplural\":\"Files\",\"intro\":\"<div class=\\\"no-overflow\\\"><p>description<\\/p><\\/div>\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/39\\/fichier.png\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T09:39:13.253162Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T09:39:13.000000Z\",\"created_at\":\"2026-07-03T09:39:13.000000Z\",\"id\":60}','done',NULL,NULL,0,NULL,'2026-07-03 08:39:13','2026-07-03 08:39:17'),
(516,'CREATE','modules',61,'{\"moodle_id\":40,\"section_id\":86,\"name\":\"fichier 2\",\"modname\":\"resource\",\"modplural\":\"Files\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/40\\/fichier 2.png\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T09:41:36.725480Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T09:41:36.000000Z\",\"created_at\":\"2026-07-03T09:41:36.000000Z\",\"id\":61}','done',NULL,NULL,0,NULL,'2026-07-03 08:41:36','2026-07-03 08:41:40'),
(517,'UPDATE','courses',138,'{\"fullname\":\"reseaux mobiles\",\"shortname\":\"res-mobi\",\"summary\":\"<p>description 1<\\/p>\",\"numsections\":\"4\",\"startdate\":\"2026-05-29T00:00:00.000000Z\",\"enddate\":\"2027-09-29T00:00:00.000000Z\",\"teacher_id\":64,\"category_id\":\"22\",\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"old_values\":{\"fullname\":\"reseaux mobiles\",\"shortname\":\"res-mobi\",\"summary\":\"<p>description 1<\\/p>\",\"numsections\":4,\"startdate\":\"2026-05-29T23:00:00.000000Z\",\"enddate\":\"2027-09-29T23:00:00.000000Z\",\"teacher_id\":64,\"category_id\":22,\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":0,\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null}}','done',NULL,NULL,0,NULL,'2026-07-03 08:50:29','2026-07-03 08:50:50'),
(518,'CREATE','modules',62,'{\"moodle_id\":41,\"section_id\":86,\"name\":\"fichier 4\",\"modname\":\"resource\",\"modplural\":\"Files\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/41\\/d.png\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T10:08:28.426119Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T10:08:28.000000Z\",\"created_at\":\"2026-07-03T10:08:28.000000Z\",\"id\":62}','done',NULL,NULL,0,NULL,'2026-07-03 09:08:28','2026-07-03 09:08:35'),
(519,'CREATE','sections',183,'{\"moodle_id\":144,\"course_id\":162,\"name\":\"General\",\"summary\":\"\",\"position\":0,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T11:25:18.736823Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T11:25:18.000000Z\",\"created_at\":\"2026-07-03T11:25:18.000000Z\",\"id\":183}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 10:25:18',NULL),
(520,'CREATE','modules',63,'{\"moodle_id\":42,\"section_id\":183,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=42\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T11:25:18.748885Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T11:25:18.000000Z\",\"created_at\":\"2026-07-03T11:25:18.000000Z\",\"id\":63}','done',NULL,NULL,0,NULL,'2026-07-03 10:25:18','2026-07-03 10:25:19'),
(521,'CREATE','sections',184,'{\"moodle_id\":145,\"course_id\":162,\"name\":\"New section\",\"summary\":\"\",\"position\":1,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T11:25:18.760925Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T11:25:18.000000Z\",\"created_at\":\"2026-07-03T11:25:18.000000Z\",\"id\":184}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 10:25:18',NULL),
(522,'CREATE','sections',185,'{\"moodle_id\":146,\"course_id\":162,\"name\":\"New section\",\"summary\":\"\",\"position\":2,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T11:25:18.770414Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T11:25:18.000000Z\",\"created_at\":\"2026-07-03T11:25:18.000000Z\",\"id\":185}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 10:25:18',NULL),
(523,'CREATE','sections',186,'{\"moodle_id\":147,\"course_id\":162,\"name\":\"New section\",\"summary\":\"\",\"position\":3,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T11:25:18.780701Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T11:25:18.000000Z\",\"created_at\":\"2026-07-03T11:25:18.000000Z\",\"id\":186}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 10:25:18',NULL),
(524,'CREATE','sections',187,'{\"moodle_id\":148,\"course_id\":162,\"name\":\"New section\",\"summary\":\"\",\"position\":4,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T11:25:18.790059Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T11:25:18.000000Z\",\"created_at\":\"2026-07-03T11:25:18.000000Z\",\"id\":187}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 10:25:18',NULL),
(525,'UPDATE','users',67,'{\"id\":67,\"moodle_id\":26,\"moodle_token\":\"a0c926124e36a042779d5e6728ce586a\",\"must_change_password\":false,\"name\":\"moise michel\",\"email\":\"moise@email.test\",\"username\":\"moise\",\"email_verified_at\":null,\"created_at\":\"2026-07-03T07:39:36.000000Z\",\"updated_at\":\"2026-07-03T11:31:54.000000Z\",\"profile_picture\":\"images\\/default-profile-picture.png\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:31:54.702123Z\",\"dirty\":false}','error',NULL,NULL,1,'Moodle Exception: webservice_access_exception (code: accessexception) - Access control exception','2026-07-03 10:31:54',NULL),
(526,'CREATE','participants',572,'{\"course_id\":138,\"user_id\":\"59\",\"role\":\"ROLE_STUDENT\",\"status\":1,\"enrolled_at\":\"2026-07-03T11:32:38.000000Z\",\"moodle_enrolment_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T11:32:38.000000Z\",\"created_at\":\"2026-07-03T11:32:38.000000Z\",\"id\":572}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\moodle_exception (code: Message was not sent.) - error/Message was not sent.','2026-07-03 10:32:38',NULL),
(528,'UPDATE','users',59,'{\"id\":59,\"moodle_id\":18,\"moodle_token\":\"02280e562e72f749ba407646f5bfa1fe\",\"must_change_password\":false,\"name\":\"aurore divine\",\"email\":\"aurore@email.test\",\"username\":\"aurore\",\"email_verified_at\":null,\"created_at\":\"2026-05-25T14:04:25.000000Z\",\"updated_at\":\"2026-07-03T11:32:52.000000Z\",\"profile_picture\":\"images\\/default-profile-picture.png\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:32:51.941315Z\",\"dirty\":false}','processing',NULL,NULL,1,NULL,'2026-07-03 10:32:52',NULL),
(529,'UPDATE','modules',27,'{\"id\":27,\"moodle_id\":16,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=16\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:25.013645Z\",\"dirty\":0,\"section_id\":54,\"assignment_id\":null,\"created_at\":\"2026-05-28T07:18:28.000000Z\",\"updated_at\":\"2026-07-03T11:41:25.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-27-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 529)','2026-07-03 10:41:25',NULL),
(530,'UPDATE','modules',28,'{\"id\":28,\"moodle_id\":17,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=17\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:25.278167Z\",\"dirty\":0,\"section_id\":60,\"assignment_id\":null,\"created_at\":\"2026-05-28T07:33:44.000000Z\",\"updated_at\":\"2026-07-03T11:41:25.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-28-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 530)','2026-07-03 10:41:25',NULL),
(531,'UPDATE','modules',29,'{\"id\":29,\"moodle_id\":18,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=18\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:25.537725Z\",\"dirty\":0,\"section_id\":66,\"assignment_id\":null,\"created_at\":\"2026-05-28T15:06:38.000000Z\",\"updated_at\":\"2026-07-03T11:41:25.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-29-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 531)','2026-07-03 10:41:25',NULL),
(532,'UPDATE','modules',30,'{\"id\":30,\"moodle_id\":19,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=19\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:25.603988Z\",\"dirty\":0,\"section_id\":71,\"assignment_id\":null,\"created_at\":\"2026-05-28T15:06:38.000000Z\",\"updated_at\":\"2026-07-03T11:41:25.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-30-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 532)','2026-07-03 10:41:25',NULL),
(533,'UPDATE','modules',31,'{\"id\":31,\"moodle_id\":20,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=20\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:25.862478Z\",\"dirty\":0,\"section_id\":76,\"assignment_id\":null,\"created_at\":\"2026-05-29T02:35:54.000000Z\",\"updated_at\":\"2026-07-03T11:41:25.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-31-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 533)','2026-07-03 10:41:25',NULL),
(534,'UPDATE','modules',33,'{\"id\":33,\"moodle_id\":21,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=21\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:26.122652Z\",\"dirty\":0,\"section_id\":81,\"assignment_id\":null,\"created_at\":\"2026-05-29T07:26:12.000000Z\",\"updated_at\":\"2026-07-03T11:41:26.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-33-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 534)','2026-07-03 10:41:26',NULL),
(535,'UPDATE','modules',34,'{\"id\":34,\"moodle_id\":22,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=22\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:26.380388Z\",\"dirty\":0,\"section_id\":86,\"assignment_id\":null,\"created_at\":\"2026-05-29T07:26:13.000000Z\",\"updated_at\":\"2026-07-03T11:41:26.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-34-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 535)','2026-07-03 10:41:26',NULL),
(536,'UPDATE','modules',60,'{\"id\":60,\"moodle_id\":39,\"name\":\"fichier\",\"modname\":\"resource\",\"modplural\":\"Files\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/39\\/fichier.png\",\"moodle_file_url\":\"http:\\/\\/localhost\\/webservice\\/pluginfile.php\\/138\\/mod_resource\\/content\\/1\\/fichier.png?forcedownload=1\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:26.395090Z\",\"dirty\":0,\"section_id\":86,\"assignment_id\":null,\"created_at\":\"2026-07-03T09:39:13.000000Z\",\"updated_at\":\"2026-07-03T11:41:26.000000Z\",\"intro\":\"<div class=\\\"no-overflow\\\"><p>description<\\/p><\\/div>\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:26','2026-07-03 10:41:31'),
(537,'UPDATE','modules',61,'{\"id\":61,\"moodle_id\":40,\"name\":\"fichier 2\",\"modname\":\"resource\",\"modplural\":\"Files\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/40\\/fichier 2.png\",\"moodle_file_url\":\"http:\\/\\/localhost\\/webservice\\/pluginfile.php\\/139\\/mod_resource\\/content\\/1\\/fichier%202.png?forcedownload=1\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:26.408485Z\",\"dirty\":0,\"section_id\":86,\"assignment_id\":null,\"created_at\":\"2026-07-03T09:41:36.000000Z\",\"updated_at\":\"2026-07-03T11:41:26.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:26','2026-07-03 10:41:31'),
(538,'UPDATE','modules',62,'{\"id\":62,\"moodle_id\":41,\"name\":\"fichier 4\",\"modname\":\"resource\",\"modplural\":\"Files\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/41\\/d.png\",\"moodle_file_url\":\"http:\\/\\/localhost\\/webservice\\/pluginfile.php\\/140\\/mod_resource\\/content\\/1\\/d.png?forcedownload=1\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:26.418957Z\",\"dirty\":0,\"section_id\":86,\"assignment_id\":null,\"created_at\":\"2026-07-03T10:08:28.000000Z\",\"updated_at\":\"2026-07-03T11:41:26.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:26','2026-07-03 10:41:31'),
(539,'CREATE','modules',64,'{\"moodle_id\":43,\"section_id\":86,\"name\":\"fichier 5\",\"modname\":\"resource\",\"modplural\":\"Files\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/43\\/cover 4.png\",\"moodle_file_url\":\"http:\\/\\/localhost\\/webservice\\/pluginfile.php\\/143\\/mod_resource\\/content\\/1\\/cover%204.png?forcedownload=1\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T11:41:26.435116Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T11:41:26.000000Z\",\"created_at\":\"2026-07-03T11:41:26.000000Z\",\"id\":64}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:26','2026-07-03 10:41:31'),
(540,'UPDATE','modules',41,'{\"id\":41,\"moodle_id\":23,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=23\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:26.870209Z\",\"dirty\":0,\"section_id\":93,\"assignment_id\":null,\"created_at\":\"2026-05-29T21:05:55.000000Z\",\"updated_at\":\"2026-07-03T11:41:26.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-41-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 540)','2026-07-03 10:41:26',NULL),
(541,'UPDATE','modules',42,'{\"id\":42,\"moodle_id\":24,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=24\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:27.126982Z\",\"dirty\":0,\"section_id\":98,\"assignment_id\":null,\"created_at\":\"2026-05-30T07:46:37.000000Z\",\"updated_at\":\"2026-07-03T11:41:27.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-42-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 541)','2026-07-03 10:41:27',NULL),
(542,'UPDATE','modules',43,'{\"id\":43,\"moodle_id\":25,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=25\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:27.383971Z\",\"dirty\":0,\"section_id\":103,\"assignment_id\":null,\"created_at\":\"2026-05-30T07:46:37.000000Z\",\"updated_at\":\"2026-07-03T11:41:27.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-43-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 542)','2026-07-03 10:41:27',NULL),
(543,'UPDATE','modules',44,'{\"id\":44,\"moodle_id\":26,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=26\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:27.649436Z\",\"dirty\":0,\"section_id\":110,\"assignment_id\":null,\"created_at\":\"2026-05-30T10:06:28.000000Z\",\"updated_at\":\"2026-07-03T11:41:27.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-44-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 543)','2026-07-03 10:41:27',NULL),
(544,'UPDATE','modules',45,'{\"id\":45,\"moodle_id\":27,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=27\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:27.924011Z\",\"dirty\":0,\"section_id\":115,\"assignment_id\":null,\"created_at\":\"2026-05-30T10:06:28.000000Z\",\"updated_at\":\"2026-07-03T11:41:27.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-45-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 544)','2026-07-03 10:41:27',NULL),
(545,'UPDATE','modules',46,'{\"id\":46,\"moodle_id\":28,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=28\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:28.175123Z\",\"dirty\":0,\"section_id\":120,\"assignment_id\":null,\"created_at\":\"2026-05-30T11:09:08.000000Z\",\"updated_at\":\"2026-07-03T11:41:28.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-46-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 545)','2026-07-03 10:41:28',NULL),
(546,'UPDATE','modules',47,'{\"id\":47,\"moodle_id\":29,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=29\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:28.439791Z\",\"dirty\":0,\"section_id\":125,\"assignment_id\":null,\"created_at\":\"2026-05-30T11:09:09.000000Z\",\"updated_at\":\"2026-07-03T11:41:28.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-47-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 546)','2026-07-03 10:41:28',NULL),
(547,'UPDATE','modules',48,'{\"id\":48,\"moodle_id\":30,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=30\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:28.686342Z\",\"dirty\":0,\"section_id\":130,\"assignment_id\":null,\"created_at\":\"2026-05-30T11:12:23.000000Z\",\"updated_at\":\"2026-07-03T11:41:28.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-48-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 547)','2026-07-03 10:41:28',NULL),
(548,'UPDATE','modules',49,'{\"id\":49,\"moodle_id\":31,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=31\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:28.943216Z\",\"dirty\":0,\"section_id\":135,\"assignment_id\":null,\"created_at\":\"2026-05-30T15:58:43.000000Z\",\"updated_at\":\"2026-07-03T11:41:28.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-49-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 548)','2026-07-03 10:41:28',NULL),
(549,'UPDATE','modules',51,'{\"id\":51,\"moodle_id\":32,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=32\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:29.206032Z\",\"dirty\":0,\"section_id\":142,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:26.000000Z\",\"updated_at\":\"2026-07-03T11:41:29.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-51-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 549)','2026-07-03 10:41:29',NULL),
(550,'UPDATE','modules',52,'{\"id\":52,\"moodle_id\":33,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=33\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:29.455492Z\",\"dirty\":0,\"section_id\":148,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:27.000000Z\",\"updated_at\":\"2026-07-03T11:41:29.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-52-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 550)','2026-07-03 10:41:29',NULL),
(551,'UPDATE','modules',53,'{\"id\":53,\"moodle_id\":34,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=34\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:29.712219Z\",\"dirty\":0,\"section_id\":153,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:27.000000Z\",\"updated_at\":\"2026-07-03T11:41:29.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-53-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 551)','2026-07-03 10:41:29',NULL),
(552,'UPDATE','modules',54,'{\"id\":54,\"moodle_id\":35,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=35\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:29.969548Z\",\"dirty\":0,\"section_id\":158,\"assignment_id\":null,\"created_at\":\"2026-05-30T17:23:27.000000Z\",\"updated_at\":\"2026-07-03T11:41:29.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','error',NULL,NULL,1,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'modules-54-UPDATE-done\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = done, `processed_at` = 2026-07-03 11:41:31 where `id` = 552)','2026-07-03 10:41:29',NULL),
(553,'UPDATE','modules',56,'{\"id\":56,\"moodle_id\":36,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=36\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:30.229350Z\",\"dirty\":0,\"section_id\":167,\"assignment_id\":null,\"created_at\":\"2026-07-03T07:48:43.000000Z\",\"updated_at\":\"2026-07-03T11:41:30.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:30','2026-07-03 10:41:31'),
(554,'UPDATE','modules',57,'{\"id\":57,\"moodle_id\":37,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=37\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:30.493744Z\",\"dirty\":0,\"section_id\":173,\"assignment_id\":null,\"created_at\":\"2026-07-03T07:55:32.000000Z\",\"updated_at\":\"2026-07-03T11:41:30.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:30','2026-07-03 10:41:31'),
(555,'UPDATE','modules',59,'{\"id\":59,\"moodle_id\":38,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=38\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:30.745806Z\",\"dirty\":0,\"section_id\":178,\"assignment_id\":null,\"created_at\":\"2026-07-03T08:32:36.000000Z\",\"updated_at\":\"2026-07-03T11:41:30.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:30','2026-07-03 10:41:31'),
(556,'UPDATE','modules',63,'{\"id\":63,\"moodle_id\":42,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=42\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:30.994108Z\",\"dirty\":0,\"section_id\":183,\"assignment_id\":null,\"created_at\":\"2026-07-03T11:25:18.000000Z\",\"updated_at\":\"2026-07-03T11:41:30.000000Z\",\"intro\":\"\",\"activity\":null,\"duedate\":null,\"timeopen\":null,\"timeclose\":null,\"timelimit\":null,\"attempts\":1,\"grademethod\":0,\"shuffleanswers\":true,\"questionsperpage\":0,\"allowsubmissionsfromdate\":null,\"cutoffdate\":null,\"gradingduedate\":null,\"pdf_filename\":null,\"pdf_url\":null,\"maxattempts\":1,\"grade\":100}','done',NULL,NULL,0,NULL,'2026-07-03 10:41:30','2026-07-03 10:41:31'),
(557,'UPDATE','users',59,'{\"id\":59,\"moodle_id\":18,\"moodle_token\":\"02280e562e72f749ba407646f5bfa1fe\",\"must_change_password\":false,\"name\":\"aurore divine\",\"email\":\"aurore@email.test\",\"username\":\"aurore\",\"email_verified_at\":null,\"created_at\":\"2026-05-25T14:04:25.000000Z\",\"updated_at\":\"2026-07-03T11:41:56.000000Z\",\"profile_picture\":\"images\\/default-profile-picture.png\",\"sync_status\":\"synced\",\"sync_action\":null,\"synced_at\":\"2026-07-03T11:41:55.992502Z\",\"dirty\":false}','error',NULL,NULL,29,'SQLSTATE[23000]: Integrity constraint violation: 1062 Duplicate entry \'users-59-UPDATE-processing\' for key \'sync_queue_entity_type_entity_id_operation_status_unique\' (Connection: mysql, SQL: update `sync_queue` set `status` = processing where `id` = 557)','2026-07-03 10:41:56',NULL),
(558,'CREATE','users',68,'{\"name\":\"stella moffo\",\"username\":\"stella\",\"email\":\"stella@email.test\",\"moodle_id\":27,\"moodle_token\":\"49c882038a011cbd54d122b38b1e660f\",\"must_change_password\":false,\"profile_picture\":\"images\\/default-profile-picture.png\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T12:40:53.144403Z\",\"dirty\":false,\"updated_at\":\"2026-07-03T12:40:53.000000Z\",\"created_at\":\"2026-07-03T12:40:53.000000Z\",\"id\":68}','done',NULL,NULL,0,NULL,'2026-07-03 11:40:53','2026-07-03 12:21:07'),
(559,'CREATE','users',69,'{\"name\":\"valerie kono\",\"username\":\"valerie\",\"email\":\"valerie@email.test\",\"moodle_id\":28,\"moodle_token\":\"9d8389f36a7098b0194507c98b8de365\",\"must_change_password\":false,\"profile_picture\":\"images\\/default-profile-picture.png\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T12:41:09.638889Z\",\"dirty\":false,\"updated_at\":\"2026-07-03T12:41:09.000000Z\",\"created_at\":\"2026-07-03T12:41:09.000000Z\",\"id\":69}','done',NULL,NULL,0,NULL,'2026-07-03 11:41:09','2026-07-03 12:21:07'),
(560,'CREATE','categories',33,'{\"name\":\"test synchro 4\",\"parent_id\":null,\"idnumber\":null,\"description\":\"description\",\"descriptionformat\":1}','done',NULL,NULL,0,NULL,'2026-07-03 11:43:30','2026-07-03 12:21:07'),
(561,'CREATE','categories',34,'{\"name\":\"test synchro 5\",\"parent_id\":null,\"idnumber\":null,\"description\":null,\"descriptionformat\":1}','done',NULL,NULL,0,NULL,'2026-07-03 11:46:28','2026-07-03 12:21:08'),
(562,'CREATE','courses',163,'{\"fullname\":\"cours test 5\",\"shortname\":\"ct5\",\"summary\":null,\"numsections\":\"4\",\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-21T00:00:00.000000Z\",\"teacher_id\":68,\"category_id\":\"34\",\"image\":\"courses\\/images\\/FavhwnLGVJXai7ZvIyOZ5hyhcnGFc2KZJCCLYVXQ.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T12:47:35.000000Z\",\"created_at\":\"2026-07-03T12:47:35.000000Z\",\"id\":163}','done',NULL,NULL,0,NULL,'2026-07-03 11:47:35','2026-07-03 12:21:08'),
(564,'CREATE','sections',188,'{\"name\":\"intro\",\"course_id\":163,\"summary\":null,\"position\":0,\"visible\":1,\"moodle_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T12:50:34.000000Z\",\"created_at\":\"2026-07-03T12:50:34.000000Z\",\"id\":188}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 11:50:34',NULL),
(566,'CREATE','categories',35,'{\"name\":\"info\",\"parent_id\":null,\"idnumber\":null,\"description\":null,\"descriptionformat\":1}','done',NULL,NULL,0,NULL,'2026-07-03 12:03:27','2026-07-03 12:21:08'),
(567,'CREATE','courses',164,'{\"fullname\":\"c\",\"shortname\":\"c\",\"summary\":null,\"numsections\":\"4\",\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":null,\"teacher_id\":64,\"category_id\":\"17\",\"image\":\"courses\\/images\\/ZRCCxD1t8wq9lUfEEfjwQ6wgEm6ByczPYyqhPp4I.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T13:04:21.000000Z\",\"created_at\":\"2026-07-03T13:04:21.000000Z\",\"id\":164}','done',NULL,NULL,0,NULL,'2026-07-03 12:04:21','2026-07-03 12:21:08'),
(569,'CREATE','sections',189,'{\"name\":\"intro\",\"course_id\":164,\"summary\":null,\"position\":0,\"visible\":1,\"moodle_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T13:04:34.000000Z\",\"created_at\":\"2026-07-03T13:04:34.000000Z\",\"id\":189}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 12:04:34',NULL),
(571,'CREATE','modules',65,'{\"moodle_id\":44,\"section_id\":87,\"name\":\"a\",\"modname\":\"resource\",\"modplural\":\"Files\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"moodle_files\\/courses\\/23\\/modules\\/44\\/Gemini_Generated_Image_gb6kn9gb6kn9gb6k.png\",\"moodle_file_url\":\"http:\\/\\/http:\\/\\/localhost\\/webservice\\/pluginfile.php\\/146\\/mod_resource\\/content\\/1\\/Gemini_Generated_Image_gb6kn9gb6kn9gb6k.png?forcedownload=1\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T13:07:29.866059Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T13:07:29.000000Z\",\"created_at\":\"2026-07-03T13:07:29.000000Z\",\"id\":65}','done',NULL,NULL,0,NULL,'2026-07-03 12:07:29','2026-07-03 12:21:08'),
(572,'CREATE','participants',993,'{\"course_id\":138,\"user_id\":\"69\",\"role\":\"ROLE_STUDENT\",\"status\":1,\"enrolled_at\":\"2026-07-03T13:12:59.000000Z\",\"moodle_enrolment_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T13:12:59.000000Z\",\"created_at\":\"2026-07-03T13:12:59.000000Z\",\"id\":993}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\moodle_exception (code: Message was not sent.) - error/Message was not sent.','2026-07-03 12:12:59',NULL),
(574,'UPDATE','categories',33,'{\"id\":33,\"moodle_id\":14,\"parent_id\":null,\"name\":\"test synchro 4\",\"idnumber\":null,\"description\":\"description\",\"descriptionformat\":1,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T13:21:07.000000Z\",\"dirty\":0,\"created_at\":\"2026-07-03T12:43:30.000000Z\",\"updated_at\":\"2026-07-03T13:21:07.000000Z\"}','done',NULL,NULL,0,NULL,'2026-07-03 12:21:07','2026-07-03 12:23:35'),
(575,'UPDATE','categories',34,'{\"id\":34,\"moodle_id\":15,\"parent_id\":null,\"name\":\"test synchro 5\",\"idnumber\":null,\"description\":null,\"descriptionformat\":1,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T13:21:07.000000Z\",\"dirty\":0,\"created_at\":\"2026-07-03T12:46:28.000000Z\",\"updated_at\":\"2026-07-03T13:21:07.000000Z\"}','done',NULL,NULL,0,NULL,'2026-07-03 12:21:07','2026-07-03 12:23:35'),
(576,'UPDATE','courses',163,'{\"id\":163,\"moodle_id\":41,\"fullname\":\"cours test 5\",\"shortname\":\"ct5\",\"summary\":null,\"numsections\":4,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T13:21:08.055751Z\",\"dirty\":0,\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-21T00:00:00.000000Z\",\"teacher_id\":68,\"created_at\":\"2026-07-03T12:47:35.000000Z\",\"updated_at\":\"2026-07-03T13:21:08.000000Z\",\"category_id\":34,\"image\":\"courses\\/images\\/FavhwnLGVJXai7ZvIyOZ5hyhcnGFc2KZJCCLYVXQ.png\",\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":0,\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null}','done',NULL,NULL,0,NULL,'2026-07-03 12:21:08','2026-07-03 12:23:35'),
(577,'UPDATE','categories',35,'{\"id\":35,\"moodle_id\":16,\"parent_id\":null,\"name\":\"info\",\"idnumber\":null,\"description\":null,\"descriptionformat\":1,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T13:21:08.000000Z\",\"dirty\":0,\"created_at\":\"2026-07-03T13:03:27.000000Z\",\"updated_at\":\"2026-07-03T13:21:08.000000Z\"}','done',NULL,NULL,0,NULL,'2026-07-03 12:21:08','2026-07-03 12:23:35'),
(578,'UPDATE','courses',164,'{\"id\":164,\"moodle_id\":42,\"fullname\":\"c\",\"shortname\":\"c\",\"summary\":null,\"numsections\":4,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T13:21:08.230506Z\",\"dirty\":0,\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":null,\"teacher_id\":64,\"created_at\":\"2026-07-03T13:04:21.000000Z\",\"updated_at\":\"2026-07-03T13:21:08.000000Z\",\"category_id\":17,\"image\":\"courses\\/images\\/ZRCCxD1t8wq9lUfEEfjwQ6wgEm6ByczPYyqhPp4I.png\",\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":0,\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null}','done',NULL,NULL,0,NULL,'2026-07-03 12:21:08','2026-07-03 12:23:35'),
(579,'DELETE','categories',28,'{\"name\":\"aaa\"}','done',NULL,NULL,0,NULL,'2026-07-03 12:23:19','2026-07-03 12:23:35'),
(580,'CREATE','sections',190,'{\"moodle_id\":149,\"course_id\":163,\"name\":\"General\",\"summary\":\"\",\"position\":0,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T13:23:34.626346Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T13:23:34.000000Z\",\"created_at\":\"2026-07-03T13:23:34.000000Z\",\"id\":190}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 12:23:34',NULL),
(581,'CREATE','modules',66,'{\"moodle_id\":45,\"section_id\":190,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=45\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T13:23:34.640580Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T13:23:34.000000Z\",\"created_at\":\"2026-07-03T13:23:34.000000Z\",\"id\":66}','done',NULL,NULL,0,NULL,'2026-07-03 12:23:34','2026-07-03 12:23:35'),
(582,'CREATE','sections',191,'{\"moodle_id\":150,\"course_id\":163,\"name\":\"New section\",\"summary\":\"\",\"position\":1,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T13:23:34.654756Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T13:23:34.000000Z\",\"created_at\":\"2026-07-03T13:23:34.000000Z\",\"id\":191}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 12:23:34',NULL),
(583,'CREATE','sections',192,'{\"moodle_id\":151,\"course_id\":163,\"name\":\"New section\",\"summary\":\"\",\"position\":2,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T13:23:34.666827Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T13:23:34.000000Z\",\"created_at\":\"2026-07-03T13:23:34.000000Z\",\"id\":192}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 12:23:34',NULL),
(584,'CREATE','sections',193,'{\"moodle_id\":152,\"course_id\":163,\"name\":\"New section\",\"summary\":\"\",\"position\":3,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T13:23:34.679619Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T13:23:34.000000Z\",\"created_at\":\"2026-07-03T13:23:34.000000Z\",\"id\":193}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 12:23:34',NULL),
(585,'CREATE','sections',194,'{\"moodle_id\":153,\"course_id\":163,\"name\":\"New section\",\"summary\":\"\",\"position\":4,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T13:23:34.691675Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T13:23:34.000000Z\",\"created_at\":\"2026-07-03T13:23:34.000000Z\",\"id\":194}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 12:23:34',NULL),
(586,'CREATE','categories',36,'{\"name\":\"categorie test 5\",\"parent_id\":null,\"idnumber\":null,\"description\":null,\"descriptionformat\":1}','done',NULL,NULL,0,NULL,'2026-07-03 12:24:55','2026-07-03 12:25:17'),
(587,'UPDATE','categories',36,'{\"id\":36,\"moodle_id\":17,\"parent_id\":null,\"name\":\"categorie test 5\",\"idnumber\":null,\"description\":null,\"descriptionformat\":1,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T13:25:17.000000Z\",\"dirty\":0,\"created_at\":\"2026-07-03T13:24:55.000000Z\",\"updated_at\":\"2026-07-03T13:25:17.000000Z\"}','done',NULL,NULL,0,NULL,'2026-07-03 12:25:17','2026-07-03 12:26:36'),
(588,'CREATE','modules',67,'{\"name\":\"devoir 5\",\"modplural\":\"Devoirs\",\"downloadcontent\":false,\"modname\":\"assign\",\"section_id\":190,\"intro\":\"instructiions 5\",\"activity\":null,\"duedate\":\"2026-07-28T11:01:00.000000Z\",\"grade\":\"100\",\"pdf_filename\":\"1783085178_CARI_2026_paper_29 (1).pdf\",\"pdf_url\":\"\\/images\\/pdf\\/1783085178_CARI_2026_paper_29 (1).pdf\",\"file_path\":\"images\\/pdf\\/1783085178_CARI_2026_paper_29 (1).pdf\",\"updated_at\":\"2026-07-03T13:26:18.000000Z\",\"created_at\":\"2026-07-03T13:26:18.000000Z\",\"id\":67}','done',NULL,NULL,0,NULL,'2026-07-03 12:26:18','2026-07-03 12:26:36'),
(589,'CREATE','courses',165,'{\"fullname\":\"cours de test 6\",\"shortname\":\"ct6\",\"summary\":null,\"numsections\":\"4\",\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-21T00:00:00.000000Z\",\"teacher_id\":68,\"category_id\":\"36\",\"image\":\"courses\\/images\\/QJQQnawWYAPQfQDV0qXVpxduhOm5oCdAbENfCFxR.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T13:29:54.000000Z\",\"created_at\":\"2026-07-03T13:29:54.000000Z\",\"id\":165}','done',NULL,NULL,0,NULL,'2026-07-03 12:29:54','2026-07-03 12:30:12'),
(591,'UPDATE','courses',165,'{\"id\":165,\"moodle_id\":43,\"fullname\":\"cours de test 6\",\"shortname\":\"ct6\",\"summary\":null,\"numsections\":4,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T13:30:12.626956Z\",\"dirty\":0,\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-21T00:00:00.000000Z\",\"teacher_id\":68,\"created_at\":\"2026-07-03T13:29:54.000000Z\",\"updated_at\":\"2026-07-03T13:30:12.000000Z\",\"category_id\":36,\"image\":\"courses\\/images\\/QJQQnawWYAPQfQDV0qXVpxduhOm5oCdAbENfCFxR.png\",\"visible\":true,\"idnumber\":null,\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":0,\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null}','done',NULL,NULL,0,NULL,'2026-07-03 12:30:12','2026-07-03 13:19:21'),
(592,'CREATE','modules',68,'{\"name\":\"analyse numerique\",\"modplural\":\"Devoirs\",\"downloadcontent\":false,\"modname\":\"assign\",\"section_id\":190,\"intro\":\"faire le quiz\",\"activity\":null,\"duedate\":\"2026-07-12T12:00:00.000000Z\",\"grade\":\"60\",\"pdf_filename\":\"1783087175_sbom_minimum_elements_report_0.pdf\",\"pdf_url\":\"\\/images\\/pdf\\/1783087175_sbom_minimum_elements_report_0.pdf\",\"file_path\":\"images\\/pdf\\/1783087175_sbom_minimum_elements_report_0.pdf\",\"updated_at\":\"2026-07-03T13:59:35.000000Z\",\"created_at\":\"2026-07-03T13:59:35.000000Z\",\"id\":68}','done',NULL,NULL,0,NULL,'2026-07-03 12:59:35','2026-07-03 13:19:21'),
(593,'CREATE','courses',166,'{\"fullname\":\"mathemartique\",\"shortname\":\"math\",\"summary\":\"decription du cours\",\"numsections\":\"4\",\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-29T00:00:00.000000Z\",\"teacher_id\":64,\"category_id\":\"18\",\"image\":\"courses\\/images\\/WFbpWbRH9R5DhxdHuIFue8qwcwLYVCLNckKh2SE2.png\",\"moodle_id\":null,\"visible\":true,\"idnumber\":\"mat-001\",\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":\"0\",\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T14:11:30.000000Z\",\"created_at\":\"2026-07-03T14:11:30.000000Z\",\"id\":166}','done',NULL,NULL,0,NULL,'2026-07-03 13:11:30','2026-07-03 13:19:21'),
(595,'CREATE','participants',1100,'{\"course_id\":166,\"user_id\":\"69\",\"role\":\"ROLE_STUDENT\",\"status\":1,\"enrolled_at\":\"2026-07-03T14:11:50.000000Z\",\"moodle_enrolment_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T14:11:50.000000Z\",\"created_at\":\"2026-07-03T14:11:50.000000Z\",\"id\":1100}','done',NULL,NULL,0,NULL,'2026-07-03 13:11:50','2026-07-03 13:19:21'),
(597,'CREATE','documents',40,'{\"course_id\":166,\"filename\":\"chapitre 1\",\"filepath\":\"courses\\/documents\\/1783087938_CARI_2026_paper_29 (1).pdf\",\"filesize\":376531}','error',NULL,NULL,1,'Moodle Exception: dml_missing_record_exception (code: invalidrecord) - Can\'t find data record in database table external_functions.','2026-07-03 13:12:18',NULL),
(598,'CREATE','sections',195,'{\"name\":\"introduction au math\",\"course_id\":166,\"summary\":null,\"position\":0,\"visible\":1,\"moodle_id\":null,\"sync_status\":\"pending\",\"sync_action\":\"create\",\"dirty\":1,\"updated_at\":\"2026-07-03T14:13:15.000000Z\",\"created_at\":\"2026-07-03T14:13:15.000000Z\",\"id\":195}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 13:13:15',NULL),
(600,'CREATE','modules',69,'{\"name\":\"devoir 1\",\"modplural\":\"Devoirs\",\"downloadcontent\":false,\"modname\":\"assign\",\"section_id\":195,\"intro\":null,\"activity\":\"description\",\"duedate\":\"2026-07-28T12:00:00.000000Z\",\"grade\":\"20\",\"pdf_filename\":\"1783088044_CARI_2026_paper_29 (1).pdf\",\"pdf_url\":\"\\/images\\/pdf\\/1783088044_CARI_2026_paper_29 (1).pdf\",\"file_path\":\"images\\/pdf\\/1783088044_CARI_2026_paper_29 (1).pdf\",\"updated_at\":\"2026-07-03T14:14:04.000000Z\",\"created_at\":\"2026-07-03T14:14:04.000000Z\",\"id\":69}','done',NULL,NULL,0,NULL,'2026-07-03 13:14:04','2026-07-03 13:19:21'),
(601,'CREATE','modules',70,'{\"name\":\"quiz 1\",\"modname\":\"quiz\",\"modplural\":\"Quiz\",\"downloadcontent\":false,\"file_path\":\"\",\"section_id\":195,\"intro\":\"8 files changed\\r\\n+131\\r\\n-30\\r\\nKeep\\r\\nUndo\\r\\nCourseController.phpMoodle-client \\u2022 app\\/Http\\/Controllers\\r\\n+1\\r\\n-1\\r\\nModuleController.phpMoodle-client \\u2022 app\\/Http\\/Controllers\\r\\n+49\\r\\n-12\\r\\nModule.phpMoodle-client \\u2022 app\\/Models\\r\\n+2\\r\\n-0\\r\\nSyncService.phpMoodle-client \\u2022 app\\/Services\\r\\n+35\\r\\n-8\\r\\n2026_07_03_113545_add_moodle_file_url_to_modules_table.phpMoodle-client \\u2022 database\\/migrations\\r\\n+2\\r\\n-2\\r\\nshow.blade.phpMoodle-client \\u2022 resources\\/views\\/courses\\r\\n+12\\r\\n-5\\r\\nd\",\"grade\":\"20\",\"timeopen\":\"2026-07-20T11:01:00.000000Z\",\"timeclose\":\"2026-07-31T11:01:00.000000Z\",\"timelimit\":null,\"attempts\":\"0\",\"grademethod\":\"0\",\"shuffleanswers\":true,\"questionsperpage\":0,\"updated_at\":\"2026-07-03T14:14:56.000000Z\",\"created_at\":\"2026-07-03T14:14:56.000000Z\",\"id\":70}','done',NULL,NULL,0,NULL,'2026-07-03 13:14:56','2026-07-03 13:19:21'),
(602,'CREATE','sections',196,'{\"moodle_id\":159,\"course_id\":165,\"name\":\"General\",\"summary\":\"\",\"position\":0,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T14:19:20.517382Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T14:19:20.000000Z\",\"created_at\":\"2026-07-03T14:19:20.000000Z\",\"id\":196}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 13:19:20',NULL),
(603,'CREATE','modules',71,'{\"moodle_id\":47,\"section_id\":196,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=47\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T14:19:20.536219Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T14:19:20.000000Z\",\"created_at\":\"2026-07-03T14:19:20.000000Z\",\"id\":71}','done',NULL,NULL,0,NULL,'2026-07-03 13:19:20','2026-07-03 13:19:21'),
(604,'CREATE','sections',197,'{\"moodle_id\":160,\"course_id\":165,\"name\":\"New section\",\"summary\":\"\",\"position\":1,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T14:19:20.562475Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T14:19:20.000000Z\",\"created_at\":\"2026-07-03T14:19:20.000000Z\",\"id\":197}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 13:19:20',NULL),
(605,'CREATE','sections',198,'{\"moodle_id\":161,\"course_id\":165,\"name\":\"New section\",\"summary\":\"\",\"position\":2,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T14:19:20.575540Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T14:19:20.000000Z\",\"created_at\":\"2026-07-03T14:19:20.000000Z\",\"id\":198}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 13:19:20',NULL),
(606,'CREATE','sections',199,'{\"moodle_id\":162,\"course_id\":165,\"name\":\"New section\",\"summary\":\"\",\"position\":3,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T14:19:20.586974Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T14:19:20.000000Z\",\"created_at\":\"2026-07-03T14:19:20.000000Z\",\"id\":199}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 13:19:20',NULL),
(607,'CREATE','sections',200,'{\"moodle_id\":163,\"course_id\":165,\"name\":\"New section\",\"summary\":\"\",\"position\":4,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-03T14:19:20.600774Z\",\"dirty\":0,\"updated_at\":\"2026-07-03T14:19:20.000000Z\",\"created_at\":\"2026-07-03T14:19:20.000000Z\",\"id\":200}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-03 13:19:20',NULL),
(608,'UPDATE','courses',166,'{\"id\":166,\"moodle_id\":44,\"fullname\":\"mathemartique\",\"shortname\":\"math\",\"summary\":\"decription du cours\",\"numsections\":4,\"sync_status\":\"synced\",\"sync_action\":\"create\",\"synced_at\":\"2026-07-03T14:19:21.257140Z\",\"dirty\":0,\"startdate\":\"2026-07-03T00:00:00.000000Z\",\"enddate\":\"2026-07-29T00:00:00.000000Z\",\"teacher_id\":64,\"created_at\":\"2026-07-03T14:11:30.000000Z\",\"updated_at\":\"2026-07-03T14:19:21.000000Z\",\"category_id\":18,\"image\":\"courses\\/images\\/WFbpWbRH9R5DhxdHuIFue8qwcwLYVCLNckKh2SE2.png\",\"visible\":true,\"idnumber\":\"mat-001\",\"format\":\"topics\",\"hiddensections\":0,\"coursedisplay\":0,\"lang\":null,\"newsitems\":5,\"showgrades\":true,\"showreports\":false,\"showactivitydates\":true,\"maxbytes\":0,\"enablecompletion\":true,\"showcompletionconditions\":true,\"groupmode\":0,\"groupmodeforce\":false,\"defaultgroupingid\":0,\"tags\":null}','done',NULL,NULL,0,NULL,'2026-07-03 13:19:21','2026-07-07 08:18:46'),
(609,'CREATE','sections',201,'{\"moodle_id\":164,\"course_id\":166,\"name\":\"General\",\"summary\":\"\",\"position\":0,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-07T09:18:45.550697Z\",\"dirty\":0,\"updated_at\":\"2026-07-07T09:18:45.000000Z\",\"created_at\":\"2026-07-07T09:18:45.000000Z\",\"id\":201}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-07 08:18:45',NULL),
(610,'CREATE','modules',72,'{\"moodle_id\":48,\"section_id\":201,\"name\":\"Announcements\",\"modname\":\"forum\",\"modplural\":\"Forums\",\"intro\":\"\",\"position\":0,\"visible\":1,\"completion\":0,\"downloadcontent\":true,\"file_path\":\"http:\\/\\/localhost\\/mod\\/forum\\/view.php?id=48\",\"moodle_file_url\":\"\",\"sync_status\":\"synced\",\"synced_at\":\"2026-07-07T09:18:45.562005Z\",\"dirty\":0,\"updated_at\":\"2026-07-07T09:18:45.000000Z\",\"created_at\":\"2026-07-07T09:18:45.000000Z\",\"id\":72}','done',NULL,NULL,0,NULL,'2026-07-07 08:18:45','2026-07-07 08:18:46'),
(611,'CREATE','sections',202,'{\"moodle_id\":165,\"course_id\":166,\"name\":\"New section\",\"summary\":\"\",\"position\":1,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-07T09:18:45.572985Z\",\"dirty\":0,\"updated_at\":\"2026-07-07T09:18:45.000000Z\",\"created_at\":\"2026-07-07T09:18:45.000000Z\",\"id\":202}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-07 08:18:45',NULL),
(612,'CREATE','sections',203,'{\"moodle_id\":166,\"course_id\":166,\"name\":\"New section\",\"summary\":\"\",\"position\":2,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-07T09:18:45.590467Z\",\"dirty\":0,\"updated_at\":\"2026-07-07T09:18:45.000000Z\",\"created_at\":\"2026-07-07T09:18:45.000000Z\",\"id\":203}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-07 08:18:45',NULL),
(613,'CREATE','sections',204,'{\"moodle_id\":167,\"course_id\":166,\"name\":\"New section\",\"summary\":\"\",\"position\":3,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-07T09:18:45.608278Z\",\"dirty\":0,\"updated_at\":\"2026-07-07T09:18:45.000000Z\",\"created_at\":\"2026-07-07T09:18:45.000000Z\",\"id\":204}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-07 08:18:45',NULL),
(614,'CREATE','sections',205,'{\"moodle_id\":168,\"course_id\":166,\"name\":\"New section\",\"summary\":\"\",\"position\":4,\"visible\":1,\"sync_status\":\"synced\",\"synced_at\":\"2026-07-07T09:18:45.620978Z\",\"dirty\":0,\"updated_at\":\"2026-07-07T09:18:45.000000Z\",\"created_at\":\"2026-07-07T09:18:45.000000Z\",\"id\":205}','error',NULL,NULL,1,'Moodle Exception: core\\exception\\invalid_parameter_exception (code: invalidparameter) - Invalid parameter value detected','2026-07-07 08:18:45',NULL);
/*!40000 ALTER TABLE `sync_queue` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_competencies`
--

DROP TABLE IF EXISTS `user_competencies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `user_competencies` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL COMMENT 'Identifiant unique de Moodle',
  `user_id` bigint(20) unsigned NOT NULL,
  `competency_id` bigint(20) unsigned NOT NULL,
  `proficiency` int(11) NOT NULL DEFAULT 0 COMMENT '0=incomplete, 1=complete',
  `grade` decimal(5,2) DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_competencies_user_id_competency_id_unique` (`user_id`,`competency_id`),
  UNIQUE KEY `user_competencies_moodle_id_unique` (`moodle_id`),
  KEY `user_competencies_competency_id_foreign` (`competency_id`),
  KEY `user_competencies_moodle_id_index` (`moodle_id`),
  KEY `user_competencies_proficiency_index` (`proficiency`),
  CONSTRAINT `user_competencies_competency_id_foreign` FOREIGN KEY (`competency_id`) REFERENCES `competencies` (`id`) ON DELETE CASCADE,
  CONSTRAINT `user_competencies_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_competencies`
--

LOCK TABLES `user_competencies` WRITE;
/*!40000 ALTER TABLE `user_competencies` DISABLE KEYS */;
/*!40000 ALTER TABLE `user_competencies` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `moodle_id` int(11) DEFAULT NULL,
  `moodle_token` varchar(255) DEFAULT NULL,
  `must_change_password` tinyint(1) NOT NULL DEFAULT 1,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `sync_status` enum('synced','pending','conflict') NOT NULL DEFAULT 'pending',
  `sync_action` enum('create','update','delete') DEFAULT NULL,
  `synced_at` timestamp NULL DEFAULT NULL,
  `dirty` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_moodle_id_unique` (`moodle_id`),
  UNIQUE KEY `users_username_unique` (`username`),
  KEY `users_sync_status_index` (`sync_status`),
  KEY `users_dirty_index` (`dirty`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES
(58,17,'6ac7e8510f6ea4dc54c32aee212638b6',0,'mason danielle','mason@email.test','mason',NULL,'$2y$12$B84Gvq9Tji.Tgc6ZXYrJU.iVipdUuVIBS.UM7N2.DX95pInrEStf.',NULL,'2026-05-25 12:52:11','2026-07-02 11:39:59','profile_pictures/XZ8RhvrKtZVtCNpzS4A2Djo5GJw2KpB6ZiuauGN8.png','pending',NULL,'2026-05-28 04:18:13',1),
(59,18,'02280e562e72f749ba407646f5bfa1fe',0,'aurore divine','aurore@email.test','aurore',NULL,'$2y$12$2JGRDLIadrB/6aC3R1aleeWsKK8MS57MzW5foWZQcb3KiGmafEGAW',NULL,'2026-05-25 13:04:25','2026-07-07 08:18:39','images/default-profile-picture.png','synced',NULL,'2026-07-07 08:18:39',0),
(60,19,'46eb1b688137e2d1ffb7590b1a8a7ed3',0,'Ali Mahat','ali@email.test','ali',NULL,'$2y$12$TDLME31hOP2uUbiUpU07T.wiLZF7LZgDYxd6alinaB6.54NwlmNIC',NULL,'2026-05-28 04:20:41','2026-05-28 04:32:35','images/default-profile-picture.png','synced',NULL,'2026-05-28 04:32:35',0),
(61,20,'32e8e2661bd2df8067ce6d5ca3c7bf2d',0,'Kofi Jean','kofi@email.test','kofi',NULL,'$2y$12$flKT5nqEuQ5r9HjFsRb5OeWpwa2bYzRVP2mpXJPVtgvEj1cNsYZOC',NULL,'2026-05-28 04:33:21','2026-07-07 08:18:41','profile_pictures/yG7rldre1m6MTlR9auCeXMcOe2rZtaNZCV0jvEOM.png','synced',NULL,'2026-07-07 08:18:41',0),
(62,21,'318f2e53062491eeae8b31324e235057',0,'Audrey Cinthia','cinthia@email.test','cinthia',NULL,'$2y$12$fljLtqwzzaZfcu0pUIpk/evvKWLbRWsr7uYhWYlP8GYsvnUmAzFW6',NULL,'2026-05-29 06:05:56','2026-05-29 06:20:23','images/default-profile-picture.png','synced',NULL,'2026-05-29 06:20:23',0),
(63,22,'35142ae45d59d2ea0c3e2c517c19fd19',0,'amandine ful','amandine@email.test','amandine',NULL,'$2y$12$TCo3VVoXvD4sizMXHuDARew9Z4Y6cwQBwBMFDFkcqVNNXNVoQySfq',NULL,'2026-05-29 06:15:33','2026-07-07 08:18:27','images/default-profile-picture.png','synced',NULL,'2026-07-07 08:18:27',0),
(64,2,NULL,1,'lyon fokou','menomeloic2013@gmail.com',NULL,NULL,'$2y$12$wvOTK0fJwsqumt966x.HLuvX.S2oWsXKw0r.PIylvRHrHC3mrsdIC',NULL,'2026-05-29 06:26:14','2026-07-07 08:18:46',NULL,'synced',NULL,'2026-07-07 08:18:46',0),
(65,24,'154ee305b74fe80681755ef41b5a91b7',0,'jack kaffo','jack@email.test','jack',NULL,'$2y$12$pyKrQWgX31BpaHMl24U5EeW8NDFpxl4nPU4ZGQYMlDySwCqLK/tZe',NULL,'2026-05-29 16:10:26','2026-05-29 16:15:44','images/default-profile-picture.png','synced',NULL,'2026-05-29 16:15:44',0),
(66,25,'37856b88a6b2ca69dee19e92ad53e48c',0,'jeremy renner','jeremy@email.test','jeremy',NULL,'$2y$12$8FFWb8jftr2ngwUg4PUV.e2/VNvXc0XNhHApAXPDo.gbcINiRXhHO',NULL,'2026-07-02 14:48:20','2026-07-02 14:48:51','images/default-profile-picture.png','synced',NULL,'2026-07-02 14:48:51',0),
(67,26,'a0c926124e36a042779d5e6728ce586a',0,'moise michel','moise@email.test','moise',NULL,'$2y$12$hXasoYDxT5o.xC39qt4sbe4rjQ/4mD9E3y9.Zf34JxAzeP4T6iulu',NULL,'2026-07-03 06:39:36','2026-07-07 08:18:43','images/default-profile-picture.png','synced',NULL,'2026-07-07 08:18:43',0),
(68,27,'49c882038a011cbd54d122b38b1e660f',0,'stella moffo','stella@email.test','stella',NULL,'$2y$12$dlRYa.0z7DezHjcsKWkTaOwOLjz7QyYvGpX7zkp75LpvZmBxE2MoG',NULL,'2026-07-03 11:40:53','2026-07-07 08:18:45','images/default-profile-picture.png','synced',NULL,'2026-07-07 08:18:45',0),
(69,28,'9d8389f36a7098b0194507c98b8de365',0,'valerie kono','valerie@email.test','valerie',NULL,'$2y$12$yLmzyhNi9ItsVTSFB4v2i.Uc/9nnBCYLj9HQKn7fJHAjJNbkLDRBO',NULL,'2026-07-03 11:41:09','2026-07-03 12:21:07','images/default-profile-picture.png','synced',NULL,'2026-07-03 12:21:07',0);
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'laravel'
--

--
-- Dumping routines for database 'laravel'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-07-07 11:06:25
