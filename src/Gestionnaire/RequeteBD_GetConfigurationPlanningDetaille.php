<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

try {
    $sql = "SELECT * FROM configurationPlanningDetaille";
    $stmt = $bdd->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($result);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>