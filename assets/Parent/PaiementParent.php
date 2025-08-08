<?php
$message = "";
require_once __DIR__ . '/../Controllers/AuthController.php';
$auth = new AuthController();

// Variables pour pré-remplir le formulaire
$matricule_prefill = htmlspecialchars($_GET['matricule'] ?? '');
$nom_eleve_prefill = htmlspecialchars($_GET['nom_eleve'] ?? '');
$postnom_eleve_prefill = htmlspecialchars($_GET['postnom_eleve'] ?? '');
$prenom_eleve_prefill = htmlspecialchars($_GET['prenom_eleve'] ?? '');
$sexe_eleve_prefill = htmlspecialchars($_GET['sexe_eleve'] ?? '');
$classe_eleve_prefill = htmlspecialchars($_GET['classe_eleve'] ?? '');
$nom_parent_prefill = htmlspecialchars($_GET['nom_parent'] ?? '');
$adresse_eleve_prefill = htmlspecialchars($_GET['adresse_eleve'] ?? '');
$montant_du_prefill = htmlspecialchars($_GET['montant_du'] ?? ''); // Montant restant à payer
$motif_paiement_prefill = htmlspecialchars($_GET['motif_paiement'] ?? '');



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

if ($_SERVER["REQUEST_METHOD"] == "GET" && isset($_GET['matricule_info_ajax'])) { // Renommez le paramètre pour éviter la confusion
    header('Content-Type: application/json');
    echo json_encode($auth->getStudentInfoByMatricule($_GET['matricule_info_ajax']));
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
                                            <h5 class="text-primary text-center">Paiement en ligne</h5>
                                        </div>
                                        <?php echo $message; ?>
                                        <div id="form-error-message" class="text-danger mb-3 fw-semibold"></div>
                                        <div class="mt-4">
                                            <form class="needs-validation" novalidate method="POST">
                                                <input type="hidden" name="local_payment" value="1">
                                                <input type="hidden" name="payment_validated" id="payment_validated"
                                                    value="0">
                                                <input type="hidden" name="transaction_id" id="transaction_id" value="">

                                                <label for="matricule" class="form-label">Matricule <span
                                                        class="text-danger">*</span></label>
                                                <input type="text" class="form-control" id="matricule" name="matricule"
                                                    placeholder="Entrez le matricule" required
                                                    value="<?php echo $matricule_prefill; ?>" readonly>
                                                <div class="invalid-feedback">
                                                    Veuillez entrer le matricule de l'élève.
                                                </div>
                                                <div class="row">
                                                    <div class="col-md-4 mb-3">
                                                        <label for="nom_eleve" class="form-label">Nom <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="nom_eleve"
                                                            name="nom_eleve" placeholder="Nom" required
                                                            value="<?php echo $nom_eleve_prefill; ?>" readonly>
                                                        <div class="invalid-feedback">
                                                            Veuillez entrer le nom de l'élève.
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="postnom_eleve" class="form-label">Postnom <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="postnom_eleve"
                                                            name="postnom_eleve" placeholder="Postnom" required
                                                            value="<?php echo $postnom_eleve_prefill; ?>" readonly>
                                                        <div class="invalid-feedback">
                                                            Veuillez entrer le postnom de l'élève.
                                                        </div>
                                                    </div>

                                                    <div class="col-md-4 mb-3">
                                                        <label for="prenom_eleve" class="form-label">Prénom <span
                                                                class="text-danger">*</span></label>
                                                        <input type="text" class="form-control" id="prenom_eleve"
                                                            name="prenom_eleve" placeholder="Prénom" required
                                                            value="<?php echo $prenom_eleve_prefill; ?>" readonly>
                                                        <div class="invalid-feedback">
                                                            Veuillez entrer le prénom de l'élève.
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="sexe_eleve" class="form-label">Sexe <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="sexe_eleve" name="sexe_eleve"
                                                        required disabled>
                                                        <option value="">Sélectionner le sexe</option>
                                                        <option value="M" <?php echo ($sexe_eleve_prefill === 'M') ? 'selected' : ''; ?>>Masculin</option>
                                                        <option value="F" <?php echo ($sexe_eleve_prefill === 'F') ? 'selected' : ''; ?>>Féminin</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Veuillez sélectionner le sexe de l'élève.
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="classe_eleve" class="form-label">Classe <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="classe_eleve"
                                                        name="classe_eleve" placeholder="Classe" required
                                                        value="<?php echo $classe_eleve_prefill; ?>" readonly>
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer la classe de l'élève.
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="nom_parent" class="form-label">Nom du Parent <span
                                                            class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="nom_parent"
                                                        name="nom_parent" placeholder="Nom du parent" required
                                                        value="<?php echo $nom_parent_prefill; ?>" readonly>
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer le nom du parent.
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="adresse_eleve" class="form-label">Adresse de
                                                        l'élève</label>
                                                    <input type="text" class="form-control" id="adresse_eleve"
                                                        name="adresse_eleve" placeholder="Adresse"
                                                        value="<?php echo $adresse_eleve_prefill; ?>" readonly>
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer l'adresse de l'eleve.
                                                    </div>
                                                </div>


                                                <div class="mb-3">
                                                    <label for="montant_payer" class="form-label">Montant à payer <span
                                                            class="text-danger">*</span></label>
                                                    <input type="number" class="form-control" id="montant_payer"
                                                        name="montant_payer" placeholder="Entrez le montant" required
                                                        value="<?php echo ($montant_du_prefill > 0) ? $montant_du_prefill : ''; ?>">
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer le montant à payer.
                                                    </div>
                                                </div>

                                                <div class="mb-3">
                                                    <label for="devise" class="form-label">Devise <span
                                                            class="text-danger">*</span></label>
                                                    <select class="form-select" id="devise" name="devise" required>
                                                        <option value="">Sélectionnez la devise</option>
                                                        <option value="FC" selected>FC</option>
                                                        <option value="$">$</option>
                                                    </select>
                                                    <div class="invalid-feedback">
                                                        Veuillez sélectionner la devise.
                                                    </div>
                                                </div>
                                                <div class="mb-3">
                                                    <label for="motif_paiement" class="form-label">Motif du
                                                        paiement <span class="text-danger">*</span></label>
                                                    <input type="text" class="form-control" id="motif_paiement"
                                                        name="motif_paiement" placeholder="Ex: Frais Scolaire" required
                                                        value="<?php echo htmlspecialchars($motif_paiement_prefill ?? ''); ?>"
                                                        readonly>
                                                    <div class="invalid-feedback">
                                                        Veuillez entrer le motif du paiement.
                                                    </div>
                                                </div>
                                                <input type="hidden" name="total_annuel" id="total_annuel"
                                                    value="<?php echo htmlspecialchars($_GET['total_annuel'] ?? ''); ?>">


                                                <div class="mt-4">
                                                    <button class="btn btn-success w-100" type="button"
                                                        onclick="checkout()">Confirmer le Paiement</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../libs/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../libs/simplebar/simplebar.min.js"></script>
    <script src="../libs/node-waves/waves.min.js"></script>
    <script src="../libs/feather-icons/feather.min.js"></script>
    <script src="../js/pages/plugins/lord-icon-2x.js"></script>
    <script src="../js/plugins.js"></script>

    <script src="../libs/particles.js/particles.js"></script>
    <script src="../js/pages/particles.app.js"></script>
    <script src="../js/pages/password-addon.init.js"></script>

    <script>
        // Fonction pour pré-remplir les champs si des paramètres GET sont présents
        document.addEventListener('DOMContentLoaded', function () {
            const urlParams = new URLSearchParams(window.location.search);

            if (urlParams.has('matricule')) {
                document.getElementById('matricule').value = urlParams.get('matricule');
                document.getElementById('nom_eleve').value = urlParams.get('nom_eleve');
                document.getElementById('postnom_eleve').value = urlParams.get('postnom_eleve');
                document.getElementById('prenom_eleve').value = urlParams.get('prenom_eleve');
                // Gérer le select pour le sexe
                const sexeSelect = document.getElementById('sexe_eleve');
                const sexeValue = urlParams.get('sexe_eleve');
                if (sexeValue) {
                    sexeSelect.value = sexeValue;
                }
                document.getElementById('classe_eleve').value = urlParams.get('classe_eleve');
                document.getElementById('nom_parent').value = urlParams.get('nom_parent');
                document.getElementById('adresse_eleve').value = urlParams.get('adresse_eleve') || ''; // L'adresse n'est pas toujours passée

                const montantDu = urlParams.get('montant_du');
                if (montantDu) {
                    document.getElementById('montant_payer').value = parseFloat(montantDu).toFixed(2);
                }
                // Si vous avez besoin de total_annuel ici pour CinetPay, assurez-vous de le passer aussi via l'URL
                document.getElementById('total_annuel').value = urlParams.get('total_annuel') || '';

                // Désactiver la recherche par matricule si les infos sont déjà là
                document.getElementById('matricule').removeEventListener('blur', chercherEleveParMatricule);
            }
        });

        // Gardez la fonction chercherEleveParMatricule pour les cas où le formulaire n'est pas pré-rempli
        async function chercherEleveParMatricule(matricule) {
            if (matricule.length > 0) {
                try {
                    // Utilisez un paramètre différent pour la requête AJAX afin de ne pas interférer avec le pré-remplissage initial
                    const response = await fetch(`PaiementParent.php?matricule_info_ajax=${encodeURIComponent(matricule)}`);
                    const data = await response.json();

                    if (data.success) {
                        document.getElementById("nom_eleve").value = data.data.nom_eleve;
                        document.getElementById("postnom_eleve").value = data.data.postnom_eleve;
                        document.getElementById("prenom_eleve").value = data.data.prenom_eleve;
                        document.getElementById("sexe_eleve").value = data.data.sexe_eleve;
                        document.getElementById("classe_eleve").value = data.data.classe_selection;
                        document.querySelector('input[name="nom_parent"]').value = data.data.nom_parent; // Assurez-vous d'avoir 'nom_parent' dans les données
                        document.querySelector('input[name="adresse_eleve"]').value = data.data.adresse_eleve; // Assurez-vous d'avoir 'adresse_eleve'
                        // Si vous voulez pré-remplir le montant_du basé sur l'historique de l'élève ici,
                        // il faudrait une autre requête ou une logique plus complexe pour résumer ses paiements.
                        // Pour l'instant, on se contente des infos de l'élève.
                        document.getElementById("form-error-message").innerHTML = "";
                    } else {
                        document.getElementById("form-error-message").innerHTML = "Matricule non trouvé ou erreur: " + data.message;
                        // Effacer les champs si le matricule n'est pas trouvé
                        document.getElementById("nom_eleve").value = "";
                        document.getElementById("postnom_eleve").value = "";
                        document.getElementById("prenom_eleve").value = "";
                        document.getElementById("sexe_eleve").value = "";
                        document.getElementById("classe_eleve").value = "";
                        document.querySelector('input[name="nom_parent"]').value = "";
                        document.querySelector('input[name="adresse_eleve"]').value = "";
                        document.getElementById("montant_payer").value = "";
                    }
                } catch (error) {
                    console.error('Erreur de recherche du matricule:', error);
                    document.getElementById("form-error-message").innerHTML = "Erreur de connexion lors de la recherche du matricule.";
                }
            }
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