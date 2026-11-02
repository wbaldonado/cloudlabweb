-- =============================================================================
-- CloudLab Latam — CRM Dashboard database update
-- =============================================================================
-- Run in phpMyAdmin (Hostinger) when you cannot run: php artisan migrate --force
--
-- Prerequisites (must already exist):
--   users, crm_companies, crm_contacts, crm_deals, migrations
--
-- What this script does:
--   1. Creates table crm_activities (for CRM Dashboard activities CRUD)
--   2. Registers Laravel migration 2026_06_01_000001_create_crm_activities_table
--
-- Safe to re-run: uses IF NOT EXISTS / skips duplicate migration row.
--
-- After running:
--   CRM Dashboard → https://cloudlablatam.tech/admin/crm
-- =============================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- -----------------------------------------------------------------------------
-- crm_activities
-- -----------------------------------------------------------------------------
CREATE TABLE IF NOT EXISTS `crm_activities` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `type` enum('email','meeting','task','call') NOT NULL DEFAULT 'task',
  `status` enum('not_started','in_progress','blocked','completed') NOT NULL DEFAULT 'not_started',
  `due_date` date DEFAULT NULL,
  `contact_id` bigint unsigned DEFAULT NULL,
  `company_id` bigint unsigned DEFAULT NULL,
  `deal_id` bigint unsigned DEFAULT NULL,
  `owner_id` bigint unsigned DEFAULT NULL,
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

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------------------------------
-- Laravel migrations table (mark activities migration as applied)
-- -----------------------------------------------------------------------------
SET @crm_batch := (SELECT COALESCE(MAX(`batch`), 0) + 1 FROM `migrations`);

INSERT INTO `migrations` (`migration`, `batch`)
SELECT '2026_06_01_000001_create_crm_activities_table', @crm_batch
FROM DUAL
WHERE NOT EXISTS (
    SELECT 1 FROM `migrations`
    WHERE `migration` = '2026_06_01_000001_create_crm_activities_table'
);

-- -----------------------------------------------------------------------------
-- Verify
-- -----------------------------------------------------------------------------
-- SHOW TABLES LIKE 'crm_activities';
-- DESCRIBE crm_activities;
-- SELECT * FROM migrations WHERE migration LIKE '%crm_activities%';

-- =============================================================================
-- OPTIONAL: sample CRM data (uncomment only on empty database / for testing)
-- =============================================================================
/*
-- Sample company
INSERT INTO `crm_companies` (`name`, `industry`, `country`, `created_at`, `updated_at`)
SELECT 'Acme Corp', 'Tecnología', 'Colombia', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `crm_companies` WHERE `name` = 'Acme Corp');

SET @company_id := (SELECT `id` FROM `crm_companies` WHERE `name` = 'Acme Corp' LIMIT 1);
SET @owner_id   := (SELECT `id` FROM `users` WHERE `is_admin` = 1 ORDER BY `id` LIMIT 1);

-- Sample contact
INSERT INTO `crm_contacts` (
    `first_name`, `last_name`, `email`, `phone`, `job_title`,
    `lifecycle_stage`, `company_id`, `owner_id`, `captured_at`, `created_at`, `updated_at`
)
SELECT
    'Juan', 'Pérez', 'juan.perez@acme.com', '+57 300 000 0000', 'Director TI',
    'sql', @company_id, @owner_id, NOW(), NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `crm_contacts` WHERE `email` = 'juan.perez@acme.com');

SET @contact_id := (SELECT `id` FROM `crm_contacts` WHERE `email` = 'juan.perez@acme.com' LIMIT 1);

-- Sample deal
INSERT INTO `crm_deals` (
    `title`, `amount`, `stage`, `contact_id`, `company_id`, `owner_id`,
    `expected_close_date`, `probability`, `created_at`, `updated_at`
)
SELECT
    'Implementación cloud', 15000.00, 'proposal', @contact_id, @company_id, @owner_id,
    DATE_ADD(CURDATE(), INTERVAL 30 DAY), 60, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `crm_deals` WHERE `title` = 'Implementación cloud');

SET @deal_id := (SELECT `id` FROM `crm_deals` WHERE `title` = 'Implementación cloud' LIMIT 1);

-- Sample activities
INSERT INTO `crm_activities` (`title`, `type`, `status`, `due_date`, `contact_id`, `company_id`, `deal_id`, `owner_id`, `notes`, `created_at`, `updated_at`)
SELECT 'Llamada de seguimiento', 'call', 'not_started', DATE_ADD(CURDATE(), INTERVAL 3 DAY),
       @contact_id, @company_id, @deal_id, @owner_id, 'Confirmar propuesta enviada.', NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `crm_activities` WHERE `title` = 'Llamada de seguimiento');

INSERT INTO `crm_activities` (`title`, `type`, `status`, `due_date`, `contact_id`, `company_id`, `deal_id`, `owner_id`, `notes`, `created_at`, `updated_at`)
SELECT 'Enviar propuesta formal', 'email', 'in_progress', DATE_ADD(CURDATE(), INTERVAL 7 DAY),
       @contact_id, @company_id, @deal_id, @owner_id, NULL, NOW(), NOW()
FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM `crm_activities` WHERE `title` = 'Enviar propuesta formal');
*/
