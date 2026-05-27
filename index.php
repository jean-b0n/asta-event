<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Accueil';
include __DIR__ . '/includes/header.php';

$db = getDB();
$competStarted = isCompetitionStarted();

// Stats rapides
$totalPlayers = $db->query("SELECT COUNT(*) FROM users WHERE role='player'")->fetchColumn();
$totalTeams   = $db->query("SELECT COUNT(*) FROM teams")->fetchColumn();
$totalTrials  = $db->query("SELECT COUNT(*) FROM trials")->fetchColumn();
?>

<!-- HERO -->
<section style="text-align:center; padding: 4rem 1rem 3rem; position:relative;">
    <div style="position:absolute;inset:0;overflow:hidden;pointer-events:none;z-index:0">
        <div style="position:absolute;top:-40px;left:50%;transform:translateX(-50%);width:600px;height:300px;background:radial-gradient(ellipse, rgba(212,160,23,.15) 0%, transparent 70%);"></div>
    </div>
    <div style="position:relative;z-index:1">
        <div style="font-size:3.5rem;margin-bottom:1rem;filter:drop-shadow(0 0 20px rgba(212,160,23,.4))">⚔</div>
        <h1 style="font-family:'Cinzel Decorative',serif; font-size:clamp(2.2rem,7vw,4.5rem); font-weight:900; color:var(--gold-light); text-shadow:0 0 60px rgba(240,192,64,.4); line-height:1.1; margin-bottom:.5rem;">
            ASTAS<span style="color:var(--green)">CUP</span>
        </h1>
        <div style="font-family:'Cinzel',serif; font-size:1.1rem; color:var(--gold); letter-spacing:.3em; text-transform:uppercase; margin-bottom:1.5rem; opacity:.8">Version 1 — Minecraft Event</div>
        <div class="divider-gold"></div>
        <p style="max-width:680px; margin:1.5rem auto; font-size:1.05rem; line-height:1.85; color:var(--text);">
            L'AstasCup V1, créé par <strong style="color:var(--gold)">Enoe_one</strong>, fondateur du serveur <strong style="color:var(--green)">Astasia</strong>, est un événement Minecraft multi-épreuves où le fun prime avant tout.
            Une dizaine d'épreuves seront révélées au fil de la compétition, sur plusieurs jours et plusieurs serveurs.
        </p>
        <div style="display:flex; gap:1rem; justify-content:center; flex-wrap:wrap;">
            <?php if (!isLoggedIn()): ?>
                <a href="/rejoindre.php" class="btn btn-gold">⚔ Rejoindre l'aventure</a>
            <?php endif; ?>
            <a href="/regles.php" class="btn btn-outline">📜 Voir les règles</a>
            <a href="/classement.php" class="btn btn-outline">🏆 Classement</a>
        </div>
    </div>
</section>

<!-- STATS -->
<section style="margin:2rem 0 3rem;">
    <div class="grid-3">
        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; margin-bottom:.5rem">🎮</div>
            <div style="font-family:'Cinzel Decorative',serif; font-size:2rem; color:var(--gold-light); font-weight:900"><?= $totalPlayers ?></div>
            <div style="font-family:'Cinzel',serif; font-size:.75rem; color:var(--text-dim); text-transform:uppercase; letter-spacing:.1em; margin-top:.25rem">Participants</div>
        </div>
        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; margin-bottom:.5rem">⚔</div>
            <div style="font-family:'Cinzel Decorative',serif; font-size:2rem; color:var(--gold-light); font-weight:900"><?= $totalTeams ?></div>
            <div style="font-family:'Cinzel',serif; font-size:.75rem; color:var(--text-dim); text-transform:uppercase; letter-spacing:.1em; margin-top:.25rem">Équipes</div>
        </div>
        <div class="card" style="text-align:center;">
            <div style="font-size:2.5rem; margin-bottom:.5rem">🏆</div>
            <div style="font-family:'Cinzel Decorative',serif; font-size:2rem; color:var(--gold-light); font-weight:900">~10</div>
            <div style="font-family:'Cinzel',serif; font-size:.75rem; color:var(--text-dim); text-transform:uppercase; letter-spacing:.1em; margin-top:.25rem">Épreuves prévues</div>
        </div>
    </div>
</section>

<!-- INFOS EVENT -->
<section style="margin-bottom:3rem;">
    <div class="grid-2">
        <!-- Prix -->
        <div class="card">
            <div class="card-title">🏅 Récompenses</div>
            <div style="display:flex; flex-direction:column; gap:1rem;">
                <div style="display:flex; align-items:center; gap:1rem; padding:1rem; background:var(--card2); border-radius:6px; border:1px solid rgba(212,160,23,.2);">
                    <div style="font-size:2rem">🥇</div>
                    <div>
                        <div style="font-family:'Cinzel',serif; color:var(--gold-light); font-weight:700">1ère place</div>
                        <div style="font-size:.85rem; color:var(--text); margin-top:.25rem">L'énorme, l'astronomique somme de <strong style="color:var(--gold-light); font-size:1.1rem">1,00 €</strong> pour toute l'équipe 🎉</div>
                    </div>
                </div>
                <div style="display:flex; align-items:center; gap:1rem; padding:1rem; background:var(--card2); border-radius:6px; border:1px solid rgba(90,110,122,.3);">
                    <div style="font-size:2rem">🥈</div>
                    <div>
                        <div style="font-family:'Cinzel',serif; color:#aab; font-weight:700">2ème place</div>
                        <div style="font-size:.85rem; color:var(--text); margin-top:.25rem">La somme astronomique de <strong style="color:#aab; font-size:1.1rem">0,50 €</strong> pour l'équipe entière 💸</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Format -->
        <div class="card">
            <div class="card-title">📋 Format de l'événement</div>
            <ul style="display:flex; flex-direction:column; gap:.85rem; list-style:none;">
                <li style="display:flex; gap:.75rem; align-items:flex-start;">
                    <span style="color:var(--gold); font-size:1.1rem; flex-shrink:0">⏳</span>
                    <span style="font-size:.9rem; line-height:1.6">L'événement se déroule sur <strong style="color:var(--text-bright)">plusieurs jours</strong></span>
                </li>
                <li style="display:flex; gap:.75rem; align-items:flex-start;">
                    <span style="color:var(--gold); font-size:1.1rem; flex-shrink:0">🎲</span>
                    <span style="font-size:.9rem; line-height:1.6">Les <strong style="color:var(--text-bright)">équipes et épreuves</strong> sont tirées au sort</span>
                </li>
                <li style="display:flex; gap:.75rem; align-items:flex-start;">
                    <span style="color:var(--gold); font-size:1.1rem; flex-shrink:0">🌍</span>
                    <span style="font-size:.9rem; line-height:1.6">Les épreuves se déroulent sur <strong style="color:var(--text-bright)">différents serveurs</strong></span>
                </li>
                <li style="display:flex; gap:.75rem; align-items:flex-start;">
                    <span style="color:var(--gold); font-size:1.1rem; flex-shrink:0">📊</span>
                    <span style="font-size:.9rem; line-height:1.6">Le classement est mis à jour <strong style="color:var(--text-bright)">chaque fin de journée</strong></span>
                </li>
                <li style="display:flex; gap:.75rem; align-items:flex-start;">
                    <span style="color:var(--gold); font-size:1.1rem; flex-shrink:0">🎯</span>
                    <span style="font-size:.9rem; line-height:1.6">Les épreuves seront <strong style="color:var(--text-bright)">annoncées en cours d'événement</strong></span>
                </li>
            </ul>
        </div>
    </div>
</section>

<!-- STATUS COMPET -->
<section style="margin-bottom:2rem;">
    <div class="card" style="text-align:center; padding:2.5rem;">
        <?php if ($competStarted): ?>
            <div style="font-size:2.5rem; margin-bottom:.75rem">🔥</div>
            <div style="font-family:'Cinzel Decorative',serif; color:var(--green); font-size:1.5rem; margin-bottom:.5rem">LA COMPÉTITION EST EN COURS !</div>
            <p style="color:var(--text-dim);">Consultez le <a href="/classement.php" style="color:var(--gold)">classement en direct</a> et la <a href="/equipes.php" style="color:var(--gold)">répartition des équipes</a>.</p>
        <?php else: ?>
            <div style="font-size:2.5rem; margin-bottom:.75rem; animation:pulse 2.5s ease-in-out infinite">⏳</div>
            <div style="font-family:'Cinzel Decorative',serif; color:var(--gold); font-size:1.3rem; margin-bottom:.5rem">En attente du lancement</div>
            <p style="color:var(--text-dim); max-width:480px; margin:0 auto; line-height:1.7;">La compétition n'a pas encore débuté. Inscris-toi dès maintenant et Enoe_one te contactera sur Discord pour confirmer ta participation.</p>
            <div style="margin-top:1.5rem">
                <a href="/rejoindre.php" class="btn btn-gold">Préinscrire ma participation</a>
            </div>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
