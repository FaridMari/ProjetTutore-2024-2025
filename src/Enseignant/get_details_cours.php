<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

$conn = connexionFactory::makeConnection();

$code_cours = $_GET['code_cours'] ?? '';
if (!$code_cours) {
    echo json_encode(['error' => 'Code de cours manquant']);
    exit;
}

// Récupérer l'id du cours
$stmt = $conn->prepare("SELECT id_cours FROM cours WHERE code_cours = ?");
$stmt->execute([$code_cours]);
$cours = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$cours) {
    echo json_encode(['error' => 'Cours non trouvé']);
    exit;
}
$id_cours = $cours['id_cours'];

// Récupérer la fiche dans details_cours
$stmt = $conn->prepare("SELECT * FROM details_cours WHERE id_cours = ?");
$stmt->execute([$id_cours]);
$details = $stmt->fetch(PDO::FETCH_ASSOC);

// Renvoie en JSON (même si la fiche n'existe pas, on renvoie null)
echo json_encode($details);
exit;
?>
