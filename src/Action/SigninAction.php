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

                $stmt = $pdo->prepare("SELECT id_utilisateur, email, mot_de_passe, role, supprimer FROM utilisateurs WHERE email = :email");
                $stmt->bindParam(':email', $email);
                $stmt->execute();

                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ( ($password === $user['mot_de_passe'] || password_verify($password, $user['mot_de_passe'])) && !$user['supprimer']) {
                    // Authentification réussie
                    session_destroy();
                    session_start();
                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
                    $_SESSION['user_nom'] = $user['nom'];
                    $_SESSION['user_prenom'] = $user['prenom'];

                    // Création d'un cookie avec l'idUser
                    setcookie('user_id', $user['id_utilisateur'], time() + 3600, '/', '', false, true); // Cookie sécurisé

                    if ($user['role'] === 'gestionnaire') {
                        header('Location: index.php?action=gestionnairePagePrincipal');
                    } else {
                        header('Location: index.php?action=accueilEnseignant');
                    }
                    exit();
                } else {
                    echo "L'authentification a échoué. Veuillez vérifier vos identifiants.(sign in action)";
                }
            } catch (\PDOException $e) {
                echo "Erreur d'authentification: " . $e->getMessage();
            }
        } else {
            ob_start();
            include 'src/User/login.php';
            return returnHTML();
        }

        return '';
    }
}
?>
