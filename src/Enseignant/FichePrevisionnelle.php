<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once __DIR__ . '/../modele/CoursDTO.php';
require_once __DIR__ . '/../modele/EnseignantDTO.php';
require_once __DIR__ . '/../modele/VoeuDTO.php';
require_once __DIR__ . '/../modele/VoeuHorsIUTDTO.php';
use src\Db\connexionFactory;
$errors = [];
$userId = $_SESSION['user_id'];
$conn = connexionFactory::makeConnection();
$stmt = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = :userId");
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$enseignantData = $stmt->fetch(PDO::FETCH_ASSOC);

$nomPrenom = $enseignantData ? $enseignantData['nom'] . ' ' . $enseignantData['prenom'] : "Nom Inexistant";

$coursDTO = new CoursDTO();
$enseignantDTO = new EnseignantDTO();
$voeuDTO = new VoeuDTO();
$voeuHorsIUTDTO = new VoeuHorsIUTDTO();

$enseignant = $enseignantDTO->findByUtilisateurId($userId);
$idEnseignant = $enseignant ? $enseignant->getIdEnseignant() : null;

$coursList = $coursDTO->findAll();

$voeuxExistants = [];
$voeuxHorsIUTExistants = [];
if ($idEnseignant !== null) {
    $voeuxExistants = $voeuDTO->findByEnseignant($idEnseignant);
    $voeuxHorsIUTExistants = $voeuHorsIUTDTO->findByEnseignant($idEnseignant);
}

$voeuxSeptembre = [];
$voeuxJanvier = [];
$semestresSeptembre = ['1','3','5'];
$semestresJanvier = ['2','4','6'];

foreach ($voeuxExistants as $v) {
    if (in_array($v->getSemestre(), $semestresSeptembre)) {
        $voeuxSeptembre[] = $v;
    } else {
        $voeuxJanvier[] = $v;
    }
}
// Stockage des données pour le PDF
$_SESSION['pdf_data'] = [
    'enseignant' => $nomPrenom,
    'voeux_septembre' => array_map(function($v) use ($coursDTO) {
        $cours = $coursDTO->findById($v->getIdCours());
        return [
            'ressource' => $cours ? $cours->getNomCours() : 'Inconnu',
            'semestre' => $v->getSemestre(),
            'total' => $v->getNbHeures(),
        ];
    }, $voeuxSeptembre),
    'voeux_janvier' => array_map(function($v) use ($coursDTO) {
        $cours = $coursDTO->findById($v->getIdCours());
        return [
            'ressource' => $cours ? $cours->getNomCours() : 'Inconnu',
            'semestre' => $v->getSemestre(),
            'total' => $v->getNbHeures(),
        ];
    }, $voeuxJanvier),
    'voeux_hors_iut' => array_map(function($v) {
        return [
            'composant' => $v->getComposant(),
            'formation' => $v->getFormation(),
            'module' => $v->getModule(),
            'cm' => $v->getNbHeuresCM(),
            'td' => $v->getNbHeuresTD(),
            'tp' => $v->getNbHeuresTP(),
            'ei' => $v->getNbHeuresEI(),
            'total' => $v->getNbHeuresTotal(),
        ];
    }, $voeuxHorsIUTExistants)
];

$septembre_count = max(1, count($voeuxSeptembre));
$janvier_count   = max(1, count($voeuxJanvier));
$horsIUT_count   = max(1, count($voeuxHorsIUTExistants));

$postData = $_POST;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $postData['septembre'] = [
        'ressource' => [],
        'remarques' => [],
        'formation' => [],
        'semestre' => [],
        'cm' => [],
        'td' => [],
        'tp' => [],
        'ei' => [],
        'total' => [],
        'hetd' => []
    ];

    foreach ($voeuxSeptembre as $v) {
        $cours = $coursDTO->findById($v->getIdCours());
        $nomCours = $cours ? $cours->getNomCours() : '';
        $postData['septembre']['ressource'][] = $nomCours;
        $postData['septembre']['remarques'][] = $v->getRemarque();
        $postData['septembre']['formation'][] = ''; 
        $postData['septembre']['semestre'][] = $v->getSemestre();
        $postData['septembre']['cm'][] = '';
        $postData['septembre']['td'][] = '';
        $postData['septembre']['tp'][] = '';
        $postData['septembre']['ei'][] = '';
        $postData['septembre']['total'][] = $v->getNbHeures();
        $postData['septembre']['hetd'][] = '';
    }

    $postData['janvier'] = [
        'ressource' => [],
        'remarques' => [],
        'formation' => [],
        'semestre' => [],
        'cm' => [],
        'td' => [],
        'tp' => [],
        'ei' => [],
        'total' => [],
        'hetd' => []
    ];

    foreach ($voeuxJanvier as $v) {
        $cours = $coursDTO->findById($v->getIdCours());
        $nomCours = $cours ? $cours->getNomCours() : '';
        $postData['janvier']['ressource'][] = $nomCours;
        $postData['janvier']['remarques'][] = $v->getRemarque();
        $postData['janvier']['formation'][] = '';
        $postData['janvier']['semestre'][] = $v->getSemestre();
        $postData['janvier']['cm'][] = '';
        $postData['janvier']['td'][] = '';
        $postData['janvier']['tp'][] = '';
        $postData['janvier']['ei'][] = '';
        $postData['janvier']['total'][] = $v->getNbHeures();
        $postData['janvier']['hetd'][] = '';
    }

    $postData['hors_iut'] = [
        'composant' => [],
        'formation' => [],
        'module' => [],
        'cm' => [],
        'td' => [],
        'tp' => [],
        'ei' => [],
        'total' => [],
    ];

    foreach ($voeuxHorsIUTExistants as $v) {
        $postData['hors_iut']['composant'][] = htmlspecialchars($v->getComposant());
        $postData['hors_iut']['formation'][] = htmlspecialchars($v->getFormation());
        $postData['hors_iut']['module'][] = htmlspecialchars($v->getModule());
        $postData['hors_iut']['cm'][] = $v->getNbHeuresCM();
        $postData['hors_iut']['td'][] = $v->getNbHeuresTD();
        $postData['hors_iut']['tp'][] = $v->getNbHeuresTP();
        $postData['hors_iut']['ei'][] = $v->getNbHeuresEI();
        $postData['hors_iut']['total'][] = $v->getNbHeuresTotal();
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['ajouter_septembre'])) {
        $septembre_count++;
    }
    if (isset($_POST['ajouter_janvier'])) {
        $janvier_count++;
    }
    if (isset($_POST['ajouter_hors_info'])) {
        $horsIUT_count++;
    }

    if (isset($_POST['envoyer'])) {
        if (isset($_POST['hors_iut'])) {
            $hors_iut_composant = $_POST['hors_iut']['composant'] ?? [];
            $hors_iut_formation = $_POST['hors_iut']['formation'] ?? [];
            $hors_iut_module = $_POST['hors_iut']['module'] ?? [];
            $hors_iut_cm = $_POST['hors_iut']['cm'] ?? [];
            $hors_iut_td = $_POST['hors_iut']['td'] ?? [];
            $hors_iut_tp = $_POST['hors_iut']['tp'] ?? [];
            $hors_iut_ei = $_POST['hors_iut']['ei'] ?? [];
            $hors_iut_total = $_POST['hors_iut']['total'] ?? [];

            foreach ($hors_iut_composant as $i => $composant) {
                $composant = trim($composant);
                $formation = trim($hors_iut_formation[$i] ?? '');
                $module = trim($hors_iut_module[$i] ?? '');
                $cm = isset($hors_iut_cm[$i]) ? $hors_iut_cm[$i] : '';
                $td = isset($hors_iut_td[$i]) ? $hors_iut_td[$i] : '';
                $tp = isset($hors_iut_tp[$i]) ? $hors_iut_tp[$i] : '';
                $ei = isset($hors_iut_ei[$i]) ? $hors_iut_ei[$i] : '';

                if ($composant === '' || $formation === '' || $module === '') {
                    $errors[] = "La ligne " . ($i + 1) . " de 'hors_iut' doit avoir les champs Composant, Formation et Module remplis.";
                }

                if (!is_numeric($cm) || !is_numeric($td) || !is_numeric($tp) || !is_numeric($ei)) {
                    $errors[] = "La ligne " . ($i + 1) . " de 'hors_iut' doit avoir les champs CM, TD, TP et EI numériques.";
                }
            }
        }

        if (!empty($errors)) {
        } else {
            if ($idEnseignant !== null) {
                $voeuDTO->deleteByEnseignant($idEnseignant);
                $voeuHorsIUTDTO->deleteByEnseignant($idEnseignant);
            }

            if (isset($_POST['septembre'])) {
                $sept_ressources = $_POST['septembre']['ressource'] ?? [];
                $sept_semestres = $_POST['septembre']['semestre'] ?? [];
                $sept_remarques = $_POST['septembre']['remarques'] ?? [];
                $sept_total = $_POST['septembre']['total'] ?? [];

                foreach ($sept_ressources as $i => $nomCours) {
                    if ($nomCours !== '') {
                        $coursTrouves = $coursDTO->findByName($nomCours);
                        if (!empty($coursTrouves)) {
                            $cours = $coursTrouves[0];
                            $idCours = $cours->getIdCours();

                            $remarque = $sept_remarques[$i] ?? '';
                            $semestre = 'S'. $sept_semestres[$i] ?? '';
                            $nbHeures = isset($sept_total[$i]) ? (float)$sept_total[$i] : 0;

                            $voeu = new Voeu(null, $idEnseignant, $idCours, $remarque, $semestre, $nbHeures);
                            $voeuDTO->save($voeu);
                        }
                    }
                }
            }

            if (isset($_POST['janvier'])) {
                $jan_ressources = $_POST['janvier']['ressource'] ?? [];
                $jan_semestres = $_POST['janvier']['semestre'] ?? [];
                $jan_remarques = $_POST['janvier']['remarques'] ?? [];
                $jan_total = $_POST['janvier']['total'] ?? [];
                $jan_hetd = $_POST['janvier']['hetd'] ?? [];

                foreach ($jan_ressources as $i => $nomCours) {
                    if ($nomCours !== '') {
                        $coursTrouves = $coursDTO->findByName($nomCours);
                        if (!empty($coursTrouves)) {
                            $cours = $coursTrouves[0];
                            $idCours = $cours->getIdCours();

                            $remarque = $jan_remarques[$i] ?? '';
                            $semestre = $jan_semestres[$i] ?? '';
                            $nbHeures = isset($jan_total[$i]) ? (float)$jan_total[$i] : 0;

                            $voeu = new Voeu(null, $idEnseignant, $idCours, $remarque, $semestre, $nbHeures);
                            $voeuDTO->save($voeu);
                        }
                    }
                }
            }

            if (isset($_POST['hors_iut'])) {
                $hors_iut_composant = $_POST['hors_iut']['composant'] ?? [];
                $hors_iut_formation = $_POST['hors_iut']['formation'] ?? [];
                $hors_iut_module = $_POST['hors_iut']['module'] ?? [];
                $hors_iut_cm = $_POST['hors_iut']['cm'] ?? [];
                $hors_iut_td = $_POST['hors_iut']['td'] ?? [];
                $hors_iut_tp = $_POST['hors_iut']['tp'] ?? [];
                $hors_iut_ei = $_POST['hors_iut']['ei'] ?? [];
                $hors_iut_total = $_POST['hors_iut']['total'] ?? [];
                $hors_iut_hetd = $_POST['hors_iut']['hetd'] ?? [];

                foreach ($hors_iut_composant as $i => $composant) {
                    $composant = trim($composant);
                    $formation = trim($hors_iut_formation[$i] ?? '');
                    $module = trim($hors_iut_module[$i] ?? '');
                    $cm = isset($hors_iut_cm[$i]) ? floatval($hors_iut_cm[$i]) : 0;
                    $td = isset($hors_iut_td[$i]) ? floatval($hors_iut_td[$i]) : 0;
                    $tp = isset($hors_iut_tp[$i]) ? floatval($hors_iut_tp[$i]) : 0;
                    $ei = isset($hors_iut_ei[$i]) ? floatval($hors_iut_ei[$i]) : 0;
                    $total = isset($hors_iut_total[$i]) ? floatval($hors_iut_total[$i]) : 0;

                    if ($composant !== '' && $formation !== '' && $module !== '') {
                        $voeuHI = new VoeuHorsIUT(
                            null,
                            $idEnseignant,
                            $composant,
                            $formation,
                            $module,
                            $cm,
                            $td,
                            $tp,
                            $ei,
                            $total
                        );
                        $voeuHorsIUTDTO->save($voeuHI);
                    }
                }
            }
        }
    }
}

function genererLigne($type, $coursList, $count, $postData) {
    $allowedSemesters = ($type === 'septembre') ? ['1','3','5'] : ['2','4','6'];

    $ressources = $postData[$type]['ressource'] ?? [];
    $remarques = $postData[$type]['remarques'] ?? [];
    $formations = $postData[$type]['formation'] ?? [];
    $semestres = $postData[$type]['semestre'] ?? [];
    $cm = $postData[$type]['cm'] ?? [];
    $td = $postData[$type]['td'] ?? [];
    $tp = $postData[$type]['tp'] ?? [];
    $ei = $postData[$type]['ei'] ?? [];
    $total = $postData[$type]['total'] ?? [];
    $hetd = $postData[$type]['hetd'] ?? [];

    for ($i = 0; $i < $count; $i++) {
        $selectedCoursName = $ressources[$i] ?? '';
        $remarqueValue = isset($remarques[$i]) ? htmlspecialchars($remarques[$i]) : '';
        echo '<tr>';
        echo '<td><input type="text" name="'.$type.'[formation][]" value="'.($formations[$i] ?? '').'" readonly /></td>';

        echo '<td><select name="'.$type.'[ressource][]">';
        echo '<option value="">-- Sélectionner un cours --</option>';
        foreach ($coursList as $c) {
            if (in_array($c->getSemestre(), $allowedSemesters)) {
                $sel = ($c->getNomCours() === $selectedCoursName) ? 'selected' : '';
                echo '<option value="' . htmlspecialchars($c->getNomCours()) . '" '.$sel.'>' . htmlspecialchars($c->getNomCours()) . '</option>';
            }
        }
        echo '</select></td>';

        echo '<td><input type="text" name="'.$type.'[semestre][]" value="'.($semestres[$i] ?? '').'" readonly /></td>';
        echo '<td><input type="number" name="'.$type.'[cm][]" value="'.($cm[$i] ?? '').'" readonly /></td>'; 
        echo '<td><input type="number" name="'.$type.'[td][]" value="'.($td[$i] ?? '').'" readonly /></td>'; 
        echo '<td><input type="number" name="'.$type.'[tp][]" value="'.($tp[$i] ?? '').'" readonly /></td>';
        echo '<td><input type="number" name="'.$type.'[ei][]" value="'.($ei[$i] ?? '').'" readonly /></td>';
        echo '<td><input type="number" name="'.$type.'[total][]" value="'.($total[$i] ?? '').'" readonly /></td>';
        echo '<td><input type="number" name="'.$type.'[hetd][]" value="'.($hetd[$i] ?? '').'" readonly /></td>';
        echo '<td><input type="text" name="'.$type.'[remarques][]" value="'.$remarqueValue.'" /></td>';

        echo '<td><button type="button" class="btn btn-danger btn-sm remove-line">&times;</button></td>';

        echo '</tr>';
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche Prévisionnelle</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .table input, .table select {
            border: none !important;
            box-shadow: none !important;
            background-color: transparent;
            width: 100%; 
        }
        .table input:focus, .table select:focus {
            outline: none;
        }
        .table input[readonly] {
            color: #6c757d; 
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="text-center mb-4">Fiche Prévisionnelle de Service </h2>
        <p><strong>IUT Nancy-Charlemagne - Département Informatique</strong></p>

        <form action="" method="post">
            <input type="hidden" name="septembre_count" value="<?php echo $septembre_count; ?>">
            <input type="hidden" name="janvier_count" value="<?php echo $janvier_count; ?>">

            <?php
                if (!empty($errors)) {
                    echo "<div class='alert alert-danger'>";
                    foreach ($errors as $error) {
                        echo "<p>" . htmlspecialchars($error) . "</p>";
                    }
                    echo "</div>";
                }
            ?>

            <div class="alert alert-warning font-weight-bold">Enseignements sur la période SEPTEMBRE-JANVIER</div>
            <div class="table-responsive">
                <table class="table table-bordered text-center w-100" id="table-septembre">
                    <thead class="thead-light">
                        <tr>
                            <th>Formation BUT</th>
                            <th>Ressource / SAE</th>
                            <th>Semestre</th>
                            <th>CM</th>
                            <th>TD</th>
                            <th>TP</th>
                            <th>EI</th>
                            <th>Total</th>
                            <th>HETD</th>
                            <th>Remarques</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php genererLigne('septembre', $coursList, $septembre_count, $postData); ?>
                        <tr class="total-row">
                            <td colspan="3" class="font-weight-bold">Total :</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-4">
                <button type="submit" name="ajouter_septembre" class="btn btn-success">Ajouter une ligne</button>
            </div>

            <div class="alert alert-warning font-weight-bold">Enseignements sur la période JANVIER-JUIN</div>
            <div class="table-responsive">
                <table class="table table-bordered text-center w-100" id="table-janvier">
                    <thead class="thead-light">
                        <tr>
                            <th>Formation BUT</th>
                            <th>Ressource / SAE</th>
                            <th>Semestre</th>
                            <th>CM</th>
                            <th>TD</th>
                            <th>TP</th>
                            <th>EI</th>
                            <th>Total</th>
                            <th>HETD</th>
                            <th>Remarques</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php genererLigne('janvier', $coursList, $janvier_count, $postData); ?>
                        <tr class="total-row">
                            <td colspan="3" class="font-weight-bold">Total :</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="text-center mb-4">
                <button type="submit" name="ajouter_janvier" class="btn btn-success">Ajouter une ligne</button>
            </div>

            <div class="alert alert-warning font-weight-bold">TOTAL DEPT INFO : </div>
            <div class="table-responsive">
                <table class="table table-bordered text-center w-100" id="table-dept-info">
                    <thead>
                        <tr>
                            <th>TD</th>
                            <th>TP</th>
                            <th>EI</th>
                            <th>Total</th>
                            <th>HETD</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                            <td>0</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="alert alert-warning font-weight-bold">Enseignements hors Dept Info (pour information)</div>
            <div class="table-responsive">
                <table class="table table-bordered text-center w-100" id="table_hors_iut">
                    <thead class="thead-light">
                        <tr>
                            <th>Composants</th>
                            <th>Formation</th>
                            <th>Module</th>
                            <th>CM</th>
                            <th>TD</th>
                            <th>TP</th>
                            <th>EI</th>
                            <th>Total</th>
                            <th>HETD</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>

            <div class="text-center mb-4">
                <button type="button" id="add_line_hors_info" class="btn btn-success">Ajouter une ligne</button>
            </div>

            <div class="text-center mt-4">
                <button type="submit" name="envoyer" class="btn btn-primary" >Envoyer</button>

            </div>
        </form>
    </div>


    <script>
        window.coursData = <?php
            $data = [];
            foreach ($coursList as $c) {
                $data[] = [
                    'nomCours' => $c->getNomCours(),
                    'formation' => $c->getFormation(),
                    'semestre' => $c->getSemestre(),
                    'cm' => $c->getNbHeuresCM(),
                    'td' => $c->getNbHeuresTD(),
                    'tp' => $c->getNbHeuresTP(),
                    'ei' => $c->getNbHeuresEI(),
                    'total' => $c->getNbHeuresTotal(),
                ];
            }
            echo json_encode($data);
        ?>;

        document.addEventListener('DOMContentLoaded', function() {
            function updateLine(selectElem) {
                var tr = selectElem.closest('tr');
                var nomCours = selectElem.value;
                var coursInfo = window.coursData.find(function(c) {
                    return c.nomCours === nomCours;
                });

                var formationInput = tr.querySelector('td:nth-child(1) input'); 
                var semestreInput = tr.querySelector('td:nth-child(3) input'); 

                var inputs = tr.querySelectorAll('input[type="number"]');
                if (coursInfo) {
                    formationInput.value = coursInfo.formation;
                    semestreInput.value = coursInfo.semestre;

                    inputs[0].value = coursInfo.cm;
                    inputs[1].value = coursInfo.td;
                    inputs[2].value = coursInfo.tp;
                    inputs[3].value = coursInfo.ei;
                    inputs[4].value = coursInfo.total;
                    inputs[5].value = (coursInfo.total * 1.5).toFixed(1);
                } else {
                    formationInput.value = '';
                    semestreInput.value = '';
                    inputs.forEach(function(input) {
                        input.value = '';
                    });
                }
                var tableId = selectElem.closest('table').id;
                updateTotals(tableId);
                updateDeptInfoTotals();
            }

            function updateTotals(tableId) {
                var table = document.getElementById(tableId);
                if (!table) return;
                var rows = table.querySelectorAll('tbody tr');
                var totalRow = table.querySelector('tr.total-row');
                if (!totalRow) return;

                var cmSum = 0, tdSum = 0, tpSum = 0, eiSum = 0, totalSum = 0, hetdSum = 0;

                rows.forEach(function(row) {
                    if (row.classList.contains('total-row')) return;
                    var cells = row.querySelectorAll('td');
                    var cm = parseFloat((cells[3].querySelector('input') || {}).value) || 0;
                    var td = parseFloat((cells[4].querySelector('input') || {}).value) || 0;
                    var tp = parseFloat((cells[5].querySelector('input') || {}).value) || 0;
                    var ei = parseFloat((cells[6].querySelector('input') || {}).value) || 0;
                    var tot = parseFloat((cells[7].querySelector('input') || {}).value) || 0;
                    var hetd = parseFloat((cells[8].querySelector('input') || {}).value) || 0;

                    cmSum += cm;
                    tdSum += td;
                    tpSum += tp;
                    eiSum += ei;
                    totalSum += tot;
                    hetdSum += hetd;
                });

                var totalCells = totalRow.querySelectorAll('td');
                totalCells[1].textContent = cmSum;
                totalCells[2].textContent = tdSum;
                totalCells[3].textContent = tpSum;
                totalCells[4].textContent = eiSum;
                totalCells[5].textContent = totalSum;
                totalCells[6].textContent = hetdSum.toFixed(1);
            }

            function updateDeptInfoTotals() {
                var septTable = document.getElementById('table-septembre');
                var janTable = document.getElementById('table-janvier');
                var deptTable = document.getElementById('table-dept-info');

                if (!septTable || !janTable || !deptTable) return;

                var septTotalRow = septTable.querySelector('tr.total-row');
                var janTotalRow = janTable.querySelector('tr.total-row');

                if (!septTotalRow || !janTotalRow) return;

                var septCells = septTotalRow.querySelectorAll('td');
                var janCells = janTotalRow.querySelectorAll('td');

                var septTD = parseFloat(septCells[2].textContent) || 0;
                var septTP = parseFloat(septCells[3].textContent) || 0;
                var septEI = parseFloat(septCells[4].textContent) || 0;
                var septTOTAL = parseFloat(septCells[5].textContent) || 0;
                var septHETD = parseFloat(septCells[6].textContent) || 0;

                var janTD = parseFloat(janCells[2].textContent) || 0;
                var janTP = parseFloat(janCells[3].textContent) || 0;
                var janEI = parseFloat(janCells[4].textContent) || 0;
                var janTOTAL = parseFloat(janCells[5].textContent) || 0;
                var janHETD = parseFloat(janCells[6].textContent) || 0;

                var deptTD = septTD + janTD;
                var deptTP = septTP + janTP;
                var deptEI = septEI + janEI;
                var deptTotal = septTOTAL + janTOTAL;
                var deptHETD = septHETD + janHETD;

                var deptRow = deptTable.querySelector('tbody tr');
                var deptCells = deptRow.querySelectorAll('td');

                deptCells[0].textContent = deptTD;
                deptCells[1].textContent = deptTP;
                deptCells[2].textContent = deptEI;
                deptCells[3].textContent = deptTotal;
                deptCells[4].textContent = deptHETD.toFixed(1);
            }


            var selects = document.querySelectorAll('table select');
            selects.forEach(function(selectElem) {
                selectElem.addEventListener('change', function() {
                    updateLine(this);
                });
            });

            selects.forEach(function(selectElem) {
                if (selectElem.value !== "") {
                    updateLine(selectElem);
                }
            });


            document.addEventListener('click', function(e) {
            if (e.target.classList.contains('remove-line')) {
                var tr = e.target.closest('tr');
                if (tr) {
                    tr.remove();
                }
            updateTotals('table-septembre');
            updateTotals('table-janvier');
            updateDeptInfoTotals();
            }
        });

            updateTotals('table-septembre');
            updateTotals('table-janvier');
            updateDeptInfoTotals();

            function addLineHorsIUT() {
            var table = document.getElementById('table_hors_iut').getElementsByTagName('tbody')[0];
            var newRow = table.insertRow(table.rows.length);

            // Créer les cellules avec des inputs modifiables
            var cellComposant = newRow.insertCell(0);
            var inputComposant = document.createElement('input');
            inputComposant.type = 'text';
            inputComposant.name = 'hors_iut[composant][]';
            inputComposant.required = true;
            cellComposant.appendChild(inputComposant);

            var cellFormation = newRow.insertCell(1);
            var inputFormation = document.createElement('input');
            inputFormation.type = 'text';
            inputFormation.name = 'hors_iut[formation][]';
            inputFormation.required = true;
            cellFormation.appendChild(inputFormation);

            var cellModule = newRow.insertCell(2);
            var inputModule = document.createElement('input');
            inputModule.type = 'text';
            inputModule.name = 'hors_iut[module][]';
            inputModule.required = true;
            cellModule.appendChild(inputModule);

            var cellCM = newRow.insertCell(3);
            var inputCM = document.createElement('input');
            inputCM.type = 'number';
            inputCM.name = 'hors_iut[cm][]';
            inputCM.min = '0';
            inputCM.step = '0.1';
            inputCM.required = true;
            inputCM.addEventListener('input', calculateTotalHorsIUT);
            cellCM.appendChild(inputCM);

            var cellTD = newRow.insertCell(4);
            var inputTD = document.createElement('input');
            inputTD.type = 'number';
            inputTD.name = 'hors_iut[td][]';
            inputTD.min = '0';
            inputTD.step = '0.1';
            inputTD.required = true;
            inputTD.addEventListener('input', calculateTotalHorsIUT);
            cellTD.appendChild(inputTD);

            var cellTP = newRow.insertCell(5);
            var inputTP = document.createElement('input');
            inputTP.type = 'number';
            inputTP.name = 'hors_iut[tp][]';
            inputTP.min = '0';
            inputTP.step = '0.1';
            inputTP.required = true;
            inputTP.addEventListener('input', calculateTotalHorsIUT);
            cellTP.appendChild(inputTP);

            var cellEI = newRow.insertCell(6);
            var inputEI = document.createElement('input');
            inputEI.type = 'number';
            inputEI.name = 'hors_iut[ei][]';
            inputEI.min = '0';
            inputEI.step = '0.1';
            inputEI.required = true;
            inputEI.addEventListener('input', calculateTotalHorsIUT);
            cellEI.appendChild(inputEI);

            var cellTotal = newRow.insertCell(7);
            var inputTotal = document.createElement('input');
            inputTotal.type = 'number';
            inputTotal.name = 'hors_iut[total][]';
            inputTotal.readOnly = true;
            cellTotal.appendChild(inputTotal);

            var cellHETD = newRow.insertCell(8);
            var inputHETD = document.createElement('input');
            inputHETD.type = 'number';
            inputHETD.name = 'hors_iut[hetd][]';
            inputHETD.readOnly = true;
            cellHETD.appendChild(inputHETD);

            var cellAction = newRow.insertCell(9);
            var btnDelete = document.createElement('button');
            btnDelete.type = 'button';
            btnDelete.className = 'btn btn-danger btn-sm remove-line';
            btnDelete.innerHTML = '&times;';
            btnDelete.addEventListener('click', function() {
                this.closest('tr').remove();
                updateTotals('table_hors_info');
                updateDeptInfoTotals();
            });
            cellAction.appendChild(btnDelete);
        }
        document.getElementById('add_line_hors_info').addEventListener('click', addLineHorsIUT);

        function calculateTotalHorsIUT() {
                var tr = this.closest('tr');
                var cm = parseFloat(tr.querySelector('input[name="hors_iut[cm][]"]').value) || 0;
                var td = parseFloat(tr.querySelector('input[name="hors_iut[td][]"]').value) || 0;
                var tp = parseFloat(tr.querySelector('input[name="hors_iut[tp][]"]').value) || 0;
                var ei = parseFloat(tr.querySelector('input[name="hors_iut[ei][]"]').value) || 0;
                
                var total = cm + td + tp + ei;
                var hetd = total * 1.5;
                
                tr.querySelector('input[name="hors_iut[total][]"]').value = total.toFixed(1);
                tr.querySelector('input[name="hors_iut[hetd][]"]').value = hetd.toFixed(1);

                updateTotals('table_hors_iut');
                updateDeptInfoTotals();
            }
            
        });
    </script>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            document.querySelector("button[name='envoyer']").addEventListener("click", function (event) {
                event.preventDefault();
                if (confirm("Les vœux ont été enregistrés avec succès.\nVoulez-vous télécharger le PDF ?")) {
                    const form = this.closest("form");
                    form.action = "src/User/ServicePdf.php";
                    form.submit();
                } else {
                    window.location.href = "index.php?action=fichePrevisionnelle";
                }
            });
        });
    </script>


</body>
</html>
