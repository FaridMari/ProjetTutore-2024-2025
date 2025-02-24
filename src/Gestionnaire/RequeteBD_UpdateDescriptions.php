<?php

require_once __DIR__ . '/../../vendor/autoload.php';

use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

$data = json_decode(file_get_contents('php://input'), true);
$descriptions = $data['descriptions'];
$semester = $data['semester'];

// Supprimer les anciennes descriptions pour éviter les doublons
$stmt = $bdd->prepare("DELETE FROM configurationplanningdetaille WHERE semestre = :semester AND type = 'Description'");
$stmt->bindParam(':semester', $semester);
$stmt->execute();

// Insérer les nouvelles descriptions
foreach ($descriptions as $desc) {
    $dateDebut = $desc['dateDebut'];
    $dateFin = $desc['dateFin'];
    $description = $desc['description'];

    $stmt = $bdd->prepare("INSERT INTO configurationplanningdetaille (dateDebut, dateFin, type, description, semestre)
                           VALUES (:dateDebut, :dateFin, 'Description', :description, :semester)");
    $stmt->bindParam(':dateDebut', $dateDebut);
    $stmt->bindParam(':dateFin', $dateFin);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':semester', $semester);
    $stmt->execute();
}

echo json_encode(['success' => true]);
?>
