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
        padding: 10px;
        margin-top: 15px;
        border-radius: 5px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }

    .enseignant-surcharge {
        background-color: #ff6d68;
        color: #000;
    }

    .enseignant-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
    }

    .toggle-details {
        background-color: #000;
        color: #fff;
        border: none;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        font-size: 14px;
        text-align: center;
        cursor: pointer;
    }

    .enseignant-details {
        background-color: #fff;
        padding: 10px;
        border-top: 1px solid #ddd;
        margin-top: 10px;
        border-radius: 5px;
    }

    .matiere {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        padding: 5px 0;
        border-bottom: 1px solid #ddd;
        margin-bottom: 0.5em;
    }

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


<div id="main-content">
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
            <div class="enseignant" style="background-color: #fff495">
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