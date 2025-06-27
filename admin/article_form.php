<?php
    require_once '../inc/admin/header.php';
    
    use src\Entity\Article;
    use src\Repository\ArticleRepository;

    $articleRepository = new ArticleRepository($db);

    $isEditing = isset($_GET["id"]); // Si l'id est présent, on est en mode édition sinon en mode ajout

    // Si nous sommes en mode édition, on récupère les informations de l'article
    if($isEditing){
        $id = htmlspecialchars($_GET["id"]);
        $article = $articleRepository->findOneById($id);

        if(!$article){
            echo "Cet article n'existe pas";
            exit;
        }
    }

    // Si je soumet le formulaire, on traite les données
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = isset($_POST["id"]) ? (int) htmlspecialchars($_POST["id"]) : null;
        $title = htmlspecialchars($_POST["title"]);
        $content = htmlspecialchars($_POST["content"]);

        // Si nous avons l'id, nous allons récupérer l'article en bdd, sinon new Article();
        $article = ($id) ? $articleRepository->findOneById($id) : new Article();
        $article->setUpdatedAt(new \DateTime());

        if (!$isEditing) {
            // assigné à l'article l'id de l'utilisateur connecté
            $user = $userRepository->findOneById($_SESSION['user_id']);
            $article->setUser($user);
            // assigné à l'article la date d'aujourd'hui au createdAt et à l'updateAt
            $article->setCreatedAt(new \DateTime());
        }

        if (
            isset($title) && empty($title) || 
            isset($content) && empty($content)
        )
        {
            echo "Tous les champs sont obligatoires.";
            exit;
        }

        $article->setTitle($title);
        $article->setContent($content);

        if($isEditing) {
            $articleRepository->updateArticle($article);
        } else {
            $articleRepository->create($article);
        }

        echo "L'article a été " . ($isEditing ? "modifié" : "créé") . " avec succès.";
    }
?>

    <section>
        <h2><?= $isEditing ? "Modifier" : "Créer"; ?> un article</h2>
    </section>

    <a href="article_list.php">Retour à la liste des articles</a>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?= $isEditing ? $article->getId() : ''; ?>">

        <label for="title">Titre</label>
        <input type="text" id="title" name="title" value="<?= $isEditing ? $article->getTitle() : ''; ?>">

        <label for="tinymce-editor">Contenu</label>
        <textarea id="tinymce-editor" name="content">
            <?= $isEditing ? $article->getContent() : ''; ?>
        </textarea>

        <button type="submit"><?= $isEditing ? "Modifier" : "Créer"; ?></button>
    </form>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/tinymce/7.9.1/tinymce.min.js" integrity="sha512-09JpfVm/UE1F4k8kcVUooRJAxVMSfw/NIslGlWE/FGXb2uRO1Nt4BXAJ3LxPqNbO3Hccdu46qaBPp9wVpWAVhA==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>    
    <script>
        tinymce.init({
            selector: 'textarea#tinymce-editor',
            height: 500,
            plugins: [
                'advlist', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview',
                'anchor', 'searchreplace', 'visualblocks', 'code', 'fullscreen',
                'insertdatetime', 'media', 'table', 'help', 'wordcount'
            ],
            toolbar: 'undo redo | blocks | ' +
            'bold italic backcolor | alignleft aligncenter ' +
            'alignright alignjustify | bullist numlist outdent indent | ' +
            'removeformat | help',
            content_style: 'body { font-family:Helvetica,Arial,sans-serif; font-size:16px }'
        });
    </script>

<?php
    require_once '../inc/admin/footer.php';
?>

