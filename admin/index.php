<?php
require_once __DIR__ . '/../includes/config.php';
requireAdmin();
$pageTitle = 'Admin Dashboard';

$db = getDB();
$tab = $_GET['tab'] ?? 'comptes';

// Récupérer les données selon l'onglet
$requests   = $db->query("SELECT * FROM join_requests ORDER BY created_at DESC")->fetchAll();
$pendingCount = count(array_filter($requests, fn($r) => $r['status'] === 'pending'));
$teams      = $db->query("SELECT t.*, COUNT(u.id) as member_count FROM teams t LEFT JOIN users u ON u.team_id = t.id GROUP BY t.id ORDER BY t.total_points DESC")->fetchAll();
$players    = $db->query("SELECT u.*, t.name as team_name FROM users u LEFT JOIN teams t ON u.team_id = t.id WHERE u.role='player' ORDER BY u.created_at DESC")->fetchAll();
$trials     = $db->query("SELECT * FROM trials ORDER BY trial_order ASC")->fetchAll();
$allTeams   = $db->query("SELECT * FROM teams ORDER BY name ASC")->fetchAll();

$competStarted  = getSetting('competition_started');
$teamsRevealed  = getSetting('teams_revealed');
$competName     = getSetting('competition_name');

// Messages flash
$flash = $_SESSION['flash'] ?? null;
unset($_SESSION['flash']);

include __DIR__ . '/../includes/header.php';
?>

<div style="display:flex; align-items:center; justify-content:space-between; margin-bottom:2.5rem; flex-wrap:wrap; gap:1rem;">
    <div>
        <h1 style="font-family:'Cinzel Decorative',serif; font-size:1.8rem; color:var(--gold-light);">⚙ Dashboard Admin</h1>
        <p style="color:var(--text-dim); font-size:.85rem; margin-top:.25rem;">Bienvenue, <strong style="color:var(--gold)">Enoe_one</strong></p>
    </div>
    <div style="display:flex; gap:.5rem; flex-wrap:wrap;">
        <span class="badge <?= $competStarted==='1' ? 'badge-green' : 'badge-gray' ?>"><?= $competStarted==='1' ? '🔥 Compét active' : '⏳ Compét inactive' ?></span>
        <span class="badge <?= $teamsRevealed==='1' ? 'badge-gold' : 'badge-gray' ?>"><?= $teamsRevealed==='1' ? '👥 Équipes révélées' : '🎲 Équipes masquées' ?></span>
    </div>
</div>

<?php if ($flash): ?>
<div class="alert alert-<?= $flash['type'] ?>" style="margin-bottom:1.5rem;"><?= sanitize($flash['msg']) ?></div>
<?php endif; ?>

<!-- ONGLETS ADMIN -->
<div style="display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:2rem; border-bottom:1px solid var(--border); padding-bottom:1rem;">
    <?php
    $tabs = [
        'comptes'    => ['icon' => '👤', 'label' => 'Créer des comptes'],
        'demandes'   => ['icon' => '📋', 'label' => 'Demandes' . ($pendingCount ? " <span class='badge badge-red' style='font-size:.6rem'>$pendingCount</span>" : '')],
        'parametres' => ['icon' => '⚙', 'label' => 'Paramètres'],
        'classement' => ['icon' => '🏆', 'label' => 'Équipes & Classement'],
    ];
    foreach ($tabs as $key => $t):
    ?>
    <a href="?tab=<?= $key ?>" class="btn <?= $tab === $key ? 'btn-gold' : 'btn-outline' ?> btn-sm">
        <?= $t['icon'] ?> <?= $t['label'] ?>
    </a>
    <?php endforeach; ?>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- TAB: CRÉER DES COMPTES                     -->
<!-- ═══════════════════════════════════════════ -->
<?php if ($tab === 'comptes'): ?>

<div class="grid-2" style="align-items:start;">
    <!-- Formulaire création compte -->
    <div class="card">
        <div class="card-title">➕ Créer un compte joueur</div>
        <form method="POST" action="/admin/actions.php">
            <input type="hidden" name="action" value="create_account">
            <div class="form-group">
                <label>Identifiant (login)</label>
                <input type="text" name="username" required placeholder="Ex: Steve_42">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="text" name="password" required placeholder="Mot de passe temporaire">
            </div>
            <div class="form-group">
                <label>Pseudo Minecraft</label>
                <input type="text" name="minecraft_pseudo" placeholder="Pseudo exact Minecraft">
            </div>
            <div class="form-group">
                <label>Pseudo Discord</label>
                <input type="text" name="discord_pseudo" placeholder="Pseudo Discord">
            </div>
            <div class="form-group">
                <label>Assigner à une équipe</label>
                <select name="team_id">
                    <option value="">— Aucune équipe —</option>
                    <?php foreach ($allTeams as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= sanitize($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">✅ Créer le compte</button>
        </form>
    </div>

    <!-- Liste des joueurs -->
    <div class="card">
        <div class="card-title">👥 Joueurs enregistrés (<?= count($players) ?>)</div>
        <?php if (empty($players)): ?>
            <p style="color:var(--text-dim); text-align:center; padding:1.5rem">Aucun joueur.</p>
        <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:.5rem; max-height:480px; overflow-y:auto;">
            <?php foreach ($players as $p): ?>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:.65rem 1rem; background:var(--card2); border-radius:4px; border:1px solid var(--border); gap:.5rem; flex-wrap:wrap;">
                <div>
                    <div style="font-family:'Cinzel',serif; font-size:.82rem; color:var(--text-bright);"><?= sanitize($p['username']) ?></div>
                    <div style="font-size:.72rem; color:var(--text-dim);">
                        🎮 <?= $p['minecraft_pseudo'] ? sanitize($p['minecraft_pseudo']) : '—' ?>
                        &nbsp;·&nbsp; 💬 <?= $p['discord_pseudo'] ? sanitize($p['discord_pseudo']) : '—' ?>
                    </div>
                    <?php if ($p['team_name']): ?><span class="badge badge-gold" style="font-size:.55rem; margin-top:.25rem"><?= sanitize($p['team_name']) ?></span><?php endif; ?>
                </div>
                <form method="POST" action="/admin/actions.php" onsubmit="return confirm('Supprimer ce joueur ?')">
                    <input type="hidden" name="action" value="delete_player">
                    <input type="hidden" name="player_id" value="<?= $p['id'] ?>">
                    <button type="submit" class="btn btn-red btn-sm">🗑</button>
                </form>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- TAB: DEMANDES                              -->
<!-- ═══════════════════════════════════════════ -->
<?php elseif ($tab === 'demandes'): ?>

<div class="card">
    <div class="card-title">📋 Demandes de participation (<?= count($requests) ?>)</div>
    <?php if (empty($requests)): ?>
        <p style="color:var(--text-dim); text-align:center; padding:2rem">Aucune demande reçue.</p>
    <?php else: ?>
    <div style="display:flex; flex-direction:column; gap:1rem;">
        <?php foreach ($requests as $req): ?>
        <?php $answers = json_decode($req['answers'], true) ?? []; ?>
        <div style="border:1px solid var(--border); border-radius:6px; overflow:hidden; <?= $req['status']==='pending' ? 'border-color:var(--gold-dim)' : '' ?>">
            <!-- En-tête -->
            <div style="display:flex; align-items:center; justify-content:space-between; padding:.85rem 1.25rem; background:var(--card2); flex-wrap:wrap; gap:.5rem;">
                <div>
                    <span style="font-family:'Cinzel',serif; color:var(--text-bright)">🎮 <?= sanitize($req['minecraft_pseudo']) ?></span>
                    <span style="color:var(--text-dim); margin:0 .5rem">·</span>
                    <span style="color:#7289da">💬 <?= sanitize($req['discord_pseudo']) ?></span>
                    <span style="color:var(--text-dim); font-size:.75rem; margin-left:.75rem"><?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></span>
                </div>
                <div style="display:flex; gap:.5rem; align-items:center;">
                    <?php if ($req['status'] === 'pending'): ?>
                        <span class="badge badge-gold">⏳ En attente</span>
                        <form method="POST" action="/admin/actions.php" style="display:inline">
                            <input type="hidden" name="action" value="update_request">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <input type="hidden" name="status" value="accepted">
                            <button type="submit" class="btn btn-green btn-sm">✅ Accepter</button>
                        </form>
                        <form method="POST" action="/admin/actions.php" style="display:inline">
                            <input type="hidden" name="action" value="update_request">
                            <input type="hidden" name="request_id" value="<?= $req['id'] ?>">
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-red btn-sm">❌ Refuser</button>
                        </form>
                    <?php elseif ($req['status'] === 'accepted'): ?>
                        <span class="badge badge-green">✅ Accepté</span>
                    <?php else: ?>
                        <span class="badge badge-red">❌ Refusé</span>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Réponses (collapsible) -->
            <details>
                <summary style="padding:.6rem 1.25rem; cursor:pointer; color:var(--text-dim); font-size:.82rem; user-select:none; list-style:none;">
                    ▸ Voir les réponses (<?= count($answers) ?>)
                </summary>
                <div style="padding:1rem 1.25rem; display:flex; flex-direction:column; gap:.65rem;">
                    <?php foreach ($answers as $ans): ?>
                    <div style="padding:.6rem .9rem; background:var(--card2); border-radius:4px; border-left:2px solid var(--gold-dim);">
                        <div style="font-size:.72rem; color:var(--text-dim); margin-bottom:.2rem; font-family:'Cinzel',serif; text-transform:uppercase; letter-spacing:.06em;"><?= sanitize($ans['question']) ?></div>
                        <div style="font-size:.85rem; color:var(--text-bright);"><?= $ans['answer'] ? sanitize($ans['answer']) : '<em style="color:var(--text-dim)">Non renseigné</em>' ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </details>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- TAB: PARAMÈTRES                            -->
<!-- ═══════════════════════════════════════════ -->
<?php elseif ($tab === 'parametres'): ?>

<div class="grid-2" style="align-items:start;">
    <!-- Contrôle de la compétition -->
    <div class="card">
        <div class="card-title">🔥 Contrôle de la compétition</div>

        <div style="display:flex; flex-direction:column; gap:1rem;">
            <!-- Toggle compét -->
            <div style="padding:1.25rem; background:var(--card2); border-radius:6px; border:1px solid var(--border);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                    <div>
                        <div style="font-family:'Cinzel',serif; font-size:.85rem; color:var(--text-bright); margin-bottom:.3rem;">Lancer / Arrêter la compétition</div>
                        <div style="font-size:.78rem; color:var(--text-dim);">Active l'affichage du classement et des équipes.</div>
                    </div>
                    <form method="POST" action="/admin/actions.php">
                        <input type="hidden" name="action" value="toggle_competition">
                        <button type="submit" class="btn <?= $competStarted==='1' ? 'btn-red' : 'btn-green' ?>">
                            <?= $competStarted==='1' ? '⏸ Arrêter' : '▶ Lancer' ?>
                        </button>
                    </form>
                </div>
            </div>

            <!-- Toggle équipes -->
            <div style="padding:1.25rem; background:var(--card2); border-radius:6px; border:1px solid var(--border);">
                <div style="display:flex; align-items:center; justify-content:space-between; gap:1rem; flex-wrap:wrap;">
                    <div>
                        <div style="font-family:'Cinzel',serif; font-size:.85rem; color:var(--text-bright); margin-bottom:.3rem;">Révéler les équipes</div>
                        <div style="font-size:.78rem; color:var(--text-dim);">Rend la répartition des équipes visible aux joueurs.</div>
                    </div>
                    <form method="POST" action="/admin/actions.php">
                        <input type="hidden" name="action" value="toggle_teams">
                        <button type="submit" class="btn <?= $teamsRevealed==='1' ? 'btn-red' : 'btn-gold' ?>">
                            <?= $teamsRevealed==='1' ? '🙈 Masquer' : '👁 Révéler' ?>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Gestion épreuves -->
    <div class="card">
        <div class="card-title">⚔ Gestion des épreuves</div>

        <!-- Créer épreuve -->
        <form method="POST" action="/admin/actions.php" style="margin-bottom:1.5rem;">
            <input type="hidden" name="action" value="create_trial">
            <div class="form-group">
                <label>Nom de l'épreuve</label>
                <input type="text" name="trial_name" required placeholder="Ex: Course en kit PVP">
            </div>
            <div class="form-group">
                <label>Description (optionnel)</label>
                <input type="text" name="trial_desc" placeholder="Courte description">
            </div>
            <div class="grid-2" style="gap:.75rem">
                <div class="form-group" style="margin-bottom:0">
                    <label>Ordre</label>
                    <input type="number" name="trial_order" value="<?= count($trials)+1 ?>" min="1">
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>Statut initial</label>
                    <select name="trial_status">
                        <option value="pending">En attente</option>
                        <option value="active">En cours</option>
                    </select>
                </div>
            </div>
            <button type="submit" class="btn btn-green btn-sm" style="margin-top:1rem; width:100%; justify-content:center;">➕ Ajouter l'épreuve</button>
        </form>

        <!-- Liste épreuves -->
        <?php if (empty($trials)): ?>
            <p style="color:var(--text-dim); font-size:.85rem; text-align:center;">Aucune épreuve créée.</p>
        <?php else: ?>
        <div style="display:flex; flex-direction:column; gap:.5rem;">
            <?php foreach ($trials as $trial): ?>
            <div style="display:flex; align-items:center; justify-content:space-between; padding:.65rem 1rem; background:var(--card2); border-radius:4px; border:1px solid var(--border); gap:.5rem; flex-wrap:wrap;">
                <div>
                    <span style="font-family:'Cinzel',serif; font-size:.82rem; color:var(--text-bright)"><?= sanitize($trial['name']) ?></span>
                    <?php if ($trial['status']==='active'): ?><span class="badge badge-green" style="font-size:.55rem; margin-left:.35rem">Live</span>
                    <?php elseif ($trial['status']==='finished'): ?><span class="badge badge-gray" style="font-size:.55rem; margin-left:.35rem">Terminée</span>
                    <?php else: ?><span class="badge badge-gray" style="font-size:.55rem; margin-left:.35rem">En attente</span>
                    <?php endif; ?>
                </div>
                <div style="display:flex; gap:.35rem;">
                    <?php if ($trial['status'] !== 'active'): ?>
                    <form method="POST" action="/admin/actions.php" style="display:inline">
                        <input type="hidden" name="action" value="set_trial_status">
                        <input type="hidden" name="trial_id" value="<?= $trial['id'] ?>">
                        <input type="hidden" name="status" value="active">
                        <button class="btn btn-green btn-sm">▶</button>
                    </form>
                    <?php endif; ?>
                    <?php if ($trial['status'] !== 'finished'): ?>
                    <form method="POST" action="/admin/actions.php" style="display:inline">
                        <input type="hidden" name="action" value="set_trial_status">
                        <input type="hidden" name="trial_id" value="<?= $trial['id'] ?>">
                        <input type="hidden" name="status" value="finished">
                        <button class="btn btn-outline btn-sm">✓</button>
                    </form>
                    <?php endif; ?>
                    <form method="POST" action="/admin/actions.php" style="display:inline" onsubmit="return confirm('Supprimer cette épreuve ?')">
                        <input type="hidden" name="action" value="delete_trial">
                        <input type="hidden" name="trial_id" value="<?= $trial['id'] ?>">
                        <button class="btn btn-red btn-sm">🗑</button>
                    </form>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════ -->
<!-- TAB: CLASSEMENT & ÉQUIPES                  -->
<!-- ═══════════════════════════════════════════ -->
<?php elseif ($tab === 'classement'): ?>

<div class="grid-2" style="align-items:start; margin-bottom:2rem;">
    <!-- Créer une équipe -->
    <div class="card">
        <div class="card-title">➕ Créer une équipe</div>
        <form method="POST" action="/admin/actions.php">
            <input type="hidden" name="action" value="create_team">
            <div class="form-group">
                <label>Nom de l'équipe</label>
                <input type="text" name="team_name" required placeholder="Ex: Team Dragon">
            </div>
            <div class="form-group">
                <label>Couleur de l'équipe</label>
                <input type="color" name="team_color" value="#FFD700" style="height:42px; padding:.25rem;">
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center;">➕ Créer l'équipe</button>
        </form>
    </div>

    <!-- Assigner joueur -->
    <div class="card">
        <div class="card-title">🔀 Assigner un joueur à une équipe</div>
        <form method="POST" action="/admin/actions.php">
            <input type="hidden" name="action" value="assign_team">
            <div class="form-group">
                <label>Joueur</label>
                <select name="player_id" required>
                    <option value="">— Choisir un joueur —</option>
                    <?php foreach ($players as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= sanitize($p['username']) ?> <?= $p['minecraft_pseudo'] ? '(' . sanitize($p['minecraft_pseudo']) . ')' : '' ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Équipe</label>
                <select name="team_id" required>
                    <option value="">— Choisir une équipe —</option>
                    <option value="0">Retirer de l'équipe</option>
                    <?php foreach ($allTeams as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= sanitize($t['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn btn-green" style="width:100%; justify-content:center;">✅ Assigner</button>
        </form>
    </div>
</div>

<!-- Mise à jour classement par épreuve -->
<div class="card" style="margin-bottom:2rem;">
    <div class="card-title">📊 Mettre à jour les points par épreuve</div>
    <?php if (empty($trials) || empty($allTeams)): ?>
        <p style="color:var(--text-dim); font-size:.85rem;">Créez d'abord des épreuves et des équipes.</p>
    <?php else: ?>
    <form method="POST" action="/admin/actions.php">
        <input type="hidden" name="action" value="update_points">
        <div class="form-group">
            <label>Épreuve</label>
            <select name="trial_id" required>
                <option value="">— Choisir une épreuve —</option>
                <?php foreach ($trials as $t): ?>
                <option value="<?= $t['id'] ?>"><?= sanitize($t['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div style="overflow-x:auto; margin-bottom:1rem;">
            <table style="width:100%">
                <thead>
                    <tr>
                        <th>Équipe</th>
                        <th>Points à attribuer</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allTeams as $t): ?>
                    <tr>
                        <td style="display:flex; align-items:center; gap:.5rem;">
                            <div style="width:10px;height:10px;border-radius:2px;background:<?= sanitize($t['color']) ?>;flex-shrink:0"></div>
                            <?= sanitize($t['name']) ?>
                        </td>
                        <td>
                            <input type="number" name="points[<?= $t['id'] ?>]" value="0" min="0" style="width:80px; background:var(--card2); border:1px solid var(--border); color:var(--text-bright); padding:.35rem .5rem; border-radius:4px; text-align:center;">
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <button type="submit" class="btn btn-gold">💾 Enregistrer les points</button>
    </form>
    <?php endif; ?>
</div>

<!-- Classement actuel -->
<div class="card">
    <div class="card-title">🏆 Classement actuel (<?= count($teams) ?> équipe<?= count($teams) > 1 ? 's' : '' ?>)</div>
    <?php if (empty($teams)): ?>
        <p style="color:var(--text-dim); text-align:center; padding:2rem">Aucune équipe.</p>
    <?php else: ?>
    <div class="table-wrap">
    <table>
        <thead>
            <tr>
                <th>Rang</th>
                <th>Équipe</th>
                <th>Membres</th>
                <th style="text-align:right">Points</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($teams as $i => $t):
                $r = $i + 1;
                $m = $r===1?'🥇':($r===2?'🥈':($r===3?'🥉':'#'.$r));
            ?>
            <tr>
                <td style="font-family:'Cinzel',serif; color:var(--gold)"><?= $m ?></td>
                <td>
                    <div style="display:flex; align-items:center; gap:.5rem;">
                        <div style="width:12px;height:12px;border-radius:3px;background:<?= sanitize($t['color']) ?>;flex-shrink:0"></div>
                        <span style="font-family:'Cinzel',serif; color:var(--text-bright)"><?= sanitize($t['name']) ?></span>
                    </div>
                </td>
                <td style="color:var(--text-dim)"><?= $t['member_count'] ?></td>
                <td style="text-align:right; font-family:'Cinzel Decorative',serif; color:var(--gold-light)"><?= $t['total_points'] ?> pts</td>
                <td>
                    <form method="POST" action="/admin/actions.php" style="display:inline" onsubmit="return confirm('Supprimer cette équipe ?')">
                        <input type="hidden" name="action" value="delete_team">
                        <input type="hidden" name="team_id" value="<?= $t['id'] ?>">
                        <button class="btn btn-red btn-sm">🗑</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
