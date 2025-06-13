<?php
    require_once '../inc/admin/header.php';

    use src\Service\Authentification;
    use src\Entity\Db;
    use src\Repository\UserRepository;
    use src\Enum\UserRolesEnum;
    

    $authentificationService = new Authentification();
    $authentificationService->requireRole('admin');

    // Récupérer tout les utilisateurs
    // Grâce à la fonction findAll qui est dans UserRepository
    // Initialiser l'objet UserRepository
    $db = new Db();
    $userRepository = new UserRepository($db);
    $users = $userRepository->findAll();
?>

      <section id="box-section">
        <h2>Administration</h2>
       </section>


<div class="admin-container">
    <a href="user_form.php">Créer un utilisateur</a>
    <?php foreach($users as $user): ?>
        <div class="user-card">
            <p><strong>Prénom :</strong> <?= $user->getFirstname(); ?></p>
            <p><strong>Nom :</strong> <?= $user->getLastname(); ?></p>
            <p><strong>Email :</strong> <?= $user->getEmail(); ?></p>
            <p>
                <strong>Rôles :</strong>
                <ul>
                    <?php foreach ($user->getRoles() as $role) : ?>
                        <li><?= UserRolesEnum::from($role)->getLabel(); ?></li>
                    <?php endforeach; ?>
                </ul>
            </p>
        </div>
        <div>
            <?php if(!$authentificationService->hasRole("superadmin", $user->getRoles())) : ?>
                <a href="<?='../src/Controller/deleteUser.php?id=' . $user->getId(); ?>" onclick="return confirm('Etes-vous sûr de vouloir supprimer cette utilisateur ?');">Supprimer</a>
                <a href="<?= 'user_form.php?id=' . $user->getId(); ?>">Modifier</a>
            <?php endif; ?>
        </div>
        <hr>
    <?php endforeach; ?>
</div>

<?php


    require_once '../inc/admin/footer.php';
?>

