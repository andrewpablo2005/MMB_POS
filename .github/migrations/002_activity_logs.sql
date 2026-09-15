-- Migration 002: Activity Logs (audit trail)
-- Part of the Activity Log / Audit Trail feature (Task 42).
--
-- IMPORTANT: this file is a REFERENCE copy for documentation and for
-- manual phpMyAdmin imports. The application also creates this table
-- automatically on first use (see conn/activity_log.php,
-- mmb_activity_log_ensure_table) — so running this file is OPTIONAL.
--
-- Safe to run multiple times: CREATE TABLE IF NOT EXISTS is idempotent
-- and the table is purely ADDITIVE (no existing table is touched).

CREATE TABLE IF NOT EXISTS `activity_logs` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NULL,
  `username` VARCHAR(155) NULL,
  `role` VARCHAR(20) NULL,
  `module` VARCHAR(30) NOT NULL DEFAULT 'system',
  `action` VARCHAR(40) NOT NULL DEFAULT 'unknown',
  `entity_type` VARCHAR(30) NULL,
  `entity_id` INT NULL,
  `description` VARCHAR(500) NULL,
  `ip_address` VARCHAR(45) NULL,
  `user_agent` VARCHAR(255) NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_activity_created` (`created_at`),
  ADD KEY `idx_activity_user` (`user_id`),
  ADD KEY `idx_activity_action` (`action`),
  ADD KEY `idx_activity_module` (`module`);

ALTER TABLE `activity_logs`
  MODIFY `id` INT NOT NULL AUTO_INCREMENT;
