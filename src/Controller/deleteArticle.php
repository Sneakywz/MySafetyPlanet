<?php

require_once __DIR__ .'/../../autoload.php';

use src\Entity\Db;
use src\Entity\Article;
use src\Service\CsrfToken;
use src\Service\Authentification;
use src\Repository\ArticleRepository;

try{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new \Exception("Méthode non autorisée");
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $csrfToken = new CsrfToken();
    if (!$csrfToken->isValid($_POST['csrf_token'] ?? '', 'article_delete_' . $_POST["id"])) {
        throw new \Exception("Token CSRF invalide");
    }

    $authentificationService = new Authentification();
    $authentificationService->requireRole('admin');

    $db = new Db();
    $articleRepository = new ArticleRepository($db);

    if (!isset($_POST["id"]) || empty($_POST["id"])) {
        echo "L'identifiant de l'article est manquant";
        exit;
    }

    $article = $articleRepository->findOneById(htmlspecialchars($_POST["id"]));

    if(!$article) {
        echo "L'article n'existe pas";
        exit;
    }
    
    if(!$authentificationService->hasRole("superadmin", $_SESSION["user_roles"])){
        echo "Vous ne pouvez pas faire cela";
        exit;
    }

    $articleRepository->delete($article);

    header("Location: ../../admin/article_list.php");
} catch(\Exception $e){
    echo "Une erreur est survenue durant la suppression de l'article :" . $e->getMessage();
}