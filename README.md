# FIDELEM · API

API de la plateforme FIDELEM : comptes (usagers, conseillers financiers,
responsables), demandes de financement, candidatures de conseillers, messages de
contact et suivi des dossiers. Elle alimente le front-end
[`frontend_fidelem`](https://github.com/5core-team/frontend_fidelem).

Le contrat attendu par le front (routes, champs, statuts, formats de réponse)
est décrit dans [`docs/CONTRAT-API.md`](docs/CONTRAT-API.md).

## Stack

| Rôle | Outil |
| --- | --- |
| Framework | Laravel 12 (PHP 8.2 ou plus) |
| Base de données | MySQL 8 (SQLite en mémoire pour les tests) |
| Authentification | Laravel Sanctum 4, jetons Bearer uniquement |
| E-mails | Notifications Laravel (SMTP) |
| Tests et style | PHPUnit 11, Laravel Pint |

## Installation

```sh
composer install
cp .env.example .env         # en local : APP_ENV=local et APP_DEBUG=true
php artisan key:generate
# créer la base indiquée dans DB_DATABASE, puis :
php artisan migrate
php artisan serve            # http://127.0.0.1:8000
```

Toutes les routes sont servies sous `/api` (`http://127.0.0.1:8000/api/login`,
par exemple). Côté front, `VITE_API_URL` doit donc se terminer par `/api`.

### Données de démonstration (local uniquement)

```sh
php artisan migrate:fresh --seed
```

Crée trois comptes, mot de passe `password` : `responsable@fidelem.test`,
`conseillere@fidelem.test` (zone Cotonou) et `usager@fidelem.test`, ainsi que
quelques demandes. Le seeder ne fait rien en production.

### Premier compte responsable

L'inscription publique n'existe pas : les usagers sont créés par leur conseiller,
les conseillers par une candidature ou par un responsable. Le premier responsable
se crée en ligne de commande, le mot de passe est demandé de façon masquée :

```sh
php artisan fidelem:responsable prenom.nom@fidelem.pro --prenom=Prénom --nom=Nom
```

La commande réactive aussi un compte existant et lui donne le rôle de responsable.

## Configuration

| Variable | Rôle | Défaut |
| --- | --- | --- |
| `FRONTEND_URL` | Adresse du site, pour les liens envoyés par e-mail | `http://localhost:8080` |
| `CORS_ALLOWED_ORIGINS` | Origines autorisées, séparées par des virgules | `http://localhost:8080,http://127.0.0.1:8080` |
| `SANCTUM_EXPIRATION` | Durée d'une session, en minutes | `10080` (7 jours) |
| `TRUSTED_PROXIES` | Proxies dont on lit les en-têtes `X-Forwarded-*` | `127.0.0.1,::1` |
| `FIDELEM_CONTACT_EMAIL` | Boîte qui reçoit messages de contact et candidatures | `contact@fidelem.pro` |
| `MAIL_*` | Serveur d'envoi des e-mails | Mailpit en local |

Les valeurs partagées avec le front (communes, statuts, créneaux, niveaux) sont
dans `config/fidelem.php`. Toute modification doit être reportée dans
`src/donnees/fidelem.ts` côté front.

## Rôles et statuts

| `type_compte` | Rôle | Espace front |
| --- | --- | --- |
| `user` | Usager | `/mon-espace` |
| `advisor` | Conseiller financier | `/espace-conseiller` |
| `manager` | Responsable FIDELEM | `/responsable` |

Un compte vaut `En attente`, `Actif` ou `Rejeté`. Seuls les comptes `Actif`
se connectent ; un compte rejeté perd ses sessions ouvertes. Les droits sont
appliqués par le middleware `role` (`app/Http/Middleware/EnsureRole.php`) et par
la politique `DemandeFinancementPolicy`.

## Organisation du code

```
app/
├── Http/
│   ├── Controllers/
│   │   ├── Auth/ConnexionController.php     Connexion, déconnexion, utilisateur connecté
│   │   ├── Auth/MotDePasseController.php    Mot de passe oublié et réinitialisation
│   │   ├── DemandeFinancementController.php Demandes : création, listes, prise en charge, statut, rendez-vous
│   │   ├── NoteDemandeController.php        Notes d'un dossier
│   │   ├── ConseillerController.php         Recherche publique, clients, création de conseillers, zones
│   │   ├── CompteController.php             Back-office : comptes
│   │   ├── StatistiqueController.php        Back-office : chiffres
│   │   ├── CandidatureController.php        Candidatures de conseillers
│   │   ├── MessageContactController.php     Messages de contact
│   │   ├── InteretEasyLifeController.php    Intérêts EasyLife
│   │   └── ProfilController.php             Profil et mot de passe
│   ├── Middleware/EnsureRole.php            Compte actif et type de compte
│   ├── Requests/                            Validation des formulaires
│   └── Resources/                           Formats JSON lus par le front
├── Models/                                  User, DemandeFinancement, NoteDemande, CandidatureConseiller, MessageContact, InteretEasyLife
├── Notifications/                           E-mails envoyés
├── Policies/DemandeFinancementPolicy.php    Qui peut prendre ou modifier une demande
└── Support/Notifier.php                     Envoi d'e-mails sans faire échouer la requête
config/fidelem.php                           Référentiel partagé avec le front
lang/fr/                                     Messages en français
routes/api.php                               Routes de l'API
docs/CONTRAT-API.md                          Contrat avec le front
```

## Tests

```sh
php artisan test
vendor/bin/pint --test app config/fidelem.php database/factories database/seeders routes tests lang
```

Les tests tournent sur SQLite en mémoire. Ils couvrent l'authentification, les
droits d'accès, les formulaires du site, les trois espaces, la réinitialisation
du mot de passe et la reprise des anciennes demandes.

## Sécurité

- **Accès** : chaque route passe par le middleware `role` (compte actif et type de
  compte) ; les demandes sont protégées par `DemandeFinancementPolicy`. Les comptes
  responsables ne se rejettent ni ne se suppriment depuis l'API.
- **Sessions** : jetons Sanctum préfixés `fidelem_`, expirés après
  `SANCTUM_EXPIRATION` minutes, révoqués à la déconnexion, au changement ou à la
  réinitialisation du mot de passe et au rejet du compte. Aucune session par cookie.
- **Abus** : connexion limitée à 5 essais par minute par adresse et réseau, 20 par
  réseau et 50 par heure par adresse ; formulaires à 10 par minute et 60 par heure par
  réseau ; candidatures à 5 par heure. Une adresse IPv6 compte pour son bloc /64.
- **Pas d'oracle** : la connexion répond en temps constant, « mot de passe oublié »
  et la candidature répondent pareil que le compte existe ou non, et les e-mails
  partent après la réponse. Changer d'adresse e-mail exige le mot de passe actuel.
- **Cloisonnement** : un usager ne voit pas les notes internes de son dossier ; un
  visiteur ne reçoit qu'un accusé de réception ; un conseiller ne rattache à un client
  que les demandes qu'il suit déjà.
- **Saisies** : caractères de contrôle retirés, champs d'une ligne sans retour à la
  ligne, téléphone limité aux chiffres et à `+ . - ( )`, listes bornées. Le texte des
  visiteurs est neutralisé dans les e-mails (aucun lien ni mise en forme).
- **Réponses** : en-têtes `X-Content-Type-Options`, `X-Frame-Options`,
  `Referrer-Policy`, `Permissions-Policy`, `Content-Security-Policy` et, en HTTPS,
  `Strict-Transport-Security`.
- **Dépendances** : `composer audit` tourne dans la CI et bloque en cas de faille connue.

Une faille à signaler : écrire à l'adresse de `FIDELEM_CONTACT_EMAIL` plutôt que d'ouvrir
une issue publique.

## Intégration et déploiement

`.github/workflows/ci.yml` lance Pint et les tests à chaque push et à chaque PR.

Le déploiement sur le VPS est désactivé tant que la variable de dépôt
`DEPLOY_ENABLED` ne vaut pas `true` (Settings › Secrets and variables › Actions).
Il attend les secrets `SSH_HOST`, `SSH_PORT`, `SSH_USER`, `SSH_PRIVATE_KEY`,
`SSH_FINGERPRINT` (empreinte de la clé du serveur, pour refuser un faux serveur) et,
au besoin, la variable `DEPLOY_PATH` (défaut `/var/www/backend_fidelem`). À
chaque push sur `main`, il exécute sur le serveur `git pull`, `composer install
--no-dev`, `php artisan migrate --force` et la mise en cache de la configuration
et des routes.

À prévoir une fois sur le serveur :

- PHP 8.2 ou plus, avec `expose_php = Off` dans `php.ini` ;
- dans `.env` : `APP_ENV=production`, `APP_DEBUG=false`, `LOG_LEVEL=warning`, une
  `APP_KEY` propre, `APP_URL=https://api.fidelem.pro`, `FRONTEND_URL=https://fidelem.pro`,
  `CORS_ALLOWED_ORIGINS=https://fidelem.pro,https://www.fidelem.pro`, `TRUSTED_PROXIES`
  si nginx est un proxy distant, et un serveur SMTP dans `MAIL_*` ;
- la tâche planifiée qui purge les jetons expirés :
  `* * * * * cd /var/www/backend_fidelem && php artisan schedule:run >> /dev/null 2>&1` ;
- nginx : servir uniquement `public/`, refuser les fichiers cachés (`location ~ /\. { deny all; }`)
  et ne pas exposer `storage/`.
