<?php
    require_once 'inc/header.php';

    use src\Entity\Article;
    use src\Repository\ArticleRepository;

    $articleRepository = new ArticleRepository($db);
    $articles = $articleRepository->findAll();
?>

<?php foreach($articles as $article) : ?>
    <article>
        <h1><?= $article->getTitle(); ?></h1>
        <p><?= "Créer le " . $article->getCreatedAt()->format("d/m/Y H:i") . " par " . $article->getUser()->getFirstname(); ?></p>
        <p><?= "Dernière mise à jour le " . $article->getUpdatedAt()->format("d/m/Y H:i"); ?> </p>
        <a class="btn_dashboard" href="<?= 'article_show.php?id=' . $article->getId(); ?>">
            Voir
        </a>
    </article>
<?php endforeach; ?>

<?= (count($articles) === 0) ? "Aucun article disponible" : ""; ?>

<?php
 require_once 'inc/footer.php';
?>