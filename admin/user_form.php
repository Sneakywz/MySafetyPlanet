<?php
    require_once '../inc/admin/header.php';

    use src\Entity\User;

    $isEditing = isset($_GET["id"]); // Si l'id est présent, on est en mode édition sinon en mode ajout

    // Si nous sommes en mode édition, on récupère les informations de l'utilisateur
    if($isEditing){
        $id = htmlspecialchars($_GET["id"]);
        $user = $userRepository->findOneById($id);

        if(!$user){
            echo "Cet utilisateur n'existe pas";
            exit;
        }
    }

    // Si je soumet le formulaire, on traite les données
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $id = isset($_POST["id"]) ? (int) htmlspecialchars($_POST["id"]) : null;
        $firstname = htmlspecialchars($_POST["firstname"]);
        $lastname = htmlspecialchars($_POST["lastname"]);
        $email = htmlspecialchars($_POST["email"]);

        $user = $userRepository->findOneById($id);

        // vérifier que l'email n'est pas déjà utilisé
        // Récupérer l'utilisateur de la bdd avec cette email
        if(!$isEditing || (isset($user) && $user->getEmail() !== $email)) {
            $userWithEmail = $userRepository->findByEmail($email);

            if ($userWithEmail) {
                echo "L'adresse e-mail est déjà utilisée.";
                exit;
            }
        }

        // valide les données
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo "L'adresse e-mail n'est pas valide.";
            exit;
        }

        if (!$isEditing) {
            $password = htmlspecialchars($_POST["password"]);
            $passwordVerify = htmlspecialchars($_POST["password_verify"]);

            if (strlen($password) < 8) {
                echo "Le mot de passe doit contenir au moins 8 caractères.";
                exit;
            }

            if (isset($_POST["password_verify"]) && $_POST["password_verify"] !== $password) {
                echo "Les mots de passe ne correspondent pas.";
                exit;
            }
        }

        if (
            isset($firstname) && empty($firstname) || 
            isset($lastname) && empty($lastname) || 
            isset($email) && empty($email) || 
            (!$isEditing && (empty($password) || empty($passwordVerify)))
        )
        {
            echo "Tous les champs sont obligatoires.";
            exit;
        }

        if($isEditing) {
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);

            $userRepository->updateUser($user);
        } else {
            $user = new User();
            $user->setFirstname($firstname);
            $user->setLastname($lastname);
            $user->setEmail($email);
            $user->setPassword(password_hash($password, PASSWORD_BCRYPT));
            $user->setRoles(["user"]);

            $userRepository->create($user);
        }

        // header("Location: user_form.php");
    }
?>

    <section id="box-section">
        <h2><?= $isEditing ? "Modifier" : "Créer"; ?> un utilisateur</h2>
    </section>

    <a href="user_list.php">Retour à la liste des utilisateurs</a>

    <form action="" method="POST">
        <input type="hidden" name="id" value="<?= $isEditing ? $user->getId() : ''; ?>">

        <label for="firstname">Prénom</label>
        <input type="text" id="firstname" name="firstname" value="<?= $isEditing ? $user->getFirstname() : ''; ?>">

        <label for="lastname">Nom</label>
        <input type="text" id="lastname" name="lastname" value="<?= $isEditing ?  $user->getLastname() : ''; ?>">

        <label for="email">Email</label>
        <input type="email" id="email" value="<?= $isEditing ? $user->getEmail() : ''; ?>" name="email">

        <?php if(!$isEditing) : ?>
            <label for="password">Mot de passe</label>
            <input type="password" id="password" placeholder="Mot de passe" name="password">

            <label for="password_verify">Confirmer le mot de passe</label>
            <input type="password" id="password_verify" placeholder="Mot de passe" name="password_verify">
        <?php endif; ?>

        <button type="submit"><?= $isEditing ? "Modifier" : "Créer"; ?></button>
    </form>

<?php

    // <?php if($authentificationService->hasRole("superadmin", $_SESSION["user_roles"])) : 
    //     <label for="role">Rôle</label>
    //     <select name="role" id="role">
    //         <option value="user">Utilisateur</option>
    //         <option value="admin">Administrateur</option>
    //     </select>
    // <?php endif;

    require_once '../inc/admin/footer.php';
?>

