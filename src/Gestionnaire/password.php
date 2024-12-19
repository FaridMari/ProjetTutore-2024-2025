<?php
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

$email = $_GET['email'] ?? null;

if ($email) {
    try {
        $conn = connexionFactory::makeConnection();

        // Vérifier si l'email et la validité du token existent
        $stmt = $conn->prepare("SELECT email FROM utilisateurs 
                                WHERE email = :email 
                                AND reset_token = (SELECT MAX(reset_token) FROM utilisateurs WHERE email = :email)
                                AND reset_token_expiration > NOW()");
        $stmt->bindParam(':email', $email, PDO::PARAM_STR);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            // Afficher le formulaire pour réinitialiser le mot de passe
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nouveau_mdp'], $_POST['confirmer_mdp'])) {
                $nouveauMdp = $_POST['nouveau_mdp'];
                $confirmerMdp = $_POST['confirmer_mdp'];

                // Vérification : mots de passe identiques
                if ($nouveauMdp !== $confirmerMdp) {
                    echo "<script>
                            alert('Les mots de passe ne correspondent pas.');
                            window.location.href = 'password.php?email=" . urlencode($email) . "';
                          </script>";
                    exit();
                }

                // Vérification : taille minimale du mot de passe
                if (strlen($nouveauMdp) < 8) {
                    echo "<script>
                            alert('Le mot de passe doit contenir au moins 8 caractères.');
                            window.location.href = 'password.php?email=" . urlencode($email) . "';
                          </script>";
                    exit();
                }

                // Hasher le mot de passe
                $hashedPassword = password_hash($nouveauMdp, PASSWORD_BCRYPT);

                // Mettre à jour le mot de passe et supprimer les jetons
                $stmt = $conn->prepare("UPDATE utilisateurs 
                                        SET mot_de_passe = :mdp, reset_token = NULL, reset_token_expiration = NULL 
                                        WHERE email = :email");
                $stmt->bindParam(':mdp', $hashedPassword, PDO::PARAM_STR);
                $stmt->bindParam(':email', $email, PDO::PARAM_STR);
                $stmt->execute();

                echo "<script>alert('Mot de passe enregistré avec succès.'); window.location.href = '../../index.php?action=gestionCompteUtilisateur';</script>";
                exit();
            }
            ?>
            <!DOCTYPE html>
            <html lang="fr">
            <head>
                <meta charset="UTF-8">
                <title>Créer votre mot de passe</title>
                <style>
                    body {
                        font-family: Arial, sans-serif;
                        display: flex;
                        justify-content: center;
                        align-items: center;
                        height: 100vh;
                        background-color: #f9f9f9;
                    }

                    form {
                        background: #fff;
                        padding: 2em;
                        border-radius: 8px;
                        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
                        text-align: center;
                    }

                    label {
                        display: block;
                        margin-bottom: 0.5em;
                        color: #555;
                    }

                    input[type="password"] {
                        width: 100%;
                        padding: 0.8em;
                        margin-bottom: 1em;
                        border: 1px solid #ddd;
                        border-radius: 4px;
                        font-size: 1em;
                    }

                    button {
                        padding: 0.8em 1.5em;
                        background-color: #007BFF;
                        color: #fff;
                        border: none;
                        border-radius: 4px;
                        font-size: 1em;
                        cursor: pointer;
                        transition: background-color 0.3s;
                    }

                    button:hover {
                        background-color: #0056b3;
                    }
                </style>
            </head>
            <body>
            <form method="POST">
                <h2>Créer votre mot de passe</h2>
                <label for="nouveau_mdp">Choisissez un mot de passe :</label>
                <input type="password" id="nouveau_mdp" name="nouveau_mdp" required>
                <label for="confirmer_mdp">Confirmez votre mot de passe :</label>
                <input type="password" id="confirmer_mdp" name="confirmer_mdp" required>
                <button type="submit">Enregistrer le mot de passe</button>
            </form>
            </body>
            </html>
            <?php
        } else {
            echo "<script>alert('Lien expiré ou invalide.'); window.location.href = '../../index.php?action=gestionCompteUtilisateur';</script>";
        }
    } catch (Exception $e) {
        echo "<script>alert('Erreur : " . addslashes($e->getMessage()) . "');</script>";
    }
} else {
    echo "<script>alert('Aucun email fourni.'); window.location.href = '../../index.php?action=gestionCompteUtilisateur';</script>";
}
?>
