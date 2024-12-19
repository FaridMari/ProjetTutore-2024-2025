<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire des comptes utilisateurs</title>
    <link rel="stylesheet" href="styles.css">
    <style>


        .briques-container {
            display: flex;
            width: 100%;
            height: 80vh;
            margin: 2em auto;
        }

        .brique {
            background-color: #5dade2;
            border-radius: 10px;
            padding: 2em;
            width: 40%;
            height: 50%;
            text-align: center;
            align-items: center;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.3);
            transition: transform 0.3s, background-color 0.3s;
            cursor: pointer;
            margin: 5em;
        }


        .brique:hover {
            transform: scale(1.05);
            background-color: #2e86c1;
        }

        .brique a {
            text-decoration: none;
            color: white;
            font-size: 16px;
            font-weight: bold;
            width: 100%;
            height: 100%;
            display: flex;
            justify-content: center;
        }


    </style>
</head>
<body>


<div class="briques-container">
    <div class="brique">
        <a href="index.php?action=gestionnaireCreerUtilisateurAction">Créer un utilisateur</a>
    </div>
    <div class="brique">
        <a href="index.php?action=edit-user">Modifier un utilisateur</a>
    </div>
    <div class="brique">
        <a href="index.php?action=delete-user">Supprimer un utilisateur</a>
    </div>
</div>


</body>
</html>
