<?php
// Configuration de la connexion à la base de données
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

header('Content-Type: application/json');

try {
    $pdo = connexionFactory::makeConnection();

    if (isset($_GET['semester'])) {
        // Récupère le paramètre 'semester' et supprime le premier caractère s'il commence par 'S'
        $semester = $_GET['semester'];
        if (strpos($semester, 'S') === 0) {
            $semester = substr($semester, 1);
        }
        // Convertit le semestre en entier pour éviter les injections SQL potentielles
        $semester = (int)$semester;

        // Prépare et exécute la requête SQL
        $stmt = $pdo->prepare("SELECT code_cours, nom_cours FROM cours WHERE semestre = :semester");
        $stmt->execute(['semester' => $semester]);
        $cours = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Retourne les résultats en format JSON
        echo json_encode($cours);
    } else {
        echo json_encode(['error' => 'Le paramètre "semester" est manquant.']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
