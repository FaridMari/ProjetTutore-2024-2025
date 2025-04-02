<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    $bdd = connexionFactory::makeConnection();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $estGestionnaire = isset($_SESSION['type']) && $_SESSION['type'] === 'gestionnaire';

        $id_utilisateur = $_POST['id_utilisateur'] ?? null;
        $id_responsable_module = $_POST['responsibleName'] ?? null;
        $ds = $_POST['dsDetails'] ?? '';
        $commentaire = $_POST['scheduleDetails'] ?? '';
        $systeme = $_POST['systeme'] ?? '';

        $salle016 = $_POST['salle016'] ?? '';
        $equipementsSpecifiques = match($salle016) {
            'Oui' => "salle 016 : oui",
            'Indifférent' => "salle 016 : indifférent",
            'Non' => "salle 016 : non",
            default => '',
        };

        if (!$id_utilisateur) {
            throw new Exception("ID utilisateur manquant.");
        }

        $stmtCours = $bdd->prepare("SELECT id_ressource FROM details_cours d
            INNER JOIN enseignants e ON e.id_enseignant = d.id_responsable_module
            WHERE e.id_utilisateur = ?");
        $stmtCours->execute([$id_utilisateur]);
        $fiche = $stmtCours->fetch(PDO::FETCH_ASSOC);

        if (!$fiche) {
            throw new Exception("Aucune fiche trouvée pour cet utilisateur.");
        }

        $stmtUpdate = $bdd->prepare("UPDATE details_cours SET
            id_responsable_module = :id_responsable_module,
            equipements_specifiques = :equipements_specifiques,
            ds = :ds,
            commentaire = :commentaire,
            systeme = :systeme,
            statut = :statut
            WHERE id_ressource = :id_ressource");

        $stmtUpdate->execute([
            ':id_responsable_module'    => $id_responsable_module,
            ':equipements_specifiques'  => $equipementsSpecifiques,
            ':ds'                       => $ds,
            ':commentaire'              => $commentaire,
            ':systeme'                  => $systeme,
            ':statut'                   => 'en attente',
            ':id_ressource'             => $fiche['id_ressource']
        ]);

        $_SESSION['toast_message'] = "Votre fiche ressource a été modifiée.";
        $_SESSION['toast_type'] = "info";

        header('Location: ../../index.php?action=ficheEnseignant');
        exit();
    }

} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
