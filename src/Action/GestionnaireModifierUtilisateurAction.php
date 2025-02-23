<?php
namespace src\Action;

use src\Db\connexionFactory;
use PDO;

class GestionnaireModifierUtilisateurAction {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
            // Modifier les informations de l'utilisateur
            $id = trim($_POST['id']);
            $nom = trim($_POST['nom']);
            $prenom = trim($_POST['prenom']);
            $email = trim($_POST['email']);
            $statut = trim($_POST['statut']);

            if (empty($nom) || empty($prenom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "<script>alert('Veuillez remplir tous les champs obligatoires.');</script>";
            }

            try {
                $conn = connexionFactory::makeConnection();
                $stmt = $conn->prepare("UPDATE utilisateurs 
                                        SET nom = :nom, prenom = :prenom, email = :email, statut = :statut 
                                        WHERE id_utilisateur = :id");
                $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    return "<script>
                                alert('Utilisateur modifié avec succès.');
                                window.location.href = 'index.php?action=gestionCompteUtilisateur';
                            </script>";
                } else {
                    return "<script>alert('Aucune modification détectée.');</script>";
                }
            } catch (\PDOException $e) {
                return "<script>alert('Une erreur est survenue : " . addslashes($e->getMessage()) . "');</script>";
            }
        }

        try {
            // Charger la liste des utilisateurs
            $conn = connexionFactory::makeConnection();
            $stmt = $conn->prepare("SELECT id_utilisateur, nom, prenom, supprimer FROM utilisateurs");
            $stmt->execute();
            $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $selectedUser = null;
            if (isset($_GET['id'])) {
                // Charger les détails de l'utilisateur sélectionné
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $selectedUser = $stmt->fetch(PDO::FETCH_ASSOC);
            }

            // Inclure le fichier HTML
            ob_start();
            include __DIR__ . '/../Gestionnaire/Page_EditUtilisateur.php';
            return ob_get_clean();
        } catch (\PDOException $e) {
            return "<script>alert('Une erreur est survenue : " . addslashes($e->getMessage()) . "');</script>";
        }
    }
}
