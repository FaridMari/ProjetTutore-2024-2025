<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require_once __DIR__ . '/../../vendor/autoload.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

if (!isset($_SESSION['pdf_data'])) {
    die("Aucune donnée disponible pour générer le PDF.");
}

require_once __DIR__ . '/../modele/UtilisateurDTO.php';

$data = $_SESSION['pdf_data'];

$utilisateurDTO = new UtilisateurDTO();
$user = $utilisateurDTO->findById($userId);
$enseignantFullName = $user ? $user->getNom() . ' ' . $user->getPrenom() : 'Inconnu';


$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);


$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor($enseignantFullName);
$pdf->SetTitle('Fiche Prévisionnelle de Service');
$pdf->SetSubject('Fiche Prévisionnelle');
$pdf->SetKeywords('TCPDF, PDF, Fiche, Prévisionnelle');


$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);


$pdf->AddPage();


$html = '
<style>
    body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; }
    h1 { color: #003366; text-align: center; }
    h2 { color: #0066CC; margin-top: 20px; }
    table { border-collapse: collapse; width: 100%; margin-top: 10px; }
    table, th, td { border: 1px solid #444444; }
    th { background-color: #CCCCCC; padding: 8px; text-align: center; }
    td { padding: 8px; text-align: center; }
    .total-row { font-weight: bold; background-color: #EFEFEF; }
</style>
';

$html .= '<h1>Fiche Prévisionnelle de Service</h1>';
$html .= '<p style="text-align:center;"><strong>Enseignant :</strong> ' . $enseignantFullName . '</p>';

// Section Vœux Septembre
if (!empty($data['voeux_septembre'])) {
    $html .= '<h2>Vœux Septembre</h2>';
    $html .= '<table cellpadding="4">
                <tr>
                  <th>Ressource</th>
                  <th>Semestre</th>
                  <th>CM</th>
                  <th>TD</th>
                  <th>TP</th>
                  <th>EI</th>
                  <th>Remarque</th>
                  <th>Total</th>
                </tr>';
    foreach ($data['voeux_septembre'] as $v) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($v['ressource']) . '</td>
                    <td>' . htmlspecialchars($v['semestre']) . '</td>
                    <td>' . htmlspecialchars($v['cm']) . '</td>
                    <td>' . htmlspecialchars($v['td']) . '</td>
                    <td>' . htmlspecialchars($v['tp']) . '</td>
                    <td>' . htmlspecialchars($v['ei']) . '</td>
                    <td>' . htmlspecialchars($v['remarque'] ?? '') . '</td>
                    <td>' . htmlspecialchars($v['total']) . '</td>
                  </tr>';
    }
    $html .= '</table>';
}

// Section Vœux Janvier
if (!empty($data['voeux_janvier'])) {
    $html .= '<h2>Vœux Janvier</h2>';
    $html .= '<table cellpadding="4">
                <tr>
                  <th>Ressource</th>
                  <th>Semestre</th>
                  <th>CM</th>
                  <th>TD</th>
                  <th>TP</th>
                  <th>EI</th>
                  <th>Remarque</th>
                  <th>Total</th>
                </tr>';
    foreach ($data['voeux_janvier'] as $v) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($v['ressource']) . '</td>
                    <td>' . htmlspecialchars($v['semestre']) . '</td>
                    <td>' . htmlspecialchars($v['cm']) . '</td>
                    <td>' . htmlspecialchars($v['td']) . '</td>
                    <td>' . htmlspecialchars($v['tp']) . '</td>
                    <td>' . htmlspecialchars($v['ei']) . '</td>
                    <td>' . htmlspecialchars($v['remarque'] ?? '') . '</td>
                    <td>' . htmlspecialchars($v['total']) . '</td>
                  </tr>';
    }
    $html .= '</table>';
}

// Section Vœux hors IUT
if (!empty($data['voeux_hors_iut'])) {
    $html .= '<h2>Vœux hors IUT</h2>';
    $html .= '<table cellpadding="4">
                <tr>
                  <th>Composant</th>
                  <th>Formation</th>
                  <th>Module</th>
                  <th>CM</th>
                  <th>TD</th>
                  <th>TP</th>
                  <th>EI</th>
                  <th>Total</th>
                </tr>';
    foreach ($data['voeux_hors_iut'] as $v) {
        $html .= '<tr>
                    <td>' . htmlspecialchars($v['composant']) . '</td>
                    <td>' . htmlspecialchars($v['formation']) . '</td>
                    <td>' . htmlspecialchars($v['module']) . '</td>
                    <td>' . htmlspecialchars($v['cm']) . '</td>
                    <td>' . htmlspecialchars($v['td']) . '</td>
                    <td>' . htmlspecialchars($v['tp']) . '</td>
                    <td>' . htmlspecialchars($v['ei']) . '</td>
                    <td>' . htmlspecialchars($v['total']) . '</td>
                  </tr>';
    }
    $html .= '</table>';
}

// Calcul des totaux pour le département (somme des heures de CM, TD, TP, EI)
$totalCM = $totalTD = $totalTP = $totalEI = 0;

if (!empty($data['voeux_septembre'])) {
    foreach ($data['voeux_septembre'] as $v) {
        $totalCM += floatval($v['cm']);
        $totalTD += floatval($v['td']);
        $totalTP += floatval($v['tp']);
        $totalEI += floatval($v['ei']);
    }
}

if (!empty($data['voeux_janvier'])) {
    foreach ($data['voeux_janvier'] as $v) {
        $totalCM += floatval($v['cm']);
        $totalTD += floatval($v['td']);
        $totalTP += floatval($v['tp']);
        $totalEI += floatval($v['ei']);
    }
}

if (!empty($data['voeux_hors_iut'])) {
    foreach ($data['voeux_hors_iut'] as $v) {
        $totalCM += floatval($v['cm']);
        $totalTD += floatval($v['td']);
        $totalTP += floatval($v['tp']);
        $totalEI += floatval($v['ei']);
    }
}

$html .= '<h2>Total</h2>';
$html .= '<table cellpadding="4" id="table-dept-info">
            <tr>
              <th>CM</th>
              <th>TD</th>
              <th>TP</th>
              <th>EI</th>
            </tr>
            <tr class="total-row">
              <td>' . $totalCM . '</td>
              <td>' . $totalTD . '</td>
              <td>' . $totalTP . '</td>
              <td>' . $totalEI . '</td>
            </tr>
          </table>';


$pdf->writeHTML($html, true, false, true, false, '');


$pdf->Output('fiche_previsionnelle.pdf', 'I');