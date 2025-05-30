<?php

require_once __DIR__ .'/../autoload.php';

use src\Service\Authentification;

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$authentificationService = new Authentification();

?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MySafetyPlanet - Accueil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

    <header>
        <div class="banner">
            <img src="img/logo-banniere.jpg" alt="Bannière MySafetyPlanet">
        </div>

        <?php if (isset($_SESSION['user_id'])) : ?>
            <h1>
                <?= "Bonjour " . $_SESSION['user_firstname'] . " " . $_SESSION['user_lastname']; ?>
            </h1>
        <?php else : ?>
            <h1>Bienvenue sur MySafetyPlanet</h1>
        <?php endif; ?>

        <nav>
            <a href="index.php">Accueil</a>
            <a href="entreprise.php">Vous êtes une entreprise</a>
            <a href="particulier.php">Vous êtes un particulier</a>
            <a href="collectivite.php">Vous êtes une collectivité</a>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <a href="logout.php">Déconnexion</a>
                <?php if($authentificationService->hasRole('admin')): ?>
                    <a href="admin.php">Administration</a>
                <?php endif; ?>
            <?php else : ?>
                <a href="login.php">Connexion</a>
                <a href="creer-compte.php">Créer un compte</a>
            <?php endif; ?>
        </nav>
    </header>