<?php

require_once '../Controllers/AuthController.php';
$auth = new AuthController();
$listeEleves = $auth->getToutesLesInscriptions();
$eleveData = null;
$messageErreur = null;

$current_user_id = $_SESSION['user_id'] ?? null;

// Préparer les options des parents pour le select
$parentsOptions = '';
$conn = new mysqli("localhost", "root", "", "school");
if (!$conn->connect_error) {
    $sql = "SELECT id, Names_User FROM utilisateurs WHERE Role_User = 'parent'";
    $result = $conn->query($sql);
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $parentsOptions .= '<option value="' . $row['id'] . '">' . htmlspecialchars($row['Names_User']) . '</option>';
        }
    }
    $conn->close();
}

// GESTION DE L'INSCRIPTION POST (classique ou AJAX)
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['inscription_eleve'])) {
    $nom_eleve = trim($_POST['nom_eleve'] ?? '');
    $postnom_eleve = trim($_POST['postnom_eleve'] ?? '');
    $prenom_eleve = trim($_POST['prenom_eleve'] ?? '');
    $sexe_eleve = $_POST['sexe_eleve'] ?? '';
    $classe_selection = $_POST['classe_selection'] ?? '';
    $parent_id = $_POST['nom_parent'] ?? ''; // Ce champ contient l'ID du parent, pas son nom
    $adresse_eleve = trim($_POST['adresse_eleve'] ?? '');
    $annee_inscription = trim($_POST['annee_inscription'] ?? '');

    if (
        empty($nom_eleve) ||
        empty($postnom_eleve) ||
        empty($prenom_eleve) ||
        empty($sexe_eleve) ||
        empty($classe_selection) ||
        empty($parent_id) ||      // Ici on vérifie l'ID du parent
        empty($adresse_eleve) ||
        empty($annee_inscription)
    ) {
        $result = ['success' => false, 'message' => "Tous les champs sont requis pour l'inscription."];
    } else {
        if ($current_user_id === null) {
            $result = ['success' => false, 'message' => "Erreur : L'ID de l'utilisateur parent n'est pas disponible. Veuillez vous connecter."];
        } else {
            // Connexion pour récupérer le nom du parent à partir de son ID
            $nom_parent = '';
            $conn = new mysqli("localhost", "root", "", "school");
            if (!$conn->connect_error) {
                $stmt = $conn->prepare("SELECT Names_User FROM utilisateurs WHERE id = ?");
                $stmt->bind_param("i", $parent_id);
                $stmt->execute();
                $stmt->bind_result($nom_parent_result);
                if ($stmt->fetch()) {
                    $nom_parent = $nom_parent_result;
                }
                $stmt->close();
                $conn->close();
            }

            if (empty($nom_parent)) {
                $result = ['success' => false, 'message' => "Le parent sélectionné est invalide."];
            } else {
                // Appel à la méthode pour enregistrer l'élève
                $result = $auth->enregistrerEleve(
                    $nom_eleve,
                    $postnom_eleve,
                    $prenom_eleve,
                    $sexe_eleve,
                    $classe_selection,
                    $nom_parent,    // On enregistre ici le nom du parent (pas l'ID)
                    $adresse_eleve,
                    $annee_inscription,
                    $parent_id      // Tu peux aussi stocker l'ID du parent en base si tu as ce champ parent_id
                );
            }
        }
    }

    // Si requête AJAX, répondre JSON et sortir
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($result);
        exit;
    } else {
        // Sinon message HTML classique à afficher dans la page
        $messageErreur = $result['success']
            ? "<p class='text-green-500 text-center'>" . htmlspecialchars($result['message']) . "</p>"
            : "<p class='text-red-500 text-center'>" . htmlspecialchars($result['message']) . "</p>";
    }
}

// --- GESTION DES REQUÊTES AJAX ---
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');

    $action = null;

    if (isset($_POST['action'])) {
        $action = $_POST['action'];
    } elseif (isset($_GET['action'])) {
        $action = $_GET['action'];
    }

    if ($action) {
        switch ($action) {
            case 'getStudents':
                $classe = isset($_GET['classe']) ? htmlspecialchars(trim($_GET['classe'])) : null;
                if ($classe) {
                    $resultat = $auth->obtenirElevesParClasse($classe);
                    if ($resultat['success']) {
                        echo json_encode(['success' => true, 'inscriptions' => $resultat['eleves']]);
                    } else {
                        echo json_encode(['success' => false, 'message' => $resultat['message']]);
                    }
                } else {
                    $inscriptions = $auth->getToutesLesInscriptions();
                    echo json_encode(['success' => true, 'inscriptions' => $inscriptions]);
                }
                exit;

            case 'getEleve':
                $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
                if ($id > 0) {
                    $eleve = $auth->obtenirInfosEleveParId($id);
                    if ($eleve) {
                        echo json_encode(['success' => true, 'eleve' => $eleve]);
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Élève non trouvé.']);
                    }
                } else {
                    echo json_encode(['success' => false, 'message' => 'ID d\'élève invalide.']);
                }
                exit;

            case 'modifierEleve':
                $data = [
                    'id' => isset($_POST['id']) ? (int) $_POST['id'] : 0,
                    'nom_eleve' => isset($_POST['nom_eleve']) ? htmlspecialchars(trim($_POST['nom_eleve'])) : '',
                    'postnom_eleve' => isset($_POST['postnom_eleve']) ? htmlspecialchars(trim($_POST['postnom_eleve'])) : '',
                    'prenom_eleve' => isset($_POST['prenom_eleve']) ? htmlspecialchars(trim($_POST['prenom_eleve'])) : '',
                    'sexe_eleve' => isset($_POST['sexe_eleve']) ? htmlspecialchars(trim($_POST['sexe_eleve'])) : '',
                    'classe_selection' => isset($_POST['classe_selection']) ? htmlspecialchars(trim($_POST['classe_selection'])) : '',
                    'nom_parent' => isset($_POST['nom_parent']) ? htmlspecialchars(trim($_POST['nom_parent'])) : '',
                    'adresse_eleve' => isset($_POST['adresse_eleve']) ? htmlspecialchars(trim($_POST['adresse_eleve'])) : '',
                    'annee_inscription' => isset($_POST['annee_inscription']) ? htmlspecialchars(trim($_POST['annee_inscription'])) : ''
                ];

                if ($data['id'] === 0) {
                    echo json_encode(['success' => false, 'message' => 'ID d\'élève invalide pour la modification.']);
                    exit;
                }

                $result = $auth->modifierEleves($data);
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Élève modifié avec succès.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la modification de l\'élève.']);
                }
                exit;

            case 'supprimerEleve':
                $id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
                if ($id === 0) {
                    echo json_encode(['success' => false, 'message' => 'ID d\'élève manquant pour la suppression.']);
                    exit;
                }
                $result = $auth->supprimerEleve($id);
                if ($result) {
                    echo json_encode(['success' => true, 'message' => 'Élève supprimé avec succès.']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression de l\'élève.']);
                }
                exit;

            default:
                echo json_encode(['success' => false, 'message' => 'Action non supportée.']);
                exit;
        }
    }
}
?>


<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secrétariat | C.S.P.P.UNILU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="/assets/images/logo_pp.png">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css" rel="stylesheet">
</head>

<body class="bg-gray-100 text-black">

    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white shadow-md">
        <div class="text-2xl font-bold flex items-center">
            <img src="/assets/images/logo_pp2.png" alt="Logo" class="h-10 w-10 mr-2" />
            <span class="text-black">C.S.P.P</span><span class="text-orange-500 font-extrabold">.UNILU</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="mailto:administrationcsppunilu@gmail.com" target="_blank" class="text-sm font-medium">Aide</a>
            <a href="/logoutsec"
                class="bg-gradient-to-r from-red-600 to-orange-500 hover:from-orange-600 hover:to-red-500 transition text-white font-semibold py-2 px-4 rounded-full text-sm">
                Se déconnecter
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 mt-20">
        <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de bord du Secrétaire</h1>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <button id="showInscriptionFormBtn"
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between w-full text-left">
                <div class="flex items-center">
                    <div class="bg-blue-100 text-blue-600 p-3 rounded-full mr-4">
                        <i class="fas fa-user-plus fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Action principale</p>
                        <p class="text-2xl font-semibold text-gray-900">Inscrire un élève</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>


            <button id="viewAllStudentsBtn"
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-green-100 text-green-600 p-3 rounded-full mr-4">
                        <i class="fas fa-users fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Liste des élèves</p>
                        <p class="text-2xl font-semibold text-gray-900">Tout afficher</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </button>

            <a href="mailto:administrationcsppunilu@gmail.com"
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
                <div class="flex items-center">
                    <div class="bg-yellow-100 text-yellow-600 p-3 rounded-full mr-4">
                        <i class="fas fa-headset fa-lg"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-500">Contact</p>
                        <p class="text-2xl font-semibold text-gray-900">Direction</p>
                    </div>
                </div>
                <i class="fas fa-chevron-right text-gray-400"></i>
            </a>

            <form id="filterByClassForm"
                class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex flex-col justify-center">
                <label for="studentClass" class="text-sm font-medium text-gray-500 mb-2">Filtrer par classe</label>
                <div class="flex items-center">
                    <select id="studentClass" name="classe" required
                        class="flex-grow p-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-purple-500">
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
                    <button type="submit"
                        class="ml-2 bg-purple-600 text-white p-2 rounded-lg hover:bg-purple-700 transition-colors duration-200">
                        <i class="fas fa-filter"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="contentContainer" class="container mx-auto px-4 py-8">
        <div id="inscriptionsSection" class="bg-white p-6 rounded-xl shadow-lg min-h-[50vh]">
            <h2 class="text-2xl font-bold text-gray-400 mb-4 flex items-center">
                <i class="fas fa-user-circle fa-lg mr-3 text-gray-400"></i>
                Liste des élèves
            </h2>

            <div id="defaultMessage" class="text-center text-gray-500 py-10">
                <i class="fas fa-info-circle fa-2x mb-4 text-gray-400"></i>
                <p class="text-lg">Sélectionnez une option ci-dessus pour afficher les données.</p>
            </div>

            <div id="resultsContainer"></div>

        </div>
    </div>
    <!-- Modale de confirmation suppression -->
    <div id="confirmationModal"
        class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-lg shadow-lg max-w-md w-full p-6">
            <h2 class="text-xl font-semibold mb-4 text-red-600 flex items-center">
                <i class="fas fa-exclamation-triangle mr-2"></i> Confirmation de suppression
            </h2>
            <p class="mb-6 text-gray-700">Voulez-vous vraiment supprimer cet élève ? <br> <span
                    class="font-semibold text-red-600">Cette action est irréversible.</span></p>
            <div class="flex justify-end space-x-4">
                <button id="cancelBtn"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold">Annuler</button>
                <button id="confirmBtn"
                    class="px-4 py-2 rounded bg-red-600 hover:bg-red-700 text-white font-semibold">Supprimer</button>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        $(document).ready(function () {
            let eleveEnModification = null;

            // Charger tous les élèves quand on clique sur le bouton
            $("#viewAllStudentsBtn").on("click", function () {
                chargerEleves();
            });

            // Charger élèves filtrés par classe
            $("#filterByClassForm").on("submit", function (e) {
                e.preventDefault();
                let classe = $("#studentClass").val();
                if (!classe) {
                    toastr.warning("Veuillez sélectionner une classe.");
                    return;
                }
                chargerEleves(classe);
            });

            // Fonction pour charger les élèves, avec filtre optionnel
            function chargerEleves(classe = null) {
                $.ajax({
                    url: window.location.href,
                    method: "GET",
                    data: { action: "getStudents", classe: classe },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            afficherTableau(response.inscriptions);
                        } else {
                            toastr.error(response.message || "Erreur lors du chargement des élèves.");
                        }
                    },
                    error: function () {
                        toastr.error("Une erreur est survenue.");
                    }
                });
            }

            // Fonction d'affichage du tableau avec colonne action
            function afficherTableau(eleves) {
                if (!eleves || eleves.length === 0) {
                    $("#resultsContainer").html("<p class='text-center text-gray-500 py-6'>Aucun élève trouvé.</p>");
                    $("#defaultMessage").hide();
                    $("#formModifContainer").remove();
                    return;
                }

                let html = `
            <div class="overflow-x-auto">
            <table class="min-w-full border border-gray-300 rounded-lg overflow-hidden">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 border">#</th>
                        <th class="px-4 py-2 border">Nom</th>
                        <th class="px-4 py-2 border">Postnom</th>
                        <th class="px-4 py-2 border">Prénom</th>
                        <th class="px-4 py-2 border">Sexe</th>
                        <th class="px-4 py-2 border">Classe</th>
                        <th class="px-4 py-2 border">Parent</th>
                        <th class="px-4 py-2 border">Adresse</th>
                        <th class="px-4 py-2 border">Année</th>
                        <th class="px-4 py-2 border">Action</th>
                    </tr>
                </thead>
                <tbody>
        `;

                eleves.forEach((eleve, index) => {
                    html += `
                <tr class="hover:bg-gray-50" data-id="${eleve.id}">
                    <td class="px-4 py-2 border">${index + 1}</td>
                    <td class="px-4 py-2 border">${eleve.nom_eleve || ''}</td>
                    <td class="px-4 py-2 border">${eleve.postnom_eleve || ''}</td>
                    <td class="px-4 py-2 border">${eleve.prenom_eleve || ''}</td>
                    <td class="px-4 py-2 border">${eleve.sexe_eleve || ''}</td>
                    <td class="px-4 py-2 border">${eleve.classe_selection || ''}</td>
                    <td class="px-4 py-2 border">${eleve.nom_parent || ''}</td>
                    <td class="px-4 py-2 border">${eleve.adresse_eleve || ''}</td>
                    <td class="px-4 py-2 border">${eleve.annee_inscription || ''}</td>
                    <td class="px-4 py-2 border text-center space-x-2">
                        <button class="modifierEleveBtn text-orange-600 hover:text-orange-800 transition" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="supprimerEleveBtn text-red-600 hover:text-red-800 transition" title="Supprimer">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </td>
                </tr>
            `;
                });

                html += `</tbody></table></div>`;

                $("#resultsContainer").html(html);
                $("#defaultMessage").hide();

                // Retirer formulaire modif s'il existe
                $("#formModifContainer").remove();

                // Attacher événements pour boutons Modifier et Supprimer
                $(".modifierEleveBtn").on("click", function () {
                    const tr = $(this).closest("tr");
                    const id = tr.data("id");
                    ouvrirFormulaireModification(id);
                });

                $(".supprimerEleveBtn").on("click", function () {
                    const tr = $(this).closest("tr");
                    const id = tr.data("id");
                    confirmerSuppression(id);
                });
            }

            // Ouvre le formulaire modif avec les données de l'élève
            function ouvrirFormulaireModification(id) {
                $.ajax({
                    url: window.location.href,
                    method: "GET",
                    data: { action: "getEleve", id: id },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            eleveEnModification = response.eleve;
                            afficherFormulaireModification(response.eleve);
                            $('html, body').animate({ scrollTop: $("#formModifContainer").offset().top }, 600);
                        } else {
                            toastr.error(response.message || "Impossible de récupérer les données de l'élève.");
                        }
                    },
                    error: function () {
                        toastr.error("Erreur lors de la récupération des données.");
                    }
                });
            }

            // Affiche le formulaire de modification sous le tableau
            function afficherFormulaireModification(eleve) {
                $("#formModifContainer").remove();

                let formHtml = `
            <div id="formModifContainer" class="bg-white p-6 rounded-xl shadow-lg mt-6 max-w-4xl mx-auto">
                <h3 class="text-xl font-semibold mb-4">Modifier l'élève : ${eleve.nom_eleve} ${eleve.postnom_eleve}</h3>
                <form id="formModifierEleve" class="space-y-4">
                    <input type="hidden" name="id" value="${eleve.id}">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium mb-1" for="nom_eleve">Nom</label>
                            <input id="nom_eleve" name="nom_eleve" type="text" value="${eleve.nom_eleve || ''}" required
                                class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="postnom_eleve">Postnom</label>
                            <input id="postnom_eleve" name="postnom_eleve" type="text" value="${eleve.postnom_eleve || ''}" required
                                class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="prenom_eleve">Prénom</label>
                            <input id="prenom_eleve" name="prenom_eleve" type="text" value="${eleve.prenom_eleve || ''}" required
                                class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="sexe_eleve">Sexe</label>
                            <select id="sexe_eleve" name="sexe_eleve" required class="w-full p-2 border border-gray-300 rounded-lg">
                                <option value="">Sélectionnez</option>
                                <option value="M" ${eleve.sexe_eleve === 'M' ? 'selected' : ''}>Masculin</option>
                                <option value="F" ${eleve.sexe_eleve === 'F' ? 'selected' : ''}>Féminin</option>
                            </select>
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="classe_selection">Classe</label>
                            <input id="classe_selection" name="classe_selection" type="text" value="${eleve.classe_selection || ''}" required
                                class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Ex: 7e EB">
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="nom_parent">Nom du parent</label>
                            <input id="nom_parent" name="nom_parent" type="text" value="${eleve.nom_parent || ''}" required
                                class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="adresse_eleve">Adresse</label>
                            <input id="adresse_eleve" name="adresse_eleve" type="text" value="${eleve.adresse_eleve || ''}" required
                                class="w-full p-2 border border-gray-300 rounded-lg">
                        </div>
                        <div>
                            <label class="block font-medium mb-1" for="annee_inscription">Année d'inscription</label>
                            <input id="annee_inscription" name="annee_inscription" type="text" value="${eleve.annee_inscription || ''}" required
                                class="w-full p-2 border border-gray-300 rounded-lg" placeholder="Ex: 2024">
                        </div>
                    </div>
                    <div class="mt-6 flex justify-end space-x-4">
                        <button type="button" id="btnAnnulerModif"
                            class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg">
                            Annuler
                        </button>
                        <button type="submit"
                            class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-lg">
                            Enregistrer
                        </button>
                    </div>
                </form>
            </div>
        `;

                $("#resultsContainer").after(formHtml);

                // Annuler la modification
                $("#btnAnnulerModif").on("click", function () {
                    $("#formModifContainer").remove();
                    eleveEnModification = null;
                });

                // Gérer la soumission du formulaire de modification
                $("#formModifierEleve").on("submit", function (e) {
                    e.preventDefault();
                    modifierEleve();
                });
            }

            // Envoi la modification à l'API et met à jour le tableau
            function modifierEleve() {
                let form = $("#formModifierEleve");
                let data = form.serializeArray();
                data.push({ name: "action", value: "modifierEleve" });

                $.ajax({
                    url: window.location.href,
                    method: "POST",
                    data: $.param(data),
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message || "Élève modifié avec succès.");
                            // Recharger la liste avec modifications
                            chargerEleves();
                            $("#formModifContainer").remove();
                            eleveEnModification = null;
                        } else {
                            toastr.error(response.message || "Erreur lors de la modification.");
                        }
                    },
                    error: function () {
                        toastr.error("Erreur lors de la modification.");
                    }
                });
            }

            // Confirmation suppression
            let idToDelete = null;

            function confirmerSuppression(id) {
                idToDelete = id;
                $("#confirmationModal").fadeIn(200);
            }

            $("#cancelBtn").on("click", function () {
                idToDelete = null;
                $("#confirmationModal").fadeOut(200);
            });

            $("#confirmBtn").on("click", function () {
                if (idToDelete !== null) {
                    supprimerEleve(idToDelete);
                    idToDelete = null;
                    $("#confirmationModal").fadeOut(200);
                }
            });


            // Supprimer un élève via AJAX
            function supprimerEleve(id) {
                $.ajax({
                    url: window.location.href,
                    method: "POST",
                    data: { action: "supprimerEleve", id: id },
                    dataType: "json",
                    success: function (response) {
                        if (response.success) {
                            toastr.success(response.message || "Élève supprimé avec succès.");
                            chargerEleves();
                        } else {
                            toastr.error(response.message || "Erreur lors de la suppression.");
                        }
                    },
                    error: function () {
                        toastr.error("Erreur lors de la suppression.");
                    }
                });
            }
        });
    </script>

    <script>
        const parentsOptions = `<?php echo $parentsOptions; ?>`;
    </script>


    <script>
        $(document).ready(function () {
            $("#showInscriptionFormBtn").on("click", function (e) {
                e.preventDefault();
                // Masquer le message par défaut
                $("#defaultMessage").hide();

                // Si le formulaire existe déjà, ne pas le recréer
                if ($("#inscriptionFormContainer").length) {
                    $("#inscriptionFormContainer").show();
                    return;
                }

                // Injecter le formulaire sous #resultsContainer
                const formHtml = `
      <div id="inscriptionFormContainer" class="bg-white p-6 rounded-xl shadow-lg max-w-4xl mx-auto">
        <h3 class="text-xl font-semibold mb-6">Formulaire d'inscription d'un élève</h3>
        <form id="inscriptionEleveForm" method="POST" action="" class="space-y-4">
          <input type="hidden" name="inscription_eleve" value="1" />
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label for="nom_eleve" class="block mb-1 font-medium">Nom<span class="text-orange-600">*</span></label>
              <input type="text" id="nom_eleve" name="nom_eleve" required
                class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
            <div>
              <label for="postnom_eleve" class="block mb-1 font-medium">Postnom<span class="text-orange-600">*</span></label>
              <input type="text" id="postnom_eleve" name="postnom_eleve" required
                class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
            <div>
              <label for="prenom_eleve" class="block mb-1 font-medium">Prénom<span class="text-orange-600">*</span></label>
              <input type="text" id="prenom_eleve" name="prenom_eleve" required
                class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
            <div>
              <label for="sexe_eleve" class="block mb-1 font-medium">Sexe<span class="text-orange-600">*</span></label>
              <select id="sexe_eleve" name="sexe_eleve" required
                class="w-full p-2 border border-gray-300 rounded-lg">
                <option value="">Sélectionnez</option>
                <option value="M">Masculin</option>
                <option value="F">Féminin</option>
              </select>
            </div>
            <div>
                <label for="classe_selection" class="block mb-1 font-medium">Classe<span class="text-orange-600">*</span></label>
                <select id="classe_selection" name="classe_selection" required
                    class="w-full p-2 border border-gray-300 rounded-lg">
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
                </div>

            <div>
                <label for="nom_parent" class="block mb-1 font-medium">Nom du parent<span class="text-orange-600">*</span></label>
                <select id="nom_parent" name="nom_parent" required class="w-full p-2 border border-gray-300 rounded-lg">
                    <option value="">Sélectionnez un parent</option>
                    ${parentsOptions}
                </select>
            </div>
            <div>
              <label for="adresse_eleve" class="block mb-1 font-medium">Adresse<span class="text-orange-600">*</span></label>
              <input type="text" id="adresse_eleve" name="adresse_eleve" required
                class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>
           <div>
            <label for="annee_inscription" class="block mb-1 font-medium">Année d'inscription<span class="text-orange-600">*</span></label>
            <input type="text" id="annee_inscription" name="annee_inscription" required placeholder="Ex: 2024-01"
                value="<?php echo date('Y') . '-'; ?>" 
                class="w-full p-2 border border-gray-300 rounded-lg" />
            </div>


          </div>
          <div class="mt-6 flex justify-end space-x-4">
            <button type="button" id="annulerInscriptionBtn"
              class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-semibold py-2 px-4 rounded-lg">Annuler</button>
            <button type="submit"
              class="bg-orange-600 hover:bg-orange-700 text-white font-semibold py-2 px-6 rounded-lg">Enregistrer</button>
          </div>
          <div id="messageInscription" class="mt-4"></div>
        </form>
      </div>
    `;

                $("#resultsContainer").html(formHtml);

                // Annuler : masquer formulaire et réafficher le message par défaut
                $("#annulerInscriptionBtn").on("click", function () {
                    $("#inscriptionFormContainer").hide();
                    $("#defaultMessage").show();
                    $("#resultsContainer").html("");
                });

                // Soumission du formulaire en AJAX
                $("#inscriptionEleveForm").on("submit", function (e) {
                    e.preventDefault();

                    const formData = $(this).serialize();

                    $.ajax({
                        url: window.location.href,
                        method: "POST",
                        data: formData,
                        dataType: "json",
                        success: function (response) {
                            if (response.success) {
                                toastr.success(response.message || "Inscription réussie.");
                                // Réinitialiser le formulaire
                                $("#inscriptionEleveForm")[0].reset();
                            } else {
                                $("#messageInscription").html(`<p class="text-red-500">${response.message || "Erreur lors de l'inscription."}</p>`);
                                toastr.error(response.message || "Erreur lors de l'inscription.");
                            }
                        },
                        error: function () {
                            toastr.error("Erreur réseau lors de l'inscription.");
                        }
                    });
                });
            });
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
        cercle.style.border = '3px solid #e12c4e';
        cercle.style.borderTop = '3px solid #e49100';
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