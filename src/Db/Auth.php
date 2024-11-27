<?php

class AuthException extends \Exception {}

class Auth {
    public static function authenticate($email, $password, $db) {
        // Récupérer le mot de passe haché depuis la base
        $hashedPassword = self::getHashedPasswordFromDatabase($email, $db);

        // Vérifier si le mot de passe correspond au hash
        if ($hashedPassword && password_verify($password, $hashedPassword)) {
            $userId = self::getUserId($email, $db);

            // Créer un cookie pour stocker l'identifiant utilisateur
            setcookie('id_utilisateur', $userId, time() + 3600, '/'); // Durée : 1 heure
            return true;
        } else {
            throw new AuthException("L'authentification a échoué. Veuillez vérifier vos identifiants.");
        }
    }

    public static function getUserId($email, $pdo) {
        $query = "SELECT id_utilisateur FROM utilisateurs WHERE email = :email";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $userData = $stmt->fetch(\PDO::FETCH_ASSOC);

        if (!$userData) {
            throw new AuthException("Utilisateur non trouvé.");
        }

        return $userData['id_utilisateur'];
    }

    private static function getHashedPasswordFromDatabase($email, $db) {
        $sql = "SELECT mot_de_passe FROM utilisateurs WHERE email = :email";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':email', $email, \PDO::PARAM_STR);
        $stmt->execute();

        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        if ($result) {
            return $result['mot_de_passe'];
        } else {
            throw new AuthException("Utilisateur non trouvé.");
        }
    }
}

// Exemple de test
try {
    // Connexion à la base de données
    require_once 'connexionFactory.php';
    $db = connexionFactory::makeConnection();

    // Création de l'utilisateur test
    $email = "test@gmail.com";
    $password = "azertyuiop";
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Insérer l'utilisateur dans la base de données (à exécuter une seule fois)
    $db->prepare("
        INSERT INTO utilisateurs (email, mot_de_passe, role) 
        VALUES (:email, :mot_de_passe, 'enseignant')
    ")->execute(['email' => $email, 'mot_de_passe' => $hashedPassword]);

    // Authentification
    if (Auth::authenticate($email, $password, $db)) {
        echo "Connexion réussie pour l'utilisateur $email.";
    }
} catch (AuthException $e) {
    echo "Erreur d'authentification : " . $e->getMessage();
} catch (\PDOException $e) {
    echo "Erreur SQL : " . $e->getMessage();
}

