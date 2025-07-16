<?php

namespace crm\src\services;

use crm\src\services\Repositories\DbRepository\services\ASchemaProvider;

class CrmSchemaProvider extends ASchemaProvider
{
    /**
     * @return array<string,string>
     */
    protected static function schemas(): array
    {
        return array_merge(
            self::userSchemas(),
            self::p2pSchemas(),
            self::accessSchemas(),
            self::investmentSchemas(), // ← добавляем сюда
        );
    }

    /**
     * @return array<string,string>
     */
    protected static function userSchemas(): array
    {
        return [
            'user_levels' => <<<SQL
                CREATE TABLE user_levels (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(64) NOT NULL UNIQUE,
                    level INT NOT NULL
                );
            SQL,

            'users' => <<<SQL
                CREATE TABLE users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    login VARCHAR(64) NOT NULL UNIQUE,
                    password_hash VARCHAR(256) NOT NULL
                );
            SQL,

            'user_group_members' => <<<SQL
                CREATE TABLE user_group_members (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL UNIQUE,
                    leader_id INT NOT NULL,
                    CONSTRAINT fk_group_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    CONSTRAINT fk_group_leader FOREIGN KEY (leader_id) REFERENCES users(id) ON DELETE CASCADE
                );
            SQL,
        ];
    }

    /**
     * @return array<string,string>
     */
    protected static function p2pSchemas(): array
    {
        return [
            'sources' => <<<SQL
                CREATE TABLE sources (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(32) NOT NULL UNIQUE
                );
            SQL,

            'statuses' => <<<SQL
                CREATE TABLE statuses (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    title VARCHAR(32) NOT NULL UNIQUE
                );
            SQL,

            'leads' => <<<SQL
                CREATE TABLE leads (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    full_name VARCHAR(128) NOT NULL,
                    address VARCHAR(256) NOT NULL DEFAULT '',
                    contact VARCHAR(128) NOT NULL,
                    source_id INT NULL DEFAULT NULL,
                    status_id INT NULL DEFAULT NULL,
                    account_manager_id INT NULL DEFAULT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    CONSTRAINT fk_lead_source FOREIGN KEY (source_id) REFERENCES sources(id) ON DELETE SET NULL,
                    CONSTRAINT fk_lead_status FOREIGN KEY (status_id) REFERENCES statuses(id) ON DELETE SET NULL,
                    CONSTRAINT fk_lead_account_manager FOREIGN KEY (account_manager_id) REFERENCES users(id) ON DELETE SET NULL
                );
            SQL,
        ];
    }

    /**
     * @return array<string,string>
     */
    protected static function accessSchemas(): array
    {
        return [
            'access_contexts' => <<<SQL
                CREATE TABLE access_contexts (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL UNIQUE,
                    session_access_hash VARCHAR(256) DEFAULT NULL,
                    role_id INT DEFAULT NULL,
                    space_id INT DEFAULT NULL,
                    CONSTRAINT fk_access_context_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                    CONSTRAINT fk_access_context_role FOREIGN KEY (role_id) REFERENCES access_roles(id) ON DELETE SET NULL,
                    CONSTRAINT fk_access_context_space FOREIGN KEY (space_id) REFERENCES access_spaces(id) ON DELETE SET NULL
                );
            SQL,

            'access_roles' => <<<SQL
                CREATE TABLE access_roles (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(128) NOT NULL UNIQUE,
                    description VARCHAR(255) DEFAULT NULL
                );
            SQL,

            'access_spaces' => <<<SQL
                CREATE TABLE access_spaces (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    name VARCHAR(128) NOT NULL UNIQUE,
                    description VARCHAR(255) DEFAULT NULL
                );
            SQL,
        ];
    }

    /**
     * @return array<string,string>
     */
    protected static function investmentSchemas(): array
    {
        return [
            'inv_sources' => <<<SQL
                CREATE TABLE inv_sources (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    code VARCHAR(64) NOT NULL UNIQUE,
                    label VARCHAR(255) NOT NULL
                );
            SQL,

            'inv_statuses' => <<<SQL
                CREATE TABLE inv_statuses (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    code VARCHAR(64) NOT NULL UNIQUE,
                    label VARCHAR(255) NOT NULL
                );
            SQL,

            'inv_leads' => <<<SQL
                CREATE TABLE inv_leads (
                    uid VARCHAR(64) NOT NULL PRIMARY KEY,
                    created_at DATETIME NOT NULL,
                    contact VARCHAR(255) NOT NULL DEFAULT '',
                    phone VARCHAR(64) NOT NULL DEFAULT '',
                    email VARCHAR(255) NOT NULL DEFAULT '',
                    full_name VARCHAR(255) NOT NULL DEFAULT '',
                    account_manager_id INT DEFAULT NULL,
                    visible BOOLEAN NOT NULL DEFAULT TRUE,
                    source_id INT DEFAULT NULL,
                    status_id INT DEFAULT NULL,

                    FOREIGN KEY (account_manager_id) REFERENCES users(id) ON DELETE SET NULL,
                    FOREIGN KEY (source_id) REFERENCES inv_sources(id) ON DELETE SET NULL,
                    FOREIGN KEY (status_id) REFERENCES inv_statuses(id) ON DELETE SET NULL
                );
            SQL,


            'inv_balances' => <<<SQL
                CREATE TABLE inv_balances (
                    lead_uid VARCHAR(64) NOT NULL PRIMARY KEY,
                    current DOUBLE(10,2) NOT NULL DEFAULT 0.00,
                    deposit DOUBLE(10,2) NOT NULL DEFAULT 0.00,
                    potation DOUBLE(10,2) NOT NULL DEFAULT 0.00,
                    active DOUBLE(10,2) NOT NULL DEFAULT 0.00,

                    FOREIGN KEY (lead_uid) REFERENCES inv_leads(uid) ON DELETE CASCADE
                );
            SQL,

            'inv_comments' => <<<SQL
                CREATE TABLE inv_comments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    lead_uid VARCHAR(64) NOT NULL,
                    body TEXT NOT NULL,
                    time DATETIME NOT NULL,
                    who VARCHAR(64) DEFAULT '',
                    who_id VARCHAR(64) DEFAULT NULL,
                    `option` INT DEFAULT 0,

                    FOREIGN KEY (lead_uid) REFERENCES inv_leads(uid) ON DELETE CASCADE
                );
            SQL,

            'inv_deposits' => <<<SQL
                CREATE TABLE inv_deposits (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    uid VARCHAR(64) NOT NULL,
                    sum DOUBLE(10,2) NOT NULL DEFAULT 0.00,
                    created DATETIME NOT NULL,

                    FOREIGN KEY (uid) REFERENCES inv_leads(uid) ON DELETE CASCADE
                );
            SQL,

            'inv_activities' => <<<SQL
                CREATE TABLE activities (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    InvActivity_hash VARCHAR(64) NOT NULL UNIQUE,
                    lead_uid VARCHAR(64) NOT NULL,
                    type ENUM('active', 'closed') NOT NULL,
                    open_time DATETIME NOT NULL,
                    close_time DATETIME NULL,
                    pair VARCHAR(32) NOT NULL DEFAULT '',
                    open_price DOUBLE(10, 2) NOT NULL,
                    close_price DOUBLE(10, 2) NULL,
                    amount DOUBLE(10, 2) NOT NULL,
                    direction ENUM('long', 'short') NOT NULL,
                    result DOUBLE(10, 2) NULL,

                    INDEX idx_lead_uid (lead_uid),
                    FOREIGN KEY (lead_uid) REFERENCES inv_leads(uid) ON DELETE CASCADE
                );
            SQL,
        ];
    }
}
