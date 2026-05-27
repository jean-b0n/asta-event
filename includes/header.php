<?php
// includes/header.php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? sanitize($pageTitle) . ' — ' : '' ?><?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cinzel+Decorative:wght@700;900&family=Cinzel:wght@400;600;700&family=Inter:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --black:      #080a0c;
            --dark:       #0e1214;
            --card:       #131820;
            --card2:      #181f28;
            --border:     #1e2a35;
            --gold:       #d4a017;
            --gold-light: #f0c040;
            --gold-dim:   #8a6510;
            --green:      #3ddc3d;
            --green-dim:  #1a5c1a;
            --red:        #e03030;
            --text:       #c8d4dc;
            --text-dim:   #5a6e7a;
            --text-bright:#eef4f8;
            --shadow-gold: 0 0 24px rgba(212,160,23,.25);
            --shadow-green: 0 0 18px rgba(61,220,61,.2);
        }

        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        html { scroll-behavior: smooth; }

        body {
            background-color: var(--black);
            color: var(--text);
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* ─── BACKGROUND TEXTURE ─── */
        body::before {
            content: '';
            position: fixed; inset: 0; z-index: 0;
            background-image:
                radial-gradient(ellipse 80% 50% at 50% -10%, rgba(212,160,23,.12), transparent),
                repeating-linear-gradient(0deg, transparent, transparent 31px, rgba(255,255,255,.012) 32px),
                repeating-linear-gradient(90deg, transparent, transparent 31px, rgba(255,255,255,.012) 32px);
            pointer-events: none;
        }

        /* ─── NAVBAR ─── */
        nav {
            position: sticky; top: 0; z-index: 100;
            background: rgba(8,10,12,.92);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--gold-dim);
            padding: 0 2rem;
        }

        .nav-inner {
            max-width: 1280px; margin: 0 auto;
            display: flex; align-items: center; gap: 0;
            height: 64px;
        }

        .nav-brand {
            font-family: 'Cinzel Decorative', serif;
            font-size: 1.1rem; font-weight: 900;
            color: var(--gold-light);
            text-decoration: none;
            letter-spacing: .04em;
            margin-right: auto;
            text-shadow: 0 0 18px rgba(240,192,64,.5);
            white-space: nowrap;
        }

        .nav-brand span { color: var(--green); }

        .nav-links {
            display: flex; align-items: center; gap: .25rem;
            list-style: none;
        }

        .nav-links a {
            display: block;
            padding: .5rem .85rem;
            color: var(--text-dim);
            text-decoration: none;
            font-family: 'Cinzel', serif;
            font-size: .72rem; font-weight: 600;
            letter-spacing: .08em;
            text-transform: uppercase;
            border-radius: 4px;
            transition: color .2s, background .2s;
        }

        .nav-links a:hover,
        .nav-links a.active {
            color: var(--gold-light);
            background: rgba(212,160,23,.08);
        }

        .nav-links a.active { border-bottom: 2px solid var(--gold); }

        .nav-user {
            display: flex; align-items: center; gap: .5rem;
            margin-left: 1.5rem;
            padding-left: 1.5rem;
            border-left: 1px solid var(--border);
        }

        .nav-user-name {
            font-family: 'Cinzel', serif;
            font-size: .75rem; color: var(--gold);
        }

        .btn {
            display: inline-flex; align-items: center; gap: .4rem;
            padding: .5rem 1.2rem;
            border-radius: 4px;
            font-family: 'Cinzel', serif;
            font-size: .72rem; font-weight: 700;
            letter-spacing: .08em; text-transform: uppercase;
            text-decoration: none;
            border: none; cursor: pointer;
            transition: all .2s;
        }

        .btn-gold {
            background: linear-gradient(135deg, var(--gold), var(--gold-dim));
            color: var(--black);
            box-shadow: 0 2px 12px rgba(212,160,23,.3);
        }
        .btn-gold:hover { background: linear-gradient(135deg, var(--gold-light), var(--gold)); box-shadow: var(--shadow-gold); transform: translateY(-1px); }

        .btn-outline {
            background: transparent;
            color: var(--gold);
            border: 1px solid var(--gold-dim);
        }
        .btn-outline:hover { background: rgba(212,160,23,.08); border-color: var(--gold); }

        .btn-green {
            background: linear-gradient(135deg, var(--green), var(--green-dim));
            color: var(--black);
        }
        .btn-green:hover { filter: brightness(1.15); transform: translateY(-1px); }

        .btn-red {
            background: linear-gradient(135deg, var(--red), #7a1010);
            color: #fff;
        }
        .btn-red:hover { filter: brightness(1.15); }

        .btn-sm { padding: .35rem .8rem; font-size: .65rem; }

        /* ─── LAYOUT ─── */
        main {
            position: relative; z-index: 1;
            max-width: 1280px;
            margin: 0 auto;
            padding: 2.5rem 2rem 4rem;
        }

        /* ─── PAGE TITLE ─── */
        .page-hero {
            text-align: center;
            margin-bottom: 3rem;
            padding: 3rem 1rem 2rem;
        }

        .page-hero h1 {
            font-family: 'Cinzel Decorative', serif;
            font-size: clamp(1.8rem, 5vw, 3.2rem);
            font-weight: 900;
            color: var(--gold-light);
            text-shadow: 0 0 40px rgba(240,192,64,.4);
            margin-bottom: .75rem;
            line-height: 1.15;
        }

        .page-hero p {
            color: var(--text-dim);
            font-size: 1rem;
            max-width: 600px; margin: 0 auto;
            line-height: 1.7;
        }

        .divider-gold {
            width: 120px; height: 2px;
            background: linear-gradient(90deg, transparent, var(--gold), transparent);
            margin: 1.25rem auto;
        }

        /* ─── CARDS ─── */
        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1.75rem;
            transition: border-color .2s, box-shadow .2s;
        }

        .card:hover { border-color: var(--gold-dim); box-shadow: var(--shadow-gold); }

        .card-title {
            font-family: 'Cinzel', serif;
            font-size: .9rem; font-weight: 700;
            color: var(--gold);
            letter-spacing: .06em; text-transform: uppercase;
            margin-bottom: 1.25rem;
            padding-bottom: .75rem;
            border-bottom: 1px solid var(--border);
        }

        /* ─── GRID ─── */
        .grid-2 { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1.5rem; }
        .grid-3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 1.5rem; }
        .grid-4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1.25rem; }

        @media (max-width: 900px) {
            .grid-4 { grid-template-columns: repeat(2,1fr); }
            .grid-3 { grid-template-columns: repeat(2,1fr); }
        }
        @media (max-width: 600px) {
            .grid-2, .grid-3, .grid-4 { grid-template-columns: 1fr; }
            nav .nav-links { display: none; }
            main { padding: 1.5rem 1rem 3rem; }
        }

        /* ─── BADGE ─── */
        .badge {
            display: inline-block;
            padding: .2rem .6rem;
            border-radius: 3px;
            font-size: .65rem; font-weight: 700;
            letter-spacing: .06em; text-transform: uppercase;
        }
        .badge-gold   { background: rgba(212,160,23,.15); color: var(--gold); border: 1px solid var(--gold-dim); }
        .badge-green  { background: rgba(61,220,61,.12);  color: var(--green); border: 1px solid var(--green-dim); }
        .badge-red    { background: rgba(224,48,48,.15);  color: var(--red);  border: 1px solid #7a1010; }
        .badge-gray   { background: rgba(90,110,122,.15); color: var(--text-dim); border: 1px solid var(--border); }

        /* ─── FORMS ─── */
        .form-group { margin-bottom: 1.25rem; }
        .form-group label {
            display: block;
            font-family: 'Cinzel', serif;
            font-size: .7rem; font-weight: 600;
            color: var(--gold);
            letter-spacing: .07em; text-transform: uppercase;
            margin-bottom: .45rem;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            background: var(--card2);
            border: 1px solid var(--border);
            border-radius: 4px;
            padding: .7rem 1rem;
            color: var(--text-bright);
            font-family: 'Inter', sans-serif;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
            outline: none;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(212,160,23,.12);
        }
        .form-group textarea { min-height: 100px; resize: vertical; }
        .form-group select option { background: var(--card); }

        /* ─── ALERTS ─── */
        .alert {
            padding: .9rem 1.25rem;
            border-radius: 6px;
            font-size: .88rem;
            margin-bottom: 1.25rem;
        }
        .alert-success { background: rgba(61,220,61,.1);  border: 1px solid var(--green-dim); color: var(--green); }
        .alert-error   { background: rgba(224,48,48,.1);  border: 1px solid #7a1010;         color: var(--red); }
        .alert-info    { background: rgba(212,160,23,.08); border: 1px solid var(--gold-dim); color: var(--gold); }

        /* ─── TABLES ─── */
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        table th {
            font-family: 'Cinzel', serif;
            font-size: .68rem; font-weight: 700;
            color: var(--gold);
            letter-spacing: .08em; text-transform: uppercase;
            padding: .8rem 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border);
            background: rgba(212,160,23,.04);
        }
        table td {
            padding: .85rem 1rem;
            border-bottom: 1px solid rgba(30,42,53,.6);
            font-size: .88rem;
            color: var(--text);
        }
        table tbody tr:hover { background: rgba(212,160,23,.03); }
        table tbody tr:last-child td { border-bottom: none; }

        /* ─── WAITING STATE ─── */
        .waiting-block {
            text-align: center;
            padding: 4rem 2rem;
        }
        .waiting-block .icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            animation: pulse 2.5s ease-in-out infinite;
        }
        .waiting-block h2 {
            font-family: 'Cinzel Decorative', serif;
            color: var(--gold);
            font-size: 1.6rem;
            margin-bottom: .75rem;
        }
        .waiting-block p { color: var(--text-dim); max-width: 480px; margin: 0 auto; line-height: 1.7; }

        @keyframes pulse {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: .6; transform: scale(.95); }
        }

        /* ─── FOOTER ─── */
        footer {
            position: relative; z-index: 1;
            text-align: center;
            padding: 2rem;
            border-top: 1px solid var(--border);
            color: var(--text-dim);
            font-size: .8rem;
        }
        footer span { color: var(--gold); }
    </style>
</head>
<body>

<nav>
    <div class="nav-inner">
        <a href="/index.php" class="nav-brand">⚔ Astas<span>Cup</span> <small style="font-size:.6em;opacity:.7">V1</small></a>
        <ul class="nav-links">
            <li><a href="/index.php" class="<?= $currentPage==='index'?'active':'' ?>">Accueil</a></li>
            <li><a href="/classement.php" class="<?= $currentPage==='classement'?'active':'' ?>">Classement</a></li>
            <li><a href="/equipes.php" class="<?= $currentPage==='equipes'?'active':'' ?>">Équipes</a></li>
            <li><a href="/rejoindre.php" class="<?= $currentPage==='rejoindre'?'active':'' ?>">Rejoindre</a></li>
            <li><a href="/regles.php" class="<?= $currentPage==='regles'?'active':'' ?>">Règles</a></li>
        </ul>
        <div class="nav-user">
            <?php if (isLoggedIn() && $user): ?>
                <span class="nav-user-name">⚡ <?= sanitize($user['username']) ?></span>
                <?php if (isAdmin()): ?>
                    <a href="/admin/index.php" class="btn btn-gold btn-sm">Admin</a>
                <?php else: ?>
                    <a href="/player/dashboard.php" class="btn btn-outline btn-sm">Dashboard</a>
                <?php endif; ?>
                <a href="/logout.php" class="btn btn-sm" style="color:var(--text-dim);border:1px solid var(--border)">Déco</a>
            <?php else: ?>
                <a href="/login.php" class="btn btn-outline btn-sm">Connexion</a>
            <?php endif; ?>
        </div>
    </div>
</nav>

<main>
