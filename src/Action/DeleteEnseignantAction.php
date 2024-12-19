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

                // Supprimer l'utilisateur
                $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE email = :email");
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                if ($stmt->rowCount() > 0) {
                    return "<script>
                                alert('L\\'utilisateur a été supprimé avec succès.');
                                window.location.href = 'index.php?action=gestionCompteUtilisateur';
                            </script>";
                } else {
                    return "<script>alert('Aucun utilisateur trouvé avec cet email.');</script>";
                }
            } catch (\PDOException $e) {
                return "<script>alert('Une erreur est survenue : " . addslashes($e->getMessage()) . "');</script>";
            }
        }

        // Inclure le fichier HTML
        ob_start();
        include __DIR__ . '/../Gestionnaire/DeleteUtilisateur.php';
        return ob_get_clean();
    }
}
