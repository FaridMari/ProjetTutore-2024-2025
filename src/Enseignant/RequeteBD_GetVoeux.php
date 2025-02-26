<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

$enseignantId = $_GET['enseignant']; // Récupérer l'ID de l'enseignant depuis l'URL

$sql = "SELECT id_cours, semestre, nb_CM, nb_TD, nb_TP, nb_EI FROM voeux WHERE id_enseignant = :enseignant";
$stmt = $bdd->prepare($sql);
$stmt->bindParam(':enseignant', $enseignantId);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($result);
?>
