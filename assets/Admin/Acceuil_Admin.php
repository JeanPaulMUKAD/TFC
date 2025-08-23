<?php

require_once __DIR__ . '/../Controllers/AuthController.php';
$auth = new AuthController();

// Initialisation de $_SESSION['username']
if (!isset($_SESSION['username'])) {
    $_SESSION['username'] = 'Admin';
}

$dbError = null;
if ($auth->conn->connect_error) {
    $dbError = "Erreur de connexion à la base de données : " . $auth->conn->connect_error;
}

$paymentCount = 0;
$paymentTypes = [];

if (!$dbError) {
    // Récupérer nombre total types de paiement
    $sql2 = "SELECT COUNT(*) as total FROM payementtype";
    $result2 = $auth->conn->query($sql2);
    if ($result2 && $row2 = $result2->fetch_assoc()) {
        $paymentCount = (int) $row2['total'];
    }

    // Récupérer types de paiement pour affichage initial
    $sql_payments = "SELECT id, nom_type FROM payementtype";
    $result_payments = $auth->conn->query($sql_payments);
    if ($result_payments && $result_payments->num_rows > 0) {
        while ($row = $result_payments->fetch_assoc()) {
            $paymentTypes[] = $row;
        }
    }
}

$notification = null;

// --- Gestion POST utilisateurs ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && in_array($_POST['action'], ['ajouter', 'modifier', 'supprimer'])) {
    $action = $_POST['action'];
    $id = $_POST['user_id'] ?? null;
    $name = trim($_POST['Names_User'] ?? '');
    $email = trim($_POST['Email'] ?? '');
    $role = $_POST['Role_User'] ?? null;
    $password = $_POST['Password_User'] ?? '';
    $confirmPassword = $_POST['Confirm_Password'] ?? '';

    if ($action === 'ajouter') {
        if ($password !== $confirmPassword) {
            $notification = ['type' => 'error', 'message' => 'Les mots de passe ne correspondent pas.'];
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $auth->conn->prepare("INSERT INTO utilisateurs (Names_User, Email, Password_User, Role_User) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $name, $email, $hashed, $role);
            if ($stmt->execute()) {
                $notification = ['type' => 'success', 'message' => 'Utilisateur ajouté avec succès !'];
            } else {
                $notification = ['type' => 'error', 'message' => 'Erreur lors de l\'ajout de l\'utilisateur.'];
            }
            $stmt->close();
        }
    } elseif ($action === 'modifier' && $id) {
        if ($password && $password !== $confirmPassword) {
            $notification = ['type' => 'error', 'message' => 'Les mots de passe ne correspondent pas.'];
        } else {
            if ($password) {
                $hashed = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $auth->conn->prepare("UPDATE utilisateurs SET Names_User=?, Email=?, Password_User=?, Role_User=? WHERE id=?");
                $stmt->bind_param("ssssi", $name, $email, $hashed, $role, $id);
            } else {
                $stmt = $auth->conn->prepare("UPDATE utilisateurs SET Names_User=?, Email=?, Role_User=? WHERE id=?");
                $stmt->bind_param("sssi", $name, $email, $role, $id);
            }
            if ($stmt->execute()) {
                $notification = ['type' => 'success', 'message' => 'Utilisateur modifié avec succès !'];
            } else {
                $notification = ['type' => 'error', 'message' => 'Erreur lors de la modification de l\'utilisateur.'];
            }
            $stmt->close();
        }
    } elseif ($action === 'supprimer' && $id) {
        $stmt = $auth->conn->prepare("DELETE FROM utilisateurs WHERE id=?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            $notification = ['type' => 'success', 'message' => 'Utilisateur supprimé avec succès !'];
        } else {
            $notification = ['type' => 'error', 'message' => 'Erreur lors de la suppression de l\'utilisateur.'];
        }
        $stmt->close();
    }

}
// --- Gestion POST types de paiement ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    // Récupération sécurisée des valeurs
    $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
    $nomType = trim($_POST['nom_type'] ?? '');
    $classeType = trim($_POST['classe_type'] ?? '');
    $montantClasse = isset($_POST['montant_classe']) ? (float) $_POST['montant_classe'] : 0.0;
    $mois = trim($_POST['mois'] ?? '');

    if ($action === 'ajouterPaiement' && $nomType && $classeType && $montantClasse > 0 && $mois) {
        $stmt = $auth->conn->prepare("INSERT INTO payementtype (nom_type, classe_type, montant_classe, mois) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $nomType, $classeType, $montantClasse, $mois);
        $notification = $stmt->execute()
            ? ['type' => 'success', 'message' => 'Type de paiement ajouté avec succès !']
            : ['type' => 'error', 'message' => 'Erreur lors de l\'ajout.'];
        $stmt->close();
    } elseif ($action === 'modifierPaiement' && $id > 0 && $nomType && $classeType && $montantClasse > 0 && $mois) {
        $stmt = $auth->conn->prepare("UPDATE payementtype SET nom_type=?, classe_type=?, montant_classe=?, mois=? WHERE id=?");
        $stmt->bind_param("ssdsi", $nomType, $classeType, $montantClasse, $mois, $id);
        $notification = $stmt->execute()
            ? ['type' => 'success', 'message' => 'Type de paiement modifié avec succès !']
            : ['type' => 'error', 'message' => 'Erreur lors de la modification.'];
        $stmt->close();
    } elseif ($action === 'supprimerPaiement' && $id > 0) {
        $stmt = $auth->conn->prepare("DELETE FROM payementtype WHERE id=?");
        $stmt->bind_param("i", $id);
        $notification = $stmt->execute()
            ? ['type' => 'success', 'message' => 'Type de paiement supprimé avec succès !']
            : ['type' => 'error', 'message' => 'Erreur lors de la suppression.'];
        $stmt->close();
    }
}

// --- Récupération de tous les types de paiement pour affichage ---
$paymentTypes = $auth->conn->query("SELECT * FROM payementtype ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
$paymentCount = count($paymentTypes);


// Récupérer utilisateurs pour affichage
$users = [];
if (!$dbError) {
    $sqlUsers = "SELECT id, Names_User, Email, Role_User FROM utilisateurs ORDER BY id ASC";
    $resultUsers = $auth->conn->query($sqlUsers);
    if ($resultUsers && $resultUsers->num_rows > 0) {
        while ($row = $resultUsers->fetch_assoc()) {
            $users[] = $row;
        }
    }
}

?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Dashboard Admin | C.S.P.P.UNILU</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet" />
    <link rel="shortcut icon" href="/assets/images/logo_pp.png" />
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }

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

        .card-shadow {
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1),
                0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .hover-card-shadow:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1),
                0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Animations toast */
        @keyframes slide-in {
            0% {
                transform: translateX(100%);
                opacity: 0;
            }

            100% {
                transform: translateX(0);
                opacity: 1;
            }
        }

        @keyframes slide-out {
            0% {
                transform: translateX(0);
                opacity: 1;
            }

            100% {
                transform: translateX(100%);
                opacity: 0;
            }
        }

        .animate-slide-in {
            animation: slide-in 0.5s forwards;
        }

        .animate-slide-out {
            animation: slide-out 0.5s forwards;
        }
    </style>
</head>

<body class="bg-gray-100 text-black">
    <div id="toast-container" class="fixed top-5 right-5 z-[100] space-y-2"></div>

    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white shadow-md">
        <div class="text-2xl font-bold flex items-center">
            <img src="/assets/images/logo_pp2.png" alt="Logo" class="h-10 w-10 mr-2" />
            <span class="text-black">C.S.P.P</span><span class="text-indigo-500 font-extrabold">.UNILU</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="mailto:administrationcsppunilu@gmail.com"
                class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition duration-200">Aide</a>
            <a href="/logoutAdmin"
                class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-indigo-600 hover:to-blue-500 text-white font-semibold py-2 px-4 rounded-full text-sm">
                Se déconnecter
            </a>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8 mt-20">
        <header class="flex items-center justify-between mb-8 bg-white p-6 rounded-xl shadow-lg">
            <h1 class="text-3xl font-extrabold text-gray-800">Tableau de bord de
                l'Administrateur</h1>
            <span
                class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full text-sm font-semibold flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i>
                <span>Espace Administrateur</span>
            </span>
        </header>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Actions rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

                <a href="#" id="manageUsersBtn"
                    class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-green-100 text-green-600 p-3 rounded-full mr-4">
                            <i class="fas fa-user-plus fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Gérer</p>
                            <p class="text-xl font-semibold text-gray-900">Utilisateurs</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>

                <a id="paymentTypeLink" href="#"
                    class="bg-white p-6 rounded-xl shadow-lg hover:shadow-xl transition-shadow duration-300 flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="bg-blue-100 text-blue-600 p-3 rounded-full mr-4">
                            <i class="fas fa-cogs fa-lg"></i>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Configurer</p>
                            <p class="text-xl font-semibold text-gray-900">Paiements</p>
                        </div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-400"></i>
                </a>
            </div>
        </section>

        <section id="dashboardContent" class="bg-white p-6 rounded-xl shadow-lg min-h-[50vh]">

            <?php if ($dbError): ?>
                <div class="text-center text-red-500 py-10">
                    <i class="fas fa-exclamation-triangle fa-2x mb-4 text-red-400"></i>
                    <p class="text-lg"><?= htmlspecialchars($dbError) ?></p>
                    <p class="text-sm text-gray-500 mt-2">Veuillez vérifier les paramètres de votre
                        base de données dans AuthController.php.</p>
                </div>
            <?php else: ?>

                <div id="defaultMessage" class="text-center text-gray-500 py-10">
                    <i class="fas fa-info-circle fa-2x mb-4 text-gray-400"></i>
                    <p class="text-lg">Sélectionnez une option ci-dessus pour afficher les données.</p>
                </div>

                <div id="userManagement" class="flex flex-col lg:flex-row gap-8" style="display:none;">
                    <div class="lg:w-1/3">
                        <div class="p-6 bg-white rounded-xl shadow-lg">
                            <h3 class="text-green-700 text-center text-2xl font-bold mb-4">
                                Gérer les utilisateurs
                            </h3>
                            <form id="userForm" method="POST" class="space-y-4">
                                <input type="hidden" name="action" id="userAction" value="ajouter" />
                                <input type="hidden" name="user_id" id="userId" value="" />

                                <div>
                                    <label for="Names_User" class="block text-sm font-medium text-gray-700">Nom
                                        complet
                                        <span class="text-green-700">*</span></label>
                                    <input type="text" name="Names_User" id="Names_User" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Nom complet" />
                                </div>
                                <div>
                                    <label for="Email" class="block text-sm font-medium text-gray-700">Email
                                        <span class="text-green-700">*</span></label>
                                    <input type="email" name="Email" id="Email" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Email" />
                                </div>
                                <div>
                                    <label for="Role_User" class="block text-sm font-medium text-gray-700">Rôle
                                        <span class="text-green-700">*</span></label>
                                    <select name="Role_User" id="Role_User" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="Admin">Admin</option>
                                        <option value="caissier">Caissier</option>
                                        <option value="parent">Parent</option>
                                        <option value="prefet">Préfet</option>
                                        <option value="sec">Secrétaire</option>
                                    </select>
                                </div>
                                <div>
                                    <label for="Password_User" class="block text-sm font-medium text-gray-700">Mot
                                        de passe
                                        <span class="text-green-700">*</span></label>
                                    <input type="password" name="Password_User" id="Password_User" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Mot de passe" />
                                </div>
                                <div>
                                    <label for="Confirm_Password" class="block text-sm font-medium text-gray-700">Confirmer
                                        mot
                                        de passe
                                        <span class="text-green-700">*</span></label>
                                    <input type="password" name="Confirm_Password" id="Confirm_Password" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Confirmer mot de passe" />
                                </div>

                                <div class="flex space-x-3 mt-4">
                                    <button type="submit" id="submitAdd"
                                        class="flex-1 bg-green-600 hover:bg-green-700 text-white font-semibold py-2 rounded-lg transition duration-200">Ajouter</button>
                                    <button type="button" id="btnModify"
                                        class="flex-1 bg-yellow-500 hover:bg-yellow-600 text-white font-semibold py-2 rounded-lg transition duration-200">Modifier</button>
                                    <button type="button" id="btnDelete"
                                        class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-2 rounded-lg transition duration-200">Supprimer</button>
                                </div>
                            </form>
                        </div>
                    </div>

                    <div class="lg:w-2/3">
                        <div class="bg-white rounded-xl shadow-lg p-6 overflow-x-auto">
                            <h4 class="text-xl font-semibold mb-4">Liste des utilisateurs</h4>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom complet
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Email
                                        </th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Rôle
                                        </th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <?php if (count($users) > 0): ?>
                                        <?php foreach ($users as $user): ?>
                                            <tr class="hover:bg-gray-50 transition duration-150"
                                                data-id="<?= htmlspecialchars($user['id']) ?>"
                                                data-name="<?= htmlspecialchars($user['Names_User']) ?>"
                                                data-email="<?= htmlspecialchars($user['Email']) ?>"
                                                data-role="<?= htmlspecialchars($user['Role_User']) ?>">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($user['Names_User']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                    <?= htmlspecialchars($user['Email']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-700">
                                                    <?= htmlspecialchars($user['Role_User']) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button class="editUserBtn text-indigo-600 hover:text-indigo-900 mr-4"
                                                        title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="deleteUserBtn text-red-600 hover:text-red-900" title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="4" class="text-center text-sm text-gray-500 py-4">Aucun
                                                utilisateur trouvé.</td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div id="toastContainer" class="space-y-2 mb-4"></div>
                <div id="paymentFormContainer" class="flex flex-col lg:flex-row gap-8" style="display: none;">
                    <div class="lg:w-1/3">
                        <div class="p-6 bg-white rounded-xl shadow-lg">
                            <h3 class="text-2xl font-bold text-gray-800 mb-6">Gérer les types de paiement</h3>

                            <form id="paymentForm" class="space-y-4">
                                <input type="hidden" id="paymentId" name="id" />
                                <div>
                                    <label for="paymentTypeName" class="block text-sm font-medium text-gray-700">Nom du
                                        type
                                        de
                                        paiement</label>
                                    <input type="text" id="paymentTypeName" name="nom_type" required
                                        class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Entrer un type de paiement" />
                                </div>
                                <div>
                                    <label for="classeType" class="block text-sm font-medium text-gray-700">
                                        Classe concernée
                                    </label>
                                    <select id="classeType" name="classe_type" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Sélectionner une classe --</option>
                                        <option value="7e EB">7e EB</option>
                                        <option value="8e EB">8e EB</option>
                                        <option value="1ere SC">1ère SC</option>
                                        <option value="1ere CG">1ère CG</option>
                                        <option value="1ere HP">1ère HP</option>
                                        <option value="1ere MG">1ère MG</option>
                                        <option value="1ere ELECT">1ère ELECT</option>
                                        <option value="2ere SC">2ère SC</option>
                                        <option value="2ere CG">2ère CG</option>
                                        <option value="2ere HP">2ère HP</option>
                                        <option value="2ere MG">2ère MG</option>
                                        <option value="2ere ELECT">2ère ELECT</option>
                                        <option value="2eme TCC">2ème TCC</option>
                                        <option value="3ere SC">3ère SC</option>
                                        <option value="3ere CG">3ère CG</option>
                                        <option value="3ere HP">3ère HP</option>
                                        <option value="3ere MG">3ère MG</option>
                                        <option value="3ere ELECT">3ère ELECT</option>
                                        <option value="3eme TCC">3ème TCC</option>
                                        <option value="4ere SC">4ère SC</option>
                                        <option value="4ere CG">4ère CG</option>
                                        <option value="4ere HP">4ère HP</option>
                                        <option value="4ere MG">4ère MG</option>
                                        <option value="4ere ELECT">4ère ELECT</option>
                                        <option value="4eme TCC">4ème TCC</option>
                                    </select>
                                </div>
                                <!-- Montant -->
                                <div>
                                    <label for="montantClasse" class="block text-sm font-medium text-gray-700">
                                        Montant (CDF)
                                    </label>
                                    <input type="text" id="montantClasse" name="montant_classe" required min="0" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                        placeholder="Entrer le montant" />
                                </div>
                                <!-- Mois -->
                                <div>
                                    <label for="mois" class="block text-sm font-medium text-gray-700">
                                        Mois de paiement
                                    </label>
                                    <select id="mois" name="mois" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm 
                   focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                                        <option value="">-- Sélectionner un mois --</option>
                                        <option value="Séptembre">Séptembre</option>
                                        <option value="Octobre">Octobre</option>
                                        <option value="Novembre">Novembre</option>
                                        <option value="Décembre">Décembre</option>
                                        <option value="Janvier">Janvier</option>
                                        <option value="Février">Février</option>
                                        <option value="Mars">Mars</option>
                                        <option value="Août">Avril</option>
                                        <option value="Avril">Mai</option>
                                        <option value="Juin">Juin</option>
                                        <option value="Annuel">Tous les mois</option>
                                    </select>
                                </div>
                                <script>
                                    document.getElementById('paymentForm').addEventListener('submit', function (e) {
                                        const moisSelect = document.getElementById('mois');
                                        const montantInput = document.getElementById('montantClasse'); // Assure-toi que ton input montant a cet id
                                        const selectedMois = moisSelect.value;
                                        let montant = parseFloat(montantInput.value);

                                        if (selectedMois === 'Annuel') {
                                            // Multiplier par 10 si "Tous les mois" est sélectionné
                                            montant = montant * 10;
                                            montantInput.value = montant.toFixed(2); // Met à jour le champ montant
                                        }

                                        // Le formulaire continue son envoi normal
                                    });
                                </script>

                                <div class="flex justify-end space-x-2">
                                    <button type="button" id="paymentCancelBtn"
                                        class="px-4 py-2 rounded-md bg-gray-300 hover:bg-gray-400 text-gray-700 transition duration-200">Annuler</button>
                                    <button type="submit" id="paymentSubmitBtn"
                                        class="px-4 py-2 rounded-md bg-indigo-600 hover:bg-indigo-700 text-white transition duration-200">Ajouter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <!-- Tableau des types de paiement -->
                    <div class="lg:w-2/3">
                        <div class="overflow-x-auto bg-white rounded-xl shadow-lg p-6">
                            <h4 class="text-xl font-semibold mb-4">Liste des types de paiement</h4>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Nom du type</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Classe</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Montant</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Motif</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Actions</th>
                                    </tr>
                                </thead>
                                <tbody id="paymentTableBody" class="bg-white divide-y divide-gray-200">
                                    <?php if ($paymentCount === 0): ?>
                                        <tr>
                                            <td colspan="5" class="text-center text-sm text-gray-500 py-4">Aucun type de
                                                paiement trouvé.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($paymentTypes as $ptype): ?>
                                            <?php
                                            $nomType = $ptype['nom_type'] ?? '';
                                            $classeType = $ptype['classe_type'] ?? '';
                                            $montant = isset($ptype['montant_classe']) ? $ptype['montant_classe'] : 0;
                                            $mois = $ptype['mois'] ?? 'Mensuel';
                                            ?>
                                            <tr class="hover:bg-gray-50 transition duration-150" data-id="<?= $ptype['id'] ?? 0 ?>">
                                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                    <?= htmlspecialchars($nomType) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?= htmlspecialchars($classeType) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?= $montant ?> FC
                                                    <span
                                                        class="text-xs text-gray-500">(<?= $mois === 'Annuel' ? 'Annuel' : 'Mensuel' ?>)</span>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                    <?= htmlspecialchars($mois) ?>
                                                </td>
                                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                                    <button class="editPaymentBtn text-indigo-600 hover:text-indigo-900 mr-4"
                                                        title="Modifier">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <button class="deletePaymentBtn text-red-600 hover:text-red-900"
                                                        title="Supprimer">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                </div>

            <?php endif; ?>
        </section>
    </div>
    <!-- Modal suppression paiement -->
    <div id="deletePaymentModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-96 p-6 text-center">
            <h2 class="text-xl font-semibold text-red-600 mb-3">⚠️ Suppression</h2>
            <p class="text-gray-700 mb-6">Voulez-vous vraiment supprimer ce type de paiement ?</p>
            <div class="flex justify-center gap-4">
                <button id="cancelDeletePayment"
                    class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg transition">
                    Annuler
                </button>
                <button id="confirmDeletePayment"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg transition">
                    Supprimer
                </button>
            </div>
        </div>
    </div>
    <script>
        let paymentIdToDelete = null;

        document.querySelectorAll('.deletePaymentBtn').forEach(btn => {
            btn.addEventListener('click', e => {
                const tr = e.target.closest('tr');
                paymentIdToDelete = tr.dataset.id;
                document.getElementById('deletePaymentModal').classList.remove('hidden');
            });
        });

        // Annuler
        document.getElementById('cancelDeletePayment').addEventListener('click', () => {
            document.getElementById('deletePaymentModal').classList.add('hidden');
            paymentIdToDelete = null;
        });

        // Confirmer suppression
        document.getElementById('confirmDeletePayment').addEventListener('click', () => {
            if (paymentIdToDelete) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = ''; // 🔹 tu mets ton endpoint ici
                form.classList.add('hidden');

                const actionInput = document.createElement('input');
                actionInput.type = 'hidden';
                actionInput.name = 'action';
                actionInput.value = 'supprimerPaiement';

                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = paymentIdToDelete;

                form.appendChild(actionInput);
                form.appendChild(idInput);
                document.body.appendChild(form);
                form.submit();
            }
            document.getElementById('deletePaymentModal').classList.add('hidden');
        });

    </script>
    <!-- Modal -->
    <div id="deleteModal" class="hidden fixed inset-0 bg-gray-900 bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-2xl shadow-xl w-96 p-6 text-center">
            <h2 class="text-xl font-semibold text-red-600 mb-4">⚠️ Confirmation</h2>
            <p class="text-gray-700 mb-6">Voulez-vous vraiment supprimer cet utilisateur ?</p>
            <div class="flex justify-center gap-4">
                <button id="cancelDelete"
                    class="px-4 py-2 bg-gray-300 hover:bg-gray-400 text-gray-800 rounded-lg">Annuler</button>
                <button id="confirmDelete"
                    class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded-lg">Supprimer</button>
            </div>
        </div>
    </div>

    <script>
        let trToDelete = null;

        document.querySelectorAll('.deleteUserBtn').forEach(btn => {
            btn.addEventListener('click', e => {
                trToDelete = e.target.closest('tr');
                document.getElementById('deleteModal').classList.remove('hidden');
            });
        });

        document.getElementById('cancelDelete').addEventListener('click', () => {
            document.getElementById('deleteModal').classList.add('hidden');
            trToDelete = null;
        });

        document.getElementById('confirmDelete').addEventListener('click', () => {
            if (trToDelete) {
                userId.value = trToDelete.dataset.id;
                userAction.value = 'supprimer';
                userForm.submit();
            }
            document.getElementById('deleteModal').classList.add('hidden');
        });
    </script>


    <script>
        // Variables pour éléments
        const defaultMessage = document.getElementById('defaultMessage');
        const userManagement = document.getElementById('userManagement');
        const paymentFormContainer = document.getElementById('paymentFormContainer');
        const manageUsersBtn = document.getElementById('manageUsersBtn');
        const paymentTypeLink = document.getElementById('paymentTypeLink');

        // Afficher gestion utilisateurs au clic
        manageUsersBtn.addEventListener('click', (e) => {
            e.preventDefault();
            defaultMessage.style.display = 'none';
            paymentFormContainer.style.display = 'none';
            userManagement.style.display = 'flex';
        });

        // Afficher gestion paiements au clic
        paymentTypeLink.addEventListener('click', (e) => {
            e.preventDefault();
            defaultMessage.style.display = 'none';
            userManagement.style.display = 'none';
            paymentFormContainer.style.display = 'flex';
        });

        // --- Gestion formulaire utilisateur ---
        const userForm = document.getElementById('userForm');
        const userAction = document.getElementById('userAction');
        const userId = document.getElementById('userId');
        const namesInput = document.getElementById('Names_User');
        const emailInput = document.getElementById('Email');
        const roleInput = document.getElementById('Role_User');
        const passwordInput = document.getElementById('Password_User');
        const confirmPasswordInput = document.getElementById('Confirm_Password');
        const submitAddBtn = document.getElementById('submitAdd');
        const btnModify = document.getElementById('btnModify');
        const btnDelete = document.getElementById('btnDelete');

        function resetUserForm() {
            userForm.reset();
            userAction.value = 'ajouter';
            userId.value = '';
            passwordInput.required = true;
            confirmPasswordInput.required = true;
            submitAddBtn.textContent = 'Ajouter';
            namesInput.parentElement.style.display = 'block';
            emailInput.parentElement.style.display = 'block';
            roleInput.parentElement.style.display = 'block';
            passwordInput.parentElement.style.display = 'block';
            confirmPasswordInput.parentElement.style.display = 'block';
        }

        btnModify.addEventListener('click', () => {
            userAction.value = 'modifier';
            submitAddBtn.textContent = 'Modifier';
            passwordInput.required = false;
            confirmPasswordInput.required = false;
            namesInput.parentElement.style.display = 'block';
            emailInput.parentElement.style.display = 'block';
            roleInput.parentElement.style.display = 'block';
            passwordInput.parentElement.style.display = 'block';
            confirmPasswordInput.parentElement.style.display = 'block';
        });

        btnDelete.addEventListener('click', () => {
            userAction.value = 'supprimer';
            submitAddBtn.textContent = 'Supprimer';
            namesInput.parentElement.style.display = 'none';
            emailInput.parentElement.style.display = 'none';
            roleInput.parentElement.style.display = 'none';
            passwordInput.parentElement.style.display = 'none';
            confirmPasswordInput.parentElement.style.display = 'none';
        });

        // Modifier utilisateur via tableau
        document.querySelectorAll('.editUserBtn').forEach(btn => {
            btn.addEventListener('click', e => {
                const tr = e.target.closest('tr');
                userId.value = tr.dataset.id;
                namesInput.value = tr.dataset.name;
                emailInput.value = tr.dataset.email;
                roleInput.value = tr.dataset.role;
                passwordInput.value = '';
                confirmPasswordInput.value = '';
                userAction.value = 'modifier';
                submitAddBtn.textContent = 'Modifier';
                passwordInput.required = false;
                confirmPasswordInput.required = false;
                namesInput.parentElement.style.display = 'block';
                emailInput.parentElement.style.display = 'block';
                roleInput.parentElement.style.display = 'block';
                passwordInput.parentElement.style.display = 'block';
                confirmPasswordInput.parentElement.style.display = 'block';
                userForm.scrollIntoView({
                    behavior: 'smooth'
                });
            });
        });


        // --- Gestion formulaire paiement ---
        const paymentForm = document.getElementById('paymentForm');
        const paymentTableBody = document.getElementById('paymentTableBody');
        const paymentIdInput = document.getElementById('paymentId');
        const paymentNameInput = document.getElementById('paymentTypeName');
        const paymentSubmitBtn = document.getElementById('paymentSubmitBtn');
        const paymentCancelBtn = document.getElementById('paymentCancelBtn');

        // Reset formulaire paiement
        function resetPaymentForm() {
            paymentIdInput.value = '';
            paymentNameInput.value = '';
            paymentSubmitBtn.textContent = 'Ajouter';
        }

        // Éditer paiement
        document.querySelectorAll('.editPaymentBtn').forEach(btn => {
            btn.addEventListener('click', e => {
                const tr = e.target.closest('tr');

                // Récupérer les données du tr via data-* ou td
                const id = tr.dataset.id;
                const nomType = tr.querySelector('td:nth-child(1)').textContent.trim();
                const classeType = tr.querySelector('td:nth-child(2)').textContent.trim();
                const montantClasse = tr.querySelector('td:nth-child(3)').textContent.trim().split(' ')[0].replace(',', '.'); // retirer FC
                const mois = tr.querySelector('td:nth-child(4)').textContent.trim();

                // Remplir le formulaire
                document.getElementById('paymentId').value = id;
                document.getElementById('paymentTypeName').value = nomType;
                document.getElementById('classeType').value = classeType;
                document.getElementById('montantClasse').value = montantClasse;
                document.getElementById('mois').value = mois;

                // Modifier le bouton
                document.getElementById('paymentSubmitBtn').textContent = 'Modifier';
                document.getElementById('paymentTypeName').focus();
            });
        });


        // Annuler modification paiement
        paymentCancelBtn.addEventListener('click', (e) => {
            e.preventDefault();
            resetPaymentForm();
        });

        // Soumettre formulaire paiement (ajouter/modifier)
        paymentForm.addEventListener('submit', (e) => {
            e.preventDefault();

            const id = paymentIdInput.value;
            const nom_type = paymentNameInput.value.trim();

            if (!nom_type) {
                alert("Veuillez entrer un nom de type de paiement.");
                return;
            }

            const action = id ? 'modifierPaiement' : 'ajouterPaiement';

            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '';

            // Action
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = action;
            form.appendChild(actionInput);

            // Nom type
            const nomTypeInput = document.createElement('input');
            nomTypeInput.type = 'hidden';
            nomTypeInput.name = 'nom_type';
            nomTypeInput.value = nom_type;
            form.appendChild(nomTypeInput);

            // ID si modification
            if (id) {
                const idInput = document.createElement('input');
                idInput.type = 'hidden';
                idInput.name = 'id';
                idInput.value = id;
                form.appendChild(idInput);
            }

            // --- Champs supplémentaires ---
            const classeType = document.getElementById('classeType').value;
            const montantClasse = document.getElementById('montantClasse').value;
            const mois = document.getElementById('mois').value;

            const classeInput = document.createElement('input');
            classeInput.type = 'hidden';
            classeInput.name = 'classe_type';
            classeInput.value = classeType;

            const montantInput = document.createElement('input');
            montantInput.type = 'hidden';
            montantInput.name = 'montant_classe';
            montantInput.value = montantClasse;

            const moisInput = document.createElement('input');
            moisInput.type = 'hidden';
            moisInput.name = 'mois';
            moisInput.value = mois;

            form.appendChild(classeInput);
            form.appendChild(montantInput);
            form.appendChild(moisInput);

            // Soumettre
            document.body.appendChild(form);
            form.submit();
        });

        // --- Fonctionnalité Toast Messages ---
        function showToast(message, type = 'success') {
            const toast = document.createElement('div');
            toast.className = `
            flex items-center w-full max-w-lg p-4 rounded-xl shadow-md border
            transform scale-95 opacity-0 transition-all duration-300 ease-in-out
            ${type === 'success'
                    ? 'bg-green-50 border-green-200 text-green-700'
                    : 'bg-red-50 border-red-200 text-red-700'}
        `;
            toast.role = 'alert';

            const icon = type === 'success'
                ? '<i class="fas fa-check-circle text-green-500 text-xl mr-3"></i>'
                : '<i class="fas fa-times-circle text-red-500 text-xl mr-3"></i>';

            toast.innerHTML = `
            ${icon}
            <div class="text-sm font-medium flex-grow">${message}</div>
            <button type="button" 
                class="ml-3 bg-transparent rounded-lg p-1.5 inline-flex items-center justify-center 
                       text-gray-400 hover:text-gray-700 hover:bg-gray-100 transition" 
                aria-label="Close">
                <i class="fas fa-times"></i>
            </button>
        `;

            const container = document.getElementById('toastContainer');
            container.appendChild(toast);

            // Animation apparition
            setTimeout(() => {
                toast.classList.remove("scale-95", "opacity-0");
                toast.classList.add("scale-100", "opacity-100");
            }, 100);

            // Bouton fermer
            toast.querySelector('button').addEventListener('click', () => hideToast(toast));

            // Auto hide
            setTimeout(() => hideToast(toast), 5000);
        }

        function hideToast(toast) {
            toast.classList.remove("scale-100", "opacity-100");
            toast.classList.add("scale-95", "opacity-0");
            setTimeout(() => toast.remove(), 300);
        }

        // Vérifier si une notification PHP existe
        <?php if ($notification): ?>
            document.addEventListener('DOMContentLoaded', () => {
                showToast('<?= htmlspecialchars($notification['message']) ?>', '<?= htmlspecialchars($notification['type']) ?>');
                <?php unset($_SESSION['notification']); ?>
            });
        <?php endif; ?>
    </script>



    <!--SEARCH LOGO-->
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