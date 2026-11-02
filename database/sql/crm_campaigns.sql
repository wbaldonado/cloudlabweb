<?php

-- CRM email campaigns (Hostinger-friendly install)
-- Run in phpMyAdmin if migrations are not available.

CREATE TABLE IF NOT EXISTS `crm_email_unsubscribes` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_email_unsubscribes_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `crm_campaigns` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `html_body` longtext NOT NULL,
  `text_body` longtext DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'draft',
  `from_name` varchar(255) DEFAULT NULL,
  `from_email` varchar(255) DEFAULT NULL,
  `cta_label` varchar(255) DEFAULT NULL,
  `cta_url` varchar(255) DEFAULT NULL,
  `created_by` bigint unsigned DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `recipients_count` int unsigned NOT NULL DEFAULT 0,
  `sent_count` int unsigned NOT NULL DEFAULT 0,
  `failed_count` int unsigned NOT NULL DEFAULT 0,
  `open_count` int unsigned NOT NULL DEFAULT 0,
  `unique_open_count` int unsigned NOT NULL DEFAULT 0,
  `click_count` int unsigned NOT NULL DEFAULT 0,
  `unique_click_count` int unsigned NOT NULL DEFAULT 0,
  `unsubscribe_count` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_campaigns_created_by_foreign` (`created_by`),
  CONSTRAINT `crm_campaigns_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `crm_campaign_recipients` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `contact_id` bigint unsigned DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `name` varchar(255) DEFAULT NULL,
  `token` varchar(64) NOT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'pending',
  `error` text DEFAULT NULL,
  `sent_at` timestamp NULL DEFAULT NULL,
  `opened_at` timestamp NULL DEFAULT NULL,
  `clicked_at` timestamp NULL DEFAULT NULL,
  `open_count` int unsigned NOT NULL DEFAULT 0,
  `click_count` int unsigned NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `crm_campaign_recipients_token_unique` (`token`),
  KEY `crm_campaign_recipients_campaign_id_status_index` (`campaign_id`,`status`),
  KEY `crm_campaign_recipients_campaign_id_email_index` (`campaign_id`,`email`),
  CONSTRAINT `crm_campaign_recipients_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `crm_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_campaign_recipients_contact_id_foreign` FOREIGN KEY (`contact_id`) REFERENCES `crm_contacts` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `crm_campaign_events` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `campaign_id` bigint unsigned NOT NULL,
  `recipient_id` bigint unsigned NOT NULL,
  `type` varchar(50) NOT NULL,
  `url` text DEFAULT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `crm_campaign_events_campaign_id_type_index` (`campaign_id`,`type`),
  KEY `crm_campaign_events_recipient_id_type_index` (`recipient_id`,`type`),
  CONSTRAINT `crm_campaign_events_campaign_id_foreign` FOREIGN KEY (`campaign_id`) REFERENCES `crm_campaigns` (`id`) ON DELETE CASCADE,
  CONSTRAINT `crm_campaign_events_recipient_id_foreign` FOREIGN KEY (`recipient_id`) REFERENCES `crm_campaign_recipients` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
