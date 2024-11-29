<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Créer un utilisateur</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            flex-direction: column;
            background-color: #f9f9f9;
            margin: 2em;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        form {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 2em;
            max-width: 30%;
            width: 100%;;
        }

        label {
            display: block;
            color: #555;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="password"] {
            width: 95%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }

        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }


        div {
            margin-bottom: 1em;
        }

        button {
            display: block;
            width: 100%;
            background-color: #007BFF;
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
            background-color: #FFF;
            color: #007BFF;
            border: 1px solid #007BFF;
        }

        button:hover {
            background-color: #0056b3;
        }

    </style>
</head>
<body>
<h1>Créer un Utilisateur</h1>
<form method="post" action="index.php?action=creer-utilisateur">
    <label for="nom">Nom :</label>
    <input type="text" id="nom" name="nom" required>

    <label for="prenom">Prénom :</label>
    <input type="text" id="prenom" name="prenom" required>

    <label for="email">Email :</label>
    <input type="email" id="email" name="email" required>

    <label for="motdepasse">Mot de Passe :</label>
    <input type="password" id="motdepasse" name="mot_de_passe" required>

    <div>
        <label for="statut">Statut :</label>
        <select id="statut" name="statut" required>
            <option value="enseignant-chercheur">Enseignant-Chercheur</option>
            <option value="enseignant">Enseignant</option>
            <option value="vacataire">Vacataire</option>
            <option value="pro">Salarié</option>
        </select>
        <label for="number">Nombre d'heures :</label>
        <input type="number" id="nombre_heure" name="Nombre d'heures">
    </div>



    <div>
        <label for="role_enseignant">
            <input type="radio" id="role_enseignant" name="role" value="prof" required>
            Enseignant
        </label>
        <label for="role_gestionnaire">
            <input type="radio" id="role_gestionnaire" name="role" value="gestionnaire">
            Gestionnaire
        </label>
    </div>


    <button type="submit">Créer l'utilisateur</button>
    <button id="retour" onclick="window.location.href='.php';">Retour au menu</button>

</form>

</body>
</html>

<style>

</style>
