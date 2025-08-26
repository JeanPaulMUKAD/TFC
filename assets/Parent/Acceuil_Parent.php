<?php

require_once '../Controllers/AuthController.php';

$auth = new AuthController();

$messageErreur = null;

// Récupération des informations du parent connecté

$loggedInParentName = $_SESSION['username'] ?? '';
$loggedInParentId = $_SESSION['parent_id'] ?? null;


/**
 * Fonction pour lire les données d'une requête POST, qu'elle soit encodée
 * en application/json ou x-www-form-urlencoded.
 * @return array
 */
function getRequestData()
{
    // Vérifie si le type de contenu de la requête est JSON
    if (isset($_SERVER["CONTENT_TYPE"]) && strpos($_SERVER["CONTENT_TYPE"], 'application/json') !== false) {
        $input = file_get_contents('php://input');
        // Décode le JSON et retourne un tableau associatif
        return json_decode($input, true) ?? [];
    }
    // Sinon, retourne le tableau $_POST standard
    return $_POST;
}

// --- BLOC DE GÉNÉRATION DE FACTURE (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'generate_invoice') {
    ob_start();
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    // Assurez-vous que le chemin vers 
    // php est correct
    require_once __DIR__ . '/../../fpdf.php';

    $data = $_POST;
    $requiredFields = ['paiement_id', 'matricule', 'nom_eleve', 'classe_eleve', 'montant_paye', 'motif_paiement', 'date_paiement'];

    foreach ($requiredFields as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            exit('Erreur : Données de facture incomplètes.');
        }
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

    // Instanciation de FPDF et génération du PDF
    $pdf = new FPDF();
    $pdf->AddPage();
    $pdf->SetFont('Arial', 'B', 16);
    $pdf->Cell(0, 10, mb_convert_encoding('Facture de paiement', 'ISO-8859-1', 'UTF-8'), 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('Arial', '', 12);
    $pdf->Cell(50, 10, 'ID Paiement:', 0, 0);
    $pdf->Cell(0, 10, $paiement_id, 0, 1);
    $pdf->Cell(50, 10, 'Matricule:', 0, 0);
    $pdf->Cell(0, 10, $matricule, 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding("Nom de l'élève:", 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding("{$nom_eleve} {$postnom_eleve} {$prenom_eleve}", 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, 'Classe:', 0, 0);
    $pdf->Cell(0, 10, $classe_eleve, 0, 1);
    $pdf->Cell(50, 10, 'Motif:', 0, 0);
    $pdf->Cell(0, 10, mb_convert_encoding($motif_paiement, 'ISO-8859-1', 'UTF-8'), 0, 1);
    $pdf->Cell(50, 10, mb_convert_encoding('Montant payé:', 'ISO-8859-1', 'UTF-8'), 0, 0);
    $pdf->Cell(0, 10, $montant_paye, 0, 1);
    $pdf->Cell(50, 10, 'Date de paiement:', 0, 0);
    $pdf->Cell(0, 10, $date_paiement, 0, 1);

    ob_end_clean();
    header('Content-Type: application/pdf');
    $pdf->Output('I', "Facture_{$matricule}_{$paiement_id}.pdf");
    exit;
}

// --- BLOC AJAX / POST GÉNÉRAL (API) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');

    // Utilisation de la nouvelle fonction pour récupérer les données de la requête
    $requestData = getRequestData();
    $action = $requestData['action'] ?? '';

    $resultat = ['success' => false, 'message' => 'Action POST non reconnue.'];

    switch ($action) {
        case 'get_paiements':
            $matricule = $requestData['matricule'] ?? '';
            if ($matricule) {
                $resultat = $auth->obtenirPaiementsParMatricule($matricule);
            } else {
                $resultat['message'] = 'Matricule manquant pour get_paiements.';
            }
            break;

        case 'get_paiements_by_parent':
            if ($loggedInParentId) { // ID parent en session
                $stmt = $auth->conn->prepare("
            SELECT 
                p.id AS id_paiement,
                i.matricule,
                p.montant_payer,
                p.motif_paiement,
                p.date_paiement,
                i.nom_eleve,
                i.postnom_eleve,
                i.prenom_eleve,
                i.sexe_eleve,
                i.classe_selection AS classe_eleve,
                i.adresse_eleve,
                p.total_annuel,
                p.transaction_id AS numero_transaction
            FROM paiement p
            JOIN inscriptions i ON p.matricule = i.matricule
            WHERE i.parent_id = ?
        ");

                if ($stmt) {
                    $stmt->bind_param("i", $loggedInParentId);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result) {
                        $paiements = $result->fetch_all(MYSQLI_ASSOC);

                        if (!empty($paiements)) {
                            $paiementsRegroupes = [];

                            foreach ($paiements as $paiement) {
                                $key = $paiement['matricule'] . '_' . $paiement['motif_paiement'];

                                if (!isset($paiementsRegroupes[$key])) {
                                    $paiementsRegroupes[$key] = $paiement;
                                    $paiementsRegroupes[$key]['montant_payer'] = (float) $paiement['montant_payer'];
                                } else {
                                    $paiementsRegroupes[$key]['montant_payer'] += (float) $paiement['montant_payer'];
                                }
                            }

                            $resultat = [
                                'success' => true,
                                'paiements' => array_values($paiementsRegroupes)
                            ];
                        } else {
                            $resultat = ['success' => false, 'message' => 'Aucun historique de paiement trouvé pour les enfants de ce parent.'];
                        }
                    } else {
                        $resultat = ['success' => false, 'message' => 'Erreur lors de l\'exécution de la requête.'];
                    }
                } else {
                    $resultat = ['success' => false, 'message' => 'Erreur de préparation de la requête SQL.'];
                }
            } else {
                $resultat = ['success' => false, 'message' => 'ID du parent non disponible en session.'];
            }
            break;


        case 'get_enfants_et_statut_paiement':
            if ($loggedInParentId) {
                $resultat = $auth->obtenirEnfantsEtStatutPaiementParIdParent($loggedInParentId);
            } else {
                $resultat['message'] = 'ID du parent non disponible en session. Veuillez vous reconnecter.';
            }
            break;

        case 'get_enfants_by_parent':
            if ($loggedInParentId) {
                // Récupérer les enfants du parent
                $resultat = $auth->obtenirEnfantsParIdParent($loggedInParentId);

                if (!empty($resultat['enfants'])) {
                    foreach ($resultat['enfants'] as &$enfant) {
                        // Initialiser le tableau des types de paiement pour cet enfant
                        $enfant['typesPaiement'] = [];


                        // Récupérer les types de paiement correspondant à la classe de l'enfant
                        $stmt = $auth->conn->prepare("
                    SELECT id, nom_type, montant_classe, mois 
                    FROM payementtype 
                    WHERE TRIM(classe_type) = TRIM(?)
                ");
                        $stmt->bind_param("s", $enfant['classe_selection']);
                        $stmt->execute();
                        $res = $stmt->get_result();

                        while ($row = $res->fetch_assoc()) {
                            $montant_type = (float) $row['montant_classe'];
                            $est_annuel = (strtolower($row['mois']) === 'annuel');
                            $total_annuel_calcule = $est_annuel ? $montant_type : $montant_type * 10;

                            // Récupérer le montant déjà payé pour ce type de paiement et cet enfant via matricule
                            $stmt_paiement = $auth->conn->prepare("
                        SELECT SUM(montant_payer) as montant_paye
                        FROM paiement
                        WHERE matricule = ? AND motif_paiement = ?
                    ");
                            $stmt_paiement->bind_param("ss", $enfant['matricule'], $row['nom_type']); // matricule = string, nom_type = string
                            $stmt_paiement->execute();
                            $res_paiement = $stmt_paiement->get_result();
                            $row_paiement = $res_paiement->fetch_assoc();
                            $montant_paye = $row_paiement['montant_paye'] ? (float) $row_paiement['montant_paye'] : 0;

                            $enfant['typesPaiement'][] = [
                                'nom_type' => $row['nom_type'],
                                'montant_classe' => $montant_type,
                                'est_annuel' => $est_annuel,
                                'total_annuel_calcule' => $total_annuel_calcule,
                                'montant_paye' => $montant_paye
                            ];
                        }

                        $enfant['aucunPaiement'] = empty($enfant['typesPaiement']);
                    }
                    $resultat['success'] = true;
                } else {
                    $resultat['success'] = false;
                    $resultat['message'] = 'Aucun enfant trouvé pour ce parent.';
                }
            } else {
                $resultat['success'] = false;
                $resultat['message'] = 'ID parent non disponible en session.';
            }
            break;

        default:
            // Le message par défaut est déjà défini, pas besoin de le redéfinir ici
            break;
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        .table-responsive {
            overflow-x: auto;
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

        td.dette-rouge {
            color: red !important;
            font-weight: bold;
        }

        .payer-solde-btn {
            background-color: red;
            /* Vert */
            color: white;
            border: none;
            padding: 8px 12px;
            font-size: 14px;
            cursor: pointer;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }

        .payer-solde-btn:hover {
            background-color: #88212a;
            /* Vert foncé au survol */
        }
    </style>
</head>

<body class="bg-gray-100 text-black">

    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white shadow-md">
        <div class="text-2xl font-bold flex items-center">
            <img src="/assets/images/logo_pp2.png" alt="Logo" class="h-10 w-10 mr-2" />
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

    <div class="container mx-auto px-4 py-8 mt-20">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de bord du Parent</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <button id="viewPaymentsBtn"
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-full mr-4">
                        <i class="fas fa-history fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Historique des paiements</p>
                        <p class="text-xl font-semibold text-gray-900">Tout afficher</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>

            <button id="viewChildrenBtn"
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-indigo-100 text-indigo-600 p-3 rounded-full mr-4">
                        <i class="fas fa-child fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Liste des enfants</p>
                        <p class="text-xl font-semibold text-gray-900">Afficher tous</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>

            <button id="contactAdminBtn"
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-purple-100 text-purple-600 p-3 rounded-full mr-4">
                        <i class="fas fa-headset fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Assistance</p>
                        <p class="text-xl font-semibold text-gray-900">Contacter la direction</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>
        </div>

        <div id="dashboardContent" class="bg-white p-6 rounded-xl shadow-lg min-h-[50vh]">
            <div id="defaultMessage" class="text-center text-gray-500 py-10">
                <i class="fas fa-info-circle fa-2x mb-4 text-gray-400"></i>
                <p class="text-lg">Sélectionnez une option ci-dessus pour afficher les données.</p>
            </div>

            <div id="paymentsTableContainer" style="display: none;">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Historique des Paiements de Votre Enfant</h2>
                <div class="table-responsive">
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
                                    Total annuel</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Motif</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date Paiement</th>

                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Numéro de transaction
                                </th>


                            </tr>
                        </thead>
                        <tbody id="paiementsTableBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="12" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Chargement de l'historique des paiements...</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div id="childrenTableContainer" style="display: none;">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">Mes Enfants</h2>
                <div class="table-responsive">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Nom complet
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Sexe
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Classe
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Année d'Inscription
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Type de paiement
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Total annuel
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Payé
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Dette
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Avance
                                </th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Action de paiement
                                </th>
                            </tr>
                        </thead>
                        <tbody id="childrenTableBody" class="bg-white divide-y divide-gray-200">
                            <tr>
                                <td colspan="10" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">
                                    Chargement de la liste des enfants...
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>


        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const tableBody = document.getElementById("childrenTableBody");
            const tableContainer = document.getElementById("childrenTableContainer");

            fetch('Acceuil_Parent.php', {
                method: 'POST',
                credentials: 'same-origin',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({ action: 'get_enfants_by_parent' })
            })
                .then(response => response.json())
                .then(data => {
                    tableBody.innerHTML = ''; // Vider le message de chargement

                    if (data.success && data.enfants.length > 0) {
                        data.enfants.forEach(enfant => {
                            let selectOptions = '';
                            if (!enfant.aucunPaiement) {
                                enfant.typesPaiement.forEach(tp => {
                                    selectOptions += `
                                <option 
                                    value="${tp.nom_type}" 
                                    data-total-annuel-calcule="${tp.total_annuel_calcule}"
                                    data-montant-paye="${tp.montant_paye}">
                                    ${tp.nom_type}
                                </option>

                                    `;
                                });
                            } else {
                                selectOptions = `<option disabled selected>Aucun paiement configuré</option>`;
                            }



                            const tr = document.createElement('tr');
                            tr.innerHTML = `
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${enfant.nom_eleve} ${enfant.postnom_eleve} ${enfant.prenom_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${enfant.sexe_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${enfant.classe_selection}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${enfant.annee_inscription}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <select 
                                class="paymentTypeSelect px-2 py-1 border rounded" 
                                data-matricule="${enfant.matricule}"
                                data-nom-eleve="${enfant.nom_eleve}"
                                data-postnom-eleve="${enfant.postnom_eleve}"
                                data-prenom-eleve="${enfant.prenom_eleve}"
                                data-sexe-eleve="${enfant.sexe_eleve}"
                                data-classe-eleve="${enfant.classe_selection}"
                                data-nom-parent="${enfant.nom_parent}"
                                data-adresse-eleve="${enfant.adresse_eleve}">
                                <option value="" disabled selected>Sélectionner</option>
                                ${selectOptions}
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 total_annuel">0,00 FC</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 montant_paye">0,00 FC</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 dette">0,00 FC</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 avance">0,00 FC</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 payment-action"></td>
                    `;
                            tableBody.appendChild(tr);
                        });

                        // Ajouter l'événement 'change' pour chaque select
                        document.querySelectorAll('.paymentTypeSelect').forEach(select => {
                            select.addEventListener('change', function () {
                                const selectedOption = this.selectedOptions[0];

                                // Récupérer total annuel et montant déjà payé depuis data-*
                                const totalAnnuel = parseFloat(selectedOption.dataset.totalAnnuelCalcule) || 0;
                                const montantPaye = parseFloat(selectedOption.dataset.montantPaye) || 0;

                                const dette = Math.max(0, totalAnnuel - montantPaye);
                                const avance = Math.max(0, montantPaye - totalAnnuel);


                                const tr = this.closest('tr');
                                tr.querySelector('.total_annuel').textContent = totalAnnuel.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + " FC";
                                tr.querySelector('.montant_paye').textContent = montantPaye.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + " FC";

                                tr.querySelector('.dette').textContent = dette.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + " FC";
                                tr.querySelector('.avance').textContent = avance.toLocaleString('fr-FR', { minimumFractionDigits: 2 }) + " FC";


                                const paymentActionTd = tr.querySelector('.payment-action');

                                if (dette > 0) {
                                    tr.querySelector('.dette').classList.add('dette-rouge');
                                    paymentActionTd.innerHTML = `<button class="payer-solde-btn">Payer solde</button>`;
                                    paymentActionTd.querySelector('.payer-solde-btn').addEventListener('click', function () {
                                        const parentSelect = this.closest('tr').querySelector('.paymentTypeSelect');
                                        const selectedOption = parentSelect.selectedOptions[0];

                                        const params = new URLSearchParams({
                                            matricule: parentSelect.dataset.matricule,
                                            nom_eleve: parentSelect.dataset.nomEleve,
                                            postnom_eleve: parentSelect.dataset.postnomEleve,
                                            prenom_eleve: parentSelect.dataset.prenomEleve,
                                            sexe_eleve: parentSelect.dataset.sexeEleve,
                                            classe_eleve: parentSelect.dataset.classeEleve,
                                            nom_parent: parentSelect.dataset.nomParent,
                                            adresse_eleve: parentSelect.dataset.adresseEleve,
                                            montant_du: dette,
                                            total_annuel: totalAnnuel,
                                            motif_paiement: selectedOption.value
                                        });

                                        window.location.href = `PaiementParent.php?${params.toString()}`;
                                    });
                                } else {
                                    tr.querySelector('.dette').classList.remove('dette-rouge');
                                    paymentActionTd.innerHTML = '';
                                }
                            });
                        });

                    } else {
                        tableBody.innerHTML = `<tr><td colspan="10" class="px-6 py-4 text-center text-gray-500">${data.message || 'Aucun enfant trouvé.'}</td></tr>`;
                    }
                    tableContainer.style.display = 'block';
                })
                .catch(error => {
                    console.error('Erreur AJAX:', error);
                    tableBody.innerHTML = `<tr><td colspan="10" class="px-6 py-4 text-center text-red-500">Erreur lors du chargement des enfants.</td></tr>`;
                    tableContainer.style.display = 'block';
                });
        });
    </script>



    <script>
        // Fonction pour cacher tous les conteneurs et afficher le message par défaut
        function showDefaultMessage() {
            document.getElementById('defaultMessage').style.display = 'block';
            document.getElementById('paymentsTableContainer').style.display = 'none';
            document.getElementById('childrenTableContainer').style.display = 'none';
        }

        // Événement pour le bouton "Historique des paiements"
        document.getElementById('viewPaymentsBtn').addEventListener('click', function () {
            showDefaultMessage();
            document.getElementById('paymentsTableContainer').style.display = 'block';
            // Ici, vous devrez ajouter la logique pour charger les données de la table des paiements via une requête AJAX
        });

        // Événement pour le nouveau bouton "Liste des enfants"
        document.getElementById('viewChildrenBtn').addEventListener('click', function () {
            showDefaultMessage();
            document.getElementById('childrenTableContainer').style.display = 'block';
            // Ici, vous devrez ajouter la logique pour charger les données de la table des enfants via une requête AJAX
        });

        // Événement pour le bouton "Contacter la direction"
        document.getElementById('contactAdminBtn').addEventListener('click', function () {
            window.location.href = 'mailto:administrationcsppunilu@gmail.com';
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const paiementsTableBody = document.getElementById('paiementsTableBody');
            const loggedInParentName = "<?php echo htmlspecialchars($loggedInParentName); ?>";
            const loadingMessage = '<tr><td colspan="12" class="px-6 py-4 text-sm text-gray-500 text-center">Chargement en cours...</td></tr>';
            const errorMessage = '<tr><td colspan="12" class="px-6 py-4 text-sm text-red-500 text-center">Une erreur est survenue lors du chargement des paiements.</td></tr>';

            // Fonction pour formater les données et générer une ligne de tableau
            const createPaymentRow = (paiement) => {
                const montantPayeNumeric = parseFloat(String(paiement.montant_payer).replace(/[^0-9.,]/g, '').replace(',', '.')) || 0;
                const totalAnnuelNumeric = parseFloat(String(paiement.total_annuel).replace(/[^0-9.,]/g, '').replace(',', '.')) || 0;
                const montantRestant = totalAnnuelNumeric - montantPayeNumeric;

                const isPaid = montantRestant <= 0;
                const statusClass = isPaid ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                const statusText = isPaid ? 'Payé' : `Reste : ${montantRestant.toLocaleString('fr-FR')} FC`;

                const paymentUrl = `../Parent/PaiementParent.php?${new URLSearchParams({
                    matricule: paiement.matricule,
                    nom_eleve: paiement.nom_eleve,
                    postnom_eleve: paiement.postnom_eleve,
                    prenom_eleve: paiement.prenom_eleve,
                    sexe_eleve: paiement.sexe_eleve,
                    classe_eleve: paiement.classe_eleve,
                    adresse_eleve: paiement.adresse_eleve,
                    motif_paiement: paiement.motif_paiement,
                    nom_parent: loggedInParentName,
                    montant_du: montantRestant.toFixed(2)
                })}`;

                const paymentButton = isPaid ? '' : `<a href="${paymentUrl}" class="ml-2 px-3 py-1 bg-blue-600 text-white rounded-md text-xs hover:bg-blue-700">Payer le solde</a>`;
                const factureButton = montantPayeNumeric > 0 ? `<button class="download-facture-btn ml-2 px-3 py-1 bg-green-600 text-white rounded-md text-xs hover:bg-green-700" data-paiement-id="${paiement.id_paiement}" data-paiement-data='${JSON.stringify(paiement)}'><i class="fas fa-file-invoice"></i> Télécharger</button>` : '';

                return `
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm  text-gray-900">${escapeHtml(paiement.matricule)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">${escapeHtml(paiement.nom_eleve)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.postnom_eleve)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.prenom_eleve)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.sexe_eleve || 'N/A')}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.classe_eleve)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.adresse_eleve)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.montant_payer)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.total_annuel)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.motif_paiement)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.date_paiement)}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${escapeHtml(paiement.numero_transaction || '')}</td>
                   
                   
                </tr>
                `;

            };

            // Fonction principale pour récupérer et afficher les paiements
            const fetchAndRenderPaiements = async (parentName) => {
                if (!parentName) {
                    paiementsTableBody.innerHTML = '<tr><td colspan="12" class="px-6 py-4 text-sm text-red-500 text-center">Le nom du parent n\'est pas disponible.</td></tr>';
                    return;
                }
                paiementsTableBody.innerHTML = loadingMessage;
                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            action: 'get_paiements_by_parent',
                            Names_User: parentName
                        })
                    });
                    if (!response.ok) {
                        throw new Error(`Erreur HTTP: ${response.status}`);
                    }
                    const data = await response.json();
                    if (data.success && data.paiements && data.paiements.length > 0) {
                        const rowsHtml = data.paiements.map(createPaymentRow).join('');
                        paiementsTableBody.innerHTML = rowsHtml;
                        attachInvoiceListeners();
                    } else {
                        const message = data.message || "Aucun historique de paiement trouvé pour les enfants de ce parent.";
                        paiementsTableBody.innerHTML = `<tr><td colspan="12" class="px-6 py-4 text-sm text-gray-500 text-center">${message}</td></tr>`;
                    }
                } catch (error) {
                    console.error('Erreur lors de la récupération des paiements:', error);
                    paiementsTableBody.innerHTML = errorMessage;
                }
            };

            // --- LOGIQUE DE TÉLÉCHARGEMENT DE FACTURE ---
            const generateInvoice = async (paiement) => {
                try {
                    // Crée un formulaire temporaire pour envoyer les données au serveur
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = window.location.href;
                    form.target = '_blank'; // Ouvre la facture dans un nouvel onglet

                    // Ajoute l'action de génération de facture en tant que champ caché
                    const actionInput = document.createElement('input');
                    actionInput.type = 'hidden';
                    actionInput.name = 'action';
                    actionInput.value = 'generate_invoice';
                    form.appendChild(actionInput);

                    // Crée un objet avec les noms de champs corrects pour le script PHP
                    const invoiceDataToSend = {
                        paiement_id: paiement.id_paiement,
                        matricule: paiement.matricule,
                        nom_eleve: paiement.nom_eleve,
                        postnom_eleve: paiement.postnom_eleve || '',
                        prenom_eleve: paiement.prenom_eleve || '',
                        sexe_eleve: paiement.sexe_eleve || '',
                        classe_eleve: paiement.classe_eleve,
                        adresse_eleve: paiement.adresse_eleve || '',
                        montant_paye: paiement.montant_payer,
                        motif_paiement: paiement.motif_paiement,
                        date_paiement: paiement.date_paiement,
                        total_annuel: paiement.total_annuel
                    };

                    // Parcours l'objet de données et ajoute chaque champ au formulaire
                    for (const key in invoiceDataToSend) {
                        if (Object.prototype.hasOwnProperty.call(invoiceDataToSend, key)) {
                            const input = document.createElement('input');
                            input.type = 'hidden';
                            input.name = key;
                            input.value = invoiceDataToSend[key];
                            form.appendChild(input);
                        }
                    }

                    // Envoie le formulaire et le retire du DOM
                    document.body.appendChild(form);
                    form.submit();
                    document.body.removeChild(form);

                } catch (error) {
                    console.error('Erreur lors de la génération de la facture:', error);
                    alert('Impossible de générer la facture. Veuillez réessayer.');
                }
            };

            // Fonction pour attacher les gestionnaires d'événements
            const attachInvoiceListeners = () => {
                const downloadButtons = document.querySelectorAll('.download-facture-btn');
                downloadButtons.forEach(button => {
                    button.addEventListener('click', (event) => {
                        event.preventDefault();
                        try {
                            const invoiceData = JSON.parse(button.dataset.paiementData);
                            generateInvoice(invoiceData); // Appel de la nouvelle fonction
                        } catch (e) {
                            console.error('Erreur lors de l\'analyse des données de la facture:', e);
                            alert('Erreur: Données de facture corrompues.');
                        }
                    });
                });
            };

            // Fonction d'échappement HTML
            const escapeHtml = (unsafe) => {
                if (typeof unsafe !== 'string') return unsafe;
                return unsafe
                    .replace(/&/g, "&amp;")
                    .replace(/</g, "&lt;")
                    .replace(/>/g, "&gt;")
                    .replace(/"/g, "&quot;")
                    .replace(/'/g, "&#039;");
            };

            // Appel initial
            fetchAndRenderPaiements(loggedInParentName);
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