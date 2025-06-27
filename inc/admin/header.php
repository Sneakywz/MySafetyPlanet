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
    <title>MySafetyPlanet - Adminsitration</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../assets/css/styles.css">
</head>
<body>

    <header class="header">
        <div class="header_banner">
            <img src="../assets/img/logo-banniere.jpg" alt="Bannière MySafetyPlanet">
        </div>

        <nav id="navbar">
            <div id="burger"><i class="fa-solid fa-bars"></i></div>
            <ul>
                <li>
                    <a href="../index.php">
                        <i class="fa-solid fa-house"></i>Accueil
                    </a>
                </li>
                <li>
                    <a href="../entreprise.php">
                        <i class="fa-solid fa-building"></i>
                        Vous êtes une entreprise
                    </a>
                </li>
                <li>
                    <a href="../particulier.php">
                        <i class="fa-solid fa-person-shelter"></i>
                        Vous êtes un particulier
                    </a>
                </li>
                <li>
                    <a href="../collectivite.php">
                        <i class="fa-solid fa-people-group"></i>
                        Vous êtes une collectivité
                    </a>
                </li>
                <li>
                    <a href="../articles.php">
                        <i class="fa-regular fa-newspaper"></i>
                        Nos articles
                    </a>
                </li>
                <?php if (isset($_SESSION['user_id'])) : ?>
                    <li>
                        <a href="../profile.php" title="Mon Profil">
                            <i class="fa-solid fa-user"></i>
                            
                        </a>
                    </li>
                    <li>
                        <a href="../logout.php" title="Déconnexion">
                            <i class="fa-solid fa-right-from-bracket" ></i>
                            
                        </a>
                    </li>
                    <?php if($authentificationService->hasUserRole('admin')): ?>
                        <li>
                            <a href="../admin/dashboard.php" title="Administration">
                                <i class="fa-solid fa-screwdriver-wrench"></i>
                            </a>
                        </li>
                    <?php endif; ?>
                <?php else : ?>
                    <li>
                        <a href="../login.php">
                            <i class="fa-solid fa-right-to-bracket"></i>
                            
                        </a>
                    </li>
                    <li>
                        <a href="../creer-compte.php">
                            <i class="fa-solid fa-user-plus"></i>
                            
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav>

        <?php if (isset($_SESSION['user_id'])) : ?>
            <h1 class="header_welcome">
                <?= "Bonjour " . $_SESSION['user_firstname'] . " " . $_SESSION['user_lastname']; ?>
            </h1>
        <?php else : ?>
            <h1 class="header_welcome">Bienvenue sur MySafetyPlanet</h1>
        <?php endif; ?>
    </header>