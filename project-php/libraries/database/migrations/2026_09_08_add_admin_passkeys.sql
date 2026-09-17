CREATE TABLE IF NOT EXISTS `table_user_passkey` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    `id_user` INT NOT NULL,
    `credential_id` VARCHAR(1400) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    `credential_hash` CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    `public_key` TEXT NOT NULL,
    `sign_count` BIGINT UNSIGNED NOT NULL DEFAULT 0,
    `name` VARCHAR(100) NOT NULL,
    `created_at` INT UNSIGNED NOT NULL,
    `last_used_at` INT UNSIGNED DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `credential_hash` (`credential_hash`),
    KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `table_user_passkey_limit` (
    `bucket` CHAR(64) CHARACTER SET ascii COLLATE ascii_bin NOT NULL,
    `attempts` INT UNSIGNED NOT NULL DEFAULT 0,
    `expires_at` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`bucket`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
