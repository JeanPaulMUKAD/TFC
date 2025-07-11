<?php
    require_once __DIR__ . '/../Controllers/AuthController.php';

    $auth = new AuthController();
    $message = "";

    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        // INSCRIPTION
        if (isset($_POST['inscription_eleve'])) {
            $nom_eleve = trim($_POST['nom_eleve'] ?? '');
            $postnom_eleve = trim($_POST['postnom_eleve'] ?? '');
            $prenom_eleve = trim($_POST['prenom_eleve'] ?? '');
            $sexe_eleve = $_POST['sexe_eleve'] ?? '';
            $classe_selection = $_POST['classe_selection'] ?? '';
            $nom_parent = trim($_POST['nom_parent'] ?? '');
            $adresse_eleve = trim($_POST['adresse_eleve'] ?? ''); 
            $annee_inscription = trim($_POST['annee_inscription'] ?? '');

            if (
                empty($nom_eleve) ||
                empty($postnom_eleve) ||
                empty($prenom_eleve) ||
                empty($sexe_eleve) ||
                empty($classe_selection) ||
                empty($nom_parent) ||
                empty($adresse_eleve) ||
                empty($annee_inscription)
            ) {
                $message = "<p class='text-red-500 text-center'>Tous les champs sont requis pour l'inscription.</p>";
            } else {
                $result = $auth->enregistrerEleve(
                    $nom_eleve,
                    $postnom_eleve,
                    $prenom_eleve,
                    $sexe_eleve,
                    $classe_selection,
                    $nom_parent,
                    $adresse_eleve,
                    $annee_inscription
                );

                if ($result['success']) {
                    $message = "<p class='text-green-500 text-center'>" . htmlspecialchars($result['message']) . "</p>";
                } else {
                    $message = "<p class='text-red-500 text-center'>" . htmlspecialchars($result['message']) . "</p>";
                }
            }
        }

        // MODIFICATION
        if (isset($_POST['modifier_eleve'])) {
            $id = intval($_POST['id'] ?? 0);
            $nom_eleve = trim($_POST['nom_eleve'] ?? '');
            $postnom_eleve = trim($_POST['postnom_eleve'] ?? '');
            $prenom_eleve = trim($_POST['prenom_eleve'] ?? '');
            $sexe_eleve = $_POST['sexe_eleve'] ?? '';
            $classe_selection = $_POST['classe_selection'] ?? '';
            $nom_parent = trim($_POST['nom_parent'] ?? '');
            $adresse_eleve = trim($_POST['adresse_eleve'] ?? '');
            $annee_inscription = trim($_POST['annee_inscription'] ?? '');

            if (
                empty($id) || empty($nom_eleve) || empty($postnom_eleve) || empty($prenom_eleve) ||
                empty($sexe_eleve) || empty($classe_selection) || empty($nom_parent) ||
                empty($adresse_eleve) || empty($annee_inscription)
            ) {
                $message = "<p class='text-red-500 text-center'>Tous les champs sont requis pour la modification.</p>";
            } else {
                $result = $auth->modifierEleve(
                    $id,
                    $nom_eleve,
                    $postnom_eleve,
                    $prenom_eleve,
                    $sexe_eleve,
                    $classe_selection,
                    $nom_parent,
                    $adresse_eleve,
                    $annee_inscription
                );

                if ($result['success']) {
                    $message = "<p class='text-green-500 text-center'>" . htmlspecialchars($result['message']) . "</p>";
                } else {
                    $message = "<p class='text-red-500 text-center'>" . htmlspecialchars($result['message']) . "</p>";
                }
            }
        }

        // SUPPRESSION 
        if (isset($_POST['supprimer_eleve'])) {
            $matricule = trim($_POST['matricule'] ?? '');

            if (empty($matricule)) {
                $message = "<p class='text-red-500 text-center'>Veuillez saisir un matricule pour la suppression.</p>";
            } else {
                $result = $auth->supprimerEleveParMatricule($matricule);

                if ($result['success']) {
                    $message = "<p class='text-green-500 text-center'>" . htmlspecialchars($result['message']) . "</p>";
                } else {
                    $message = "<p class='text-red-500 text-center'>" . htmlspecialchars($result['message']) . "</p>";
                }
            }
        }
    }
?>


<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <title>Paiement en espèces | Administration C.S.P.P.UNILU</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="Premium Multipurpose Admin & Dashboard Template" name="description" />
    <meta content="Themesbrand" name="author" />
    <!-- App favicon -->
    <link rel="shortcut icon" href="assets/images/favicon.ico">

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

    <!-- App favicon -->
    <link rel="shortcut icon" href="../images/logo_pp.png">

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
                                            </div>
                                            <div class="mt-auto">
                                                <div class="mb-3">
                                                    <i class="ri-double-quotes-l display-4 text-success"></i>
                                                </div>

                                                <div id="qoutescarouselIndicators" class="carousel slide"
                                                    data-bs-ride="carousel">
                                                    <div class="carousel-indicators">
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="0" class="active" aria-current="true"
                                                            aria-label="Slide 1"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="1" aria-label="Slide 2"></button>
                                                        <button type="button" data-bs-target="#qoutescarouselIndicators"
                                                            data-bs-slide-to="2" aria-label="Slide 3"></button>
                                                    </div>
                                                    <div class="carousel-inner text-center text-white-50 pb-5">
                                                        <div class="carousel-item active">
                                                            <p class="fs-15 fst-italic">" Paiement sécurisé et
                                                                enregistrement rapide de toutes les transactions
                                                                scolaires. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Suivi simple et clair des
                                                                frais scolaires annuels."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Gestion efficace des paiements
                                                                élèves avec confirmation instantanée. "</p>
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
                                            <img src="../images/logo_pp2.png" alt="Logo"
                                                style="max-width: 90px; height: auto;">
                                        </div>
                                        <div>
                                            <h5 class="text-primary text-center">Inscription des élèves</h5>
                                        </div>
                                        <?php echo $message; ?>
                                        <div class="mt-4">
                                                
                                            <!-- FORMULAIRE D’INSCRIPTION -->
                                            <div id="form_inscription" style="display: block;">
                                                <form method="POST">
                                                    <input type="hidden" name="inscription_eleve" value="1">
                                                    <!-- Champs identiques -->
                                                    <div class="mb-3"><label>Nom</label><input type="text"
                                                            name="nom_eleve" class="form-control" placeholder="Veillez entre le nom" required></div>
                                                    <div class="mb-3"><label>Post-nom</label><input type="text"
                                                            name="postnom_eleve" class="form-control" placeholder="Veillez entrer le post-nom" required></div>
                                                    <div class="mb-3"><label>Prénom</label><input type="text"
                                                            name="prenom_eleve" class="form-control" placeholder="Veillez entrer le prénom" required></div>
                                                    <div class="mb-3"><label>Sexe</label>
                                                        <select name="sexe_eleve" class="form-control" required>
                                                            <option value="">Sélectionnez</option>
                                                            <option value="M">Masculin</option>
                                                            <option value="F">Féminin</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3"><label>Classe</label>
                                                        <select name="classe_selection" class="form-control" required>
                                                            <option value="">Choisir classe</option>
                                                            <option value="7e EB">7e EB</option>
                                                            <option value="8e EB">8e EB</option>
                                                            <option value="1ere SC">1ère SC</option>
                                                            <option value="1ere CG">1ère CG</option>
                                                            <option value="1ere HP">1ère HP</option>
                                                            <option value="1ere MG">1ère MG</option>
                                                            <option value="1ere ELECT">1ère ELECT</option>
                                                            <option value="2ere SC">2ère SC</option>
                                                            <option value="2ere CG">2ère CG</option>
                                                            <option value="2ere HP">2ère HP</option>
                                                            <option value="2ere MG">2ère MG</option>
                                                            <option value="2ere ELECT">2ère ELECT</option>
                                                            <option value="2eme TCC">2ème TCC</option>
                                                            <option value="3ere SC">3ère SC</option>
                                                            <option value="3ere CG">3ère CG</option>
                                                            <option value="3ere HP">3ère HP</option>
                                                            <option value="3ere MG">3ère MG</option>
                                                            <option value="3ere ELECT">3ère ELECT</option>
                                                            <option value="3eme TCC">3ème TCC</option>
                                                            <option value="4ere SC">4ère SC</option>
                                                            <option value="4ere CG">4ère CG</option>
                                                            <option value="4ere HP">4ère HP</option>
                                                            <option value="4ere MG">4ère MG</option>
                                                            <option value="4ere ELECT">4ère ELECT</option>
                                                            <option value="4eme TCC">4ème TCC</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3"><label>Nom du parent</label>
                                                        <input type="text" name="nom_parent" class="form-control"
                                                            placeholder="Entrez le nom du parent(tuteur) de l'eleve" required>
                                                    </div>
                                                    <div class="mb-3"><label>Adresse</label>
                                                        <input type="text" name="adresse_eleve" class="form-control"
                                                            placeholder="Entrez l'adresse de l'eleve" required>
                                                    </div>
                                                    <div class="mb-3"><label>Année d'inscription</label>
                                                        <input type="text" name="annee_inscription" class="form-control"
                                                            placeholder="(ex: 2024-2025)" required>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-primary" onclick="toggleForm('inscription')">Inscrire</button>
                                                        <button type="button" class="btn btn-warning" onclick="toggleForm('modifier')">Modifier</button>
                                                        <button type="button" class="btn btn-danger" onclick="toggleForm('supprimer')">Supprimer</button>
                                                    </div>
                                                    

                                                </form>
                                            </div>

                                            <!-- FORMULAIRE DE MODIFICATION -->
                                            <div id="form_modifier" style="display: none;">
                                                <form method="POST">
                                                    <input type="hidden" name="modifier_eleve" value="1">
                                                    <div class="mb-3"><label>Matricule </label><input type="text"
                                                            name="matricule_original" class="form-control" placeholder="Veillez entrer l'ancien matricule" required>
                                                    </div>
                                                    <!-- mêmes champs que l’inscription -->
                                                    <div class="mb-3"><label>Nouveau nom</label><input type="text"
                                                            name="nom_eleve" class="form-control" placeholder="Veillez entrer le nom" required></div>
                                                    <div class="mb-3"><label>Nouveau post-nom</label><input type="text"
                                                            name="postnom_eleve" class="form-control" placeholder="Veillez entrer le post-nom" required></div>
                                                    <div class="mb-3"><label>Nouveau prénom</label><input type="text"
                                                            name="prenom_eleve" class="form-control" placeholder="Veillez entrer le prénom" required></div>
                                                    <div class="mb-3"><label>Sexe</label>
                                                        <select name="sexe_eleve" class="form-control" required>
                                                            <option value="">Sélectionnez</option>
                                                            <option value="M">Masculin</option>
                                                            <option value="F">Féminin</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3"><label>Classe</label>
                                                        <select class="form-control" id="classe_selection" name="classe_selection" required>
                                                            <option value="">Choisir classe</option>
                                                            <option value="7e EB">7e EB</option>
                                                            <option value="8e EB">8e EB</option>
                                                            <option value="1ere SC">1ère SC</option>
                                                            <option value="1ere CG">1ère CG</option>
                                                            <option value="1ere HP">1ère HP</option>
                                                            <option value="1ere MG">1ère MG</option>
                                                            <option value="1ere ELECT">1ère ELECT</option>
                                                            <option value="2ere SC">2ère SC</option>
                                                            <option value="2ere CG">2ère CG</option>
                                                            <option value="2ere HP">2ère HP</option>
                                                            <option value="2ere MG">2ère MG</option>
                                                            <option value="2ere ELECT">2ère ELECT</option>
                                                            <option value="2eme TCC">2ème TCC</option>
                                                            <option value="3ere SC">3ère SC</option>
                                                            <option value="3ere CG">3ère CG</option>
                                                            <option value="3ere HP">3ère HP</option>
                                                            <option value="3ere MG">3ère MG</option>
                                                            <option value="3ere ELECT">3ère ELECT</option>
                                                            <option value="3eme TCC">3ème TCC</option>
                                                            <option value="4ere SC">4ère SC</option>
                                                            <option value="4ere CG">4ère CG</option>
                                                            <option value="4ere HP">4ère HP</option>
                                                            <option value="4ere MG">4ère MG</option>
                                                            <option value="4ere ELECT">4ère ELECT</option>
                                                            <option value="4eme TCC">4ème TCC</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3"><label>Nom du parent</label>
                                                        <input type="text" name="nom_parent" class="form-control"
                                                            placeholder="Entrez le nom du parent(tuteur) de l'eleve" required>
                                                    </div>
                                                    <div class="mb-3"><label>Adresse</label>
                                                        <input type="text" name="adresse_eleve" class="form-control"
                                                            placeholder="Entrez l'adresse de l'eleve" required>
                                                    </div>
                                                    <div class="mb-3"><label>Année d'inscription</label>
                                                        <input type="text" name="annee_inscription" class="form-control"
                                                            placeholder="(ex: 2024-2025)" required>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-warning" onclick="toggleForm('modifier')">Modifier</button>
                                                        <button class="btn btn-secondary" type="button" onclick="toggleForm('inscription')">Retour</button>
                                                    </div>
                                                    
                                                </form>
                                            </div>

                                            <!-- FORMULAIRE DE SUPPRESSION -->
                                            <div id="form_supprimer" style="display: none;">
                                                <form method="POST">
                                                    <input type="hidden" name="supprimer_eleve" value="1">
                                                    <div class="mb-3"><label>Matricule de l’élève</label><input
                                                            type="text" name="matricule" class="form-control" placeholder="Veillez entrer le matricule" required>
                                                    </div>
                                                    <div class="text-center">
                                                        <button type="submit" class="btn btn-danger" onclick="toggleForm('supprimer')">Supprimer</button>
                                                        <button class="btn btn-secondary" type="button" onclick="toggleForm('inscription')">Retour</button>
                                                    </div>
                                                    
                                                </form>
                                            </div>
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
                                <script>document.write(new Date().getFullYear())</script> Administration <i
                                    class="mdi mdi-heart text-success"></i> by C.S.P.P.UNILU
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
    <script src="assets/libs/node-waves/waves.min.js"></script>
    <script src="../libs/feather-icons/feather.min.js"></script>
    <script src="../js/pages/plugins/lord-icon-2.1.0.js"></script>
    <script src="../js/plugins.js"></script>

    <!-- validation init -->
    <script src="../js/pages/form-validation.init.js"></script>
    <!-- password create init -->
    <script src="../js/pages/passowrd-create.init.js"></script>
    
    <!-- JS Affichage des formulaires -->
    <script>
        function toggleForm(formType) {
            document.getElementById('form_inscription').style.display = 'none';
            document.getElementById('form_modifier').style.display = 'none';
            document.getElementById('form_supprimer').style.display = 'none';
            document.getElementById('form_' + formType).style.display = 'block';
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