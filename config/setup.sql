-- ============================================================
-- Document Management System - Database Schema
-- ============================================================

CREATE DATABASE IF NOT EXISTS dms_db
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE dms_db;

-- -----------------------------------------------------------
-- Admins table (top-level role, full control)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS admins (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  full_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  phone         VARCHAR(20)  DEFAULT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Subadmins table (can upload / modify / delete documents only)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS subadmins (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  full_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  phone         VARCHAR(20)  DEFAULT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Users table (category 1 = class-restricted, category 2 = all)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS users (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  username      VARCHAR(50)  NOT NULL UNIQUE,
  password      VARCHAR(255) NOT NULL,
  full_name     VARCHAR(100) NOT NULL,
  email         VARCHAR(100) NOT NULL UNIQUE,
  phone         VARCHAR(20)  DEFAULT NULL,
  class_id      INT          DEFAULT NULL,
  category      TINYINT      NOT NULL DEFAULT 1 COMMENT '1=class restricted,2=all docs',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_user_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Classes table (document classes / categories)
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS classes (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  class_name    VARCHAR(100) NOT NULL UNIQUE,
  description   VARCHAR(255) DEFAULT NULL,
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Documents table
-- -----------------------------------------------------------
CREATE TABLE IF NOT EXISTS documents (
  id            INT AUTO_INCREMENT PRIMARY KEY,
  document_name VARCHAR(200) NOT NULL,
  project_name  VARCHAR(200) NOT NULL,
  class_id      INT          DEFAULT NULL,
  doc_date      DATE         NOT NULL,
  file_path     VARCHAR(500) NOT NULL,
  file_type     VARCHAR(20)  DEFAULT NULL,
  uploaded_by   VARCHAR(50)  NOT NULL COMMENT 'username of uploader',
  uploader_role VARCHAR(20)  NOT NULL COMMENT 'admin|subadmin',
  created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_doc_class FOREIGN KEY (class_id) REFERENCES classes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- -----------------------------------------------------------
-- Default admin account (password: admin123)
-- -----------------------------------------------------------
INSERT INTO admins (username, password, full_name, email, phone)
SELECT 'admin', '$2y$12$z4KrywskF3BDOkOx0Ae7Vuyjo/hh3wqgb82/s98oBe5M/Hyv3tD.O',
       'System Administrator', 'admin@dms.local', '0000000000'
WHERE NOT EXISTS (SELECT 1 FROM admins WHERE username = 'admin');
