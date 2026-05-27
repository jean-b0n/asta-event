<?php
$host = 'mysql.railway.internal'; // ⚠️ change si tu es en local
$port = '3306';
$name = 'railway';
$user = 'root';
$pass = 'GzyrBxCJiKgZfcIAJePGBvlwDpOyccEA'; // ⚠️ change ça !

$sql = <<<SQL
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  minecraft_pseudo VARCHAR(50) DEFAULT NULL,
  discord_pseudo VARCHAR(50) DEFAULT NULL,
  role ENUM('player','admin') DEFAULT 'player',
  team_id INT DEFAULT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS join_requests (
  id INT AUTO_INCREMENT PRIMARY KEY,
  minecraft_pseudo VARCHAR(50) NOT NULL,
  discord_pseudo VARCHAR(50) NOT NULL,
  answers TEXT NOT NULL,
  status ENUM('pending','accepted','rejected') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS teams (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  color VARCHAR(7) DEFAULT '#FFD700',
  total_points INT DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS trials (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  description TEXT,
  trial_order INT DEFAULT 0,
  status ENUM('pending','active','finished') DEFAULT 'pending',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS trial_results (
  id INT AUTO_INCREMENT PRIMARY KEY,
  trial_id INT NOT NULL,
  team_id INT NOT NULL,
  points INT DEFAULT 0,
  rank_position INT DEFAULT 0,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  FOREIGN KEY (trial_id) REFERENCES trials(id) ON DELETE CASCADE,
  FOREIGN KEY (team_id) REFERENCES teams(id) ON DELETE CASCADE,
  UNIQUE KEY unique_trial_team (trial_id, team_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS competition_settings (
  id INT AUTO_INCREMENT PRIMARY KEY,
  setting_key VARCHAR(50) NOT NULL UNIQUE,
  setting_value TEXT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO competition_settings (setting_key, setting_value) VALUES
('competition_started', '0'),
('competition_name', 'AstasCup V1'),
('competition_description', 'Événement Minecraft by Enoe_one'),
('teams_revealed', '0');

INSERT IGNORE INTO users (username, password, role) VALUES
('Enoe_one', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC..og/fFnEp1UHBf0Mq', 'admin');
SQL;
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>AstasCup — Installation BDD</title>
<style>
  body { font-family: monospace; background: #0a0c0e; color: #c8d4dc; padding: 2rem; }
  h1 { color: #d4a017; margin-bottom: 1.5rem; }
  .ok  { color: #3ddc3d; }
  .err { color: #e03030; }
  .box { background: #131820; border: 1px solid #1e2a35; border-radius: 6px; padding: 1.5rem; margin-bottom: 1rem; }
  .btn { display:inline-block; margin-top:1.5rem; padding:.75rem 2rem; background:#d4a017; color:#000; border:none; border-radius:4px; font-size:1rem; cursor:pointer; text-decoration:none; font-weight:700; }
</style>
</head>
<body>

<h1>⚔ AstasCup V1 — Installation de la base de données</h1>

<?php
try {
    $dsn = "mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);

    echo '<div class="box"><span class="ok">✅ Connexion MySQL réussie !</span></div>';

    // découpe propre des requêtes
    $queries = preg_split('/;[\r\n]+/', $sql);

    $i = 1;

    echo '<div class="box">';
    foreach ($queries as $query) {
        $query = trim($query);
        if (empty($query)) continue;

        try {
            $pdo->exec($query);
            echo "<span class='ok'>✔ Requête $i OK</span><br>";
        } catch (PDOException $e) {
            echo "<span class='err'>❌ Erreur requête $i :</span><br>";
            echo "<pre style='color:#ff8080'>" . htmlspecialchars($query) . "</pre>";
            echo "<pre style='color:#ffaa00'>" . $e->getMessage() . "</pre>";
        }

        $i++;
    }
    echo '</div>';

} catch (PDOException $e) {
    echo '<div class="box"><span class="err">❌ Erreur de connexion :</span><br>'.$e->getMessage().'</div>';
}
?>
</body>
</html>
