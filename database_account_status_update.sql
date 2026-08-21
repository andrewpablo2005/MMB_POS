-- Add reversible account disabling support.
-- Run apply_account_status_migration.php for an idempotent migration.
ALTER TABLE users
	ADD COLUMN status ENUM('active', 'disabled') NOT NULL DEFAULT 'active';
