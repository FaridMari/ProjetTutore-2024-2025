<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

// Récupérer les données envoyées en POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if ($data && isset($data['id_cours'])) {
    $idCours = $data['id_cours'];

    $sql = "DELETE FROM cours WHERE id_cours = :id_cours";
    $stmt = $bdd->prepare($sql);
    $stmt->bindParam(':id_cours', $idCours);

    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Échec de la suppression']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Données manquantes']);
}
?>
