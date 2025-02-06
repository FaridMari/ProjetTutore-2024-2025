<style>
    .accueil-enseignant {
        text-align: center;
        padding: 2em; /* un peu d'espace autour */
    }

    .accueil-enseignant h1 {
        font-size: 2rem;
        color: #000;
        margin-bottom: 0.5em;
    }
    .accueil-enseignant h3 {
        font-size: 1.2rem;
        color: #444;
        margin-bottom: 2em;
    }

    /* Conteneur de bulles */
    .bulle-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: center;
        gap: 2em;
    }

    /* Bulles */
    .bulle {
        width: 220px;
        height: 220px;
        background-color: #FFEF65;
        color: #000;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        transition: transform 0.3s, box-shadow 0.3s;
        cursor: pointer;
    }

    .bulle h2 {
        font-size: 1.1rem;
        margin-bottom: 0.5em;
        text-transform: uppercase;
        color: #000;
    }

    .bulle .valeur {
        font-size: 1.5rem;
        font-weight: 600;
    }

</style>

<!-- #main-content est géré par layout.css (margin-left...) -->
<div id="main-content">
    <div class="accueil-enseignant">
        <!-- Formulaire avec action sur index.php?action=accueilEnseignant -->
        <form method="post" action="index.php?action=accueilEnseignant">

            <!-- Titre -->
            <h1>Bonjour <span class="nom">Dupont</span> <span class="prenom">Alice</span></h1>
            <h3>Statut : <span class="statut">Vacataire</span></h3>

            <!-- Conteneur des bulles -->
            <div class="bulle-container">
                <div class="bulle">
                    <h2>Heures Affectées</h2>
                    <p class="valeur">42 h</p>
                </div>
                <div class="bulle">
                    <h2>Total HETD</h2>
                    <p class="valeur">28</p>
                </div>
                <div class="bulle">
                    <h2>Votre Rôle</h2>
                    <p class="valeur">Enseignant</p>
                </div>
            </div>
        </form>
    </div>
</div>