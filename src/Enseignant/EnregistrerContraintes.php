<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    $conn = connexionFactory::makeConnection();

    $id_utilisateur = $_POST['id_utilisateur'] ?? null;
    $isFromGestionnaire = isset($_POST['modification_gestionnaire']);

    if (!$id_utilisateur) {
        throw new Exception("ID utilisateur manquant.");
    }

    $creneau_preference = $_POST['creneau_prefere'] ?? "Non spécifié";
    $cours_samedi = $_POST['cours_samedi'] ?? "Non spécifié";
    $choix_contraintes = $_POST['contraintes'] ?? [];
    $commentaire = $_POST['commentaire'] ?? "";

    $conn->beginTransaction();
    $stmtDelete = $conn->prepare("DELETE FROM contraintes WHERE id_utilisateur = ?");
    $stmtDelete->execute([$id_utilisateur]);

    foreach ($choix_contraintes as $contrainte) {
        $parts = explode('_', $contrainte);
        $jour = $parts[0];
        $heure_debut = str_replace('h', '', $parts[1]);
        $heure_fin = intval($heure_debut) + 2;

        $stmtInsert = $conn->prepare("INSERT INTO contraintes (id_utilisateur, jour, heure_debut, heure_fin, creneau_preference, cours_samedi, commentaire, statut)
                                      VALUES (?, ?, ?, ?, ?, ?, ?, 'en attente')");
        $stmtInsert->execute([$id_utilisateur, $jour, $heure_debut, $heure_fin, $creneau_preference, $cours_samedi, $commentaire]);
    }

    $conn->commit();

    if ($isFromGestionnaire) {
        $_SESSION['toast_message'] = "Vous avez modifié cette fiche.";
        header("Location: ../../index.php?action=ficheEnseignant");
    } else {
        $_SESSION['success_message'] = "Fiche enregistrée.";
        header("Location: ../../index.php?action=enseignantFicheContrainte");
    }
    // Lever le verrou après modification
    $conn->prepare("UPDATE contraintes SET modification_en_cours = 0 WHERE id_utilisateur = ?")->execute([$id_utilisateur]);

    exit();

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
    header("Location: ../../index.php?action=enseignantFicheContrainte");

    exit();
}
