<?php
    require_once 'inc/header.php';

    // dump($_SESSION);

    if (!isset($_SESSION["user_id"])) {
        echo "Vous n'êtes pas connecté";
        exit;
    }

    /** @var UserRepository $userRepository */
    $user = $userRepository->findOneById($_SESSION["user_id"]);

    if(!$user){
        echo "Utilisateur introuvable";
        exit;
    }

    // Traitement du formulaire de mise à jour
    if($_SERVER["REQUEST_METHOD"] === "POST") {
        $email = htmlspecialchars($_POST["email"]); // <script>alert('XSS');</script> devient &lt;script&gt;alert('XSS');&lt;/script&gt;
        $password = htmlspecialchars($_POST["password"]);
        $passwordVerify = htmlspecialchars($_POST["password_verify"]);

        // Validation des données

        // Validation de l'email
        if (isset($email) && !empty($email)) {
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Regex utiliser pour valider l'email
                echo "L'adresse e-mail n'est pas valide.";
                exit;
            }
            $user->setEmail($email);
        }

        // Validation du mot de passe

        // Il nous faut le mot de passe et la confirmation du mot de passe pour valider
        if (isset($password) && !empty($password) && isset($passwordVerify) && !empty($passwordVerify) ) {
             // On vérifie que les deux mots de passe sont identiques
            if($password !== $passwordVerify) {
                echo "Les mots de passe ne correspondent pas.";
                exit;
            }

            // Si les mots de passe sont identiques

            // On pourrait utiliser une regex pour valider la complexité du mot de passe
            // Par exemple, on peut vérifier qu'il contient au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial
            // ex : 
            // $passwordPattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/';
            // if (!preg_match($passwordPattern, $password)) {
            //     echo "Le mot de passe doit contenir au moins 8 caractères, une majuscule, une minuscule, un chiffre et un caractère spécial.";
            //     exit;
            // }

            if (strlen($password) < 8) {
                echo "Le mot de passe doit contenir au moins 8 caractères.";
                exit;
            }

            $passwordHash = password_hash($password, PASSWORD_BCRYPT);
            $user->setPassword($passwordHash);
        }

        // Mise à jour de l'utilisateur
        try{
            $userRepository->updateUser($user);
            $_SESSION["user_email"] = $user->getEmail(); //On pourrait faire une objet maj user pour limiter le risque d'erreur
            echo "Mise à jour des identifiants réussie";
        }
        catch(\Exception $e){
            echo "Une erreur est survenue durant la mise à jour du profile : " . $e->getMessage();
        }
    }
?>

    <form action="" method="POST">
        <label for="firstname">Prénom</label>
        <input type="text" id="firstname" value="<?= $user->getFirstname(); ?>" disabled>

        <label for="lastname">Nom</label>
        <input type="text" id="lastname" value="<?= $user->getLastname(); ?>" disabled>

        <label for="email">Email</label>
        <input type="email" id="email" value="<?= $user->getEmail(); ?>" name="email">

        <label for="password">Mot de passe</label>
        <input type="password" id="password" placeholder="Mot de passe" name="password">

        <label for="password_verify">Confirmer le mot de passe</label>
        <input type="password" id="password_verify" placeholder="Mot de passe" name="password_verify">

        <button type="submit">Mettre à jour</button>
    </form>

    

<?php

use src\Repository\UserRepository;
 require_once 'inc/footer.php';
?>
