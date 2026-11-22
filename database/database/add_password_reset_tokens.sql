-- Add password reset tokens table
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    token VARCHAR(255) NOT NULL UNIQUE,
    email VARCHAR(255) NOT NULL,
    expires_at BIGINT NOT NULL, -- Unix timestamp
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    used_at TIMESTAMP NULL,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_token (token),
    INDEX idx_user_id (user_id)
);

-- Add email field to users table if not exists
ALTER TABLE users ADD COLUMN IF NOT EXISTS email VARCHAR(255) AFTER username;
