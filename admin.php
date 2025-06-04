<?php
    require_once 'inc/header.php';

    use src\Service\Authentification;
    use src\Entity\Db;
    use src\Repository\UserRepository;

    $authentificationService = new Authentification();
    $authentificationService->requireRole('admin');

    // Récupérer tout les utilisateurs
    // Grâce à la fonction findAll qui est dans UserRepository
    // Initialiser l'objet UserRepository
    $db = new Db();
    $userRepository = new UserRepository($db);
    $users = $userRepository->findAll();
?>

    <h1 style="text-align: center;">Administration</h1>


<div class="admin-container">
    <?php foreach($users as $user): ?>
        <div class="user-card">
            <p><strong>Prénom :</strong> <?= $user->getFirstname(); ?></p>
            <p><strong>Nom :</strong> <?= $user->getLastname(); ?></p>
        </div>
    <?php endforeach; ?>
</div>

<?php
    require_once 'inc/footer.php';
?>

