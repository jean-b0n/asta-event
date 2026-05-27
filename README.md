# ⚔ AstasCup V1 — Site Officiel

Site PHP de gestion de l'événement Minecraft **AstasCup V1** par Enoe_one.

---

## 🚀 Déploiement sur Railway

### 1. Préparer le dépôt
```bash
git init
git add .
git commit -m "AstasCup V1 - initial"
```

### 2. Créer le projet Railway
1. Va sur [railway.app](https://railway.app)
2. **New Project → Deploy from GitHub repo** → sélectionne ton repo
3. Railway détecte automatiquement PHP via `nixpacks.toml`

### 3. Ajouter MySQL
1. Dans ton projet Railway : **New → Database → MySQL**
2. Railway crée automatiquement les variables d'environnement :
   - `MYSQLHOST`, `MYSQLPORT`, `MYSQLDATABASE`, `MYSQLUSER`, `MYSQLPASSWORD`

### 4. Initialiser la base de données
1. Clique sur ton service MySQL dans Railway
2. Onglet **Query** ou utilise un client MySQL (TablePlus, DBeaver...)
3. Colle et exécute le contenu de `database.sql`

### 5. Variable d'environnement de démarrage
Dans ton service PHP (pas MySQL), ajoute la variable :
```
START_COMMAND=php -S 0.0.0.0:$PORT router.php
```

Ou via `nixpacks.toml` (déjà configuré), Railway utilise le fichier automatiquement.

---

## 🔐 Compte admin par défaut

| Champ | Valeur |
|-------|--------|
| Identifiant | `Enoe_one` |
| Mot de passe | `admin123` |

> ⚠️ **CHANGE LE MOT DE PASSE** dès le premier login !
> Va dans le dashboard admin → tu peux te recréer un compte avec un nouveau mdp.

---

## 📁 Structure des fichiers

```
astascup/
├── index.php           → Page d'accueil
├── classement.php      → Classement (global + par épreuve)
├── equipes.php         → Répartition des équipes
├── rejoindre.php       → Formulaire de candidature
├── regles.php          → Règles du serveur
├── login.php           → Connexion
├── logout.php          → Déconnexion
├── router.php          → Routeur PHP built-in (Railway)
├── nixpacks.toml       → Config déploiement Railway
├── database.sql        → Schéma + données initiales
├── includes/
│   ├── config.php      → Config BDD + fonctions globales
│   ├── header.php      → Header + CSS global
│   └── footer.php      → Footer
├── admin/
│   ├── index.php       → Dashboard admin (4 onglets)
│   └── actions.php     → Traitement actions admin (POST)
└── player/
    └── dashboard.php   → Dashboard joueur
```

---

## 🎮 Pages du site

| Page | URL | Accès |
|------|-----|-------|
| Accueil | `/` | Public |
| Classement | `/classement.php` | Public |
| Équipes | `/equipes.php` | Public |
| Rejoindre | `/rejoindre.php` | Public |
| Règles | `/regles.php` | Public |
| Connexion | `/login.php` | Public |
| Dashboard Joueur | `/player/dashboard.php` | Connecté |
| Dashboard Admin | `/admin/index.php` | Admin seulement |

---

## ✏️ Personnaliser les questions du questionnaire

Édite la fonction `getQuestions()` dans `rejoindre.php` :
```php
function getQuestions(): array {
    return [
        'Quel est ton pseudo Minecraft ?',
        'Quel est ton pseudo Discord ?',
        // Ajoute tes questions ici...
    ];
}
```

---

## 📝 Notes importantes

- **Mot de passe admin initial** : `admin123` — à changer immédiatement
- Le classement s'affiche uniquement **après** avoir lancé la compétition dans l'admin
- Les équipes sont visibles uniquement **après** avoir activé "Révéler les équipes"
- Les points sont recalculés automatiquement à chaque mise à jour
