<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $names = $_POST['Names_User'];
  $email = $_POST['Email'];
  $password = $_POST['Password_User'];
  $confirm_password = $_POST['Confirm_Password'];

  $check_sql = "SELECT * FROM Pupil WHERE Names_User = '$names'";
  $result = $conn->query($check_sql);

if ($result->num_rows > 0) {
    $message = "<p style='color: red; text-align: center;'>L'utilisateur avec ce nom est déjà enregistré.</p>";
} elseif ($conn->query("SELECT * FROM Pupil WHERE Email = '$email'")->num_rows > 0) {
    $message = "<p style='color: red; text-align: center;'>L'adresse email est déjà utilisée. Veuillez en choisir une autre.</p>";
}elseif ($password !== $confirm_password) {
    $message = "<p style='color: red; text-align: center;'>Les mots de passe ne correspondent pas. Veuillez confirmer le bon mot de passe.</p>";
}else{
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = "INSERT INTO Pupil (Names_User, Email, Password_User) VALUES ('$names', '$email', '$hashed_password')";

    if ($conn->query($sql) === TRUE) {
      $message = "<p style='color: green; text-align: center;'>Enregistrement réussi.</p>";
    } else {
      $message = "<p style='color: red; text-align: center;'>Erreur: " . $sql . "<br>" . $conn->error . "</p>";
    }
  }
}


$conn->close();
?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">


<!-- Mirrored from themesbrand.com/velzon/html/master/auth-signup-cover.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:59 GMT -->
<head>

    <meta charset="utf-8" />
    <title>Créer un compte | Administration C.S.P.P.UNILU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

    <!-- Layout config Js -->
    <script src="assets/js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="assets/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="assets/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="assets/css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="card overflow-hidden m-0 card-bg-fill galaxy-border-none">
                            <div class="row justify-content-center g-0">
                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4 auth-one-bg h-100">
                                        <div class="bg-overlay"></div>
                                        <div class="position-relative h-100 d-flex flex-column">
                                            <div class="mb-4">
                                                <a href="index.html" class="d-block">
                                                    <img src="assets/images/logout.png" alt="" height="50">
                                                </a>
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide" data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="0" class="active" aria-current="true" aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators" data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Super ! Code propre, design épuré, facile à personnaliser. Merci beaucoup ! "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Le thème est vraiment génial avec un support client incroyable."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Super ! Code propre, design épuré, facile à personnaliser. Merci beaucoup ! "</p>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- end carousel -->

                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <div class="p-lg-5 p-4">
                                        <div>
                                            <h5 class="text-primary">Créer un compte</h5>
                                            <p class="text-muted">Remplissez les informations ci-dessous pour créer un compte.</p>
                                        </div>
                                        <?php echo $message; ?>
                                        <div class="mt-4">
                                            <form class="needs-validation" novalidate method="POST">
                                                <div class="mb-3">
                                                    <label for="Names_User" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="Names_User" name="Names_User" placeholder="Entrez votre nom complet" required>
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer votre nom complet.
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="Email" class="form-label">Adresse email <span class="text-danger">*</span></label>
                                                    <input type="email" class="form-control" id="Email" name="Email" placeholder="Entrez votre adresse email" required>
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer une adresse email valide.
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="Password_User" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control" id="Password_User" name="Password_User" placeholder="Entrez un mot de passe" required>
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer un mot de passe.
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="Confirm_Password" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                                    <input type="password" class="form-control" id="Confirm_Password" name="Confirm_Password" placeholder="Confirmez votre mot de passe" required>
                                                    <div class="invalid-feedback">
                                                        Veuillez confirmer votre mot de passe.
                                                    </div>
                                                </div>
                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="submit">Créer un compte</button>
                                                </div>
                                            </form>
                                        </div>

                                        <div class="mt-5 text-center">
                                            <p class="mb-0">Vous avez déjà un compte ? <a href="auth-signin-cover.php" class="fw-semibold text-primary text-decoration-underline">Se connecter</a></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end card -->
                    </div>
                    <!-- end col -->

                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
        <!-- end auth page content -->

        <!-- footer -->
        <footer class="footer galaxy-border-none">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="text-center">
                            <p class="mb-0">&copy;
                                <script>document.write(new Date().getFullYear())</script> Administration <i class="mdi mdi-heart text-danger"></i> by C.S.P.P.UNILU
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
        <!-- end Footer -->
    </div>
    <!-- end auth-page-wrapper -->

    <!-- JAVASCRIPT -->
    <script src="assets/libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="assets/libs/simplebar/simplebar.min.js"></script>
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="assets/libs/feather-icons/feather.min.js"></script>
    <script src="assets/js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="assets/js/plugins.js"></script>

    <!-- validation init -->
    <script src="assets/js/pages/form-validation.init.js"></script>
    <!-- password create init -->
    <script src="assets/js/pages/passowrd-create.init.js"></script>
</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/auth-signup-cover.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:59 GMT -->
</html>