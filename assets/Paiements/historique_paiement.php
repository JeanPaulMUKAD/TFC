<?php
  require_once __DIR__ . '/../Controllers/AuthController.php';

  $authController = new AuthController();
  $data = $authController->getPaymentHistory();

  $payments = $data['payments'];
  $total_usd = $data['total_usd'];
  $total_fc = $data['total_fc'];
  $payments_by_class = $data['payments_by_class'];
  $percentage_change = $data['percentage_change'];
  $percentage_class = $data['percentage_class'];
  $percentage_icon = $data['percentage_icon'];
?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Historique des paiements | Administration C.S.P.P.UNILU</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>

<body class="min-h-screen bg-gradient-to-br from-teal-400 to-indigo-900 flex items-center justify-center">
  <div class="container mx-auto px-4">
     <!-- Lien de retour -->
     <div class="mb-4">
      <a href="../../Dashboad.php" class="text-blue text-sm font-medium hover:underline ">&larr; Retour vers la page analyse</a>
    </div>
    <h1 class="text-3xl font-bold text-center text-blue-900 mb-8">HISTORIQUES DE PAIEMENTS</h1>
    <div class="table-responsive">
      <table class="table-auto w-full border-collapse border border-gray-300 bg-white rounded-lg shadow">
        <thead class="bg-gray-50">
          <tr>
            <th class="border border-gray-300 px-4 py-2 text-blue-900">ID</th>
            <th class="border border-gray-300 px-4 py-2">Nom de l'élève</th>
            <th class="border border-gray-300 px-4 py-2">Classe</th>
            <th class="border border-gray-300 px-4 py-2">Montant payé</th>
            <th class="border border-gray-300 px-4 py-2">Devise</th>
            <th class="border border-gray-300 px-4 py-2">Motif de paiement</th>
            <th class="border border-gray-300 px-4 py-2">Date de paiement</th>
            <th class="border border-gray-300 px-4 py-2">Annuel</th>
            <th class="border border-gray-300 px-4 py-2">Status</th>
            <th class="border border-gray-300 px-4 py-2">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if (count($payments) > 0): ?>
             <?php $id = 1; ?>
             <?php foreach ($payments as $row): ?>
              <?php
                // Déterminer la devise en fonction de la valeur de montant_payer
                $devise = strpos($row['montant_payer'], '$') !== false ? '$' : 'Fc';
                // Calculer le montant restant à payer
                // Extraction des données
                $total_annuel = $row['total_annuel'];
                $montant_payer = $row['montant_payer'];

                // Retirer les lettres éventuelles (par ex: "500USD" -> 500)
                $total_annuel_numeric = (int) filter_var($total_annuel, FILTER_SANITIZE_NUMBER_INT);
                $montant_payer_numeric = (int) filter_var($montant_payer, FILTER_SANITIZE_NUMBER_INT);

                // Trouver la devise (USD ou CDF) depuis total_annuel
                $devise = preg_replace('/[0-9]/', '', $total_annuel);
                // Faire la soustraction
                $reste = $total_annuel_numeric - $montant_payer_numeric;
              ?>
              <tr class="hover:bg-gray-100" id="row-<?php echo $row['id']; ?>">
                <td class="border border-gray-300 px-4 py-2"><?php echo $id++; ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo $row['nom_eleve']; ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo $row['classe_eleve']; ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo $row['montant_payer']; ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo $devise; ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo $row['motif_paiement']; ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo $row['date_paiement']; ?></td>
                <td class="border border-gray-300 px-4 py-2"><?php echo $row['total_annuel']; ?></td>
                <td class="border border-gray-300 px-4 py-2">
                  <?php if ($reste > 0): ?>
                    <span class="text-red-500">Reste à payer: <?php echo $reste . ' ' . $devise; ?></span>
                  <?php else: ?>
                    <span class="text-green-500">Payé</span>
                  <?php endif; ?>
                </td>
                <td class="border border-gray-300 px-4 py-2">
                  <button onclick="imprimerRecu('<?php echo addslashes($row['nom_eleve']); ?>', '<?php echo addslashes($row['classe_eleve']); ?>', '<?php echo addslashes($row['montant_payer']); ?>', '<?php echo $devise; ?>', '<?php echo addslashes($row['motif_paiement']); ?>', '<?php echo addslashes($row['date_paiement']); ?>')" class="inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Imprimer
                  </button>
                  <form method="POST" class="inline">
                    <input type="hidden" name="delete_id" value="<?php echo $row['id']; ?>">
                    <button type="submit" class="inline-flex items-center px-3 py-1.5 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                      Supprimer
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach;?>
          <?php else: ?>
            <tr>
              <td colspan="8" class="border border-gray-300 px-4 py-2 text-center text-gray-500">Aucun paiement trouvé</td>
            </tr>
          <?php endif; ?>
        </tbody>
        <tfoot class="bg-gray-50">
          <tr>
            <td colspan="9" class="border border-gray-300 px-4 py-2 font-semibold text-right">Total en USD</td>
            <td class="border border-gray-300 px-4 py-2 font-semibold">$<?php echo number_format($total_usd, 2); ?></td>
          </tr>
          <tr>
            <td colspan="9" class="border border-gray-300 px-4 py-2 font-semibold text-right">Total en Fc</td>
            <td class="border border-gray-300 px-4 py-2 font-semibold"><?php echo number_format($total_fc, 2); ?> Fc</td>
          </tr>
        </tfoot>
      </table>
    </div>
    <!-- footer -->
    <footer class="footer galaxy-border-none mt-8">
      <div class="container mx-auto px-4">
      <div class="text-center">
        <p class="mb-0 text-blue">&copy;
      <script>document.write(new Date().getFullYear())</script> Administration <i class="mdi mdi-heart text-blue-500"></i> by C.S.P.P.UNILU
        </p>
      </div>
      </div>
    </footer>
    <!-- end Footer -->
  </div>

    <script>
        function imprimerRecu(nom, classe, montant, devise, motif, date) {
            const recu = `
                <html>
                    <head>
                        <title>Reçu de paiement</title>
                        <style>
                            body { font-family: Arial, sans-serif; margin: 20px; }
                            .recu-container { max-width: 600px; margin: auto; padding: 20px; border: 1px solid #ccc; border-radius: 10px; background-color: #f9f9f9; }
                            .recu-header { text-align: center; margin-bottom: 20px; }
                            .recu-header h2 { margin: 0; font-size: 24px; color: #333; }
                            .recu-details { margin-bottom: 20px; }
                            .recu-details table { width: 100%; border-collapse: collapse; }
                            .recu-details th, .recu-details td { padding: 8px; text-align: left; border-bottom: 1px solid #ddd; }
                            .recu-details th { background-color: #f2f2f2; }
                            .recu-footer { text-align: center; margin-top: 20px; }
                            .recu-footer p { margin: 0; font-size: 14px; color: #777; }
                        </style>
                    </head>
                    <body>
                        <div class="recu-container">
                            <div class="recu-header">
                                <h2>Reçu de paiement</h2>
                            </div>
                            <div class="recu-details">
                                <table>
                                    <tr>
                                        <th>Nom de l'élève</th>
                                        <td>${nom}</td>
                                    </tr>
                                    <tr>
                                        <th>Classe</th>
                                        <td>${classe}</td>
                                    </tr>
                                    <tr>
                                        <th>Montant payé</th>
                                        <td>${montant}</td>
                                    </tr>
                                    <tr>
                                        <th>Motif</th>
                                        <td>${motif}</td>
                                    </tr>
                                    <tr>
                                        <th>Date de paiement</th>
                                        <td>${date}</td>
                                    </tr>
                                    <tr>
                                        <th>Statut</th>
                                        <?php if ($reste > 0): ?>
                                        <span class="text-red-500">Reste à payer: <?php echo $reste . ' ' . $devise; ?></span>
                                        <?php else: ?>
                                          <span class="text-green-500">Payé</span>
                                        <?php endif; ?>
                                        <td><?php echo $reste > 0 ? 'Reste à payer: ' . $reste . ' ' . $devise : 'Payé'; ?></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="recu-footer">
                                <p>Merci pour votre paiement.</p>
                            </div>
                        </div>
                    </body>
                </html>
            `;
            const printWindow = window.open('', '', 'height=600,width=800');
            printWindow.document.write(recu);
            printWindow.document.close();
            printWindow.print();
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