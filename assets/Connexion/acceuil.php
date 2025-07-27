<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>C.S.P.P UNILU | la meilleure</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Ajout de Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <link rel="shortcut icon" href="{{ asset('logo.jpeg') }}" type="image/x-icon">
    <style>
        .custom-dropdown::after {
            content: "▼";
            font-size: 0.6rem;
            margin-left: 0.2rem;
            vertical-align: middle;
        }
    </style>
</head>

<body>

    <nav class="bg-white shadow-sm py-4 px-4 sm:px-6 fixed top-0 w-full z-50 transition duration-300">
        <div class="max-w-7xl mx-auto flex justify-between items-center flex-wrap">

            <!-- Logo + Titre -->
            <div class="flex items-center space-x-3 mb-3 md:mb-0">
                <img src="./assets/images/logo_pp.png" alt="Logo C.SP.P. UNILU" class="h-14 w-14 ">

            </div>

            <!-- Boutons d'actions -->
            <div class="flex items-center space-x-4">
                

                <!-- Bouton Se Connecter -->
                <a href="./assets/Connexion/auth-signin-cover.php"
                    class="px-4 py-2 bg-green-800 text-white rounded-md hover:bg-green-700 transition-colors">
                    Se connecter
                </a>

                <!-- Hamburger Menu (mobile) -->
                <button id="nav-toggle" class="md:hidden text-blue-900 focus:outline-none">
                    <i class="fas fa-bars text-2xl"></i>
                </button>
            </div>
        </div>
    </nav>

    <section class="relative h-[600px] bg-gray-900 text-white mt-16">
        <!-- Video de fond en boucle -->
        <div class="absolute inset-0 overflow-hidden">
            <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=1350&q=80"
                alt="Réfugiés birmans en Thaïlande" class="w-full h-full object-cover filter brightness-75 blur-sm">
            <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent"></div>
        </div>

        <!-- Contenu texte -->
        <div class="relative z-10 flex items-center h-full">
            <div class="max-w-7xl mx-auto w-full px-6 md:px-12">
                <div class="max-w-3xl">
                    <h2 class="text-3xl md:text-4xl font-bold mb-4">Médiathèque Académique C.SP.P. UNILU</h2>
                    <div class="border-t border-green-400 my-4 w-24"></div>
                    <h3 class="text-lg text-green-400 font-semibold mb-3">Savoir • Excellence • Leadership</h3>
                    <p class="text-xl font-semibold mb-4">
                        Centre de ressources multimédia pour l'enseignement et la recherche universitaire.
                    </p>
                    <p class="text-gray-200 mb-6">
                        Accédez à nos conférences académiques, cours enregistrés et documentaires pédagogiques
                        produits par notre institution.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#mediatheque"
                            class="inline-flex items-center px-6 py-3 bg-green-500 text-gray-900 font-bold rounded-md hover:bg-green-400 transition-colors">
                            Accéder à la médiathèque
                        </a>
                        <a href="login.html"
                            class="inline-flex items-center px-6 py-3 border border-green-500 text-green-400 font-bold rounded-md hover:bg-yellow-500/10 transition-colors">
                            Espace enseignant
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Gestion du menu mobile
        document.getElementById('nav-toggle').addEventListener('click', function () {
            document.getElementById('nav-menu').classList.toggle('hidden');
        });
    </script>
</body>

</html>