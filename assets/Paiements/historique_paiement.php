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

// Handle delete request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $paymentId = $_POST['delete_id'];
  $authController->deletePayment($paymentId);
  // Redirect to refresh the page and show updated data
  header('Location: ' . $_SERVER['PHP_SELF']);
  exit();
}

?>

<!doctype html>
<html lang="en">

<head>
  <meta charset="utf-8" />
  <title>Historique des paiements | Administration C.S.P.P.UNILU</title>
  <link rel="shortcut icon" href="/assets/images/logo_pp.png">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <style>
    /* Custom scrollbar for table-responsive */
    .table-responsive::-webkit-scrollbar {
      height: 8px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
      background-color: #a0aec0;
      /* Tailwind gray-400 */
      border-radius: 4px;
    }

    .table-responsive::-webkit-scrollbar-track {
      background-color: #edf2f7;
      /* Tailwind gray-200 */
    }

    /* Hidden by default */
    .modal {
      display: none;
      position: fixed;
      z-index: 1000;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0, 0, 0, 0.5);
      justify-content: center;
      align-items: center;
    }

    .modal-content {
      background-color: #fefefe;
      margin: auto;
      padding: 20px;
      border-radius: 8px;
      width: 90%;
      max-width: 400px;
      box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    }
  </style>
</head>

<body
  class="min-h-screen bg-gradient-to-br from-green-400 to-indigo-700 flex flex-col items-center justify-center py-8">
  <div class="container mx-auto px-4 w-full lg:w-4/5 xl:w-3/4">
    <div class="mb-6">
      <a href="../../Dashboad.php" class="text-white text-sm font-medium hover:underline flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
          stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
        </svg>
        Retour vers la page d'analyse
      </a>
    </div>

    <h1 class="text-4xl font-extrabold text-white text-center mb-8 drop-shadow-lg">RAPPORT DES PAIEMENTS</h1>

    <div class="bg-white rounded-xl shadow-2xl overflow-hidden">
      <div class="p-6">
        <div class="table-responsive overflow-x-auto">
          <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-100">
              <tr>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">ID</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Nom</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Post-nom
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Prénom</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Parents
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Classe</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Montant
                  Payé</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Devises
                </th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Motif</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Mode</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Date</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Annuel</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Statut</th>
                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Action</th>
              </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
              <?php if (count($payments) > 0): ?>
                <?php $id = 1; ?>
                <?php foreach ($payments as $row): ?>
                  <?php
                  // Determine currency based on montant_payer value
                  $devise_montant_payer = strpos($row['montant_payer'], '$') !== false ? '$' : 'Fc';

                  // Extract numeric values and currency for calculations
                  $total_annuel_numeric = (int) filter_var($row['total_annuel'], FILTER_SANITIZE_NUMBER_INT);
                  $montant_payer_numeric = (int) filter_var($row['montant_payer'], FILTER_SANITIZE_NUMBER_INT);
                  $devise_total_annuel = preg_replace('/[0-9,\s\.]+/', '', $row['total_annuel']);

                  $reste = $total_annuel_numeric - $montant_payer_numeric;
                  $devise_reste = $devise_total_annuel ?: $devise_montant_payer;
                  ?>
                  <tr class="hover:bg-gray-50 transition-colors duration-150" id="row-<?php echo $row['id']; ?>">
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800"><?php echo $id++; ?></td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['nom_eleve']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['postnom_eleve']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['prenom_eleve']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['nom_parent']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['classe_eleve']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['montant_payer']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['devise']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['motif_paiement']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['mode_paiement']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['date_paiement']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-800">
                      <?php echo htmlspecialchars($row['total_annuel']); ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm">
                      <?php if ($reste > 0): ?>
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                          Reste: <?php echo number_format($reste, 2) . ' ' . $devise_reste; ?>
                        </span>
                      <?php else: ?>
                        <span
                          class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                          Payé
                        </span>
                      <?php endif; ?>
                    </td>
                    <td class="px-4 py-2 whitespace-nowrap text-sm font-medium">
                      <button
                        onclick="imprimerRecu('<?php echo addslashes($row['nom_eleve']); ?>', '<?php echo addslashes($row['postnom_eleve']); ?>', '<?php echo addslashes($row['prenom_eleve']); ?>', '<?php echo addslashes($row['classe_eleve']); ?>', '<?php echo addslashes($row['montant_payer']); ?>', '<?php echo addslashes($row['devise']); ?>', '<?php echo addslashes($row['motif_paiement']); ?>', '<?php echo addslashes($row['mode_paiement']); ?>','<?php echo addslashes($row['date_paiement']); ?>', '<?php echo number_format($reste, 2); ?>', '<?php echo addslashes($devise_reste); ?>')"
                        class="inline-flex items-center px-3 py-1.5 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-150 mb-1 lg:mb-0 lg:mr-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24"
                          stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Imprimer
                      </button>
                      
                    </td>
                  </tr>
                <?php endforeach; ?>
              <?php else: ?>
                <tr>
                  <td colspan="13" class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">Aucun paiement
                    trouvé.</td>
                </tr>
              <?php endif; ?>
            </tbody>
            <tfoot class="bg-gray-100">
              <tr>
                <td colspan="9"
                  class="px-4 py-3 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">Total en USD
                </td>
                <td colspan="4" class="px-4 py-3 text-left text-sm font-bold text-gray-900">
                  $<?php echo number_format($total_usd, 2); ?>
                </td>
              </tr>
              <tr>
                <td colspan="9"
                  class="px-4 py-3 text-right text-sm font-semibold text-gray-700 uppercase tracking-wider">Total en CDF
                </td>
                <td colspan="4" class="px-4 py-3 text-left text-sm font-bold text-gray-900">
                  <?php echo number_format($total_fc, 2); ?> Fc
                </td>
              </tr>
            </tfoot>
          </table>
        </div>
      </div>
    </div>

    <footer class="mt-8 text-white text-center">
      <p class="mb-0 text-opacity-80">
        &copy;
        <script>document.write(new Date().getFullYear())</script> Administration <span
          class="text-red-300">&hearts;</span> by C.S.P.P.UNILU
      </p>
    </footer>
  </div>

  <div id="deleteModal" class="modal">
    <div class="modal-content">
      <div class="text-center p-5">
        <svg class="mx-auto mb-4 w-12 h-12 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="mb-5 text-lg font-normal text-gray-800">Êtes-vous sûr de vouloir supprimer ce paiement ?</h3>
        <form id="deleteForm" method="POST" class="inline-block">
          <input type="hidden" name="delete_id" id="modalDeleteId">
          <button type="submit"
            class="text-white bg-red-600 hover:bg-red-800 focus:ring-4 focus:outline-none focus:ring-red-300 font-medium rounded-lg text-sm inline-flex items-center px-5 py-2.5 text-center mr-2">
            Oui, je suis sûr
          </button>
        </form>
        <button type="button" onclick="hideDeleteModal()"
          class="text-gray-900 bg-white hover:bg-gray-100 focus:ring-4 focus:outline-none focus:ring-gray-200 rounded-lg border border-gray-200 text-sm font-medium px-5 py-2.5 hover:text-gray-900 focus:z-10">
          Non, annuler
        </button>
      </div>
    </div>
  </div>

  <script>
    function imprimerRecu(nom, postnom, prenom, classe, montant, devise, motif, mode, date, reste, deviseReste) {
      const statusText = parseFloat(reste) > 0 ? `Reste à payer: ${reste} ${deviseReste}` : 'Payé';
      const statusColor = parseFloat(reste) > 0 ? '#ef4444' : '#22c55e'; // Tailwind red-500 vs green-500

      const recu = `
            <!doctype html>
            <html>
                <head>
                    <title>Reçu de paiement</title>
                    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
                    <style>
                        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; margin: 0; padding: 20px; background-color: #f8fafc; color: #333; }
                        .recu-container { max-width: 650px; margin: 30px auto; padding: 30px; border: 1px solid #e2e8f0; border-radius: 12px; background-color: #fff; box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
                        .recu-header { text-align: center; margin-bottom: 30px; border-bottom: 2px solid #3b82f6; padding-bottom: 15px; }
                        .recu-header h2 { margin: 0; font-size: 2.25rem; color: #1e40af; font-weight: 700; }
                        .recu-header p { margin-top: 5px; font-size: 1rem; color: #64748b; }
                        .recu-details { margin-bottom: 30px; }
                        .recu-details table { width: 100%; border-collapse: separate; border-spacing: 0 10px; }
                        .recu-details th, .recu-details td { padding: 12px 15px; text-align: left; vertical-align: top; }
                        .recu-details th { background-color: #eff6ff; color: #1e40af; font-weight: 600; border-radius: 8px 0 0 8px; }
                        .recu-details td { background-color: #f8fafc; border-radius: 0 8px 8px 0; }
                        .recu-details tr:last-child th, .recu-details tr:last-child td { border-bottom: none; }
                        .recu-total { margin-top: 20px; text-align: right; font-size: 1.125rem; font-weight: 600; color: #1e40af; }
                        .recu-footer { text-align: center; margin-top: 30px; padding-top: 15px; border-top: 1px dashed #cbd5e1; }
                        .recu-footer p { margin: 0; font-size: 0.9rem; color: #718096; }
                        .status-paid { color: #22c55e; font-weight: bold; }
                        .status-remaining { color: #ef4444; font-weight: bold; }
                    </style>
                </head>
                <body>
                    <div class="recu-container">
                        <div class="recu-header">
                            <h2>REÇU DE PAIEMENT</h2>
                            <p>C.S.P.P.UNILU</p>
                        </div>
                        <div class="recu-details">
                            <table>
                                <tr>
                                    <th>Nom de l'élève</th>
                                    <td>${nom} ${postnom} ${prenom}</td>
                                </tr>
                                <tr>
                                    <th>Classe</th>
                                    <td>${classe}</td>
                                </tr>
                                <tr>
                                    <th>Montant Payé</th>
                                    <td>${montant} ${devise}</td>
                                </tr>
                                <tr>
                                    <th>Motif de paiement</th>
                                    <td>${motif}</td>
                                </tr>
                                 <tr>
                                    <th>Motif de paiement</th>
                                    <td>${mode}</td>
                                </tr>
                                <tr>
                                    <th>Date de paiement</th>
                                    <td>${date}</td>
                                </tr>
                                <tr>
                                    <th>Statut</th>
                                    <td style="color: ${statusColor}; font-weight: bold;">${statusText}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="recu-footer">
                            <p>Merci pour votre paiement. Pour toute question, veuillez nous contacter.</p>
                        </div>
                    </div>
                </body>
            </html>
        `;
      const printWindow = window.open('', '_blank', 'height=600,width=800');
      printWindow.document.write(recu);
      printWindow.document.close();
      printWindow.print();
    }
  </script>

  <script>
    // Récupère les éléments de la fenêtre modale et du formulaire
    const deleteModal = document.getElementById('deleteModal');
    const modalDeleteIdInput = document.getElementById('modalDeleteId');
    const deleteForm = document.getElementById('deleteForm');

    // Fonction pour afficher la fenêtre modale de suppression
    function showDeleteModal(id) {
      modalDeleteIdInput.value = id;
      deleteModal.style.display = 'flex';
    }

    // Fonction pour masquer la fenêtre modale de suppression
    function hideDeleteModal() {
      deleteModal.style.display = 'none';
    }

    // Gère la soumission du formulaire de suppression
    deleteForm.addEventListener('submit', function (event) {
      event.preventDefault(); // Empêche la soumission du formulaire par défaut
      const paymentId = modalDeleteIdInput.value;

      // Soumet le formulaire
      this.submit();
    });
  </script>

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