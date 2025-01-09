<?php
$host = 'localhost';
$dbname = 'projettutore';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    if (isset($_GET['nom_cours'])) {
        $nom_cours = $_GET['nom_cours'];

        // Récupérer l'id_cours correspondant au nom_cours
        $stmt = $pdo->prepare("SELECT id_cours FROM cours WHERE nom_cours = :nom_cours");
        $stmt->execute(['nom_cours' => $nom_cours]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cours) {
            $id_cours = $cours['id_cours'];

            // Récupérer les répartitions pour l'id_cours
            $stmt = $pdo->prepare("SELECT semaine_debut, semaine_fin, type_heure, nb_heures_par_semaine FROM repartition_heures WHERE id_cours = :id_cours");
            $stmt->execute(['id_cours' => $id_cours]);
            $repartitions = $stmt->fetchAll(PDO::FETCH_ASSOC);

            echo json_encode($repartitions);
        } else {
            echo json_encode(['error' => 'Cours non trouvé']);
        }
    } else {
        echo json_encode(['error' => 'Le paramètre "nom_cours" est manquant']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
