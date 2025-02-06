<?php
session_start();
require_once __DIR__ . '/../../vendor/TCPDF/tcpdf.php';


if (!isset($_POST['semester']) || !isset($_POST['resourceName']) || !isset($_POST['resourceCode']) || !isset($_POST['responsibleName'])) {
    echo "<script>
        alert('Données manquantes pour générer le PDF.');
        window.location.href = '../index.php?action=enseignantFicheRessource';
    </script>";
    exit();
}


$semester = htmlspecialchars($_POST['semester']);
$resourceName = htmlspecialchars($_POST['resourceName']);
$resourceCode = htmlspecialchars($_POST['resourceCode']);
$responsibleName = htmlspecialchars($_POST['responsibleName']);
$phone = htmlspecialchars($_POST['phone'] ?? 'Non spécifié');
$dsDetails = htmlspecialchars($_POST['dsDetails'] ?? 'Non spécifié');
$salle016 = htmlspecialchars($_POST['salle016'] ?? 'Non spécifié');
$system = htmlspecialchars($_POST['system'] ?? 'Non spécifié');
$scheduleDetails = htmlspecialchars($_POST['scheduleDetails'] ?? 'Non spécifié');

// TCPDF
ob_end_clean();
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetTitle('Fiche Prévisionnelle de Service');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();


$html = <<<EOD
<style>
    body { font-family: Arial, sans-serif; }
    h1, h4 { text-align: center; }
    table { border-collapse: collapse; width: 100%; }
    th, td { border: 1px solid black; padding: 5px; text-align: center; }
    th { background-color: #f2f2f2; }
    .container { width: 80%; margin: auto; }
</style>

<h1>Fiche Prévisionnelle de Service</h1>
<p style="text-align: center;">IUT Nancy-Charlemagne - Département Informatique</p>

<h4>Informations Générales</h4>
<table>
    <tr><th>Semestre</th><td>$semester</td></tr>
    <tr><th>Nom de la Ressource</th><td>$resourceName</td></tr>
    <tr><th>Code de la Ressource</th><td>$resourceCode</td></tr>
    <tr><th>Nom du Responsable</th><td>$responsibleName</td></tr>
    <tr><th>Téléphone</th><td>$phone</td></tr>
</table>

<h4>Réservations DS</h4>
<p>$dsDetails</p>

<h4>Salle 016</h4>
<p>$salle016</p>

<h4>Besoins en Chariots / Salles Informatiques</h4>
<p><strong>Système souhaité :</strong> $system</p>
<p><strong>Période et heures :</strong> $scheduleDetails</p>
EOD;


$pdf->writeHTML($html, true, false, true, false, '');


$pdf_folder = __DIR__ . '../../../temp/';
$nom_prof = preg_replace('/[^a-zA-Z0-9]/', '_', $responsibleName);
$pdf_filename = 'FicheRessource_' . $nom_prof . '.pdf';
$pdf_filepath = $pdf_folder . $pdf_filename;

if (!is_dir($pdf_folder)) {
    mkdir($pdf_folder, 0777, true);
}

// Sauvegarder le PDF
$pdf->Output($pdf_filepath, 'F');
echo "<script>
    alert('Le PDF de la Fiche Ressource a été téléchargé.');
    window.location.href = '../../index.php?action=enseignantFicheRessource';
</script>";
exit();
?>
