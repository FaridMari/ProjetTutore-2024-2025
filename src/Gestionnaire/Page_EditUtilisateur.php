<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;

            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;

            margin: 0;
            height: 100vh;
            padding: 2em;
        }

        h1 {
            text-align: center;
            color: #000;
            margin-bottom: 1em;
        }

        form {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 2em;
            max-width: 400px;
            width: 100%;
            color: #000;
            margin-bottom: 1%;
        }

        label {
            display: block;
            color: #444;
            margin-top: 1em;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            color: #000;
        }

        button {
            display: block;
            width: 100%;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;

            transition: background-color 0.3s, color 0.3s;
        }

        button[type="submit"] {
            background-color: #FFEF65;
            color: #000;
            font-weight: 600;
            margin-top: 1em;
        }

        button[type="submit"]:hover {
            background-color: #FFE74A;
            color: #000;
        }

        #retour {
            margin-top: 1em;
            background-color: #000;
            color: #fff;
            border: none;
        }

        #retour:hover {
            background-color: #222;
            color: #fff;
        }
    </style>

</head>
<body>
<h1>Modifier un utilisateur</h1>

<!-- Liste déroulante des utilisateurs -->
<form method="get" action="index.php">
    <input type="hidden" name="action" value="edit-user">
    <label for="user-select">Sélectionnez un utilisateur :</label>
    <select id="user-select" name="id" onchange="this.form.submit()">
        <option value="">-- Choisissez un utilisateur --</option>
        <?php foreach ($users as $user): ?>
            <?php if ($user['supprimer'] == 0): ?>
                <option value="<?php echo $user['id_utilisateur']; ?>"
                    <?php echo (isset($selectedUser) && $selectedUser['id_utilisateur'] === $user['id_utilisateur']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</form>

<?php if (isset($selectedUser)): ?>
    <!-- Formulaire de modification -->
    <form method="post" action="index.php?action=edit-user">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($selectedUser['id_utilisateur']); ?>">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($selectedUser['nom']); ?>" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($selectedUser['prenom']); ?>" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($selectedUser['email']); ?>" required>

        <label for="statut">Statut :</label>
        <select id="statut" name="statut" required>
            <option value="enseignant-chercheur" <?php echo $selectedUser['statut'] === 'enseignant-chercheur' ? 'selected' : ''; ?>>Enseignant-Chercheur</option>
            <option value="enseignant" <?php echo $selectedUser['statut'] === 'enseignant' ? 'selected' : ''; ?>>Enseignant</option>
            <option value="vacataire" <?php echo $selectedUser['statut'] === 'vacataire' ? 'selected' : ''; ?>>Vacataire</option>
            <option value="pro" <?php echo $selectedUser['statut'] === 'pro' ? 'selected' : ''; ?>>Salarié</option>
        </select>

        <button type="submit">Modifier l'utilisateur</button>
        <button id="retour" onclick="window.location.href='index.php?action=gestionCompteUtilisateur'; return false;">Retour au menu</button>
    </form>
<?php endif; ?>
</body>
</html>
