-- ========================================================
-- = Criacao do schema JM INFORMATICA
-- ========================================================

CREATE DATABASE IF NOT EXISTS `jm_informatica`
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE `jm_informatica`;

-- ========================================================
-- = Tabela user
-- = Armazena os usuarios do sistema
-- ========================================================

CREATE TABLE IF NOT EXISTS `user` (
    `id_user` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(255) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `update_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `ativo` TINYINT(1) NOT NULL DEFAULT 1,
    PRIMARY KEY (`id_user`),
    INDEX `idx_user_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ========================================================
-- = Tabela: service
-- = Armazena os servicos prestados pelos funcionarios
-- ========================================================

CREATE TABLE IF NOT EXISTS `service` (
    `id_service` BIGINT(20) NOT NULL AUTO_INCREMENT,
    `description` VARCHAR(255) NOT NULL,
    `price` DECIMAL(11, 2) NOT NULL,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `update_at` DATETIME NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    `finished_at` DATETIME NULL DEFAULT NULL,
    `commission_user` DECIMAL(11, 2) NULL DEFAULT NULL,
    `user_id_user` BIGINT(20) NOT NULL,
    PRIMARY KEY (`id_service`),
    INDEX `idx_service_user` (`user_id_user`),
    INDEX `idx_service_finished` (`finished_at`),
    CONSTRAINT `fk_service_user` 
        FOREIGN KEY (`user_id_user`) 
        REFERENCES `user` (`id_user`) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



-- ========================================================
-- = Insercao de usuarios teste
-- ========================================================

INSERT INTO `user` (`id_user`, `name`, `email`, `password`, `created_at`, `ativo`) VALUES
(1, 'Jose Silva', 'jose@jminformatica.com.br', '$2y$12$zkGkxZTApEHrz2GCaFFcRO4oYCsN62VtHaPl9Ebmd7YX.ZpYrwn.q', NOW(), 1),
(2, 'Maria Oliveira', 'maria@jminformatica.com.br', '$2y$12$zkGkxZTApEHrz2GCaFFcRO4oYCsN62VtHaPl9Ebmd7YX.ZpYrwn.q', NOW(), 1)
ON DUPLICATE KEY UPDATE `id_user` = `id_user`;

-- ========================================================
-- = Insercao de servicos teste 
-- ========================================================

INSERT INTO `service` (`id_service`, `description`, `price`, `created_at`, `finished_at`, `commission_user`, `user_id_user`) VALUES
(1, 'Troca de Tela de Notebook', 425.00, NOW() - INTERVAL 5 DAY, NULL, NULL, 1),
(2, 'Conserto de Carregador', 150.00, NOW() - INTERVAL 4 DAY, NULL, NULL, 1),
(3, 'Troca de Pasta Termica', 80.00, NOW() - INTERVAL 3 DAY, NULL, NULL, 1),
(4, 'Instalacao de Office 2016', 120.00, NOW() - INTERVAL 2 DAY, NOW() - INTERVAL 1 DAY, 6.00, 1),
(5, 'Limpeza de Computador', 200.00, NOW() - INTERVAL 1 DAY, NOW(), 10.00, 2)
ON DUPLICATE KEY UPDATE `id_service` = `id_service`;