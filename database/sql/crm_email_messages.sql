-- CRM email messages (send from CRM + IMAP reply tracking)
-- Safe to re-run: CREATE TABLE IF NOT EXISTS

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE TABLE IF NOT EXISTS `crm_email_messages` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` bigint unsigned DEFAULT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `direction` enum('outbound','inbound') NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body` longtext NOT NULL,
  `from_email` varchar(255) NOT NULL,
  `to_email` varchar(255) NOT NULL,
  `message_id` varchar(255) DEFAULT NULL,
  `in_reply_to` varchar(255) DEFAULT NULL,
  `imap_uid` varchar(255) DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_email_messages_message_id_unique` (`message_id`),
  UNIQUE KEY `crm_email_messages_imap_uid_unique` (`imap_uid`),
  KEY `crm_email_messages_contact_id_direction_index` (`contact_id`,`direction`),
  KEY `crm_email_messages_read_at_index` (`read_at`),
  KEY `crm_email_messages_user_id_foreign` (`user_id`),
  CONSTRAINT `crm_email_messages_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `crm_contacts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_email_messages_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 1;

INSERT IGNORE INTO `migrations` (`migration`, `batch`) VALUES
('2026_06_08_000001_create_crm_email_messages_table', 1);
