<?php
    require_once 'inc/header.php';
?>

    <section>
        <div class="titre-centré">
            <h2>Créer un Compte</h2>
        </div>
        <form action="src/inscription.php" method="POST" class="login-form">
            <label for="firstname">Prénom :</label>
            <input type="text" id="firstname" name="firstname" required>
            <label for="lastname">Nom :</label>
            <input type="text" id="lastname" name="lastname" required>
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            <small>Nous n'utiliserons jamais votre adresse e-mail à des fins commerciales.</small>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <small>Le mot de passe doit contenir au moins 8 caractères.</small>
            <small>Il doit contenir au moins une lettre majuscule, une lettre minuscule et un chiffre.</small>
            <small>Il doit contenir au moins un caractère spécial.</small>
            <label for="confirm-password">Confirmer le mot de passe :</label>
            <input type="password" id="confirm-password" name="confirm-password" required>
            <button type="submit">Créer un compte</button>
        </form>
    </section>

<?php
    require_once 'inc/footer.php';
?>
