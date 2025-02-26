<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

// Récupérer les données envoyées en POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if ($data) {
    $formation = $data['formation'];
    $semestre = $data['semestre'];
    $nomCours = $data['nom_cours'];
    $codeCours = $data['code_cours'];
    $nbHeuresTotal = $data['nb_heures_total'];
    $nbHeuresCM = $data['nb_heures_cm'];
    $nbHeuresTD = $data['nb_heures_td'];
    $nbHeuresTP = $data['nb_heures_tp'];
    $nbHeuresEI = $data['nb_heures_ei'];

    // Vérification des heures
    $totalHeures = $nbHeuresCM + $nbHeuresTD + $nbHeuresTP + $nbHeuresEI;
    if ($totalHeures > $nbHeuresTotal) {
        echo json_encode(['success' => false, 'error' => 'La somme des heures dépasse les heures totales']);
        exit;
    }

    $sql = "INSERT INTO cours (formation, semestre, nom_cours, code_cours, nb_heures_total, nb_heures_cm, nb_heures_td, nb_heures_tp, nb_heures_ei)
            VALUES (:formation, :semestre, :nom_cours, :code_cours, :nb_heures_total, :nb_heures_cm, :nb_heures_td, :nb_heures_tp, :nb_heures_ei)";

    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':formation', $formation);
    $stmt->bindParam(':semestre', $semestre);
    $stmt->bindParam(':nom_cours', $nomCours);
    $stmt->bindParam(':code_cours', $codeCours);
    $stmt->bindParam(':nb_heures_total', $nbHeuresTotal);
    $stmt->bindParam(':nb_heures_cm', $nbHeuresCM);
    $stmt->bindParam(':nb_heures_td', $nbHeuresTD);
    $stmt->bindParam(':nb_heures_tp', $nbHeuresTP);
    $stmt->bindParam(':nb_heures_ei', $nbHeuresEI);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Échec de l\'ajout']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
}
?>
