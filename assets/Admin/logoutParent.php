<?php
    session_start();
    session_unset(); // Supprime toutes les variables de session
    session_destroy(); // Détruit la session
    header("Location: /auth-signin-cover.php"); // Redirige vers votre page de connexion
    exit();
?>