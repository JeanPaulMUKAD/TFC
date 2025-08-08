<?php

// Assurez-vous que le chemin vers AuthController est correct
require_once '../Controllers/AuthController.php';

$auth = new AuthController();
$messageErreur = null;

// Démarrez la session si elle n'est pas déjà démarrée
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

$loggedInParentName = $_SESSION['username'] ?? '';


// --- NOUVEAU BLOC POUR LA GÉNÉRATION DE LA FACTURE ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'generate_invoice') {
    ob_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Inclure la bibliothèque FPDF ici
    require_once '../../fpdf.php';

    $data = $_POST;
    if (
        !isset($data['paiement_id']) ||
        !isset($data['matricule']) ||
        !isset($data['nom_eleve']) ||
        !isset($data['classe_eleve']) ||
        !isset($data['montant_paye']) ||
        !isset($data['motif_paiement']) ||
        !isset($data['date_paiement'])
    ) {
        http_response_code(400);
        exit('Erreur : Données de facture incomplètes.');
    }

    $paiement_id = $data['paiement_id'];
    $matricule = $data['matricule'];
    $nom_eleve = $data['nom_eleve'];
    $postnom_eleve = $data['postnom_eleve'] ?? '';
    $prenom_eleve = $data['prenom_eleve'] ?? '';
    $classe_eleve = $data['classe_eleve'];
    $montant_paye = $data['montant_paye'];
    $motif_paiement = $data['motif_paiement'];
    $date_paiement = $data['date_paiement'];

    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, mb_convert_encoding('Facture de paiement', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, mb_convert_encoding('ID Paiement:', 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding($paiement_id, 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding('Matricule:', 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding($matricule, 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding("Nom de l'élève:", 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding("{$nom_eleve} {$postnom_eleve} {$prenom_eleve}", 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding('Classe:', 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding($classe_eleve, 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding('Motif:', 'ISO-859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding($motif_paiement, 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding('Montant payé:', 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding($montant_paye, 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding('Date de paiement:', 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding($date_paiement, 'ISO-8859-1', 'UTF-8'), 0, 1);

    ob_end_clean();
    header('Content-Type: application/pdf');
    $pdf->Output('I', "Facture_{$matricule}_{$paiement_id}.pdf");
    exit;
}
// --- Bloc UNIQUE pour toutes les autres requêtes POST (y compris AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $resultat = ['success' => false, 'message' => 'Action non traitée.'];

    $action = $_POST['action'] ?? '';

    error_log("Requête POST reçue dans Acceuil_Parent.php. Action: " . $action);
    error_log("POST data: " . print_r($_POST, true));

    if ($action === 'get_paiements') {
        if (isset($_POST['matricule'])) {
            $matricule = htmlspecialchars(trim($_POST['matricule']));
            $resultat = $auth->obtenirPaiementsParMatricule($matricule);
            error_log("Résultat de obtenirPaiementsParMatricule: " . json_encode($resultat));
        } else {
            $resultat = ['success' => false, 'message' => 'Matricule manquant pour get_paiements.'];
            error_log("Erreur: Matricule manquant.");
        }
    } elseif ($action === 'get_paiements_by_parent') {
        if (isset($_POST['parent_name'])) {
            $parentNameFromAjax = htmlspecialchars(trim($_POST['parent_name']));

            error_log("parentNameFromAjax (via POST): " . $parentNameFromAjax);
            error_log("loggedInParentName (via session): " . $loggedInParentName);

            if ($parentNameFromAjax !== $loggedInParentName || empty($loggedInParentName)) {
                $resultat = ['success' => false, 'message' => 'Accès non autorisé ou parent non identifié.'];
                error_log("Accès non autorisé ou non-concordance des noms de parent: " . json_encode($resultat));
            } else {
                $resultat = $auth->obtenirPaiementsParNomParent($parentNameFromAjax);
                error_log("Résultat final de obtenirPaiementsParNomParent: " . json_encode($resultat));
            }
        } elseif ($action === 'get_enfants_et_statut_paiement') {
            if ($loggedInParentId !== null) {
                $resultat = $auth->obtenirEnfantsEtStatutPaiementParIdParent($loggedInParentId);
                error_log("Résultat de obtenirEnfantsEtStatutPaiementParIdParent: " . json_encode($resultat));
            } else {
                $resultat = ['success' => false, 'message' => 'ID du parent non disponible en session. Veuillez vous reconnecter.'];
                error_log("Erreur: ID parent non disponible en session.");
            }
        } else {
            $resultat = ['success' => false, 'message' => 'Clé "parent_name" manquante dans la requête POST.'];
            error_log("Erreur: Clé 'parent_name' manquante.");

        }
    } else {
        $resultat = ['success' => false, 'message' => 'Action POST non reconnue.'];
        error_log("Action POST non reconnue: " . json_encode($resultat));
    }

    echo json_encode($resultat);
    exit;
}

?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Parent | C.S.P.P.UNILU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="/assets/images/logo_pp.png">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Styles supplémentaires pour la table */
        .table-auto {
            width: 100%;
            border-collapse: collapse;
        }

        .table-auto th,
        .table-auto td {
            border: 1px solid #e2e8f0;
            padding: 12px;
        }

        .table-auto th {
            background-color: #f8fafc;
            font-weight: 600;
            color: #4a5568;
            text-transform: uppercase;
            font-size: 0.75rem;
        }

        .table-auto td {
            font-size: 0.875rem;
            color: #4a5568;
        }

        .min-w-full {
            min-width: 100%;
        }

        .divide-y>*+* {
            border-top-width: 1px;
        }

        .divide-gray-200>*+* {
            border-color: #edf2f7;
        }

        .bg-gray-50 {
            background-color: #f9fafb;
        }

        .hover\:bg-gray-50:hover {
            background-color: #f9fafb;
        }

        .text-xs {
            font-size: 0.75rem;
        }

        .text-sm {
            font-size: 0.875rem;
        }

        .text-red-500 {
            color: #ef4444;
        }

        .text-green-500 {
            color: #22c55e;
        }

        .text-yellow-600 {
            color: #d97706;
        }

        .bg-blue-600 {
            background-color: #2563eb;
        }

        .hover\:bg-blue-700:hover {
            background-color: #1d4ed8;
        }

        .rounded-md {
            border-radius: 0.375rem;
        }

        .px-3 {
            padding-left: 0.75rem;
            padding-right: 0.75rem;
        }

        .py-1 {
            padding-top: 0.25rem;
            padding-bottom: 0.25rem;
        }
    </style>


</head>

<body class="bg-gray-50 text-black">

    <!-- Navbar -->
    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white shadow-md">
        <div class="text-2xl font-bold">
            <span class="text-black">C.S.P.P</span><span class="text-indigo-500 font-extrabold">.UNILU</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="mailto:administrationcsppunilu@gmail.com" class="text-sm font-medium">Aide</a>
            <a href="/logoutParent"
                class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-indigo-600 hover:to-blue-500 text-white font-semibold py-2 px-4 rounded-full text-sm">
                Se déconnecter
            </a>
        </div>
    </nav>

    <!-- Section Parent -->
    <section class="relative px-4 py-10 bg-white pt-28">
        <div
            class="relative mx-auto max-w-14xl rounded-3xl overflow-hidden bg-black text-white min-h-[100vh] flex items-center justify-center shadow-2xl">

            <!-- Dégradés -->
            <div class="absolute top-0 left-0 w-64 h-64 bg-indigo-800 rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute top-0 right-0 w-64 h-64 bg-blue-700 rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 w-64 h-64 bg-blue-700 rounded-full opacity-20 blur-3xl"></div>
            <div class="absolute bottom-0 right-0 w-64 h-64 bg-indigo-800 rounded-full opacity-20 blur-3xl"></div>

            <!-- Contenu -->
            <div class="relative z-10 max-w-4xl w-full text-center space-y-6 p-8">
                <div class="inline-block text-xs px-4 py-1 border border-white/20 rounded-full bg-white/10 text-white">
                    <span class="text-green-500">● </span>ESPACE DU PARENT
                </div>
                <h1 class="text-4xl">Bonjour <?php echo $_SESSION['username'] ?></h1>

                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
                    Bienvenue dans votre espace<br>
                    <span
                        class="inline-block bg-gradient-to-r from-blue-600 to-indigo-500 px-2 py-1 rounded-md text-white">Parent</span>
                </h1>

                <p class="text-lg text-gray-300 mt-2">
                    Depuis cet espace, vous pouvez effectuer les paiements en ligne <br> et suivre en temps réel
                    l’historique des frais de votre enfant.
                </p>

                <div class="mt-8 flex flex-col md:flex-row justify-center items-center gap-4">

                    <a href="mailto:administrationcsppunilu@gmail.com"
                        class="bg-white text-black hover:bg-gray-200 transition font-semibold py-3 px-6 rounded-full flex items-center gap-2 text-sm">
                        <i class="fas fa-file-alt text-blue-600"></i>
                        <span>Contacter la direction</span>
                    </a>
                </div>

                <!-- Infos -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm text-gray-300">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-check-circle text-indigo-400"></i>
                        <span>Paiement sécurisé</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-clock text-indigo-400"></i>
                        <span>Suivi en temps réel</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-history text-indigo-400"></i>
                        <span>Historique des transactions</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Section explicative -->
    <section class="bg-gray-100 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Historique des Paiements de Votre Enfant
            </h2>

            <div class="bg-white rounded-xl shadow-lg p-6 overflow-hidden">
                <div class="table-responsive overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Matricule</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom Enfant</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Post-Nom</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Prénom</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sexe</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Classe</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Adresse</th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant Payé</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Motif</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date Paiement</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action</th>

                            </tr>
                        </thead>
                        <tbody id="paiementsTableBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Chargement de l'historique des paiements...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </section>

    <section class="bg-gray-100 py-16 px-6">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-3xl font-extrabold text-gray-900 mb-8 text-center">Statut de Paiement de Vos Enfants
            </h2>

            <div class="bg-white rounded-xl shadow-lg p-6 overflow-hidden">
                <div class="table-responsive overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Matricule</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom Complet Enfant</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sexe</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Classe</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total Annuel Dû</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Montant Total Payé</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Solde Dû</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action</th>

                            </tr>
                        </thead>
                        <tbody id="enfantsTableBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="9" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Chargement des informations des enfants...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </section>

    <footer class="bg-gray-800 text-white py-6 text-center">
        <p class="text-sm">
            &copy;
            <script>document.write(new Date().getFullYear())</script> C.S.P.P.UNILU. Tous droits réservés.
        </p>
    </footer>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const paiementsTableBody = document.getElementById('paiementsTableBody');
            const loggedInParentName = "<?php echo htmlspecialchars($loggedInParentName); ?>";

            async function fetchPaiementsForParent(parentName) {
                if (!parentName) {
                    paiementsTableBody.innerHTML = `<tr><td colspan="12" class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-center">Le nom du parent n'est pas disponible. Veuillez vous assurer d'être connecté.</td></tr>`;
                    return;
                }

                paiementsTableBody.innerHTML = `<tr><td colspan="12" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Chargement de l'historique des paiements pour ${htmlspecialchars(parentName)}...</td></tr>`;

                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: `action=get_paiements_by_parent&parent_name=${encodeURIComponent(parentName)}`
                    });

                    const data = await response.json();

                    if (data.success && data.paiements && data.paiements.length > 0) {
                        let rows = '';
                        data.paiements.forEach(paiement => {
                            const montantPayeNumeric = parseFloat(String(paiement.montant_payer).replace(/[^0-9.,]/g, '').replace(',', '.'));
                            const totalAnnuelNumeric = parseFloat(String(paiement.total_annuel).replace(/[^0-9.,]/g, '').replace(',', '.')) || 0;
                            const montantRestant = totalAnnuelNumeric - montantPayeNumeric;

                            let statusClass = '';
                            let statusText = '';
                            let paymentButton = '';
                            let factureButton = '';

                            if (parseFloat(String(paiement.montant_payer).replace(/[^0-9.,]/g, '').replace(',', '.')) > 0) {
                                factureButton = `<button class="download-facture-btn ml-2 px-3 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700"
                                data-paiement-id="${paiement.id_paiement}"
                                data-matricule="${htmlspecialchars(paiement.matricule)}"
                                data-nom-eleve="${htmlspecialchars(paiement.nom_eleve)}"
                                data-postnom-eleve="${htmlspecialchars(paiement.postnom_eleve)}"
                                data-prenom-eleve="${htmlspecialchars(paiement.prenom_eleve)}"
                                data-classe-eleve="${htmlspecialchars(paiement.classe_eleve)}"
                                data-montant-paye="${htmlspecialchars(paiement.montant_payer)}"
                                data-motif-paiement="${htmlspecialchars(paiement.motif_paiement)}"
                                data-date-paiement="${htmlspecialchars(paiement.date_paiement)}"
                                >
                                <i class="fas fa-file-invoice"></i> Télécharger
                            </button>`;
                            }

                            if (montantRestant <= 0) {
                                statusClass = 'bg-green-100 text-green-800';
                                statusText = 'Payé';
                            } else {
                                statusClass = 'bg-red-100 text-red-800';
                                statusText = `Reste : ${montantRestant.toLocaleString('fr-FR')}`;

                                const paymentUrl = `../Parent/PaiementParent.php?` +
                                    `matricule=${encodeURIComponent(paiement.matricule || '')}&` +
                                    `nom_eleve=${encodeURIComponent(paiement.nom_eleve || '')}&` +
                                    `postnom_eleve=${encodeURIComponent(paiement.postnom_eleve || '')}&` +
                                    `prenom_eleve=${encodeURIComponent(paiement.prenom_eleve || '')}&` +
                                    `sexe_eleve=${encodeURIComponent(paiement.sexe_eleve || '')}&` +
                                    `classe_eleve=${encodeURIComponent(paiement.classe_eleve || '')}&` +
                                    `adresse_eleve=${encodeURIComponent(paiement.adresse_eleve || '')}&` +
                                    `motif_paiement=${encodeURIComponent(paiement.motif_paiement || '')}&` +
                                    `nom_parent=${encodeURIComponent(loggedInParentName || '')}&` +
                                    `montant_du=${encodeURIComponent(montantRestant.toFixed(2))}`;

                                paymentButton = `<a href="${paymentUrl}" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">Payer le solde</a>`;
                            }

                            rows += `
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${htmlspecialchars(paiement.matricule)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${htmlspecialchars(paiement.nom_eleve)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.postnom_eleve)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.prenom_eleve)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.sexe_eleve || 'N/A')}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.classe_eleve)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.adresse_eleve)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.montant_payer)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.motif_paiement)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.date_paiement)}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                        ${statusText}
                                    </span>
                                    ${paymentButton}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                    ${factureButton}
                                </td>
                            </tr>
                        `;
                        });
                        paiementsTableBody.innerHTML = rows;

                        // Ajoutez les gestionnaires d'événements pour les boutons de téléchargement
                        const downloadButtons = document.querySelectorAll('.download-facture-btn');
                        downloadButtons.forEach(button => {
                            button.addEventListener('click', function (event) {
                                event.preventDefault(); // Empêche l'action par défaut
                                const invoiceData = {
                                    paiement_id: this.dataset.paiementId,
                                    matricule: this.dataset.matricule,
                                    nom_eleve: this.dataset.nomEleve,
                                    postnom_eleve: this.dataset.postnomEleve,
                                    prenom_eleve: this.dataset.prenomEleve,
                                    classe_eleve: this.dataset.classeEleve,
                                    montant_paye: this.dataset.montantPaye,
                                    motif_paiement: this.dataset.motifPaiement,
                                    date_paiement: this.dataset.datePaiement
                                };
                                generateInvoice(invoiceData);
                            });
                        });

                    } else {
                        paiementsTableBody.innerHTML = `<tr><td colspan="12" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">${data.message || "Aucun historique de paiement trouvé pour les enfants de ce parent."}</td></tr>`;
                    }
                } catch (error) {
                    console.error('Erreur lors de la récupération des paiements:', error);
                    paiementsTableBody.innerHTML = `<tr><td colspan="12" class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-center">Une erreur est survenue lors du chargement de l'historique des paiements.</td></tr>`;
                }
            }

            // Fonction pour générer la facture (doit être définie)
            async function generateInvoice(invoiceData) {
                try {
                    // Remplacez 'generate_invoice.php' par le chemin réel vers votre script PHP
                    const response = await fetch('/generate_invoice.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(invoiceData)
                    });

                    if (response.ok) {
                        const blob = await response.blob();
                        const url = window.URL.createObjectURL(blob);
                        const a = document.createElement('a');
                        a.style.display = 'none';
                        a.href = url;
                        a.download = `Facture_${invoiceData.matricule}_${invoiceData.paiement_id}.pdf`;
                        document.body.appendChild(a);
                        a.click();
                        window.URL.revokeObjectURL(url);
                    } else {
                        alert('Erreur lors de la génération de la facture.');
                    }
                } catch (error) {
                    console.error('Erreur:', error);
                    alert('Une erreur est survenue lors de la génération de la facture.');
                }
            }

            if (loggedInParentName) {
                fetchPaiementsForParent(loggedInParentName);
            } else {
                paiementsTableBody.innerHTML = `<tr><td colspan="12" class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-center">Le nom du parent n'est pas disponible. Veuillez vous assurer d'être connecté.</td></tr>`;
            }

            function htmlspecialchars(str) {
                if (typeof str !== 'string') return str;
                var map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return str.replace(/[&<>"']/g, function (m) { return map[m]; });
            }
        });
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
        cercle.style.border = '3px solid #2563eb';
        cercle.style.borderTop = '3px solid #000000';
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

</html>