<?php
require_once __DIR__ . '/../includes/config.php';
requireLogin();
if (isAdmin()) { header('Location: /admin/index.php'); exit; }

$pageTitle = 'Mon Dashboard';
$user = getCurrentUser();
$db = getDB();

// Points de l'équipe
$teamPoints = null;
$teamRank   = null;
$teamMembers = [];
if ($user['team_id']) {
    $stmt = $db->query("SELECT t.*, (SELECT COUNT(*)+1 FROM teams WHERE total_points > t.total_points) as rank_pos FROM teams t WHERE t.id = " . (int)$user['team_id']);
    $teamData = $stmt->fetch();
    if ($teamData) {
        $teamPoints = $teamData['total_points'];
        $teamRank   = $teamData['rank_pos'];
    }
    $stmt2 = $db->prepare("SELECT username, minecraft_pseudo FROM users WHERE team_id = ? AND role='player'");
    $stmt2->execute([$user['team_id']]);
    $teamMembers = $stmt2->fetchAll();
}

// Épreuves en cours / terminées
$trials = $db->query("SELECT * FROM trials WHERE status != 'pending' ORDER BY trial_order ASC")->fetchAll();

include __DIR__ . '/../includes/header.php';
?>

<div class="page-hero">
    <h1>👤 Mon Dashboard</h1>
    <div class="divider-gold"></div>
    <p>Bienvenue, <strong style="color:var(--gold)"><?= sanitize($user['username']) ?></strong> !</p>
</div>

<div class="grid-2" style="margin-bottom:2rem;">
    <!-- Infos compte -->
    <div class="card">
        <div class="card-title">🎮 Mon Profil</div>
        <div style="display:flex; flex-direction:column; gap:.85rem;">
            <div style="display:flex; justify-content:space-between; padding:.7rem 1rem; background:var(--card2); border-radius:4px; border:1px solid var(--border);">
                <span style="color:var(--text-dim); font-size:.85rem;">Identifiant</span>
                <span style="font-family:'Cinzel',serif; color:var(--text-bright); font-size:.85rem;"><?= sanitize($user['username']) ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:.7rem 1rem; background:var(--card2); border-radius:4px; border:1px solid var(--border);">
                <span style="color:var(--text-dim); font-size:.85rem;">Pseudo Minecraft</span>
                <span style="font-family:'Cinzel',serif; color:var(--green); font-size:.85rem;"><?= $user['minecraft_pseudo'] ? sanitize($user['minecraft_pseudo']) : '<span style="color:var(--text-dim)">Non renseigné</span>' ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:.7rem 1rem; background:var(--card2); border-radius:4px; border:1px solid var(--border);">
                <span style="color:var(--text-dim); font-size:.85rem;">Discord</span>
                <span style="font-family:'Cinzel',serif; color:#7289da; font-size:.85rem;"><?= $user['discord_pseudo'] ? sanitize($user['discord_pseudo']) : '<span style="color:var(--text-dim)">Non renseigné</span>' ?></span>
            </div>
            <div style="display:flex; justify-content:space-between; padding:.7rem 1rem; background:var(--card2); border-radius:4px; border:1px solid var(--border);">
                <span style="color:var(--text-dim); font-size:.85rem;">Équipe</span>
                <?php if ($user['team_name']): ?>
                    <span style="display:flex; align-items:center; gap:.4rem;">
                        <span style="width:10px;height:10px;border-radius:2px;background:<?= sanitize($user['team_color']) ?>;display:inline-block;"></span>
                        <span style="font-family:'Cinzel',serif; color:var(--text-bright); font-size:.85rem;"><?= sanitize($user['team_name']) ?></span>
                    </span>
                <?php else: ?>
                    <span style="color:var(--text-dim); font-size:.85rem; font-style:italic;">En attente d'assignation</span>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Équipe & classement -->
    <div class="card">
        <div class="card-title">🏆 Mon Équipe</div>
        <?php if (!isCompetitionStarted()): ?>
            <div class="waiting-block" style="padding:2rem 1rem;">
                <div class="icon" style="font-size:2rem">⏳</div>
                <p style="color:var(--text-dim)">La compétition n'a pas encore débuté. Reste attentif aux annonces !</p>
            </div>
        <?php elseif (!$user['team_id']): ?>
            <div style="text-align:center; padding:2rem; color:var(--text-dim);">
                <p>Tu n'as pas encore été assigné à une équipe.</p>
            </div>
        <?php else: ?>
            <!-- Rang & points -->
            <div class="grid-2" style="margin-bottom:1.25rem; gap:1rem;">
                <div style="text-align:center; padding:1.25rem; background:var(--card2); border-radius:6px; border:1px solid rgba(212,160,23,.2);">
                    <div style="font-family:'Cinzel Decorative',serif; font-size:2rem; color:var(--gold-light);"><?= $teamPoints ?></div>
                    <div style="font-size:.7rem; color:var(--text-dim); text-transform:uppercase; letter-spacing:.08em; margin-top:.25rem; font-family:'Cinzel',serif;">Points</div>
                </div>
                <div style="text-align:center; padding:1.25rem; background:var(--card2); border-radius:6px; border:1px solid rgba(212,160,23,.2);">
                    <div style="font-family:'Cinzel Decorative',serif; font-size:2rem; color:var(--gold-light);">#<?= $teamRank ?></div>
                    <div style="font-size:.7rem; color:var(--text-dim); text-transform:uppercase; letter-spacing:.08em; margin-top:.25rem; font-family:'Cinzel',serif;">Classement</div>
                </div>
            </div>
            <!-- Membres -->
            <div style="font-family:'Cinzel',serif; font-size:.72rem; color:var(--gold); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.75rem;">Membres de l'équipe</div>
            <div style="display:flex; flex-direction:column; gap:.5rem;">
                <?php foreach ($teamMembers as $m): ?>
                <div style="display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; background:var(--card2); border-radius:4px; border:1px solid var(--border);">
                    <?php if ($m['username'] === $user['username']): ?>
                        <span style="color:var(--gold); font-size:.75rem;">★</span>
                    <?php else: ?>
                        <span style="font-size:.75rem; color:var(--text-dim)">🎮</span>
                    <?php endif; ?>
                    <span style="font-size:.85rem; color:var(--text-bright)"><?= sanitize($m['minecraft_pseudo'] ?: $m['username']) ?></span>
                    <?php if ($m['username'] === $user['username']): ?><span class="badge badge-gold" style="margin-left:auto;font-size:.55rem">Toi</span><?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Épreuves -->
<?php if (!empty($trials)): ?>
<div class="card">
    <div class="card-title">⚔ Épreuves</div>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Épreuve</th>
                <th>Statut</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($trials as $t): ?>
            <tr>
                <td style="color:var(--gold); font-family:'Cinzel',serif"><?= $t['trial_order'] ?></td>
                <td style="color:var(--text-bright)"><?= sanitize($t['name']) ?></td>
                <td>
                    <?php if ($t['status'] === 'active'): ?>
                        <span class="badge badge-green">🔴 En cours</span>
                    <?php elseif ($t['status'] === 'finished'): ?>
                        <span class="badge badge-gray">✅ Terminée</span>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
