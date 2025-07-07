<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Espace Préfet | C.S.P.P.UNILU</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700;900&display=swap');
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
    <div class="relative mx-auto max-w-14xl rounded-3xl overflow-hidden bg-gray-900 text-white min-h-[90vh] flex items-center justify-center shadow-2xl">

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
          <span class="inline-block bg-gradient-to-r from-green-600 to-emerald-500 px-2 py-1 rounded-md text-white">Préfet</span>
        </h1>

        <p class="text-lg text-gray-300 mt-2">
          Depuis cet espace, vous pouvez consulter les rapports détaillés des paiements <br> et la liste complète des élèves inscrits dans l’établissement.
        </p>

        <div class="mt-8 flex flex-col md:flex-row justify-center items-center gap-4">
          <a href="#"
            class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-emerald-600 hover:to-green-500 text-white font-semibold py-3 px-6 rounded-full text-sm flex items-center gap-2">
            <i class="fas fa-chart-bar text-white"></i>
            <span>Voir rapports de paiements</span>
          </a>
          <a href="#"
            class="bg-white text-black hover:bg-gray-200 transition font-semibold py-3 px-6 rounded-full flex items-center gap-2 text-sm">
            <i class="fas fa-users text-green-600"></i>
            <span>Liste des élèves inscrits</span>
          </a>
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
          <li>L’accès à la liste actualisée des élèves inscrits.</li>
          <li>Un affichage clair pour faciliter les décisions de gestion.</li>
        </ul>

        <div class="mt-8">
          <a href="#"
            class="inline-block bg-gradient-to-r from-green-600 to-emerald-500 text-white font-semibold py-3 px-6 rounded-full hover:from-emerald-600 hover:to-green-600 transition">
            Accéder aux rapports
          </a>
        </div>
      </div>

      <!-- Image -->
      <div class="flex justify-center">
        <img src="../images/Eleve.png" alt="Suivi des paiements" class="rounded-lg ">
      </div>
    </div>
  </section>

</body>

</html>
