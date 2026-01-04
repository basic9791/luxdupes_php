CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    slogan VARCHAR(255),
    avatar VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    banned_at TIMESTAMP NULL,
    status TINYINT DEFAULT 0 COMMENT '0启用,1禁用',
    
    company_name VARCHAR(100),
    job_title VARCHAR(100),
    job_start_date DATE,
    job_end_date DATE,
    
    school_name VARCHAR(100),
    major VARCHAR(100),
    minor VARCHAR(100),
    degree_type VARCHAR(50),
    graduation_year YEAR,
    
    region VARCHAR(100),
    region_start_date DATE,
    region_end_date DATE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;