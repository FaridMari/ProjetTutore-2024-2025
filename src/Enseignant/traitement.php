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

        // Récupération de l'id du responsable (ici, l'id_enseignant)
        $id_responsable_module = $_POST['responsibleName'];

        // Récupération des autres champs du formulaire
        // Pour le DS, on récupère directement le contenu saisi (sans préfixe "DS : ")
        $ds = $_POST['dsDetails'] ?? '';
        // Pour le commentaire libre
        $commentaire = $_POST['scheduleDetails'] ?? '';
        // Pour le système, on récupère la valeur du champ radio
        $systeme = $_POST['system'] ?? '';

        // Traitement de la préférence pour la salle 016
        $salle016 = $_POST['salle016'] ?? '';
        $equipementsSpecifiques = '';
        if ($salle016 === 'Oui') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Oui, de préférence\n";
        } elseif ($salle016 === 'Indifférent') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Indifférent\n";
        } elseif ($salle016 === 'Non') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Non, salle non adaptée\n";
        }

        // Exemple de récupération du type de salle depuis un champ "hour_type" (tableau)
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
                    ds = :ds,
                    commentaire = :commentaire,
                    systeme = :systeme,
                    statut = :statut
                WHERE id_ressource = :id_ressource
            ");
            $stmtUpdate->execute([
                ':id_responsable_module'    => $id_responsable_module,
                ':type_salle'               => $type_salle,
                ':equipements_specifiques'  => $equipementsSpecifiques,
                ':ds'                       => $ds,
                ':commentaire'              => $commentaire,
                ':systeme'                  => $systeme,
                ':statut'                   => "en attente",
                ':id_ressource'             => $existingRecord['id_ressource']
            ]);
        } else {
            // Insertion d'une nouvelle fiche ressource
            $stmtInsert = $bdd->prepare("
                INSERT INTO details_cours (
                    id_cours,
                    id_responsable_module,
                    type_salle,
                    equipements_specifiques,
                    ds,
                    commentaire,
                    systeme,
                    statut
                ) VALUES (
                    :id_cours,
                    :id_responsable_module,
                    :type_salle,
                    :equipements_specifiques,
                    :ds,
                    :commentaire,
                    :systeme,
                    :statut
                )
            ");
            $stmtInsert->execute([
                ':id_cours'                => $id_cours,
                ':id_responsable_module'   => $id_responsable_module,
                ':type_salle'              => $type_salle,
                ':equipements_specifiques' => $equipementsSpecifiques,
                ':ds'                      => $ds,
                ':commentaire'             => $commentaire,
                ':systeme'                 => $systeme,
                ':statut'                  => "en attente",
            ]);
        }

        header('Location: ../../index.php?action=accueilEnseignant');
        exit();
    }
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
