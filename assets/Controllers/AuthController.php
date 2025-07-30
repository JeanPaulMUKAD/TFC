<?php
class AuthController
{
    public $conn;
    private $paymentModel;
    private $table_name = "paiement"; // Nom de votre table de paiements
    private $students_table = "inscriptions"; // Nom de votre table des élèves
    private $parents_table = "utilisateurs"; // Nom de votre table des parents

    // ... (vos propriétés et méthodes existantes, y compris le constructeur) ...

    /**
     * Récupère tous les paiements pour les enfants associés à un parent donné par son nom.
     * Effectue des jointures pour obtenir les détails complets de l'élève et du parent.
     *
     * @param string $parentName Le nom complet du parent (ou le champ que vous utilisez pour l'identification).
     * @return array Les données de paiement si trouvées, ou un tableau vide.
     */

    public function __construct()
    {
        $this->conn = new mysqli("localhost", "root", "", "school");
        if ($this->conn->connect_error) {
            die("Erreur de connexion : " . $this->conn->connect_error);
        }

        session_start();
    }

    //LOGIN
    public function login($names, $password)
    {
        $names = $this->conn->real_escape_string($names);
        $sql = "SELECT * FROM utilisateurs WHERE Names_User = '$names'";
        $result = $this->conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['Password_User'])) {
                $_SESSION['username'] = $row['Names_User'];
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['Role_User'] = $row['Role_User'];
                $this->conn->query("UPDATE utilisateurs SET is_connected = 1 WHERE id = " . $row['id']);

                if ($row['Role_User'] === 'caissier') {
                    header("Location: Dashboad.php");
                } elseif ($row['Role_User'] === 'parent') {
                    header("Location: ./assets/Parent/Acceuil_Parent.php");

                } elseif ($row['Role_User'] === 'prefet') {
                    header("Location: ./assets/Prefet/Acceuil_Prefet.php");

                } elseif ($row['Role_User'] === 'sec') {
                    header("Location: ./assets/Inscription/Acceuil_inscrip.php");
                } elseif ($row['Role_User'] === 'Admin') {
                    header("Location: ./assets/Admin/Acceuil_Admin.php");
                } else {
                    header("Location: ./auth-signin-cover.php");
                }
                exit;
            } else {
                return "<p style='color: red; text-align: center;'>Mot de passe incorrect.</p>";
            }
        } else {
            return "<p style='color: red; text-align: center;'>Nom d'utilisateur incorrect ou inexistant.</p>";
        }
    }

    // REGISTER
    public function register($names, $email, $password, $confirmPassword, $role)
    {
        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => "Les mots de passe ne correspondent pas."];
        }

        $stmtCheckName = $this->conn->prepare("SELECT id FROM utilisateurs WHERE Names_User = ?");
        $stmtCheckName->bind_param("s", $names);
        $stmtCheckName->execute();
        $stmtCheckName->store_result();
        if ($stmtCheckName->num_rows > 0) {
            return ['success' => false, 'message' => "Ce nom d'utilisateur existe déjà."];
        }

        $stmtCheckEmail = $this->conn->prepare("SELECT id FROM utilisateurs WHERE Email = ?");
        $stmtCheckEmail->bind_param("s", $email);
        $stmtCheckEmail->execute();
        $stmtCheckEmail->store_result();
        if ($stmtCheckEmail->num_rows > 0) {
            return ['success' => false, 'message' => "Cette adresse email est déjà utilisée."];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmtInsert = $this->conn->prepare("INSERT INTO utilisateurs (Names_User, Email, Password_User, Role_User, Created_at, is_connected) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmtInsert->bind_param("ssss", $names, $email, $hashedPassword, $role);

        if ($stmtInsert->execute()) {
            // Envoi email
            $subject = "Création de votre compte";
            $headers = "From: administration@ppunilu.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body = "
            <html>
            <body>
                <h2>Bonjour $names,</h2>
                <p>Votre compte a été créé avec succès.</p>
                <p><strong>Rôle :</strong> $role</p>
                <p>Cordialement,<br>L'équipe de l'administration</p>
            </body>
            </html>
        ";
            mail($email, $subject, $body, $headers);

            return ['success' => true, 'message' => "Compte créé avec succès. Un email a été envoyé à $email."];
        } else {
            return ['success' => false, 'message' => "Erreur lors de la création du compte."];
        }
    }



    //RESET PASSWORD
    public function resetPassword($email)
    {
        $stmt = $this->conn->prepare("SELECT id, Names_User FROM utilisateurs WHERE Email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 0) {
            return ['success' => false, 'message' => "Aucun compte trouvé avec cette adresse e-mail."];
        }

        $stmt->bind_result($id, $name);
        $stmt->fetch();

        $new_password = bin2hex(random_bytes(4));
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

        $updateStmt = $this->conn->prepare("UPDATE utilisateurs SET Password_User = ? WHERE Email = ?");
        $updateStmt->bind_param("ss", $hashed_password, $email);

        if ($updateStmt->execute()) {
            $subject = "Mot de passe restauré";
            $headers = "From: administrationcsppunilu@gmail.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";

            $body = "
            <html>
            <body>
                <h2>Bonjour $name,</h2>
                <p>Votre nouveau mot de passe est : <strong>$new_password</strong></p>
                <p>Merci de vous connecter et de le modifier après connexion.</p>
            </body>
            </html>
        ";

            if (mail($email, $subject, $body, $headers)) {
                return ['success' => true, 'message' => "Un nouveau mot de passe a été envoyé à votre adresse e-mail."];
            } else {
                return ['success' => false, 'message' => "Erreur lors de l'envoi de l'e-mail. Veuillez réessayer plus tard."];
            }
        } else {
            return ['success' => false, 'message' => "Erreur lors de la mise à jour du mot de passe."];
        }
    }

    // Ajout d’un parent
    public function addParent($names, $email, $password, $confirmPassword)
    {
        if ($password !== $confirmPassword) {
            return ['success' => false, 'message' => "Les mots de passe ne correspondent pas."];
        }

        $stmtCheckName = $this->conn->prepare("SELECT id FROM utilisateurs WHERE Names_User = ?");
        $stmtCheckName->bind_param("s", $names);
        $stmtCheckName->execute();
        $stmtCheckName->store_result();
        if ($stmtCheckName->num_rows > 0) {
            return ['success' => false, 'message' => "Un parent avec ce nom existe déjà."];
        }

        $stmtCheckEmail = $this->conn->prepare("SELECT id FROM utilisateurs WHERE Email = ?");
        $stmtCheckEmail->bind_param("s", $email);
        $stmtCheckEmail->execute();
        $stmtCheckEmail->store_result();
        if ($stmtCheckEmail->num_rows > 0) {
            return ['success' => false, 'message' => "Email déjà utilisé."];
        }

        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $role = 'parent';

        $stmtInsert = $this->conn->prepare("INSERT INTO utilisateurs (Names_User, Email, Password_User, Role_User, Created_at, is_connected) VALUES (?, ?, ?, ?, NOW(), 0)");
        $stmtInsert->bind_param("ssss", $names, $email, $hashedPassword, $role);

        if ($stmtInsert->execute()) {
            $subject = "Ajout de votre compte parent";
            $headers = "From: administration@ppunilu.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body = "<html><body><h3>Bonjour $names,</h3><p>Votre compte parent a été ajouté avec succès.</p></body></html>";
            mail($email, $subject, $body, $headers);
            return ['success' => true, 'message' => "Parent ajouté avec succès. Un email a été envoyé."];
        } else {
            return ['success' => false, 'message' => "Erreur : " . $stmtInsert->error];
        }
    }

    // Modification d’un parent
    public function modifyParent($oldName, $newName, $newEmail, $newPassword, $confirmPassword)
    {
        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => "Les mots de passe ne correspondent pas."];
        }

        $stmtCheckOld = $this->conn->prepare("SELECT id FROM utilisateurs WHERE Names_User = ? AND Role_User = 'parent'");
        $stmtCheckOld->bind_param("s", $oldName);
        $stmtCheckOld->execute();
        $stmtCheckOld->store_result();
        if ($stmtCheckOld->num_rows === 0) {
            return ['success' => false, 'message' => "Ancien nom introuvable."];
        }

        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        $stmtUpdate = $this->conn->prepare("UPDATE utilisateurs SET Names_User = ?, Email = ?, Password_User = ? WHERE Names_User = ? AND Role_User = 'parent'");
        $stmtUpdate->bind_param("ssss", $newName, $newEmail, $hashedPassword, $oldName);

        if ($stmtUpdate->execute()) {
            $subject = "Modification de votre compte parent";
            $headers = "From: administration@ppunilu.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body = "<html><body><h3>Bonjour $newName,</h3><p>Votre compte a été mis à jour avec succès.</p></body></html>";
            mail($newEmail, $subject, $body, $headers);
            return ['success' => true, 'message' => "Modification réussie. Un email a été envoyé."];
        } else {
            return ['success' => false, 'message' => "Erreur lors de la modification : " . $stmtUpdate->error];
        }
    }

    // Suppression d’un parent
    public function deleteParent($name)
    {
        $stmtCheck = $this->conn->prepare("SELECT Email FROM utilisateurs WHERE Names_User = ? AND Role_User = 'parent'");
        $stmtCheck->bind_param("s", $name);
        $stmtCheck->execute();
        $result = $stmtCheck->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => "Nom introuvable."];
        }

        $row = $result->fetch_assoc();

        $stmtDelete = $this->conn->prepare("DELETE FROM utilisateurs WHERE Names_User = ? AND Role_User = 'parent'");
        $stmtDelete->bind_param("s", $name);

        if ($stmtDelete->execute()) {
            $subject = "Suppression de votre compte parent";
            $headers = "From: administration@ppunilu.com\r\n";
            $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
            $body = "<html><body><h3>Bonjour,</h3><p>Votre compte parent a été supprimé du système.</p></body></html>";
            mail($row['Email'], $subject, $body, $headers);
            return ['success' => true, 'message' => "Parent supprimé avec succès. Un email a été envoyé."];
        } else {
            return ['success' => false, 'message' => "Erreur lors de la suppression : " . $stmtDelete->error];
        }
    }

    //INSCRIPTIONS PAAR CLASSE
    public function getInscriptionsParClasse()
    {
        $stmt = $this->conn->prepare("SELECT * FROM inscriptions ORDER BY classe_selection ASC");
        $stmt->execute();
        $result = $stmt->get_result();
        $inscriptions = [];

        while ($row = $result->fetch_assoc()) {
            $inscriptions[] = $row;
        }

        return $inscriptions;
    }


    // TOUTES LES INSCRIPTIONS
    public function getToutesLesInscriptions()
    {
        $stmt = $this->conn->prepare("SELECT * FROM inscriptions ORDER BY classe_selection, nom_eleve");
        $stmt->execute();
        $result = $stmt->get_result();
        $eleves = [];

        while ($row = $result->fetch_assoc()) {
            $eleves[] = $row;
        }

        return $eleves;
    }





    //PAIEMENT EN LIGNE
    public function handlePaymentAndReport(array $postData): array
    {


        // Récupération sécurisée des données
        $conn = $this->conn;

        // Rapport de paiement
        if (isset($postData['action']) && $postData['action'] === 'fetch_report') {
            $nom_eleve_report = $conn->real_escape_string($postData['nom_eleve_report'] ?? '');
            $classe_report = $conn->real_escape_string($postData['classe_report'] ?? '');

            // total annuel
            $sql_total_annuel = "SELECT total_annuel FROM paiement WHERE classe_eleve = '$classe_report' LIMIT 1";
            $result_total_annuel = $conn->query($sql_total_annuel);
            $total_annuel = ($result_total_annuel && $result_total_annuel->num_rows > 0) ? (float) $result_total_annuel->fetch_assoc()['total_annuel'] : 0;

            // total payé
            $sql_total_paye = "SELECT SUM(montant_payer) as total_paye FROM paiement WHERE nom_eleve = '$nom_eleve_report' AND classe_eleve = '$classe_report'";
            $result_total_paye = $conn->query($sql_total_paye);
            $total_paye = ($result_total_paye && $result_total_paye->num_rows > 0) ? (float) $result_total_paye->fetch_assoc()['total_paye'] : 0;

            // récupérer les paiements
            $sql_report = "SELECT montant_payer, motif_paiement, transaction_id, payment_status, classe_eleve 
                   FROM paiement 
                   WHERE nom_eleve = '$nom_eleve_report' AND classe_eleve = '$classe_report'";

            $result_report = $conn->query($sql_report);

            $payments = [];
            if ($result_report && $result_report->num_rows > 0) {
                while ($row = $result_report->fetch_assoc()) {
                    $row['total_annuel'] = $total_annuel;
                    $row['reste_a_payer'] = max(0, $total_annuel - $total_paye);
                    $payments[] = $row;
                }
            }

            return ['success' => true, 'payments' => $payments];
        }

        if (isset($postData['local_payment'])) {
            if (isset($postData['payment_validated']) && $postData['payment_validated'] == "1") {
                $matricule = $conn->real_escape_string($postData['matricule'] ?? '');
                $nom_eleve = $conn->real_escape_string($postData['nom_eleve'] ?? '');
                $postnom_eleve = $conn->real_escape_string($postData['postnom_eleve'] ?? '');
                $prenom_eleve = $conn->real_escape_string($postData['prenom_eleve'] ?? '');
                $sexe_eleve = $conn->real_escape_string($postData['sexe_eleve'] ?? '');
                $classe_eleve = $conn->real_escape_string($postData['classe_eleve'] ?? '');
                $nom_parent = $conn->real_escape_string($postData['nom_parent'] ?? '');
                $adresse_eleve = $conn->real_escape_string($postData['adresse_eleve'] ?? '');
                $montant_payer = (float) ($postData['montant_payer'] ?? 0);
                $devise = $conn->real_escape_string($postData['devise'] ?? '');
                $motif_paiement = $conn->real_escape_string($postData['motif_paiement'] ?? '');
                $transaction_id = $conn->real_escape_string($postData['transaction_id'] ?? '');


                $sql_total = "SELECT total_annuel FROM paiement WHERE classe_eleve = '$classe_eleve' LIMIT 1";
                $result_total = $conn->query($sql_total);
                $total_annuel = ($result_total && $result_total->num_rows > 0) ? (float) $result_total->fetch_assoc()['total_annuel'] : 0;

                // Calculer le montant déjà payé
                $sql_deja_paye = "SELECT SUM(montant_payer) as total_paye FROM paiement WHERE nom_eleve = '$nom_eleve' AND classe_eleve = '$classe_eleve'";
                $result_paye = $conn->query($sql_deja_paye);
                $total_paye = ($result_paye && $result_paye->num_rows > 0) ? (float) $result_paye->fetch_assoc()['total_paye'] : 0;

                $nouveau_total_paye = $total_paye + $montant_payer;
                $reste_a_payer = $total_annuel - $nouveau_total_paye;

                $sql = "INSERT INTO paiement (
                        matricule, nom_eleve, postnom_eleve, prenom_eleve, sexe_eleve, classe_eleve, nom_parent, adresse_eleve,
                        montant_payer, devise, motif_paiement, transaction_id, payment_status, total_annuel
                    ) VALUES (
                        '$matricule', '$nom_eleve', '$postnom_eleve', '$prenom_eleve', '$sexe_eleve', '$classe_eleve', '$nom_parent', '$adresse_eleve',
                        '$montant_payer', '$devise', '$motif_paiement', '$transaction_id', 'success', '$total_annuel'
                    )";
                if ($conn->query($sql) === TRUE) {
                    return ['success' => true, 'message' => "Paiement enregistré avec succès."];
                } else {
                    return ['success' => false, 'message' => "Le paiement n'a pas été validé. Veuillez réessayer."];
                }
            } else {
                return ['success' => false, 'message' => "Le paiement n'a pas été validé. Veuillez réessayer."];
            }
        }

        // Si aucune condition remplie
        return ['success' => false, 'message' => "Action non reconnue."];
    }

    //HISTORIQUE DES PAIEMENTS
    public function getPaymentHistory()
    {
        $payments = [];
        $paymentsByClass = [];
        $totalUsd = 0;
        $totalFc = 0;
        $percentageChange = 0;
        $percentageClass = "text-success";
        $percentageIcon = "ri-arrow-right-up-line";

        // Récupérer tous les paiements
        $sql = "SELECT * FROM paiement";
        $result = $this->conn->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $payments[] = $row;
            }
        }

        // Calcul total USD (montant_payer contient le signe $)
        $sqlTotalUsd = "SELECT SUM(REPLACE(REPLACE(montant_payer, '$', ''), ',', '') + 0) AS total_usd 
                    FROM paiement WHERE montant_payer LIKE '%$%'";
        $resultUsd = $this->conn->query($sqlTotalUsd);
        if ($resultUsd && $rowUsd = $resultUsd->fetch_assoc()) {
            $totalUsd = (float) $rowUsd['total_usd'];
        }

        // Calcul total Fc (montant_payer contient 'Fc')
        $sqlTotalFc = "SELECT SUM(REPLACE(REPLACE(montant_payer, 'Fc', ''), ',', '') + 0) AS total_fc 
                   FROM paiement WHERE montant_payer LIKE '%Fc%'";
        $resultFc = $this->conn->query($sqlTotalFc);
        if ($resultFc && $rowFc = $resultFc->fetch_assoc()) {
            $totalFc = (float) $rowFc['total_fc'];
        }

        // Nombre total de paiements par classe
        $sqlPaymentsByClass = "SELECT classe_eleve, COUNT(*) AS total_paiements 
                          FROM paiement WHERE montant_payer IS NOT NULL GROUP BY classe_eleve";
        $resultByClass = $this->conn->query($sqlPaymentsByClass);
        if ($resultByClass && $resultByClass->num_rows > 0) {
            while ($rowClass = $resultByClass->fetch_assoc()) {
                $paymentsByClass[$rowClass['classe_eleve']] = (int) $rowClass['total_paiements'];
            }
        }

        // Valeur précédente pour calcul du pourcentage (à adapter selon contexte)
        $previousPayments = 100; // Valeur de référence, par ex. stockée dans une config ou une autre table
        $currentPayments = array_sum($paymentsByClass);

        if ($previousPayments > 0) {
            $percentageChange = (($currentPayments - $previousPayments) / $previousPayments) * 100;
        }

        $percentageClass = $percentageChange >= 0 ? "text-success" : "text-danger";
        $percentageIcon = $percentageChange >= 0 ? "ri-arrow-right-up-line" : "ri-arrow-right-down-line";

        return [
            'payments' => $payments,
            'total_usd' => $totalUsd,
            'total_fc' => $totalFc,
            'payments_by_class' => $paymentsByClass,
            'percentage_change' => $percentageChange,
            'percentage_class' => $percentageClass,
            'percentage_icon' => $percentageIcon
        ];
    }

    //PAIEMENT EN PRESENTIEL
    public function processCashPayment(
        $matricule,
        $nom_eleve,
        $postnom_eleve,
        $prenom_eleve,
        $sexe_eleve,
        $classe_eleve,
        $nom_parent,
        $adresse_eleve,
        $montant_payer,
        $devise1,
        $devise,
        $motif_paiement,
        $total_annuel
    ) {
        $transaction_id = uniqid();
        $montant_payer_str = $montant_payer . $devise;
        $total_annuel_str = $total_annuel . $devise1;

        $stmt = $this->conn->prepare("INSERT INTO paiement 
                            (matricule, nom_eleve, postnom_eleve, prenom_eleve, sexe_eleve, 
                             classe_eleve, nom_parent, adresse_eleve, montant_payer, motif_paiement, transaction_id, 
                             payment_status, total_annuel) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'success', ?)");

        $stmt->bind_param(
            "ssssssssssss",
            $matricule,
            $nom_eleve,
            $postnom_eleve,
            $prenom_eleve,
            $sexe_eleve,
            $classe_eleve,
            $nom_parent,
            $adresse_eleve,
            $montant_payer_str,
            $motif_paiement,
            $transaction_id,
            $total_annuel_str
        );

        if ($stmt->execute()) {
            return ['success' => true, 'message' => "Paiement enregistré avec succès"];
        } else {
            return ['success' => false, 'message' => "Erreur: " . $stmt->error];
        }
    }
    //GERER LDES INFORMATIONS DE L'ELEVE
    public function getStudentInfoByMatricule($matricule)
    {
        $conn = $this->conn;
        $matricule = trim($matricule);

        if (empty($matricule)) {
            return ['success' => false, 'message' => 'Matricule non fourni'];
        }

        $stmt = $conn->prepare("SELECT nom_eleve, postnom_eleve, prenom_eleve, sexe_eleve, 
                           classe_selection, nom_parent, adresse_eleve 
                           FROM inscriptions WHERE matricule = ?");
        if (!$stmt) {
            return ['success' => false, 'message' => "Erreur SQL: " . $conn->error];
        }

        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => "Aucun élève trouvé avec ce matricule"];
        }

        $row = $result->fetch_assoc();
        return [
            'success' => true,
            'nom_eleve' => $row['nom_eleve'],
            'postnom_eleve' => $row['postnom_eleve'],
            'prenom_eleve' => $row['prenom_eleve'],
            'sexe_eleve' => $row['sexe_eleve'],
            'classe_selection' => $row['classe_selection'],
            'nom_parent' => $row['nom_parent'],
            'adresse_eleve' => $row['adresse_eleve']
        ];
    }
    //RECHERCGER ELEVE PAR SON MATRICULE
    public function rechercherEleveParMatricule($matricule)
    {
        // Valider le matricule
        if (empty($matricule)) {
            return ['success' => false, 'message' => 'Le matricule est requis'];
        }

        try {
            $stmt = $this->conn->prepare("SELECT * FROM inscriptions WHERE matricule = ?");
            $stmt->bind_param("s", $matricule);

            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($result->num_rows > 0) {
                    return [
                        'success' => true,
                        'eleve' => $result->fetch_assoc()
                    ];
                } else {
                    return ['success' => false, 'message' => 'Aucun élève trouvé avec ce matricule'];
                }
            } else {
                return ['success' => false, 'message' => 'Erreur de base de données'];
            }
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Erreur: ' . $e->getMessage()];
        }
    }




    //DASHBOARD
    public function getDashboardStatistics()
    {
        $currentConnected = 0;
        $connectedUsers = [];
        $paymentsByClass = [];
        $currentPayments = 0;


        // Nombre d'utilisateurs connectés
        $stmt = $this->conn->prepare("SELECT COUNT(*) AS total_connected FROM utilisateurs WHERE is_connected = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $currentConnected = $row['total_connected'];
        }

        // Récupérer les noms des utilisateurs connectés
        $stmt = $this->conn->prepare("SELECT Names_User FROM utilisateurs WHERE is_connected = 1");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $connectedUsers[] = $row['Names_User'];
        }

        // Paiements par classe
        $stmt = $this->conn->prepare("SELECT classe_eleve, COUNT(*) AS total_paiements FROM paiement WHERE montant_payer IS NOT NULL GROUP BY classe_eleve");
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $paymentsByClass[$row['classe_eleve']] = $row['total_paiements'];
        }

        $currentPayments = array_sum($paymentsByClass);
        $previousPayments = 100; // valeur fictive ou à récupérer dynamiquement

        $paymentPercentageChange = ($previousPayments > 0) ? (($currentPayments - $previousPayments) / $previousPayments) * 100 : 0;
        $paymentPercentageClass = $paymentPercentageChange >= 0 ? "text-success" : "text-danger";
        $paymentPercentageIcon = $paymentPercentageChange >= 0 ? "ri-arrow-right-up-line" : "ri-arrow-right-down-line";

        return [
            'currentConnected' => $currentConnected,
            'connectedUsers' => $connectedUsers,
            'username' => $_SESSION['username'],
            'paymentsByClass' => $paymentsByClass,
            'currentPayments' => $currentPayments,
            'paymentPercentageChange' => $paymentPercentageChange,
            'paymentPercentageClass' => $paymentPercentageClass,
            'paymentPercentageIcon' => $paymentPercentageIcon,
        ];



    }
    // INSCRIPTION DES ELEVES
    public function enregistrerEleve($nom, $postnom, $prenom, $sexe, $classe, $nom_parent, $adresse, $annee)
    {
        $conn = $this->conn;

        $stmt = $conn->prepare("INSERT INTO inscriptions (nom_eleve, postnom_eleve, prenom_eleve, sexe_eleve, classe_selection, nom_parent, adresse_eleve, annee_inscription) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            return ['success' => false, 'message' => "Erreur SQL préparation : " . $conn->error];
        }
        $stmt->bind_param("ssssssss", $nom, $postnom, $prenom, $sexe, $classe, $nom_parent, $adresse, $annee);

        if ($stmt->execute()) {
            $lastId = $conn->insert_id;

            $annee_suffix = substr($annee, -2);
            $matricule = strtoupper($annee_suffix . substr($nom, 0, 1) . substr($postnom, 0, 1) . $lastId);

            $stmt_update = $conn->prepare("UPDATE inscriptions SET matricule = ? WHERE id = ?");
            if (!$stmt_update) {
                return ['success' => false, 'message' => "Erreur SQL update matricule : " . $conn->error];
            }
            $stmt_update->bind_param("si", $matricule, $lastId);
            $stmt_update->execute();

            return ['success' => true, 'message' => "Inscription réussie. Voici le matricule de l'élève: $matricule"];
        } else {
            return ['success' => false, 'message' => "Erreur d'inscription de l'élève : " . $stmt->error];
        }
    }
    //MODIFIER ELEVE

    public function modifierEleve($id, $nom, $postnom, $prenom, $sexe, $classe, $nom_parent, $adresse, $annee)
    {
        $conn = $this->conn;

        $stmt = $conn->prepare("UPDATE inscriptions SET nom_eleve=?, postnom_eleve=?, prenom_eleve=?, sexe_eleve=?, classe_selection=?, nom_parent=?, adresse_eleve=?, annee_inscription=? WHERE id=?");
        if (!$stmt) {
            return ['success' => false, 'message' => "Erreur SQL préparation modification : " . $conn->error];
        }
        $stmt->bind_param("ssssssssi", $nom, $postnom, $prenom, $sexe, $classe, $nom_parent, $adresse, $annee, $id);

        if ($stmt->execute()) {
            $annee_suffix = substr($annee, -2);
            $matricule = strtoupper($annee_suffix . substr($nom, 0, 1) . substr($postnom, 0, 1) . $id);

            $stmt_update = $conn->prepare("UPDATE inscriptions SET matricule=? WHERE id=?");
            if (!$stmt_update) {
                return ['success' => false, 'message' => "Erreur SQL update matricule : " . $conn->error];
            }
            $stmt_update->bind_param("si", $matricule, $id);
            $stmt_update->execute();

            return ['success' => true, 'message' => "Modification réussie. Nouveau matricule: $matricule"];
        } else {
            return ['success' => false, 'message' => "Erreur lors de la modification: " . $stmt->error];
        }
    }

    public function modifierEleves($data)
    {
        $conn = $this->conn;
        $id = $data['id'];
        $nom = $data['nom_eleve'];
        $postnom = $data['postnom_eleve'];
        $prenom = $data['prenom_eleve'];
        $sexe = $data['sexe_eleve'];
        $classe = $data['classe_selection'];
        $nom_parent = $data['nom_parent'];
        $adresse = $data['adresse_eleve'];
        $annee = $data['annee_inscription'];

        $stmt = $conn->prepare("UPDATE inscriptions SET nom_eleve=?, postnom_eleve=?, prenom_eleve=?, sexe_eleve=?, classe_selection=?, nom_parent=?, adresse_eleve=?, annee_inscription=? WHERE id=?");
        if (!$stmt) {
            error_log("Erreur SQL préparation modification : " . $conn->error);
            return false;
        }
        $stmt->bind_param("ssssssssi", $nom, $postnom, $prenom, $sexe, $classe, $nom_parent, $adresse, $annee, $id);

        if ($stmt->execute()) {
            $annee_suffix = substr($annee, -2);
            $matricule = strtoupper($annee_suffix . substr($nom, 0, 1) . substr($postnom, 0, 1) . $id);

            $stmt_update = $conn->prepare("UPDATE inscriptions SET matricule=? WHERE id=?");
            if (!$stmt_update) {
                error_log("Erreur SQL update matricule : " . $conn->error);
                return false;
            }
            $stmt_update->bind_param("si", $matricule, $id);
            $stmt_update->execute();
            return true; // Retourne true si tout va bien
        } else {
            error_log("Erreur lors de la modification: " . $stmt->error);
            return false;
        }
    }



    //SUPPRIMER ELEVE
    public function supprimerEleveParMatricule($matricule)
    {
        $conn = $this->conn;

        $stmt = $conn->prepare("SELECT id FROM inscriptions WHERE matricule=?");
        if (!$stmt) {
            return ['success' => false, 'message' => "Erreur SQL préparation sélection : " . $conn->error];
        }
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            return ['success' => false, 'message' => "Matricule incorrect ou introuvable."];
        }

        $row = $result->fetch_assoc();
        $id = $row['id'];

        $stmt_del = $conn->prepare("DELETE FROM inscriptions WHERE id=?");
        if (!$stmt_del) {
            return ['success' => false, 'message' => "Erreur SQL préparation suppression : " . $conn->error];
        }
        $stmt_del->bind_param("i", $id);

        if ($stmt_del->execute()) {
            return ['success' => true, 'message' => "Suppression réussie pour le matricule $matricule."];
        } else {
            return ['success' => false, 'message' => "Erreur lors de la suppression : " . $stmt_del->error];
        }
    }

    public function obtenirInfosEleveParId($id)
    {
        $conn = $this->conn;
        $stmt = $conn->prepare("SELECT * FROM inscriptions WHERE id = ? LIMIT 1");
        if (!$stmt) {
            error_log("Erreur SQL préparation obtenirInfosEleveParId : " . $conn->error);
            return false;
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            return $result->fetch_assoc();
        } else {
            return false;
        }
    }

    public function supprimerEleve($id)
    {
        $conn = $this->conn;
        $stmt_del = $conn->prepare("DELETE FROM inscriptions WHERE id=?");
        if (!$stmt_del) {
            error_log("Erreur SQL préparation suppression : " . $conn->error);
            return false;
        }
        $stmt_del->bind_param("i", $id);

        if ($stmt_del->execute()) {
            return true;
        } else {
            error_log("Erreur lors de la suppression : " . $stmt_del->error);
            return false;
        }
    }

    //Caissier_Rapport(Inscription)

    public function obtenirInfosEleveParMatricule($matricule)
    {
        // Vérifie si la connexion est bien établie
        if (!$this->conn) {
            return ['success' => false, 'message' => 'Connexion à la base de données non disponible.'];
        }

        $conn = $this->conn;

        $stmt = $conn->prepare("SELECT * FROM inscriptions WHERE matricule = ?");
        if (!$stmt) {
            return ['success' => false, 'message' => 'Erreur de préparation de la requête : ' . $conn->error];
        }

        $stmt->bind_param("s", $matricule);
        if (!$stmt->execute()) {
            return ['success' => false, 'message' => 'Erreur lors de l\'exécution : ' . $stmt->error];
        }

        $result = $stmt->get_result();
        if (!$result) {
            return ['success' => false, 'message' => 'Erreur lors de la récupération du résultat : ' . $stmt->error];
        }

        if ($eleve = $result->fetch_assoc()) {
            return ['success' => true, 'eleve' => $eleve];
        } else {
            return ['success' => false, 'message' => "Aucun élève trouvé avec ce matricule."];
        }
    }

    //OBTENIR PAIEMENTS PR MATRICULE
    public function obtenirPaiementsParMatricule($matricule)
    {
        // Valider le matricule
        if (empty($matricule)) {
            return ['success' => false, 'message' => 'Le matricule est requis'];
        }

        $stmt = $this->conn->prepare("SELECT * FROM paiement WHERE matricule = ? ORDER BY date_paiement DESC");
        $stmt->bind_param("s", $matricule);

        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $paiements = $result->fetch_all(MYSQLI_ASSOC);

            return ['success' => true, 'paiements' => $paiements];
        } else {
            return ['success' => false, 'message' => 'Erreur de base de données'];
        }
    }


    public function __destruct()
    {
        $this->conn->close();
    }

    public function deletePayment($paymentId)
    {
        // Utilisez la méthode de suppression de votre modèle de paiement
        return $this->paymentModel->delete($paymentId);
    }

    public function getPaiementsByParentName($parentName)
    {
        error_log("Début de getPaiementsByParentName dans AuthController pour parent: " . $parentName);

        // Assurez-vous que $this->conn est bien un objet mysqli valide
        if (!$this->conn) {
            error_log("Erreur: La connexion à la base de données n'est pas établie dans getPaiementsByParentName (AuthController).");
            return false;
        }

        $query = "SELECT
            i.matricule,
            p.montant_payer,
            p.motif_paiement,
            p.date_paiement,
            i.nom_eleve AS nom_eleve,
            i.postnom_eleve AS postnom_eleve,
            i.prenom_eleve AS prenom_eleve,
            i.sexe_eleve AS sexe_eleve,
            i.classe_selection AS classe_eleve,
            p.total_annuel AS total_annuel
        FROM
            " . $this->table_name . " p
        JOIN
            " . $this->students_table . " i ON p.matricule = i.matricule
        JOIN
            " . $this->parents_table . " u ON i.parent_id = u.id 
        WHERE
            u.Names_User = ?";

        $stmt = $this->conn->prepare($query);

        if (!$stmt) {
            error_log("Erreur de préparation de la requête SQL dans AuthController::getPaiementsByParentName: " . $this->conn->error);
            return false;
        }

        $stmt->bind_param("s", $parentName);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            error_log("Erreur d'exécution de la requête ou de récupération des résultats dans AuthController::getPaiementsByParentName: " . $stmt->error);
            return false;
        }

        $paiements = $result->fetch_all(MYSQLI_ASSOC);
        error_log("Requête SQL réussie. Nombre de paiements trouvés: " . count($paiements));
        return $paiements;
    }

    // La fonction qui utilise maintenant la version interne de getPaiementsByParentName
    public function obtenirPaiementsParNomParent($parentName)
    {
        error_log("Appel de obtenirPaiementsParNomParent dans AuthController avec parentName: " . $parentName);
        try {
            // APPEL MODIFIÉ : on appelle la méthode directement sur l'objet courant ($this)
            $paiements = $this->getPaiementsByParentName($parentName);

            // Log le résultat brut de la méthode interne
            error_log("Résultat brut de getPaiementsByParentName (interne): " . json_encode($paiements));

            if ($paiements !== false && !empty($paiements)) {
                foreach ($paiements as &$paiement) {
                    $paiement['montant_payer_numeric'] = (float) filter_var($paiement['montant_payer'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    $paiement['total_annuel_numeric'] = (float) filter_var($paiement['total_annuel'] ?? 0, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                }
                error_log("Paiements formatés pour la réponse: " . json_encode($paiements));
                return ['success' => true, 'paiements' => $paiements];
            } else {
                error_log("Aucun paiement trouvé ou échec de la méthode interne pour parent: " . $parentName);
                return ['success' => false, 'message' => 'Aucun historique de paiement trouvé pour les enfants de ce parent.'];
            }
        } catch (Exception $e) {
            error_log("Erreur critique dans obtenirPaiementsParNomParent (AuthController): " . $e->getMessage());
            return ['success' => false, 'message' => 'Une erreur est survenue lors de la récupération des données (Controller): ' . $e->getMessage()];
        }
    }



}



// Gestion des requêtes AJAX
if (isset($_GET['action']) && $_GET['action'] == 'rechercherEleve') {
    $auth = new AuthController();
    $matricule = $_GET['matricule'] ?? '';
    $result = $auth->rechercherEleveParMatricule($matricule);
    header('Content-Type: application/json');
    echo json_encode($result);
    exit;
}
?>