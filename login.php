<?php
    require_once 'inc/header.php';
?>

    <section>
        <div class="titre-centré">
            <h2>Login</h2>
        </div>
        <form action="src/connexion.php" method="POST" class="login-form">
            <label for="email">Email :</label>
            <input type="email" id="email" name="email" required>
            <label for="password">Mot de passe :</label>
            <input type="password" id="password" name="password" required>
            <button type="submit">Se connecter</button>
        </form>
    </section>

<?php
 require_once 'inc/footer.php';
?>

