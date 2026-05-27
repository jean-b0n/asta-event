<?php
require_once __DIR__ . '/includes/config.php';
$pageTitle = 'Connexion';

if (isLoggedIn()) {
    header('Location: ' . (isAdmin() ? '/admin/index.php' : '/player/dashboard.php'));
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($username && $password) {
        $db = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role']    = $user['role'];
            header('Location: ' . ($user['role'] === 'admin' ? '/admin/index.php' : '/player/dashboard.php'));
            exit;
        } else {
            $error = 'Identifiant ou mot de passe incorrect.';
        }
    } else {
        $error = 'Merci de remplir tous les champs.';
    }
}

include __DIR__ . '/includes/header.php';
?>

<div style="max-width:420px; margin:4rem auto;">
    <div style="text-align:center; margin-bottom:2.5rem;">
        <div style="font-size:3rem; margin-bottom:.75rem; filter:drop-shadow(0 0 20px rgba(212,160,23,.4))">🔐</div>
        <h1 style="font-family:'Cinzel Decorative',serif; font-size:1.8rem; color:var(--gold-light); text-shadow:0 0 30px rgba(240,192,64,.3);">Connexion</h1>
        <div class="divider-gold"></div>
    </div>

    <?php if ($error): ?>
    <div class="alert alert-error"><?= sanitize($error) ?></div>
    <?php endif; ?>

    <div class="card">
        <form method="POST">
            <div class="form-group">
                <label>Identifiant</label>
                <input type="text" name="username" required autofocus placeholder="Ton identifiant" value="<?= sanitize($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Mot de passe</label>
                <input type="password" name="password" required placeholder="••••••••">
            </div>
            <button type="submit" class="btn btn-gold" style="width:100%; justify-content:center; padding:.8rem;">
                ⚔ Se connecter
            </button>
        </form>
    </div>

    <div style="text-align:center; margin-top:1.5rem; color:var(--text-dim); font-size:.85rem;">
        Pas encore de compte ? <a href="/rejoindre.php" style="color:var(--gold)">Rejoindre l'AstasCup</a>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>
