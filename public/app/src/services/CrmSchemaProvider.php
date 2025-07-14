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

    protected static function investmentSchemas(): array
    {
        return [
            // 'balances' => <<<SQL
            //     CREATE TABLE balances (
            //         id INT AUTO_INCREMENT PRIMARY KEY,
            //         lead_id INT NOT NULL UNIQUE,
            //         current DOUBLE(10,2) NOT NULL DEFAULT 0.00,
            //         drain DOUBLE(10,2) NOT NULL DEFAULT 0.00,
            //         potential DOUBLE(10,2) NOT NULL DEFAULT 0.00,
            //         CONSTRAINT fk_balance_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
            //     );
            // SQL,

            // 'deposits' => <<<SQL
            //     CREATE TABLE deposits (
            //         id INT AUTO_INCREMENT PRIMARY KEY,
            //         lead_id INT NOT NULL,
            //         tx_id VARCHAR(256) NOT NULL DEFAULT '',
            //         created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            //         sum DOUBLE(10,2) NOT NULL DEFAULT 0.00,
            //         CONSTRAINT fk_deposit_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
            //     );
            // SQL,

            // 'comments' => <<<SQL
            //     CREATE TABLE comments (
            //         id INT AUTO_INCREMENT PRIMARY KEY,
            //         comment TEXT NOT NULL,
            //         user_id INT NULL,
            //         lead_id INT NOT NULL,
            //         deposit_id INT NULL DEFAULT NULL,
            //         created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            //         CONSTRAINT fk_comment_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            //         CONSTRAINT fk_comment_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE,
            //         CONSTRAINT fk_comment_deposit FOREIGN KEY (deposit_id) REFERENCES deposits(id) ON DELETE SET NULL
            //     );
            // SQL,

            'activities' => <<<SQL
                CREATE TABLE activities (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    activity_hash VARCHAR(64) NOT NULL UNIQUE,
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

                    INDEX idx_lead_uid (lead_uid)
                );
            SQL,
        ];
    }
}
