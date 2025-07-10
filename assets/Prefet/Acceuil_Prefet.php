<?php
  require_once '../Controllers/AuthController.php';
  $auth = new AuthController();
  $messageErreur = null;

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $matricule = htmlspecialchars(trim($_POST['matricule']));

    if (isset($_POST['action']) && $_POST['action'] === 'get_paiements') {
      $resultat = $auth->obtenirPaiementsParMatricule($matricule);
    } else {
      $resultat = $auth->obtenirInfosEleveParMatricule($matricule);
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
  <title>Espace Préfet | C.S.P.P.UNILU</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 text-black">

  <!-- Navbar -->
  <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white shadow-md">
    <div class="text-2xl font-bold">
      <span class="text-black">C.S.P.P</span><span class="text-green-600 font-extrabold">.UNILU</span>
    </div>
    <div class="flex items-center space-x-4">
      <a href="mailto:administrationcsppunilu@gmail.com" class="text-sm font-medium">Assistance</a>
      <a href="#"
        class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-emerald-600 hover:to-green-500 text-white font-semibold py-2 px-4 rounded-full text-sm">
        Se déconnecter
      </a>
    </div>
  </nav>

  <!-- Section Préfet -->
  <section class="relative px-4 py-10 bg-white pt-28">
    <div
      class="relative mx-auto max-w-14xl rounded-3xl overflow-hidden bg-gray-900 text-white min-h-[90vh] flex items-center justify-center shadow-2xl">

      <!-- Dégradés -->
      <div class="absolute top-0 left-0 w-64 h-64 bg-green-800 rounded-full opacity-20 blur-3xl"></div>
      <div class="absolute top-0 right-0 w-64 h-64 bg-emerald-700 rounded-full opacity-20 blur-3xl"></div>
      <div class="absolute bottom-0 left-0 w-64 h-64 bg-emerald-700 rounded-full opacity-20 blur-3xl"></div>
      <div class="absolute bottom-0 right-0 w-64 h-64 bg-green-800 rounded-full opacity-20 blur-3xl"></div>

      <!-- Contenu -->
      <div class="relative z-10 max-w-4xl w-full text-center space-y-6 p-8">
        <div class="inline-block text-xs px-4 py-1 border border-white/20 rounded-full bg-white/10 text-white">
          <span class="text-green-400">● </span>ESPACE DU PRÉFET
        </div>

        <h1 class="text-4xl md:text-6xl font-extrabold leading-tight">
          Bienvenue dans votre interface<br>
          <span
            class="inline-block bg-gradient-to-r from-green-600 to-emerald-500 px-2 py-1 rounded-md text-white">Préfet</span>
        </h1>

        <p class="text-lg text-gray-300 mt-2">
          Depuis cet espace, vous pouvez consulter les rapports détaillés des paiements <br> et la liste complète des
          élèves inscrits dans l'établissement.
        </p>

        <div class="mt-8 flex flex-col md:flex-row justify-center items-center gap-4">
          <button id="reportButton"
            class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-emerald-600 hover:to-green-500 text-white font-semibold py-3 px-6 rounded-full text-sm flex items-center gap-2">
            <i class="fas fa-chart-bar text-white"></i>
            <span>Voir rapports de paiements</span>
          </button>
          <button id="studentButton"
            class="bg-white text-black hover:bg-gray-200 transition font-semibold py-3 px-6 rounded-full flex items-center gap-2 text-sm">
            <i class="fas fa-users text-green-600"></i>
            <span>Rechercher un élève</span>
          </button>
        </div>

        <!-- Formulaire pour les rapports de paiements -->
        <div id="reportForm" class="hidden mt-8 bg-white/10 p-6 rounded-xl backdrop-blur-sm">
          <form id="reportSearchForm" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-center">
              <div class="w-full md:w-auto">
                <label for="reportMatricule" class="block text-sm font-medium text-white mb-1">Matricule</label>
                <input type="text" id="reportMatricule" name="reportMatricule" required
                  class="w-full px-4 py-2 border font-normal border-gray-300 rounded-lg text-black"
                  placeholder="Entrez le matricule">
              </div>
              <button type="submit"
                class="bg-gradient-to-r from-blue-600 to-blue-500 hover:from-blue-700 hover:to-blue-600 text-white font-medium py-2 px-6 rounded-lg transition duration-200 h-10 mt-5 md:mt-0">
                <i class="fas fa-search mr-2"></i> Chercher
              </button>
            </div>
          </form>
          <div id="paiementsResult" class="mt-6"></div>
        </div>

        <!-- Formulaire de recherche d'élève -->
        <div id="matriculeForm" class="hidden mt-8 bg-white/10 p-6 rounded-xl backdrop-blur-sm">
          <form id="studentSearchForm" class="space-y-4">
            <div class="flex flex-col md:flex-row gap-4 items-center justify-center">
              <div class="w-full md:w-auto">
                <label for="matricule" class="block text-sm font-medium text-white mb-1">Matricule</label>
                <input type="text" id="matricule" name="matricule" required
                  class="w-full px-4 py-2 border font-normal border-gray-300 rounded-lg text-black"
                  placeholder="Entrez le matricule">
              </div>
              <button type="submit"
                class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-emerald-600 hover:to-green-500 text-white font-medium py-2 px-6 rounded-lg transition duration-200 h-10 mt-5 md:mt-0">
                <i class="fas fa-search mr-2"></i> Rechercher
              </button>
            </div>
          </form>
          <div id="result" class="mt-6"></div>
        </div>

        <!-- Infos -->
        <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-8 text-sm text-gray-300">
          <div class="flex items-center justify-center space-x-2">
            <i class="fas fa-shield-alt text-green-400"></i>
            <span>Accès sécurisé</span>
          </div>
          <div class="flex items-center justify-center space-x-2">
            <i class="fas fa-database text-green-400"></i>
            <span>Données en temps réel</span>
          </div>
          <div class="flex items-center justify-center space-x-2">
            <i class="fas fa-check-double text-green-400"></i>
            <span>Suivi transparent</span>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Section explicative -->
  <section class="bg-white py-20 px-6">
    <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
      <!-- Texte -->
      <div>
        <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
          Suivi des paiements & inscriptions
        </h2>
        <p class="text-gray-600 text-lg mb-6">
          Cet espace est réservé au préfet pour la consultation des données relatives aux frais scolaires. Il permet :
        </p>
        <ul class="list-disc pl-5 space-y-3 text-gray-700 text-base">
          <li>La lecture des rapports de paiement par élève ou par classe.</li>
          <li>Le suivi global des transactions et alertes de retard.</li>
          <li>L'accès à la liste actualisée des élèves inscrits.</li>
          <li>Un affichage clair pour faciliter les décisions de gestion.</li>
        </ul>
      </div>

      <!-- Image -->
      <div class="flex justify-center">
        <img src="../images/Eleve.png" alt="Suivi des paiements" class="rounded-lg">
      </div>
    </div>
  </section>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      // Références aux éléments
      const reportButton = document.getElementById('reportButton');
      const studentButton = document.getElementById('studentButton');
      const reportForm = document.getElementById('reportForm');
      const studentForm = document.getElementById('matriculeForm');
      const reportSearchForm = document.getElementById('reportSearchForm');
      const studentSearchForm = document.getElementById('studentSearchForm');

      // Initialisation - cacher les formulaires
      reportForm.classList.add('hidden');
      studentForm.classList.add('hidden');

      // Gestion du bouton Rapport
      reportButton.addEventListener('click', function (e) {
        e.preventDefault();
        reportForm.classList.toggle('hidden');
        if (!studentForm.classList.contains('hidden')) {
          studentForm.classList.add('hidden');
        }
        document.getElementById('result').innerHTML = '';
      });

      // Gestion du bouton Élève
      studentButton.addEventListener('click', function (e) {
        e.preventDefault();
        studentForm.classList.toggle('hidden');
        if (!reportForm.classList.contains('hidden')) {
          reportForm.classList.add('hidden');
        }
        document.getElementById('paiementsResult').innerHTML = '';
      });

      // Soumission du formulaire de rapport
      reportSearchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const matricule = document.getElementById('reportMatricule').value;
        fetchPaiements(matricule);
      });

      // Soumission du formulaire élève
      studentSearchForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const matricule = document.getElementById('matricule').value;
        fetchEleve(matricule);
      });
    });

    async function fetchPaiements(matricule) {
      const resultDiv = document.getElementById('paiementsResult');
      resultDiv.innerHTML = `
        <div class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-blue-500"></div>
          <span class="ml-3 text-white">Chargement des paiements...</span>
        </div>
      `;

      try {
        const formData = new FormData();
        formData.append('matricule', matricule);
        formData.append('action', 'get_paiements');

        const response = await fetch(window.location.href, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success && data.paiements && data.paiements.length > 0) {
          resultDiv.innerHTML = createPaiementsTable(data.paiements);

          // Ajouter le bouton PDF
          resultDiv.innerHTML += `
            <div class="mt-4 flex justify-end">
              <button onclick="exportToPDF('${matricule}')"
                      class="bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg flex items-center gap-2">
                <i class="fas fa-file-pdf mr-2"></i> Exporter en PDF
              </button>
            </div>
          `;
        } else {
          resultDiv.innerHTML = `
            <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-lg">
              <div class="flex items-center">
                <i class="fas fa-exclamation-triangle text-yellow-500 mr-3"></i>
                <p class="text-yellow-700">${data.message || "Aucun paiement trouvé pour ce matricule"}</p>
              </div>
            </div>
          `;
        }
      } catch (error) {
        resultDiv.innerHTML = `
          <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center">
              <i class="fas fa-times-circle text-red-500 mr-3"></i>
              <p class="text-red-700">Erreur lors de la récupération des données</p>
            </div>
          </div>
        `;
        console.error("Erreur:", error);
      }
    }

    async function fetchEleve(matricule) {
      const resultDiv = document.getElementById('result');
      resultDiv.innerHTML = `
        <div class="flex justify-center items-center py-8">
          <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-b-2 border-green-500"></div>
          <span class="ml-3 text-white">Recherche en cours...</span>
        </div>
      `;

      try {
        const formData = new FormData();
        formData.append('matricule', matricule);

        const response = await fetch(window.location.href, {
          method: 'POST',
          body: formData
        });

        const data = await response.json();

        if (data.success) {
          const eleve = data.eleve;
          resultDiv.innerHTML = createStudentTable(eleve);
        } else {
          resultDiv.innerHTML = `
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
              <div class="flex items-center">
                <i class="fas fa-exclamation-circle text-red-500 mr-3"></i>
                <p class="text-red-700">${data.message || "Erreur lors de la recherche"}</p>
              </div>
            </div>
          `;
        }
      } catch (error) {
        resultDiv.innerHTML = `
          <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-lg">
            <div class="flex items-center">
              <i class="fas fa-times-circle text-red-500 mr-3"></i>
              <p class="text-red-700">Erreur de connexion au serveur</p>
            </div>
          </div>
        `;
        console.error("Erreur:", error);
      }
    }



    function createPaiementsTable(paiements) {
      const total = paiements.reduce((sum, p) => {
        return sum + parseFloat(p.montant_payer.replace(/[^0-9.]/g, ''));
      }, 0);

      return `
    <div class="bg-white rounded-xl shadow-lg overflow-hidden">
      <div class="px-6 py-4 bg-gradient-to-r from-blue-600 to-blue-500">
        <div class="flex justify-between items-center">
          <h3 class="text-lg font-semibold text-white">Historique des paiements</h3>
          <span class="bg-white text-blue-600 px-3 py-1 rounded-full text-sm font-medium">
            Total: ${total.toFixed(2)} ${paiements[0]?.devise || ''}
          </span>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
          <thead class="bg-gray-50">
            <tr>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Post-Nom</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sexe</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Motif</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
              <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
              ${paiements.map(p => `
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.nom_eleve}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.postnom_eleve}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.prenom_eleve}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.sexe_eleve === 'M' ? 'Masculin' : 'Féminin'}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.date_paiement}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.montant_payer}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.motif_paiement}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                    ${p.classe_eleve}
                  </td>
                  <td class="px-6 py-4 whitespace-nowrap">
                    <span class="px-2 py-1 text-xs rounded-full ${p.payment_status === 'success'
                      ? 'bg-green-100 text-green-800'
                      : 'bg-red-100 text-red-800'
                    }">
                      ${p.payment_status === 'success' ? 'Payé' : 'Échoué'}
                    </span>
                  </td>
                </tr>
              `).join('')}
            </tbody>
        </table>
      </div>
    </div>
  `;
    }

    function createStudentTable(eleve) {
      return `
        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
          <div class="px-6 py-4 bg-gradient-to-r from-green-600 to-emerald-500">
            <h3 class="text-lg font-semibold text-white">Informations de l'élève</h3>
          </div>
          <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
              <thead class="bg-gray-50">
                <tr>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Matricule</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Nom</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Postnom</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Prénom</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Sexe</th>
                  <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Classe</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                  <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${eleve.matricule}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${eleve.nom_eleve}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${eleve.postnom_eleve}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${eleve.prenom_eleve}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${eleve.sexe_eleve === 'M' ? 'Masculin' : 'Féminin'}</td>
                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">${eleve.classe_selection}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      `;
    }

    function exportToPDF(matricule) {
      alert("Export PDF pour le matricule: " + matricule + "\nCette fonctionnalité sera implémentée prochainement.");
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