# Maison Al Jazeera – Catalogue de parfums

Application web PHP (architecture MVC) : catalogue multi-marques de parfums avec front public et back-office d'administration. Projet fictif réalisé en équipe de 4.

> *L'art du parfum, une émotion intemporelle.*

## Fonctionnalités

- **Front :** accueil, liste des parfums (photo, nom, marque, prix « à partir de »), fiche détaillée (contenances et prix, fragrances, note moyenne, avis), filtres par marque / famille / type d'eau / fragrance / contenance, tri par nom ou prix, recherche, pagination, inscription, connexion, déconnexion, avis (un par parfum et par client), pages La Maison, L'Univers et Contact.
- **Back-office :** tableau de bord, CRUD des parfums (contenances, prix, fragrances, photo), CRUD des marques, familles, fragrances, types d'eau et contenances, modération des avis.

Le panier et la commande ne font pas partie du périmètre : le site est un catalogue.

## Prérequis

- PHP 8.1 ou plus (extensions `pdo_mysql` et `fileinfo`, actives par défaut sous XAMPP)
- MySQL ou MariaDB
- Serveur Apache avec le module `mod_rewrite` activé (ex. XAMPP, WAMP, MAMP)

Bootstrap, les icônes et les polices sont fournis dans `public/assets/` : le site fonctionne sans connexion Internet.

## Installation

1. Cloner le dépôt dans le dossier web du serveur (ex. `C:\xampp\htdocs`) :
   ```
   git clone https://github.com/gourming1-ai/AL-Jazeera.git
   ```
2. Créer la base de données, puis charger le jeu d'essai, **dans cet ordre** :
   - via phpMyAdmin : onglet *Importer*, fichier `database/parfumerie.sql`, puis `database/donnees.sql`
   - ou en ligne de commande :
     ```
     mysql -u root -p < database/parfumerie.sql
     mysql -u root -p < database/donnees.sql
     ```
3. Si vos identifiants MySQL ne sont pas ceux de XAMPP (`root`, sans mot de passe), copier le modèle de configuration puis le renseigner :
   ```
   cp config/config.example.php config/config.php
   ```
   Sans `config.php`, le site utilise les valeurs du modèle.
4. Vérifier que `mod_rewrite` est actif (et `AllowOverride All`, déjà le cas sous XAMPP).
5. Ouvrir le site : `http://localhost/AL-Jazeera/` (le `.htaccess` de la racine renvoie vers `public/`).

Sans Apache, pour un essai rapide : `php -S localhost:8000 -t public` puis `http://localhost:8000`.

## Comptes de démonstration

Créés par `database/donnees.sql` (mots de passe hachés avec `password_hash()`) :

| Rôle | Identifiant | Mot de passe |
|------|-------------|--------------|
| Administrateur | `admin` | `Admin123!` |
| Client | `leila`, `karim`, `sophie`, `thomas`, `ines`, `julien` | `Client123!` |

L'administrateur arrive sur le back-office après connexion : `http://localhost/AL-Jazeera/public/admin`.
Pour créer un autre administrateur : s'inscrire sur le site, puis passer son `role` à `admin` dans la table `USER`.
**À changer avant toute mise en ligne réelle.**

## Structure du dépôt

```
app/
  core/          Autoload, Database (PDO), Router, Controller, Model, helpers
  controllers/   contrôleurs du site (C) ; admin/ = back-office
  models/        modèles et accès à la base (M)
  views/         vues (V) ; includes/ = en-tête, pied de page, carte parfum, pagination
config/          config.example.php (modèle), routes.php (table des URL) ; config.php reste local
database/        parfumerie.sql (structure), donnees.sql (jeu d'essai)
docs/            cahier des charges, MCD, MLDR
public/          seule racine web : index.php, .htaccess, assets/ (css, js, img, polices), uploads/
```

## Fonctionnement en bref

- **URL réécrites :** `public/.htaccess` envoie toute adresse vers `public/index.php?url=…` ; `config/routes.php` associe chaque URL à une action (`parfums/12-sauvage` → `ParfumController@show`).
- **MVC :** le contrôleur lit la requête, interroge les modèles (requêtes préparées PDO) et affiche une vue entourée de `includes/header.php` et `includes/footer.php`.
- **Règles de gestion contrôlées par l'application :** au moins une fragrance par parfum, fragrances prises dans la famille du parfum (table `REGROUPE`), un seul avis par client et par parfum, note de 1 à 5, prix positif.
- **Sécurité :** mots de passe hachés, jeton CSRF sur tous les formulaires POST, sorties échappées, back-office réservé au rôle `admin`, photos vérifiées (type, taille) avant enregistrement dans `public/uploads/`.
- **Images :** les flacons du jeu d'essai sont des illustrations (`public/assets/img/parfums/`). Une vraie photo se dépose depuis le back-office (JPG, PNG ou WebP, 3 Mo max.).

## Organisation de l'équipe

- Branches : `main` (stable, rendue), `develop` (intégration), `feature/<nom>` par fonctionnalité, `fix/<nom>` pour les corrections.
- Aucun commit direct sur `main` ni `develop` : on passe par une pull request relue par un autre membre.
- Commits au format `type: message` (`feat`, `fix`, `docs`, `style`, `refactor`, `test`).
- Suivi des tâches : tableau Trello « Maison Al Jazeera – Catalogue parfums ».

## Équipe

À compléter : noms des 4 membres.
