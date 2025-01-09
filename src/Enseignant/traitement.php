<?php
$host = 'localhost';
$dbname = 'projettutore';
$user = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Vérifie si la requête est de type POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération du code du cours depuis le formulaire
        $code_cours = $_POST['resourceCode'];

        // Récupération de l'id_cours correspondant au code_cours
        $stmt = $pdo->prepare("SELECT id_cours FROM cours WHERE code_cours = :code_cours");
        $stmt->execute([':code_cours' => $code_cours]);
        $cours = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($cours) {
            $id_cours = $cours['id_cours'];
        } else {
            throw new Exception("Aucun cours trouvé avec le code $code_cours.");
        }

        $id_responsable_module = 55; // Valeur par défaut pour le responsable

        // Récupération des données spécifiques
        $dsDetails = 'DS : ' . ($_POST['dsDetails'] ?? ''); // Détails DS
        $salle016 = $_POST['salle016'] ?? '';   // Salles 016
        $scheduleDetails = $_POST['scheduleDetails'] ?? ''; // Besoins en chariots/salles

        // Construction de "equipements_specifiques"
        $equipementsSpecifiques = '';
        if ($salle016 === 'Oui') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Oui, de préférence\n";
        } elseif ($salle016 === 'Indifférent') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Indifférent\n";
        } elseif ($salle016 === 'Non') {
            $equipementsSpecifiques .= "Intervention en salle 016 : Non, salle non adaptée\n";
        }

        if (!empty($scheduleDetails)) {
            $equipementsSpecifiques .= "Besoins en chariots ou salles : $scheduleDetails\n";
        }

        // Récupérer le premier type d'heure pour remplir type_salle
        $type_salle = $_POST['hour_type'][0] ?? 'Inconnu';

        // Insertion dans la table details_cours
        $stmtDetails = $pdo->prepare("
            INSERT INTO details_cours (
                id_cours,
                id_responsable_module,
                type_salle,
                equipements_specifiques,
                details
            ) VALUES (
                :id_cours,
                :id_responsable_module,
                :type_salle,
                :equipements_specifiques,
                :details
            )
        ");
        $stmtDetails->execute([
            ':id_cours' => $id_cours,
            ':id_responsable_module' => $id_responsable_module,
            ':type_salle' => $type_salle,
            ':equipements_specifiques' => $equipementsSpecifiques,
            ':details' => $dsDetails
        ]);

        echo "Données insérées avec succès.";
    }
} catch (PDOException $e) {
    echo "Erreur PDO : " . $e->getMessage();
} catch (Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
?>
