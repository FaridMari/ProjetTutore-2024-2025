<?php    // Connexion à la base de données
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;
// Activer l'affichage des erreurs dès le début
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Test simple pour voir si PHP fonctionne
echo "<!-- Test de base PHP : OK -->";

try {
    // Démarrer la session
    if (!isset($_SESSION)) {
        session_start();
    }

    $conn = connexionFactory::makeConnection();

    // Vérifier l'authentification
    if (!isset($_SESSION['id_utilisateur'])) {
        echo "<div style='color:red; padding:20px; font-family:Arial;'>Utilisateur non connecté.</div>";
        exit;
    }

    $id_utilisateur = $_SESSION['id_utilisateur'];

    // PARTIE CRUCIALE : Vérifier le verrou
    // D'abord, vérifions si la requête fonctionne
    $stmtTest = $conn->prepare("SELECT 1");
    $stmtTest->execute();

    // Ensuite, vérifier le verrou spécifique
    $stmtVerrouTemp = $conn->prepare("SELECT modification_en_cours FROM contraintes WHERE id_utilisateur = ?");
    $stmtVerrouTemp->execute([$id_utilisateur]);
    $verrouTemp = $stmtVerrouTemp->fetch(PDO::FETCH_ASSOC);

    // Afficher la valeur pour débogage (sera visible dans le code source HTML)
    echo "<!-- Valeur de modification_en_cours : " .
        (($verrouTemp && isset($verrouTemp['modification_en_cours']))
            ? $verrouTemp['modification_en_cours'] : "non trouvée") . " -->";

    // Vérifier si le blocage doit être activé
    $doitBloquer = false;
    if ($verrouTemp && isset($verrouTemp['modification_en_cours']) && intval($verrouTemp['modification_en_cours']) === 1) {
        $doitBloquer = true;
    }

    // IMPORTANT: Afficher la page de blocage si nécessaire
    if ($doitBloquer) {
        // Ici, on affiche une version très simple d'abord pour s'assurer que ça fonctionne
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Fiche en cours de modification</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    text-align: center;
                    padding: 50px;
                }
                .message {
                    background-color: #fff3cd;
                    color: #856404;
                    padding: 20px;
                    border-radius: 5px;
                    display: inline-block;
                }
                .btn {
                    margin-top: 20px;
                    padding: 10px 20px;
                    background-color: #FFEF65;
                    color: #000;
                    text-decoration: none;
                    border-radius: 5px;
                }
            </style>
        </head>
        <body>
        <div class="message">
            Votre fiche est en train d'être modifiée par le gestionnaire.
            <br><br>
            <a href="index.php?action=accueilEnseignant" class="btn">Retour à l'accueil</a>
        </div>
        </body>
        </html>
        <?php
        // IMPORTANT - Terminer l'exécution du script ici
        exit();
    }

    // Si nous arrivons ici, pas de blocage, donc on continue avec le reste du code

    // Récupération des contraintes
    $stmt = $conn->prepare("SELECT jour, heure_debut, creneau_preference, cours_samedi, commentaire FROM contraintes WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $contraintesResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $contraintesChecked = [];
    $creneauPrefere = null;
    $coursSamedi = null;
    $commentaire = null;

    foreach ($contraintesResult as $row) {
        $jour = $row['jour'];
        $heure = $row['heure_debut'] . '_' . (intval($row['heure_debut']) + 2);
        $contraintesChecked[] = $jour . '_' . $heure;

        if ($creneauPrefere === null && isset($row['creneau_preference'])) $creneauPrefere = $row['creneau_preference'];
        if ($coursSamedi === null && isset($row['cours_samedi'])) $coursSamedi = $row['cours_samedi'];
        if ($commentaire === null && isset($row['commentaire'])) $commentaire = $row['commentaire'];
    }

    $verrouille = false;
    $stmtVerif = $conn->prepare("SELECT statut, date_validation FROM contraintes WHERE id_utilisateur = ? LIMIT 1");
    $stmtVerif->execute([$id_utilisateur]);
    $contrainte = $stmtVerif->fetch(PDO::FETCH_ASSOC);
    if ($contrainte && isset($contrainte['statut']) && $contrainte['statut'] === 'validée') {
        $verrouille = true;
    }

    $stmt2 = $conn->prepare("SELECT nb_contrainte FROM enseignants WHERE id_utilisateur = ?");
    $stmt2->execute([$id_utilisateur]);
    $nb_contrainte = $stmt2->fetch(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    // Afficher les erreurs
    echo "<div style='color:red; padding:20px; font-family:Arial;'>";
    echo "Erreur: " . $e->getMessage();
    echo "</div>";
    exit;
}
?>

    <style>
        /* Conteneur principal de la fiche */
        .fiche-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 2em;
            max-width: 1000px;
            width: 100%;
            color: #000;
            margin: 0 auto; /* Centrage horizontal */
        }

        /* Tableau de la fiche */
        .fiche-container table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        .fiche-container th,
        .fiche-container td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        .fiche-container th {
            background-color: #f2f2f2;
        }

        /* Zones de texte */
        .fiche-container textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
        }

        /* Bouton de soumission */
        .fiche-container .btn-submit {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 1em;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
            background-color: #fff495;
            color: #000;
        }

        .fiche-container .btn-submit:hover {
            background-color: #FFEF65;
        }

        /* Alertes */
        .fiche-container .alert {
            padding: 10px;
            margin-bottom: 1em;
            border-radius: 4px;
            text-align: center;
        }

        .fiche-container .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .fiche-container .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
        .fiche-container h1 {
            text-align: center;
        }


    </style>

<div id="main-content">


<div class="fiche-container">
    <h1>Fiche de Vœux</h1>
    <?php if ($verrouille): ?>
        <div class="alert alert-warning">
            Cette fiche a été validée et ne peut plus être modifiée.
        </div>
        <?php if (!empty($contrainte['date_validation'])): ?>
            <div class="alert alert-info">
                Fiche remplie le <?= date('d/m/Y à H:i', strtotime($contrainte['date_validation'])); ?>.
            </div>
        <?php endif; ?>
    <?php endif; ?>

    <form method="post" action="src/Enseignant/EnregistrerContraintes.php">
        <p>Indiquez les plages horaires durant lesquelles vous ne pouvez pas enseigner :</p>
        <table>
            <thead>
            <tr>
                <th></th>
                <th>Lundi</th>
                <th>Mardi</th>
                <th>Mercredi</th>
                <th>Jeudi</th>
                <th>Vendredi</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $horaires = ["8_10" => "8h-10h", "10_12" => "10h-12h", "14_16" => "14h-16h", "16_18" => "16h-18h"];
            $jours = ["lundi", "mardi", "mercredi", "jeudi", "vendredi"];

            foreach ($horaires as $heure_key => $heure_label) {
                echo "<tr><td>$heure_label</td>";
                foreach ($jours as $jour) {
                    $name = "{$jour}_{$heure_key}";
                    $checked = in_array($name, $contraintesChecked) ? 'checked' : '';
                    $disabled = $verrouille ? 'disabled' : '';
                    echo "<td><input type='checkbox' name='contraintes[]' value='$name' $checked $disabled></td>";
                }
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>

        <p> Je préfère, si possible, éviter le créneau : </p>
        <label><input type="radio" name="creneau_prefere" value="8h-10h" <?= $creneauPrefere === "8h-10h" ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> 8h-10h</label><br>
        <label><input type="radio" name="creneau_prefere" value="16h-18h" <?= $creneauPrefere === "16h-18h" ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> 16h-18h</label><br>

        <p> J'accepte d'avoir cours le samedi matin :</p>
        <label><input type="radio" name="cours_samedi" value="oui" <?= $coursSamedi === "oui" ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Oui</label><br>
        <label><input type="radio" name="cours_samedi" value="non" <?= $coursSamedi === "non" ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Non</label>

        <div style="margin-top: 1em">
            <label for="commentaire">Commentaire :</label><br>
            <textarea name="commentaire" id="commentaire" rows="4" <?= $verrouille ? 'disabled' : '' ?>><?= htmlspecialchars($commentaire ?? '') ?></textarea>
        </div>

        <?php if (!$verrouille): ?>
            <button type="submit" class="btn-submit">Valider</button>
        <?php endif; ?>
    </form>
</div>
</div>

<script>
    const nbContraintes = <?= isset($nb_contrainte['nb_contrainte']) ? $nb_contrainte['nb_contrainte'] : 5 ?>;
    function limiterContraintes() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        let checkedCount = 0;

        checkboxes.forEach(cb => { if (cb.checked) checkedCount++; });

        if (checkedCount > nbContraintes) {
            alert("Vous ne pouvez sélectionner que " + nbContraintes + " contraintes au maximum.");
            event.target.checked = false;
        }
    }

    document.addEventListener("DOMContentLoaded", () => {
        document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
            cb.addEventListener("change", limiterContraintes);
        });
    });
</script>