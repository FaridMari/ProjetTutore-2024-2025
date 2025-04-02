<?php
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

header('Content-Type: application/json');

$conn = connexionFactory::makeConnection();

// Récupérer l'identifiant de l'utilisateur transmis par le gestionnaire
$id_utilisateur = $_POST['id_utilisateur'] ?? $_GET['id_utilisateur'] ?? null;

if ($id_utilisateur === null) {
    echo json_encode(['success' => false, 'message' => 'ID manquant']);
    exit;
}

try {
    $stmt = $conn->prepare("UPDATE contraintes SET modification_en_cours = 0 WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);

    echo json_encode(['success' => true, 'message' => 'Verrou levé']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur : ' . $e->getMessage()]);
}
