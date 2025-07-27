<?php
require_once __DIR__ . '/../Controllers/AuthController.php';
$auth = new AuthController();

// Nombre d'utilisateurs inscrits ce mois
$userCount = 0;
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
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
    }
  </style>
</head>

<body class="bg-gray-100 text-gray-900">

  <!-- Sidebar -->
  <aside class="fixed top-0 left-0 h-full w-64 bg-gray-900 text-white shadow-lg z-50">
    <div class="p-6">
      <div class="text-2xl font-bold">
        <span class="text-white">C.S.P.P</span><span class="text-green-400">.UNILU</span>
      </div>
    </div>
    <nav class="mt-10 space-y-4 px-4">
      <a href="#" class="flex items-center space-x-3 text-white hover:text-green-400 transition">
        <i class="fas fa-tachometer-alt"></i>
        <span>Tableau de bord</span>
      </a>
      <a href="Account_User.php" class="flex items-center space-x-3 text-white hover:text-green-400 transition">
        <i class="fas fa-users"></i>
        <span>Gérer utilisateurs</span>
      </a>
      <a href="Payment_Type.php" class="flex items-center space-x-3 text-white hover:text-green-400 transition">
        <i class="fas fa-cogs"></i>
        <span>Types de paiement</span>
      </a>
      <a href="#" class="flex items-center space-x-3 text-white hover:text-red-400 transition mt-8">
        <i class="fas fa-sign-out-alt"></i>
        <span>Se déconnecter</span>
      </a>
    </nav>
  </aside>

  <!-- Main content -->
  <div class="ml-64 min-h-screen p-8">
    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
      <h1 class="text-3xl font-bold">Bienvenue, <?php echo $_SESSION['username'] ?></h1>
      <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-sm">Espace sécurisé</span>
    </div>

    <!-- Statistiques -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-2xl transition">
        <div class="flex items-center space-x-4">
          <div class="text-4xl text-green-500"><i class="fas fa-users"></i></div>
          <div>
            <h2 class="text-sm text-gray-500">Utilisateurs inscrits</h2>
            <p class="text-2xl font-bold text-gray-800"><?= $userCount ?></p>
            <p class="text-xs text-gray-400">En <strong><?= $mois ?></strong></p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-2xl transition">
        <div class="flex items-center space-x-4">
          <div class="text-4xl text-emerald-500"><i class="fas fa-cogs"></i></div>
          <div>
            <h2 class="text-sm text-gray-500">Types de paiement</h2>
            <p class="text-2xl font-bold text-gray-800"><?= $paymentCount ?></p>
            <p class="text-xs text-gray-400">Configurés</p>
          </div>
        </div>
      </div>

      <div class="bg-white shadow-lg rounded-xl p-6 hover:shadow-2xl transition">
        <div class="flex items-center space-x-4">
          <div class="text-4xl text-blue-500"><i class="fas fa-check-double"></i></div>
          <div>
            <h2 class="text-sm text-gray-500">Suivi des opérations</h2>
            <p class="text-2xl font-bold text-gray-800">Actif</p>
            <p class="text-xs text-gray-400">Transparence assurée</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Boutons d'action -->
    <div class="mt-10 flex flex-wrap gap-4">
      <a href="Account_User.php"
        class="bg-gradient-to-r from-green-600 to-emerald-500 hover:from-emerald-600 hover:to-green-500 text-white py-3 px-6 rounded-full text-sm font-semibold flex items-center gap-2 shadow">
        <i class="fas fa-user-plus"></i>
        Gérer les utilisateurs
      </a>

      <a href="Payment_Type.php"
        class="bg-white text-green-700 border border-green-500 hover:bg-gray-50 py-3 px-6 rounded-full text-sm font-semibold flex items-center gap-2 shadow">
        <i class="fas fa-cogs"></i>
        Configurer les types de paiement
      </a>
    </div>

    <!-- Infos -->
    <div class="mt-12 grid grid-cols-1 md:grid-cols-3 gap-6 text-sm text-gray-600">
      <div class="flex items-center gap-2">
        <i class="fas fa-lock text-green-400"></i>
        <span>Connexion sécurisée</span>
      </div>
      <div class="flex items-center gap-2">
        <i class="fas fa-database text-green-400"></i>
        <span>Données en temps réel</span>
      </div>
      <div class="flex items-center gap-2">
        <i class="fas fa-bullseye text-green-400"></i>
        <span>Gestion centralisée</span>
      </div>
    </div>
  </div>
</body>

</html>