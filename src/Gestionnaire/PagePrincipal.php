<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Interface Gestionnaire</title>
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

        .content {
            padding-right: 2em;
            width: 95%;
        }
        .filtres {
            width: 100%;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 1.5em;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-evenly;
            align-items: center;
        }

        .filtres input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
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
    </style>
</head>
<body>
<div id="menu">
    <ul>
        <?php if (false): ?>
            <li class="element_menu"><i class="bi bi-box-arrow-in-right"></i> <a href="index.php?action=signin">Log-in</a></li>
        <?php else: ?>
            <li class="element_menu"> <a href="index.php?action=">Visualisation Gestionnaire</a></li>
            <li class="element_menu"><a href="PagePrincipal.php?action=gestionCompteUtilisateur">Gestion Compte Utilisateur</a></li>
            <li class="element_menu"><a href="index.php?action=">Gestion Ressource</a></li>
            <li class="element_menu"><a href="index.php?action=">Gestion Fiche Utilisateurs</a></li>
        <?php endif; ?>
    </ul>
    <div id="deconnexion">
        <li class="element_menu">Deconnexion</li>
    </div>
</div>

<div class="content">
    <div class="filtres">
        <div>
            <button>Supprimer les filtres</button>
        </div>
        <div>
            <label for="recherche">Recherche :</label>
            <input type="text" id="recherche" name="recherche" placeholder="Rechercher...">
        </div>
        <div>
            <label for="statut">Statut :</label>
            <select id="statut" name="statut" required>
                <option value="enseignant-chercheur">Enseignant-Chercheur</option>
                <option value="enseignant">Enseignant</option>
                <option value="vacataire">Vacataire</option>
                <option value="pro">Salarié</option>
            </select>
        </div>
        <div>
            <label for="fiche_remplie">Fiche remplie : </label>
            <select id="fiche_remplie" name="fiche_remplie" required>
                <option value="oui">Oui</option>
                <option value="non">Non</option>
                <option value="les_deux">Les deux</option>
            </select>
        </div>


    </div>

</div>

<div id="corps">
    <?php
    require_once 'vendor/autoload.php';
    use src\Db\connexionFactory;
    use src\Dispatcher\dispatcher;

    //connexionFactory::setConfig('src\Db\db.config.ini');

    session_start();

    if (isset($_GET['id']) && is_numeric($_GET['id'])) {
        $_SESSION['user_id'] = intval($_GET['id_utilisateur']);
    }

    $action = isset($_GET['action']) ? $_GET['action'] : 'default';


    $dispatcher = new Dispatcher($action);
    $dispatcher->run();
    ?>
</div>
</body>
</html>
