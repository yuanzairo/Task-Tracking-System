-- =============================================
--  Task Tracking System — Database Schema
-- =============================================

CREATE DATABASE IF NOT EXISTS task_tracker;
USE task_tracker;

-- Accounts table
CREATE TABLE IF NOT EXISTS accounts (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    username    VARCHAR(50)  NOT NULL UNIQUE,
    password    VARCHAR(255) NOT NULL,          -- bcrypt hash
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
);

-- Tasks table
CREATE TABLE IF NOT EXISTS tasks (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    account_id  INT          NOT NULL,
    title       VARCHAR(150) NOT NULL,
    description TEXT,
    status      ENUM('pending','completed') NOT NULL DEFAULT 'pending',
    due_date    DATE,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_id) REFERENCES accounts(id) ON DELETE CASCADE
);
