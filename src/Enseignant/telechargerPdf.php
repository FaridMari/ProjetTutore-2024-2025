<?php

if (!isset($_SESSION['id_utilisateur'])) {
    die("Erreur : Utilisateur non connecté.");
}

// Type de fiche demandé
$type = $_POST['fiche'] ?? $_GET['fiche'] ?? 'fiche_voeux';

// Définir les URL d'accès à la fiche (en mode Puppeteer = paramètre `pdf=1`)
switch ($type) {
    case 'fiche_voeux':
    case 'fiche_contrainte':
        $ficheUrl = "http://localhost:8000/index.php?action=enseignantFicheContrainte&pdf=1";
        $pdfName = 'fiche_contrainte.pdf';
        break;
    case 'fiche_ressource':
        $ficheUrl = "http://localhost:8000/index.php?action=enseignantFicheRessource&pdf=1";
        $pdfName = 'fiche_ressource.pdf';
        break;
    default:
        die('Type de fiche non autorisé.');
}

// Chemin de sortie
$pdfPath = realpath(__DIR__) . DIRECTORY_SEPARATOR . $pdfName;

// Commande Puppeteer (assure-toi que le script JS utilise `puppeteer.launch()` correctement)
$cmd = "node ../../src/User/GenerePdf.js " . escapeshellarg($ficheUrl) . " " . escapeshellarg($pdfPath);

// Exécution
exec($cmd, $output, $return_var);

if ($return_var !== 0 || !file_exists($pdfPath)) {
    die("Erreur : le PDF n'a pas été généré.");
}

// Téléchargement
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="' . $pdfName . '"');
readfile($pdfPath);
unlink($pdfPath);
exit;
?>
