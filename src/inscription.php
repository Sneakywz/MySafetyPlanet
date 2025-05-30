<?php

require_once __DIR__ .'/../autoload.php';

use src\Entity\User;

use src\Entity\Db;
use src\Repository\UserRepository;

$db = new Db();
$userRepository = new UserRepository($db);

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $firstname = htmlspecialchars($_POST["firstname"]);
    $lastname = htmlspecialchars($_POST["lastname"]);
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);

    // vérifier que l'email n'est pas déjà utilisé
    // Récupérer l'utilisateur de la bdd avec cette email
    $user = $userRepository->findByEmail($email);

    // Si l'utilisateur existe renvoyer une erreur
    if ($user) {
        echo "L'adresse e-mail est déjà utilisée.";
        exit;
    }

    // valide les données
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "L'adresse e-mail n'est pas valide.";
        exit;
    }

    if (strlen($password) < 8) {
        echo "Le mot de passe doit contenir au moins 8 caractères.";
        exit;
    }

    if (isset($_POST["confirm-password"]) && $_POST["confirm-password"] !== $password) {
        echo "Les mots de passe ne correspondent pas.";
        exit;
    }

    if (
        isset($firstname) && empty($firstname) || 
        isset($lastname) && empty($lastname) || 
        isset($email) && empty($email) || 
        isset($password) && empty($password)) 
    {
        echo "Tous les champs sont obligatoires.";
        exit;
    }

    $user = new User();
    $user->setId(null);
    $user->setFirstname($firstname);
    $user->setLastname($lastname);
    $user->setEmail($email);
    $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
    $user->setRoles(["user"]);

    $userRepository->create($user);

    // Redirection vers la page d'accueil
    header("Location: ../index.php");
}
