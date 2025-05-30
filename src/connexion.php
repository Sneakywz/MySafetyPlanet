<?php 

require_once __DIR__ .'/../autoload.php';

use src\Entity\Db;
use src\Entity\User;
use src\Repository\UserRepository;

$db = new Db();
$userRepository = new UserRepository($db);
$users = new User;

if($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = htmlspecialchars($_POST["email"]);
    $password = htmlspecialchars($_POST["password"]);

    // valide les données
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "L'adresse e-mail n'est pas valide.";
        exit;
    }

    if (
        isset($email) && empty($email) || 
        isset($password) && empty($password)) 
    {
        echo "Tous les champs sont obligatoires.";
        exit;
    }

    // Si l'utilsateur existe (le trouver en base de données)
    $user = $userRepository->findByEmail($email); // Soit User soit null

    // S'il est null renvoyer une erreur comme quoi l'utilisateur n'existe pas
    if ($user === null){
        echo "Identifiant incorrect.";
        exit;
    }

    // On demande l'inverse via le !, true devient false et false devient true
    // if (!false) -> retourne true

    // Si l'utilisateur existe, vérifier le mot de passe
    if (!password_verify($password, $user->getPassword())) {
        echo "Identifiant incorrect.";
        exit;
    }

    // Si le mot de passe est correct, on démarre la session

    // Si il n'y a pas de session, on la démarre
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // On stocke les informations de l'utilisateur dans la session
    $_SESSION["user_id"] = $user->getId();
    $_SESSION["user_email"] = $user->getEmail();
    $_SESSION["user_firstname"] = $user->getFirstname();
    $_SESSION["user_lastname"] = $user->getLastname();
    $_SESSION["user_roles"] = $user->getRoles();

    if(isset($_POST["remember_me"])){
        try {
            // créer un token
            $token = bin2hex(random_bytes(32));
            // Le stocker le token en base de donnée pour l'utilisateur
            $success = $userRepository->updateRememberMe($token, $user->getId());

            if ($success) {
                // Créer un cookie -> nom => valeur
                // time() correspond au nombre de second depuis le 1 janvier 1970
                $expiredAt = time() + (7 * 24 * 60 * 60); // le cookie duree 7 jours
                setcookie("remember_me", $token, $expiredAt, "/", "", true, false);
            }
        } catch (\Exception $e) {
            // Ne rien faire
        }
    }

    // Redirection vers la page d'accueil
    header("Location: ../index.php");
    exit;
}
