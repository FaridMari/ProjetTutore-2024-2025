<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';

use src\Db\connexionFactory;

try {
    $conn = connexionFactory::makeConnection();

    if (!isset($_SESSION['id_utilisateur'])) {
        throw new Exception("Utilisateur non connecté.");
    }

    $id_utilisateur = $_SESSION['id_utilisateur'];

    // Gestion verrouillage par le gestionnaire
    if (isset($_SESSION['fiche_contrainte_en_edition']) && $_SESSION['fiche_contrainte_en_edition'] == $id_utilisateur) {
        $_SESSION['error_message'] = "Vous ne pouvez plus accéder à cette fiche, elle est en cours de consultation par un gestionnaire.";
        header("Location: ../../index.php?action=enseignantFicheContrainte");
        exit();
    }

    // Récupération infos utilisateur
    $stmtUser = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = :id_utilisateur");
    $stmtUser->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmtUser->execute();
    $user = $stmtUser->fetch();

    // Vérification fiche validée
    $stmtCheck = $conn->prepare("SELECT statut FROM contraintes WHERE id_utilisateur = :id_utilisateur LIMIT 1");
    $stmtCheck->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmtCheck->execute();
    $existingContrainte = $stmtCheck->fetch();

    $ficheValidee = $existingContrainte && $existingContrainte['statut'] === 'valide';

    // Récupération des données
    $creneau_preference = $_POST['creneau_prefere'] ?? "Non spécifié";
    $cours_samedi = $_POST['cours_samedi'] ?? "Non spécifié";
    $choix_contraintes = $_POST['contraintes'] ?? [];
    $commentaire = $_POST['commentaire'] ?? "";



    if (!$ficheValidee) {
        $conn->beginTransaction();

        // Suppression anciennes contraintes
        $stmtDelete = $conn->prepare("DELETE FROM contraintes WHERE id_utilisateur = :id_utilisateur AND statut != 'valide'");
        $stmtDelete->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
        $stmtDelete->execute();

        // Insertion des contraintes
        foreach ($choix_contraintes as $contrainte) {
            $stmtInsert = $conn->prepare("INSERT INTO contraintes (id_utilisateur, jour, heure_debut, heure_fin, creneau_preference, cours_samedi, commentaire, statut) 
                VALUES (?, ?, ?, ?, ?, ?, ?, 'en attente')");
            $jour_heure = explode('_', $contrainte);
            $jour = $jour_heure[0];
            $heure_debut = str_replace('h', '', $jour_heure[1]);
            $heure_fin = intval($heure_debut) + 2;


            $stmtInsert->execute([$id_utilisateur, $jour, $heure_debut, $heure_fin, $creneau_preference, $cours_samedi, $commentaire]);
        }

        $conn->commit();
    }

    // Enregistrement en session pour affichage
    $_SESSION['pdf_data'] = [
        'nom_prenom' => $user['nom'] . ' ' . $user['prenom'],
        'choix_contraintes' => $choix_contraintes,
        'creneau_prefere' => $creneau_preference,
        'cours_samedi' => $cours_samedi,
        'commentaire' => $commentaire
    ];

    $_SESSION['success_message'] = "Les contraintes ont bien été enregistrées.";

    // Affichage fiche selon statut
    if ($ficheValidee) {
        $_SESSION['info_message'] = "Votre fiche a été validée. Vous pouvez la télécharger au format PDF.";
        header("Location: ../../index.php?action=enseignantFicheContrainte&pdf=1");
    } else {
        header("Location: ../../index.php?action=enseignantFicheContrainte");
    }
    exit();

} catch (Exception $e) {
    if ($conn->inTransaction()) $conn->rollBack();
    $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
    header("Location: ../../index.php?action=enseignantFicheContrainte");
    exit();
}