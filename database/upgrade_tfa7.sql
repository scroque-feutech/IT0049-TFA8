-- Back up your database first. Run ONCE on the exact supplied TFA7 schema.
-- Preserves every existing user, customer and avatar; adds authentication columns.
ALTER TABLE users
 ADD COLUMN email VARCHAR(255) NULL AFTER full_name,
 ADD COLUMN email_verified_at TIMESTAMP NULL AFTER email,
 ADD COLUMN password VARCHAR(255) NULL AFTER email_verified_at,
 ADD COLUMN remember_token VARCHAR(100) NULL AFTER avatar,
 ADD UNIQUE KEY users_email_unique (email);
CREATE TABLE password_reset_tokens (
 email VARCHAR(255) NOT NULL PRIMARY KEY, token VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
