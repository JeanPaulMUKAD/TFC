<?php
class AuthController
{
    private $conn;

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
                }elseif ($row['Role_User'] === 'Admin') {
                    header("Location: ./assets/Comptes/Acceuil_Admin.php");
                }else {
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

    //PAIEMENT EN LIGNE
    public function handlePaymentAndReport(array $postData): array
    {
        // Cette fonction prend les données $_POST en paramètre
        // et retourne un tableau avec 'success' bool et 'message' ou 'payments'

        // Récupération sécurisée des données
        $conn = $this->conn;

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
                $nom_eleve = $conn->real_escape_string($postData['nom_eleve'] ?? '');
                $classe_selection = $conn->real_escape_string($postData['classe_selection'] ?? '');
                $montant_payer = (float) ($postData['montant_payer'] ?? 0);
                $devise = $conn->real_escape_string($postData['devise'] ?? '');
                $motif_paiement = $conn->real_escape_string($postData['motif_paiement'] ?? '');
                $transaction_id = $conn->real_escape_string($postData['transaction_id'] ?? '');

                // Vérifier le total annuel
                $sql_total = "SELECT total_annuel FROM paiement WHERE classe_eleve = '$classe_selection' LIMIT 1";
                $result_total = $conn->query($sql_total);
                $total_annuel = ($result_total && $result_total->num_rows > 0) ? (float) $result_total->fetch_assoc()['total_annuel'] : 0;

                // Calculer le montant déjà payé
                $sql_deja_paye = "SELECT SUM(montant_payer) as total_paye FROM paiement WHERE nom_eleve = '$nom_eleve' AND classe_selection = '$classe_selection'";
                $result_paye = $conn->query($sql_deja_paye);
                $total_paye = ($result_paye && $result_paye->num_rows > 0) ? (float) $result_paye->fetch_assoc()['total_paye'] : 0;

                $nouveau_total_paye = $total_paye + $montant_payer;
                $reste_a_payer = $total_annuel - $nouveau_total_paye;

                $sql = "INSERT INTO paiement (nom_eleve, classe_eleve, montant_payer, motif_paiement, transaction_id, payment_status, classe_selection, total_annuel)
                    VALUES ('$nom_eleve', '$classe_selection', '$montant_payer', '$motif_paiement', '$transaction_id', 'success', '$classe_selection', '$total_annuel')";

                if ($conn->query($sql) === TRUE) {
                    return ['success' => true, 'message' => "Paiement enregistré avec succès."];
                } else {
                    return ['success' => false, 'message' => "Erreur lors de l'enregistrement du paiement : " . $conn->error];
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
        $classe_eleve, // On utilise uniquement ce champ maintenant
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
                                 classe_eleve, montant_payer, motif_paiement, transaction_id, 
                                 payment_status, total_annuel) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'success', ?)");

        $stmt->bind_param(
            "ssssssssss",
            $matricule,
            $nom_eleve,
            $postnom_eleve,
            $prenom_eleve,
            $sexe_eleve,
            $classe_eleve,
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
    public function rechercherEleveParMatricule($matricule)
    {
        $stmt = $this->conn->prepare("SELECT * FROM inscriptions WHERE matricule = ?");
        $stmt->bind_param("s", $matricule);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $eleve = $result->fetch_assoc();
            return ['success' => true, 'eleve' => $eleve];
        } else {
            return ['success' => false, 'message' => "Aucun élève trouvé avec ce matricule"];
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
    public function enregistrerEleve($nom, $postnom, $prenom, $sexe, $classe, $annee)
    {
        $conn = $this->conn;

        $stmt = $conn->prepare("INSERT INTO inscriptions (nom_eleve, postnom_eleve, prenom_eleve, sexe_eleve, classe_selection, annee_inscription) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            return ['success' => false, 'message' => "Erreur SQL préparation : " . $conn->error];
        }
        $stmt->bind_param("ssssss", $nom, $postnom, $prenom, $sexe, $classe, $annee);

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

    public function modifierEleve($id, $nom, $postnom, $prenom, $sexe, $classe, $annee)
    {
        $conn = $this->conn;

        $stmt = $conn->prepare("UPDATE inscriptions SET nom_eleve=?, postnom_eleve=?, prenom_eleve=?, sexe_eleve=?, classe_selection=?, annee_inscription=? WHERE id=?");
        if (!$stmt) {
            return ['success' => false, 'message' => "Erreur SQL préparation modification : " . $conn->error];
        }
        $stmt->bind_param("ssssssi", $nom, $postnom, $prenom, $sexe, $classe, $annee, $id);

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

    public function obtenirPaiementsParMatricule($matricule) {
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