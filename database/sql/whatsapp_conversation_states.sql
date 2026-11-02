-- WhatsApp conversation states (Hostinger phpMyAdmin)
-- Run once on production database.

CREATE TABLE IF NOT EXISTS `whatsapp_conversation_states` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `phone` varchar(32) NOT NULL,
  `step` varchar(32) NOT NULL DEFAULT 'greeting',
  `payload` json DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `whatsapp_conversation_states_phone_unique` (`phone`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
