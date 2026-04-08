-- ============================================================
--  PROJEX DATABASE — Full Schema + Seed Data
--  Import this into phpMyAdmin after creating projex_db
-- ============================================================

SET FOREIGN_KEY_CHECKS = 0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";

-- ── Tables ───────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `users` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(100) NOT NULL,
  `email`      VARCHAR(150) NOT NULL UNIQUE,
  `password`   VARCHAR(255) NOT NULL,
  `role`       ENUM('Admin','Project Manager','Developer','Designer','Client') NOT NULL DEFAULT 'Developer',
  `avatar`     VARCHAR(10)  DEFAULT '👤',
  `created_at` TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `projects` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `name`        VARCHAR(150) NOT NULL,
  `description` TEXT,
  `progress`    TINYINT UNSIGNED DEFAULT 0,
  `deadline`    DATE,
  `status`      ENUM('Active','On Hold','Completed') DEFAULT 'Active',
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `project_members` (
  `project_id` INT NOT NULL,
  `user_id`    INT NOT NULL,
  PRIMARY KEY (`project_id`, `user_id`),
  FOREIGN KEY (`project_id`) REFERENCES `projects`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`)    REFERENCES `users`(`id`)    ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `tasks` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `title`       VARCHAR(200) NOT NULL,
  `description` TEXT,
  `status`      ENUM('Backlog','To Do','In Progress','Review','Completed') DEFAULT 'To Do',
  `priority`    ENUM('Low','Medium','High','Critical') DEFAULT 'Medium',
  `assigned_to` INT NULL,
  `project_id`  INT NULL,
  `due_date`    DATE,
  `created_at`  TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`assigned_to`) REFERENCES `users`(`id`)    ON DELETE SET NULL,
  FOREIGN KEY (`project_id`)  REFERENCES `projects`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `comments` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `task_id`    INT NOT NULL,
  `user_id`    INT NOT NULL,
  `text`       TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`task_id`) REFERENCES `tasks`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ─────────────────────────────────────────────────────────

CREATE TABLE IF NOT EXISTS `notifications` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `user_id`    INT NOT NULL,
  `message`    VARCHAR(255) NOT NULL,
  `type`       ENUM('task','project','comment','system') DEFAULT 'system',
  `is_read`    TINYINT(1) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================================
--  SEED DATA
-- ============================================================

-- Users (passwords are all "password123" hashed with bcrypt)
INSERT INTO `users` (`id`,`name`,`email`,`password`,`role`,`avatar`) VALUES
(1, 'Alex Chen',       'alex@projex.io',   '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Admin',           '👨‍💼'),
(2, 'Sarah Kim',       'sarah@projex.io',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Project Manager', '👩‍💼'),
(3, 'Marcus Johnson',  'marcus@projex.io', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Developer',       '👨‍💻'),
(4, 'Emily Rodriguez', 'emily@projex.io',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Designer',        '👩‍🎨'),
(5, 'David Park',      'david@projex.io',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Developer',       '👨‍💻'),
(6, 'Lisa Wong',       'lisa@client.com',  '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Client',          '👩‍💼');

-- Projects
INSERT INTO `projects` (`id`,`name`,`description`,`progress`,`deadline`,`status`) VALUES
(1, 'Quantum Dashboard', 'Next-gen analytics platform with real-time data visualization', 65, '2026-05-15', 'Active'),
(2, 'NeuroSync Mobile',  'AI-powered mobile app for productivity tracking',               42, '2026-06-30', 'Active'),
(3, 'CyberVault API',    'Secure blockchain-based API for data storage',                  88, '2026-04-20', 'Active');

-- Project members
INSERT INTO `project_members` (`project_id`,`user_id`) VALUES
(1,2),(1,3),(1,4),(1,5),
(2,2),(2,3),(2,5),
(3,3),(3,5);

-- Tasks
INSERT INTO `tasks` (`id`,`title`,`description`,`status`,`priority`,`assigned_to`,`project_id`,`due_date`,`created_at`) VALUES
(1, 'Design authentication flow',         'Create wireframes and UI mockups for login/register screens', 'In Progress', 'High',     4, 1, '2026-04-10', '2026-03-28'),
(2, 'Implement WebSocket real-time updates','Set up WebSocket connection for live data streaming',       'To Do',       'Critical', 3, 1, '2026-04-12', '2026-03-29'),
(3, 'Database optimization',              'Optimize query performance for large datasets',               'Review',      'Medium',   5, 1, '2026-04-08', '2026-03-25'),
(4, 'AI model integration',               'Integrate TensorFlow model for predictive analytics',         'Backlog',     'Medium',   3, 2, '2026-05-01', '2026-03-20'),
(5, 'Mobile UI polish',                   'Final touches on mobile interface animations',                'Completed',   'Low',      4, 2, '2026-04-01', '2026-03-15'),
(6, 'API security audit',                 'Comprehensive security review and penetration testing',       'In Progress', 'Critical', 5, 3, '2026-04-05', '2026-03-28'),
(7, 'User dashboard wireframes',          'Create initial wireframes for dashboard layouts',             'To Do',       'High',     4, 1, '2026-04-15', '2026-04-01'),
(8, 'Setup CI/CD pipeline',               'Configure automated testing and deployment',                  'Backlog',     'Medium',   3, 3, '2026-04-25', '2026-03-22');

-- Comments
INSERT INTO `comments` (`task_id`,`user_id`,`text`,`created_at`) VALUES
(1, 2, 'Looking great! Can we add biometric options?', '2026-03-30 10:30:00'),
(3, 3, 'Index performance looks good',                 '2026-04-01 14:20:00');

-- Notifications (for user id=1, Alex the Admin)
INSERT INTO `notifications` (`user_id`,`message`,`type`,`is_read`) VALUES
(1, 'Marcus Johnson completed "Database optimization"',        'task',    0),
(1, 'New comment on "Design authentication flow"',             'comment', 0),
(1, 'Project "CyberVault API" deadline approaching in 3 days', 'project', 1);

SET FOREIGN_KEY_CHECKS = 1;
