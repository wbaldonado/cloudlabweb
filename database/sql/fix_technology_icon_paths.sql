-- Fix technology icon paths for files in public/images/technologies/
-- Run in phpMyAdmin on production AFTER confirming files exist in that folder.
--
-- Example file on disk: public/images/technologies/0ed3ad73-4e7c-48c0-919c-3eb717e49e92.svg
-- icon_path must be:        images/technologies/0ed3ad73-4e7c-48c0-919c-3eb717e49e92.svg

UPDATE `technologies`
SET `icon_path` = CONCAT('images/technologies/', SUBSTRING_INDEX(REPLACE(`icon_path`, '\\', '/'), '/', -1))
WHERE `icon_path` IS NOT NULL
  AND `icon_path` != ''
  AND `icon_path` NOT LIKE 'images/technologies/%';

-- Ensure technologies are visible on the homepage
UPDATE `technologies`
SET `is_active` = 1
WHERE `icon_path` IS NOT NULL
  AND `icon_path` != '';

-- Verify results:
-- SELECT id, name, icon_path, is_active FROM technologies;
