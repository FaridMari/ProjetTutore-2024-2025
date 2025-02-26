<?php
session_start();
require_once __DIR__ . '/../../vendor/TCPDF/tcpdf.php';

$host = 'localhost';
$dbname = 'projettutore';
$user = 'root';
$password = '';

try {
    // ✅ Connexion à la base de données
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // ✅ Vérifier que `resourceName` est bien envoyé et non vide
        if (!isset($_POST['resourceName']) || empty(trim($_POST['resourceName']))) {
            throw new Exception("⚠️ Erreur : Aucun cours sélectionné !");
        }

        // ✅ Récupération du nom du cours
        $nom_cours = trim($_POST['resourceName']);

        // ✅ Récupérer l'ID du cours en fonction du **nom**
        $stmt = $pdo->prepare("SELECT id_cours, code_cours FROM cours WHERE nom_cours = :nom_cours");
        $stmt->execute([':nom_cours' => $nom_cours]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$cours) {
            throw new Exception("⚠️ Erreur : Aucun cours trouvé avec le nom '$nom_cours' !");
        }

        $id_cours = $cours['id_cours'];
        $code_cours = $cours['code_cours'];
        $id_responsable_module = 55; // Remplace par la vraie valeur si nécessaire

        // ✅ Récupération des données du formulaire
        $dsDetails = 'DS : ' . ($_POST['dsDetails'] ?? '');
        $salle016 = $_POST['salle016'] ?? '';
        $scheduleDetails = $_POST['scheduleDetails'] ?? '';

        // ✅ Construction de la chaîne d'équipements spécifiques
        $equipementsSpecifiques = "Intervention en salle 016 : $salle016\n";
        if (!empty($scheduleDetails)) {
            $equipementsSpecifiques .= "Besoins en chariots ou salles : $scheduleDetails\n";
        }

        $type_salle = $_POST['hour_type'][0] ?? 'Inconnu';

        // ✅ Insertion dans la table `details_cours`
        $stmtDetails = $pdo->prepare("
            INSERT INTO details_cours (
                id_cours,
                id_responsable_module,
                type_salle,
                equipements_specifiques,
                details
            ) VALUES (
                :id_cours,
                :id_responsable_module,
                :type_salle,
                :equipements_specifiques,
                :details
            )
        ");
        $stmtDetails->execute([
            ':id_cours' => $id_cours,
            ':id_responsable_module' => $id_responsable_module,
            ':type_salle' => $type_salle,
            ':equipements_specifiques' => $equipementsSpecifiques,
            ':details' => $dsDetails
        ]);

        // ✅ **Génération du PDF après enregistrement**
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
    <tr><th>Semestre</th><td>{$_POST['semester']}</td></tr>
    <tr><th>Nom de la Ressource</th><td>{$nom_cours}</td></tr>
    <tr><th>Code de la Ressource</th><td>{$code_cours}</td></tr>
    <tr><th>Nom du Responsable</th><td>{$_POST['responsibleName']}</td></tr>
    <tr><th>Téléphone</th><td>{$_POST['phone']}</td></tr>
</table>

<h4>Réservations DS</h4>
<p>$dsDetails</p>

<h4>Salle 016</h4>
<p>$salle016</p>

<h4>Besoins en Chariots / Salles Informatiques</h4>
<p><strong>Système souhaité :</strong> {$_POST['system']}</p>
<p><strong>Période et heures :</strong> $scheduleDetails</p>
EOD;

        $pdf->writeHTML($html, true, false, true, false, '');

        //Envoi du fichier PDF en téléchargement direct
        $nom_prof = preg_replace('/[^a-zA-Z0-9]/', '_', $_POST['responsibleName']);
        $pdf_filename = 'FicheRessource_' . $nom_prof . '.pdf';

        //Force le téléchargement
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $pdf_filename . '"');
        header('Cache-Control: private, must-revalidate, max-age=0');
        header('Pragma: public');

        $pdf->Output($pdf_filename, 'D');

        // ✅ Redirection après téléchargement
        echo "<script>
            window.location.href = '../../index.php?action=enseignantFicheRessource';
        </script>";
        exit();
    }
} catch (PDOException $e) {
    // ✅ Gestion des erreurs SQL
    error_log("Erreur Sql : " . $e->getMessage());
    die("<script>
        alert('Erreur PDO : " . addslashes($e->getMessage()) . "');
        window.location.href = '../../index.php?action=enseignantFicheRessource';
    </script>");
} catch (Exception $e) {
    // ✅ Gestion des autres erreurs
    error_log("Autres Erreurs : " . $e->getMessage());
    die("<script>
        alert('Erreur : " . addslashes($e->getMessage()) . "');
        window.location.href = '../../index.php?action=enseignantFicheRessource';
    </script>");
}
?>
