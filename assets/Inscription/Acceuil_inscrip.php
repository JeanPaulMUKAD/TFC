 <?php
    require_once '../Controllers/AuthController.php';
    $auth = new AuthController();
    $eleveData = null;
    $messageErreur = null;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['matricule'])) {
        header('Content-Type: application/json');
        
        $matricule = htmlspecialchars(trim($_POST['matricule']));
        $resultat = $auth->obtenirInfosEleveParMatricule($matricule);
        
        echo json_encode($resultat);
        exit;
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


    <section class="bg-white py-20 px-6">
        <div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 gap-12 items-center">

            <!-- Contenu texte -->
            <div>
                <h2 class="text-4xl font-extrabold text-gray-900 mb-4">
                    Inscription d’un élève
                </h2>
                <p class="text-gray-600 text-lg mb-6">
                    Grâce à notre plateforme, l’inscription d’un élève devient simple, rapide et entièrement numérique.
                    Le secrétaire académique peut :
                </p>
                <ul class="list-disc pl-5 space-y-3 text-gray-700 text-base">
                    <li>Enregistrer un nouvel élève avec son nom, sexe et classe.</li>
                    <li>Générer automatiquement un matricule unique.</li>
                    <li>Modifier ou supprimer une inscription en un clic.</li>
                    <li>Garder un historique complet des inscriptions par année scolaire.</li>
                </ul>

                <div class="mt-8 text-left">
                    <button onclick="toggleForm()"
                        class="inline-block bg-gradient-to-r from-red-600 to-orange-500 text-white font-semibold py-3 px-6 rounded-full hover:bg-orange-600 transition">
                        Consulter les informations de l'élève
                    </button>

                    <div id="matriculeForm" class="hidden">
                        <form onsubmit="fetchEleve(event)" class="space-y-4">
                            <div>
                                <label for="matricule"
                                    class="block text-sm font-medium text-gray-700 mb-1">Matricule</label>
                                <input type="text" id="matricule" name="matricule" required
                                    class="w-full px-4 py-2 border font-normal border-black rounded-lg text-black">
                            </div>
                            <button type="submit"
                                class="inline-block bg-gradient-to-r from-red-600 to-orange-500 hover:bg-orange-600 text-white font-medium py-2 px-4 rounded-lg transition duration-200">
                                Rechercher
                            </button>
                        </form>
                    </div>

                    <!-- Zone d’affichage du résultat -->
                    <div id="result" class="mt-6"></div>
                </div>


            </div>

            <!-- Image illustrative -->
            <div class="flex justify-center">
                <img src="../images/Eleve.png" alt="Inscription élève" class="rounded-lg ">
            </div>
        </div>
    </section>

    <script>
        function toggleForm() {
            const form = document.getElementById('matriculeForm');
            form.classList.toggle('hidden');
        }

        async function fetchEleve(e) {
            e.preventDefault();
            const matricule = document.getElementById('matricule').value;
            const resultDiv = document.getElementById('result');

            resultDiv.innerHTML = "<p class='text-gray-500'>Recherche en cours...</p>";

            try {
                // Création des données à envoyer
                const formData = new FormData();
                formData.append('matricule', matricule);

                // Envoi de la requête POST
                const response = await fetch(window.location.href, {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    const eleve = data.eleve;

                    resultDiv.innerHTML = `
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
                    resultDiv.innerHTML = `<p class="text-red-500 font-semibold">${data.message}</p>`;
                }

            } catch (error) {
                resultDiv.innerHTML = `<p class="text-red-500 font-semibold">Erreur lors de la requête: ${error.message}</p>`;
            }

            return false;
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