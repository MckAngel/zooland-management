# Zoo'land — Application de gestion pour un zoo

Site web de gestion développé en binôme dans le cadre d'un projet universitaire (SAE), permettant à un zoo de gérer son personnel, ses animaux et son activité au travers de plusieurs profils d'accès.

## Fonctionnalités

- **Gestion du personnel** : recrutement, modification des rôles, consultation par niveau hiérarchique.
- **Gestion des animaux** : historique des soins, alimentation, recherche sur les animaux, enclos et espèces.
- **Statistiques et finances** : chiffre d'affaires, contrats, suivi des boutiques.
- **Système de rôles multi-niveaux** : super-gérant, gérant, comptable, gérant boutique, soignant, agent d'entretien des enclos — chaque profil dispose d'un périmètre d'accès différent.
- **Authentification** : connexion, gestion des mots de passe.

## Technologies

- PHP
- MySQL / SQL (schéma de base de données complet, scripts de création et de suppression)
- HTML / CSS

## Structure du projet

```
Public_html/
├── index.php, login.php, dashboard.php     # Authentification et accueil
├── gerant/          # Gestion du personnel, statistiques, parrainage
├── soignant/        # Alimentation, historique des soins
├── agent_entretien/ # Entretien des enclos
└── style.css
bd.sql / drop.sql     # Schéma et initialisation de la base de données
```

## Comptes de démonstration

| Rôle | ID personnel | Mot de passe |
|---|---|---|
| Super-gérant (accès complet) | 1 | default |
| Gérant | 1 | default |
| Comptable | 23 | default |
| Gérant boutique | 37 | default |
| Soignant | 5 | default |
| Agent entretien enclos | 11 | default |

> Le gérant avec l'ID 1 est le super-gérant : il voit tout le personnel, y compris lui-même, dans la gestion.

## Démonstration vidéo

[Présentation du site en vidéo](https://youtu.be/RHngCigiY2o?feature=shared)

## Ce que ce projet démontre

- Conception d'une base de données relationnelle et de requêtes SQL pour une application multi-utilisateurs.
- Gestion des droits d'accès selon le rôle de l'utilisateur (contrôle d'accès basé sur les rôles).
- Développement web PHP classique (formulaires, sessions, recherche, affichage de données).
- Travail en binôme sur un projet de taille conséquente.

## Auteurs

Michael Djeatsa Keleko & Mohamed El brrah — projet réalisé en binôme, étudiants en L2 MIAGE Informatique.

## Remarque

Ce dépôt est publié à des fins de démonstration de compétences (portfolio). Les comptes et mots de passe utilisés lors de la démonstration ont été retirés.
