<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier un utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            margin: 0;
            height: 100vh;
            padding: 2em;
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
            max-width: 400px;
            width: 100%;
            color: #000;
            margin-bottom: 1%;
        }

        label {
            display: block;
            color: #444;
            margin-top: 1em;
            font-weight: 500;
        }

        input[type="text"],
        input[type="email"],
        input[type="number"],
        input[type="password"],
        input[type="tel"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            color: #000;
        }

        button {
            display: block;
            width: 100%;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s, color 0.3s;
        }

        button[type="submit"] {
            background-color: #FFEF65;
            color: #000;
            font-weight: 600;
            margin-top: 1em;
        }

        button[type="submit"]:hover {
            background-color: #FFE74A;
            color: #000;
        }

        #retour {
            margin-top: 1em;
            background-color: #000;
            color: #fff;
            border: none;
        }

        #retour:hover {
            background-color: #222;
            color: #fff;
        }

        .toast {
            visibility: hidden;
            max-width: 300px;
            color: #fff;
            text-align: center;
            border-radius: 5px;
            padding: 16px;
            position: fixed;
            right: 20px;
            bottom: 20px;
            opacity: 0;
            transition: opacity 0.5s, visibility 0.5s;
        }

        .toast.show {
            visibility: visible;
            opacity: 0.7;
        }

        .toast.success {
            background-color: rgba(0, 128, 0, 0.7); /* Vert transparent */
        }

        .toast.error {
            background-color: rgba(255, 0, 0, 0.7); /* Rouge transparent */
        }
    </style>
</head>
<body>
<h1>Modifier un utilisateur</h1>

<!-- Liste déroulante des utilisateurs -->
<form method="get" action="index.php">
    <input type="hidden" name="action" value="edit-user">
    <label for="user-select">Sélectionnez un utilisateur :</label>
    <select id="user-select" name="id" onchange="this.form.submit()">
        <option value="">-- Choisissez un utilisateur --</option>
        <?php foreach ($users as $user): ?>
            <?php if ($user['supprimer'] == 0): ?>
                <option value="<?php echo $user['id_utilisateur']; ?>"
                    <?php echo (isset($selectedUser) && $selectedUser['id_utilisateur'] === $user['id_utilisateur']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($user['nom'] . ' ' . $user['prenom']); ?>
                </option>
            <?php endif; ?>
        <?php endforeach; ?>
    </select>
</form>

<?php if (isset($selectedUser)): ?>
    <!-- Formulaire de modification -->
    <form method="post" action="index.php?action=edit-user">
        <input type="hidden" name="id" value="<?php echo htmlspecialchars($selectedUser['id_utilisateur']); ?>">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" value="<?php echo htmlspecialchars($selectedUser['nom'] ?? ''); ?>" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo htmlspecialchars($selectedUser['prenom'] ?? ''); ?>" required>

        <label for="email">Email :</label>
        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($selectedUser['email'] ?? ''); ?>" required>

        <label for="telephone">Numéro de téléphone :</label>
        <input type="tel" id="telephone" name="telephone" value="<?php echo htmlspecialchars($selectedUser['telephone'] ?? ''); ?>">

        <label for="role">Rôle :</label>
        <select id="role" name="role" required>
            <option value="gestionnaire" <?php echo ($selectedUser['role'] ?? '') === 'gestionnaire' ? 'selected' : ''; ?>>Gestionnaire</option>
            <option value="enseignant" <?php echo ($selectedUser['role'] ?? '') === 'enseignant' ? 'selected' : ''; ?>>Enseignant</option>
        </select>

        <label for="statut">Statut :</label>
        <select id="statut" name="statut" required>
            <option value="enseignant-chercheur" <?php echo ($selectedUser['statut'] ?? '') === 'enseignant-chercheur' ? 'selected' : ''; ?>>Enseignant-Chercheur</option>
            <option value="ater" <?php echo ($selectedUser['statut'] ?? '') === 'ater' ? 'selected' : ''; ?>>ATER</option>
            <option value="vacataire_enseignant" <?php echo ($selectedUser['statut'] ?? '') === 'vacataire_enseignant' ? 'selected' : ''; ?>>Vacataire enseignant</option>
            <option value="vacataire_professionnel" <?php echo ($selectedUser['statut'] ?? '') === 'vacataire_professionnel' ? 'selected' : ''; ?>>Vacataire professionnel</option>
            <option value="prce_prag" <?php echo ($selectedUser['statut'] ?? '') === 'prce_prag' ? 'selected' : ''; ?>>PRCE/PRAG</option>
            <option value="doctorant_missionnaire" <?php echo ($selectedUser['statut'] ?? '') === 'doctorant_missionnaire' ? 'selected' : ''; ?>>Doctorant missionnaire</option>
            <option value="doctorant_vacataire" <?php echo ($selectedUser['statut'] ?? '') === 'doctorant_vacataire' ? 'selected' : ''; ?>>Doctorant vacataire</option>
            <option value="enseignant_associe" <?php echo ($selectedUser['statut'] ?? '') === 'enseignant_associe' ? 'selected' : ''; ?>>Enseignant associé</option>
        </select>

        <label for="nombre_heures">Nombre d'heures :</label>
        <input type="text" id="nombre_heures" name="nombre_heures" value="<?php echo htmlspecialchars($selectedUser['nombre_heures'] ?? ''); ?>" readonly>

        <label for="nb_contrainte">Nombre limite de contrainte :</label>
        <input type="text" id="nb_contrainte" name="nb_contrainte" value="<?php echo htmlspecialchars($selectedUser['nb_contrainte'] ?? ''); ?>">

        <div id="responsableDiv">
            <label for="responsable_module">
                <input type="checkbox" id="responsable_module" name="responsable" value="responsable" <?php echo ($selectedUser['responsable'] ?? '') === 'oui' ? 'checked' : ''; ?>>
                Responsable de module
            </label>
        </div>

        <button type="submit">Modifier l'utilisateur</button>
        <button id="retour" onclick="window.location.href='index.php?action=gestionCompteUtilisateur'; return false;">Retour au menu</button>
    </form>
<?php endif; ?>

<script>
    class Toast {
        constructor() {
            this.toastElement = document.createElement('div');
            this.toastElement.className = 'toast';
            document.body.appendChild(this.toastElement);
        }

        show(message, type = 'success') {
            this.toastElement.textContent = message;
            this.toastElement.className = `toast show ${type}`;
            setTimeout(() => {
                this.toastElement.className = this.toastElement.className.replace('show', '');
            }, 3000); // Le toast disparaît après 3 secondes
        }
    }

    const toast = new Toast();

    document.addEventListener('DOMContentLoaded', function() {
        const form = document.querySelector('form[method="post"]');
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData
            })
                .then(response => response.text())
                .then(result => {
                    if (result.includes('success')) {
                        toast.show('Utilisateur modifié avec succès!');
                        setTimeout(() => {
                            window.location.href = 'index.php?action=gestionCompteUtilisateur';
                        }, 3000);
                    } else {
                        toast.show('Erreur lors de la modification de l\'utilisateur.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    toast.show('Une erreur s\'est produite.', 'error');
                });
        });
    });
</script>
</body>
</html>
