# Contrat d'API FIDELEM

Ce document décrit les routes de l'API telles que le front-end (`frontend_fidelem`)
les appelle. Chaque route est couverte par les tests de `tests/Feature`.

## 1. Conventions

- **Préfixe** : toutes les routes sont sous `/api`. Côté front, `VITE_API_URL`
  vaut `https://api.fidelem.pro/api` en production.
- **Format** : JSON en entrée et en sortie, avec `Accept: application/json`.
  Les objets et les listes sont renvoyés tels quels, sans enveloppe `data`.
- **Authentification** : jeton Sanctum, `Authorization: Bearer <jeton>`.
  Un jeton expire après `SANCTUM_EXPIRATION` minutes (7 jours par défaut).
- **Erreurs** : `{"message": "..."}` en français. Une validation refusée (422)
  ajoute `{"errors": {"champ": ["..."]}}`. Un compte non actif reçoit 403 avec
  un champ `code`.
- **Montants** : FCFA, nombres entiers, jusqu'à 1 000 000 000. Durées en mois.
- **Valeurs de référence** (communes, créneaux, statuts…) : `config/fidelem.php`,
  identique à `src/donnees/fidelem.ts` et `src/components/site/Formulaires.tsx`.

| Code HTTP | Sens |
| --- | --- |
| 401 | Pas de jeton, ou jeton expiré ou révoqué. Le front ferme la session. |
| 403 | Rôle insuffisant, compte non actif (`code: compte_inactif`) ou action refusée par la politique. |
| 404 | Ressource introuvable. |
| 409 | Demande déjà prise en charge par un autre conseiller. |
| 422 | Données invalides. |
| 429 | Trop de requêtes. Connexion : 5 par minute par adresse et réseau, 20 par minute par réseau, 50 par heure par adresse. Formulaires : 10 par minute et 60 par heure par réseau. Candidatures : 5 par heure. Une adresse IPv6 compte pour son bloc /64. |

Rôles (`type_compte`) : `user` (usager), `advisor` (conseiller), `manager` (responsable).

## 2. Statuts d'une demande

| Statut | Sens | Qui le fixe |
| --- | --- | --- |
| `Nouvelle` | Reçue, pas encore prise en charge | création |
| `Prise en charge` | Un conseiller suit la demande | prise en charge, ou création par un conseiller |
| `Rendez-vous fixé` | Rendez-vous confirmé | fixation du rendez-vous |
| `Dossier en cours` | Montage du dossier | conseiller ou responsable |
| `Acceptée` | Financement accordé | conseiller ou responsable |
| `Refusée` | Financement refusé | conseiller ou responsable |

## 3. Formats de réponse

### 3.1 Utilisateur connecté

Renvoyé par `POST /login` (clé `user`), `GET /me` et `POST /update-profile` (clé `user`).

```json
{
  "id": 12, "name": "Aïcha", "last_name": "Houénou", "email": "aicha@exemple.bj",
  "phone": "01 97 00 11 22", "address": "Cotonou", "role": "advisor", "statut": "Actif",
  "created_by": null, "zone": "Cotonou", "niveau": "croissance", "financements": ["Immobilier"],
  "conseiller_nom": null, "conseiller_telephone": null
}
```

`zone`, `niveau` et `financements` concernent les conseillers. `conseiller_nom` et
`conseiller_telephone` donnent à l'usager le conseiller qui a créé son compte.

### 3.2 Demande de financement

Un seul format pour toutes les demandes (`DemandeResource`). Il ne contient jamais
les clés `amount` ni `purpose`.

```json
{
  "id": 101, "prenom": "Bernadette", "nom": "Kpossou", "telephone": "01 95 44 21 08", "email": null,
  "financement": "immobilier", "objet": "Construction d'une maison", "montant": 18000000, "duree": 120,
  "message": "Terrain acquis à Fidjrossè.", "zone": "Cotonou",
  "rendezVous": { "mode": "En agence", "date": "2026-10-09", "creneau": "Matin (8 h – 12 h)",
                  "autresDisponibilites": ["Mardi"], "zone": "Cotonou", "contactPrefere": "Appel" },
  "statut": "Nouvelle", "origine": "site", "usagerId": null, "conseillerId": null,
  "conseiller": null,
  "notes": [{ "id": 3, "texte": "Pièces reçues.", "date": "2026-10-07T10:00:00.000000Z", "auteur": "Aïcha Houénou" }],
  "created_at": "2026-10-06T08:12:00.000000Z", "updated_at": "2026-10-06T08:12:00.000000Z"
}
```

`origine` vaut `site` (formulaire public), `espace` (créée depuis un espace) ou
`historique` (reprise de l'ancienne table `credit_requests`). `conseiller` vaut
`{ id, nom, telephone }` une fois la demande attribuée.

### 3.3 Compte (back-office, clients d'un conseiller)

```json
{ "id": 4, "name": "Rodrigue", "last_name": "Agossou", "email": "r@exemple.bj", "phone": "01 96 12 34 56",
  "address": "Akpakpa", "type_compte": "user", "statut": "Actif", "zone": null, "niveau": null,
  "financements": [], "created_by": 12, "created_at": "2026-10-01T09:00:00.000000Z" }
```

## 4. Authentification et compte

| Méthode | Route | Accès | Corps | Réponse |
| --- | --- | --- | --- | --- |
| POST | `/login` | public | `email`, `password` | `{ token, user }` ; 401 ; 403 `compte_en_attente` ou `compte_rejete` |
| POST | `/logout` | connecté | | 204, jeton révoqué |
| GET | `/me` | connecté | | utilisateur § 3.1 |
| POST | `/update-profile` | conseiller, responsable | `firstName`, `lastName`, `email`, `phone`, `address`, et `currentPassword` si l'e-mail change | `{ message, user }` ; 403 pour un usager ; les autres sessions sont fermées si l'e-mail change |
| POST | `/update-password` | connecté | `currentPassword`, `newPassword`, `newPassword_confirmation` | `{ message }` ; les autres sessions sont fermées |
| POST | `/mot-de-passe/oubli` | public | `email` | 200, même réponse et même durée que l'adresse existe ou non (l'e-mail part après la réponse) |
| POST | `/mot-de-passe/reinitialiser` | public | `token`, `email`, `password`, `password_confirmation` | 200 ; 422 si le lien n'est plus valide |

Le lien de l'e-mail ouvre `FRONTEND_URL/reinitialiser-mot-de-passe?token=…&email=…`.
Il est valable 60 minutes et ne sert qu'une fois.

## 5. Site public

| Méthode | Route | Page du front | Effet |
| --- | --- | --- | --- |
| POST | `/demandes-financement` | Services › détail, Trouver un conseiller | Demande `Nouvelle`, e-mail aux conseillers de la zone (ou au conseiller choisi). Réponse 201 : `{ id, statut }` seulement |
| POST | `/messages-contact` | Contact | Message enregistré et transmis à `FIDELEM_CONTACT_EMAIL` |
| POST | `/candidatures-conseiller` | Conseiller Financier › candidature | Candidature + compte conseiller `En attente`, e-mail à FIDELEM. Réponse 201 : `{ message }`, identique si l'e-mail a déjà un compte : aucun compte n'est alors créé, et le titulaire de l'adresse est prévenu par e-mail |
| POST | `/easylife/interets` | (aucune pour l'instant) | Intérêt EasyLife enregistré |
| GET | `/conseillers?zone=` | Trouver un conseiller | Conseillers actifs de la commune : `id, prenom, nom, zone, niveau, financements, photo` |

**Bloc `rendezVous`** : `zone` (commune de la liste) et `date` (`AAAA-MM-JJ`,
après aujourd'hui) obligatoires ; `creneau`, `mode`, `autresDisponibilites`,
`contactPrefere` parmi les valeurs de `config/fidelem.php`.

**Demande** : `prenom`, `nom`, `telephone` (8 chiffres au moins) obligatoires,
`email` facultatif ; `financement` parmi `immobilier`, `transport`, `affaires`,
`conseil` ; `objet` ; `montant` et `duree` entiers (0 accepté pour une prise de
rendez-vous) ; `message` ; `conseillerId` facultatif (conseiller actif) ; `rendezVous`.

**Contact** : coordonnées, `objet` (`Demande de financement`, `EasyLife`,
`Devenir conseiller`, `Autre`), `message` obligatoire, `rendezVous` facultatif.

**Candidature** : coordonnées avec `email` obligatoire ;
`niveauVise` (`CF Inclusion`, `CF Croissance`, `CF Patrimoine`, `Je ne sais pas encore`) ;
`situation` ; `experience` facultative ; `motDePasse` (8 caractères au moins) ; `rendezVous`.

## 6. Mon espace (usager)

| Méthode | Route | Effet |
| --- | --- | --- |
| GET | `/credit-requests-client` | Demandes du compte connecté (§ 3.2), sans les notes internes. Le paramètre `userId` est ignoré. |
| POST | `/credit-requests` | Nouvelle demande au nom du compte connecté |

**`POST /credit-requests`** (usager ou conseiller) : `amount`, `duration`,
`purpose` (objet), `financement` (facultatif ; sinon déduit de « Immobilier · … »),
`additional_details`, `rendez_vous` (obligatoire pour l'usager), `zone`.
Pour un conseiller, `clientId` est obligatoire et doit désigner l'un de ses clients ;
la demande naît alors `Prise en charge`. Pour un usager, elle naît `Nouvelle` et
est adressée à son conseiller attitré s'il en a un.

## 7. Espace Conseiller

| Méthode | Route | Accès | Effet |
| --- | --- | --- | --- |
| GET | `/conseiller/demandes-zone` | conseiller | Demandes `Nouvelle` de sa zone sans conseiller, et celles qui lui sont adressées |
| GET | `/credit-requests-conseiller` | conseiller | Dossiers qu'il suit (hors `Nouvelle`). `userId` est ignoré. |
| GET | `/advisor/{id}/clients` | conseiller | Ses clients ; 403 si `{id}` n'est pas lui |
| POST | `/conseiller/clients` | conseiller | Crée un client **actif** (`name`, `last_name`, `email`, `phone`, `address`, `password`) et lui rattache les demandes sans compte **que ce conseiller suit déjà** (même e-mail, ou mêmes 8 derniers chiffres de téléphone) |
| POST | `/demandes-financement/{id}/prise-en-charge` | conseiller | Statut `Prise en charge` ; 409 si un autre l'a déjà prise ; 403 hors zone |
| PUT | `/demandes-financement/{id}/statut` | conseiller qui suit, responsable | `{ statut }` parmi les six du § 2 |
| PUT | `/demandes-financement/{id}/rendez-vous` | conseiller qui suit, responsable | `{ date, creneau, mode? }` ; statut `Rendez-vous fixé` ; e-mail à l'usager s'il a une adresse |
| POST | `/demandes-financement/{id}/notes` | conseiller qui suit, responsable | `{ texte }` ; renvoie la note `{ id, texte, date, auteur }` |

## 8. Back-office (responsable)

| Méthode | Route | Effet |
| --- | --- | --- |
| GET | `/users?type_compte=` | Comptes (§ 3.3), filtre facultatif |
| POST | `/users/{id}/approve` | Compte `Actif` |
| POST | `/users/{id}/reject` | Compte `Rejeté`, sessions fermées ; 422 sur son propre compte |
| DELETE | `/users/{id}` | Suppression ; ses clients restent, sans conseiller ; 422 sur son propre compte |
| GET | `/user-stats` | `{ totalUsers, totalAdvisors, totalManagers, pendingUsers }` (`totalUsers` = usagers) |
| GET | `/credit-stats` | `{ total, montantTotal, parStatut, parMois: [{ mois, nombre, montant }] }` sur 12 mois |
| GET | `/credit-requests-admin` | Toutes les demandes (§ 3.2) |
| GET | `/candidatures-conseiller` | `[{ id, prenom, nom, telephone, email, niveauVise, situation, experience, rendezVous, compteId, statutCompte, created_at }]` |
| GET | `/messages-contact` | `[{ id, prenom, nom, telephone, email, objet, message, rendezVous, created_at }]` |
| GET | `/easylife/interets` | Intérêts EasyLife, plus les messages de contact d'objet `EasyLife` |
| POST | `/responsable/conseillers` | Crée un conseiller **actif** : `name`, `last_name`, `email`, `phone`, `password`, `zone?`, `niveau?`, `financements?` |
| PUT | `/conseillers/{id}/zone` | `{ zone, niveau?, financements? }` ; `zone: null` retire la zone |

## 9. Données reprises de l'ancienne version

La migration `2026_10_07_000007` copie les lignes de `credit_requests` dans
`demandes_financement` (`origine: historique`). Statuts : `Approuvé` donne
`Acceptée`, `Rejeté` donne `Refusée`, le reste `Dossier en cours`. La table
`credit_requests` est conservée : elle peut être supprimée une fois la reprise
vérifiée en production. Il en va de même pour `funding_requests` (levées de fonds),
qui n'a plus de route.
