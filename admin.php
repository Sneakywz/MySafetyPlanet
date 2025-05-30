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
    dd($users);
?>

    <h1>Administration</h1>

    <ul>
        <?php foreach($users as $user):?>
        <li>
            prénom : <?= $user->getFirstname();?>
        </li>
        <li>
            nom : <?= $user->getLastname();?>
        </li>
        <?php endforeach;?>
    </ul>
<?php
    require_once 'inc/footer.php';
?>

