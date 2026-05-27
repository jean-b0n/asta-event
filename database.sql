-- AstasCup V1 - Base de données MySQL
-- Compatible Railway

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Table des comptes joueurs (créés par l'admin)
CREATE TABLE IF NOT EXISTS `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `minecraft_pseudo` VARCHAR(50) DEFAULT NULL,
  `discord_pseudo` VARCHAR(50) DEFAULT NULL,
  `role` ENUM('player','admin') DEFAULT 'player',
  `team_id` INT DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des demandes d'inscription (questionnaire)
CREATE TABLE IF NOT EXISTS `join_requests` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `minecraft_pseudo` VARCHAR(50) NOT NULL,
  `discord_pseudo` VARCHAR(50) NOT NULL,
  `answers` TEXT NOT NULL COMMENT 'JSON des réponses',
  `status` ENUM('pending','accepted','rejected') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des équipes
CREATE TABLE IF NOT EXISTS `teams` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `color` VARCHAR(7) DEFAULT '#FFD700',
  `total_points` INT DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des épreuves
CREATE TABLE IF NOT EXISTS `trials` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `trial_order` INT DEFAULT 0,
  `status` ENUM('pending','active','finished') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table du classement par épreuve
CREATE TABLE IF NOT EXISTS `trial_results` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `trial_id` INT NOT NULL,
  `team_id` INT NOT NULL,
  `points` INT DEFAULT 0,
  `rank_position` INT DEFAULT 0,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (`trial_id`) REFERENCES `trials`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Table des paramètres de la compétition
CREATE TABLE IF NOT EXISTS `competition_settings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `setting_key` VARCHAR(50) NOT NULL UNIQUE,
  `setting_value` TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Paramètres par défaut
INSERT INTO `competition_settings` (`setting_key`, `setting_value`) VALUES
('competition_started', '0'),
('competition_name', 'AstasCup V1'),
('competition_description', 'Événement Minecraft by Enoe_one'),
('teams_revealed', '0');

-- Compte admin par défaut (mot de passe: admin123 - À CHANGER)
INSERT INTO `users` (`username`, `password`, `role`) VALUES
('Enoe_one', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC..og/fFnEp1UHBf0Mq', 'admin');

-- Contrainte FK pour users.team_id
ALTER TABLE `users` ADD CONSTRAINT `fk_user_team` FOREIGN KEY (`team_id`) REFERENCES `teams`(`id`) ON DELETE SET NULL;
ALTER TABLE `trial_results` ADD UNIQUE KEY `unique_trial_team` (`trial_id`, `team_id`);
