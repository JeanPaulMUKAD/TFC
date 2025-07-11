<?php
class ControleurType
{
    private $conn;

    public function __construct()
    {
        // Connexion à la base de données school
        $this->conn = new mysqli("localhost", "root", "", "school");

        if ($this->conn->connect_error) {
            die("Échec de la connexion à la base de données : " . $this->conn->connect_error);
        }
    }

    // Méthode pour enregistrer un type
    public function enregistrerType($nom_type)
    {
        $stmt = $this->conn->prepare("INSERT INTO payementtype (nom_type) VALUES (?)");
        $stmt->bind_param("s", $nom_type);

        if ($stmt->execute()) {
            return "Type de paiement enregistré avec succès.";
        } else {
            return "Erreur lors de l'enregistrement : " . $this->conn->error;
        }
    }

    // Méthode pour modifier un type existant
    public function modifierType($ancien_nom, $nouveau_nom)
    {
        $stmt = $this->conn->prepare("UPDATE payementtype SET nom_type = ? WHERE nom_type = ?");
        $stmt->bind_param("ss", $nouveau_nom, $ancien_nom);

        if ($stmt->execute()) {
            return "Type modifié avec succès.";
        } else {
            return "Erreur lors de la modification : " . $this->conn->error;
        }
    }

    // Méthode pour supprimer un type
    public function supprimerType($nom_type)
    {
        $stmt = $this->conn->prepare("DELETE FROM payementtype WHERE nom_type = ?");
        $stmt->bind_param("s", $nom_type);

        if ($stmt->execute()) {
            return "Type supprimé avec succès.";
        } else {
            return "Erreur lors de la suppression : " . $this->conn->error;
        }
    }
        
    // Méthode pour afficher tous les types
    public function afficherType()
    {
        $result = $this->conn->query("SELECT * FROM payementtype");

        if ($result->num_rows > 0) {
            $types = [];
            while ($row = $result->fetch_assoc()) {
                $types[] = $row;
            }
            return $types;
        } else {
            return [];
        }
    }
}
?>
