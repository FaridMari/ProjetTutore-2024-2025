<?php
session_start();
require_once __DIR__ . '/../../vendor/TCPDF/tcpdf.php';

$host = 'localhost';
$dbname = 'projettutore';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $code_cours = $_POST['resourceCode'];

        $stmt = $pdo->prepare("SELECT id_cours FROM cours WHERE code_cours = :code_cours");
        $stmt->execute([':code_cours' => $code_cours]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cours) {
            $id_cours = $cours['id_cours'];
        } else {
            throw new Exception("Aucun cours trouvé avec le code $code_cours.");
        }

        // Dernier id d'enseignant
        $id_responsable_module = 55;


        $dsDetails = 'DS : ' . ($_POST['dsDetails'] ?? '');
        $salle016 = $_POST['salle016'] ?? '';
        $scheduleDetails = $_POST['scheduleDetails'] ?? '';


        $equipementsSpecifiques = '';
        if ($salle016 === 'Oui') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Oui, de préférence\n";
        } elseif ($salle016 === 'Indifférent') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Indifférent\n";
        } elseif ($salle016 === 'Non') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Non, salle non adaptée\n";
        }

        if (!empty($scheduleDetails)) {
            $equipementsSpecifiques .= "Besoins en chariots ou salles : $scheduleDetails\n";
        }

        $type_salle = $_POST['hour_type'][0] ?? 'Inconnu';

        // Insertion dans la table details_cours
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

        // 📄 **Génération du PDF après enregistrement en base**
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
    <tr><th>Nom de la Ressource</th><td>{$_POST['resourceName']}</td></tr>
    <tr><th>Code de la Ressource</th><td>{$_POST['resourceCode']}</td></tr>
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


        $pdf_folder = __DIR__ . '/../../temp/';
        if (!is_dir($pdf_folder)) {
            mkdir($pdf_folder, 0777, true);
        }

        $nom_prof = preg_replace('/[^a-zA-Z0-9]/', '_', $_POST['responsibleName']);
        $pdf_filename = 'FicheRessource_' . $nom_prof . '.pdf';
        $pdf_filepath = $pdf_folder . $pdf_filename;

        $pdf->Output($pdf_filepath, 'F');


        echo "<script>
            alert('Les vœux ont été enregistrés en base et le PDF a été téléchargé.');
            window.location.href = '../../index.php?action=enseignantFicheRessource';
        </script>";
        exit();
    }
} catch (PDOException $e) {
    echo "<script>
        alert('Erreur PDO : " . addslashes($e->getMessage()) . "');
        window.location.href = '../index.php?action=enseignantFicheRessource';
    </script>";
} catch (Exception $e) {
    echo "<script>
        alert('Erreur : " . addslashes($e->getMessage()) . "');
        window.location.href = '../index.php?action=enseignantFicheRessource';
    </script>";
}
?>
