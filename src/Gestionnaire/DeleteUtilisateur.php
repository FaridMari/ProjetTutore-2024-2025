<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Supprimer un utilisateur</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            background-color: #eef2ff; /* Fond bleu clair */
            margin: 2em;
            padding: 0;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            text-align: center;
            color: #003366; /* Titre en bleu foncé */
        }

        form {
            background: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 2em;
            max-width: 30%;
            width: 100%;
        }

        label {
            display: block;
            color: #003366; /* Label en bleu foncé */
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #003366; /* Bordure bleue */
            border-radius: 5px;
            font-size: 14px;
        }

        button {
            display: block;
            width: 100%;
            background-color: #007bff; /* Boutons en bleu */
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        #retour {
            margin-top: 1em;
            background-color: #ffffff;
            color: #007bff;
            border: 1px solid #007bff; /* Bouton Retour en bleu */
        }

        button:hover {
            background-color: #0056b3; /* Couleur au survol */
        }
    </style>
</head>
<body>
<h1>Supprimer un utilisateur</h1>

<form method="post" action="index.php?action=delete-user">
    <label for="email">Email de l'utilisateur à supprimer :</label>
    <select id="email" name="email" required>
        <option value="">-- Sélectionnez un email --</option>
        <?php
        // Connexion à la base de données
        require_once __DIR__ . '/../Db/connexionFactory.php';
        use src\Db\connexionFactory;

        try {
            $conn = connexionFactory::makeConnection();

            // Récupérer les emails
            $stmt = $conn->prepare("SELECT email FROM utilisateurs");
            $stmt->execute();
            $emails = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($emails as $row) {
                echo '<option value="' . htmlspecialchars($row['email']) . '">' . htmlspecialchars($row['email']) . '</option>';
            }
        } catch (Exception $e) {
            echo '<option disabled>Erreur lors du chargement des emails</option>';
        }
        ?>
    </select>
    <button type="submit">Supprimer l'utilisateur</button>

    <button id="retour" onclick="window.location.href='index.php?action=gestionCompteUtilisateur'; return false;">Retour au menu</button>
</form>
</body>
<script>
    // Ajouter une confirmation de suppression
    document.querySelector('form').addEventListener('submit', function (event) {
        if (!confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
            event.preventDefault();
        }
    });
</script>
</html>

