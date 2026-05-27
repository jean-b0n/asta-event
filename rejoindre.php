<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Rejoindre';

$error = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $minecraft = trim($_POST['minecraft_pseudo'] ?? '');
    $discord   = trim($_POST['discord_pseudo'] ?? '');

    if (!$minecraft || !$discord) {
        $error = 'Merci de renseigner ton pseudo Minecraft et Discord.';
    } else {
        $db = getDB();
        // Vérifier si déjà candidaté
        $check = $db->prepare("SELECT id FROM join_requests WHERE minecraft_pseudo = ? OR discord_pseudo = ?");
        $check->execute([$minecraft, $discord]);
        if ($check->fetch()) {
            $error = 'Une demande existe déjà avec ce pseudo Minecraft ou Discord.';
        } else {
            // Récupérer les réponses au questionnaire
            $answers = [];
            $questions = getQuestions();
            foreach ($questions as $i => $q) {
                $answers[] = [
                    'question' => $q,
                    'answer'   => trim($_POST['q' . $i] ?? '')
                ];
            }
            $stmt = $db->prepare("INSERT INTO join_requests (minecraft_pseudo, discord_pseudo, answers) VALUES (?, ?, ?)");
            $stmt->execute([$minecraft, $discord, json_encode($answers, JSON_UNESCAPED_UNICODE)]);
            $success = true;
        }
    }
}

// Les questions (tu peux les modifier ici !)
function getQuestions(): array {
    return [
        'Quel est ton pseudo Minecraft ?',
        'Quel est ton pseudo Discord ?',
        'Depuis combien de temps joues-tu à Minecraft ?',
        'Quel est ton mode de jeu préféré sur Minecraft ?',
        'As-tu déjà participé à des events Minecraft ? Si oui, lesquels ?',
        'Que recherches-tu dans cet événement ?',
        'Es-tu plutôt joueur solo ou team player ?',
        'Comment as-tu entendu parler de l\'AstasCup ?',
        'As-tu des compétences particulières utiles en équipe ? (Redstone, PVP, construction...)',
        'Quelle est ta disponibilité approximative pour l\'événement ?',
        'As-tu un micro fonctionnel pour communiquer avec ton équipe ?',
        'Quelque chose à ajouter pour te présenter ?',
    ];
}

include __DIR__ . '/includes/header.php';
?>

<div class="page-hero">
    <h1>⚔ Rejoindre l'AstasCup</h1>
    <div class="divider-gold"></div>
    <p>Remplis le questionnaire ci-dessous. Enoe_one te contactera sur Discord pour confirmer ta participation et te donner ton accès.</p>
</div>

<?php if ($success): ?>
<div class="card" style="text-align:center; padding:3rem; max-width:600px; margin:0 auto;">
    <div style="font-size:3rem; margin-bottom:1rem">✅</div>
    <h2 style="font-family:'Cinzel Decorative',serif; color:var(--green); margin-bottom:.75rem">Demande envoyée !</h2>
    <p style="color:var(--text-dim); line-height:1.8">
        Ta demande de participation a bien été enregistrée.<br>
        <strong style="color:var(--gold)">Enoe_one</strong> te contactera sur Discord afin de te donner ton compte et confirmer ta participation à l'AstasCup V1.<br><br>
        <span style="color:var(--text-dim); font-size:.85rem">Assure-toi d'être accessible sur Discord !</span>
    </p>
    <div style="margin-top:2rem">
        <a href="/index.php" class="btn btn-outline">← Retour à l'accueil</a>
    </div>
</div>

<?php else: ?>

<?php if ($error): ?>
<div class="alert alert-error" style="max-width:700px; margin:0 auto 1.5rem;"><?= sanitize($error) ?></div>
<?php endif; ?>

<div style="max-width:700px; margin:0 auto;">
    <!-- Info Banner -->
    <div class="alert alert-info" style="margin-bottom:1.5rem;">
        📌 Une fois le questionnaire complété, <strong>Enoe_one vous recontactera sur Discord</strong> afin de vous donner votre compte de connexion.
    </div>

    <div class="card">
        <div class="card-title">📝 Questionnaire de candidature</div>
        <form method="POST" id="joinForm">
            <!-- Pseudos -->
            <div class="grid-2" style="margin-bottom:1.5rem;">
                <div class="form-group" style="margin-bottom:0">
                    <label>🎮 Pseudo Minecraft *</label>
                    <input type="text" name="minecraft_pseudo" required placeholder="Ton pseudo exact sur Minecraft" value="<?= sanitize($_POST['minecraft_pseudo'] ?? '') ?>">
                </div>
                <div class="form-group" style="margin-bottom:0">
                    <label>💬 Pseudo Discord *</label>
                    <input type="text" name="discord_pseudo" required placeholder="Pseudo#0000 ou nouveau format" value="<?= sanitize($_POST['discord_pseudo'] ?? '') ?>">
                </div>
            </div>

            <div style="border-top:1px solid var(--border); margin:1.5rem 0; padding-top:1.5rem;">
                <div style="font-family:'Cinzel',serif; font-size:.8rem; color:var(--gold); text-transform:uppercase; letter-spacing:.08em; margin-bottom:1.25rem;">Questions sur toi</div>

                <?php foreach (getQuestions() as $i => $question):
                    // Skip les 2 premières (pseudo MC + Discord déjà au dessus)
                    if ($i < 2) continue;
                ?>
                <div class="form-group">
                    <label>Q<?= $i ?>. <?= sanitize($question) ?></label>
                    <?php if ($i >= 10): ?>
                        <textarea name="q<?= $i ?>" placeholder="Ta réponse..."><?= sanitize($_POST['q'.$i] ?? '') ?></textarea>
                    <?php else: ?>
                        <input type="text" name="q<?= $i ?>" placeholder="Ta réponse..." value="<?= sanitize($_POST['q'.$i] ?? '') ?>">
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>

                <!-- Hidden: on envoie aussi les pseudos dans les answers -->
                <input type="hidden" name="q0" value="<?= sanitize($_POST['minecraft_pseudo'] ?? '') ?>">
                <input type="hidden" name="q1" value="<?= sanitize($_POST['discord_pseudo'] ?? '') ?>">
            </div>

            <div style="text-align:center; margin-top:1.5rem;">
                <button type="submit" class="btn btn-gold" style="padding:.8rem 2.5rem; font-size:.85rem;">
                    ⚔ Envoyer ma candidature
                </button>
                <p style="color:var(--text-dim); font-size:.78rem; margin-top:.75rem;">Aucun spam — uniquement contacté pour l'AstasCup.</p>
            </div>
        </form>
    </div>
</div>

<?php endif; ?>

<?php include __DIR__ . '/includes/footer.php'; ?>
