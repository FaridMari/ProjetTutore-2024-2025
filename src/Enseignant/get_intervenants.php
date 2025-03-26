<?php
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

header('Content-Type: application/json');

try {
    $bdd = connexionFactory::makeConnection();

    $stmt = $bdd->prepare("SELECT nom, prenom FROM utilisateurs");
    $stmt->execute();
    $intervenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($intervenants);

} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur PDO : ' . $e->getMessage()]);
}
