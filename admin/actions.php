<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();

$db     = getDB();
$action = $_POST['action'] ?? '';
$redirect = '/admin/index.php';

function flash(string $type, string $msg): void {
    $_SESSION['flash'] = ['type' => $type, 'msg' => $msg];
}

switch ($action) {

    // ── Créer un compte joueur
    case 'create_account':
        $username  = trim($_POST['username'] ?? '');
        $password  = trim($_POST['password'] ?? '');
        $mc        = trim($_POST['minecraft_pseudo'] ?? '');
        $dc        = trim($_POST['discord_pseudo'] ?? '');
        $teamId    = !empty($_POST['team_id']) ? (int)$_POST['team_id'] : null;

        if (!$username || !$password) {
            flash('error', 'Identifiant et mot de passe requis.');
            break;
        }
        try {
            $stmt = $db->prepare("INSERT INTO users (username, password, minecraft_pseudo, discord_pseudo, team_id, role) VALUES (?,?,?,?,?,'player')");
            $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), $mc ?: null, $dc ?: null, $teamId]);
            flash('success', "Compte « $username » créé avec succès.");
        } catch (PDOException $e) {
            flash('error', 'Erreur : identifiant déjà pris ou données invalides.');
        }
        $redirect = '/admin/index.php?tab=comptes';
        break;

    // ── Supprimer un joueur
    case 'delete_player':
        $id = (int)($_POST['player_id'] ?? 0);
        $db->prepare("DELETE FROM users WHERE id = ? AND role = 'player'")->execute([$id]);
        flash('success', 'Joueur supprimé.');
        $redirect = '/admin/index.php?tab=comptes';
        break;

    // ── Accepter / Refuser demande
    case 'update_request':
        $id     = (int)($_POST['request_id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['accepted','rejected']) ? $_POST['status'] : 'pending';
        $db->prepare("UPDATE join_requests SET status = ? WHERE id = ?")->execute([$status, $id]);
        flash('success', 'Demande mise à jour.');
        $redirect = '/admin/index.php?tab=demandes';
        break;

    // ── Toggle compétition
    case 'toggle_competition':
        $current = getSetting('competition_started');
        setSetting('competition_started', $current === '1' ? '0' : '1');
        flash('success', $current === '1' ? 'Compétition arrêtée.' : 'Compétition lancée ! 🔥');
        $redirect = '/admin/index.php?tab=parametres';
        break;

    // ── Toggle équipes révélées
    case 'toggle_teams':
        $current = getSetting('teams_revealed');
        setSetting('teams_revealed', $current === '1' ? '0' : '1');
        flash('success', $current === '1' ? 'Équipes masquées.' : 'Équipes révélées ! 🎲');
        $redirect = '/admin/index.php?tab=parametres';
        break;

    // ── Créer une épreuve
    case 'create_trial':
        $name   = trim($_POST['trial_name'] ?? '');
        $desc   = trim($_POST['trial_desc'] ?? '');
        $order  = (int)($_POST['trial_order'] ?? 1);
        $status = in_array($_POST['trial_status'] ?? '', ['pending','active','finished']) ? $_POST['trial_status'] : 'pending';
        if (!$name) { flash('error', 'Nom requis.'); break; }
        $db->prepare("INSERT INTO trials (name, description, trial_order, status) VALUES (?,?,?,?)")->execute([$name, $desc, $order, $status]);
        flash('success', "Épreuve « $name » créée.");
        $redirect = '/admin/index.php?tab=parametres';
        break;

    // ── Changer statut épreuve
    case 'set_trial_status':
        $id     = (int)($_POST['trial_id'] ?? 0);
        $status = in_array($_POST['status'] ?? '', ['pending','active','finished']) ? $_POST['status'] : 'pending';
        $db->prepare("UPDATE trials SET status = ? WHERE id = ?")->execute([$status, $id]);
        flash('success', 'Statut de l\'épreuve mis à jour.');
        $redirect = '/admin/index.php?tab=parametres';
        break;

    // ── Supprimer épreuve
    case 'delete_trial':
        $id = (int)($_POST['trial_id'] ?? 0);
        $db->prepare("DELETE FROM trials WHERE id = ?")->execute([$id]);
        flash('success', 'Épreuve supprimée.');
        $redirect = '/admin/index.php?tab=parametres';
        break;

    // ── Créer une équipe
    case 'create_team':
        $name  = trim($_POST['team_name'] ?? '');
        $color = preg_match('/^#[0-9A-Fa-f]{6}$/', $_POST['team_color'] ?? '') ? $_POST['team_color'] : '#FFD700';
        if (!$name) { flash('error', 'Nom requis.'); break; }
        $db->prepare("INSERT INTO teams (name, color) VALUES (?,?)")->execute([$name, $color]);
        flash('success', "Équipe « $name » créée.");
        $redirect = '/admin/index.php?tab=classement';
        break;

    // ── Supprimer équipe
    case 'delete_team':
        $id = (int)($_POST['team_id'] ?? 0);
        $db->prepare("UPDATE users SET team_id = NULL WHERE team_id = ?")->execute([$id]);
        $db->prepare("DELETE FROM teams WHERE id = ?")->execute([$id]);
        flash('success', 'Équipe supprimée.');
        $redirect = '/admin/index.php?tab=classement';
        break;

    // ── Assigner joueur à une équipe
    case 'assign_team':
        $playerId = (int)($_POST['player_id'] ?? 0);
        $teamId   = (int)($_POST['team_id'] ?? 0);
        $db->prepare("UPDATE users SET team_id = ? WHERE id = ? AND role = 'player'")->execute([$teamId ?: null, $playerId]);
        flash('success', 'Assignation mise à jour.');
        $redirect = '/admin/index.php?tab=classement';
        break;

    // ── Mettre à jour les points d'une épreuve
    case 'update_points':
        $trialId = (int)($_POST['trial_id'] ?? 0);
        $points  = $_POST['points'] ?? [];

        if (!$trialId || empty($points)) {
            flash('error', 'Données invalides.');
            break;
        }

        // Reset total_points pour les équipes concernées, puis recalculer
        $db->beginTransaction();
        try {
            foreach ($points as $teamId => $pts) {
                $teamId = (int)$teamId;
                $pts    = max(0, (int)$pts);
                // Upsert dans trial_results
                $stmt = $db->prepare("INSERT INTO trial_results (trial_id, team_id, points) VALUES (?,?,?) ON DUPLICATE KEY UPDATE points = ?");
                $stmt->execute([$trialId, $teamId, $pts, $pts]);
            }
            // Recalculer total_points pour toutes les équipes
            $db->query("UPDATE teams t SET total_points = (SELECT COALESCE(SUM(tr.points),0) FROM trial_results tr WHERE tr.team_id = t.id)");
            $db->commit();
            flash('success', 'Points mis à jour et classement recalculé ✅');
        } catch (Exception $e) {
            $db->rollBack();
            flash('error', 'Erreur lors de la mise à jour : ' . $e->getMessage());
        }
        $redirect = '/admin/index.php?tab=classement';
        break;

    default:
        flash('error', 'Action inconnue.');
}

header('Location: ' . $redirect);
exit;
