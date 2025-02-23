<?php
namespace src\Action;

use src\Db\connexionFactory;
use PDO;

class DeleteEnseignantAction {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
            $email = trim($_POST['email']);

            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return "<script>alert('Adresse email invalide ou vide.');</script>";
            }

            try {
                $conn = connexionFactory::makeConnection();
                $conn->beginTransaction();

                // Récupérer l'id_utilisateur associé à l'email
                $stmt = $conn->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = :email");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                $id_utilisateur = $stmt->fetchColumn();

                if (!$id_utilisateur) {
                    return "<script>alert('Aucun utilisateur trouvé avec cet email.');</script>";
                }



                // Modifier l'attribut supprimer d'un utilisateur
                $stmt = $conn->prepare("UPDATE utilisateurs SET supprimer = true WHERE id_utilisateur = :id_utilisateur");
                $stmt->bindParam(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
                $stmt->execute();

                $conn->commit();

                return "<script>
                            alert('L\\'utilisateur a été supprimé avec succès.');
                            window.location.href = 'index.php?action=gestionCompteUtilisateur';
                        </script>";
            } catch (\PDOException $e) {
                return "<script>alert('Une erreur est survenue : " . addslashes($e->getMessage()) . "');</script>";
            }
        }

        // Inclure le fichier HTML
        ob_start();
        include __DIR__ . '/../Gestionnaire/Page_DeleteUtilisateur.php';
        return ob_get_clean();
    }
}
