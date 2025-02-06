<?php
session_start();
require_once __DIR__ . '/../../vendor/TCPDF/tcpdf.php';


if (!isset($_SESSION['id_utilisateur'])) {
    die("Erreur : Utilisateur non connecté.");
}

$nom_prenom = $_SESSION['nom_prenom'] ?? "Non spécifié";
$choix_contraintes = $_SESSION['choix_contraintes'] ?? [];
$creneau_prefere = $_SESSION['creneau_prefere'] ?? "Non spécifié";
$cours_samedi = $_SESSION['cours_samedi'] ?? "Non spécifié";

// Initialisation du PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Fiche de Voeux 2024-2025');
$pdf->SetTitle('Fiche de Vœux 2024-2025');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, "Fiche de Vœux 2024-2025", 0, 1, 'C');
$pdf->Ln(5);

$pdf->SetFont('helvetica', '', 12);
$pdf->Cell(0, 10, "Nom et Prénom : $nom_prenom", 0, 1);
$pdf->Ln(5);

// Affichage des contraintes
$pdf->SetFont('helvetica', '', 10);
$jours = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];
$horaires = ['8h-10h', '10h-12h', '14h-16h', '16h-18h'];

$pdf->Cell(40, 10, "Jour", 1, 0, 'C');
foreach ($jours as $jour) {
    $pdf->Cell(30, 10, $jour, 1, 0, 'C');
}
$pdf->Ln();

foreach ($horaires as $heure) {
    $pdf->Cell(40, 10, $heure, 1, 0, 'C');
    foreach ($jours as $jour) {
        $key = strtolower($jour) . '_' . str_replace('h', '_', $heure);
        $marque = in_array($key, $choix_contraintes) ? 'X' : '';
        $pdf->Cell(30, 10, $marque, 1, 0, 'C');
    }
    $pdf->Ln();
}

$pdf->Output('FicheVoeux2024-2025.pdf', 'D');
exit();
