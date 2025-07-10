<?php
    require_once '../Controllers/AuthController.php';
    $auth = new AuthController();
    $messageErreur = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        header('Content-Type: application/json');
        $matricule = htmlspecialchars(trim($_POST['matricule']));

        if (isset($_POST['action']) && $_POST['action'] === 'get_paiements') {
            $resultat = $auth->obtenirPaiementsParMatricule($matricule);
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
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');

        body {
            font-family: 'Poppins', sans-serif;
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
            <a href="#"
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
                    <a href="../Parent/PaiementParent.php"
                        class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-indigo-600 hover:to-blue-500 text-white font-semibold py-3 px-6 rounded-full text-sm flex items-center gap-2">
                        <i class="fas fa-money-bill-wave text-white"></i>
                        <span>Payer les frais</span>
                    </a>
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
    <section class="bg-white py-20 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <!-- Texte -->
            <div>
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                    Paiement des frais scolaires
                </h2>
                <p class="text-gray-600 text-lg mb-6">
                    Vous pouvez régler en ligne les frais de votre enfant en toute sécurité. Ce portail vous permet
                    également de :
                </p>
                <ul class="list-disc pl-5 space-y-3 text-gray-700 text-base">
                    <li>Accéder au statut de paiement en temps réel.</li>
                    <li>Visualiser et télécharger les reçus précédents.</li>
                    <li>Consulter les échéances futures des paiements.</li>
                    <li>Recevoir des notifications par email ou SMS.</li>
                </ul>

                <div class="mt-8">
                    <button id="reportButton"class="inline-block bg-gradient-to-r from-blue-600 to-indigo-500 text-white font-semibold py-3 px-6 rounded-full hover:from-indigo-600 hover:to-blue-600 transition">
                        <i class="fas fa-chart-bar text-white"></i>
                        <span>Voir rapports de paiements</span>
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
            </div>

            

            <!-- Image -->
            <div class="flex justify-center">
                <img src="../images/Eleve.png" alt="Paiement élève" class="rounded-lg ">
            </div>
        </div>
    </section>

    <script>
        // Afficher/Masquer le formulaire des rapports
        document.getElementById('reportButton').addEventListener('click', function () {
            const form = document.getElementById('reportForm');
            form.classList.toggle('hidden');
        });

        // Traitement du formulaire des rapports de paiements
        document.getElementById('reportSearchForm').addEventListener('submit', async function (e) {
            e.preventDefault();
            const matricule = document.getElementById('reportMatricule').value;
            const resultDiv = document.getElementById('paiementsResult');

            resultDiv.innerHTML = `<p class="text-gray-500">Recherche en cours...</p>`;

            try {
                const response = await fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `matricule=${encodeURIComponent(matricule)}&action=get_paiements`
                });

                const data = await response.json();

                if (data.success) {
                    let rows = '';
                    data.paiements.forEach(paiement => {
                        rows += `
                            <tr class="border-b">
                                <td class="px-4 py-2">${paiement.nom_eleve}</td>
                                <td class="px-4 py-2">${paiement.postnom_eleve}</td>
                                <td class="px-4 py-2">${paiement.prenom_eleve}</td>
                                <td class="px-4 py-2">${paiement.sexe_eleve === 'M' ? 'Masculin' : 'Féminin'}</td>
                                <td class="px-4 py-2">${paiement.date_paiement}</td>
                                <td class="px-4 py-2">${paiement.montant_payer}</td>
                                <td class="px-4 py-2">${paiement.motif_paiement}</td>
                                <td class="px-4 py-2">${paiement.classe_eleve}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 text-xs rounded-full ${paiement.payment_status === 'success'
                                    ? 'bg-green-100 text-green-800'
                                    : 'bg-red-100 text-red-800'
                                    }">
                                    ${paiement.payment_status === 'success' ? 'Payé' : 'Échoué'}
                                    </span>
                                </td>
                            </tr>
                        `;
                    });

                    resultDiv.innerHTML = `
                        <table class="min-w-full text-sm text-left border border-gray-300 rounded-lg mt-4">
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
                            <tbody class="bg-white text-black">
                                ${rows}
                            </tbody>
                        </table>
                    `;
                } else {
                    resultDiv.innerHTML = `<p class="text-red-500 font-semibold">${data.message}</p>`;
                }
            } catch (error) {
                resultDiv.innerHTML = `<p class="text-red-500 font-semibold">Erreur lors de la requête.</p>`;
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