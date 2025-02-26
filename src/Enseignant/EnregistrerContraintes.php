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


    // Vérifier si la fiche est déjà validée
    $stmtCheck = $conn->prepare("SELECT statut FROM contraintes WHERE id_utilisateur = :id_utilisateur LIMIT 1");
    $stmtCheck->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmtCheck->execute();
    $existingContrainte = $stmtCheck->fetch();

    if ($existingContrainte && $existingContrainte['statut'] === 'valide') {
        $_SESSION['error_message'] = "Votre fiche a été validée et ne peut plus être modifiée.";
        header("Location: ../../index.php?action=enseignantFicheContrainte");
        exit();
    }

    // Récupérer les valeurs du formulaire
    $creneau_preference = isset($_POST['creneau_prefere']) ? $_POST['creneau_prefere'] : "Non spécifié";
    $cours_samedi = isset($_POST['cours_samedi']) ? $_POST['cours_samedi'] : "Non spécifié";
    $choix_contraintes = isset($_POST['contraintes']) ? $_POST['contraintes'] : [];

    if (count($choix_contraintes) > 4) {
        $_SESSION['error_message'] = "Vous ne pouvez sélectionner que 4 contraintes au maximum.";
        header("Location: ../../index.php?action=enseignantFicheContrainte");
        exit();
    }

    $conn->beginTransaction();

    // 🔹 Supprimer les anciennes contraintes seulement si elles ne sont pas validées
    $stmtDelete = $conn->prepare("DELETE FROM contraintes WHERE id_utilisateur = :id_utilisateur AND statut != 'valide'");
    $stmtDelete->bindValue(':id_utilisateur', $id_utilisateur, PDO::PARAM_INT);
    $stmtDelete->execute();

    // 🔹 Insérer les nouvelles contraintes avec `statut = en attente`
    foreach ($choix_contraintes as $contrainte) {
        $stmtInsert = $conn->prepare("INSERT INTO contraintes (id_utilisateur, jour, heure_debut, heure_fin, creneau_preference, cours_samedi, statut) VALUES (?, ?, ?, ?, ?, ?, 'en attente')");
        $jour_heure = explode('_', $contrainte);
        $jour = $jour_heure[0];
        $heure_debut = str_replace('h', '', $jour_heure[1]);
        $heure_fin = intval($heure_debut) + 2;

        $stmtInsert->execute([$id_utilisateur, $jour, $heure_debut, $heure_fin, $creneau_preference, $cours_samedi]);
    }

    $conn->commit();

    // Stockage des données pour la génération du PDF
    $_SESSION['pdf_data'] = [
        'nom_prenom' => $user['nom'] . ' ' . $user['prenom'],
        'choix_contraintes' => $choix_contraintes,
        'creneau_prefere' => $creneau_preference,
        'cours_samedi' => $cours_samedi
    ];

    // ✅ Affichage de l'alerte et génération du PDF
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
    $conn->rollBack();
    $_SESSION['error_message'] = "Erreur : " . $e->getMessage();
    header("Location: ../../index.php?action=enseignantFicheContrainte");
    exit();
}
?>
