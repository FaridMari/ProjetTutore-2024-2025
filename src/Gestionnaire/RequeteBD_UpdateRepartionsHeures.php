<?php
require_once __DIR__ . '/../../vendor/autoload.php';
use src\Db\connexionFactory;

$bdd = connexionFactory::makeConnection();

// Récupérer les données envoyées en POST (JSON)
$data = json_decode(file_get_contents('php://input'), true);


// Vérifier si les données sont présentes
if ($data && is_array($data)) {
    $repartition = $data['repartition'] ?? null;
    $semester = $data['semester'] ?? null;
    //Supprimer toutes les données de la table repartition_heures
    $stmt = $bdd->prepare('DELETE FROM repartition_heures where semestre = :semester');
    $stmt->bindParam(':semester', $semester);
    $stmt->execute();
    foreach ($repartition as $item) {
        // Vérifier si toutes les informations nécessaires sont présentes dans chaque élément
        if (isset($item['codeCours'], $item['semaineDebut'], $item['semaineFin'], $item['typeHeure'], $item['nbHeures'])) {
            // Récupérer l'ID du cours en fonction du code du cours
            $coursCode = $item['codeCours'];
            $stmt = $bdd->prepare('SELECT id_cours FROM cours WHERE code_cours = :code');
            $stmt->bindParam(':code', $coursCode);
            $stmt->execute();

            // Si un cours est trouvé, on récupère l'ID
            $cours = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($cours) {
                $coursId = $cours['id_cours'];

                // Insertion de la répartition dans la table des répartitions
                $stmt = $bdd->prepare('INSERT INTO repartition_heures (semaine_debut, semaine_fin, id_cours, type_heure, nb_heures_par_semaine, semestre) 
                                       VALUES (:semaineDebut, :semaineFin, :coursId, :typeHeure, :nbHeures, :semester)');
                $stmt->bindParam(':semaineDebut', $item['semaineDebut'], PDO::PARAM_INT);
                $stmt->bindParam(':semaineFin', $item['semaineFin'], PDO::PARAM_INT);
                $stmt->bindParam(':coursId', $coursId, PDO::PARAM_INT);
                $stmt->bindParam(':typeHeure', $item['typeHeure']);
                $stmt->bindParam(':nbHeures', $item['nbHeures'], PDO::PARAM_INT);
                $stmt->bindParam(':semester', $semester);

                if (!$stmt->execute()) {
                    // Si l'insertion échoue pour cet élément, vous pouvez enregistrer l'erreur
                    echo json_encode(['success' => false, 'error' => 'Échec de l\'insertion pour un élément']);
                    exit; // Terminer immédiatement si l'insertion échoue
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'Cours non trouvé pour ' . $coursCode]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Données manquantes pour une répartition']);
            exit;
        }
    }
    // Si tout a réussi, renvoyer une réponse de succès
    echo json_encode(['success' => true]);

} else {
    if (!$data) {
        $stmt = $bdd->prepare('DELETE FROM repartition_heures where semestre = :semester');
        $stmt->execute();
        echo json_encode(['success' => true, 'error' => 'Aucune donnée reçue']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Les données doivent être un tableau']);
    }
}
?>
