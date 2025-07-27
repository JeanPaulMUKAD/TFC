<?php
require_once __DIR__ . '/fpdf.php'; // inclut la classe FPDF
require_once __DIR__ . '/assets/Controllers/AuthController.php';

$auth = new AuthController();

if (isset($_GET['matricule'])) {
    $matricule = htmlspecialchars(trim($_GET['matricule']));
    $paiements = $auth->obtenirPaiementsParMatricule($matricule);

    if ($paiements['success']) {
        $pdf = new FPDF();
        $pdf->AddPage();
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, "Paiements de l'élève : $matricule", 0, 1, 'C');
        $pdf->SetFont('Arial', '', 12);

        foreach ($paiements['paiements'] as $paiement) {
            $pdf->Cell(0, 10, "Date : {$paiement['date_paiement']} - Montant : {$paiement['montant_payer']} - Motif : {$paiement['motif_paiement']}", 0, 1);
        }

        $pdf->Output('D', "paiements_$matricule.pdf");
    } else {
        echo "Aucun paiement trouvé pour ce matricule.";
    }
} else {
    echo "Matricule manquant.";
}


?>