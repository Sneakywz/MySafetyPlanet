<?php

require_once __DIR__ .'/../../autoload.php';

use src\Entity\Db;
use src\Entity\User;
use src\Service\Authentification;
use src\Repository\UserRepository;

try{
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $authentificationService = new Authentification();
    $db = new Db();
    $userRepository = new UserRepository($db);
    $user = $userRepository->findOneById(htmlspecialchars($_GET["id"]));

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





?>