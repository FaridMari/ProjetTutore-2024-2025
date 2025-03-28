<?php
namespace src\Action;

use src\Db\connexionFactory;

class SigninAction extends Action {
    public function execute(): string {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = $_POST['email'] ?? '';
            $password = $_POST['password'] ?? '';

            try {
                $pdo = connexionFactory::makeConnection();
                $stmt = $pdo->prepare("SELECT id_utilisateur, email, mot_de_passe, role, supprimer, nom, prenom FROM utilisateurs WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($user && (password_verify($password, $user['mot_de_passe']) && !$user['supprimer'])) {
                    // Authentification réussie
                    if (session_status() !== PHP_SESSION_ACTIVE) {
                        session_start(); // Vérifie que la session est active
                    }

                    // Sauvegarder l'information dans la session
                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];

                    // Création du cookie (sécurisé) pour Puppeteer
                    setcookie('user_id', $user['id_utilisateur'], time() + 3600, '/', '', false, true);

                    // Redirection en fonction du rôle de l'utilisateur
                    if ($user['role'] === 'gestionnaire') {
                        echo json_encode(['success' => true, 'redirect' => 'index.php?action=gestionnairePagePrincipal']);
                    } else {
                        echo json_encode(['success' => true, 'redirect' => 'index.php?action=accueilEnseignant']);
                    }
                    exit();
                } else {
                    // Renvoie error si l'authentification a échoué
                    echo json_encode(['success' => false, 'message' => 'Email ou mot de passe incorrect.']);
                }
            } catch (\PDOException $e) {
                echo json_encode(['success' => false, 'message' => 'Erreur de connexion à la base de données.']);
            }
        } else {
            ob_start();
            include 'src/User/login.php';
        }

        return '';
    }
}
?>
