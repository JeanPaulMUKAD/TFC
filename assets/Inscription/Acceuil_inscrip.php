<?php
require_once '../Controllers/AuthController.php';
$auth = new AuthController();
$listeEleves = $auth->getToutesLesInscriptions();
$inscriptions = $auth->getInscriptionsParClasse();
$eleveData = null;
$messageErreur = null;

// --- GESTION DES REQUÊTES AJAX (Anciennement vos fichiers API) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' || (isset($_GET['action']) && $_GET['action'] === 'getEleve')) {
    // Si la requête est AJAX et demande des données JSON
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');

        if (isset($_POST['action'])) {
            $action = $_POST['action'];

            switch ($action) {
                case 'rechercherEleve':
                    $matricule = htmlspecialchars(trim($_POST['matricule']));
                    $resultat = $auth->rechercherEleveParMatricule($matricule);
                    echo json_encode($resultat);
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
            }
        } elseif (isset($_GET['action']) && $_GET['action'] === 'getEleve' && isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $eleve = $auth->obtenirInfosEleveParId($id);

            if ($eleve) {
                echo json_encode(['success' => true, 'eleve' => $eleve]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Élève non trouvé.']);
            }
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
    <!-- Font Awesome (version stable gratuite sans compte) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>

<body class="bg-black text-white">

    <!-- Navbar -->
    <nav
        class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white text-black shadow-md">
        <div class="text-2xl font-bold">
            <span class="text-black">C.S.P.P</span><span class="text-orange-500 font-extrabold">.UNILU</span>
        </div>

        <div class="flex items-center space-x-4">
            <a href="mailto:administrationcsppunilu@gmail.com" target="_blank" class="text-sm font-medium">Aide</a>
            <a href="#"
                class="bg-gradient-to-r from-red-600 to-orange-500 hover:bg-orange-600 transition text-white font-semibold py-2 px-4 rounded-full text-sm">
                Se déconnecter
            </a>
        </div>
    </nav>


    <section class="relative px-4 py-10 bg-white pt-28">
        <div
            class="relative mx-auto max-w-14xl rounded-3xl overflow-hidden bg-black text-white min-h-[100vh] flex items-center justify-center shadow-2xl">
            <!-- Dégradés discrets dans les coins -->
            <div
                class="absolute top-0 left-0 w-64 h-64 bg-red-800 rounded-full opacity-20 blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute top-0 right-0 w-64 h-64 bg-red-800 rounded-full opacity-20 blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 left-0 w-64 h-64 bg-red-800 rounded-full opacity-20 blur-3xl pointer-events-none">
            </div>
            <div
                class="absolute bottom-0 right-0 w-64 h-64 bg-red-800 rounded-full opacity-20 blur-3xl pointer-events-none">
            </div>

            <!-- Contenu centré -->
            <div class="relative z-10 max-w-4xl w-full text-center space-y-6 p-8">
                <div class="inline-block text-xs px-4 py-1 border border-white/20 rounded-full text-white bg-white/10">
                    <span class="text-green-700">● </span>ESPACE DU SECRÉTAIRE ACADÉMIQUE
                </div>

                <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
                    Bienvenue sur la page du<br>
                    <span
                        class="inline-block bg-gradient-to-r from-red-600 to-orange-500 px-2 py-1 rounded-md text-white">Secrétaire</span>
                </h1>

                <p class="text-lg text-gray-300 mt-2">
                    Vous êtes chargé de la gestion administrative des élèves et du suivi des inscriptions. <br>
                    Votre rôle est essentiel dans le bon fonctionnement de l’établissement.
                </p>

                <div class="mt-8 flex flex-col md:flex-row justify-center items-center gap-4">
                    <a href="../Inscription/inscription.php"
                        class="bg-gradient-to-r from-red-600 to-orange-500 hover:bg-orange-600 transition text-white font-semibold py-3 px-6 rounded-full text-sm flex items-center gap-2">
                        <i class="fas fa-user-pen text-white"></i>
                        <span>Inscrire un élève</span>
                    </a>
                    <a href="mailto:administrationcsppunilu@gmail.com"
                        class="bg-white text-black hover:bg-gray-200 transition font-semibold py-3 px-6 rounded-full flex items-center gap-2 text-sm">
                        <i class="fas fa-phone text-red-500"></i>
                        <span>Contacter la direction</span>
                    </a>
                </div>

                <!-- Infos du bas -->
                <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm text-gray-300">
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-folder text-orange-400"></i>
                        <span>Gestion des dossiers</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-user-pen text-orange-400"></i>
                        <span>Inscriptions des élèves</span>
                    </div>
                    <div class="flex items-center justify-center space-x-2">
                        <i class="fas fa-lock text-orange-400"></i>
                        <span>Sécurité des données</span>
                    </div>
                </div>
            </div>
        </div>
    </section>




    <section class="bg-gray-50 py-20 px-6 text-black">
        <div class="max-w-7xl mx-auto">
            <h2 class="text-4xl font-extrabold text-gray-900 mb-6 text-center">
                Liste de tous les élèves inscrits
            </h2>

            <p class="text-gray-600 text-lg text-center mb-6">
                Voici tous les élèves inscrits cette année, vous pouvez filtrer par classe.
            </p>

            <div class="mb-8 max-w-md mx-auto">
                <input type="text" id="searchClasse" placeholder="Filtrer par classe..."
                    class="w-full border border-gray-300 rounded-lg px-4 py-2" onkeyup="filterClasses()">
            </div>

            <div id="inscriptionsContainer">
                <?php
                if (!empty($inscriptions)) {
                    $currentClasse = null;
                    $index = 1;
                    foreach ($inscriptions as $eleve) {
                        if ($eleve['classe_selection'] !== $currentClasse) {
                            if ($currentClasse !== null) {
                                // Ferme le tableau précédent et le div de groupe de classe
                                echo "</tbody></table></div></div>";
                            }
                            $currentClasse = $eleve['classe_selection'];
                            $index = 1; // Réinitialise l'index pour la nouvelle classe
                            echo '<div class="classe-group mb-12" data-classe="' . htmlspecialchars($currentClasse) . '">';
                            echo "<h3 class='text-2xl font-semibold mb-4 text-gray-800'>Classe : " . htmlspecialchars($currentClasse) . "</h3>"; // Couleur de texte pour les titres de classe
                            echo '<div class="overflow-x-auto bg-white rounded-xl shadow-lg">';
                            echo '<table class="min-w-full text-sm border border-gray-200 rounded-lg">'; // Supprime text-left de la table entière pour contrôler par colonne
                            echo '<thead class="bg-gray-100 text-gray-700">'; // Supprime text-center ici
                            echo '<tr>';
                            echo '<th class="px-4 py-3 text-center w-12">#</th>'; // Centre, avec largeur fixe min.
                            echo '<th class="px-4 py-3 text-left">Matricule</th>'; // Alignement à gauche pour la lisibilité
                            echo '<th class="px-4 py-3 text-left">Nom complet</th>'; // Alignement à gauche
                            echo '<th class="px-4 py-3 text-center">Sexe</th>'; // Centré
                            echo '<th class="px-4 py-3 text-left">Classe</th>'; // Alignement à gauche
                            echo '<th class="px-4 py-3 text-left">Parent</th>'; // Alignement à gauche
                            echo '<th class="px-4 py-3 text-left">Adresse</th>'; // Alignement à gauche
                            echo '<th class="px-4 py-3 text-center">Année</th>'; // Centré
                            echo '<th class="px-4 py-3 text-center w-36">Action</th>'; // Centré, avec largeur fixe min.
                            echo '</tr></thead><tbody class="bg-white text-gray-800">'; // Supprime text-center ici
                        }

                        $nomComplet = htmlspecialchars($eleve['nom_eleve'] . ' ' . $eleve['postnom_eleve'] . ' ' . $eleve['prenom_eleve']);
                        $matricule = htmlspecialchars($eleve['matricule']);
                        $id = (int) $eleve['id'];
                        echo '<tr class="border-t hover:bg-gray-50">';
                        echo '<td class="px-4 py-2 text-center">' . $index++ . '</td>'; // Centré
                        echo '<td class="px-4 py-2 text-left">' . $matricule . '</td>'; // Gauche
                        echo '<td class="px-4 py-2 text-left">' . $nomComplet . '</td>'; // Gauche
                        echo '<td class="px-4 py-2 text-center">' . htmlspecialchars($eleve['sexe_eleve']) . '</td>'; // Centré
                        echo '<td class="px-4 py-2 text-left">' . htmlspecialchars($eleve['classe_selection']) . '</td>'; // Gauche
                        echo '<td class="px-4 py-2 text-left">' . htmlspecialchars($eleve['nom_parent']) . '</td>'; // Gauche
                        echo '<td class="px-4 py-2 text-left">' . htmlspecialchars($eleve['adresse_eleve']) . '</td>'; // Gauche
                        echo '<td class="px-4 py-2 text-center">' . htmlspecialchars($eleve['annee_inscription']) . '</td>'; // Centré
                
                        // Boutons Modifier / Supprimer
                        echo '<td class="px-4 py-2 flex items-center justify-center space-x-2">'; // Utilise flex et justify-center pour aligner les boutons
                        echo '<button onclick="openModifierModal(' . $id . ')" class="bg-yellow-600 text-white text-xs px-4 py-1 rounded hover:bg-yellow-700 transition" title="Modifier">Modifier</button>'; // Taille de texte plus petite
                        echo '<button onclick="openSupprimerModal(' . $id . ', \'' . $matricule . '\')" class="bg-red-600 text-white text-xs px-4 py-1 rounded hover:bg-red-700 transition" title="Supprimer">Supprimer</button>'; // Taille de texte plus petite
                        echo '</td>';

                        echo '</tr>';
                    }
                    // Ferme le dernier tableau et le div de groupe après la boucle
                    echo "</tbody></table></div></div>";
                } else {
                    echo '<p class="text-center text-red-500 font-semibold">Aucun élève inscrit pour l’instant.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <div id="modalModifier" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white text-black">
            <h3 class="text-lg font-bold mb-4">Modifier l'élève</h3>
            <form id="modifierForm" onsubmit="submitModifier(event)">
                <input type="hidden" id="modId" name="id">
                <div class="mt-4">
                    <label for="modNom" class="block text-sm font-medium text-gray-700">Nom:</label>
                    <input type="text" id="modNom" name="nom_eleve"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="mt-4">
                    <label for="modPostnom" class="block text-sm font-medium text-gray-700">Postnom:</label>
                    <input type="text" id="modPostnom" name="postnom_eleve"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="mt-4">
                    <label for="modPrenom" class="block text-sm font-medium text-gray-700">Prénom:</label>
                    <input type="text" id="modPrenom" name="prenom_eleve"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="mt-4">
                    <label for="modSexe" class="block text-sm font-medium text-gray-700">Sexe:</label>
                    <select id="modSexe" name="sexe_eleve"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="M">Masculin</option>
                        <option value="F">Féminin</option>
                    </select>
                </div>
                <div class="mt-4">
                    <label for="modClasse" class="block text-sm font-medium text-gray-700">Classe:</label>
                    <input type="text" id="modClasse" name="classe_selection"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="mt-4">
                    <label for="modParent" class="block text-sm font-medium text-gray-700">Nom Parent:</label>
                    <input type="text" id="modParent" name="nom_parent"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="mt-4">
                    <label for="modAdresse" class="block text-sm font-medium text-gray-700">Adresse:</label>
                    <input type="text" id="modAdresse" name="adresse_eleve"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="mt-4">
                    <label for="modAnnee" class="block text-sm font-medium text-gray-700">Année:</label>
                    <input type="text" id="modAnnee" name="annee_inscription"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div class="items-center px-4 py-3">
                    <button type="submit"
                        class="px-4 py-2 bg-green-500 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-green-300">
                        Enregistrer
                    </button>
                    <button type="button" onclick="closeModifierModal()"
                        class="mt-3 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div id="modalSupprimer" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white text-black">
            <h3 class="text-lg font-bold mb-4">Confirmer la suppression</h3>
            <p id="supprimerMessage" class="py-4 text-center text-gray-700">Voulez-vous vraiment supprimer cet élève ?
            </p>
            <form id="supprimerForm" onsubmit="submitSupprimer(event)">
                <input type="hidden" id="supId" name="id">
                <div class="items-center px-4 py-3">
                    <button type="submit"
                        class="px-4 py-2 bg-red-600 text-white text-base font-medium rounded-md w-full shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-300">
                        Supprimer
                    </button>
                    <button type="button" onclick="closeSupprimerModal()"
                        class="mt-3 px-4 py-2 bg-gray-300 text-gray-700 text-base font-medium rounded-md w-full shadow-sm hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300">
                        Annuler
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // JavaScript for Matricule Search (unchanged in its logic, but will now hit the same file)
        async function fetchEleveByMatricule(e) {
            e.preventDefault();
            const matricule = document.getElementById('matriculeSearchInput').value;
            const resultContainer = document.getElementById('searchResultContainer');

            resultContainer.innerHTML = "<p class='text-gray-500 text-center'>Recherche en cours...</p>";

            try {
                const formData = new FormData();
                formData.append('action', 'rechercherEleve'); // Ajoutez une action pour la gestion PHP
                formData.append('matricule', matricule);

                const response = await fetch(window.location.href, { // Toujours POST vers la même page
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest' // Indique que c'est une requête AJAX
                    },
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    const eleve = data.eleve;
                    resultContainer.innerHTML = `
                        <div class="overflow-x-auto mt-4">
                            <table class="min-w-full text-sm text-left border border-gray-200 rounded-lg">
                                <thead class="bg-gray-100 text-gray-700">
                                    <tr>
                                        <th class="px-4 py-2">Matricule</th>
                                        <th class="px-4 py-2">Nom</th>
                                        <th class="px-4 py-2">Postnom</th>
                                        <th class="px-4 py-2">Prénom</th>
                                        <th class="px-4 py-2">Sexe</th>
                                        <th class="px-4 py-2">Classe</th>
                                        <th class="px-4 py-2">Nom Parent</th>
                                        <th class="px-4 py-2">Adresse</th>
                                        <th class="px-4 py-2">Année</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white text-black">
                                    <tr class="border-t">
                                        <td class="px-4 py-2">${eleve.matricule}</td>
                                        <td class="px-4 py-2">${eleve.nom_eleve}</td>
                                        <td class="px-4 py-2">${eleve.postnom_eleve}</td>
                                        <td class="px-4 py-2">${eleve.prenom_eleve}</td>
                                        <td class="px-4 py-2">${eleve.sexe_eleve}</td>
                                        <td class="px-4 py-2">${eleve.classe_selection}</td>
                                        <td class="px-4 py-2">${eleve.nom_parent}</td>
                                        <td class="px-4 py-2">${eleve.adresse_eleve}</td>
                                        <td class="px-4 py-2">${eleve.annee_inscription}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    `;
                } else {
                    resultContainer.innerHTML = `<p class="text-red-500 font-semibold text-center">${data.message}</p>`;
                }

            } catch (error) {
                resultContainer.innerHTML = `<p class="text-red-500 font-semibold text-center">Erreur lors de la requête: ${error.message}</p>`;
                console.error('Fetch error:', error); // Log l'erreur complète dans la console
            }

            return false; // Prevent default form submission
        }

        // JavaScript for Class Filter (for the second section)
        function filterClasses() {
            const input = document.getElementById('searchClasse');
            const filter = input.value.toLowerCase();
            const groupes = document.querySelectorAll('#inscriptionsContainer .classe-group');

            groupes.forEach(groupe => {
                const classe = groupe.getAttribute('data-classe').toLowerCase();
                groupe.style.display = classe.includes(filter) ? '' : 'none';
            });
        }

        // JavaScript for Modals (Modifier / Supprimer) - MODIFIED to hit the same file
        let currentEditId = null;
        let currentDeleteId = null;
        let currentDeleteMatricule = null;

        function openModifierModal(id) {
            currentEditId = id;
            // Requête GET vers le même fichier, avec un paramètre 'action'
            fetch(`${window.location.href}?action=getEleve&id=${id}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Indique que c'est une requête AJAX
                }
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        const e = data.eleve;
                        document.getElementById('modId').value = e.id;
                        document.getElementById('modNom').value = e.nom_eleve;
                        document.getElementById('modPostnom').value = e.postnom_eleve;
                        document.getElementById('modPrenom').value = e.prenom_eleve;
                        document.getElementById('modSexe').value = e.sexe_eleve;
                        document.getElementById('modClasse').value = e.classe_selection;
                        document.getElementById('modParent').value = e.nom_parent;
                        document.getElementById('modAdresse').value = e.adresse_eleve;
                        document.getElementById('modAnnee').value = e.annee_inscription;
                        document.getElementById('modalModifier').classList.remove('hidden');
                    } else {
                        alert("Erreur chargement élève : " + data.message);
                        console.error("Erreur PHP (getEleve) :", data.message);
                    }
                })
                .catch(err => {
                    alert("Erreur réseau ou JSON : " + err.message);
                    console.error('Fetch error (openModifierModal):', err); // Log l'erreur complète dans la console
                });
        }

        function closeModifierModal() {
            document.getElementById('modalModifier').classList.add('hidden');
        }

        function submitModifier(e) {
            e.preventDefault();
            const form = e.target;
            const formData = new FormData(form);
            formData.append('action', 'modifierEleve'); // Ajoutez une action pour la gestion PHP

            fetch(window.location.href, { // POST vers la même page
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Indique que c'est une requête AJAX
                },
                body: formData
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload(); // Reload page to reflect changes
                    }
                })
                .catch(err => {
                    alert('Erreur réseau ou JSON: ' + err.message);
                    console.error('Fetch error (submitModifier):', err); // Log l'erreur complète dans la console
                });
        }

        function openSupprimerModal(id, matricule) {
            currentDeleteId = id;
            currentDeleteMatricule = matricule;
            document.getElementById('supId').value = id;
            document.getElementById('supprimerMessage').textContent = `Voulez-vous vraiment supprimer l'élève avec le matricule ${matricule} ?`;
            document.getElementById('modalSupprimer').classList.remove('hidden');
        }

        function closeSupprimerModal() {
            document.getElementById('modalSupprimer').classList.add('hidden');
        }

        function submitSupprimer(e) {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'supprimerEleve'); // Ajoutez une action pour la gestion PHP

            fetch(window.location.href, { // POST vers la même page
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest' // Indique que c'est une requête AJAX
                },
                body: formData
            })
                .then(res => {
                    if (!res.ok) {
                        throw new Error(`HTTP error! status: ${res.status}`);
                    }
                    return res.json();
                })
                .then(data => {
                    alert(data.message);
                    if (data.success) {
                        location.reload(); // Reload page to reflect changes
                    }
                })
                .catch(err => {
                    alert('Erreur réseau ou JSON: ' + err.message);
                    console.error('Fetch error (submitSupprimer):', err); // Log l'erreur complète dans la console
                });
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