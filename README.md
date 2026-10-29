# Zoo'land — Application de gestion pour un zoo

Site web de gestion développé en binôme dans le cadre d'un projet universitaire (SAE, L2 MIAGE), permettant à un zoo de gérer son personnel, ses animaux et son activité au travers de plusieurs profils d'accès.

🎥 [Démonstration vidéo du site](https://youtu.be/RHngCigiY2o?feature=shared)

## Fonctionnalités

- **Gestion du personnel** : recrutement, modification des rôles, consultation par niveau hiérarchique.
- **Gestion des animaux** : historique des soins, alimentation, recherche sur les animaux, enclos et espèces.
- **Statistiques et finances** : chiffre d'affaires, contrats, suivi des boutiques.
- **Contrôle d'accès par rôle** : six profils (super-gérant, gérant, comptable, gérant boutique, soignant, agent d'entretien des enclos), chacun avec un périmètre différent.
- **Authentification** : connexion et changement de mot de passe.

## Technologies

- PHP (extension OCI8)
- Oracle Database / SQL (schéma complet, scripts de création et de suppression)
- HTML / CSS

## Sécurité

- Mots de passe **hachés** avec `password_hash()` et vérifiés avec `password_verify()` : aucun mot de passe n'est stocké en clair.
- Requêtes SQL **paramétrées** (`oci_bind_by_name`) pour se protéger des injections SQL.
- Recherche limitée à une **liste blanche** de tables et de colonnes autorisées selon le rôle de l'utilisateur.
- Identifiants de connexion à la base **hors du dépôt** : `myparam.inc.php` est ignoré par Git, seul un modèle est publié.

## Installation

1. Créer la base en exécutant `bd.sql` sur une instance Oracle (`drop.sql` permet de tout supprimer).
2. Copier `Public_html/myparam.example.inc.php` en `Public_html/myparam.inc.php` et y renseigner vos propres identifiants.
3. Servir le dossier `Public_html/` avec un serveur PHP disposant de l'extension OCI8.

Les comptes de démonstration sont créés par `bd.sql`. Changez leur mot de passe si l'application est exposée sur un réseau.

## Structure du projet

```
Public_html/
├── index.php, login.php, dashboard.php     # Authentification et accueil
├── gerant/          # Gestion du personnel, statistiques, parrainage
├── soignant/        # Alimentation, historique des soins
├── agent_entretien/ # Entretien des enclos
├── myparam.example.inc.php  # Modèle de configuration de la base
└── style.css
bd.sql / drop.sql     # Schéma et initialisation de la base de données
```

## Ce que ce projet démontre

- Conception d'une base de données relationnelle et de requêtes SQL pour une application multi-utilisateurs.
- Contrôle d'accès basé sur les rôles.
- Bonnes pratiques de sécurité web : hachage des mots de passe, requêtes paramétrées, secrets hors du code versionné.
- Travail en binôme sur un projet de taille conséquente.

## Auteurs

Michael Djeatsa Keleko & Mohamed El brrah — projet réalisé en binôme en L2 MIAGE Informatique (Université de Picardie Jules Verne).
