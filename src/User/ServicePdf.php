<?php
session_start();
require_once __DIR__ . '/../../vendor/TCPDF/tcpdf.php';

if (!isset($_SESSION['pdf_data'])) {
    echo "<script>
        alert('Aucune donnée disponible pour générer le PDF.');
        window.location.href = '../index.php?action=fichePrevisionnelle';
    </script>";
    exit();
}

$data = $_SESSION['pdf_data'];
$enseignant = $data['enseignant'];
$voeux_septembre = $data['voeux_septembre'];
$voeux_janvier = $data['voeux_janvier'];
$voeux_hors_iut = $data['voeux_hors_iut'];

ob_end_clean();
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Fiche Prévisionnelle de Service 2024-2025');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Fiche Prévisionnelle de Service 2024-2025", 0, 1, 'C');
$pdf->Ln(5);
$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Nom de l'enseignant : " . $enseignant, 0, 1);
$pdf->Ln(5);

function generateTable($pdf, $title, $data) {
    $pdf->SetFont('helvetica', 'B', 12);
    $pdf->Cell(0, 10, $title, 0, 1, 'L');
    $pdf->SetFont('helvetica', '', 10);
    $pdf->Ln(2);

    // En-tête du tableau
    $pdf->SetFillColor(230, 230, 230);
    $pdf->Cell(40, 10, "Ressource", 1, 0, 'C', 1);
    $pdf->Cell(20, 10, "Sem.", 1, 0, 'C', 1);
    $pdf->Cell(20, 10, "CM", 1, 0, 'C', 1);
    $pdf->Cell(20, 10, "TD", 1, 0, 'C', 1);
    $pdf->Cell(20, 10, "TP", 1, 0, 'C', 1);
    $pdf->Cell(20, 10, "EI", 1, 0, 'C', 1);
    $pdf->Cell(30, 10, "Total Heures", 1, 1, 'C', 1);

    // Affichage des lignes
    foreach ($data as $voeu) {
        $ressource = $voeu['ressource'];
        $semestre  = $voeu['semestre'];
        $cm        = $voeu['cm'];
        $td        = $voeu['td'];
        $tp        = $voeu['tp'];
        $ei        = $voeu['ei'];
        $total     = $voeu['total'];

        $pdf->Cell(40, 10, $ressource, 1, 0, 'L');
        $pdf->Cell(20, 10, $semestre, 1, 0, 'C');
        $pdf->Cell(20, 10, $cm, 1, 0, 'C');
        $pdf->Cell(20, 10, $td, 1, 0, 'C');
        $pdf->Cell(20, 10, $tp, 1, 0, 'C');
        $pdf->Cell(20, 10, $ei, 1, 0, 'C');
        $pdf->Cell(30, 10, $total, 1, 1, 'C');
    }
    $pdf->Ln(5);
}

generateTable($pdf, "Enseignements sur la période SEPTEMBRE-JANVIER", $voeux_septembre);
generateTable($pdf, "Enseignements sur la période JANVIER-JUIN", $voeux_janvier);

$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(0, 10, "Enseignements hors Dept Info", 0, 1, 'L');
$pdf->SetFont('helvetica', '', 10);
$pdf->Ln(2);

$pdf->SetFillColor(230, 230, 230);
$pdf->Cell(40, 10, "Composant", 1, 0, 'C', 1);
$pdf->Cell(40, 10, "Formation", 1, 0, 'C', 1);
$pdf->Cell(40, 10, "Module", 1, 0, 'C', 1);
$pdf->Cell(30, 10, "Total Heures", 1, 1, 'C', 1);

foreach ($voeux_hors_iut as $voeu) {
    $pdf->Cell(40, 10, $voeu['composant'], 1, 0, 'C');
    $pdf->Cell(40, 10, $voeu['formation'], 1, 0, 'C');
    $pdf->Cell(40, 10, $voeu['module'], 1, 0, 'C');
    $pdf->Cell(30, 10, $voeu['total'], 1, 1, 'C');
}

$nom_prof = preg_replace('/[^a-zA-Z0-9]/', '_', $enseignant);
$pdf_folder = __DIR__ . '/../../temp/';
$pdf_filename = 'FichePrevisionnelle_' . $nom_prof . '.pdf';
$pdf_filepath = $pdf_folder . $pdf_filename;

if (!is_dir($pdf_folder)) {
    mkdir($pdf_folder, 0777, true);
}

$pdf->Output($pdf_filepath, 'F');

echo "<script>
    alert('Le PDF de la fiche prévisionnelle a été téléchargé.');
    window.location.href = '../../index.php?action=fichePrevisionnelle';
</script>";
exit();
?>
