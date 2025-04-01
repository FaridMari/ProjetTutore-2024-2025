<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

header('Content-Type: application/json');

try {
    $bdd = connexionFactory::makeConnection();

    // Suppression du filtre sur "responsable" pour inclure tous les enseignants
    $stmt = $bdd->prepare("
        SELECT 
            u.id_utilisateur, 
            e.id_enseignant AS id_enseignant, 
            u.nom, 
            u.prenom, 
            u.telephone 
        FROM utilisateurs u
        INNER JOIN enseignants e ON u.id_utilisateur = e.id_utilisateur
        WHERE u.role = 'enseignant'
    ");
    $stmt->execute();
    $intervenants = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($intervenants);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Erreur PDO : ' . $e->getMessage()]);
}
?>
