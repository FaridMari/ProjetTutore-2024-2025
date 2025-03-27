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
    $ficheId = intval($_POST['fiche_id']);
    $table = $_POST['table'];

    if ($table === 'contraintes') {
        Contrainte::validerFiche($pdo, $ficheId);
    } elseif ($table === 'details_cours') {
        DetailsCours::validerFiche($pdo, $ficheId);
    } elseif ($table === 'voeux') {
        Voeux::validerFiche($pdo, $ficheId);
    }

    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des Fiches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="p-4">
<h2>Fiches en attente</h2>

<h3>Fiches de Contraintes</h3>
<?php
// Nouvelle requête pour regrouper les fiches par utilisateur
$stmt = $pdo->prepare("
    SELECT c.id_utilisateur, u.nom, u.prenom, c.creneau_preference, c.cours_samedi, c.commentaire
    FROM contraintes c
    JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
    WHERE c.statut = 'en attente'
    GROUP BY c.id_utilisateur
");
$stmt->execute();
$fichesContraintes = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<?php if (!empty($fichesContraintes)): ?>
    <?php foreach ($fichesContraintes as $fiche): ?>
        <div class="border p-3 mb-3">
            <p>
                <strong>Nom :</strong> <?= htmlspecialchars($fiche['prenom'] . ' ' . $fiche['nom']) ?><br>
                <strong>Préférence :</strong> <?= htmlspecialchars($fiche['creneau_preference']) ?><br>
                <strong>Cours le samedi :</strong> <?= htmlspecialchars($fiche['cours_samedi']) ?><br>
                <strong>Commentaire :</strong>
                <?= (isset($fiche['commentaire']) && strlen(trim($fiche['commentaire'])) > 0)
                    ? nl2br(htmlspecialchars($fiche['commentaire']))
                    : 'Aucun'; ?>
            </p>
            <form method="POST">
                <input type="hidden" name="fiche_id" value="<?= $fiche['id_utilisateur'] ?>">
                <input type="hidden" name="table" value="contraintes">
                <button type="submit" name="valider" class="btn btn-success">Valider</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune fiche de contrainte en attente.</p>
<?php endif; ?>


<h3>Fiches Ressources</h3>
<?php if (!empty($fichesDetailsCours)): ?>
    <?php foreach ($fichesDetailsCours as $fiche): ?>
        <div class="border p-3 mb-3">
            <p>
                <strong>Type de salle :</strong> <?= htmlspecialchars($fiche['type_salle']) ?><br>
                <strong>Équipements spécifiques :</strong> <?= htmlspecialchars($fiche['equipements_specifiques']) ?><br>
                <strong>Détails :</strong> <?= htmlspecialchars($fiche['details']) ?><br>
                <strong>Commentaire :</strong> <?= isset($fiche['commentaire']) && strlen(trim($fiche['commentaire'])) > 0 ? nl2br(htmlspecialchars($fiche['commentaire'])) : 'Aucun' ?>
            </p>
            <form method="POST">
                <input type="hidden" name="fiche_id" value="<?= $fiche['id_ressource'] ?>">
                <input type="hidden" name="table" value="details_cours">
                <button type="submit" name="valider" class="btn btn-success">Valider</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune fiche ressource en attente.</p>
<?php endif; ?>

<h3>Fiches Prévisionnelles</h3>
<?php if (!empty($fichesVoeux)): ?>
    <?php foreach ($fichesVoeux as $fiche): ?>
        <div class="border p-3 mb-3">
            <p>
                <strong>Semestre :</strong> <?= htmlspecialchars($fiche['semestre']) ?><br>
                <strong>CM :</strong> <?= htmlspecialchars($fiche['nb_CM']) ?>h,
                <strong>TD :</strong> <?= htmlspecialchars($fiche['nb_TD']) ?>h,
                <strong>TP :</strong> <?= htmlspecialchars($fiche['nb_TP']) ?>h,
                <strong>EI :</strong> <?= htmlspecialchars($fiche['nb_EI']) ?>h<br>
                <strong>Remarques :</strong> <?= isset($fiche['remarques']) && strlen(trim($fiche['remarques'])) > 0 ? nl2br(htmlspecialchars($fiche['remarques'])) : 'Aucune' ?>
            </p>
            <form method="POST">
                <input type="hidden" name="fiche_id" value="<?= $fiche['id_voeu'] ?>">
                <input type="hidden" name="table" value="voeux">
                <button type="submit" name="valider" class="btn btn-success">Valider</button>
            </form>
        </div>
    <?php endforeach; ?>
<?php else: ?>
    <p>Aucune fiche prévisionnelle en attente.</p>
<?php endif; ?>

</body>
</html>
