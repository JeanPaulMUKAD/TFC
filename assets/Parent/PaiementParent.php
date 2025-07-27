<?php
$message = "";
require_once __DIR__ . '/../Controllers/AuthController.php';
$auth = new AuthController();

// Récupérer dynamiquement les types de paiement via AuthController
$typesPaiement = [];
try {
    $sql = "SELECT id, nom_type FROM payementtype";
    $result = $auth->conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $typesPaiement[] = $row;
        }
    }
} catch (Exception $e) {
    $typesPaiement = [];
}

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['matricule'])) {
    header('Content-Type: application/json');
    echo json_encode($auth->getStudentInfoByMatricule($_GET['matricule']));
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Si c'est une requête AJAX pour le rapport
    if (isset($_POST['action']) && $_POST['action'] === 'fetch_report') {
        header('Content-Type: application/json');
        echo json_encode($auth->handlePaymentAndReport($_POST));
        exit;
    } else {
        $response = $auth->handlePaymentAndReport($_POST);
        $message = $response['message'];
    }
}
?>

<!doctype html>
<html lang="en" data-layout="vertical" data-topbar="light" data-sidebar="dark" data-sidebar-size="lg"
    data-sidebar-image="none" data-preloader="disable" data-theme="default" data-theme-colors="default">

<head>

    <meta charset="utf-8" />
    <title>Paiement en ligne| Administration C.S.P.P.UNILU</title>
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

    <script src="https://cdn.cinetpay.com/seamless/main.js"></script>

    <script>
        function checkout() {


            let matricule = document.getElementById("matricule").value.trim();
            let nomEleve = document.getElementById("nom_eleve").value.trim();
            let postnomEleve = document.getElementById("postnom_eleve").value.trim();
            let prenomEleve = document.getElementById("prenom_eleve").value.trim();
            let sexeEleve = document.getElementById("sexe_eleve").value;
            let classe = document.getElementById("classe_eleve").value;
            let nomParent = document.querySelector('input[name="nom_parent"]').value.trim();
            let adresseEleve = document.querySelector('input[name="adresse_eleve"]').value.trim();
            let montant = document.getElementById("montant_payer").value.trim();
            let devise = document.getElementById("devise").value;
            let motif = document.getElementById("motif_paiement").value.trim();
            let errorDiv = document.getElementById("form-error-message");
            errorDiv.innerHTML = "";



            if (!matricule || !nomEleve || !postnomEleve || !prenomEleve || !sexeEleve || !classe || !nomParent || !adresseEleve || !montant || !devise || !motif || parseFloat(montant) <= 0) {
                errorDiv.innerHTML = "Veuillez remplir tous les champs obligatoires correctement avant de confirmer le paiement.";
                return;
            }

            
            let transactionId = Math.floor(Math.random() * 100000000).toString();
          


            CinetPay.setConfig({
                apikey: '137270109768837fd94f8549.16939776',
                site_id: '105903173',
                notify_url: 'http://localhost:8080/assets/Parent/PaiementParent.php',
                mode: 'PRODUCTION'
            });

            CinetPay.getCheckout({
                transaction_id: transactionId,
                amount: parseFloat(montant),
                currency: devise === '$' ? 'USD' : 'CDF',
                channels: 'ALL',
                description: motif,
                customer_name: nomEleve + " " + postnomEleve + " " + prenomEleve,
                customer_email: "mukadjeanpaul@gmail.com",
                customer_phone_number: "0977199714",
                customer_address: adresseEleve,
                customer_city: "Lubumbashi",
                customer_country: "CD",
                customer_state: "Haut-Katanga",
                customer_zip_code: "12345"
            });

            CinetPay.waitResponse(function (data) {
                if (data.status === "REFUSED") {
                    alert("Votre paiement a échoué");
                    window.location.reload();
                } else if (data.status === "ACCEPTED") {
                    alert("Votre paiement a été effectué avec succès");

                    document.getElementById('payment_validated').value = "1";
                    document.getElementById('transaction_id').value = data.transaction_id;

                    document.querySelector("form").submit();
                }
            });
        }
    </script>


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
                                            <h5 class="text-primary text-center">Paiement en lignes</h5>
                                        </div>
                                        <?php echo $message; ?>
                                        <div id="form-error-message" class="text-danger mb-3 fw-semibold"></div>
                                        <div class="mt-4">
                                            <form class="needs-validation" novalidate method="POST">
                                              
                                                <input type="hidden" name="local_payment" value="1">
                                                <input type="hidden" name="payment_validated" id="payment_validated"
                                                    value="0">
                                                <input type="hidden" name="local_payment" value="1">
                                                <label for="matricule" class="form-label">Matricule <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="matricule" name="matricule"
                                                    placeholder="Entrez le matricule" required
                                                    onblur="chercherEleveParMatricule(this.value)">
                                                <div class="invalid-feedback">
                                                    Veuillez entrer le matricule de l'élève.
                                                </div>
                                                <!-- Nouveaux champs pour les informations de l'élève -->
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="nom_eleve" class="form-label">Nom <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="nom_eleve"
                                                            name="nom_eleve" placeholder="Nom" required>
                                                        <div class="invalid-feedback">
                                                            Veuillez entrer le nom de l'élève.
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="postnom_eleve" class="form-label">Postnom <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="postnom_eleve"
                                                            name="postnom_eleve" placeholder="Postnom" required>
                                                        <div class="invalid-feedback">
                                                            Veuillez entrer le postnom de l'élève.
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="prenom_eleve" class="form-label">Prénom <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="prenom_eleve"
                                                            name="prenom_eleve" placeholder="Prénom" required>
                                                        <div class="invalid-feedback">
                                                            Veuillez entrer le prénom de l'élève.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="sexe_eleve" class="form-label">Sexe <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="sexe_eleve" name="sexe_eleve"
                                                        required>
                                                        <option value="">Sélectionnez le sexe</option>
                                                        <option value="M">Masculin</option>
                                                        <option value="F">Féminin</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Veuillez sélectionner le sexe de l'élève.
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="classe_eleve" class="form-label">Classe <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="classe_eleve" name="classe_eleve"
                                                        required>
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
                                                    <label for="nom_parent" class="form-control">Nom du parent<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="nom_parent" class="form-control"
                                                        placeholder="Entrez le nom du parent(tuteur) de l'eleve"
                                                        required>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="adresse_eleve" class="form_control">Adresse<span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" name="adresse_eleve" class="form-control"
                                                        placeholder="Entrez l'adresse de l'eleve" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="montant_payer" class="form-label">Montant à payer <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="montant_payer"
                                                        name="montant_payer" placeholder="Entrez le montant à payer"
                                                        min="1" required>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="devise" class="form-label">Devise <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-control" id="devise" name="devise" required>
                                                        <option value="">Sélectionnez une devise</option>
                                                        <option value="$">USD</option>
                                                        <option value="Fc">CDF</option>
                                                    </select>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="motif_paiement" class="form-label">Motif du paiement
                                                        <span class="text-danger">*</span>
                                                    </label>
                                                    <select class="form-control" id="motif_paiement"
                                                        name="motif_paiement" required>
                                                        <option value="">Sélectionnez le motif du paiement</option>
                                                        <?php foreach ($typesPaiement as $type): ?>
                                                            <option value="<?= htmlspecialchars($type['nom_type']) ?>">
                                                                <?= htmlspecialchars($type['nom_type']) ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <div class="mt-4">
                                                    <button type="button" onclick="checkout()"
                                                        class="btn btn-success w-100">Confirmer</button>
                                                </div>

                                            </form>

                                            <hr class="my-4">




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

    <script>
        function chercherEleveParMatricule(matricule) {
            if (!matricule) return;

            fetch(window.location.pathname + '?matricule=' + encodeURIComponent(matricule))
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        // Remplir tous les champs
                        document.getElementById('nom_eleve').value = data.nom_eleve || '';
                        document.getElementById('postnom_eleve').value = data.postnom_eleve || '';
                        document.getElementById('prenom_eleve').value = data.prenom_eleve || '';
                        document.getElementById('sexe_eleve').value = data.sexe_eleve || '';
                        document.getElementById('classe_eleve').value = data.classe_selection || '';

                        // Correction pour les champs nom_parent et adresse_eleve
                        document.querySelector('input[name="nom_parent"]').value = data.nom_parent || '';
                        document.querySelector('input[name="adresse_eleve"]').value = data.adresse_eleve || '';

                        document.getElementById('form-error-message').textContent = '';
                    } else {
                        document.getElementById('form-error-message').textContent = data.message || 'Élève non trouvé';
                        // Réinitialiser les champs si élève non trouvé
                        document.querySelector('input[name="nom_parent"]').value = '';
                        document.querySelector('input[name="adresse_eleve"]').value = '';
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    document.getElementById('form-error-message').textContent = 'Erreur lors de la recherche';
                });
        }
    </script>

</body>


<!-- Mirrored from themesbrand.com/velzon/html/master/auth-signup-cover.html by HTTrack Website Copier/3.x [XR&CO'2014], Mon, 12 Aug 2024 07:46:59 GMT -->

</html>