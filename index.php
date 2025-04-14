<?php
    if (isset ($_GET["search"]) and ($_GET["search"] == "auth-signin-cover.php"))
     {
        include("auth-signin-cover.php");
     }
     elseif(isset ($_GET["search"]) and ($_GET["search"] == "auth-signup-cover.php"))
     {
        include("auth-signup-cover.php");
     }
     elseif(isset ($_GET["search"]) and ($_GET["search"] == "auth-pass-reset-cover.php"))
    {
        include("auth-pass-reset-cover.php");
    }
     else
     {
         include("auth-signin-cover.php"); // Modification ici
     }
 ?>