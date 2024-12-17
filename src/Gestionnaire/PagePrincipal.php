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

        .enseignants {
            width: 102%;
            background-color: #ecf0f1;
            border-radius: 8px;
            padding: 10px;
            margin-top: 20px;
        }

        .enseignant {
            background-color: #dff0d8;
            padding: 10px;
            margin-top: 15px;
            border-radius: 5px;
        }

        .enseignant-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .toggle-details {
            background-color: black;
            border-radius: 100%;
            font-size: 16px;
            cursor: pointer;
        }

        .enseignant-details {
            background-color: #f9f9f9;
            padding: 10px;
            border-top: 1px solid #ddd;
            margin-top: 10px;
            border-radius: 5px;
        }

        .matiere {
            display: grid;
            grid-template-columns: repeat(6, 1fr);
            padding: 5px;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>


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
    <div class="enseignants">
        <div class="enseignant" >
            <div class="enseignant-header" >
                <span>Nom Prénom Enseignant</span>
                <span>Statut enseignant</span>
                <span>Nombre d'heures affectées</span>
                <button class="toggle-details">▼</button>
            </div>
            <div class="enseignant-details" style="display: none;">
                <div class="matiere">
                    <span>Nom module</span>
                    <span>Groupe associé</span>
                    <span>Nombre d'heures</span>
                    <span>Responsable module</span>
                    <span>Type de cours</span>
                    <span>Type salle</span>
                </div>
            </div>
        </div>
        <div class="enseignant">
            <div class="enseignant-header">
                <span>Nom Prénom Enseignant</span>
                <span>Statut enseignant</span>
                <span>Nombre d'heures affectées</span>
                <button class="toggle-details">▼</button>
            </div>
            <div class="enseignant-details" style="display: none;">
                <div class="matiere">
                    <span>Nom module</span>
                    <span>Groupe associé</span>
                    <span>Nombre d'heures</span>
                    <span>Responsable module</span>
                    <span>Type de cours</span>
                    <span>Type salle</span>
                </div>
                <div class="matiere">
                    <span>Nom module</span>
                    <span>Groupe associé</span>
                    <span>Nombre d'heures</span>
                    <span>Responsable module</span>
                    <span>Type de cours</span>
                    <span>Type salle</span>
                </div>
                <div class="matiere">
                    <span>Nom module</span>
                    <span>Groupe associé</span>
                    <span>Nombre d'heures</span>
                    <span>Responsable module</span>
                    <span>Type de cours</span>
                    <span>Type salle</span>
                </div>
            </div>
        </div>
        <div class="enseignant" style="background-color: #ff6d68">
            <div class="enseignant-header">
                <span>Nom Prénom Enseignant</span>
                <span>Statut enseignant</span>
                <span>Nombre d'heures affectées</span>
                <button class="toggle-details">▼</button>
            </div>
            <div class="enseignant-details" style="display: none;">
            </div>
        </div>
        <div class="enseignant">
            <div class="enseignant-header">
                <span>Nom Prénom Enseignant</span>
                <span>Statut enseignant</span>
                <span>Nombre d'heures affectées</span>
                <button class="toggle-details">▼</button>
            </div>
            <div class="enseignant-details" style="display: none;">
                <div class="matiere">
                    <span>Nom module</span>
                    <span>Groupe associé</span>
                    <span>Nombre d'heures</span>
                    <span>Responsable module</span>
                    <span>Type de cours</span>
                    <span>Type salle</span>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    document.querySelectorAll('.toggle-details').forEach(button => {
        button.addEventListener('click', function () {
            const details = this.closest('.enseignant').querySelector('.enseignant-details');
            const isVisible = details.style.display === 'block';
            details.style.display = isVisible ? 'none' : 'block';
            this.textContent = isVisible ? '▼' : '▲';
        });
    });
</script>

</body>
</html>
