<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

// Récupérer les données envoyées en POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['year'])) {
    $year = $data['year'];

    // Liste des tables à restaurer
    $tables_i = [
        'voeux',
        'voeux_hors_iut',
        'details_cours',
        'contraintes',
        'repartition_heures',
        'affectations',
        'configurationplanningdetaille'
    ];

    $tables = [
        'configurationplanningdetaille',
        'affectations',
        'repartition_heures',
        'contraintes',
        'details_cours',
        'voeux_hors_iut',
        'voeux',
    ];

    try {
        $bdd->beginTransaction();

        // Supprimer toutes les données actuelles
        foreach ($tables as $table) {
            $deleteSql = "DELETE FROM `$table`";
            $deleteStmt = $bdd->prepare($deleteSql);
            if (!$deleteStmt->execute()) {
                throw new Exception("Échec de la suppression des données dans la table $table");
            }
        }

        // Récupérer et insérer les données historisées pour l'année spécifiée
        foreach ($tables as $table) {
            // Nom de la table historisée
            $historizedTable = $table . '_historisees';

            // Récupérer les colonnes de la table originale
            $result = $bdd->query("DESCRIBE `$table`");
            $columns = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                // Exclure les colonnes d'identifiants auto-incrémentés et les clés primaires
                if ($row['Extra'] !== 'auto_increment' ) {
                    $columns[] = $row['Field'];
                }
            }

            // Construire la requête d'insertion
            $sql = "INSERT INTO `$table` (";
            foreach ($columns as $column) {
                $sql .= "`$column`, ";
            }
            $sql = rtrim($sql, ', ');
            $sql .= ") SELECT ";
            foreach ($columns as $column) {
                $sql .= "`$column`, ";
            }
            $sql = rtrim($sql, ', ');
            $sql .= " FROM `$historizedTable` WHERE `annee` = :annee";

            $stmt = $bdd->prepare($sql);
            $stmt->bindParam(':annee', $year, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Échec de la récupération des données dans la table $historizedTable");
            }
        }

        // Mettre à jour les statuts à "en attente"
        $updateTables = ['contraintes', 'voeux', 'details_cours'];
        foreach ($updateTables as $table) {
            $updateSql = "UPDATE `$table` SET `statut` = 'en attente'";
            $updateStmt = $bdd->prepare($updateSql);
            if (!$updateStmt->execute()) {
                throw new Exception("Échec de la mise à jour du statut dans la table $table");
            }
        }

        $bdd->commit();
        echo json_encode(['success' => true]);
    } catch (Exception $e) {
        $bdd->rollBack();
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Année manquante']);
}
?>
