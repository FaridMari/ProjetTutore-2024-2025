<style>
    h1 {
        text-align: center;
        color: #000;
    }

    .content {
        width: 80%;
        margin: 0 auto;
        padding: 2em;
    }

    .filtres {
        width: 100%;
        background-color: #fff; /* Blanc */
        border-radius: 8px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        padding: 1.5em;
        margin-bottom: 20px;

        display: flex;
        align-items: center;
        justify-content: space-evenly;
    }

    .filtres input[type="text"],
    .filtres select {
        width: 100%;
        padding: 10px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 5px;
        font-size: 14px;
    }

    .filtres button {
        background-color: #FFEF65;
        color: #000;
        border: none;
        padding: 10px;
        border-radius: 5px;
        font-weight: 500;
        cursor: pointer;
    }

    .filtres button:hover {
        background-color: #FFE74A;
    }

    .enseignants {
        width: 100%;
        background-color: #fff;
        border-radius: 8px;
        padding: 10px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    .enseignant {
        background-color: white;
        color: #000;
        padding: 15px;
        margin-top: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    /* Pour les enseignants en surcharge */
    .enseignant-surcharge {
        background-color: #ff6d68;
        color: #000;
    }

    /* Alignement en colonne */
    .enseignant-header {
        display: grid;
        grid-template-columns: repeat(4, 1fr) auto; /* 4 colonnes + 1 pour le toggle */
        align-items: center;
        gap: 10px;
        cursor: pointer;
    }

    /* Bouton Toggle centré */
    .toggle-details {
        display: flex;
        align-items: center;
        justify-content: center;
        background-color: #000;
        color: #fff;
        border: none;
        width: 30px;
        height: 30px;
        border-radius: 50%;
        font-size: 16px;
        font-weight: bold;
        cursor: pointer;
    }

    /* Contenu caché au début */
    .enseignant-details {
        background-color: #fff;
        padding: 10px;
        border-top: 1px solid #ddd;
        margin-top: 10px;
        border-radius: 5px;
        display: none;
    }

    /* Alignement des matières */
    .matiere {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 10px;
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
    }

    /* Boutons généraux */
    button {
        background-color: #FFEF65;
        color: #000;
        border: none;
        padding: 8px 14px;
        border-radius: 5px;
        cursor: pointer;
    }

    button:hover {
        background-color: #FFE74A;
    }

</style>
<?php
    use src\Db\connexionFactory;
    $bdd = connexionFactory::makeConnection();

    // Requête SQL pour récupérer les données des enseignants et leurs affectations
    $sql = "
        SELECT 
            u.nom, 
            u.prenom, 
            e.statut, 
            e.heures_affectees, 
            a.heures_affectees AS heures_affectees_cours, 
            a.type_heure,
            c.nom_cours, 
            g.nom_groupe, 
            d.type_salle, 
            d.equipements_specifiques, 
            d.details
    
        FROM 
            utilisateurs u
        JOIN 
            enseignants e ON u.id_utilisateur = e.id_utilisateur
        LEFT JOIN 
            affectations a ON e.id_enseignant = a.id_enseignant
        LEFT JOIN 
            cours c ON a.id_cours = c.id_cours
        LEFT JOIN 
            groupes g ON a.id_groupe = g.id_groupe
        LEFT JOIN 
            details_cours d ON c.id_cours = d.id_cours
        WHERE 
            u.supprimer = 0 AND u.nom != 'test' AND u.nom != 'test2'
    ";

    $result = $bdd->query($sql);

    $enseignants = [];

    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $enseignants[] = $row;
        }
    }



// Encoder les données en JSON
    $enseignants_json = json_encode($enseignants);

?>

<div id="main-content">
    <div class="content">
        <div>
            <h1>Liste des enseignants</h1>
        </div>
        <!--<div class="filtres">
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
        </div>-->
        <div class="enseignants" id="enseignants-container">

        </div>
    </div>
</div>

<script>
    var enseignants = <?php echo $enseignants_json; ?>;
    console.log(enseignants);

    document.addEventListener('DOMContentLoaded', function () {
        const enseignantsContainer = document.getElementById('enseignants-container');

        // Regroupement des enseignants par id_nom_prenom
        let enseignantsMap = {};

        enseignants.forEach(enseignant => {
            let id = `${enseignant.nom}-${enseignant.prenom}`;

            if (!enseignantsMap[id]) {
                enseignantsMap[id] = {
                    nom: enseignant.nom,
                    prenom: enseignant.prenom,
                    statut: enseignant.statut,
                    heures_affectees: enseignant.heures_affectees,
                    affectations: []
                };
            }

            // Ajouter les détails du cours uniquement s'il y a une affectation
            if (enseignant.nom_cours) {
                enseignantsMap[id].affectations.push({
                    nom_cours: enseignant.nom_cours,
                    nom_groupe: enseignant.nom_groupe,
                    heures_affectees_cours: enseignant.heures_affectees_cours,
                    type_heure: enseignant.type_heure
                });
            }
        });

        // Générer l'affichage
        Object.values(enseignantsMap).forEach(enseignant => {
            const enseignantDiv = document.createElement('div');
            enseignantDiv.className = 'enseignant';

            const enseignantHeader = document.createElement('div');
            enseignantHeader.className = 'enseignant-header';

            enseignantHeader.innerHTML = `
            <span>${enseignant.nom} ${enseignant.prenom}</span>
            <span>${enseignant.statut}</span>
            <span>${enseignant.heures_affectees} heures affectées</span>
            <button class="toggle-details">▼</button>
        `;

            const enseignantDetails = document.createElement('div');
            enseignantDetails.className = 'enseignant-details';
            enseignantDetails.style.display = 'none';

            if (enseignant.affectations.length > 0) {
                enseignant.affectations.forEach(affectation => {
                    const matiereDiv = document.createElement('div');
                    matiereDiv.className = 'matiere';
                    matiereDiv.innerHTML = `
                    <span>${affectation.nom_cours}</span>
                    <span>${affectation.nom_groupe}</span>
                    <span>${affectation.type_heure}</span>
                `;
                    enseignantDetails.appendChild(matiereDiv);
                });
            } else {
                enseignantDetails.innerHTML = `<p>Aucune affectation.</p>`;
            }

            enseignantDiv.appendChild(enseignantHeader);
            enseignantDiv.appendChild(enseignantDetails);
            enseignantsContainer.appendChild(enseignantDiv);

            enseignantHeader.querySelector('.toggle-details').addEventListener('click', function () {
                const isVisible = enseignantDetails.style.display === 'block';
                enseignantDetails.style.display = isVisible ? 'none' : 'block';
                this.textContent = isVisible ? '▼' : '▲';
            });
        });
    });

</script>