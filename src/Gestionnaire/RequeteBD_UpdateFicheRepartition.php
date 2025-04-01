<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../modele/CoursDTO.php';
require_once __DIR__ . '/../modele/AffectationDTO.php';
require_once __DIR__ . '/../modele/EnseignantDTO.php';
require_once __DIR__ . '/../modele/UtilisateurDTO.php';
require_once __DIR__ . '/../modele/GroupeDTO.php';

use src\Db\connexionFactory;

$pdo = connexionFactory::makeConnection();

// Lecture du payload JSON envoyé par le client
$dataRaw = file_get_contents('php://input');
$payload = json_decode($dataRaw, true);
if (!$payload || !isset($payload['data']) || !isset($payload['formation'])) {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
    exit;
}
$tableData = $payload['data'];
$formationCode = $payload['formation'];

// Re-construire les mappings
$coursDTO = new CoursDTO();
$affectationDTO = new AffectationDTO();
$enseignantDTO = new EnseignantDTO();
$utilisateurDTO = new UtilisateurDTO();
$groupeDTO = new GroupeDTO();

// Récupérer les cours pour la formation
$listeCours = $coursDTO->findByFormation($formationCode);
$courseIndexMapping = [];
foreach ($listeCours as $index => $cours) {
    $courseIndexMapping[$index] = $cours->getIdCours();
}

// Pour les groupes, on suppose les 5 groupes fixes
$fixedGroups = ['GR A', 'GR B', 'GR C', 'GR D', 'GR E'];
$groupMapping = [];
foreach ($fixedGroups as $groupName) {
    $niveau = ($formationCode == 'S1' || $formationCode == 'S2') ? 'BUT 1' : 'BUT 2';
    $group = $groupeDTO->findByNomAndNiveau($groupName, $niveau);
    if ($group) {
        $groupMapping[$groupName] = $group->getIdGroupe();
    }
}

// Construire le mapping enseignant: "nom prenom" (en minuscules) -> teacherId
$listeEnseignants = $enseignantDTO->findAll();
$listeUtilisateur = $utilisateurDTO->findAll();
$teacherMapping = [];
foreach ($listeEnseignants as $enseignant) {
    foreach ($listeUtilisateur as $utilisateur) {
        if ($enseignant->getIdUtilisateur() === $utilisateur->getIdUtilisateur()) {
            $key = strtolower(trim($utilisateur->getNom() . ' ' . $utilisateur->getPrenom()));
            $teacherMapping[$key] = $enseignant->getIdEnseignant();
        }
    }
}

// Mapping des types d'heures: offset -> type string
$offsetMapping = [
    0 => 'CM',
    1 => 'TD',
    2 => 'TP',
    3 => 'EI'
];

// Parcourir le tableau
foreach ($tableData as $rowIndex => $row) {
    // La première colonne contient le nom du groupe (mise à jour suite aux modifications côté Handsontable)
    $groupName = trim($row[0]);
    if (!isset($groupMapping[$groupName])) {
        continue;
    }
    $groupId = $groupMapping[$groupName];
    
    // Calculer le nombre de cours : (nombre de colonnes - 1) / 4
    $numCourses = (count($row) - 1) / 4;
    
    for ($courseIdx = 0; $courseIdx < $numCourses; $courseIdx++) {
        for ($offset = 0; $offset < 4; $offset++) {
            // Utilisation de isset pour éviter les undefined array key et le passage de null à trim()
            $colIndex = 1 + $courseIdx * 4 + $offset;
            $teacherField = isset($row[$colIndex]) ? trim($row[$colIndex]) : '';
            $typeHour = $offsetMapping[$offset];
            
            if (!isset($courseIndexMapping[$courseIdx])) {
                continue;
            }
            $courseId = $courseIndexMapping[$courseIdx];
            
            // Supprimer toutes les affectations existantes pour ce couple (cours, groupe, type)
            $affectationDTO->deleteByCourseAndGroupAndType($courseId, $groupId, $typeHour);
            
            if ($teacherField === "") {
                continue;
            } else {
                // Si plusieurs enseignants sont saisis, les séparer par une virgule
                $teacherNames = array_map('trim', explode(',', $teacherField));
                foreach ($teacherNames as $tName) {
                    $tNameNormalized = strtolower(trim($tName));
                    if (!isset($teacherMapping[$tNameNormalized])) {
                        continue;
                    }
                    $teacherId = $teacherMapping[$tNameNormalized];



// Récupérer la valeur correspondante dans la table voeux en fonction du type d'heure
                    $stmt = $pdo->prepare("
                    SELECT nb_CM, nb_TD, nb_TP, nb_EI
                    FROM voeux
                    WHERE id_enseignant = :id_enseignant
                    AND id_cours = :id_cours
                    LIMIT 1
                ");
                    $stmt->bindParam(':id_enseignant', $teacherId, PDO::PARAM_INT);
                    $stmt->bindParam(':id_cours', $courseId, PDO::PARAM_INT);
                    $stmt->execute();
                    $voeu = $stmt->fetch(PDO::FETCH_ASSOC);

// Déterminer la valeur des heures affectées en fonction du type d'heure
                    $heuresAffectees = 0;
                    if ($voeu) {
                        switch ($typeHour) {
                            case 'CM':
                                $heuresAffectees = $voeu['nb_CM'];
                                break;
                            case 'TD':
                                $heuresAffectees = $voeu['nb_TD'];
                                break;
                            case 'TP':
                                $heuresAffectees = $voeu['nb_TP'];
                                break;
                            case 'EI':
                                $heuresAffectees = $voeu['nb_EI'];
                                break;
                        }
                    }


                    // Récupérer l'ID utilisateur associé à l'enseignant
                    $stmt = $pdo->prepare("
    SELECT id_utilisateur 
    FROM enseignants 
    WHERE id_enseignant = :id_enseignant
");
                    $stmt->bindParam(':id_enseignant', $teacherId, PDO::PARAM_INT);
                    $stmt->execute();
                    $enseignant = $stmt->fetch(PDO::FETCH_ASSOC);

                    $statut = null;
                    if ($enseignant) {
                        // Récupérer le statut de l'utilisateur
                        $stmt = $pdo->prepare("
        SELECT statut 
        FROM utilisateurs 
        WHERE id_utilisateur = :id_utilisateur
    ");
                        $stmt->bindParam(':id_utilisateur', $enseignant['id_utilisateur'], PDO::PARAM_INT);
                        $stmt->execute();
                        $user = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($user) {
                            $statut = $user['statut'];
                        }
                    }

// Déterminer heures_affectees_reel en fonction du statut et du type d'heure
                    $heures_affectees_reel = $heuresAffectees; // Par défaut, garder la valeur originale

                    if ($statut) {
                        switch (true) {
                            case preg_match('/^doctorant missionnaire$/', $statut):
                                switch ($typeHour) {
                                    case 'TD': case 'TP': case 'CM': case 'EI': case 'PBUT':
                                    $heures_affectees_reel = $heuresAffectees;
                                    break;
                                }
                                break;

                            case preg_match('/^ater$/', $statut):
                                switch ($typeHour) {
                                    case 'TD': case 'CM':case 'PBUT':
                                    $heures_affectees_reel = $heuresAffectees;
                                    break;
                                    case 'EI':
                                        $heures_affectees_reel = $heuresAffectees * 7 / 6;
                                        break;
                                    case 'TP':
                                        $heures_affectees_reel = $heuresAffectees * 2 / 3;
                                        break;
                                }
                                break;

                            case preg_match('/^(vacataire enseignant|vacataire professionnel|doctorant vacataire)$/', $statut):
                                switch ($typeHour) {
                                    case 'TD': case 'PBUT':
                                    $heures_affectees_reel = $heuresAffectees;
                                    break;
                                    case 'TP':
                                        $heures_affectees_reel = $heuresAffectees * 2 / 3;
                                        break;
                                    case 'EI':
                                        $heures_affectees_reel = $heuresAffectees * 7 / 6;
                                        break;
                                    case 'CM':
                                        $heures_affectees_reel = $heuresAffectees * 1.5;
                                        break;
                                }
                                break;

                            case preg_match('/^(enseignant-chercheur|enseignant associé)$/', $statut):
                                switch ($typeHour) {
                                    case 'TD': case 'DS': case 'PBUT': case 'TP':
                                    $heures_affectees_reel = $heuresAffectees;
                                    break;
                                    case 'CM':
                                        $heures_affectees_reel = $heuresAffectees * 1.5;
                                        break;
                                    case 'EI':
                                        $heures_affectees_reel = $heuresAffectees * 7 / 6;
                                        break;
                                }
                                break;

//                            case preg_match('/^PRCE/PRAG$/', $statut):
//                                switch ($typeHour) {
//                                    case 'TD': case 'DS': case 'PBUT': case 'TPL': case 'TP':
//                                    $heures_affectees_reel = $heuresAffectees;
//                                    break;
//                                    case 'CM':
//                                        $heures_affectees_reel = $heuresAffectees * 1.5;
//                                        break;
//                                    case 'EI':
//                                        $heures_affectees_reel = $heuresAffectees * 7 / 6;
//                                        break;
//                                }
//                                break;
                        }
                    }



// Utiliser cette valeur lors de l'insertion
                    $newAffectation = new Affectation(null, $teacherId, $courseId, $groupId, $heures_affectees_reel, $typeHour);


                    // Utiliser insert() pour forcer un nouvel enregistrement à chaque fois
                    $affectationDTO->insert($newAffectation);
                }
            }
        }
    }
}

echo json_encode(['success' => true]);
?>
