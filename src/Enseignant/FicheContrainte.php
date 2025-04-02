<?php
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;
$conn = connexionFactory::makeConnection();

if (!isset($_SESSION)) session_start();

if (!isset($_SESSION['id_utilisateur'])) {
    die("Utilisateur non connecté.");
}

$id_utilisateur = $_SESSION['id_utilisateur'];

// Récupérer toutes les contraintes de l'utilisateur
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

    if ($creneauPrefere === null) $creneauPrefere = $row['creneau_preference'];
    if ($coursSamedi === null) $coursSamedi = $row['cours_samedi'];
    if ($commentaire === null) $commentaire = $row['commentaire'];
}

// Vérifier si la fiche est validée
$verrouille = false;
$stmtVerif = $conn->prepare("SELECT statut, date_validation FROM contraintes WHERE id_utilisateur = ? LIMIT 1");
$stmtVerif->execute([$id_utilisateur]);
$contrainte = $stmtVerif->fetch(PDO::FETCH_ASSOC);
if ($contrainte && $contrainte['statut'] === 'validée') {
    $verrouille = true;
}

// Nombre de contraintes autorisé
$stmt2 = $conn->prepare("SELECT nb_contrainte FROM enseignants WHERE id_utilisateur = ?");
$stmt2->execute([$id_utilisateur]);
$nb_contrainte = $stmt2->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche de Vœux</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 2em;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #000;
            margin-bottom: 1em;
        }

        .fiche-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 2em;
            max-width: 1000px;
            width: 100%;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
        }

        .btn-submit, .btn-download {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 1em;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-submit {
            background-color: #fff495;
            color: #000;
        }

        .btn-submit:hover {
            background-color: #FFEF65;
        }

        .btn-download {
            background-color: #FFEF65;
            color: #000;
            width: 25%;
            margin: 0 auto;
        }

        .alert {
            padding: 10px;
            margin-bottom: 1em;
            border-radius: 4px;
            text-align: center;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>

<h1>Fiche de Vœux</h1>

<div class="fiche-container">
    <?php if ($verrouille): ?>
        <div class="alert alert-warning">
            Cette fiche a été validée et ne peut plus être modifiée.
        </div>
        <?php if (!empty($contrainte['date_validation'])): ?>
            <div class="alert alert-info">
                Fiche remplie le <?php echo date('d/m/Y à H:i', strtotime($contrainte['date_validation'])); ?>.
            </div>
        <?php endif; ?>
        <form method="post" action="src/Enseignant/telechargerPdf.php" target="_blank">
            <input type="hidden" name="fiche" value="fiche_voeux">
            <button type="submit" class="btn-download">Télécharger la fiche en PDF</button>
        </form>
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
        <label><input type="radio" name="creneau_prefere" value="8h-10h" <?php echo ($creneauPrefere === "8h-10h") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>> 8h-10h</label><br>
        <label><input type="radio" name="creneau_prefere" value="16h-18h" <?php echo ($creneauPrefere === "16h-18h") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>> 16h-18h</label><br>

        <br><p> J’accepte d’avoir cours le samedi matin :</p>
        <label><input type="radio" name="cours_samedi" value="oui" <?php echo ($coursSamedi === "oui") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>> Oui</label><br>
        <label><input type="radio" name="cours_samedi" value="non" <?php echo ($coursSamedi === "non") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>> Non</label>

        <div style="margin-top: 1em">
            <label for="commentaire">Commentaire :</label><br>
            <textarea name="commentaire" id="commentaire" rows="4" <?php echo $verrouille ? 'disabled' : ''; ?>><?php echo htmlspecialchars($commentaire ?? ''); ?></textarea>
        </div>

        <?php if (!$verrouille): ?>
            <button type="submit" class="btn-submit">Valider</button>
        <?php endif; ?>
    </form>
</div>

</body>
</html>
    <script>
        const nbContraintes = <?php echo $nb_contrainte['nb_contrainte']; ?>;
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



