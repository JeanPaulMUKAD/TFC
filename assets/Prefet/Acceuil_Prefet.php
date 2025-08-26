<?php

require_once '../Controllers/AuthController.php';

$auth = new AuthController();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  header('Content-Type: application/json');
  $action = $_POST['action'] ?? null;

  if ($action === 'get_all_paiements') {
    $resultat = $auth->obtenirTousLesPaiements();
  } elseif ($action === 'get_all_eleves') {
    $resultat = $auth->obtenirTousLesEleves();
  } elseif ($action === 'get_arrears') { // Nouvelle action
    $resultat = $auth->obtenirElevesAvecArrieres();
  } elseif ($action === 'get_eleves_by_class') { // Nouvelle action
    $classe = htmlspecialchars(trim($_POST['classe']));
    $resultat = $auth->obtenirElevesParClasse($classe);
  }

  echo json_encode($resultat);
  exit;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard Préfet | C.S.P.P.UNILU</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link rel="shortcut icon" href="/assets/images/logo_pp.png">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 text-black">
  <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white shadow-md">
    <div class="text-2xl font-bold flex items-center">
            <img src="/assets/images/logo_pp2.png" alt="Logo" class="h-10 w-10 mr-2" />
            <span class="text-black">C.S.P.P</span><span class="text-green-600 font-extrabold">.UNILU</span>
        </div>
    <div class="flex items-center space-x-4">
      <a href="mailto:administrationcsppunilu@gmail.com" class="text-sm font-medium">Assistance</a>
      <a href="/logoutPrefet"
        class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-emerald-600 hover:to-green-500 text-white font-semibold py-2 px-4 rounded-full text-sm">
        Se déconnecter
      </a>
    </div>
  </nav>

  <div class="container mx-auto px-4 py-8 mt-20">
    <h1 class="text-3xl font-bold text-gray-800 mb-6">Tableau de bord du Préfet</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
      <button id="viewAllPaymentsBtn"
        class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
        <div class="flex items-center">
          <div class="bg-blue-100 text-blue-600 p-3 rounded-full mr-4">
            <i class="fas fa-money-bill-wave fa-lg"></i>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Rapports de paiements</p>
            <p class="text-2xl font-semibold text-gray-900">Tout afficher</p>
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

      <button id="viewArrearsBtn"
        class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
        <div class="flex items-center">
          <div class="bg-red-100 text-red-600 p-3 rounded-full mr-4">
            <i class="fas fa-exclamation-triangle fa-lg"></i>
          </div>
          <div>
            <p class="text-sm font-medium text-gray-500">Alertes</p>
            <p class="text-2xl font-semibold text-gray-900">Arriérés de paiement</p>
          </div>
        </div>
        <i class="fas fa-chevron-right text-gray-400"></i>
      </button>

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

    <div id="dashboardContent" class="bg-white p-6 rounded-xl shadow-lg min-h-[50vh]">
      <div id="defaultMessage" class="text-center text-gray-500 py-10">
        <i class="fas fa-info-circle fa-2x mb-4 text-gray-400"></i>
        <p class="text-lg">Sélectionnez une option ci-dessus pour afficher les données.</p>
      </div>
      <div id="resultsContainer"></div>
    </div>
    
  </div>
  

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Références aux éléments du DOM
      const viewAllPaymentsBtn = document.getElementById('viewAllPaymentsBtn');
      const viewAllStudentsBtn = document.getElementById('viewAllStudentsBtn');
      const viewArrearsBtn = document.getElementById('viewArrearsBtn');
      const filterByClassForm = document.getElementById('filterByClassForm');
      const searchPaymentForm = document.getElementById('searchPaymentForm');
      const searchStudentForm = document.getElementById('searchStudentForm');
      const resultsContainer = document.getElementById('resultsContainer');
      const defaultMessage = document.getElementById('defaultMessage');

      // Cacher le message par défaut
      defaultMessage.classList.remove('hidden');

      // --- Gestion des événements des boutons et formulaires ---

      viewAllPaymentsBtn.addEventListener('click', function () {
        fetchAllData('paiements');
      });

      viewAllStudentsBtn.addEventListener('click', function () {
        fetchAllData('eleves');
      });

      viewArrearsBtn.addEventListener('click', function () {
        fetchArrearsData();
      });

      filterByClassForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const classe = document.getElementById('studentClass').value;
        if (classe) {
          fetchDataByClass(classe);
        }
      });

      searchPaymentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const matricule = document.getElementById('paymentMatricule').value;
        if (matricule) {
          fetchDataByMatricule('paiements', matricule);
        }
      });

      searchStudentForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const matricule = document.getElementById('studentMatricule').value;
        if (matricule) {
          fetchDataByMatricule('eleve', matricule);
        }
      });

      // --- Fonctions de requête asynchrone ---

      async function fetchArrearsData() {
        showLoading();
        try {
          const formData = new FormData();
          formData.append('action', 'get_arrears');

          const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success && data.eleves && data.eleves.length > 0) {
            resultsContainer.innerHTML = createTable(data.eleves, 'eleves');
          } else {
            showError(data.message || "Aucun élève avec des arriérés de paiement.");
          }
        } catch (error) {
          showError("Erreur de connexion au serveur.");
          console.error("Erreur:", error);
        }
      }

      async function fetchDataByClass(classe) {
        showLoading();
        try {
          const formData = new FormData();
          formData.append('action', 'get_eleves_by_class');
          formData.append('classe', classe);

          const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success && data.eleves && data.eleves.length > 0) {
            resultsContainer.innerHTML = createTable(data.eleves, 'eleves');
          } else {
            showError(data.message || `Aucun élève trouvé pour la classe ${classe}.`);
          }
        } catch (error) {
          showError("Erreur de connexion au serveur.");
          console.error("Erreur:", error);
        }
      }

      async function fetchDataByMatricule(type, matricule) {
        showLoading();
        const action = type === 'paiements' ? 'get_paiements_by_matricule' : 'get_eleve_by_matricule';

        try {
          const formData = new FormData();
          formData.append('matricule', matricule);
          formData.append('action', action);

          const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success && data[type]) {
            const items = Array.isArray(data[type]) ? data[type] : [data[type]];
            resultsContainer.innerHTML = createTable(items, type);
          } else {
            showError(data.message || "Aucune donnée trouvée.");
          }
        } catch (error) {
          showError("Erreur de connexion au serveur.");
          console.error("Erreur:", error);
        }
      }

      async function fetchAllData(type) {
        showLoading();
        const action = type === 'paiements' ? 'get_all_paiements' : 'get_all_eleves';

        try {
          const formData = new FormData();
          formData.append('action', action);

          const response = await fetch(window.location.href, {
            method: 'POST',
            body: formData
          });
          const data = await response.json();

          if (data.success && data[type] && data[type].length > 0) {
            resultsContainer.innerHTML = createTable(data[type], type);
          } else {
            showError(data.message || "Aucune donnée trouvée.");
          }
        } catch (error) {
          showError("Erreur de connexion au serveur.");
          console.error("Erreur:", error);
        }
      }

      // --- Fonctions d'interface utilisateur (UI) ---

      function showLoading() {
        defaultMessage.classList.add('hidden');
        resultsContainer.innerHTML = `
                    <div class="flex justify-center items-center py-8">
                        <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-green-500"></div>
                        <span class="ml-3 text-gray-600">Chargement des données...</span>
                    </div>
                `;
      }

      function showError(message) {
        resultsContainer.innerHTML = `
                    <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
                        <div class="flex items-center">
                            <i class="fas fa-times-circle text-red-500 mr-3"></i>
                            <p class="text-red-700">${message}</p>
                        </div>
                    </div>
                `;
      }

      function createTable(data, type) {
        if (type === 'paiements') {
          // Tableau pour les paiements
          const headers = ['Matricule', 'Nom', 'Post-Nom', 'Prénom', 'Sexe', 'Date', 'Montant', 'Motif', 'Classe'];
          const rows = data.map(p => `
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.matricule}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.nom_eleve}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.postnom_eleve}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.prenom_eleve}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.sexe_eleve === 'M' ? 'Masculin' : 'Féminin'}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.date_paiement}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.montant_payer}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.motif_paiement}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${p.classe_eleve}</td>
                            
                        </tr>
                    `).join('');

          return `
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Rapports de Paiements</h2>
                        <div class="overflow-x-auto bg-white rounded-xl shadow-md">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        ${headers.map(h => `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">${h}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                    `;
        } else { // 'eleves', 'arrears', ou 'eleves_by_class'
          // Tableau pour les élèves (y compris les arriérés et le filtrage par classe)
          // Vérifie si les données contiennent la colonne 'arrieres' pour adapter l'affichage.
          const isArrears = data.some(item => item.arrieres !== undefined);
          const headers = isArrears
            ? ['Matricule', 'Nom', 'Postnom', 'Prénom', 'Classe', 'Montant annuel', 'Total payé', 'Reste à payer', ]
            : ['Matricule', 'Nom', 'Postnom', 'Prénom', 'Sexe', 'Classe', 'Nom parent', 'Adresse'];

          const rows = data.map(e => {
            if (isArrears) {
              return `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${e.matricule}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.nom_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.postnom_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.prenom_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.classe_selection}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.total_annuel}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.total_paye}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-red-600 font-bold">${e.arrieres}</td>
                    </tr>
                `;
            } else {
              return `
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${e.matricule}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.nom_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.postnom_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.prenom_eleve}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.sexe_eleve === 'M' ? 'Masculin' : 'Féminin'}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.classe_selection}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.nom_parent}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${e.adresse_eleve}</td>
                    </tr>
                `;
            }
          }).join('');

          let title = "Liste des élèves";
          if (isArrears) {
            title = "Élèves avec des arriérés de paiement";
          }
          return `
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">${title}</h2>
                        <div class="overflow-x-auto bg-white rounded-xl shadow-md">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        ${headers.map(h => `<th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">${h}</th>`).join('')}
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    ${rows}
                                </tbody>
                            </table>
                        </div>
                    `;
        }
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
    cercle.style.border = '3px solid #16a34a';
    cercle.style.borderTop = '3px solid #041141';
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