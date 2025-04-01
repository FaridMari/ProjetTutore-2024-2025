<?php

use src\Db\connexionFactory;
$pdo = connexionFactory::makeConnection();

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php"); // Rediriger vers la page de connexion si non connecté
    exit();
}

// Récupérer les informations de l'utilisateur
$stmt = $pdo->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = :id_utilisateur");
$stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Utilisateur non trouvé.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Enseignant</title>
    <style>
        .profile-container {
            background: #FFEF65;
            padding: 3em;
            border-radius: 15px;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
            text-align: center;
            width: 50%;
            margin: auto;
        }

        .profile-header {
            font-size: 2rem;
            font-weight: bold;
            color: #000;
            margin-bottom: 1.5em;
        }

        .profile-info {
            background: #f9f9f9;
            padding: 2em;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: left;
            font-size: 1.3rem;
        }

        .profile-info p {
            margin: 1em 0;
        }

        .profile-info span {
            font-weight: bold;
            color: #000;
        }

        .profile-button {
            margin-top: 2em;
        }

        .change-password-btn {
            background: #000;
            color: #fff;
            border: none;
            padding: 1em 2em;
            font-size: 1.2rem;
            font-weight: bold;
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .change-password-btn:hover {
            background: #333;
            transform: scale(1.05);
        }
    </style>
</head>
<body>
<div id="main-content">
    <div class="profile-container">
        <div class="profile-header">Profil Enseignant</div>

        <div class="profile-info">
            <p><span>Nom :</span> <?php echo htmlspecialchars($user['nom']); ?></p>
            <p><span>Prénom :</span> <?php echo htmlspecialchars($user['prenom']); ?></p>
            <p><span>Adresse mail :</span> <?php echo htmlspecialchars($user['email']); ?></p>
            <p><span>Statut :</span> <?php echo htmlspecialchars($user['role']); ?></p>
        </div>

        <form class="profile-button" method="POST" action="index.php?action=modifierMdp">
            <button class="change-password-btn" type="submit">Changer le mot de passe</button>
        </form>
    </div>
</div>
</body>
</html>
