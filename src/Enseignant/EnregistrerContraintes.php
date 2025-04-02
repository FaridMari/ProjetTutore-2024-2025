<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    $conn = connexionFactory::makeConnection();

    $id_utilisateur = $_POST['id_utilisateur'] ?? ($_SESSION['id_utilisateur'] ?? null);
    $isFromGestionnaire = isset($_POST['modification_gestionnaire']);
    $isRemplissage = isset($_POST['source']) && $_POST['source'] === 'remplir'; // 🆕

    if (!$id_utilisateur) {
        throw new Exception("ID utilisateur manquant.");
    }

    $creneau_preference = $_POST['creneau_prefere'] ?? $_POST['creneau_preference'] ?? "Non spécifié"; // accepte les deux noms
    $cours_samedi = $_POST['cours_samedi'] ?? "Non spécifié";
    $choix_contraintes = $_POST['contraintes'] ?? [];
    $commentaire = $_POST['commentaire'] ?? "";

    $conn->beginTransaction();

    // Supprimer anciennes contraintes si existantes
    $stmtDelete = $conn->prepare("DELETE FROM contraintes WHERE id_utilisateur = ?");
    $stmtDelete->execute([$id_utilisateur]);

    if (empty($choix_contraintes)) {
        // Si aucun créneau sélectionné, on en insère quand même une ligne "vide"
        $stmtInsert = $conn->prepare("INSERT INTO contraintes (id_utilisateur, jour, heure_debut, heure_fin, creneau_preference, cours_samedi, commentaire, statut)
                                      VALUES (?, NULL, NULL, NULL, ?, ?, ?, 'en attente')");
        $stmtInsert->execute([$id_utilisateur, $creneau_preference, $cours_samedi, $commentaire]);
    } else {
        foreach ($choix_contraintes as $contrainte) {
            $parts = explode('_', $contrainte);
            $jour = $parts[0];
            $heure_debut = str_replace('h', '', $parts[1]);
            $heure_fin = intval($heure_debut) + 2;

            $stmtInsert = $conn->prepare("INSERT INTO contraintes (id_utilisateur, jour, heure_debut, heure_fin, creneau_preference, cours_samedi, commentaire, statut)
                                          VALUES (?, ?, ?, ?, ?, ?, ?, 'en attente')");
            $stmtInsert->execute([$id_utilisateur, $jour, $heure_debut, $heure_fin, $creneau_preference, $cours_samedi, $commentaire]);
        }
    }

    $conn->commit();

    //Déverrouiller la fiche après modification/remplissage
    $conn->prepare("UPDATE contraintes SET modification_en_cours = 0 WHERE id_utilisateur = ?")->execute([$id_utilisateur]);

    if ($isFromGestionnaire) {
        $_SESSION['toast_message'] = "La fiche a été modifiée.";
        header("Location: ../../index.php?action=ficheEnseignant");
    } elseif ($isRemplissage) {
        $_SESSION['toast_message'] = "La fiche a été remplie.";
        header("Location: ../../index.php?action=ficheEnseignant");
    } else {
        $_SESSION['success_message'] = "Fiche enregistrée.";
        header("Location: ../../index.php?action=enseignantFicheContrainte");
    }

    exit();

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
    header("Location: ../../index.php?action=enseignantFicheContrainte");
    exit();
}
