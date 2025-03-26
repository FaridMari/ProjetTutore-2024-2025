<?php
namespace src\Action;

use src\Db\connexionFactory;
use PDOException;

class GestionnaireCreerUtilisateurAction {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';
            $statut = $_POST['statut'] ?? '';
            $role = $_POST['role'] ?? '';
            $nombre_heures = $_POST['nombre_heures'] ?? 0;
            $nb_contraintes = $_POST['nombre_contrainte'] ?? 4;

            // Validation des champs
            if (empty($nom) || empty($prenom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Erreur : Veuillez remplir tous les champs obligatoires avec des valeurs valides.";
            }

            try {
                // Connexion à la base de données
                $conn = connexionFactory::makeConnection();
                $conn->beginTransaction();

                // Insertion dans la table utilisateurs
                $stmt = $conn->prepare("
                    INSERT INTO utilisateurs (nom, prenom, email, statut, role, nombre_heures)
                    VALUES (:nom, :prenom, :email, :statut, :role, :nombre_heures)
                ");
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':statut', $statut);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':nombre_heures', $nombre_heures);
                $stmt->execute();

                // Récupération l'id_utilisateur
                $id_utilisateur = $conn->lastInsertId();

                // Insertion dans la table enseignants
                $stmt2 = $conn->prepare("
                    INSERT INTO enseignants (id_utilisateur, statut, nb_contrainte)
                    VALUES (:id_utilisateur, :statut, :nb_contrainte)
                ");
                $stmt2->bindParam(':id_utilisateur', $id_utilisateur);
                $stmt2->bindParam(':statut', $statut);
                $stmt2->bindParam(':nb_contrainte', $nb_contraintes);
                $stmt2->execute();

                // Insérer dans la table contraintes (avec id_utilisateur)
                $stmt3 = $conn->prepare("
                    INSERT INTO contraintes (id_utilisateur)
                    VALUES (:id_utilisateur)
                ");
                $stmt3->bindParam(':id_utilisateur', $id_utilisateur);
                $stmt3->execute();

                // Confirmer la transaction
                $conn->commit();

                // Redirection après création
                header("Location: src/Gestionnaire/Page_EnvoyerEmail.php?email=" . urlencode($email));
                return "Utilisateur créé avec succès.";
            } catch (PDOException $e) {
                //$conn->rollBack();
                return "Erreur lors de la création de l'utilisateur : " . $e->getMessage();
            }
        }

        return "Veuillez soumettre le formulaire.";
    }
}
