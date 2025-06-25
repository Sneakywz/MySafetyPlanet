<?php

require_once __DIR__ .'/../../autoload.php';

use src\Entity\Db;
use src\Entity\User;
use src\Service\CsrfToken;
use src\Service\Authentification;
use src\Repository\UserRepository;

try{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new \Exception("Méthode non autorisée");
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $csrfToken = new CsrfToken();
    if (!$csrfToken->isValid($_POST['csrf_token'] ?? '', 'user_delete_' . $_POST['id'])) {
        throw new \Exception("Token CSRF invalide");
    }

    $authentificationService = new Authentification();
    $authentificationService->requireRole('admin');

    $db = new Db();
    $userRepository = new UserRepository($db);

    if(!isset($_POST["id"]) || empty($_POST["id"])){
        echo "Aucun identifiant d'utilisateur fourni";
        exit;
    }

    if(!$authentificationService->isAuthenticated()){
        echo "Vous devez être connecté pour supprimer un utilisateur";
        exit;
    }

    $user = $userRepository->findOneById(htmlspecialchars($_POST["id"]));

    if($user && $user->getId() === $_SESSION["user_id"]){
        echo "Vous ne pouvez pas vous supprimer vous-même";
        exit;
    }

    if(!$user) {
        echo "L'utilisateur n'existe pas";
        exit;
    }
    
    if($authentificationService->hasRole("superadmin", $user->getRoles())){
        echo "Vous ne pouvez pas faire cela";
        // header("Refresh:5;url=../../admin.php"); Dommage il y a un problème :(
        exit;
    }

    $userRepository->delete($user);

    header("Location: ../../admin/user_list.php");
} catch(\Exception $e){
    echo "Une erreur est survenue durant la suppression de l'utilisateur :" . $e->getMessage();
}