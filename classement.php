<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Classement';
include __DIR__ . '/includes/header.php';

$db = getDB();
$started = isCompetitionStarted();

if ($started) {
    $teams = $db->query("SELECT t.*, COUNT(u.id) as member_count FROM teams t LEFT JOIN users u ON u.team_id = t.id GROUP BY t.id ORDER BY t.total_points DESC")->fetchAll();
    $trials = $db->query("SELECT * FROM trials ORDER BY trial_order ASC")->fetchAll();
}
?>

<div class="page-hero">
    <h1>🏆 Classement</h1>
    <div class="divider-gold"></div>
    <p>Suivi en temps réel de la compétition AstasCup V1</p>
</div>

<?php if (!$started): ?>
<!-- EN ATTENTE -->
<div class="card waiting-block">
    <div class="icon">🏆</div>
    <h2>En attente de la compétition</h2>
    <p>Le classement sera disponible une fois que la compétition aura été lancée par <strong style="color:var(--gold)">Enoe_one</strong>. Revenez ici dès le coup d'envoi !</p>
    <div style="margin-top:2rem">
        <div style="display:inline-flex; gap:.5rem; align-items:center; padding:.75rem 1.5rem; border:1px solid var(--gold-dim); border-radius:6px; background:rgba(212,160,23,.05);">
            <span style="color:var(--gold-dim)">●</span>
            <span style="font-family:'Cinzel',serif; font-size:.8rem; color:var(--gold-dim); letter-spacing:.08em;">COMPÉTITION NON COMMENCÉE</span>
        </div>
    </div>
</div>

<?php else: ?>

<!-- ONGLETS -->
<div style="display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:2rem;" id="tabs">
    <button class="tab-btn active" onclick="showTab('global')" style="font-family:'Cinzel',serif; font-size:.75rem; letter-spacing:.07em; text-transform:uppercase; padding:.6rem 1.2rem; background:rgba(212,160,23,.15); border:1px solid var(--gold); color:var(--gold); border-radius:4px; cursor:pointer;">🌍 Classement Global</button>
    <?php foreach ($trials as $i => $trial): ?>
        <button class="tab-btn" onclick="showTab('trial-<?= $trial['id'] ?>')" style="font-family:'Cinzel',serif; font-size:.75rem; letter-spacing:.07em; text-transform:uppercase; padding:.6rem 1.2rem; background:transparent; border:1px solid var(--border); color:var(--text-dim); border-radius:4px; cursor:pointer; transition:all .2s;">
            ⚔ <?= sanitize($trial['name']) ?>
            <?php if ($trial['status'] === 'active'): ?>
                <span class="badge badge-green" style="margin-left:.3rem; font-size:.55rem;">LIVE</span>
            <?php endif; ?>
        </button>
    <?php endforeach; ?>
</div>

<!-- CLASSEMENT GLOBAL -->
<div id="tab-global" class="tab-content">
    <div class="card">
        <div class="card-title">🌍 Classement Général</div>
        <?php if (empty($teams)): ?>
            <p style="color:var(--text-dim); text-align:center; padding:2rem">Aucune équipe enregistrée.</p>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:60px">Rang</th>
                    <th>Équipe</th>
                    <th>Membres</th>
                    <th style="text-align:right">Points</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teams as $i => $team):
                    $rank = $i + 1;
                    $medal = $rank === 1 ? '🥇' : ($rank === 2 ? '🥈' : ($rank === 3 ? '🥉' : '#' . $rank));
                    $rowStyle = $rank === 1 ? 'background:rgba(212,160,23,.07);' : ($rank === 2 ? 'background:rgba(170,170,187,.04);' : '');
                ?>
                <tr style="<?= $rowStyle ?>">
                    <td style="font-family:'Cinzel',serif; font-size:1.1rem; text-align:center; color:var(--gold)"><?= $medal ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:14px; height:14px; border-radius:3px; background:<?= sanitize($team['color']) ?>; flex-shrink:0; box-shadow:0 0 8px <?= sanitize($team['color']) ?>88;"></div>
                            <span style="font-family:'Cinzel',serif; font-weight:600; color:var(--text-bright)"><?= sanitize($team['name']) ?></span>
                        </div>
                    </td>
                    <td style="color:var(--text-dim)"><?= $team['member_count'] ?> joueur<?= $team['member_count'] > 1 ? 's' : '' ?></td>
                    <td style="text-align:right; font-family:'Cinzel Decorative',serif; font-size:1.1rem; color:var(--gold-light)"><?= $team['total_points'] ?> <span style="font-size:.65rem; color:var(--text-dim)">pts</span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- PAR ÉPREUVE -->
<?php foreach ($trials as $trial): ?>
<div id="tab-trial-<?= $trial['id'] ?>" class="tab-content" style="display:none;">
    <div class="card">
        <div class="card-title">
            ⚔ <?= sanitize($trial['name']) ?>
            <?php if ($trial['status'] === 'active'): ?>
                <span class="badge badge-green" style="margin-left:.5rem">EN COURS</span>
            <?php elseif ($trial['status'] === 'finished'): ?>
                <span class="badge badge-gray" style="margin-left:.5rem">TERMINÉE</span>
            <?php else: ?>
                <span class="badge badge-gray" style="margin-left:.5rem">À VENIR</span>
            <?php endif; ?>
        </div>
        <?php if ($trial['description']): ?>
            <p style="color:var(--text-dim); margin-bottom:1.25rem; font-size:.9rem; line-height:1.7;"><?= sanitize($trial['description']) ?></p>
        <?php endif; ?>

        <?php
        $results = $db->prepare("SELECT tr.*, t.name as team_name, t.color FROM trial_results tr JOIN teams t ON t.id = tr.team_id WHERE tr.trial_id = ? ORDER BY tr.points DESC");
        $results->execute([$trial['id']]);
        $rows = $results->fetchAll();
        ?>

        <?php if (empty($rows)): ?>
            <p style="color:var(--text-dim); text-align:center; padding:2rem">Aucun résultat enregistré pour cette épreuve.</p>
        <?php else: ?>
        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Rang</th>
                    <th>Équipe</th>
                    <th style="text-align:right">Points</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rows as $j => $row):
                    $r = $j + 1;
                    $m = $r===1?'🥇':($r===2?'🥈':($r===3?'🥉':'#'.$r));
                ?>
                <tr>
                    <td style="font-family:'Cinzel',serif; color:var(--gold)"><?= $m ?></td>
                    <td>
                        <div style="display:flex; align-items:center; gap:.75rem;">
                            <div style="width:12px;height:12px;border-radius:2px;background:<?= sanitize($row['color']) ?>;flex-shrink:0"></div>
                            <span style="font-family:'Cinzel',serif; color:var(--text-bright)"><?= sanitize($row['team_name']) ?></span>
                        </div>
                    </td>
                    <td style="text-align:right; font-family:'Cinzel Decorative',serif; color:var(--gold-light)"><?= $row['points'] ?> pts</td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>
<?php endforeach; ?>

<script>
function showTab(id) {
    document.querySelectorAll('.tab-content').forEach(el => el.style.display = 'none');
    document.getElementById('tab-' + id).style.display = 'block';
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.style.background = 'transparent';
        btn.style.borderColor = 'var(--border)';
        btn.style.color = 'var(--text-dim)';
    });
    event.target.style.background = 'rgba(212,160,23,.15)';
    event.target.style.borderColor = 'var(--gold)';
    event.target.style.color = 'var(--gold)';
}
</script>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
