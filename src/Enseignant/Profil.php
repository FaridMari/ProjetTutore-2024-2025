<style>
    .profile-container {
        background: #FFEF65;
        padding: 3em;
        border-radius: 15px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.2);
        text-align: center;
        width: 50%;
        margin: auto;
    }

    .profile-header {
        font-size: 2rem;
        font-weight: bold;
        color: #000;
        margin-bottom: 1.5em;
    }

    .profile-info {
        background: #f9f9f9;
        padding: 2em;
        border-radius: 10px;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        text-align: left;
        font-size: 1.3rem;
    }

    .profile-info p {
        margin: 1em 0;
    }

    .profile-info span {
        font-weight: bold;
        color: #000;
    }

    .profile-button {
        margin-top: 2em;
    }

    .change-password-btn {
        background: #000;
        color: #fff;
        border: none;
        padding: 1em 2em;
        font-size: 1.2rem;
        font-weight: bold;
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .change-password-btn:hover {
        background: #333;
        transform: scale(1.05);
    }

    </style>

<div id="main-content">
    <div class="profile-container">
        <div class="profile-header">Profil Enseignant</div>

        <div class="profile-info">
            <p><span>Nom :</span> Dupont</p>
            <p><span>Prénom :</span> Jean</p>
            <p><span>Adresse mail :</span> jean.dupont@example.com</p>
            <p><span>Statut :</span> Enseignant-Chercheur</p>
        </div>

        <div class="profile-button">
            <button class="change-password-btn">Changer le mot de passe</button>
        </div>
    </div>
</div>

