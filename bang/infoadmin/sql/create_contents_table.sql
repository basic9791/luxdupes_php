CREATE TABLE IF NOT EXISTS contents (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL COMMENT '标题',
    keywords VARCHAR(255) DEFAULT NULL COMMENT '关键字',
    content TEXT DEFAULT NULL COMMENT '内容',
    status TINYINT DEFAULT 0 COMMENT '状态：0-草稿，1-已发布，2-已下线',
    platform VARCHAR(50) DEFAULT NULL COMMENT '平台',
    user_email VARCHAR(100) DEFAULT NULL COMMENT '使用者邮箱',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT '内容创建时间',
    published_at TIMESTAMP NULL DEFAULT NULL COMMENT '发布时间',
    INDEX idx_title (title),
    INDEX idx_keywords (keywords),
    INDEX idx_status (status),
    INDEX idx_platform (platform),
    INDEX idx_user_email (user_email),
    INDEX idx_created_at (created_at),
    INDEX idx_published_at (published_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;