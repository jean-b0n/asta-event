<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Équipes';
include __DIR__ . '/includes/header.php';

$db = getDB();
$revealed = isTeamsRevealed();
$started  = isCompetitionStarted();

if ($revealed) {
    $teams = $db->query("SELECT t.*, COUNT(u.id) as member_count FROM teams t LEFT JOIN users u ON u.team_id = t.id GROUP BY t.id ORDER BY t.total_points DESC")->fetchAll();
    foreach ($teams as &$team) {
        $stmt = $db->prepare("SELECT username, minecraft_pseudo FROM users WHERE team_id = ? AND role = 'player'");
        $stmt->execute([$team['id']]);
        $team['members'] = $stmt->fetchAll();
    }
    unset($team);
}
?>

<div class="page-hero">
    <h1>⚔ Répartition des Équipes</h1>
    <div class="divider-gold"></div>
    <p>Les équipes sont tirées au sort avant le début de la compétition.</p>
</div>

<?php if (!$revealed): ?>
<div class="card waiting-block">
    <div class="icon">🎲</div>
    <h2>Répartition non dévoilée</h2>
    <p>
        La répartition des équipes n'a pas encore été révélée.<br>
        Elle sera tirée au sort par <strong style="color:var(--gold)">Enoe_one</strong> avant le début de la compétition et sera publiée ici.
    </p>
    <div style="margin-top:2rem; display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
        <div style="padding:.75rem 1.5rem; border:1px solid var(--border); border-radius:6px; background:rgba(212,160,23,.03); text-align:center">
            <div style="font-family:'Cinzel',serif; font-size:.7rem; color:var(--text-dim); text-transform:uppercase; letter-spacing:.08em; margin-bottom:.25rem">Statut</div>
            <div style="color:var(--gold-dim); font-size:.9rem">En attente du tirage au sort</div>
        </div>
    </div>
</div>

<?php else: ?>

<div style="margin-bottom:1.5rem;">
    <div class="alert alert-info">🎲 Les équipes ont été tirées au sort — <?= count($teams) ?> équipe<?= count($teams) > 1 ? 's' : '' ?> formée<?= count($teams) > 1 ? 's' : '' ?>.</div>
</div>

<div class="grid-3">
    <?php foreach ($teams as $team): ?>
    <div class="card" style="border-color:<?= sanitize($team['color']) ?>44; position:relative; overflow:hidden;">
        <!-- Accent bar -->
        <div style="position:absolute; top:0; left:0; right:0; height:3px; background:<?= sanitize($team['color']) ?>; box-shadow:0 0 12px <?= sanitize($team['color']) ?>88;"></div>

        <div style="display:flex; align-items:center; gap:.75rem; margin-bottom:1rem; padding-top:.25rem;">
            <div style="width:18px; height:18px; border-radius:4px; background:<?= sanitize($team['color']) ?>; flex-shrink:0; box-shadow:0 0 10px <?= sanitize($team['color']) ?>88;"></div>
            <div style="font-family:'Cinzel',serif; font-size:1rem; font-weight:700; color:var(--text-bright)"><?= sanitize($team['name']) ?></div>
        </div>

        <div style="display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap;">
            <span class="badge badge-gray"><?= $team['member_count'] ?> joueur<?= $team['member_count'] > 1 ? 's' : '' ?></span>
            <?php if ($started): ?>
                <span class="badge badge-gold"><?= $team['total_points'] ?> pts</span>
            <?php endif; ?>
        </div>

        <div style="display:flex; flex-direction:column; gap:.5rem;">
            <?php if (empty($team['members'])): ?>
                <div style="color:var(--text-dim); font-size:.85rem; font-style:italic;">Aucun membre assigné</div>
            <?php else: ?>
                <?php foreach ($team['members'] as $member): ?>
                <div style="display:flex; align-items:center; gap:.5rem; padding:.5rem .75rem; background:var(--card2); border-radius:4px; border:1px solid var(--border);">
                    <span style="font-size:.85rem">🎮</span>
                    <span style="font-size:.85rem; color:var(--text-bright)"><?= sanitize($member['minecraft_pseudo'] ?: $member['username']) ?></span>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
