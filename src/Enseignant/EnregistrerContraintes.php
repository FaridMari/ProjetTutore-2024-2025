<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Définir l'id_utilisateur à 32 directement
        $id_utilisateur = 16;

        // Connexion à la base de données
        $conn = connexionFactory::makeConnection();

        // Définir les plages horaires disponibles
        $horaires = [
            ['jour' => 'lundi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'lundi_8_10'],
            ['jour' => 'mardi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'mardi_8_10'],
            ['jour' => 'mercredi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'mercredi_8_10'],
            ['jour' => 'jeudi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'jeudi_8_10'],
            ['jour' => 'vendredi', 'heure_debut' => '08:00', 'heure_fin' => '10:00', 'key' => 'vendredi_8_10'],
        ];

        // Préparer la requête SQL
        $stmt = $conn->prepare("
            INSERT INTO contraintes (id_utilisateur, jour, heure_debut, heure_fin)
            VALUES (:id_utilisateur, :jour, :heure_debut, :heure_fin)
        ");

        // Insérer les contraintes cochées
        foreach ($horaires as $horaire) {
            if (isset($_POST[$horaire['key']])) {
                $stmt->bindValue(':id_utilisateur', $id_utilisateur, \PDO::PARAM_INT);
                $stmt->bindValue(':jour', $horaire['jour'], \PDO::PARAM_STR);
                $stmt->bindValue(':heure_debut', $horaire['heure_debut'], \PDO::PARAM_STR);
                $stmt->bindValue(':heure_fin', $horaire['heure_fin'], \PDO::PARAM_STR);
                $stmt->execute();
            }
        }

        echo "<script>alert('Vos contraintes ont été enregistrées avec succès.'); window.location.href = 'index.php?action=enseignantFicheContrainte';</script>";
    } catch (Exception $e) {
        echo "Erreur : " . $e->getMessage();
    }
} else {
    echo "Accès non autorisé.";
}
