<?php
namespace src\Action;

use src\Db\connexionFactory;

class CreerUtilisateurAction {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = $_POST['nom'] ?? '';
            $prenom = $_POST['prenom'] ?? '';
            $email = $_POST['email'] ?? '';
            $statut = $_POST['statut'] ?? '';
            $role = $_POST['role'] ?? '';
            $nombre_heures = $_POST['nombre_heures'] ?? 0;

            if (empty($nom) || empty($prenom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "Erreur : Veuillez remplir tous les champs obligatoires.";
            }

            try {
                $conn = connexionFactory::makeConnection();

                $stmt = $conn->prepare("INSERT INTO utilisateurs (nom, prenom, email, statut, role, nombre_heures) 
                                        VALUES (:nom, :prenom, :email, :statut, :role, :nombre_heures)");
                $stmt->bindParam(':nom', $nom);
                $stmt->bindParam(':prenom', $prenom);
                $stmt->bindParam(':email', $email);
                $stmt->bindParam(':statut', $statut);
                $stmt->bindParam(':role', $role);
                $stmt->bindParam(':nombre_heures', $nombre_heures);
                $stmt->execute();

                // Récupérer l'id_utilisateur généré automatiquement
                $id_utilisateur = $conn->lastInsertId();

                $stmt2 = $conn->prepare("INSERT INTO contraintes (id_utilisateur) 
                             VALUES (:id_utilisateur)");
                $stmt2->bindParam(':id_utilisateur', $id_utilisateur);


                header("Location: src/Gestionnaire/LienEmail.php?email=" . urlencode($email));
                exit();

            } catch (\PDOException $e) {
                return "Erreur lors de la création de l'utilisateur : " . $e->getMessage();
            }
        }

        return "Veuillez soumettre le formulaire.";
    }
}
