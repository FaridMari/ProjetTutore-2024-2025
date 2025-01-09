<?php
session_start();


require_once __DIR__ . '/../../vendor/TCPDF/tcpdf.php';
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;


if (!isset($_SESSION['id_utilisateur'])) {
    die("Erreur : utilisateur non connecté.");
}


$conn = connexionFactory::makeConnection();
$idUtilisateur = $_SESSION['id_utilisateur'];

$stmt = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = :id");
$stmt->bindParam(':id', $idUtilisateur, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    die("Erreur : utilisateur introuvable.");
}

$nom = htmlspecialchars($user['nom']);
$prenom = htmlspecialchars($user['prenom']);

// Vérifiez si des données POST sont disponibles
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST)) {
    die("Erreur : aucune donnée reçue.");
}

$data = $_POST;
$ficheType = "Fiche de Vœux";


ob_start();
include __DIR__ . '/../Enseignant/FicheContrainte.php';
$pageContent = ob_get_clean();

// Extraire uniquement le div contenant le tableau
$dom = new DOMDocument();
$dom->loadHTML($pageContent);


// Récupérer le tableau
$tableDiv = '';
$xpath = new DOMXPath($dom);
$divs = $xpath->query("//div[@class='table-responsive']");
if ($divs->length > 0) {
    $tableDiv = $dom->saveHTML($divs->item(0));
}


foreach ($data as $key => $value) {
    if (strpos($key, '_') !== false && !empty($value)) {
        $pattern = sprintf('/<input[^>]+name="%s"[^>]*>/', preg_quote($key, '/'));
        $tableDiv = preg_replace($pattern, 'X', $tableDiv);
    }
}

// Créez le contenu HTML pour TCPDF
$html = "<h1 style='text-align: center;'>$ficheType</h1>";
$html .= "<p><strong>Nom :</strong> $nom</p>";
$html .= "<p><strong>Prénom :</strong> $prenom</p>";
$html .= "<h3>Contraintes horaires</h3>";
$html .= $tableDiv;

// Ajouter les préférences
$creneauPrefere = isset($data['creneau_prefere']) ? htmlspecialchars($data['creneau_prefere']) : 'Aucune préférence';
$coursSamedi = isset($data['cours_samedi']) && $data['cours_samedi'] === 'oui' ? 'Oui' : 'Non';

$html .= "<h3>Préférences </h3>";
$html .= "<p><strong>Créneau à éviter :</strong> $creneauPrefere</p>";
$html .= "<p><strong>J'accepte d'avoir cours le samedi :</strong> $coursSamedi</p>";

// Création du PDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor("$prenom $nom");
$pdf->SetTitle("$ficheType - $nom $prenom");
$pdf->SetSubject('Fiche de Vœux');
$pdf->SetHeaderData('', 0, "$ficheType", '');


$pdf->AddPage();
$pdf->writeHTML($html, true, false, true, false, '');

$filename = "FicheDeVoeux-$nom-$prenom.pdf";
$pdf->Output($filename, 'D');
