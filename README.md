# Maison Al Jazeera – Catalogue de parfums

Application web PHP (architecture MVC) : catalogue multi-marques de parfums avec front public et back-office d'administration. Projet fictif réalisé en équipe de 4.

> *L'art du parfum, une émotion intemporelle.*

## Fonctionnalités

- **Front :** liste des parfums, fiche détaillée (contenances, prix, fragrances, avis), filtres, tri, recherche, pagination, inscription, connexion, avis.
- **Back-office :** CRUD des parfums, marques, familles, fragrances, types d'eau et contenances.

## Prérequis

- PHP 8.0 ou plus
- MySQL ou MariaDB
- Serveur Apache avec le module `mod_rewrite` activé (ex. XAMPP, WAMP, MAMP)

## Installation

1. Cloner le dépôt dans le dossier web du serveur :
   ```
   git clone https://github.com/gourming1-ai/AL-Jazeera.git
   ```
2. Créer la base de données en important le script (il crée la base `parfumerie`) :
   - via phpMyAdmin : onglet *Importer*, fichier `database/parfumerie.sql`
   - ou en ligne de commande : `mysql -u root -p < database/parfumerie.sql`
3. Copier le modèle de configuration puis renseigner vos identifiants :
   ```
   cp config/config.example.php config/config.php
   ```
4. Vérifier que `mod_rewrite` est actif et que le site pointe sur le dossier `public/`.
5. Ouvrir le site dans le navigateur.

## Structure du dépôt

```
app/
  controllers/   contrôleurs (C)
  models/        modèles et accès à la base (M)
  views/         vues (V)
public/          seule racine web : index.php, .htaccess, assets/, uploads/
config/          config.example.php (modèle) ; config.php reste local
database/        parfumerie.sql
docs/            cahier des charges, guides, cahier de recette
```

## Organisation de l'équipe

- Branches : `main` (stable, rendue), `develop` (intégration), `feature/<nom>` par fonctionnalité, `fix/<nom>` pour les corrections.
- Aucun commit direct sur `main` ni `develop` : on passe par une pull request relue par un autre membre.
- Commits au format `type: message` (`feat`, `fix`, `docs`, `style`, `refactor`, `test`).
- Suivi des tâches : tableau Trello « Maison Al Jazeera – Catalogue parfums ».

## Compte administrateur

À créer après l'import : insérer un utilisateur avec `role = 'admin'` et un mot de passe haché avec `password_hash()`.

## Équipe

À compléter : noms des 4 membres.
