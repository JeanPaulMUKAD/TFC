<?php

require_once '../Controllers/AuthController.php';
$auth = new AuthController();
$messageErreur = null;


$loggedInParentName = $_SESSION['username'] ?? '';

// --- Bloc UNIQUE pour toutes les requêtes POST (y compris AJAX) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    $resultat = ['success' => false, 'message' => 'Action non traitée.']; // Valeur par défaut

    // Déterminez l'action demandée
    $action = $_POST['action'] ?? '';

    error_log("Requête POST reçue dans Acceuil_Parent.php. Action: " . $action);
    error_log("POST data: " . print_r($_POST, true));

    // --- Cas de l'action 'get_paiements' (pour matricule, si toujours utilisée) ---
    // (Conservez ce bloc si vous utilisez encore cette action, sinon, vous pouvez l'enlever)
    if ($action === 'get_paiements') {
        if (isset($_POST['matricule'])) {
            $matricule = htmlspecialchars(trim($_POST['matricule']));
            // Assurez-vous que AuthController a bien une méthode obtenirPaiementsParMatricule si vous l'utilisez
            $resultat = $auth->obtenirPaiementsParMatricule($matricule);
            error_log("Résultat de obtenirPaiementsParMatricule: " . json_encode($resultat));
        } else {
            $resultat = ['success' => false, 'message' => 'Matricule manquant pour get_paiements.'];
            error_log("Erreur: Matricule manquant.");
        }
    }
    // --- Cas de l'action 'get_paiements_by_parent' (pour le parent connecté) ---
    elseif ($action === 'get_paiements_by_parent') {
        // C'EST LA LIGNE À MODIFIER ICI :
        if (isset($_POST['parent_name'])) { // OK, la clé attendue est 'parent_name'
            $parentNameFromAjax = htmlspecialchars(trim($_POST['parent_name'])); // <-- LISEZ BIEN 'parent_name' ici !

            error_log("parentNameFromAjax (via POST): " . $parentNameFromAjax);
            error_log("loggedInParentName (via session): " . $loggedInParentName);

            // Vérification de sécurité : le nom envoyé par JS doit correspondre au nom de la session
            if ($parentNameFromAjax !== $loggedInParentName || empty($loggedInParentName)) {
                $resultat = ['success' => false, 'message' => 'Accès non autorisé ou parent non identifié.'];
                error_log("Accès non autorisé ou non-concordance des noms de parent: " . json_encode($resultat));
            } else {
                $resultat = $auth->obtenirPaiementsParNomParent($parentNameFromAjax);
                error_log("Résultat final de obtenirPaiementsParNomParent: " . json_encode($resultat));
            }
        } else {
            $resultat = ['success' => false, 'message' => 'Clé "parent_name" manquante dans la requête POST.'];
            error_log("Erreur: Clé 'parent_name' manquante.");
        }
    }
    // --- Si l'action n'est pas reconnue ---
    else {
        $resultat = ['success' => false, 'message' => 'Action POST non reconnue.'];
        error_log("Action POST non reconnue: " . json_encode($resultat));
    }

    echo json_encode($resultat);
    exit; // Très important : arrête le script ici après avoir envoyé la réponse JSON
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
                    paiementsTableBody.innerHTML = `<tr><td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-center">Le nom du parent n'est pas disponible. Veuillez vous assurer d'être connecté.</td></tr>`;
                    return;
                }

                paiementsTableBody.innerHTML = `<tr><td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Chargement de l'historique des paiements pour ${htmlspecialchars(parentName)}...</td></tr>`;

                try {
                    const response = await fetch(window.location.href, {
                        method: 'POST', // Assurez-vous que la méthode est POST
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        // ICI : Utilisez 'parent_name' comme clé, pas 'nom_parent' car c'est ce que PHP attend maintenant
                        body: `action=get_paiements_by_parent&parent_name=${encodeURIComponent(parentName)}`
                    });

                    const data = await response.json();

                    if (data.success && data.paiements && data.paiements.length > 0) {
                        let rows = '';
                        data.paiements.forEach(paiement => {
                            // Assurez-vous que les valeurs sont nettoyées si elles contiennent du texte (devise)
                            const montantPayeNumeric = parseFloat(String(paiement.montant_payer).replace(/[^0-9.,]/g, '').replace(',', '.'));
                            const totalAnnuelNumeric = parseFloat(String(paiement.total_annuel).replace(/[^0-9.,]/g, '').replace(',', '.')) || 0;

                            const montantRestant = totalAnnuelNumeric - montantPayeNumeric;

                            let statusClass = '';
                            let statusText = '';
                            let paymentButton = ''; // Nouvelle variable pour le bouton

                            if (montantRestant <= 0) {
                                statusClass = 'bg-green-100 text-green-800';
                                statusText = 'Payé';
                            } else {
                                statusClass = 'bg-red-100 text-red-800';
                                statusText = `Reste : ${montantRestant.toLocaleString('fr-FR')} Frc`;

                                // Créer le bouton de paiement si un montant est dû
                                // Nous allons passer toutes les infos nécessaires via l'URL
                                const paymentUrl = `../Parent/PaiementParent.php?` +
                                    `matricule=${encodeURIComponent(paiement.matricule || '')}&` +
                                    `nom_eleve=${encodeURIComponent(paiement.nom_eleve || '')}&` +
                                    `postnom_eleve=${encodeURIComponent(paiement.postnom_eleve || '')}&` +
                                    `prenom_eleve=${encodeURIComponent(paiement.prenom_eleve || '')}&` +
                                    `sexe_eleve=${encodeURIComponent(paiement.sexe_eleve || '')}&` +
                                    `classe_eleve=${encodeURIComponent(paiement.classe_eleve || '')}&` +
                                    `nom_parent=${encodeURIComponent(loggedInParentName || '')}&` + // Utilisez le nom du parent connecté
                                    `montant_du=${encodeURIComponent(montantRestant.toFixed(2))}`; // Montant restant dû

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
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.montant_payer)}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.motif_paiement)}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">${htmlspecialchars(paiement.date_paiement)}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full ${statusClass}">
                                                ${statusText}
                                            </span>
                                            ${paymentButton} </td>
                                    </tr>
                                    `;
                        });
                        paiementsTableBody.innerHTML = rows;
                    } else {
                        paiementsTableBody.innerHTML = `<tr><td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 text-center">${data.message || "Aucun historique de paiement trouvé pour les enfants de ce parent."}</td></tr>`;
                    }
                } catch (error) {
                    console.error('Erreur lors de la récupération des paiements:', error);
                    paiementsTableBody.innerHTML = `<tr><td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-center">Une erreur est survenue lors du chargement de l'historique des paiements.</td></tr>`;
                }
            }

            if (loggedInParentName) {
                fetchPaiementsForParent(loggedInParentName);
            } else {
                paiementsTableBody.innerHTML = `<tr><td colspan="8" class="px-6 py-4 whitespace-nowrap text-sm text-red-500 text-center">Le nom du parent n'est pas disponible. Veuillez vous assurer d'être connecté.</td></tr>`;
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