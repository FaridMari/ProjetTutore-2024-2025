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

    // Ces valeurs sont redondantes, mais on prend la première fiche trouvée pour les afficher
    if ($creneauPrefere === null) $creneauPrefere = $row['creneau_preference'];
    if ($coursSamedi === null) $coursSamedi = $row['cours_samedi'];
    if ($commentaire === null) $commentaire = $row['commentaire'];
}

// Vérifier si la fiche est validée
$verrouille = false;
$stmtVerif = $conn->prepare("SELECT statut, date_validation FROM contraintes WHERE id_utilisateur = ? LIMIT 1");
$stmtVerif->execute([$id_utilisateur]);
$contrainte = $stmtVerif->fetch(PDO::FETCH_ASSOC);
if ($contrainte && $contrainte['statut'] === 'valide') {
    $verrouille = true;
}

// Toast personnalisé : message temporaire après validation
$toastMessage = $_SESSION['toast_message'] ?? null;
unset($_SESSION['toast_message']);

// Détection du mode Puppeteer
$isPuppeteer = isset($_GET['pdf']) && $_GET['pdf'] === '1';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Vœux 2024-2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        <?php if ($isPuppeteer): ?>
        body { background: #fff; }
        <?php endif; ?>
    </style>
</head>
<body class="<?php echo $isPuppeteer ? '' : 'bg-light'; ?>">

<?php if (!$isPuppeteer && $toastMessage): ?>
    <div class="toast-container position-fixed bottom-0 end-0 p-3">
        <div class="toast align-items-center text-bg-success border-0 show" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo htmlspecialchars($toastMessage); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="container my-5">
    <h1 class="text-center mb-4">Fiche de Vœux</h1>

    <?php if ($verrouille): ?>
        <div class="alert alert-warning text-center">
            Cette fiche a été validée et ne peut plus être modifiée.
        </div>
        <?php if (!empty($contrainte['date_validation'])): ?>
            <div class="alert alert-info text-center">
                Cette fiche a été remplie le <?php echo date('d/m/Y à H:i', strtotime($contrainte['date_validation'])); ?>.
            </div>
        <?php endif; ?>
        <div class="text-center mt-3">
            <form method="post" action="src/Enseignant/telechargerPdf.php" target="_blank">
                <input type="hidden" name="fiche" value="fiche_voeux">
                <button type="submit" class="btn btn-success">Télécharger la fiche en PDF</button>
            </form>
        </div>
    <?php endif; ?>

    <form id="ficheForm" action="src/Enseignant/EnregistrerContraintes.php" method="post" class="bg-white p-4 shadow-sm rounded">
        <p class="mb-4">Indiquez les plages horaires durant lesquelles vous ne pouvez pas enseigner :</p>

        <div class="table-responsive">
            <table class="table table-bordered text-center">
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
                $horaires = [
                    "8_10" => "8h-10h",
                    "10_12" => "10h-12h",
                    "14_16" => "14h-16h",
                    "16_18" => "16h-18h"
                ];
                $jours = ["lundi", "mardi", "mercredi", "jeudi", "vendredi"];

                foreach ($horaires as $heure_key => $heure_label) {
                    echo "<tr>";
                    echo "<td>$heure_label</td>";
                    foreach ($jours as $jour) {
                        $name = "{$jour}_{$heure_key}";
                        $checked = in_array($name, $contraintesChecked) ? 'checked' : '';
                        $disabled = $verrouille ? 'disabled' : '';
                        echo "<td><input type='checkbox' name='contraintes[]' value='$name' onchange='limiterContraintes()' $checked $disabled></td>";
                    }
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <p>Je préfère, si possible, éviter le créneau :</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="creneau_prefere" value="8h-10h" id="pref_8_10"
                    <?php echo ($creneauPrefere === "8h-10h") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>>
                <label class="form-check-label" for="pref_8_10">de 8h à 10h</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="creneau_prefere" value="16h-18h" id="pref_16_18"
                    <?php echo ($creneauPrefere === "16h-18h") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>>
                <label class="form-check-label" for="pref_16_18">de 16h à 18h</label>
            </div>
        </div>

        <div class="mb-3">
            <p>J'accepte d'avoir cours le samedi :</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="cours_samedi" value="oui" id="samedi_oui"
                    <?php echo ($coursSamedi === "oui") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>>
                <label class="form-check-label" for="samedi_oui">Oui</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="cours_samedi" value="non" id="samedi_non"
                    <?php echo ($coursSamedi === "non") ? 'checked' : ''; echo $verrouille ? ' disabled' : ''; ?>>
                <label class="form-check-label" for="samedi_non">Non</label>
            </div>
        </div>
        <div class="mb-3">
            <label for="commentaire" class="form-label">Commentaires ou précisions :</label>
            <textarea class="form-control" id="commentaire" name="commentaire" rows="4" placeholder="Ajoutez ici vos éventuelles remarques ou précisions..." <?php echo $verrouille ? 'disabled' : ''; ?>><?php echo htmlspecialchars($commentaire ?? ''); ?></textarea>
        </div>

        <?php if (!$isPuppeteer): ?>
            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary" id="validerBtn" <?php echo $verrouille ? 'disabled' : ''; ?>>Valider</button>
            </div>
        <?php endif; ?>
    </form>
</div>

<?php if (!$isPuppeteer): ?>
    <script>
        function limiterContraintes() {
            let checkboxes = document.querySelectorAll('input[type="checkbox"]');
            let checkedCount = 0;

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    checkedCount++;
                }
            });

            if (checkedCount > 4) {
                alert("Vous ne pouvez sélectionner que 4 contraintes au maximum.");
                event.target.checked = false;
            }
        }

        document.addEventListener("DOMContentLoaded", function() {
            document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
                checkbox.addEventListener("change", limiterContraintes);
            });
        });
    </script>
<?php endif; ?>

</body>
</html>
