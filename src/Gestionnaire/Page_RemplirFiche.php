<?php
include 'Navbar_Generique.html';
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

$conn = connexionFactory::makeConnection();

$id_utilisateur = $_GET['id'] ?? null;
$type = $_GET['type'] ?? '';

if (!$id_utilisateur || $type !== 'Fiche Contrainte') {
    echo "<div class='alert alert-danger'>Paramètres manquants ou type invalide.</div>";
    exit;
}

// Infos enseignant
$stmtEnseignant = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = ?");
$stmtEnseignant->execute([$id_utilisateur]);
$enseignant = $stmtEnseignant->fetch(PDO::FETCH_ASSOC);

if (!$enseignant) {
    echo "<div class='alert alert-danger'>Enseignant introuvable pour l'ID $id_utilisateur.</div>";
    exit;
}

// Contrainte max autorisée
$stmt2 = $conn->prepare("SELECT nb_contrainte FROM enseignants WHERE id_utilisateur = ?");
$stmt2->execute([$id_utilisateur]);
$nb_contrainte = $stmt2->fetch(PDO::FETCH_ASSOC)['nb_contrainte'] ?? 5;
?>

<div class="fiche-container">
    <div class="enseignant-info">
        Remplir la fiche contrainte de <?= htmlspecialchars($enseignant['prenom'] . ' ' . $enseignant['nom']) ?>
    </div>

    <form method="post" action="../../src/Enseignant/EnregistrerContraintes.php">
        <input type="hidden" name="modification_gestionnaire" value="1">
        <input type="hidden" name="id_utilisateur" value="<?= $id_utilisateur ?>">

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
                    echo "<td><input type='checkbox' name='contraintes[]' value='$name'></td>";
                }
                echo "</tr>";
            }
            ?>
            </tbody>
        </table>

        <p>Je préfère, si possible, éviter le créneau :</p>
        <label><input type="radio" name="creneau_prefere" value="8h-10h"> 8h-10h</label><br>
        <label><input type="radio" name="creneau_prefere" value="16h-18h"> 16h-18h</label><br>

        <p>J’accepte d’avoir cours le samedi matin :</p>
        <label><input type="radio" name="cours_samedi" value="oui"> Oui</label><br>
        <label><input type="radio" name="cours_samedi" value="non"> Non</label>

        <div style="margin-top: 1em">
            <label for="commentaire">Commentaire :</label><br>
            <textarea name="commentaire" id="commentaire" rows="4"></textarea>
        </div>

        <div style="display: flex; gap: 10px; justify-content: center; margin-top: 1em;">
            <a href="../../index.php?action=ficheEnseignant" class="btn-submit" style="text-align:center; text-decoration: none;">Retour</a>
            <button type="submit" class="btn-submit">Enregistrer</button>
        </div>
    </form>
</div>

<script>
    const nbContraintes = <?= $nb_contrainte ?>;
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

    .enseignant-info {
        text-align: center;
        font-weight: bold;
        font-size: 1.6em;
        margin-bottom: 1.5em;
        color: #2c3e50;
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
