<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    // Connexion à la base
    $conn = connexionFactory::makeConnection();

    // Requête pour récupérer les fiches en attente (status = 'pending')
    $query = "SELECT c.*, u.nom, u.prenom 
              FROM contraintes c 
              INNER JOIN utilisateurs u ON c.id_utilisateur = u.id_utilisateur
              WHERE c.status = 'pending'";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $fiches = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    die("Erreur : " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Validation des Fiches Enseignants</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h1 class="mb-4">Fiches à valider</h1>

    <?php if (empty($fiches)): ?>
        <p>Aucune fiche en attente de validation.</p>
    <?php else: ?>
        <?php foreach ($fiches as $fiche): ?>
            <div class="card mb-3">
                <div class="card-header">
                    <?php echo htmlspecialchars($fiche['prenom'] . ' ' . $fiche['nom']); ?>
                </div>
                <div class="card-body">
                    <p><strong>Créneau Préféré :</strong> <?php echo htmlspecialchars($fiche['creneau_prefere']); ?></p>
                    <p><strong>Cours le samedi :</strong> <?php echo htmlspecialchars($fiche['cours_samedi']); ?></p>
                    <p><strong>Contraintes :</strong>
                        <?php
                        // Décoder et afficher les contraintes (si stockées en JSON)
                        $contraintes = json_decode($fiche['contraintes'], true);
                        echo is_array($contraintes) ? implode(", ", $contraintes) : "Aucune";
                        ?>
                    </p>
                    <!-- Bouton de validation -->
                    <form action="validerFiche.php" method="post">
                        <input type="hidden" name="fiche_id" value="<?php echo htmlspecialchars($fiche['id']); ?>">
                        <button type="submit" class="btn btn-success">Valider la fiche</button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>
</body>
</html>
