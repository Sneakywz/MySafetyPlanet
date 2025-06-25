<?php
    require_once '../inc/admin/header.php';

    use src\Service\Authentification;
    use src\Entity\Db;
    use src\Repository\ArticleRepository;
    use src\Enum\ArticleRolesEnum;
    use src\Service\CsrfToken;

    $authentificationService = new Authentification();
    $authentificationService->requireRole('admin');

    // Récupérer tout les utilisateurs
    // Grâce à la fonction findAll qui est dans ArticleRepository
    // Initialiser l'objet ArticleRepository
    $db = new Db();
    $articleRepository = new ArticleRepository($db);
    $articles = $articleRepository->findAll();

    // Générer un token CSRF pour la sécurité
    $crsfToken = new CsrfToken();
?>

      <section id="box-section">
        <h2>Publication des Articles</h2>
       </section>


<div class="admin-container">
    <a href="article_form.php">Créer un article</a>
    <?php foreach($articles as $article): ?>
        <div class="article-card">
            <p><strong><?= $article->getTitle(); ?></strong></p>
            <p><strong>Créé le :</strong> <?= $article->getCreatedAt()->format("d/m/Y H:i"); ?></p>
            <p><strong>Créé par :</strong> <?= $article->getUser()->getFirstname() . " " . $article->getUser()->getLastname(); ?></p>
        </div>
        <div>
            <?php if($authentificationService->hasRole("superadmin", $_SESSION["user_roles"] )) : ?>
                <?php $token = $crsfToken->generate("article_delete_" . $article->getId()); ?>
                <form action="../src/Controller/deleteArticle.php" method="post">
                    <input type="hidden" name="id" value="<?= $article->getId(); ?>">
                    <input type="hidden" name="csrf_token" value="<?= $token ?>">
                    <button type="submit" onclick="return confirm('Etes-vous sûr de vouloir supprimer cet article ?');">
                        Supprimer
                    </button>
                </form>
            <?php endif; ?>

            <a href="<?= 'article_form.php?id=' . $article->getId(); ?>">Modifier</a>
            <a href="<?= '../article_show.php?id=' . $article->getId(); ?>">Voir</a>
        </div>
    <?php endforeach; ?>
</div>

<?php
    require_once '../inc/admin/footer.php';
?>