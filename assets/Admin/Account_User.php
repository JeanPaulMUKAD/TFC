<?php
    require_once __DIR__ . '/../Controllers/AuthController.php';

    $auth = new AuthController();
    $message = "";
    $modifyMessage = "";
    $deleteMessage = "";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $action = $_POST['action'] ?? '';

        if ($action === 'ajouter') {
            $names = $_POST['Names_User'];
            $email = $_POST['Email'];
            $password = $_POST['Password_User'];
            $confirmPassword = $_POST['Confirm_Password'];
            $role = $_POST['Role_User'];

            $result = $auth->register($names, $email, $password, $confirmPassword, $role);
            $message = $result['success']
                ? "<p style='color: green; text-align: center;'>{$result['message']}</p>"
                : "<p style='color: red; text-align: center;'>{$result['message']}</p>";
        }elseif ($action === 'modifier') {
            $result = $auth->modifyParent(
                trim($_POST['Old_Names_User'] ?? ''),
                trim($_POST['New_Names_User'] ?? ''),
                trim($_POST['New_Email'] ?? ''),
                $_POST['New_Password'] ?? '',
                $_POST['Confirm_New_Password'] ?? ''
            );
            $modifyMessage = $result['message'];
        } elseif ($action === 'supprimer') {
            $result = $auth->deleteParent(
                trim($_POST['Delete_Names_User'] ?? '')
            );
            $deleteMessage = $result['message'];
        }
    }
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
    <link rel="shortcut icon" href="../images/favicon.ico">

    <!-- Layout config Js -->
    <script src="../js/layout.js"></script>
    <!-- Bootstrap Css -->
    <link href="../css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="../css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="../css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <link href="../css/custom.min.css" rel="stylesheet" type="text/css" />

</head>

<body>

    <!-- auth-page wrapper -->
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <!-- Lien de retour -->
                <div class="mb-4">
                    <a href="../Admin/Acceuil_Admin.php" class="text-white text-sm font-medium hover:underline ">&larr; Retour vers la page analyse</a>
                </div>
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
                                        <div class="text-center my-3">
                                            <img src="../images/logo_pp2.png" alt="Logo" style="max-width: 90px; height: auto;">
                                        </div>
                                        
                                        <?php echo $message; ?>
                                        <div class="mt-4" id="form_add">
                                            <form class="needs-validation" novalidate method="POST">
                                                <input type="hidden" name="action" value="ajouter">
                                                <h5 class="text-primary text-center">Créer un compte utilisateur</h5>
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

                                                <div class="mb-3">
                                                    <label for="Role_User" class="form-label">Rôle de l'utilisateur <span class="text-danger">*</span></label>
                                                    <select class="form-control" id="Role_User" name="Role_User" required>
                                                        <option value="">Sélectionner le rôle</option>
                                                        <option value="parent">Parent</option>
                                                        <option value="prefet">Prefet</option>
                                                        <option value="sec">Secrétaire</option>
                                                        <option value="caissier">Caissier</option>
                                                    </select>
                                                </div>

                                                <div class="d-flex justify-content-between">
                                                    <button class="btn btn-success w-100 me-2" type="submit">Créer un compte</button>
                                                    <button class="btn btn-warning w-100 me-2" type="button"onclick="showForm('modify')">Modifier</button>
                                                    <button class="btn btn-danger w-100" type="button"onclick="showForm('delete')">Supprimer</button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Formulaire MODIFICATION -->
                                        <div id="form_modify" style="display: none;">
                                            <h5 class="text-primary text-center">Modifier un utilisateur</h5>
                                            <?php echo $modifyMessage; ?>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="modifier">
                                                <div class="mb-2">
                                                    <label for="Names_User" class="form-label">Ancien nom complet <span class="text-danger">*</span></label>
                                                    <input type="text" name="Old_Names_User" class="form-control"placeholder="Ancien nom" required>
                                                </div>
                                                <div class="mb-2">
                                                     <label for="Names_User" class="form-label">Nouveau nom complet <span class="text-danger">*</span></label>
                                                    <input type="text" name="New_Names_User" class="form-control"placeholder="Nouveau nom" required>
                                                </div>
                                                <div class="mb-2">
                                                    <label for="Names_User" class="form-label">Email <span class="text-danger">*</span></label>
                                                    <input type="email" name="New_Email" class="form-control"placeholder="Nouvel Email" required>
                                                </div>
                                                <div class="mb-2">
                                                     <label for="Names_User" class="form-label">Mot de passe <span class="text-danger">*</span></label>
                                                    <input type="password" name="New_Password" class="form-control"placeholder="Nouveau mot de passe" required>
                                                </div>
                                                <div class="mb-3">
                                                    
                                                     <label for="Names_User" class="form-label">Confirmer le mot de passe <span class="text-danger">*</span></label>
                                                    <input type="password" name="Confirm_New_Password"class="form-control" placeholder="Confirmer nouveau mot de passe" required>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <button class="btn btn-primary w-100 me-2" type="submit">Valider les modifications</button>
                                                    <button class="btn btn-secondary w-100" type="button"onclick="showForm('add')">Retour</button>
                                                </div>
                                            </form>
                                        </div>

                                        <!-- Formulaire SUPPRESSION -->
                                        <div id="form_delete" style="display: none;">
                                            <h5 class="text-primary text-center">Supprimer un utilisateur</h5>
                                            <?php echo $deleteMessage; ?>
                                            <form method="POST">
                                                <input type="hidden" name="action" value="supprimer">
                                                <div class="mb-3">
                                                     <label for="Names_User" class="form-label">Nom complet <span class="text-danger">*</span></label>
                                                    <input type="text" name="Delete_Names_User" class="form-control"placeholder="Nom à supprimer" required>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <button class="btn btn-danger w-100 me-2" type="submit"onclick="return confirm('Confirmer suppression ?')">Supprimer</button>
                                                    <button class="btn btn-secondary w-100" type="button"onclick="showForm('add')">Retour</button>
                                                </div>
                                            </form>
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
                                <script>document.write(new Date().getFullYear())</script> Administration <i class="mdi mdi-heart text-success"></i> by C.S.P.P.UNILU
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
    <script src="../libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/simplebar/simplebar.min.js"></script>
    <script src="../libs/node-waves/waves.min.js"></script>
    <script src="../libs/feather-icons/feather.min.js"></script>
    <script src="../js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="../js/plugins.js"></script>

    <!-- validation init -->
    <script src="../js/pages/form-validation.init.js"></script>
    <!-- password create init -->
    <script src="../js/pages/passowrd-create.init.js"></script>

    <script>
        function showForm(form) {
            document.getElementById("form_add").style.display = "none";
            document.getElementById("form_modify").style.display = "none";
            document.getElementById("form_delete").style.display = "none";
            document.getElementById("form_" + form).style.display = "block";
        }
    </script>

    <!-- SEARCH LOGO -->
    <script>
        let a = 0;
        let masque = document.createElement('div');
        let logo = document.createElement('img');
        let cercle = document.createElement('div');

        let angle = 0;
        let scale = 1;
        let opacityLogo = 1;

        window.addEventListener('load', () => {
            a = 1;

            // Le cercle et le logo commencent à bouger immédiatement
            anime = setInterval(() => {
                angle += 10; // Vitesse de rotation du cercle
                cercle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;

                // Zoom progressif du logo
                scale += 0.005;
                opacityLogo -= 0.005;

                logo.style.transform = `scale(${scale})`;
                logo.style.opacity = opacityLogo;

            }, 20);

            // Après 1 seconde, on arrête l'animation
            setTimeout(() => {
                clearInterval(anime);
                masque.style.opacity = '0';
            }, 1000);

            setTimeout(() => {
                masque.style.visibility = 'hidden';
            }, 1500);
        });

        // Création du masque
        masque.style.width = '100%';
        masque.style.height = '100vh';
        masque.style.zIndex = 100000;
        masque.style.background = '#ffffff';
        masque.style.position = 'fixed';
        masque.style.top = '0';
        masque.style.left = '0';
        masque.style.opacity = '1';
        masque.style.transition = '0.5s ease';
        masque.style.display = 'flex';
        masque.style.justifyContent = 'center';
        masque.style.alignItems = 'center';
        document.body.appendChild(masque);

        // Création du logo
        logo.setAttribute('src', '../images/logo_pp.png');
        logo.style.width = '10vh';
        logo.style.height = '10vh';
        logo.style.position = 'relative';
        logo.style.zIndex = '2';
        logo.style.transition = '0.2s'; // Transition pour plus de fluidité
        masque.appendChild(logo);

        // Création du cercle autour du logo
        cercle.style.width = '15vh';
        cercle.style.height = '15vh';
        cercle.style.border = '3px solid #0ab39c';
        cercle.style.borderTop = '3px solid #405189';
        cercle.style.borderRadius = '50%';
        cercle.style.position = 'absolute';
        cercle.style.top = '50%';
        cercle.style.left = '50%';
        cercle.style.transform = 'translate(-50%, -50%)';
        cercle.style.boxSizing = 'border-box';
        cercle.style.zIndex = '1';
        masque.appendChild(cercle);

        // Variables de l'animation
        let anime;

    </script>
</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/auth-signup-cover.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:59 GMT -->
</html>