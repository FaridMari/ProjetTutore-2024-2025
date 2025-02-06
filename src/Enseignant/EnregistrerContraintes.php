<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    // Connexion à la base de données
    $conn = connexionFactory::makeConnection();

    if (!isset($_SESSION['id_utilisateur'])) {
        throw new Exception("Utilisateur non connecté.");
    }

    $id_utilisateur = $_SESSION['id_utilisateur'];

    $stmtUser = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = :id_utilisateur");
    $stmtUser->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmtUser->execute();
    $user = $stmtUser->fetch();

    if (!$user) {
        throw new Exception("Utilisateur introuvable.");
    }

    $nom_prenom = $user['nom'] . ' ' . $user['prenom'];

    // 🔹 Récupérer les valeurs du formulaire
    $creneau_preference = isset($_POST['creneau_prefere']) ? $_POST['creneau_prefere'] : "Non spécifié";
    $cours_samedi = isset($_POST['cours_samedi']) ? $_POST['cours_samedi'] : "Non spécifié";
    $choix_contraintes = isset($_POST['contraintes']) ? $_POST['contraintes'] : [];

    if (count($choix_contraintes) > 4) {
        $_SESSION['error_message'] = "Vous ne pouvez sélectionner que 4 contraintes au maximum.";
        header("Location: ../../index.php?action=enseignantFicheContrainte");
        exit();
    }

    $conn->beginTransaction();
    $stmtDelete = $conn->prepare("DELETE FROM contraintes WHERE id_utilisateur = :id_utilisateur");
    $stmtDelete->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmtDelete->execute();

    $_SESSION['creneau_prefere'] = $creneau_preference; // ✅ Assurer que le créneau est stocké en session
    $_SESSION['choix_contraintes'] = $choix_contraintes;
    $_SESSION['cours_samedi'] = $cours_samedi;

    $_SESSION['pdf_data'] = [
        'nom_prenom' => $nom_prenom,
        'choix_contraintes' => $choix_contraintes,
        'creneau_prefere' => $creneau_preference,
        'cours_samedi' => $cours_samedi
    ];

    echo "<script>
        alert('Les contraintes ont bien été enregistrées.');
        if (confirm('Voulez-vous télécharger la fiche de vœux en PDF ?')) {
            window.location.href = 'telechargerPdf.php';
        } else {
            window.location.href = '../../index.php?action=enseignantFicheContrainte';
        }
    </script>";
    exit();

} catch (Exception $e) {
    $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
    header("Location: ../../index.php?action=enseignantFicheContrainte");
    exit();
}
?>
