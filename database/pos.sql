-- TFA8 FRESH DATABASE ONLY. Use upgrade_tfa7.sql for an existing TFA7 database.
CREATE TABLE users (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 username VARCHAR(255) NOT NULL UNIQUE, full_name VARCHAR(255) NOT NULL,
 email VARCHAR(255) NULL UNIQUE, email_verified_at TIMESTAMP NULL,
 password VARCHAR(255) NULL, avatar VARCHAR(255) NULL, remember_token VARCHAR(100) NULL,
 created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE customers (
 id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(255) NOT NULL, email VARCHAR(255) NOT NULL, phone VARCHAR(255) NOT NULL DEFAULT '',
 created_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
 updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
CREATE TABLE password_reset_tokens (
 email VARCHAR(255) NOT NULL PRIMARY KEY, token VARCHAR(255) NOT NULL, created_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
INSERT INTO users (username,full_name) VALUES
('michael','Michael De Santa'),('franklin','Franklin Clinton'),('trevor','Trevor Philips'),('lamar','Lamar Davis'),('lester','Lester Crest');
INSERT INTO customers (full_name,email,phone) VALUES
('Michael De Santa','michael@vangelico.example','09170000001'),
('Franklin Clinton','franklin@vagos.example','09170000002'),
('Trevor Philips','trevor@trevors.example','09170000003'),
('Lamar Davis','lamar@h_customs.example','09170000004'),
('Lester Crest','lester@bleeter.example','09170000005');
