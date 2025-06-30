<?php

namespace crm\src\services\Repositories\DbRepository\services;

class CrmSchemaProvider extends ASchemaProvider
{
    protected static function schemas(): array
    {
        return [
            /**
             * === Пользователи админки / менеджеры / админы ===
             */
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
                    password_hash VARCHAR(256) NOT NULL,
                );
            SQL,

            /**
             * === Отдельная сеть для лидов ===
             */
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
                    visible BOOLEAN NOT NULL DEFAULT TRUE,

                    CONSTRAINT fk_lead_source FOREIGN KEY (source_id) REFERENCES sources(id) ON DELETE SET NULL,
                    CONSTRAINT fk_lead_status FOREIGN KEY (status_id) REFERENCES statuses(id) ON DELETE SET NULL,
                    CONSTRAINT fk_lead_account_manager FOREIGN KEY (account_manager_id) REFERENCES users(id) ON DELETE SET NULL
                );
            SQL,

            /**
             * TABLE - lead_id INT NOT NULL UNIQUE, 1 ко 1 или лид может иметь много balances
             */
            'balances' => <<<SQL
                CREATE TABLE balances (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    lead_id INT NOT NULL UNIQUE,
                    current DOUBLE(10,2) NOT NULL DEFAULT 0.00,
                    drain DOUBLE(10,2) NOT NULL DEFAULT 0.00,
                    potential DOUBLE(10,2) NOT NULL DEFAULT 0.00,

                    CONSTRAINT fk_balance_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
                );
            SQL,

            /**
             * TABLE - lead_id INT NOT NULL UNIQUE, 1 ко 1 или лид может иметь много deposits
             * sum DOUBLE(10,2) NOT NULL, - сделать по умолчанию 0.00
             */
            'deposits' => <<<SQL
                CREATE TABLE deposits (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    lead_id INT NOT NULL UNIQUE,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    sum DOUBLE(10,2) NOT NULL DEFAULT 0.00,

                    CONSTRAINT fk_deposit_lead FOREIGN KEY (lead_id) REFERENCES leads(id) ON DELETE CASCADE
                );
            SQL,

            'comments' => <<<SQL
                CREATE TABLE comments (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    comment TEXT NOT NULL,
                    user_id INT NULL,
                    lead_id INT NOT NULL,
                    deposit_id INT NULL DEFAULT NULL,
                    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

                    CONSTRAINT fk_comment_user FOREIGN KEY (user_id)
                        REFERENCES users(id)
                        ON DELETE CASCADE,

                    CONSTRAINT fk_comment_lead FOREIGN KEY (lead_id)
                        REFERENCES leads(id)
                        ON DELETE CASCADE,

                    CONSTRAINT fk_comment_deposit FOREIGN KEY (deposit_id)
                        REFERENCES deposits(id)
                        ON DELETE SET NULL
                );
            SQL,

            'user_group_members' => <<<SQL
                CREATE TABLE user_group_members (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL UNIQUE,
                    leader_id INT NOT NULL,

                    CONSTRAINT fk_group_user FOREIGN KEY (user_id)
                        REFERENCES users(id)
                        ON DELETE CASCADE,

                    CONSTRAINT fk_group_leader FOREIGN KEY (leader_id)
                        REFERENCES users(id)
                        ON DELETE CASCADE
                );
            SQL,

        ];
    }
}
