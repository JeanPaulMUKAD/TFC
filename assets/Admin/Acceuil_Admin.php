<?php
    require_once __DIR__ . '/../Controllers/AuthController.php';
    $auth = new AuthController();

    // Initialisation de $_SESSION['username']
    if (!isset($_SESSION['username'])) {
        $_SESSION['username'] = 'Admin'; 
    }

    // Récupérer le nombre total de types de paiement
    $paymentCount = 0;
    $sql2 = "SELECT COUNT(*) as total FROM payementtype";
    $result2 = $auth->conn->query($sql2);
    if ($result2 && $row2 = $result2->fetch_assoc()) {
        $paymentCount = (int) $row2['total'];
    }

    // Récupérer tous les types de paiement pour l'affichage initial
    $paymentTypes = [];
    $sql_payments = "SELECT id, nom_type FROM payementtype";
    $result_payments = $auth->conn->query($sql_payments);
    if ($result_payments && $result_payments->num_rows > 0) {
        while ($row = $result_payments->fetch_assoc()) {
            $paymentTypes[] = $row;
        }
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
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="/assets/images/logo_pp.png">
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
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }

        .hover-card-shadow:hover {
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
    </style>
</head>

<body class="bg-gray-100 text-black">
    <div id="toast-container" class="fixed bottom-5 right-5 z-[100] space-y-2"></div>
    
    <nav class="fixed top-0 left-0 right-0 z-50 flex justify-between items-center px-6 py-4 bg-white shadow-md">
        <div class="text-2xl font-bold flex items-center">
            <img src="/assets/images/logo_pp2.png" alt="Logo" class="h-10 w-10 mr-2">
            <span class="text-black">C.S.P.P</span><span class="text-indigo-500 font-extrabold">.UNILU</span>
        </div>
        <div class="flex items-center space-x-4">
            <a href="mailto:administrationcsppunilu@gmail.com" class="text-sm font-medium text-gray-700 hover:text-indigo-600 transition duration-200">Aide</a>
            <a href="/logoutAdmin"
                class="bg-gradient-to-r from-blue-600 to-indigo-500 hover:from-indigo-600 hover:to-blue-500 text-white font-semibold py-2 px-4 rounded-full text-sm">
                Se déconnecter
            </a>
        </div>
    </nav>
    
    <div class="container mx-auto px-4 py-8 mt-20">
        <header class="flex items-center justify-between mb-8 bg-white p-6 rounded-xl shadow-lg">
            <h1 class="text-3xl font-extrabold text-gray-800">Tableau de bord de l'Administrateur</h1>
            <span class="bg-indigo-100 text-indigo-700 px-4 py-2 rounded-full text-sm font-semibold flex items-center space-x-2">
                <i class="fas fa-shield-alt"></i>
                <span>Espace Administrateur</span>
            </span>
        </header>

        <section class="mb-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Actions rapides</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <a href="Account_User.php"
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
            <div id="defaultMessage" class="text-center text-gray-500 py-10">
                <i class="fas fa-info-circle fa-2x mb-4 text-gray-400"></i>
                <p class="text-lg">Sélectionnez une option ci-dessus pour afficher les données.</p>
            </div>
            
            <div id="paymentFormContainer" class="p-6 bg-white rounded-xl shadow-lg" style="display: none;">
                <h3 class="text-2xl font-bold text-gray-800 mb-6">Gérer les types de paiement</h3>
                <form id="paymentForm" class="space-y-4">
                    <input type="hidden" id="paymentId" name="id">
                    <div>
                        <label for="paymentTypeName" class="block text-sm font-medium text-gray-700">Nom du type de paiement</label>
                        <input type="text" id="paymentTypeName" name="nom_type"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                            required>
                    </div>
                    <div class="flex justify-end space-x-2">
                        <button type="button" id="cancelFormButton" class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-200 rounded-md hover:bg-gray-300 transition-colors">Annuler</button>
                        <button type="submit"
                            class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 transition-colors">Enregistrer</button>
                    </div>
                </form>
            </div>

            <div id="paymentTableContainer" class="mt-8 overflow-x-auto" style="display: none;">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="text-2xl font-bold text-gray-800">Types de paiements existants</h3>
                    <button id="addPaymentButton" class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold py-2 px-4 rounded-full text-sm transition-colors">
                        <i class="fas fa-plus mr-2"></i>Ajouter un type
                    </button>
                </div>
                <table class="min-w-full divide-y divide-gray-200 shadow-lg rounded-xl">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nom du type</th>
                            <th scope="col" class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody id="paymentTableBody" class="bg-white divide-y divide-gray-200">
                        <?php if (!empty($paymentTypes)): ?>
                            <?php foreach ($paymentTypes as $type): ?>
                                <tr data-id="<?= htmlspecialchars($type['id']) ?>">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($type['nom_type']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <button class="text-indigo-600 hover:text-indigo-900 mr-4 edit-btn" title="Modifier"><i class="fas fa-edit"></i></button>
                                        <button class="text-red-600 hover:text-red-900 delete-btn" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">Aucun type de paiement n'est encore enregistré.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <div id="deleteModal" class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-50" style="display: none;">
                <div class="flex items-center justify-center min-h-screen p-4">
                    <div class="bg-white rounded-lg shadow-xl max-w-sm w-full p-6 text-center">
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
                            <i class="fas fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <h3 class="mt-4 text-lg leading-6 font-medium text-gray-900">Confirmer la suppression</h3>
                        <div class="mt-2">
                            <p class="text-sm text-gray-500">Êtes-vous sûr de vouloir supprimer cet élément ? Cette action est irréversible.</p>
                        </div>
                        <div class="mt-5 sm:mt-6 space-x-2">
                            <button type="button" id="cancelDeleteButton"
                                class="inline-flex justify-center w-full rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">Annuler</button>
                            <button type="button" id="confirmDeleteButton"
                                class="inline-flex justify-center w-full rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none sm:w-auto sm:text-sm">Oui, supprimer</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
    
    <script>
        const defaultMessage = document.getElementById('defaultMessage');
        const paymentFormContainer = document.getElementById('paymentFormContainer');
        const paymentTableContainer = document.getElementById('paymentTableContainer');
        const paymentTableBody = document.getElementById('paymentTableBody');
        const paymentForm = document.getElementById('paymentForm');
        const paymentIdInput = document.getElementById('paymentId');
        const paymentTypeNameInput = document.getElementById('paymentTypeName');
        const addPaymentButton = document.getElementById('addPaymentButton');
        const cancelFormButton = document.getElementById('cancelFormButton');
        const deleteModal = document.getElementById('deleteModal');
        const cancelDeleteButton = document.getElementById('cancelDeleteButton');
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');

        function showToast(message, type = 'success') {
            const toastContainer = document.getElementById('toast-container');
            const toast = document.createElement('div');
            
            let bgColor = 'bg-green-500';
            let icon = '<i class="fas fa-check-circle mr-2"></i>';

            if (type === 'error') {
                bgColor = 'bg-red-500';
                icon = '<i class="fas fa-times-circle mr-2"></i>';
            } else if (type === 'info') {
                bgColor = 'bg-blue-500';
                icon = '<i class="fas fa-info-circle mr-2"></i>';
            }

            toast.innerHTML = `
                <div class="relative ${bgColor} text-white px-6 py-4 rounded-lg shadow-xl flex items-center animate-fade-in-right">
                    ${icon}
                    <span>${message}</span>
                </div>
            `;
            
            toast.classList.add('animate-slide-in');
            toastContainer.appendChild(toast);

            setTimeout(() => {
                toast.classList.remove('animate-slide-in');
                toast.classList.add('animate-slide-out');
            }, 3000);

            setTimeout(() => {
                toast.remove();
            }, 3500);
        }

        async function loadPaymentTypes() {
            try {
                const response = await fetch('get_payment_types.php');
                
                if (!response.ok) {
                    throw new Error(`Erreur réseau: ${response.statusText}`);
                }
                
                const paymentTypes = await response.json();
                
                let html = '';
                if (paymentTypes.length > 0) {
                    paymentTypes.forEach(item => {
                        html += `
                            <tr data-id="${item.id}">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">${item.nom_type}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900 mr-4 edit-btn" title="Modifier"><i class="fas fa-edit"></i></button>
                                    <button class="text-red-600 hover:text-red-900 delete-btn" title="Supprimer"><i class="fas fa-trash-alt"></i></button>
                                </td>
                            </tr>
                        `;
                    });
                } else {
                    html = `
                        <tr>
                            <td colspan="2" class="px-6 py-4 text-center text-sm text-gray-500">Aucun type de paiement n'est encore enregistré.</td>
                        </tr>
                    `;
                }
                paymentTableBody.innerHTML = html;
            } catch (error) {
                console.error('Erreur lors du chargement des types de paiement:', error);
                showToast('Erreur lors du chargement des types de paiement.', 'error');
            }
        }

        document.getElementById('paymentTypeLink').addEventListener('click', (e) => {
            e.preventDefault();
            defaultMessage.style.display = 'none';
            paymentTableContainer.style.display = 'block';
            paymentFormContainer.style.display = 'none';
            loadPaymentTypes();
        });

        addPaymentButton.addEventListener('click', () => {
            paymentFormContainer.style.display = 'block';
            paymentTableContainer.style.display = 'none';
            paymentForm.reset();
            paymentIdInput.value = '';
        });

        cancelFormButton.addEventListener('click', () => {
            paymentFormContainer.style.display = 'none';
            paymentTableContainer.style.display = 'block';
        });

        paymentForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            
            const formData = new FormData(paymentForm);
            const isUpdate = !!paymentIdInput.value;
            formData.append('action', isUpdate ? 'update' : 'add');

            try {
                const response = await fetch('payment_handler.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Erreur serveur: ${response.statusText}`);
                }

                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message);
                    paymentFormContainer.style.display = 'none';
                    paymentTableContainer.style.display = 'block';
                    loadPaymentTypes();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Une erreur est survenue. Veuillez réessayer.', 'error');
            }
        });

        paymentTableBody.addEventListener('click', (e) => {
            const row = e.target.closest('tr');
            if (!row) return;

            const id = row.dataset.id;
            
            if (e.target.closest('.delete-btn')) {
                deleteModal.style.display = 'block';
                confirmDeleteButton.dataset.id = id;
            }
            
            if (e.target.closest('.edit-btn')) {
                const nom_type = row.querySelector('td:first-child').textContent;
                
                paymentIdInput.value = id;
                paymentTypeNameInput.value = nom_type;
                
                paymentFormContainer.style.display = 'block';
                paymentTableContainer.style.display = 'none';
            }
        });
        
        confirmDeleteButton.addEventListener('click', async () => {
            const idToDelete = confirmDeleteButton.dataset.id;
            const formData = new FormData();
            formData.append('action', 'delete');
            formData.append('id', idToDelete);

            try {
                const response = await fetch('payment_handler.php', {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    throw new Error(`Erreur serveur: ${response.statusText}`);
                }
                
                const result = await response.json();
                
                if (result.success) {
                    showToast(result.message);
                    deleteModal.style.display = 'none';
                    loadPaymentTypes();
                } else {
                    showToast(result.message, 'error');
                }
            } catch (error) {
                console.error('Erreur:', error);
                showToast('Une erreur est survenue lors de la suppression.', 'error');
            }
        });
        
        cancelDeleteButton.addEventListener('click', () => {
            deleteModal.style.display = 'none';
        });
    </script>



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