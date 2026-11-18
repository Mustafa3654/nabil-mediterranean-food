-- Run once in the Hostinger site's database using phpMyAdmin.
-- Existing orders are marked sent so the retry worker does not replay history.
ALTER TABLE orders
    ADD COLUMN telegram_status ENUM('sent', 'pending', 'failed') NOT NULL DEFAULT 'sent',
    ADD COLUMN telegram_notified_at DATETIME DEFAULT NULL,
    ADD COLUMN telegram_attempts SMALLINT UNSIGNED NOT NULL DEFAULT 0,
    ADD COLUMN telegram_last_error TEXT DEFAULT NULL,
    ADD COLUMN telegram_last_attempt_at DATETIME DEFAULT NULL,
    ADD COLUMN telegram_next_attempt_at DATETIME DEFAULT NULL,
    ADD INDEX idx_orders_telegram_retry (telegram_notified_at, telegram_next_attempt_at, id);

-- Mark existing orders as already handled so the worker does not replay history.
UPDATE orders SET telegram_notified_at = created_at, telegram_status = 'sent' WHERE telegram_notified_at IS NULL;
