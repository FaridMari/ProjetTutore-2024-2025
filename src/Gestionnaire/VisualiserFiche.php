<?php
require_once "connexionFactory.php";
require_once "Contrainte.php";
require_once "DetailsCours.php";
require_once "Voeux.php";

$pdo = ConnexionFactory::getConnexion();

// Récupérer les fiches en attente
$stmt = $pdo->prepare("SELECT * FROM contraintes WHERE statut = 'en attente'");
$stmt->execute();
$fichesContraintes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM details_cours WHERE statut = 'en attente'");
$stmt->execute();
$fichesDetailsCours = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->prepare("SELECT * FROM voeux WHERE statut = 'en attente'");
$stmt->execute();
$fichesVoeux = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Vérifier si un formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['valider'], $_POST['fiche_id'], $_POST['table'])) {
    $ficheId = intval($_POST['fiche_id']); // Sécuriser l'ID
    $table = $_POST['table'];

    if ($table === 'contraintes') {
        Contrainte::validerFiche($pdo, $ficheId);
    } elseif ($table === 'details_cours') {
        DetailsCours::validerFiche($pdo, $ficheId);
    } elseif ($table === 'voeux') {
        Voeux::validerFiche($pdo, $ficheId);
    }

    // Rediriger pour éviter la soumission multiple du formulaire
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des Fiches</title>
</head>
<body>
<h2>Fiches en attente</h2>

<h3>Contraintes</h3>
<?php if (!empty($fichesContraintes)): ?>
    <?php foreach ($fichesContraintes as $fiche): ?>
        <p><?= htmlspecialchars($fiche['jour']) ?> - <?= htmlspecialchars($fiche['heure_debut']) ?> à <?= htmlspecialchars($fiche['heure_fin']) ?></p>
        <form method="POST">
            <input type="hidden" name="fiche_id" value="<?= $fiche['id_contrainte'] ?>">
            <input type="hidden" name="table" value="contraintes">
            <button type="submit" name="valider">Valider</button>
        </form>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune fiche de contrainte en attente.</p>
<?php endif; ?>

<h3>Fiches Ressources</h3>
<?php if (!empty($fichesDetailsCours)): ?>
    <?php foreach ($fichesDetailsCours as $fiche): ?>
        <p><?= htmlspecialchars($fiche['type_salle']) ?> - <?= htmlspecialchars($fiche['details']) ?></p>
        <form method="POST">
            <input type="hidden" name="fiche_id" value="<?= $fiche['id_ressource'] ?>">
            <input type="hidden" name="table" value="details_cours">
            <button type="submit" name="valider">Valider</button>
        </form>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune fiche ressource en attente.</p>
<?php endif; ?>

<h3>Fiches Prévisionnelles</h3>
<?php if (!empty($fichesVoeux)): ?>
    <?php foreach ($fichesVoeux as $fiche): ?>
        <p>Semestre <?= htmlspecialchars($fiche['semestre']) ?> - CM: <?= htmlspecialchars($fiche['nb_CM']) ?> TD: <?= htmlspecialchars($fiche['nb_TD']) ?></p>
        <form method="POST">
            <input type="hidden" name="fiche_id" value="<?= $fiche['id_voeu'] ?>">
            <input type="hidden" name="table" value="voeux">
            <button type="submit" name="valider">Valider</button>
        </form>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune fiche prévisionnelle en attente.</p>
<?php endif; ?>

</body>
</html>
