<?php
require_once __DIR__ . '/../../vendor/autoload.php';

require_once __DIR__ . '/../modele/GroupeDTO.php';
require_once __DIR__ . '/../modele/AffectationDTO.php';

$groupeDTO = new GroupeDTO();
$affectationDTO = new AffectationDTO();

$data = json_decode(file_get_contents('php://input'), true);

if ($data && is_array($data)) {
    foreach ($data as $item) {
        if (isset($item['idCours'], $item['groupe'], $item['typeHeure'], $item['heures'], $item['semestre'],$item['enseignant'])) {
            if ($item['semestre'] == 'S1' || $item['semestre'] == 'S2') {
                $niveau = 'BUT 1';
            } else {
                $niveau = 'BUT 2';
            }
            $groupe = $groupeDTO->findByNomAndNiveau($item['groupe'], $niveau);
            $affectation = new Affectation(
                null,
                $item['enseignant'],
                $item['idCours'],
                $groupe->getIdGroupe(),
                $item['heures'],
                $item['typeHeure']
            );
            $affectationDTO->save($affectation);
        }
    }
    echo json_encode(['success' => true]);
    exit;
} else {
    echo json_encode(['success' => false, 'error' => 'Données invalides']);
}
?>