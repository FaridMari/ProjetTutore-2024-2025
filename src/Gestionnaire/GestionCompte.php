<style>
    .briques-container {

        display: flex;
        justify-content: center;
        align-items: center;

        gap: 3em;
        width: 100%;
        height: 80vh;
        margin: 0 auto;
    }

    .brique {
        background-color: #FFEF65;
        color: #000;

        width: 26%;
        min-width: 300px;
        height: 300px;
        border-radius: 12px;

        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
        cursor: pointer;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;

        transition: transform 0.3s, background-color 0.3s, box-shadow 0.3s;
    }

    .brique:hover {
        transform: scale(1.05);
        background-color: #FFE74A;
        box-shadow: 0 6px 12px rgba(0,0,0,0.3);
    }

    .brique a {
        text-decoration: none;
        color: #000;
        font-size: 1.2rem;
        font-weight: 600;

        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .brique a {
        font-size: 23px;
        margin-bottom: 0.5em;
    }

</style>



<div id="main-content">
    <div class="briques-container">
        <div class="brique">
            <a href="index.php?action=gestionnaireCreerUtilisateurAction">
                <span>Créer un utilisateur</span>
            </a>
        </div>
        <div class="brique">
            <a href="index.php?action=edit-user">
                Modifier un utilisateur
            </a>
        </div>
        <div class="brique">
            <a href="index.php?action=delete-user">
                Supprimer un utilisateur
            </a>
        </div>
    </div>
</div>
