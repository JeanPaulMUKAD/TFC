<?php
    if (isset ($_GET["search"]) and ($_GET["search"] == "./assets/Connexion/auth-signin-cover.php"))
     {
        include("./assets/Connexion/auth-signin-cover.php");
     }
     elseif(isset ($_GET["search"]) and ($_GET["search"] == "./assets/Connexion/auth-signup-cover.php"))
     {
        include("./assets/Connexion/auth-signup-cover.php");
     }
     elseif(isset ($_GET["search"]) and ($_GET["search"] == "./assets/Connexion/auth-pass-reset-cover.php"))
    {
        include("./assets/Connexion/auth-pass-reset-cover.php");
    }
     else
     {
         include("./assets/Connexion/auth-signin-cover.php");
     }
 ?>