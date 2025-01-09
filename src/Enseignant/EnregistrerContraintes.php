<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    // Connexion à la base de données
    $conn = connexionFactory::makeConnection();

    // Vérifiez que l'utilisateur est connecté
    if (!isset($_SESSION['id_utilisateur'])) {
        throw new Exception("Utilisateur non connecté.");
    }

    $id_utilisateur = $_SESSION['id_utilisateur'];

    // Récupérer le créneau préféré
    $creneau_preference = !empty($_POST['creneau_prefere']) ? $_POST['creneau_prefere'] : null;
    $cours_samedi = !empty($_POST['cours_samedi']) ? $_POST['cours_samedi'] : null;

    // Liste des horaires cochés
    $horaires = [
        ['jour' => 'lundi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'lundi_8_10'],
        ['jour' => 'mardi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'mardi_8_10'],
        ['jour' => 'mercredi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'mercredi_8_10'],
        ['jour' => 'jeudi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'jeudi_8_10'],
        ['jour' => 'vendredi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'vendredi_8_10'],
        ['jour' => 'lundi', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'key' => 'lundi_10_12'],
        ['jour' => 'mardi', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'key' => 'mardi_10_12'],
        ['jour' => 'mercredi', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'key' => 'mercredi_10_12'],
        ['jour' => 'jeudi', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'key' => 'jeudi_10_12'],
        ['jour' => 'vendredi', 'heure_debut' => '10:00', 'heure_fin' => '12:00', 'key' => 'vendredi_10_12'],
        ['jour' => 'lundi', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'key' => 'lundi_14_16'],
        ['jour' => 'mardi', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'key' => 'mardi_14_16'],
        ['jour' => 'mercredi', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'key' => 'mercredi_14_16'],
        ['jour' => 'jeudi', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'key' => 'jeudi_14_16'],
        ['jour' => 'vendredi', 'heure_debut' => '14:00', 'heure_fin' => '16:00', 'key' => 'vendredi_14_16'],
        ['jour' => 'lundi', 'heure_debut' => '16:00', 'heure_fin' => '18:00', 'key' => 'lundi_16_18'],
        ['jour' => 'mardi', 'heure_debut' => '16:00', 'heure_fin' => '18:00', 'key' => 'mardi_16_18'],
        ['jour' => 'mercredi', 'heure_debut' => '16:00', 'heure_fin' => '18:00', 'key' => 'mercredi_16_18'],
        ['jour' => 'jeudi', 'heure_debut' => '16:00', 'heure_fin' => '18:00', 'key' => 'jeudi_16_18'],
        ['jour' => 'vendredi', 'heure_debut' => '16:00', 'heure_fin' => '18:00', 'key' => 'vendredi_16_18'],
    ];

    // Commencer une transaction
    $conn->beginTransaction();

    // Supprimer les anciennes contraintes de l'utilisateur
    $stmtDelete = $conn->prepare("DELETE FROM contraintes WHERE id_utilisateur = :id_utilisateur");
    $stmtDelete->bindValue(':id_utilisateur', $id_utilisateur, \PDO::PARAM_INT);
    $stmtDelete->execute();

    // Insérer les nouvelles contraintes, y compris le créneau préféré
    $stmtInsert = $conn->prepare("
        INSERT INTO contraintes (id_utilisateur, jour, heure_debut, heure_fin, creneau_preference, cours_samedi)
        VALUES (:id_utilisateur, :jour, :heure_debut, :heure_fin, :creneau_preference, :cours_samedi)
    ");

    foreach ($horaires as $horaire) {
        if (!empty($_POST[$horaire['key']])) {
            $stmtInsert->bindValue(':id_utilisateur', $id_utilisateur, \PDO::PARAM_INT);
            $stmtInsert->bindValue(':jour', $horaire['jour'], \PDO::PARAM_STR);
            $stmtInsert->bindValue(':heure_debut', $horaire['heure_debut'], \PDO::PARAM_STR);
            $stmtInsert->bindValue(':heure_fin', $horaire['heure_fin'], \PDO::PARAM_STR);
            $stmtInsert->bindValue(':creneau_preference', $creneau_preference, \PDO::PARAM_STR);
            $stmtInsert->bindValue(':cours_samedi', $cours_samedi, \PDO::PARAM_STR);
            $stmtInsert->execute();
        }
    }
    // Valider la transaction
    $conn->commit();

    echo "<script>alert('Vos contraintes ont été mises à jour avec succès.'); window.location.href = '../../index.php?action=enseignantFicheContrainte';</script>";
    exit();
} catch (Exception $e) {
    // Annuler la transaction en cas d’erreur
    global $conn;
    $conn->rollBack();
    echo "<script>alert('Erreur : " . addslashes($e->getMessage()) . "'); window.location.href = '../../index.php?action=enseignantPagePrincipal';</script>";
    exit();
}
