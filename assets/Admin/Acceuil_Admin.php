<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard Admin</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="flex bg-gray-100 h-screen" style="font-family: 'Poppins', sans-serif";>

  <!-- Sidebar -->
  <aside class="w-64 bg-white shadow-md h-full p-6">
    <h2 class="text-2xl font-bold mb-6 text-black">Admin</h2>
    <nav class="space-y-4">
      <a href="../Admin/Account_User.php" class="block text-gray-700 hover:text-orange-500 transition font-medium">Enregistrer un utilisateur</a>
      <a href="../Admin/Payment_Type.php" class="block text-gray-700 hover:text-orange-500 transition font-medium">Configurer type de paiement</a>
    </nav>
  </aside>

  <!-- Contenu principal -->
  <main class="flex-1 p-8 overflow-auto">
    <h1 class="text-3xl font-bold mb-6">Tableau de bord</h1>

    <!-- Cartes -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-2">Utilisateurs enregistrés ce mois</h2>
        <p id="userCount" class="text-4xl text-orange-600 font-bold">0</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-2">Configurations de paiements ce mois</h2>
        <p id="paymentCount" class="text-4xl text-orange-600 font-bold">0</p>
      </div>
    </div>

    <!-- Graphique -->
    <div class="bg-white p-6 rounded-lg shadow">
      <h2 class="text-lg font-semibold mb-4">Activités mensuelles</h2>
      <canvas id="activityChart" height="100"></canvas>
    </div>
  </main>

  <script>
    // Exemple dynamique (doit être remplacé par tes données PHP via AJAX ou JSON)
    const labels = ['Jan', 'Fév', 'Mars', 'Avr', 'Mai', 'Juin', 'Juil'];
    const userActivity = [2, 5, 3, 4, 6, 8, 3];
    const paymentActivity = [1, 2, 2, 5, 3, 7, 2];

    document.getElementById('userCount').textContent = userActivity[userActivity.length - 1];
    document.getElementById('paymentCount').textContent = paymentActivity[paymentActivity.length - 1];

    const ctx = document.getElementById('activityChart').getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: labels,
        datasets: [
          {
            label: 'Utilisateurs enregistrés',
            data: userActivity,
            backgroundColor: 'rgba(255, 99, 132, 0.7)',
          },
          {
            label: 'Types de paiements configurés',
            data: paymentActivity,
            backgroundColor: 'rgba(54, 162, 235, 0.7)',
          }
        ]
      },
      options: {
        responsive: true,
        scales: {
          y: { beginAtZero: true }
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
        cercle.style.border = '3px solid #0ab39c';
        cercle.style.borderTop = '3px solid #405189';
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
