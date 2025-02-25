<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../modele/CoursDTO.php';
require_once __DIR__ . '/../modele/AffectationDTO.php';
require_once __DIR__ . '/../modele/EnseignantDTO.php';
require_once __DIR__ . '/../modele/UtilisateurDTO.php';
require_once __DIR__ . '/../modele/GroupeDTO.php';

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
    // La deuxième colonne contient le nom du groupe
    $groupName = trim($row[1]);
    if (!isset($groupMapping[$groupName])) {
        continue;
    }
    $groupId = $groupMapping[$groupName];
    // Calculer le nombre de cours : (nombre de colonnes - 2) / 4
    $numCourses = (count($row) - 2) / 4;
    for ($courseIdx = 0; $courseIdx < $numCourses; $courseIdx++) {
        for ($offset = 0; $offset < 4; $offset++) {
            $colIndex = 2 + $courseIdx * 4 + $offset;
            $teacherField = trim($row[$colIndex]);
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
                    $newAffectation = new Affectation(null, $teacherId, $courseId, $groupId, 0, $typeHour);
                    // Utiliser insert() pour forcer un nouvel enregistrement à chaque fois
                    $affectationDTO->insert($newAffectation);
                }
            }
        }
    }
}

echo json_encode(['success' => true]);
?>
