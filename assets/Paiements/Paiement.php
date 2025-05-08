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

    // Paiement en espèces
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['local_payment'])) {
    $nom_eleve = $conn->real_escape_string($_POST['nom_eleve']);
    $classe_eleve = $conn->real_escape_string($_POST['classe_eleve']);
    $montant_payer = $conn->real_escape_string($_POST['montant_payer']);
    $devise1 = $conn->real_escape_string($_POST['devise1']);
    $devise = $conn->real_escape_string($_POST['devise']);
    $motif_paiement = $conn->real_escape_string($_POST['motif_paiement']);
    $transaction_id = uniqid(); 
    $classe_selection = $conn->real_escape_string($_POST['classe_selection']);
    $total_annuel = $conn->real_escape_string($_POST['total_annuel']);
    $montant_payer .= $devise;

    $sql = "INSERT INTO eleve (nom_eleve, classe_eleve, montant_payer, motif_paiement, transaction_id, payment_status, classe_selection, total_annuel) 
            VALUES ('$nom_eleve', '$classe_eleve', '$montant_payer$devise', '$motif_paiement', '$transaction_id', 'payé', '$classe_selection', '$total_annuel$devise1')";

    if ($conn->query($sql) === TRUE) {
        $message = "<p class='text-green-500 text-center'>Le paiement de l'élève $nom_eleve s'est effectué avec succès.</p>";
    } else {
        $message = "<p class='text-red-500 text-center'>Erreur: " . $conn->error . "</p>";
    }
    }

    $conn->close();
?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg" data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <title>Paiement | Administration C.S.P.P.UNILU</title>
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

</head>

<body>

    <!-- auth-page wrapper -->
     
    <div class="auth-page-wrapper auth-bg-cover py-5 d-flex justify-content-center align-items-center min-vh-100">
        <div class="bg-overlay"></div>
        <!-- auth-page content -->
        <div class="auth-page-content overflow-hidden pt-lg-5">
            <div class="container">
                <div class="mb-4">
                    <a href="../../Dashboad.php" class="text-white text-sm font-medium hover:underline ">&larr; Retour vers la page analyse</a>
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
                                                            <p class="fs-15 fst-italic">" Paiement sécurisé et enregistrement rapide de toutes les transactions scolaires. "</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">"  Suivi simple et clair des frais scolaires annuels."</p>
                                                        </div>
                                                        <div class="carousel-item">
                                                            <p class="fs-15 fst-italic">" Gestion efficace des paiements élèves avec confirmation instantanée. "</p>
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
                                            <h5 class="text-primary">Paiement en espèces</h5>
                                        </div>
                                        <?php echo $message; ?>
                                        <div class="mt-4">
                                        <form class="needs-validation" novalidate method="POST">
                                            <div class="mb-3">
                                                <input type="hidden" name="local_payment" value="1">

                                                <label for="nom_eleve" class="form-label">Nom complet de l'élève <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="nom_eleve" name="nom_eleve" placeholder="Entrez le nom complet de l'élève" required>
                                                <div class="invalid-feedback">
                                                    Veuillez entrer le nom complet de l'élève.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="classe_selection" class="form-label">Classe <span class="text-danger">*</span></label>
                                                <select class="form-control" id="classe_selection" name="classe_selection" required>
                                                    <option value="">Sélectionnez une classe</option>
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
                                                <div class="invalid-feedback">
                                                    Veuillez sélectionner une classe.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="devise1" class="form-label">Devise <span class="text-danger">*</span></label>
                                                <select class="form-control" id="devise1" name="devise1" required>
                                                    <option value="">Sélectionnez une devise</option>
                                                    <option value="USD">$ - USD</option>
                                                    <option value="CDF">Fc - CDF</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Veuillez sélectionner une devise.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="total_annuel" class="form-label">Total annuel à payer <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="total_annuel" name="total_annuel" placeholder="Entrez le total annuel" min="1" required>
                                                <div class="invalid-feedback">
                                                    Veuillez entrer le total annuel à payer.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="classe_eleve" class="form-label">Classe de l'élève <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="classe_eleve" name="classe_eleve" placeholder="Entrez la classe de l'élève" required>
                                                <div class="invalid-feedback">
                                                    Veuillez entrer la classe de l'élève.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="montant_payer" class="form-label">Montant à payer <span class="text-danger">*</span></label>
                                                <input type="number" class="form-control" id="montant_payer" name="montant_payer" placeholder="Entrez le montant à payer" min="1" required>
                                                <div class="invalid-feedback">
                                                    Veuillez entrer le montant à payer.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="devise" class="form-label">Devise <span class="text-danger">*</span></label>
                                                <select class="form-control" id="devise" name="devise" required>
                                                    <option value="">Sélectionnez une devise</option>
                                                    <option value="$">USD</option>
                                                    <option value="Fc">CDF</option>
                                                </select>
                                                <div class="invalid-feedback">
                                                    Veuillez sélectionner une devise.
                                                </div>
                                            </div>

                                            <div class="mb-3">
                                                <label for="motif_paiement" class="form-label">Motif du paiement <span class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="motif_paiement" name="motif_paiement" placeholder="Entrez le motif du paiement" required>
                                                <div class="invalid-feedback">
                                                    Veuillez entrer le motif du paiement.
                                                </div>
                                            </div>

                                            <div class="mt-4">
                                                <button class="btn btn-success w-100" type="submit">Confirmer</button>
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
</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/auth-signup-cover.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:59 GMT -->
</html>