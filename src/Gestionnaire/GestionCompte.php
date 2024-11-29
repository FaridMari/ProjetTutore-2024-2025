<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestionnaire des comptes utilisateurs</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            margin-left: 200px;
            padding: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #34495e;
            display: flex;
            flex-direction : column;
            align-items: center;
            justify-content: center;
        }

        #menu {
            position: fixed;
            top: 0;
            left: 0;
            width: 200px;
            height: 100%;
            background-color: lightsteelblue;
            color: #fff;
        }

        h1 {
            text-align: center;
            color: white;
        }

        h4{
            margin-top: -10%;
            margin-left: 25%;
        }

        ul {
            list-style: none;
            padding: 0;
            align-items: center;
            margin-top: 4em;
        }

        .element_menu {
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: background-color 0.3s;
        }

        .element_menu a {
            color: white;
            text-decoration: none;
        }

        ul li:hover {
            background-color: #34495e;
        }



        #deconnexion {
            position: absolute;
            align-content: center;
            bottom: 20px;
            right: 35px;
            background-color: #5dade2;
            padding: 10px;
            border-radius: 20px;
        }

        button {
            background-color: #5dade2;
            color: white;
            border: none;
            padding: 10px;
            border-radius: 5px;
            cursor: pointer;
        }
        #deconnexion a {
            text-decoration: none;
            color: #fff;
        }

        #deconnexion:hover{
            background-color: #2e86c1;
            padding: 10px;
            border-radius: 20px;
        }

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
<div id="menu">
    <ul>
        <?php if (false): ?>
            <li class="element_menu"><i class="bi bi-box-arrow-in-right"></i> <a href="index.php?action=signin">Log-in</a></li>
        <?php else: ?>
            <li class="element_menu"> <a href="index.php?action=gestionnairePagePrincipal">Visualisation Gestionnaire</a></li>
            <li class="element_menu"><a href="index.php?action=gestionCompteUtilisateur">Gestion Compte Utilisateur</a></li>
            <li class="element_menu"><a href="index.php?action=">Gestion Ressource</a></li>
            <li class="element_menu"><a href="index.php?action=">Gestion Fiche Utilisateurs</a></li>
        <?php endif; ?>
    </ul>
    <div id="deconnexion">
        <li class="element_menu">Deconnexion</li>
    </div>
</div>

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
