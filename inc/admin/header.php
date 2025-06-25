<?php

require_once __DIR__ .'/../../autoload.php';

use src\Entity\Db;
use src\Service\Authentification;
use src\Repository\UserRepository;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$db = new Db();
$userRepository = new UserRepository($db);
$authentificationService = new Authentification();

// Si pas connecté et que l'on possède le cookie remember_token alors réaliser la connexion
if(!isset($_SESSION["user_id"]) && isset($_COOKIE["remember_me"])) {
    // vérifier que le cookie de l'utilisateur est le même que en bdd
    $cookie = $_COOKIE["remember_me"];
    $user = $userRepository->findOneByTokenRememberMe($cookie);

    // Si nous n'avons pas de user ne rien faire
    if($user) {
        // Alors on connecte l'utilisateur
        $_SESSION["user_id"] = $user->getId();
        $_SESSION["user_email"] = $user->getEmail();
        $_SESSION["user_firstname"] = $user->getFirstname();
        $_SESSION["user_lastname"] = $user->getLastname();
        $_SESSION["user_roles"] = $user->getRoles();
    };
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySafetyPlanet - Accueil</title>
    <link rel="stylesheet" href="../styles.css">
</head>
<body>

    <header>
        <div class="banner">
            <img src="../img/logo-banniere.jpg" alt="Bannière MySafetyPlanet">
        </div>

        <nav>
            <a href="../index.php">Accueil</a>
            <a href="../entreprise.php">Vous êtes une entreprise</a>
            <a href="../particulier.php">Vous êtes un particulier</a>
            <a href="../collectivite.php">Vous êtes une collectivité</a>
            <a href="../articles.php">Nos articles</a>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <a href="../profile.php">Mon Profil</a>
                <a href="../logout.php">Déconnexion</a>
                <?php if($authentificationService->hasUserRole('admin')): ?>
                    <a href="../admin/dashboard.php">Administration</a>
                <?php endif; ?>
            <?php else : ?>
                <a href="../login.php">Connexion</a>
                <a href="../creer-compte.php">Créer un compte</a>
            <?php endif; ?>
        </nav>

         <?php if (isset($_SESSION['user_id'])) : ?>
            <h1>
                <?= "Bonjour " . $_SESSION['user_firstname'] . " " . $_SESSION['user_lastname']; ?>
            </h1>
        <?php else : ?>
            <h1>Bienvenue sur MySafetyPlanet</h1>
        <?php endif; ?>

    </header>