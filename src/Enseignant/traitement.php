<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    $bdd = connexionFactory::makeConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        // Récupération du code du cours depuis le formulaire
        $code_cours = $_POST['resourceName'];

        // Récupération de l'id_cours correspondant au code_cours
        $stmt = $bdd->prepare("SELECT id_cours FROM cours WHERE code_cours = :code_cours");
        $stmt->execute([':code_cours' => $code_cours]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cours) {
            $id_cours = $cours['id_cours'];
        } else {
            throw new Exception("Aucun cours trouvé avec le code $code_cours.");
        }

        // Récupération de l'enseignant responsable via l'id_utilisateur
        $id_utilisateur_responsable = $_POST['responsibleName'];
        $stmt = $bdd->prepare("SELECT id_enseignant FROM enseignants WHERE id_utilisateur = :id_utilisateur");
        $stmt->execute([':id_utilisateur' => $id_utilisateur_responsable]);
        $responsable = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($responsable) {
            $id_responsable_module = $responsable['id_enseignant'];
        } else {
            throw new Exception("Aucun enseignant trouvé pour l'utilisateur sélectionné.");
        }

        // Récupération des autres champs du formulaire
        $dsDetails      = 'DS : ' . ($_POST['dsDetails'] ?? '');
        $salle016       = $_POST['salle016'] ?? '';
        $scheduleDetails = $_POST['scheduleDetails'] ?? '';

        // Construction des équipements spécifiques
        $equipementsSpecifiques = '';
        if ($salle016 === 'Oui') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Oui, de préférence\n";
        } elseif ($salle016 === 'Indifférent') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Indifférent\n";
        } elseif ($salle016 === 'Non') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Non, salle non adaptée\n";
        }
        if (!empty($scheduleDetails)) {
            $equipementsSpecifiques .= "Besoins en chariots ou salles : $scheduleDetails\n";
        }

        // Exemple : récupération du type de salle à partir d'un champ "hour_type"
        $type_salle = $_POST['hour_type'][0] ?? 'Inconnu';

        // Vérifier si une fiche existe déjà pour ce cours (unicité par cours)
        $stmtExist = $bdd->prepare("SELECT id_ressource FROM details_cours WHERE id_cours = :id_cours");
        $stmtExist->execute([':id_cours' => $id_cours]);
        $existingRecord = $stmtExist->fetch(PDO::FETCH_ASSOC);

        if ($existingRecord) {
            // Mise à jour de la fiche existante
            $stmtUpdate = $bdd->prepare("UPDATE details_cours 
                SET 
                    id_responsable_module = :id_responsable_module,
                    type_salle = :type_salle,
                    equipements_specifiques = :equipements_specifiques,
                    details = :details,
                    statut = :statut
                WHERE id_ressource = :id_details
            ");
            $stmtUpdate->execute([
                ':id_responsable_module'    => $id_responsable_module,
                ':type_salle'               => $type_salle,
                ':equipements_specifiques'  => $equipementsSpecifiques,
                ':details'                  => $dsDetails,
                ':statut'                   => "en attente",
                ':id_details'               => $existingRecord['id_details']
            ]);
        } else {
            // Insertion d'une nouvelle fiche ressource
            $stmtInsert = $bdd->prepare("
                INSERT INTO details_cours (
                    id_cours,
                    id_responsable_module,
                    type_salle,
                    equipements_specifiques,
                    details,
                    statut
                ) VALUES (
                    :id_cours,
                    :id_responsable_module,
                    :type_salle,
                    :equipements_specifiques,
                    :details,
                    :statut
                )
            ");
            $stmtInsert->execute([
                ':id_cours'                => $id_cours,
                ':id_responsable_module'   => $id_responsable_module,
                ':type_salle'              => $type_salle,
                ':equipements_specifiques' => $equipementsSpecifiques,
                ':details'                 => $dsDetails,
                ':statut'                  => "en attente",
            ]);
        }

        // Redirection vers la page d'accueil de l'enseignant après traitement
        header('Location: ../../index.php?action=accueilEnseignant');
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
