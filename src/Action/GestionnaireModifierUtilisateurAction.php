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
            $telephone = trim($_POST['telephone']);
            $role = trim($_POST['role']);
            $statut = trim($_POST['statut']);
            $nombre_heures = trim($_POST['nombre_heures']);
            $nb_contrainte = trim($_POST['nb_contrainte']);
            $responsable = isset($_POST['responsable']) ? 'oui' : 'non';

            if (empty($nom) || empty($prenom) || empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                // Retourner une erreur
                echo json_encode(['success' => false]);
            }

            try {
                $conn = connexionFactory::makeConnection();
                $stmt = $conn->prepare("UPDATE utilisateurs
                                        SET nom = :nom, prenom = :prenom, email = :email, telephone = :telephone,
                                            role = :role, statut = :statut, nombre_heures = :nombre_heures, responsable = :responsable
                                        WHERE id_utilisateur = :id");
                $stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
                $stmt->bindParam(':prenom', $prenom, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->bindParam(':telephone', $telephone, PDO::PARAM_STR);
                $stmt->bindParam(':role', $role, PDO::PARAM_STR);
                $stmt->bindParam(':statut', $statut, PDO::PARAM_STR);
                $stmt->bindParam(':nombre_heures', $nombre_heures, PDO::PARAM_INT);
                $stmt->bindParam(':responsable', $responsable, PDO::PARAM_STR);
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();

                // Si c'est un enseignant, mettre à jour le nombre de contraintes
                if ($role === 'enseignant') {
                    $stmt = $conn->prepare("UPDATE enseignants
                                            SET nb_contrainte = :nb_contrainte
                                            WHERE id_utilisateur = :id");
                    $stmt->bindParam(':nb_contrainte', $nb_contrainte, PDO::PARAM_INT);
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                }

                echo json_encode(['success' => true]);
            } catch (\PDOException $e) {
                echo json_encode(['success' => false]);
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
                $id = $_GET['id'];
                $stmt = $conn->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = :id");
                $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                $stmt->execute();
                $selectedUser = $stmt->fetch(PDO::FETCH_ASSOC);

                // Si c'est un enseignant, récupérer le nombre de contraintes
                if ($selectedUser['role'] === 'enseignant') {
                    $stmt = $conn->prepare("SELECT nb_contrainte FROM enseignants WHERE id_utilisateur = :id");
                    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                    $stmt->execute();
                    $selectedUser['nb_contrainte'] = $stmt->fetchColumn();
                }
            }

            // Inclure le fichier HTML
            ob_start();
            include __DIR__ . '/../Gestionnaire/Page_EditUtilisateur.php';
            return ob_get_clean();
        } catch (\PDOException $e) {
            echo "Erreur : " . $e->getMessage();
        }
    }
}
