<?php
session_start();

require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    $bdd = connexionFactory::makeConnection();
    // Vérifie si la requête est de type POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération du code du cours depuis le formulaire
        $code_cours = $_POST['resourceCode'];

        // Récupération de l'id_cours correspondant au code_cours
        $stmt = $bdd->prepare("SELECT id_cours FROM cours WHERE code_cours = :code_cours");
        $stmt->execute([':code_cours' => $code_cours]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cours) {
            $id_cours = $cours['id_cours'];
        } else {
            throw new Exception("Aucun cours trouvé avec le code $code_cours.");
        }

        //recuperer l'id enseignant avec une requete sql
        $stmt = $bdd->prepare("SELECT id_enseignant FROM enseignants where id_utilisateur = :id_utilisateur");
        $stmt->execute([':id_utilisateur' => $_SESSION['id_utilisateur']]);
        $enseignant = $stmt->fetch(PDO::FETCH_ASSOC);

        $id_responsable_module = $enseignant['id_enseignant'];

        // Récupération des données spécifiques
        $dsDetails = 'DS : ' . ($_POST['dsDetails'] ?? ''); // Détails DS
        $salle016 = $_POST['salle016'] ?? '';   // Salles 016
        $scheduleDetails = $_POST['scheduleDetails'] ?? ''; // Besoins en chariots/salles

        // Construction de "equipements_specifiques"
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

        // Récupérer le premier type d'heure pour remplir type_salle
        $type_salle = $_POST['hour_type'][0] ?? 'Inconnu';

        // Insertion dans la table details_cours
        $stmtDetails = $bdd->prepare("
            INSERT INTO details_cours (
                id_cours,
                id_responsable_module,
                type_salle,
                equipements_specifiques,
                details
            ) VALUES (
                :id_cours,
                :id_responsable_module,
                :type_salle,
                :equipements_specifiques,
                :details
            )
        ");
        $stmtDetails->execute([
            ':id_cours' => $id_cours,
            ':id_responsable_module' => $id_responsable_module,
            ':type_salle' => $type_salle,
            ':equipements_specifiques' => $equipementsSpecifiques,
            ':details' => $dsDetails
        ]);

        //Alert et redirection
        echo "<script>alert('Les détails du cours ont été ajoutés avec succès.');</script>";
        echo "<script>window.location = 'index.php?action=accueilEnseignant';</script>";
    }
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
