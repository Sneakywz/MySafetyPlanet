<?php
    require_once 'inc/header.php';

    use src\Entity\Article;
    use src\Repository\ArticleRepository;

    $articleRepository = new ArticleRepository($db);
    $article = $articleRepository->findOneById(htmlspecialchars($_GET["id"]));
    if(!$article){
        echo "L'article n'existe pas";
        exit;
    }
?>

<article>
    <h1><?= $article->getTitle(); ?></h1>
    <p><?= htmlspecialchars_decode($article->getContent()); ?></p>
    <p><?= "Créer le " . $article->getCreatedAt()->format("d/m/Y H:i") . " par " . $article->getUser()->getFirstname(); ?></p>
    <p><?= "Dernière mise à jour le " . $article->getUpdatedAt()->format("d/m/Y H:i"); ?> </p>

</article>


<?php


    require_once 'inc/footer.php';
?>