<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Règles';
include __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <h1>📜 Règles de l'AstasCup</h1>
    <div class="divider-gold"></div>
    <p>Règles à respecter par tous les participants pendant l'événement. Leur non-respect entraîne des sanctions pouvant aller jusqu'à la disqualification.</p>
</div>

<div style="max-width:860px; margin:0 auto; display:flex; flex-direction:column; gap:1.75rem;">

    <!-- Section 1 -->
    <div class="card">
        <div class="card-title">⚔ 1. Comportement général</div>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:.9rem;">
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Le <strong style="color:var(--text-bright)">respect</strong> entre tous les joueurs est obligatoire. Les insultes, le harcèlement et le comportement toxique sont strictement interdits.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Toute forme de <strong style="color:var(--text-bright)">discrimination</strong> (racisme, sexisme, homophobie, etc.) entraîne une disqualification immédiate et définitive.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">L'esprit sportif est la priorité. <strong style="color:var(--gold)">L'AstasCup, c'est avant tout s'amuser ensemble !</strong></span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Les décisions de l'organisateur (<strong style="color:var(--text-bright)">Enoe_one</strong>) sont finales et sans appel.</span>
            </li>
        </ul>
    </div>

    <!-- Section 2 -->
    <div class="card">
        <div class="card-title">🎮 2. Règles en jeu</div>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:.9rem;">
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;"><strong style="color:var(--red)">Le cheating est interdit</strong> : hacks, clients modifiés non autorisés, exploitation de bugs sont prohibés. Cela entraîne une disqualification immédiate.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Jouer uniquement avec le <strong style="color:var(--text-bright)">compte fourni par Enoe_one</strong>. Partager ou utiliser le compte d'un autre joueur est interdit.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Le <strong style="color:var(--text-bright)">griefing volontaire</strong> des zones non autorisées est interdit selon les règles de chaque épreuve.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Toutes les <strong style="color:var(--text-bright)">épreuves sont tirées au sort</strong> — aucun joueur ne peut connaître les épreuves à l'avance.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Se connecter à l'heure convenue. Tout retard non signalé peut entraîner un <strong style="color:var(--text-bright)">forfait pour l'épreuve</strong>.</span>
            </li>
        </ul>
    </div>

    <!-- Section 3 -->
    <div class="card">
        <div class="card-title">👥 3. Équipes</div>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:.9rem;">
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Les équipes sont formées par <strong style="color:var(--text-bright)">tirage au sort</strong> par l'organisateur. Aucune modification n'est possible.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">La <strong style="color:var(--text-bright)">communication et la coopération</strong> au sein de l'équipe sont encouragées. L'usage d'un micro est recommandé.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Tout <strong style="color:var(--red)">sabotage intentionnel</strong> de sa propre équipe entraîne une disqualification.</span>
            </li>
        </ul>
    </div>

    <!-- Section 4 -->
    <div class="card">
        <div class="card-title">📊 4. Classement & Points</div>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:.9rem;">
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Le classement est mis à jour <strong style="color:var(--text-bright)">chaque fin de journée</strong> par l'organisateur.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Les points sont attribués à <strong style="color:var(--text-bright)">chaque épreuve</strong> selon le classement obtenu. Le détail des points sera communiqué avant chaque épreuve.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">En cas d'égalité finale, l'organisateur désigne le vainqueur selon des critères qu'il définit.</span>
            </li>
        </ul>
    </div>

    <!-- Section 5 -->
    <div class="card">
        <div class="card-title">💬 5. Communication</div>
        <ul style="list-style:none; display:flex; flex-direction:column; gap:.9rem;">
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Toutes les <strong style="color:var(--text-bright)">annonces officielles</strong> se font via Discord. Il est obligatoire d'y être présent.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">Les <strong style="color:var(--text-bright)">réclamations</strong> doivent être adressées à Enoe_one uniquement, en message privé Discord, dans le respect.</span>
            </li>
            <li style="display:flex; gap:.75rem; align-items:flex-start;">
                <span style="color:var(--gold); flex-shrink:0; margin-top:.1rem">▸</span>
                <span style="line-height:1.7;">La langue principale de l'événement est le <strong style="color:var(--text-bright)">français</strong>.</span>
            </li>
        </ul>
    </div>

    <!-- Footer Rules -->
    <div class="card" style="text-align:center; background:rgba(212,160,23,.04); border-color:var(--gold-dim);">
        <div style="font-family:'Cinzel Decorative',serif; color:var(--gold); font-size:1rem; margin-bottom:.5rem;">⚔ Bon jeu à tous !</div>
        <p style="color:var(--text-dim); font-size:.9rem; line-height:1.7;">
            Ces règles ont pour seul objectif de garantir que l'AstasCup soit une expérience agréable pour tout le monde.<br>
            En participant, vous les acceptez intégralement.
        </p>
        <p style="color:var(--text-dim); font-size:.8rem; margin-top:.75rem; font-style:italic;">— Enoe_one, fondateur du serveur Astasia</p>
    </div>

</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
