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
            justify-content: center;
            align-items: center;
            background-color: #f9f9f9;
            margin: 2em;
            padding: 0;
            height: 100vh;
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
            max-width: 30%;
            width: 100%;
            color: #000;
        }

        label {
            display: block;
            color: #444;
            font-weight: 500;
            margin-bottom: 0.4em;
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

        button[type="submit"] {
            display: block;
            width: 100%;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;

            background-color: #fff495;
            color: #000;
            transition: background-color 0.3s, color 0.3s;
        }
        button[type="submit"]:hover {
            background-color: #FFEF65;
            color: #222;
        }

        #retour {
            margin-top: 1em;
            display: block;
            width: 100%;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;

            background-color: #000;
            color: #fff;
            border: none;
            transition: background-color 0.3s;
        }
        #retour:hover {
            background-color: #303030;
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

    <div>
        <label for="statut">Statut :</label>
        <select id="statut" name="statut" required onchange="updateHeures()">
            <option value="enseignant-chercheur">Enseignant-chercheur</option>
            <option value="ater">ATER</option>
            <option value="vacataire_enseignant">Vacataire enseignant</option>
            <option value="vacataire_professionnel">Vacataire professionnel</option>
            <option value="prce_prag">PRCE/PRAG</option>
            <option value="doctorant_missionnaire">Doctorant missionnaire</option>
            <option value="doctorant_vacataire">Doctorant vacataire</option>
            <option value="enseignant_associé">Enseignant associé</option>
        </select>

        <label for="nombre_heures">Nombre d'heures :</label>
        <input type="text" id="nombre_heures" name="nombre_heures" readonly>

        <label for="nombre_contrainte">Nombre limite de contrainte :</label>
        <input type="text" id="nombre_contrainte" name="nombre_contrainte" >

    </div>

    <script>
        function updateHeures() {
            let statut = document.getElementById("statut").value;
            let nombreHeuresInput = document.getElementById("nombre_heures");

            let heuresParDefaut = {
                "enseignant-chercheur": 192,
                "ater": 192,
                "prce_prag": 384
            };

            nombreHeuresInput.value = heuresParDefaut[statut] || 0;
        }

        // Initialiser au chargement
        document.addEventListener("DOMContentLoaded", updateHeures);
    </script>




    <div>
        <label for="role_enseignant">
            <input type="radio" id="role_enseignant" name="role" value="enseignant" required>
            Enseignant
        </label>
        <label for="role_gestionnaire">
            <input type="radio" id="role_gestionnaire" name="role" value="gestionnaire">
            Gestionnaire
        </label>
    </div>


    <button type="submit">Créer l'utilisateur</button>
    <button id="retour" onclick="window.location.href='index.php?action=gestionCompteUtilisateur'; return false;">Retour au menu</button>


</form>
</body>
</html>