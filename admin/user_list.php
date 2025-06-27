<?php
    require_once '../inc/admin/header.php';

    use src\Service\Authentification;
    use src\Entity\Db;
    use src\Repository\UserRepository;
    use src\Enum\UserRolesEnum;
    use src\Service\CsrfToken;

    $authentificationService = new Authentification();
    $authentificationService->requireRole('admin');

    // Récupérer tout les utilisateurs
    // Grâce à la fonction findAll qui est dans UserRepository
    // Initialiser l'objet UserRepository
    $db = new Db();
    $userRepository = new UserRepository($db);
    $users = $userRepository->findAll();

    $crsfToken = new CsrfToken();
?>

      <section>
        <h2 class="titre_section">Administration</h2>
       </section>


<div class="admin_user-list">
    <a class="btn_dashboard" href="user_form.php">Créer un utilisateur</a>
    <?php foreach($users as $user): ?>
        <div class="admin-card">
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
        <div class="admin-actions">
            <?php if(!$authentificationService->hasRole("superadmin", $user->getRoles())) : ?>
                <?php if($user->getId() !== $_SESSION["user_id"]) : ?>
                    <?php $token = $crsfToken->generate("user_delete_" . $user->getId()); ?>

                    <form action="../src/Controller/deleteUser.php" method="post">
                        <input type="hidden" name="id" value="<?= $user->getId(); ?>">
                        <input type="hidden" name="csrf_token" value="<?= $token ?>">
                        <button type="submit" onclick="return confirm('Etes-vous sûr de vouloir supprimer cette utilisateur ?');">
                            Supprimer
                        </button>
                    </form>
                <?php endif; ?>
                <a class="btn_dashboard" href="<?= 'user_form.php?id=' . $user->getId(); ?>">Modifier</a>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

<?php
    require_once '../inc/admin/footer.php';
?>

