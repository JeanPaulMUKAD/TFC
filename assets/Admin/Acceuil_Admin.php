<?php
require_once __DIR__ . '/../Controllers/AuthController.php';
$auth = new AuthController();

// Initialisation de $_SESSION['username'] si elle n'est pas définie (pour éviter les erreurs en l'absence de session)
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Admin'; // Valeur par défaut
}

// Nombre d'utilisateurs inscrits ce mois
$userCount = 0;
// Utilisation de la locale fr_FR pour le formatage du mois
$formatter = new IntlDateFormatter('fr_FR', IntlDateFormatter::LONG, IntlDateFormatter::NONE, 'Europe/Paris', IntlDateFormatter::GREGORIAN, 'LLLL yyyy');
$mois = ucfirst($formatter->format(new DateTime()));

$sql = "SELECT COUNT(*) as total FROM utilisateurs WHERE MONTH(created_at) = MONTH(CURRENT_DATE()) AND YEAR(created_at) = YEAR(CURRENT_DATE())";
$result = $auth->conn->query($sql);
if ($result && $row = $result->fetch_assoc()) {
    $userCount = (int) $row['total'];
}

// Nombre total de types de paiement configurés
$paymentCount = 0;
$sql2 = "SELECT COUNT(*) as total FROM payementtype";
$result2 = $auth->conn->query($sql2);
if ($result2 && $row2 = $result2->fetch_assoc()) {
    $paymentCount = (int) $row2['total'];
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Dashboard Admin | C.S.P.P.UNILU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/assets/images/logo_pp.png">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

        /* Styles pour la barre de défilement pour un look plus moderne */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #555;
        }

        /* Animation d'entrée pour les cartes de statistiques */
        .card-enter {
            animation: fadeInScale 0.5s ease-out forwards;
            opacity: 0;
            transform: scale(0.95);
        }

        @keyframes fadeInScale {
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
    </style>
</head>

<body class="bg-gray-100 text-gray-900 flex">

    <aside class="fixed top-0 left-0 h-full w-64 bg-gray-900 text-white shadow-lg z-50 flex flex-col">
        <div class="p-6 border-b border-gray-700">
            <div class="text-2xl font-extrabold flex items-center">
                <img src="/assets/images/logo_pp.png" alt="Logo" class="h-10 w-10 mr-2">
                <span class="text-white">C.S.P.P</span><span class="text-green-400">.UNILU</span>
            </div>
            <p class="text-sm text-center text-gray-400 mt-2">Administration</p>
        </div>
        <nav class="flex-grow mt-8 space-y-2 px-4">
            <a href="#" class="flex items-center space-x-3 p-3 rounded-lg text-white bg-gray-800 hover:bg-gray-700 transition duration-200">
                <i class="fas fa-tachometer-alt text-lg"></i>
                <span class="font-medium">Tableau de bord</span>
            </a>
            <a href="Account_User.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200">
                <i class="fas fa-users text-lg"></i>
                <span class="font-medium">Gérer utilisateurs</span>
            </a>
            <a href="Payment_Type.php" class="flex items-center space-x-3 p-3 rounded-lg text-gray-300 hover:bg-gray-700 hover:text-white transition duration-200">
                <i class="fas fa-cogs text-lg"></i>
                <span class="font-medium">Types de paiement</span>
            </a>
            
        </nav>
        <div class="p-6 border-t border-gray-700 mt-auto">
            <a href="/logoutParent" class="flex items-center space-x-3 p-3 rounded-lg text-red-400 hover:bg-gray-700 hover:text-red-300 transition duration-200">
                <i class="fas fa-sign-out-alt text-lg"></i>
                <span class="font-medium">Se déconnecter</span>
            </a>
        </div>
    </aside>

    <div class="ml-64 flex-1 min-h-screen p-8">
        <header class="flex items-center justify-between mb-10 bg-white p-6 rounded-xl shadow-md">
            <h1 class="text-4xl font-extrabold text-gray-800">Bienvenue, <span class="text-green-600"><?php echo htmlspecialchars($_SESSION['username']); ?></span></h1>
            <span class="bg-green-100 text-green-700 px-4 py-2 rounded-full text-sm font-semibold flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i>
                <span>Espace Administrateur</span>
            </span>
        </header>

        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Aperçu des statistiques</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition duration-300 card-enter" style="animation-delay: 0.1s;">
                    <div class="flex items-center justify-between">
                        <div class="bg-green-100 text-green-600 p-4 rounded-full">
                            <i class="fas fa-users text-3xl"></i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-sm text-gray-500 font-medium">Utilisateurs inscrits ce mois</h3>
                            <p class="text-4xl font-bold text-gray-800 mt-1"><?= $userCount ?></p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-4 text-center">En <strong class="text-gray-700"><?= $mois ?></strong></p>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition duration-300 card-enter" style="animation-delay: 0.2s;">
                    <div class="flex items-center justify-between">
                        <div class="bg-emerald-100 text-emerald-600 p-4 rounded-full">
                            <i class="fas fa-credit-card text-3xl"></i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-sm text-gray-500 font-medium">Types de paiement configurés</h3>
                            <p class="text-4xl font-bold text-gray-800 mt-1"><?= $paymentCount ?></p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-4 text-center">Total des options de paiement</p>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition duration-300 card-enter" style="animation-delay: 0.3s;">
                    <div class="flex items-center justify-between">
                        <div class="bg-blue-100 text-blue-600 p-4 rounded-full">
                            <i class="fas fa-chart-line text-3xl"></i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-sm text-gray-500 font-medium">Suivi des opérations</h3>
                            <p class="text-4xl font-bold text-gray-800 mt-1">Actif</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-4 text-center">Transparence et efficacité</p>
                </div>

                <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-xl transition duration-300 card-enter" style="animation-delay: 0.4s;">
                    <div class="flex items-center justify-between">
                        <div class="bg-yellow-100 text-yellow-600 p-4 rounded-full">
                            <i class="fas fa-hourglass-half text-3xl"></i>
                        </div>
                        <div class="text-right">
                            <h3 class="text-sm text-gray-500 font-medium">Opérations en attente</h3>
                            <p class="text-4xl font-bold text-gray-800 mt-1">5</p>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 mt-4 text-center">Nécessitent votre attention</p>
                </div>
            </div>
        </section>

        <section class="mb-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Actions rapides</h2>
            <div class="flex flex-wrap gap-4">
                <a href="Account_User.php"
                    class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white py-3 px-6 rounded-full text-base font-semibold flex items-center gap-3 shadow-lg transform hover:scale-105 transition duration-200">
                    <i class="fas fa-user-plus text-xl"></i>
                    Gérer les utilisateurs
                </a>

                <a href="Payment_Type.php"
                    class="bg-white text-green-700 border border-green-500 hover:bg-green-50 py-3 px-6 rounded-full text-base font-semibold flex items-center gap-3 shadow-lg transform hover:scale-105 transition duration-200">
                    <i class="fas fa-cogs text-xl"></i>
                    Configurer les types de paiement
                </a>


            </div>
        </section>

        <section class="mt-12">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Informations système</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 text-sm text-gray-700 bg-white p-6 rounded-xl shadow-md">
                <div class="flex items-center gap-3">
                    <i class="fas fa-lock text-green-500 text-xl"></i>
                    <div>
                        <h3 class="font-semibold">Connexion sécurisée</h3>
                        <p class="text-gray-600">Votre session est protégée par un protocole sécurisé.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-database text-green-500 text-xl"></i>
                    <div>
                        <h3 class="font-semibold">Données en temps réel</h3>
                        <p class="text-gray-600">Les informations affichées sont à jour.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-cube text-green-500 text-xl"></i>
                    <div>
                        <h3 class="font-semibold">Gestion centralisée</h3>
                        <p class="text-gray-600">Accédez à toutes les fonctionnalités depuis ce tableau de bord.</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <i class="fas fa-headset text-green-500 text-xl"></i>
                    <div>
                        <h3 class="font-semibold">Support technique</h3>
                        <p class="text-gray-600">Disponible 24/7 pour toute assistance.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let masque = document.createElement('div');
            let logo = document.createElement('img');
            let cercle = document.createElement('div');

            let angle = 0;
            let scale = 1;
            let opacityLogo = 1;

            // Création du masque
            masque.style.width = '100%';
            masque.style.height = '100vh';
            masque.style.zIndex = 100000;
            masque.style.background = '#ffffff';
            masque.style.position = 'fixed';
            masque.style.top = '0';
            masque.style.left = '0';
            masque.style.opacity = '1';
            masque.style.transition = 'opacity 0.5s ease, visibility 0s linear 0.5s'; // Amélioration de la transition
            masque.style.display = 'flex';
            masque.style.justifyContent = 'center';
            masque.style.alignItems = 'center';
            document.body.appendChild(masque);

            // Création du logo
            logo.setAttribute('src', '/assets/images/logo_pp.png'); // Chemin ajusté si nécessaire
            logo.style.width = '10vh';
            logo.style.height = '10vh';
            logo.style.position = 'relative';
            logo.style.zIndex = '2';
            logo.style.transition = 'transform 0.2s, opacity 0.2s';
            masque.appendChild(logo);

            // Création du cercle autour du logo
            cercle.style.width = '15vh';
            cercle.style.height = '15vh';
            cercle.style.border = '3px solid #0ab39c'; /* Vert */
            cercle.style.borderTop = '3px solid #405189'; /* Bleu foncé */
            cercle.style.borderRadius = '50%';
            cercle.style.position = 'absolute';
            cercle.style.top = '50%';
            cercle.style.left = '50%';
            cercle.style.transform = 'translate(-50%, -50%)';
            cercle.style.boxSizing = 'border-box';
            cercle.style.zIndex = '1';
            masque.appendChild(cercle);

            let anime;

            // Démarrage de l'animation au chargement complet de la page
            anime = setInterval(() => {
                angle += 10;
                cercle.style.transform = `translate(-50%, -50%) rotate(${angle}deg)`;

                scale += 0.005;
                opacityLogo -= 0.005;

                logo.style.transform = `scale(${scale})`;
                logo.style.opacity = opacityLogo;

                if (opacityLogo <= 0) { // Arrêter l'animation une fois que le logo est invisible
                    clearInterval(anime);
                }
            }, 20);

            // Masquer le loader après un certain délai
            setTimeout(() => {
                masque.style.opacity = '0';
                masque.style.visibility = 'hidden';
            }, 1000); // 1 seconde pour l'animation + 0.5s pour la transition CSS = 1.5s total pour disparaître
        });
    </script>

</body>

</html>