<?php
require_once __DIR__ . '/../Db/connexionFactory.php';

use src\Db\connexionFactory;

$email = $_GET['email'] ?? '';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Envoyer un email à l'utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f9f9f9;
            margin: 0;
        }

        form {
            background: #fff;
            padding: 2em;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            text-align: center;
            width: 100%;
            max-width: 400px;
        }

        h1 {
            margin-bottom: 1em;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 0.5em;
            color: #555;
            font-size: 1em;
            text-align: left;
        }

        input[type="email"] {
            width: 100%;
            padding: 0.8em;
            margin-bottom: 1em;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 1em;
        }

        button {
            padding: 0.8em 1.5em;
            background-color: #007BFF;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<form method="POST" action="">
    <h1>Envoyer un email</h1>
    <label for="email">Email :</label>
    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" required>
    <button type="submit">Envoyer l'email</button>
</form>
</body>
</html>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['email'])) {
    $email = $_POST['email'];

    try {
        $conn = connexionFactory::makeConnection();

        // Vérifier si l'utilisateur existe
        $stmt = $conn->prepare("SELECT id_utilisateur FROM utilisateurs WHERE email = :email");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // Générer un jeton unique
            $token = bin2hex(random_bytes(16));
            $expiry = date('Y-m-d H:i:s', time() + (24 * 60 * 60)); // Durée de validité : 24 heures

            // Mettre à jour le jeton et l'expiration dans la table utilisateurs
            $stmt = $conn->prepare("UPDATE utilisateurs 
                                    SET reset_token = :token, reset_token_expiration = :expiry 
                                    WHERE id_utilisateur = :id_utilisateur");
            $stmt->bindParam(':token', $token, PDO::PARAM_STR);
            $stmt->bindParam(':expiry', $expiry, PDO::PARAM_STR);
            $stmt->bindParam(':id_utilisateur', $user['id_utilisateur'], PDO::PARAM_INT);
            $stmt->execute();

            // Construire dynamiquement le lien avec le token et l'email
            $host = $_SERVER['HTTP_HOST'];
            $path = dirname($_SERVER['PHP_SELF']);
            $passwordLink = "http://$host$path/password.php?token=" . urlencode($token) . "&email=" . urlencode($email);

            // Ajouter le lien au message
            $message = "Bonjour,\n\nCliquez sur le lien suivant pour choisir votre mot de passe. Ce lien est valable 24 heures :\n$passwordLink";

            // Envoyer l'email
            $subject = "Choisissez votre mot de passe";
            $headers = "From: no-reply@$host";

            if (mail($email, $subject, $message, $headers)) {
                // Redirection et message de succès
                echo "<script>alert('Email envoyé avec succès.'); window.location.href = '../../index.php?action=gestionCompteUtilisateur';</script>";
            } else {
                echo "<script>alert('Erreur lors de l\'envoi de l\'email.');</script>";
            }

        } else {
            echo "<script>alert('Utilisateur introuvable.');</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Une erreur est survenue : " . addslashes($e->getMessage()) . "');</script>";
    }
}
?>
