<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();
$data = json_decode(file_get_contents('php://input'), true);

try {
    $sql = "DELETE FROM configurationPlanningDetaille";
    $stmt = $bdd->prepare($sql);
    $stmt->execute();

    foreach ($data as $row) {
        $nbSemaine = getWeeks($row['dateDebut'], $row['dateFin'], $row['type']);
        $sql = "INSERT INTO configurationPlanningDetaille (semestre, type, dateDebut, dateFin, description, nbSemaines, couleur, modifiable)
                VALUES (:semestre, :type, :dateDebut, :dateFin, :description, :nbSemaine, :couleur, :modifiable)";
        $stmt = $bdd->prepare($sql);
        $stmt->bindParam(':semestre', $row['semestre']);
        $stmt->bindParam(':type', $row['type']);
        $stmt->bindParam(':dateDebut', $row['dateDebut']);
        $stmt->bindParam(':dateFin', $row['dateFin']);
        $stmt->bindParam(':description', $row['description']);
        $stmt->bindParam(':nbSemaine', $nbSemaine);
        $stmt->bindParam(':couleur', $row['couleur']);
        $modifiable = isset($row['checkbox']) ? (bool)$row['checkbox'] : false;
        $stmt->bindParam(':modifiable', $modifiable, PDO::PARAM_BOOL);

        $stmt->execute();
    }
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}

function getWeeks($date1, $date2, $type)
{
    if (strpos($type, "Semestre") === 0 || in_array($type, ["Stage", "type_0"])) {
        $date1 = new DateTime($date1);
        $date2 = new DateTime($date2);
        $diff = $date2->diff($date1);
        $weeks = floor($diff->days / 7);
        return $weeks;
    } else {
        return 0;
    }
}