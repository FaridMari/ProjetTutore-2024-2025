<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

$sql = "SELECT * FROM cours order by semestre";
$stmt = $bdd->prepare($sql);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($result);
?>
