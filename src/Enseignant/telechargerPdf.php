<?php
session_start();
require_once __DIR__ . '/../../vendor/TCPDF/tcpdf.php';

if (!isset($_SESSION['pdf_data'])) {
    echo "<script>
        alert('Aucune donnée disponible pour générer le PDF.');
        window.location.href = '../../index.php?action=enseignantFicheContrainte';
    </script>";
    exit();
}

$data = $_SESSION['pdf_data'];
$nom_prenom = $data['nom_prenom'];
$choix_contraintes = isset($data['choix_contraintes']) ? array_values($data['choix_contraintes']) : [];
$creneau_preference = isset($data['creneau_prefere']) ? $data['creneau_prefere'] : "Non spécifié";
$cours_samedi = isset($data['cours_samedi']) ? $data['cours_samedi'] : "Non spécifié";

ob_end_clean();
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Fiche de Vœux 2024-2025');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Fiche de Vœux 2024-2025", 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Nom et Prénom : $nom_prenom", 0, 1);
$pdf->Ln(5);

// tableau des contraintes
$pdf->SetFont('helvetica', '', 10);
$pdf->Cell(40, 10, " ", 1, 0, 'C');

$jours = ['lundi', 'mardi', 'mercredi', 'jeudi', 'vendredi'];
foreach ($jours as $jour) {
    $pdf->Cell(30, 10, ucfirst($jour), 1, 0, 'C');
}
$pdf->Ln();

$horaires = ['8h-10h', '10h-12h', '14h-16h', '16h-18h'];
foreach ($horaires as $heure) {
    $pdf->Cell(40, 10, $heure, 1, 0, 'C');
    foreach ($jours as $jour) {
        $heure_correcte = rtrim(str_replace(['h-', 'h'], '_', $heure), '_');
        $key = "{$jour}_{$heure_correcte}";
        $marque = in_array($key, $choix_contraintes, true) ? 'X' : '';
        $pdf->Cell(30, 10, $marque, 1, 0, 'C');
    }
    $pdf->Ln();
}

$pdf->Ln(5);
$pdf->Cell(0, 10, 'Je préfère éviter le créneau : ' . $creneau_preference, 0, 1);
$pdf->Cell(0, 10, 'J\'accepte d\'avoir cours le samedi : ' . $cours_samedi, 0, 1);
$nom_prof = preg_replace('/[^a-zA-Z0-9]/', '_', $nom_prenom);
$pdf_folder = __DIR__ . '/../../temp/';
$pdf_filename = 'FicheVoeux2024-2025_' . $nom_prof . '.pdf';
$pdf_filepath = $pdf_folder . $pdf_filename;

if (!is_dir($pdf_folder)) {
    mkdir($pdf_folder, 0777, true);
}

$pdf->Output($pdf_filepath, 'F');
header("Location: ../../index.php?action=enseignantFicheContrainte");
exit();
?>
