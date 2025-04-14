<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "school";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['local_payment'])) {
  $nom_eleve = $conn->real_escape_string($_POST['nom_eleve']);
  $classe_eleve = $conn->real_escape_string($_POST['classe_eleve']);
  $montant_payer = $conn->real_escape_string($_POST['montant_payer']);
  $devise = $conn->real_escape_string($_POST['devise']);
  $motif_paiement = $conn->real_escape_string($_POST['motif_paiement']);
  $montant_payer .= $devise;

  $sql = "INSERT INTO eleve (nom_eleve, classe_eleve, montant_payer, motif_paiement) VALUES ('$nom_eleve', '$classe_eleve', '$montant_payer', '$motif_paiement')";

  if ($conn->query($sql) === TRUE) {
    $message = "<p class='text-green-500 text-center'>Le paiement de l'élève <?php>$nom_eleve s'est éffectué avec succès.</p>";
  } else {
    $message = "<p class='text-red-500 text-center'>Erreur: " . $conn->error . "</p>";
  }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['online_payment'])) {
  $nom_eleve = $conn->real_escape_string($_POST['nom_eleve']);
  $classe_eleve = $conn->real_escape_string($_POST['classe_eleve']);
  $montant_payer = $conn->real_escape_string($_POST['montant_payer']);
  $devise = $conn->real_escape_string($_POST['devise']);
  $motif_paiement = $conn->real_escape_string($_POST['motif_paiement']);
  $payment_method = $conn->real_escape_string($_POST['payment_method']);
  $montant_payer .= $devise;

  $sql = "INSERT INTO eleve (nom_eleve, classe_eleve, montant_payer, motif_paiement, payment_method) VALUES ('$nom_eleve', '$classe_eleve', '$montant_payer', '$motif_paiement', '$payment_method')";

  if ($conn->query($sql) === TRUE) {
    $message = "<p class='text-green-500 text-center'>Paiement enregistré avec succès.</p>";
  } else {
    $message = "<p class='text-red-500 text-center'>Erreur: " . $conn->error . "</p>";
  }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Paiement | Administration C.S.P.P.UNILU</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="container mx-auto px-4">
        <!-- Lien de retour -->
        <div class="mb-4">
            <a href="Dashboad.php" class="text-blue text-sm font-medium hover:underline ">&larr; Retour vers la page analyse</a>
        </div>
        <h1 class="text-3xl font-bold text-center text-gray-800 mb-8">PAIEMENT</h1>
        <?php echo $message; ?>

        <!-- Boutons pour afficher les formulaires -->
        <div class="flex justify-center space-x-4 mb-8">
            <button id="btnLocalPayment" class="bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition">Paiement en espèces</button>
            <button id="btnOnlinePayment" class="bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition">Paiement en ligne</button>
        </div>

        <!-- Formulaires -->
        <div id="localPaymentForm" class="hidden bg-white shadow-md rounded-lg p-6 w-full max-w-lg mx-auto">
            <div class="flex items-center justify-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/2331/2331943.png" alt="Espèces" class="w-8 h-8 mr-2">
                <h2 class="text-xl font-semibold text-gray-700">Paiement en espèces</h2>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="local_payment" value="1">
                <div>
                    <label for="nom_eleve" class="block text-sm font-medium text-gray-700">Nom complet de l'élève</label>
                    <input type="text" name="nom_eleve" id="nom_eleve" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="classe_eleve" class="block text-sm font-medium text-gray-700">Classe de l'élève</label>
                    <input type="text" name="classe_eleve" id="classe_eleve" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="montant_payer" class="block text-sm font-medium text-gray-700">Montant à payer</label>
                    <input type="number" name="montant_payer" id="montant_payer" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md" min="1">
                </div>
                <div>
                    <label for="devise" class="block text-sm font-medium text-gray-700">Devise</label>
                    <select name="devise" id="devise" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <option value="$">USD</option>
                        <option value="Fc">CDF</option>
                    </select>
                </div>
                <div>
                    <label for="motif_paiement" class="block text-sm font-medium text-gray-700">Motif du paiement</label>
                    <input type="text" name="motif_paiement" id="motif_paiement" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <button type="submit" class="w-full bg-blue-500 text-white py-2 px-4 rounded-md hover:bg-blue-600 transition">Confirmer</button>
            </form>
        </div>

        <div id="onlinePaymentForm" class="hidden bg-white shadow-md rounded-lg p-6 w-full max-w-lg mx-auto">
            <div class="flex items-center justify-center mb-4">
                <img src="https://cdn-icons-png.flaticon.com/512/1087/1087924.png" alt="En ligne" class="w-8 h-8 mr-2">
                <h2 class="text-xl font-semibold text-gray-700">Paiement en ligne</h2>
            </div>
            <form method="POST" class="space-y-4">
                <input type="hidden" name="online_payment" value="1">
                <div>
                    <label for="nom_eleve" class="block text-sm font-medium text-gray-700">Nom complet de l'élève</label>
                    <input type="text" name="nom_eleve" id="nom_eleve" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="classe_eleve" class="block text-sm font-medium text-gray-700">Classe de l'élève</label>
                    <input type="text" name="classe_eleve" id="classe_eleve" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="montant_payer" class="block text-sm font-medium text-gray-700">Montant à payer</label>
                    <input type="number" name="montant_payer" id="montant_payer" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md" min="1">
                </div>
                <div>
                    <label for="devise" class="block text-sm font-medium text-gray-700">Devise</label>
                    <select name="devise" id="devise" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <option value="$">USD</option>
                        <option value="Fc">CDF</option>
                    </select>
                </div>
                <div>
                    <label for="motif_paiement" class="block text-sm font-medium text-gray-700">Motif du paiement</label>
                    <input type="text" name="motif_paiement" id="motif_paiement" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                </div>
                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">Méthode de paiement</label>
                    <select name="payment_method" id="payment_method" required class="mt-1 block w-full p-2 border border-gray-300 rounded-md">
                        <option value="Airtel Money">Airtel Money</option>
                        <option value="Orange Money">Orange Money</option>
                        <option value="M-Pesa">M-Pesa</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-green-500 text-white py-2 px-4 rounded-md hover:bg-green-600 transition">Confirmer</button>
            </form>
        </div>
    </div>

    <script>
        const btnLocalPayment = document.getElementById('btnLocalPayment');
        const btnOnlinePayment = document.getElementById('btnOnlinePayment');
        const localPaymentForm = document.getElementById('localPaymentForm');
        const onlinePaymentForm = document.getElementById('onlinePaymentForm');

        btnLocalPayment.addEventListener('click', () => {
            localPaymentForm.classList.remove('hidden');
            onlinePaymentForm.classList.add('hidden');
        });

        btnOnlinePayment.addEventListener('click', () => {
            onlinePaymentForm.classList.remove('hidden');
            localPaymentForm.classList.add('hidden');
        });
    </script>
</body>
</html>