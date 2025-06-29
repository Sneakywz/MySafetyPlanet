# Documentation MySafetyPlanet

|  | Nom | Examinateur |
| --- | --- | --- |
| Nom | Maxime Nectoux & Lucas Perez | Nicolas Faessel |
| Date  | 28/06/2025 |  |

---

|  | Statut | Nom |
| --- | --- | --- |
| **Diffusion** | Correcteur | Nicolas Faessel |

---

### 1. Présentation générale

- Objectif du projet
- Utilisateurs visés
- Fonctionnalités principales
- Technologies utilisées (PHP, MySQL, html, css, javascript, Docker…)

---

### 2. Architecture du projet *(inspirée de Symfony)*

- Arborescence des dossiers
- comparaison de symfony et de my safety planet
- Principe MVC (Modèle – Vue – Contrôleur)
- Rôle de chaque dossier et ou sont placés les fichiers (`src/`, `templates/`, `public/`, etc.)
- Avantages de cette structure claire et modulaire

---

### 3. Fonctionnalités

### 3.1 Authentification et gestion de compte

- Inscription avec validation
- Connexion sécurisée avec sessions & "se souvenir de moi"
- Réinitialisation du mot de passe dans l’espace profil
- Déconnexion sécurisée
- Gestion des rôles (admin / utilisateur/superadmin)

### 3.2 Gestion des utilisateurs (par un admin)

- Affichage de la liste
- Modification et suppression
- Attribution des rôles
- Réinitialisation du mot de passe

### 3.3 Gestion des articles

- Création / modification / suppression (admin)
- Affichage public (visiteur ou utilisateur connecté)
- Structure des articles : titre, contenu, dates

---

### 4. Interface utilisateur

- Navigation principale
- Pages principales (accueil, connexion, profil, admin…)
- Affichage structuré des articles
- CSS : thème, responsive design
- JavaScript utilisé (navbar, interactions dynamiques)
- utilisation de TINYMCE pour rédiger les articles avec de js

---

### 5. Base de données

- Tables utilisées (`users`, `articles`…)
- Champs et relations clés
- Contraintes d’intégrité
- Éventuel schéma ERD
- Exemples de requêtes SQL utilisées dans les repositories

---

### 6. Sécurité

- Hachage des mots de passe (`password_hash`)
- Protection contre XSS (`htmlspecialchars`, filtrage)
- CSRF (sur actions sensibles comme suppression)
- Prévention des injections SQL (requêtes préparées)
- Expiration de session via cookies ("remember me")

---

### 7. Installation du projet

- Prérequis : PHP, MySQL, Docker Desktop
- Installation locale avec ou sans Docker
- Import de la base (`database.sql`)
- Configuration manuelle (fichiers à modifier, URL, ports…)
- Accès : `http://localhost:8000` ou autre selon config

---

### 8. Hébergement avec Docker (Windows)

- Fichier `docker-compose.yml`
- Fichier `Dockerfile`
- Lancer les services
- Volumes, persistance des données
- Accès à la base de données depuis l’extérieur

---

### 11. Évolutions possibles

- CSRF sur tous les formulaires
- Filtrage / recherche d’articles
- Ajout d’une API REST
- Système de commentaires
- Gestion des fichiers (upload d’image pour article)
- Pagination

---

### 12. Difficultés

---

### 13. Annexe

- Liens utiles ([PHP.net](http://php.net/), Docker doc, MySQL doc)

---

## 1. Présentation générale

### Contexte et objectif

MySafetyPlanet est une application web de type blog développée en PHP 8.x sans framework complet, s’inspirant toutefois de bonnes pratiques issues de Symfony. L’objectif est de fournir une plateforme où :

- Nous implémentons manuellement la gestion des utilisateurs, des articles et des sessions pour comprendre les mécanismes sous-jacents.
- Nous découvrons en pratique la séparation des responsabilités (Controller, Entity, Repository) avant d’aborder un framework complet.

### Enjeux techniques

1. **Gestion des sessions et cookies** :
    - Mise en place de `session_start()` et suivi d’une session utilisateur sécurisée.
    - Création d’un cookie « se souvenir de moi » avec token unique, stocké en base pour authentifier sans ressaisie.
2. **Sécurité des données** :
    - **Hachage des mots de passe** avec `password_hash()` et vérification sécurisée `password_verify()`.
    - **Prévention des injections SQL** via des requêtes préparées (`PDO::prepare`, `bindValue`).
    - **Protection CSRF** : implémentation minimale sur les actions sensibles (generateCsrfToken, verifyCsrfToken).
    - **Validation et filtrage** : vérification serveur de chaque champ (`filter_var`, regex) et gestion des messages d’erreur.
3. **Architecture MVC simplifiée** :
    - **Controller** : scripts dans `src/Controller` traitent les requêtes, appellent les repositories et redirigent vers les vues.
    - **Entity** : classes PHP qui modélisent les tables SQL (`User`, `Article`), sans ORM.
    - **Repository** : classes dédiées (`UserRepository`, `ArticleRepository`) pour centraliser les requêtes SQL.
4. **Environnement et déploiement** :
    - **Docker** : conteneurisation Apache + PHP + MySQL via `Dockerfile` et `docker-compose.yml`.
    - Gestion des **volumes** pour persister les données et synchroniser le code.
5. **Gestion des dépendances** :
    - **Composer** pour charger `autoload.php` et installer des librairies (ex : Dotenv pour `.env`).

---

## 2. Architecture du projet (Détails techniques)

### Vue d'ensemble de l'arborescence

```
MySafetyPlanet-main/
├── .gitignore
├── Dockerfile
├── docker-compose.yml
├── composer.json
├── composer.lock
├── database.sql
├── autoload.php
├── article_show.php
├── articles.php
├── collectivite.php
├── creer-compte.php
├── entreprise.php
├── index.php
├── login.php
├── logout.php
├── particulier.php
├── profile.php
├── "blog cours.session.sql"
├── src/
│   ├── connexion.php
│   ├── inscription.php
│   ├── Controller/
│   │   ├── deleteArticle.php
│   │   └── deleteUser.php
│   ├── Entity/
│   │   ├── Article.php
│   │   ├── Db.php
│   │   └── User.php
│   ├── Enum/
│   │   └── UserRolesEnum.php
│   ├── Repository/
│   │   ├── ArticleRepository.php
│   │   └── UserRepository.php
│   └── Service/
│       ├── Authentification.php
│       └── CsrfToken.php
├── public/                  # utilisé pour fichiers statiques, .htaccess
├── admin/
│   ├── dashboard.php
│   ├── article_form.php
│   ├── article_list.php
│   ├── user_form.php
│   └── user_list.php
├── assets/
│   ├── css/
│   │   └── styles.css
│   ├── js/
│   │   ├── navbar.js
│   │   └── tinymce.js
│   └── img/
│       └── logo-banniere.jpg
├── inc/
│   ├── header.php
│   ├── footer.php
│   └── admin/
│       ├── header.php
│       └── footer.php
└── templates/               # si utilisé pour vues séparées
    ├── base.php
    ├── login.php
    ├── profile.php
    ├── articles.php
    ├── article_show.php
    └── admin/
        ├── dashboard.php (raccourci)
        ├── article_form.php
        ├── article_list.php
        ├── user_form.php
        └── user_list.php
```

### 2.2 Explication des dossiers

- **.gitignore** : liste des fichiers et dossiers ignorés par Git.
- **Dockerfile** et **docker-compose.yml** : configuration de l’environnement Docker (services web et db).
- **composer.json** et **composer.lock** : gestion des dépendances PHP et autoloading PSR-4.
- **database.sql** et **blog cours.session.sql** : scripts SQL pour création et initialisation de la base de données.
- **autoload.php** : fichier généré par Composer pour le chargement automatique des classes.
- **article_show.php**, **articles.php**, **collectivite.php**, **entreprise.php**, **particulier.php**, **index.php**, **login.php**, **logout.php**, **creer-compte.php**, **profile.php** : pages frontales accessibles aux utilisateurs et visiteurs.
- **src/** : contient le code back-office structuré en sous-dossiers :
    - **Controller/** : gère les actions (authentification, création et suppression d’articles et d’utilisateurs).
    - **Entity/** : classes représentant les entités (User, Article, Db).
    - **Enum/** : énumérations telles que `UserRolesEnum` pour centraliser les rôles.
    - **Repository/** : classes `UserRepository` et `ArticleRepository` pour l’accès aux données.
    - **Service/** : services transverses comme la gestion de l’authentification et des tokens CSRF.
- **public/** : contient les ressources statiques (CSS, JS, images) et le fichier `.htaccess` si utilisé.
- **admin/** : pages d’administration accessibles uniquement aux utilisateurs avec rôle admin ou superadmin : tableau de bord (`dashboard.php`), gestion des articles et des utilisateurs.
- **assets/** : regroupe les dossiers CSS (`styles.css`), JS (`navbar.js`, `tinymce.js`) et img (`logo-banniere.jpg`).
- **inc/** : inclusions de vues partagées (`header.php`, `footer.php`) et leurs équivalents pour l’administration.
- **templates/** : gabarits de vues (si utilisés) pour découpler le HTML du code métier.

---

## 3. Architecture du projet (Inspirée de Symfony)

Lors de ce projet, nous avons découvert les concepts fondamentaux des frameworks PHP modernes, notamment à travers Symfony. En nous documentant sur ce framework très largement utilisé dans le monde professionnel, nous avons compris l'intérêt de structurer un projet en couches séparées selon le modèle MVC : Modèle - Vue - Contrôleur.

Symfony suit une architecture très rigoureuse avec des dossiers comme `src/Controller`, `templates/`, `Entity/`, `config/`, et un point d'entrée unique situé dans `public/index.php`. Le projet MySafetyPlanet reprend cette organisation avec des adaptations liées à sa simplicité et au contexte d'apprentissage.

### Comparaison structurelle avec Symfony

| Élément Symfony | MySafetyPlanet |
| --- | --- |
| `src/Controller/` | `src/Controller/` |
| `src/Entity/` | `src/Entity/` |
| `templates/` | `templates/` |
| `public/index.php` | `public/index.php` |
| `config/routes.yaml` | Pas de routing automatisé |
| `services.yaml` | Pas de conteneur de services |
| `vendor/` (Composer) | Composer utilisé, pas d'autoloading avancé |

Nous avons donc fait le choix de conserver une structure claire et inspirée du framework, tout en restant simple pour pouvoir tout comprendre et maîtriser le fonctionnement de chaque partie.

---

## 4. Fonctionnalités détaillées

### 4.1 Authentification et gestion de compte

L'application MySafetyPlanet offre un processus complet d'inscription, d'authentification et de gestion de profil, tout en garantissant la sécurité et l'intégrité des données utilisateur.

### Inscription

- Formulaire dans `creer-compte.php` avec champs **nom**, **email**, **mot de passe**, **confirmation**.
- Validation serveur : format email (`filter_var`), correspondance mot de passe/confirmation, longueur minimale, unicité de l'email (`UserRepository::findByEmail()`).
- Utilisation de requêtes préparées (`PDO`) pour l'insertion via `UserRepository::saveUser()`.
- Hachage des mots de passe avec `password_hash()`.

### Connexion

- Formulaire dans `login.php` avec champs **email**, **mot de passe**, case **Se souvenir de moi**.
- Processus :
    1. Récupération de l'utilisateur par email.
    2. Vérification du mot de passe avec `password_verify()`.
    3. Démarrage de la session (`session_start()`).
    4. Si option cochée, génération et stockage d'un token dans la colonne `remember_me` et en cookie.
- Gestion des erreurs : message générique pour éviter la fuite d'informations.

### Réinitialisation du mot de passe

- Interface disponible dans `profile.php` pour l'utilisateur et `admin/user_form.php` pour l'admin.
- Vérification de l'ancien mot de passe, validation du nouveau, et hachage avant enregistrement.

### Déconnexion

- `logout.php` détruit la session et supprime le cookie persistant.

### Gestion des rôles

Les rôles sont fondamentaux pour définir les droits d’accès et les fonctionnalités disponibles pour chaque utilisateur. Dans MySafetyPlanet, les rôles sont stockés dans le champ `roles` de la table `users` au format JSON ; par exemple :

```
["user"]
["admin"]
["superadmin"]
```

- **`user`** : accès aux fonctionnalités de base (lecture des articles, gestion de son profil).
- **`admin`** : tous les droits d’un `user` plus création, modification et suppression des articles et gestion des comptes utilisateurs.
- **`superadmin`** : même accès que `admin` avec en plus la possibilité de promouvoir d’autres admins et de modifier les paramètres globaux de l’application.

L’application utilise ces rôles de deux manières :

1. **Contrôle d’affichage** : dans les vues (templates et includes), des conditions PHP vérifient le rôle stocké en session (`$_SESSION['user']['roles']`) pour afficher ou masquer certains éléments (ex : bouton « Supprimer » ou lien vers le dashboard admin).
2. **Contrôle d’accès métier** : dans les contrôleurs, avant d’exécuter une action sensible (suppression, modification, accès admin), le rôle de l’utilisateur est vérifié ; en cas de rôle insuffisant, l’utilisateur est redirigé ou reçoit une erreur HTTP 403.

Un exemple de vérification dans un contrôleur :

```
if (!in_array('admin', $_SESSION['user']['roles'])) {
    http_response_code(403);
    exit('Accès interdit');
}
```

Pour ajouter un nouveau rôle, il suffit de :

- Ajouter la valeur correspondante dans le JSON du champ `roles` pour l’utilisateur.
- Gérer la logique d’affichage et d’autorisation dans les contrôleurs et vues.

---

### 4.2 Gestion des articles

La gestion des articles est un élément central de MySafetyPlanet, permettant à la fois la publication de contenu par les administrateurs et la consultation publique par tous les visiteurs.

### Affichage public des articles

Sur la page **articles.php**, nous utilisons la méthode `findAll()` de **ArticleRepository** pour récupérer un tableau d’articles ordonné par date de publication décroissante. Pour chaque article, nous affichons :

- Le titre cliquable
- Un extrait du contenu (limité à 200 caractères)
- La date de création formatée (ex. `15/06/2025`)
- Un lien « Voir » dirigeant vers **article_show.php?id={id}**

La page **article_show.php** récupère l’article précis via `findById($id)`, puis affiche :

- Le titre en en-tête
- Le contenu complet, rendu depuis HTML généré par TinyMCE
- Le nom de l’auteur (relation avec **User**)
- Les dates de création et de dernière mise à jour

### Création et édition d’articles (espace admin)

Les administrateurs accèdent à **admin/article_list.php**, où un tableau liste tous les articles avec options **Créer**, **Modifier** et **Supprimer**. Le bouton **Créer un article** redirige vers **admin/article_form.php?action=create**, tandis que le bouton **Modifier** ajoute `action=edit&id={id}` à l’URL.

Le formulaire **admin/article_form.php** comporte deux champs principaux :

1. **Title** : champ texte validé pour ne pas être vide et contenir au moins 5 caractères.
2. **Content** : zone **TinyMCE**, configurée via **assets/js/tinymce.js** pour autoriser les styles de texte de base, les listes, les liens et l’insertion d’images.

En mode **create**, le formulaire est initialisé vide. En mode **edit**, les valeurs actuelles sont préremplies à partir de `findById()`. Nous incluons également un **champ caché** pour le **token CSRF**, généré par **CsrfToken**.

À la soumission :

- Nous collectons et sanitisons les données postées.
- Nous vérifions le token CSRF pour prévenir toute requête malveillante.
- Nous validons la présence et la longueur des champs.
- Nous appelons `ArticleRepository::saveArticle()` pour une création ou `updateArticle()` pour une mise à jour.
- En cas de succès, nous redirigeons vers la liste des articles avec un message flash indiquant « Article créé ! » ou « Article mis à jour ! ».
- En cas d’erreurs, nous réaffichons le formulaire en conservant les valeurs saisies et en affichant les messages d’erreur correspondants.

### Suppression d’articles

Dans **admin/article_list.php**, chaque ligne contient un lien **Supprimer** qui passe par `action=delete&id={id}` et inclut le **token CSRF**. Le contrôleur vérifie le token, puis appelle `ArticleRepository::deleteArticle($id)`. Après la suppression, un message flash « Article supprimé ! » est affiché.

---

### 4.3 Gestion des utilisateurs

Le module de gestion des utilisateurs permet aux administrateurs de créer, modifier, suspendre et supprimer des comptes, ainsi que de réinitialiser les mots de passe.

### Listing et vue d’ensemble

Sur la page **admin/user_list.php**, nous appelons la méthode `findAll()` du **UserRepository** pour récupérer un tableau d’objets utilisateur. Chaque ligne du tableau affiche :

- L’ID de l’utilisateur
- Le prénom et le nom
- L’adresse e-mail
- Le rôle principal
- Un indicateur de statut (actif, suspendu)

Des boutons **Modifier** et **Supprimer**, protégés par un token CSRF, sont placés en fin de chaque ligne pour effectuer les actions correspondantes.

### Création et édition des comptes

Le formulaire **admin/user_form.php** prend en charge deux modes : création (`action=create`) et édition (`action=edit&id={id}`).

- **Champs du formulaire** :
    - Prénom (`firstname`) et nom (`lastname`) : champs texte obligatoires.
    - Adresse e-mail : validée via `filter_var()` et vérifiée pour l’unicité avec `UserRepository::findByEmail()`.
    - Rôle : menu déroulant généré à partir des valeurs de l’**Enum UserRolesEnum**.
    - Mot de passe : champ optionnel en édition, mais obligatoire pour un nouveau compte. Validation de la longueur et correspondance avec la confirmation.
- **Processus** :
    1. Génération et inclusion d’un **token CSRF** caché.
    2. À la soumission, vérification du token et des règles de validation.
    3. Hachage du mot de passe avec `password_hash()` si fourni.
    4. Appel à `UserRepository::saveUser()` pour la création ou `updateUser()` pour la mise à jour.
    5. Redirection vers la liste avec un message flash (« Utilisateur créé ! » ou « Profil mis à jour ! »), ou réaffichage du formulaire en cas d’erreurs en conservant les valeurs saisies.

### Suppression et suspension

Le lien **Supprimer** dans **user_list.php** transmet `action=delete&id={id}` et le **token CSRF**. Le contrôleur vérifie le token, puis appelle `UserRepository::deleteUser($id)` pour effacer le compte ou le suspendre selon la politique choisie. Une fois l’opération terminée, l’administrateur est redirigé vers la liste avec un message flash.

### Réinitialisation du mot de passe

Depuis **admin/user_form.php**, l’administrateur peut définir un nouveau mot de passe pour un utilisateur sans connaître l’ancien. Le champ **Nouveau mot de passe** apparaît uniquement en mode édition. Après validation et hachage, `UserRepository::updatePassword()` enregistre la nouvelle valeur. Aucune notification par e-mail n’est envoyée actuellement, mais peut être ajoutée ultérieurement.

---

### 4.4 Interface utilisateur

L’interface utilisateur de MySafetyPlanet a été pensée pour offrir une expérience cohérente, accessible et responsive sur tous les appareils.

### Templates et includes

- Le fichier `templates/base.php` contient la structure HTML principale (balises `<head>`, inclusion du CSS, du header, du footer et des scripts JS). Toutes les pages étendent ce template pour garantir une mise en forme uniforme.
- Les fichiers `inc/header.php` et `inc/footer.php` sont inclus dans chaque vue pour afficher respectivement la barre de navigation et le pied de page. Cette approche favorise la réutilisabilité et facilite les modifications globales.

### Navigation responsive

- Le menu de navigation se trouve dans `inc/header.php` et utilise une structure `<nav>` avec des liens conditionnels selon le rôle de l’utilisateur (`user`, `admin`, `superadmin`).
- En mode mobile, un bouton **hamburger** bascule la classe CSS du menu grâce au script `assets/js/navbar.js`, qui gère l’ouverture et la fermeture du menu.
- Les media queries dans `assets/css/styles.css` adaptent l’affichage (taille de police, disposition en colonne ou en ligne) pour assurer la lisibilité sur smartphone et tablette.

### Styles et classes utilitaires

- Le fichier `assets/css/styles.css` utilise des unités en **pixels** pour un contrôle précis du positionnement et de la typographie.
- Des classes utilitaires (`.btn`, `.form-group`, `.table-responsive`) sont définies pour normaliser l’apparence des formulaires, boutons et tableaux.
- Les variables CSS (couleurs principales, polices, marges) sont centralisées en début de fichier pour faciliter la personnalisation du thème.

### Éditeur de contenu

- TinyMCE est initialisé via `assets/js/tinymce.js` dans tous les formulaires d’articles.
- La configuration restreint les fonctionnalités aux éléments essentiels (mise en gras, italique, listes à puces, liens, insertion d’images) afin de limiter les risques XSS.
- Les plugins **paste** et **lists** sont activés, et le filtrage HTML est appliqué côté serveur pour nettoyer le contenu avant enregistrement.

---

## 5. Base de données

1. **Table `users`**
    - **`id`** (INT, PRIMARY KEY, AUTO_INCREMENT) : identifiant unique.
    - **`firstname`**, **`lastname`** (VARCHAR(255), NOT NULL) : noms de l’utilisateur.
    - **`email`** (VARCHAR(255), NOT NULL, UNIQUE) : adresse de connexion.
    - **`password`** (VARCHAR(255), NOT NULL) : mot de passe haché.
    - **`roles`** (JSON, NOT NULL) : liste de rôles (ex. `["user"]`).
    - **`remember_me`** (VARCHAR(255), NULL) : token stocké pour le cookie « se souvenir de moi ».
2. **Table `articles`**
    - **`id`** (INT, PRIMARY KEY, AUTO_INCREMENT) : identifiant unique.
    - **`title`** (VARCHAR(255), NOT NULL) : titre de l’article.
    - **`content`** (TEXT, NOT NULL) : contenu HTML produit par TinyMCE.
    - **`user_id`** (INT, NULL) : clé étrangère vers `users.id`.
    - **`createdAt`** (DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP) : date de création.
    - **`updatedAt`** (DATETIME, NOT NULL, DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP) : date de dernière mise à jour.

### Contraintes et index

- **Clé étrangère** : `FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL` pour préserver les articles si l’utilisateur est supprimé.
- **Index** sur `users.email` pour accélérer la recherche par e-mail.
- **Index** sur `articles.createdAt` pour trier rapidement par date.

### Exemples de requêtes SQL

- Récupérer les 10 derniers articles :
    
    ```
    SELECT * FROM articles
    ORDER BY createdAt DESC
    LIMIT 10;
    ```
    
- Trouver un utilisateur par e-mail :
    
    ```
    SELECT * FROM users
    WHERE email = ?;
    ```
    
- Mettre à jour le rôle d’un utilisateur :
    
    ```
    UPDATE users
    SET roles = ?
    WHERE id = ?;
    ```
    

### Diagramme ERD (optionnel)

Un diagramme entité-relation peut être généré via MySQL Workbench ou dbdiagram.io pour visualiser les tables `users` et `articles` et leur relation.

Le script **database.sql** fournit la création complète du schéma, incluant les contraintes et les index, prêt à être exécuté pour initialiser la base.

## 6. Sécurité

### 6.1 Hachage des mots de passe

Les mots de passe utilisateur ne sont jamais stockés en clair. Nous utilisons la fonction native PHP `password_hash()` pour générer un hash sécurisé (BCRYPT) au moment de l’enregistrement ou de la modification du mot de passe. La vérification se fait via `password_verify()`, ce qui garantit la robustesse face aux attaques par force brute.

### 6.2 Injection SQL

Pour éviter toute injection SQL, toutes les interactions avec la base de données passent par des requêtes préparées (PDO). Chaque valeur utilisateur est liée à la requête via `bindValue()` ou passée en argument à `execute()`, ce qui empêche l’injection de code malveillant.

### 6.3 Cross-Site Scripting (XSS)

Toute donnée affichée issue d’une saisie utilisateur est échappée via `htmlspecialchars()` avant d’être injectée dans le HTML. Cela empêche l’exécution de scripts JavaScript non autorisés, protégeant ainsi la confiance des visiteurs.

### 6.4 Cross-Site Request Forgery (CSRF)

Un token CSRF unique est généré pour chaque session utilisateur et stocké en session. Chaque formulaire sensible (création, modification, suppression) inclut ce token en champ caché. Lors de la soumission, nous vérifions que le token envoyé correspond à celui stocké, bloquant ainsi les requêtes externes malveillantes.

### 6.5 Sessions et cookies sécurisés

- Les sessions PHP sont démarrées avec `session_start()` et configurées pour utiliser des cookies `HttpOnly`, limitant l’accès via JavaScript.
- Le cookie « Se souvenir de moi » contient un token aléatoire stocké en base (`remember_me`) et est marqué `HttpOnly` et `Secure` si HTTPS est activé, réduisant les risques de vol de cookie.

En combinant ces techniques, MySafetyPlanet offre un socle de sécurité solide, tout en restant compréhensible pour un développeur en apprentissage.

## 7. Installation du projet

Pour installer MySafetyPlanet en local :

1. **Prérequis** : PHP 8.x, MySQL 5.7+, Composer et Docker (optionnel).
2. **Cloner le dépôt** :
    
    ```bash
    git clone <url_du_repo>
    cd MySafetyPlanet-main
    ```
    
3. **Installer les dépendances PHP** :
    
    ```bash
    composer install
    ```
    
4. **Importer la base de données** :
    
    ```bash
    mysql -u <user> -p <database> < database/database.sql
    ```
    
5. **Configurer l’environnement** : copier `.env.example` en `.env` et renseigner les paramètres de connexion.
6. **Lancer l’application** :
    - **Sans Docker** :
        
        ```bash
        php -S localhost:8000 -t public
        
        ```
        
    - **Avec Docker** :
        
        ```bash
        docker-compose up -d
        
        ```
        
7. **Accéder à l’application** : ouvrez `http://localhost:8000` dans votre navigateur.

## 8. Hébergement avec Docker (Windows)

L’environnement Docker se compose de deux services :

- **web** : conteneur Apache + PHP configuré par le `Dockerfile`.
- **db** : conteneur MySQL avec un volume `db_data` pour la persistance des données.

Les commandes principales :

```bash
docker-compose build   # construit les images
docker-compose up -d   # démarre les services
docker-compose down    # arrête et supprime les conteneurs

```

Les sources sont montées dans `/var/www/html` et la base de données est exposée sur le port 3306, tandis que le site est accessible sur le port 8000.

## 9. Sauvegarde et restauration

Pour prévenir toute perte de données :

- **Sauvegarde manuelle** :
    
    ```bash
    mysqldump -u <user> -p <database> > backup_$(date +%F).sql
    
    ```
    
- **Restauration** :
    
    ```bash
    mysql -u <user> -p <database> < backup_YYYY-MM-DD.sql
    
    ```
    
- **Docker** : utilisez `docker exec <db_container> mysqldump ...` pour exporter depuis le conteneur.
- **Automatisation** : planifier un cron (Linux) ou une tâche planifiée (Windows) pour exécuter le dump périodiquement.

## 10. Évolutions possibles

Plusieurs axes d’amélioration peuvent être envisagés :

- Étendre la protection CSRF à toutes les actions et vues.
- Développer une **API REST** pour exposer les articles et utilisateurs en JSON.
- Ajouter un module de **commentaires** avec table dédiée.
- Mettre en place la **pagination** et la **recherche** avancée pour les listes.
- Intégrer un gestionnaire d’**upload d’images** avec validation du format et redimensionnement.

## 11. Difficultés techniques rencontrées

Durant la réalisation de ce projet, plusieurs défis ont dû être relevés :

1. **Comprendre l’architecture MVC**
    
    La séparation des modèles, vues et contrôleurs peut sembler abstraite au premier abord. Il faut apprendre à structurer son code pour que chaque couche ait une responsabilité distincte et éviter les mélanges entre logique métier et affichage.
    
2. **Maîtriser les sessions et les cookies**
    
    La gestion de `session_start()`, des durées de vie et des cookies « Se souvenir de moi » nécessite de comprendre la mécanique HTTP, les headers et les risques (vol de session, fixation de session).
    
3. **Mettre en place la sécurité**
    - **Hachage de mots de passe** : choisir la bonne fonction PHP et stocker correctement le hash.
    - **Requêtes préparées** : apprendre PDO, lier les paramètres et gérer les exceptions.
    - **Protection CSRF** : générer, stocker et vérifier un token dans chaque formulaire.
    - **Échappement XSS** : savoir quand et comment utiliser `htmlspecialchars()` pour chaque sortie.
4. **Routage manuel**
    
    Sans framework, il faut gérer les paramètres d’URL (`action`, `id`) soi-même, ce qui peut rapidement devenir source d’erreurs 404 ou de failles de sécurité.
    
5. **Autoloading et organisation du code**
    
    Apprendre Composer et la norme PSR-4 pour charger automatiquement les classes. Sans container de services, il faut parfois inclure manuellement des fichiers, ce qui peut entraîner des problèmes de namespace et de duplication.
    
6. **Intégration et configuration de TinyMCE**
    
    Ajouter un éditeur WYSIWYG implique de comprendre son API JavaScript, de restreindre les plugins pour des raisons de sécurité et de gérer le nettoyage du HTML côté serveur.
    
7. **Déploiement Docker sous Windows**
    - Comprendre la syntaxe des Dockerfile et docker-compose.yml.
    - Gérer les volumes partagés entre hôte et conteneur, les permissions de fichiers et la synchronisation du code.
    - Résoudre les problèmes de compatibilité réseau et de ports bloqués.
8. **Gestion des erreurs et logs**
    
    Apprendre à capturer et journaliser les exceptions PDO, afficher des messages d’erreur conviviaux sans divulguer d’informations sensibles, et mettre en place un mode debug vs production.
    
9. **Validation et retours utilisateur**
    
    Afficher de manière conviviale les erreurs de formulaire, conserver les données saisies en cas d’erreur et guider l’utilisateur pour corriger les champs invalides.
    
10. **Versioning et collaboration**
    - Utiliser Git pour suivre l’historique, gérer les conflits de fusion.
    - Structurer le `.gitignore` pour éviter de commiter les fichiers sensibles (mots de passe, tokens).

## 12. Annexe

**Ressources utiles** :

- Documentation PHP : [https://www.php.net/](https://www.php.net/)
- Documentation MySQL : [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)
- Documentation Docker : [https://docs.docker.com/](https://docs.docker.com/)