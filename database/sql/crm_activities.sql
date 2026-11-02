-- =============================================================================
-- CloudLab Latam — CRM Activities table only (legacy shortcut)
-- =============================================================================
-- Prefer: database/sql/crm_dashboard_update.sql (includes migration registration)
-- =============================================================================

SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS `crm_activities` (
  `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` enum('email','meeting','task','call') NOT NULL DEFAULT 'task',
  `status` enum('not_started','in_progress','blocked','completed') NOT NULL DEFAULT 'not_started',
  `due_date` date DEFAULT NULL,
  `contact_id` bigint(20) UNSIGNED DEFAULT NULL,
  `company_id` bigint(20) UNSIGNED DEFAULT NULL,
  `deal_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_activities_contact_id_foreign` (`contact_id`),
  KEY `crm_activities_company_id_foreign` (`company_id`),
  KEY `crm_activities_deal_id_foreign` (`deal_id`),
  KEY `crm_activities_owner_id_foreign` (`owner_id`),
  CONSTRAINT `crm_activities_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `crm_contacts` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_activities_company_id_foreign` FOREIGN KEY (`company_id`) REFERENCES `crm_companies` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_activities_deal_id_foreign` FOREIGN KEY (`deal_id`) REFERENCES `crm_deals` (`id`) ON DELETE SET NULL,
  CONSTRAINT `crm_activities_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
