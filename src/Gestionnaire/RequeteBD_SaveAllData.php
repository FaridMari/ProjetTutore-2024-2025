<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

// Récupérer les données envoyées en POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);

if (isset($data['year'])) {
    $year = $data['year'];

    // Liste des tables à historiser
    $tables = [
        'affectations',
        'configurationplanningdetaille',
        'contraintes',
        'details_cours',
        'repartition_heures',
        'voeux',
        'voeux_hors_iut'
    ];

    try {
        $bdd->beginTransaction();

        foreach ($tables as $table) {
            // Nom de la table historisée
            $historizedTable = $table . '_historisees';

            // Récupérer les colonnes de la table originale
            $result = $bdd->query("DESCRIBE `$table`");
            $columns = [];
            while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
                // Exclure les colonnes d'identifiants auto-incrémentés
                if ($row['Extra'] !== 'auto_increment') {
                    $columns[] = $row['Field'];
                }
            }

            // Construire la requête d'insertion
            $sql = "INSERT INTO `$historizedTable` (";
            foreach ($columns as $column) {
                $sql .= "`$column`, ";
            }
            $sql .= "`annee`) SELECT ";
            foreach ($columns as $column) {
                $sql .= "`$column`, ";
            }
            $sql .= ":annee FROM `$table`";

            // Vérification des clés étrangères pour `details_cours`
            if ($table === 'details_cours') {
                $sql .= " WHERE `id_responsable_module` IN (SELECT `id_enseignant` FROM `enseignants`)";
            }

            $stmt = $bdd->prepare($sql);
            $stmt->bindParam(':annee', $year, PDO::PARAM_INT);

            if (!$stmt->execute()) {
                throw new Exception("Échec de l'insertion dans la table $historizedTable");
            }
        }

        // Mettre à jour les statuts à "en attente" pour les tables spécifiées
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
