<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
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

        form, select {
            width: 100%;
            max-width: 400px;
        }

        label {
            display: block;
            color: #003366; /* Label en bleu foncé */
            margin-top: 1em;
        }

        input[type="text"],
        input[type="email"],
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

        button:hover {
            background-color: #0056b3; /* Couleur au survol */
        }

        #retour {
            margin-top: 1em;
            background-color: #ffffff;
            color: #007bff;
            border: 1px solid #007bff;
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
            <option value="<?php echo $user['id_utilisateur']; ?>"
                <?php echo (isset($selectedUser) && $selectedUser['id_utilisateur'] === $user['id_utilisateur']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?>
            </option>
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
