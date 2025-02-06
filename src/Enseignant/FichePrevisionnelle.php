<?php
// Active l'affichage des erreurs (pour le développement)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../modele/CoursDTO.php';
require_once __DIR__ . '/../modele/EnseignantDTO.php';
require_once __DIR__ . '/../modele/VoeuDTO.php';
require_once __DIR__ . '/../modele/VoeuHorsIUTDTO.php';
use src\Db\connexionFactory;

$coursDTO         = new CoursDTO();
$enseignantDTO    = new EnseignantDTO();
$voeuDTO         = new VoeuDTO();
$voeuHorsIUTDTO   = new VoeuHorsIUTDTO();

$errors = [];
$successMessage = '';

$userId = $_SESSION['user_id'] ?? null;
if (!$userId) {
    die('Utilisateur non authentifié.');
}

$conn = connexionFactory::makeConnection();
$stmt = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = :userId");
$stmt->bindValue(':userId', $userId, PDO::PARAM_INT);
$stmt->execute();
$enseignantData = $stmt->fetch(PDO::FETCH_ASSOC);

$nomPrenom = $enseignantData ? $enseignantData['nom'] . ' ' . $enseignantData['prenom'] : "Nom Inexistant";

$enseignant = $enseignantDTO->findByUtilisateurId($userId);
$idEnseignant = $enseignant ? $enseignant->getIdEnseignant() : null;

$coursList = $coursDTO->findAll();

$existingVoeux = $idEnseignant ? $voeuDTO->findByEnseignant($idEnseignant) : [];
$existingVoeuxHorsIUT = $idEnseignant ? $voeuHorsIUTDTO->findByEnseignant($idEnseignant) : [];

$voeuxSeptembre = [];
$voeuxJanvier   = [];
$semestresSeptembre = ['1', '3', '5'];
$semestresJanvier   = ['2', '4', '6'];

foreach ($existingVoeux as $voeu) {
    if (in_array($voeu->getSemestre(), $semestresSeptembre)) {
        $voeuxSeptembre[] = $voeu;
    } else {
        $voeuxJanvier[] = $voeu;
    }
}
// Préparer les données pour le PDF
$_SESSION['pdf_data'] = [
  'enseignant' => 'Nom Prenom Exemple', // Vous pouvez adapter cette valeur
  'voeux_septembre' => array_map(function($v) use ($coursDTO) {
      $cours = $coursDTO->findById($v->getIdCours());
      // Calculer le total comme somme de nb_CM, nb_TD, nb_TP et nb_EI
      $total = $v->getNbCM() + $v->getNbTD() + $v->getNbTP() + $v->getNbEI();
      return [
          'ressource' => $cours ? $cours->getNomCours() : 'Inconnu',
          'semestre'  => $v->getSemestre(),
          'cm'        => $v->getNbCM(),
          'td'        => $v->getNbTD(),
          'tp'        => $v->getNbTP(),
          'ei'        => $v->getNbEI(),
          'total'     => $total,
      ];
  }, $voeuxSeptembre),
  'voeux_janvier' => array_map(function($v) use ($coursDTO) {
      $cours = $coursDTO->findById($v->getIdCours());
      $total = $v->getNbCM() + $v->getNbTD() + $v->getNbTP() + $v->getNbEI();
      return [
          'ressource' => $cours ? $cours->getNomCours() : 'Inconnu',
          'semestre'  => $v->getSemestre(),
          'cm'        => $v->getNbCM(),
          'td'        => $v->getNbTD(),
          'tp'        => $v->getNbTP(),
          'ei'        => $v->getNbEI(),
          'total'     => $total,
      ];
  }, $voeuxJanvier),
  // Pour les voeux hors IUT, nous utilisons déjà les accesseurs appropriés
  'voeux_hors_iut' => array_map(function($v) {
      return [
          'composant' => $v->getComposant(),
          'formation' => $v->getFormation(),
          'module'    => $v->getModule(),
          'cm'        => $v->getNbHeuresCM(),
          'td'        => $v->getNbHeuresTD(),
          'tp'        => $v->getNbHeuresTP(),
          'ei'        => $v->getNbHeuresEI(),
          'total'     => $v->getNbHeuresTotal(),
      ];
  }, $existingVoeuxHorsIUT)
];

// On s'assure qu'il y ait au moins une ligne pour chaque période
$septembreCount = isset($_POST['septembre_count']) ? intval($_POST['septembre_count']) : max(1, count($voeuxSeptembre));
$janvierCount   = isset($_POST['janvier_count'])   ? intval($_POST['janvier_count'])   : max(1, count($voeuxJanvier));
$horsIUTCount   = max(1, count($existingVoeuxHorsIUT));

$postData = $_POST;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Initialisation de la structure pour SEPTEMBRE et JANVIER
    // La valeur du select est le nom du cours (pour que findByName() fonctionne)
    $postData['septembre'] = [
        'ressource'  => [],
        'remarques'  => [],
        'formation'  => [],
        'semestre'   => [],
        'cm'         => [],
        'td'         => [],
        'tp'         => [],
        'ei'         => []
    ];
    foreach ($voeuxSeptembre as $v) {
        $cours = $coursDTO->findById($v->getIdCours());
        $postData['septembre']['ressource'][] = $cours ? $cours->getNomCours() : '';
        $postData['septembre']['remarques'][]  = $v->getRemarque();
        $postData['septembre']['formation'][]  = ''; // Non renseigné ici
        $postData['septembre']['semestre'][]   = $v->getSemestre();
        $postData['septembre']['cm'][]         = $v->getNbCM();
        $postData['septembre']['td'][]         = $v->getNbTD();
        $postData['septembre']['tp'][]         = $v->getNbTP();
        $postData['septembre']['ei'][]         = $v->getNbEI();
    }
    
    $postData['janvier'] = [
        'ressource'  => [],
        'remarques'  => [],
        'formation'  => [],
        'semestre'   => [],
        'cm'         => [],
        'td'         => [],
        'tp'         => [],
        'ei'         => []
    ];
    foreach ($voeuxJanvier as $v) {
        $cours = $coursDTO->findById($v->getIdCours());
        $postData['janvier']['ressource'][] = $cours ? $cours->getNomCours() : '';
        $postData['janvier']['remarques'][]  = $v->getRemarque();
        $postData['janvier']['formation'][]  = '';
        $postData['janvier']['semestre'][]   = $v->getSemestre();
        $postData['janvier']['cm'][]         = $v->getNbCM();
        $postData['janvier']['td'][]         = $v->getNbTD();
        $postData['janvier']['tp'][]         = $v->getNbTP();
        $postData['janvier']['ei'][]         = $v->getNbEI();
    }
    
    // Initialisation de la partie "hors IUT"
    $postData['hors_iut'] = [
        'composant' => [],
        'formation' => [],
        'module'    => [],
        'cm'        => [],
        'td'        => [],
        'tp'        => [],
        'ei'        => [],
        'total'     => [] // Optionnel pour un total affiché dans le tableau hors IUT
    ];
    foreach ($existingVoeuxHorsIUT as $v) {
        $postData['hors_iut']['composant'][] = htmlspecialchars($v->getComposant());
        $postData['hors_iut']['formation'][] = htmlspecialchars($v->getFormation());
        $postData['hors_iut']['module'][]    = htmlspecialchars($v->getModule());
        $postData['hors_iut']['cm'][]        = $v->getNbHeuresCM();
        $postData['hors_iut']['td'][]        = $v->getNbHeuresTD();
        $postData['hors_iut']['tp'][]        = $v->getNbHeuresTP();
        $postData['hors_iut']['ei'][]        = $v->getNbHeuresEI();
        $postData['hors_iut']['total'][]     = $v->getNbHeuresTotal();
    }
}

// Traitement du formulaire en POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['envoyer'])) {
        if (isset($_POST['hors_iut'])) {
            $hors_iut = $_POST['hors_iut'];
            foreach ($hors_iut['composant'] as $i => $composant) {
                $composant = trim($composant);
                $formation = trim($hors_iut['formation'][$i] ?? '');
                $module = trim($hors_iut['module'][$i] ?? '');
                $cm = $hors_iut['cm'][$i] ?? '';
                $td = $hors_iut['td'][$i] ?? '';
                $tp = $hors_iut['tp'][$i] ?? '';
                $ei = $hors_iut['ei'][$i] ?? '';
                
                if ($composant === '' || $formation === '' || $module === '') {
                    $errors[] = "La ligne " . ($i + 1) . " de 'hors_iut' doit avoir les champs Composant, Formation et Module remplis.";
                }
                if (!is_numeric($cm) || !is_numeric($td) || !is_numeric($tp) || !is_numeric($ei)) {
                    $errors[] = "La ligne " . ($i + 1) . " de 'hors_iut' doit avoir les champs CM, TD, TP et EI numériques.";
                }
            }
        }
        
        if (empty($errors)) {
            // Suppression des voeux existants pour cet enseignant
            if ($idEnseignant !== null) {
                $voeuDTO->deleteByEnseignant($idEnseignant);
                $voeuHorsIUTDTO->deleteByEnseignant($idEnseignant);
            }
            
            // Enregistrement des voeux pour la période SEPTEMBRE
            if (isset($_POST['septembre'])) {
                $septData = $_POST['septembre'];
                foreach ($septData['ressource'] as $i => $nomCours) {
                    if (!empty($nomCours)) {
                        $coursFound = $coursDTO->findByName($nomCours);
                        if (!empty($coursFound)) {
                            $cours = $coursFound[0];
                            $idCours = $cours->getIdCours();
                            $remarque = $septData['remarques'][$i] ?? '';
                            $semestre = $septData['semestre'][$i] ?? '';
                            // Si l'utilisateur n'a pas modifié la valeur, on prend la valeur par défaut du cours
                            $nbCM = (isset($septData['cm'][$i]) && $septData['cm'][$i] !== '') ? (float)$septData['cm'][$i] : $cours->getNbHeuresCM();
                            $nbTD = (isset($septData['td'][$i]) && $septData['td'][$i] !== '') ? (float)$septData['td'][$i] : $cours->getNbHeuresTD();
                            $nbTP = (isset($septData['tp'][$i]) && $septData['tp'][$i] !== '') ? (float)$septData['tp'][$i] : $cours->getNbHeuresTP();
                            $nbEI = (isset($septData['ei'][$i]) && $septData['ei'][$i] !== '') ? (float)$septData['ei'][$i] : $cours->getNbHeuresEI();
                            
                            $voeu = new Voeu(null, $idEnseignant, $idCours, $remarque, $semestre, $nbCM, $nbTD, $nbTP, $nbEI);
                            $voeuDTO->save($voeu);
                        }
                    }
                }
            }
            
            // Enregistrement des voeux pour la période JANVIER
            if (isset($_POST['janvier'])) {
                $janData = $_POST['janvier'];
                foreach ($janData['ressource'] as $i => $nomCours) {
                    if (!empty($nomCours)) {
                        $coursFound = $coursDTO->findByName($nomCours);
                        if (!empty($coursFound)) {
                            $cours = $coursFound[0];
                            $idCours = $cours->getIdCours();
                            $remarque = $janData['remarques'][$i] ?? '';
                            $semestre = $janData['semestre'][$i] ?? '';
                            $nbCM = (isset($janData['cm'][$i]) && $janData['cm'][$i] !== '') ? (float)$janData['cm'][$i] : $cours->getNbHeuresCM();
                            $nbTD = (isset($janData['td'][$i]) && $janData['td'][$i] !== '') ? (float)$janData['td'][$i] : $cours->getNbHeuresTD();
                            $nbTP = (isset($janData['tp'][$i]) && $janData['tp'][$i] !== '') ? (float)$janData['tp'][$i] : $cours->getNbHeuresTP();
                            $nbEI = (isset($janData['ei'][$i]) && $janData['ei'][$i] !== '') ? (float)$janData['ei'][$i] : $cours->getNbHeuresEI();
                            
                            $voeu = new Voeu(null, $idEnseignant, $idCours, $remarque, $semestre, $nbCM, $nbTD, $nbTP, $nbEI);
                            $voeuDTO->save($voeu);
                        }
                    }
                }
            }
            
            // Enregistrement des voeux hors IUT
            if (isset($_POST['hors_iut'])) {
                $hors_iut = $_POST['hors_iut'];
                foreach ($hors_iut['composant'] as $i => $composant) {
                    $composant = trim($composant);
                    $formation = trim($hors_iut['formation'][$i] ?? '');
                    $module = trim($hors_iut['module'][$i] ?? '');
                    $cm = isset($hors_iut['cm'][$i]) ? floatval($hors_iut['cm'][$i]) : 0;
                    $td = isset($hors_iut['td'][$i]) ? floatval($hors_iut['td'][$i]) : 0;
                    $tp = isset($hors_iut['tp'][$i]) ? floatval($hors_iut['tp'][$i]) : 0;
                    $ei = isset($hors_iut['ei'][$i]) ? floatval($hors_iut['ei'][$i]) : 0;
                    $total = isset($hors_iut['total'][$i]) ? floatval($hors_iut['total'][$i]) : 0;
                    
                    if ($composant !== '' && $formation !== '' && $module !== '') {
                        $voeuHI = new VoeuHorsIUT(null, $idEnseignant, $composant, $formation, $module, $cm, $td, $tp, $ei, $total);
                        $voeuHorsIUTDTO->save($voeuHI);
                    }
                }
            }
            
            $successMessage = "Les voeux ont été enregistrés avec succès.";
        }
    }
}

/**
 * Génère les lignes d'un tableau pour la période donnée (septembre ou janvier)
 * Si un voeu existe en base, les champs CM, TD, TP et EI proviennent du voeu,
 * sinon ils prennent les valeurs par défaut du cours (trouvées via la liste $coursList)
 */
function generateTableRows(string $type, array $coursList, int $count, array $postData): void {
    $allowedSemesters = $type === 'septembre' ? ['1', '3', '5'] : ['2', '4', '6'];
    $data       = $postData[$type] ?? [];
    $ressources = $data['ressource'] ?? [];
    $remarques  = $data['remarques'] ?? [];
    $formations = $data['formation'] ?? [];
    $semestres  = $data['semestre'] ?? [];
    $cms        = $data['cm'] ?? [];
    $tds        = $data['td'] ?? [];
    $tps        = $data['tp'] ?? [];
    $eis        = $data['ei'] ?? [];
    
    for ($i = 0; $i < $count; $i++) {
        $selectedCours = $ressources[$i] ?? '';
        $remarque = htmlspecialchars($remarques[$i] ?? '');
        // Recherche dans la liste des cours l'objet correspondant pour récupérer les valeurs par défaut
        $defaultCM = $defaultTD = $defaultTP = $defaultEI = '';
        if (!empty($selectedCours)) {
            foreach ($coursList as $cours) {
                if ($cours->getNomCours() === $selectedCours) {
                    $defaultCM = $cours->getNbHeuresCM();
                    $defaultTD = $cours->getNbHeuresTD();
                    $defaultTP = $cours->getNbHeuresTP();
                    $defaultEI = $cours->getNbHeuresEI();
                    break;
                }
            }
        }
        // Si le voeu existe (les valeurs sont dans $cms, etc.), on les utilise ; sinon, on prend les valeurs par défaut du cours.
        $valCM = (isset($cms[$i]) && $cms[$i] !== '') ? $cms[$i] : $defaultCM;
        $valTD = (isset($tds[$i]) && $tds[$i] !== '') ? $tds[$i] : $defaultTD;
        $valTP = (isset($tps[$i]) && $tps[$i] !== '') ? $tps[$i] : $defaultTP;
        $valEI = (isset($eis[$i]) && $eis[$i] !== '') ? $eis[$i] : $defaultEI;
        
        echo '<tr>';
            echo '<td><input type="text" name="' . $type . '[formation][]" value="' . htmlspecialchars($formations[$i] ?? '') . '" readonly></td>';
            echo '<td><select name="' . $type . '[ressource][]">';
                echo '<option value="">-- Sélectionner un cours --</option>';
                foreach ($coursList as $cours) {
                    if (in_array($cours->getSemestre(), $allowedSemesters)) {
                        $selected = ($cours->getNomCours() === $selectedCours) ? 'selected' : '';
                        $optionDisplay = $cours->getCodeCours() . ' - ' . $cours->getNomCours();
                        echo '<option value="' . htmlspecialchars($cours->getNomCours()) . '" ' . $selected . '>' . htmlspecialchars($optionDisplay) . '</option>';
                    }
                }
            echo '</select></td>';
            echo '<td><input type="text" name="' . $type . '[semestre][]" value="' . htmlspecialchars($semestres[$i] ?? '') . '" readonly></td>';
            echo '<td><input type="number" name="' . $type . '[cm][]" value="' . htmlspecialchars($valCM) . '"></td>';
            echo '<td><input type="number" name="' . $type . '[td][]" value="' . htmlspecialchars($valTD) . '"></td>';
            echo '<td><input type="number" name="' . $type . '[tp][]" value="' . htmlspecialchars($valTP) . '"></td>';
            echo '<td><input type="number" name="' . $type . '[ei][]" value="' . htmlspecialchars($valEI) . '"></td>';
            echo '<td><input type="text" name="' . $type . '[remarques][]" value="' . $remarque . '"></td>';
            echo '<td><button type="button" class="btn btn-danger btn-sm remove-line">&times;</button></td>';
        echo '</tr>';
    }
}

/**
 * Génère les lignes du tableau "hors IUT" à partir des données existantes
 */
function generateHorsIUTRows(array $horsIUTData): void {
    $composants = $horsIUTData['composant'] ?? [];
    $formations = $horsIUTData['formation'] ?? [];
    $modules    = $horsIUTData['module'] ?? [];
    $cms        = $horsIUTData['cm'] ?? [];
    $tds        = $horsIUTData['td'] ?? [];
    $tps        = $horsIUTData['tp'] ?? [];
    $eis        = $horsIUTData['ei'] ?? [];
    $totals     = $horsIUTData['total'] ?? [];
    
    for ($i = 0; $i < count($composants); $i++) {
        echo '<tr>';
        echo '<td><input type="text" name="hors_iut[composant][]" value="' . htmlspecialchars($composants[$i] ?? '') . '"></td>';
        echo '<td><input type="text" name="hors_iut[formation][]" value="' . htmlspecialchars($formations[$i] ?? '') . '"></td>';
        echo '<td><input type="text" name="hors_iut[module][]" value="' . htmlspecialchars($modules[$i] ?? '') . '"></td>';
        echo '<td><input type="number" name="hors_iut[cm][]" value="' . htmlspecialchars($cms[$i] ?? '') . '"></td>';
        echo '<td><input type="number" name="hors_iut[td][]" value="' . htmlspecialchars($tds[$i] ?? '') . '"></td>';
        echo '<td><input type="number" name="hors_iut[tp][]" value="' . htmlspecialchars($tps[$i] ?? '') . '"></td>';
        echo '<td><input type="number" name="hors_iut[ei][]" value="' . htmlspecialchars($eis[$i] ?? '') . '"></td>';
        echo '<td><input type="number" name="hors_iut[total][]" value="' . htmlspecialchars($totals[$i] ?? '') . '" readonly></td>';
        $hetd = '';
        if(isset($totals[$i]) && is_numeric($totals[$i])){
            $hetd = number_format($totals[$i] * 1.5, 1);
        }
        echo '<td><input type="number" name="hors_iut[hetd][]" value="' . htmlspecialchars($hetd) . '" readonly></td>';
        echo '<td><button type="button" class="btn btn-danger btn-sm remove-line">&times;</button></td>';
        echo '</tr>';
    }
}
?>

<style>
    /**********************************************************/
    /*           FICHE PREVISIONNELLE DE SERVICE             */
    /**********************************************************/

    /* ================== TITRE ET CONTENEUR ================== */
    .container {
        /* Le conteneur a déjà Bootstrap .mt-5 -> marge top */
        /* On peut ajouter un background si on veut,
           mais souvent on laisse body ou main-content s'en charger */
    }

    .container h2 {
        text-transform: uppercase;
        font-weight: 600;
        color: #000;              /* noir */
        margin-bottom: 1em;
    }

    /* ================== ALERTES (jaune, style UL) ================== */
    /* Overwrite la classe bootstrap alert-warning */
    .alert.alert-warning {
        background-color: #FFEF65; /* Jaune clair */
        color: #000;               /* texte noir */
        font-weight: 600;
        border: none;              /* pas de bordure grise */
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.1);
    }

    /**********************************************************/
    /*                TABLES & TABLE HEADERS                 */
    /**********************************************************/
    .table {
        border-color: #ddd;       /* Couleur de la bordure */
        margin-bottom: 2em;
    }

    /* En-têtes en fond noir, texte blanc */
    .table thead,
    .table thead tr.thead-light {
        background-color: #000 !important; /* Noir */
        color: #fff !important;           /* texte blanc */
    }

    .table thead th {
        border-color: #000;       /* séparer proprement */
    }

    /* Lignes totales .total-row => fond gris clair */
    .total-row {
        background-color: #f9f9f9;
        font-weight: 600;
    }

    /* Overwrite la couleur si on survole */
    .table tbody tr:hover {
        background-color: #FFE74A; /* Survol en jaune plus soutenu */
        cursor: pointer; /* juste pour le style, si tu veux */
    }

    /**********************************************************/
    /*        INPUTS ET SELECT DANS LE TABLEAU               */
    /**********************************************************/
    /* Code que tu donnais déjà, pour un style épuré */
    .table input,
    .table select {
        border: none !important;
        box-shadow: none !important;
        background-color: transparent;
        width: 100%;
    }

    /* Au focus: pas de contour, ou un soulignement ? */
    .table input:focus,
    .table select:focus {
        outline: none;
    }

    /* Inputs en readonly => texte grisé */
    .table input[readonly] {
        color: #6c757d;
    }

    /**********************************************************/
    /*             BOUTONS (AJOUTER, ENVOYER ...)            */
    /**********************************************************/
    /* .btn-success => on la recolore en Jaune UL */
    .btn.btn-success {
        background-color: #FFEF65; /* Jaune clair */
        color: #000;              /* texte noir */
        border: none;
        font-weight: 600;
        transition: 0.3s ease;
    }

    .btn.btn-success:hover {
        background-color: #FFE74A; /* plus soutenu */
        transform: scale(1.02);
    }
    .btn.btn-success:active,
    .btn.btn-success:focus {
        background-color: #FFD400;
        outline: none;
        transform: scale(0.98);
    }

    /* .btn.btn-primary => idem, si "Envoyer" est en "btn-primary" */
    .btn.btn-primary {
        background-color: #FFEF65;
        color: #000;
        border: none;
        font-weight: 600;
        transition: 0.3s;
    }
    .btn.btn-primary:hover {
        background-color: #FFE74A;
        transform: scale(1.02);
    }
    .btn.btn-primary:focus,
    .btn.btn-primary:active {
        background-color: #FFD400;
        transform: scale(0.98);
        outline: none;
        border: none;
    }

</style>

<div id="main-content">
  <div class="container mt-5">
    <h2 class="text-center mb-4">Fiche Prévisionnelle de Service</h2>
    <p><strong>IUT Nancy-Charlemagne - Département Informatique</strong></p>

    <?php if (!empty($successMessage)): ?>
      <div class="alert alert-success">
        <?= htmlspecialchars($successMessage) ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($errors)): ?>
      <div class="alert alert-danger">
        <?php foreach ($errors as $error): ?>
          <p><?= htmlspecialchars($error) ?></p>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>

    <form action="" method="post">
      <input type="hidden" name="septembre_count" value="<?= $septembreCount ?>">
      <input type="hidden" name="janvier_count" value="<?= $janvierCount ?>">

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
              <th>Remarques</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php generateTableRows('septembre', $coursList, $septembreCount, $postData); ?>
            <tr class="total-row">
              <td colspan="3" class="font-weight-bold">Total :</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td colspan="2"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="text-center mb-4">
        <button type="button" id="add_line_septembre" class="btn btn-success">Ajouter une ligne (Septembre)</button>
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
              <th>Remarques</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php generateTableRows('janvier', $coursList, $janvierCount, $postData); ?>
            <tr class="total-row">
              <td colspan="3" class="font-weight-bold">Total :</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td>0</td>
              <td colspan="2"></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="text-center mb-4">
        <button type="button" id="add_line_janvier" class="btn btn-success">Ajouter une ligne (Janvier)</button>
      </div>

      <div class="alert alert-warning font-weight-bold">TOTAL :</div>
      <!-- La table Dept Info affiche la somme des valeurs de CM, TD, TP et EI
           issues des tableaux SEPTEMBRE, JANVIER et VOEUX HORS IUT -->
      <div class="table-responsive">
        <table class="table table-bordered text-center w-100" id="table-dept-info">
          <thead>
            <tr>
              <th>CM</th>
              <th>TD</th>
              <th>TP</th>
              <th>EI</th>
            </tr>
          </thead>
          <tbody>
            <tr>
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
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php 
              if (isset($postData['hors_iut'])) {
                  generateHorsIUTRows($postData['hors_iut']);
              }
            ?>
          </tbody>
        </table>
      </div>
      <div class="text-center mb-4">
        <button type="button" id="add_line_hors_info" class="btn btn-success">Ajouter une ligne hors IUT</button>
      </div>

      <div class="text-center mt-4">
        <button type="submit" name="envoyer" class="btn btn-primary">Envoyer</button>
      </div>
    </form>
  </div>

  <!-- Templates pour l'ajout dynamique de lignes -->
  <template id="template-septembre">
    <tr>
      <td><input type="text" name="septembre[formation][]" readonly></td>
      <td>
        <select name="septembre[ressource][]">
          <option value="">-- Sélectionner un cours --</option>
        </select>
      </td>
      <td><input type="text" name="septembre[semestre][]" readonly></td>
      <!-- Les champs CM, TD, TP et EI sont éditables -->
      <td><input type="number" name="septembre[cm][]"></td>
      <td><input type="number" name="septembre[td][]"></td>
      <td><input type="number" name="septembre[tp][]"></td>
      <td><input type="number" name="septembre[ei][]"></td>
      <td><input type="text" name="septembre[remarques][]"></td>
      <td><button type="button" class="btn btn-danger btn-sm remove-line">&times;</button></td>
    </tr>
  </template>

  <template id="template-janvier">
    <tr>
      <td><input type="text" name="janvier[formation][]" readonly></td>
      <td>
        <select name="janvier[ressource][]">
          <option value="">-- Sélectionner un cours --</option>
        </select>
      </td>
      <td><input type="text" name="janvier[semestre][]" readonly></td>
      <!-- Les champs CM, TD, TP et EI sont éditables -->
      <td><input type="number" name="janvier[cm][]"></td>
      <td><input type="number" name="janvier[td][]"></td>
      <td><input type="number" name="janvier[tp][]"></td>
      <td><input type="number" name="janvier[ei][]"></td>
      <td><input type="text" name="janvier[remarques][]"></td>
      <td><button type="button" class="btn btn-danger btn-sm remove-line">&times;</button></td>
    </tr>
  </template>
</div>


<script>
    // Injection des données des cours depuis PHP.
    // La valeur du select sera le nom du cours et l'affichage sera "CODE - NOM"
    window.coursData = <?= json_encode(array_map(function($c) {
      return [
        'nomCours'  => $c->getNomCours(),
        'formation' => $c->getFormation(),
        'semestre'  => $c->getSemestre(),
        'cm'        => $c->getNbHeuresCM(),
        'td'        => $c->getNbHeuresTD(),
        'tp'        => $c->getNbHeuresTP(),
        'ei'        => $c->getNbHeuresEI(),
        'total'     => $c->getNbHeuresTotal(),
        'codeCours' => $c->getCodeCours()
      ];
    }, $coursList)); ?>;
    
    document.addEventListener('DOMContentLoaded', function () {

      // --- Mise à jour d'une ligne lors du changement du select ---
      function updateLine(selectElem) {
        var tr = selectElem.closest('tr');
        var nomCours = selectElem.value;
        var coursInfo = window.coursData.find(function(c) {
          return c.nomCours === nomCours;
        });
        var formationInput = tr.querySelector('td:nth-child(1) input');
        var semestreInput  = tr.querySelector('td:nth-child(3) input');
        var numberInputs   = tr.querySelectorAll('input[type="number"]');
        
        if (coursInfo) {
          formationInput.value = coursInfo.formation;
          semestreInput.value  = coursInfo.semestre;
          // Si l'utilisateur n'a pas déjà saisi de valeurs, on remplit par défaut avec les valeurs du cours
          if (!numberInputs[0].value) numberInputs[0].value = coursInfo.cm;
          if (!numberInputs[1].value) numberInputs[1].value = coursInfo.td;
          if (!numberInputs[2].value) numberInputs[2].value = coursInfo.tp;
          if (!numberInputs[3].value) numberInputs[3].value = coursInfo.ei;
        } else {
          formationInput.value = '';
          semestreInput.value  = '';
          numberInputs.forEach(function(input) { input.value = ''; });
        }
        updateTotals(selectElem.closest('table').id);
        updateDeptInfoTotals();
      }
      
      // --- Peupler les options du select en fonction du type ---
      function populateCoursOptions(selectElem, type) {
        var allowedSemesters = type === 'septembre' ? ['1','3','5'] : ['2','4','6'];
        selectElem.innerHTML = '<option value="">-- Sélectionner un cours --</option>';
        window.coursData.forEach(function(cour) {
          if (allowedSemesters.includes(cour.semestre)) {
            var option = document.createElement('option');
            option.value = cour.nomCours;
            option.text = cour.codeCours + ' - ' + cour.nomCours;
            selectElem.appendChild(option);
          }
        });
      }
      
      // --- Ajout d'une nouvelle ligne dans le tableau d'un type donné ---
      function addLine(type) {
        var template = document.getElementById('template-' + type);
        if (!template) return;
        var newRow = template.content.cloneNode(true);
        var selectElem = newRow.querySelector('select');
        populateCoursOptions(selectElem, type);
        selectElem.addEventListener('change', function() {
          updateLine(this);
        });
        newRow.querySelector('.remove-line').addEventListener('click', function() {
          this.closest('tr').remove();
          updateTotals('table-' + type);
          updateDeptInfoTotals();
        });
        // Insertion avant la ligne de total
        var tbody = document.getElementById('table-' + type).querySelector('tbody');
        var totalRow = tbody.querySelector('tr.total-row');
        tbody.insertBefore(newRow, totalRow);
      }
      
      // --- Calcul des totaux pour les voeux (septembre/janvier) ---
      function updateTotals(tableId) {
        var table = document.getElementById(tableId);
        if (!table) return;
        var rows = table.querySelectorAll('tbody tr');
        var totalRow = table.querySelector('tr.total-row');
        if (!totalRow) return;
        
        var cmSum = 0, tdSum = 0, tpSum = 0, eiSum = 0;
        
        rows.forEach(function(row) {
          if (row.classList.contains('total-row')) return;
          var cells = row.querySelectorAll('td');
          var cm = parseFloat((cells[3].querySelector('input') || {}).value) || 0;
          var td = parseFloat((cells[4].querySelector('input') || {}).value) || 0;
          var tp = parseFloat((cells[5].querySelector('input') || {}).value) || 0;
          var ei = parseFloat((cells[6].querySelector('input') || {}).value) || 0;
          
          cmSum += cm;
          tdSum += td;
          tpSum += tp;
          eiSum += ei;
        });
        
        var totalCells = totalRow.querySelectorAll('td');
        // Le premier <td> a colspan=3 avec le label "Total :"
        totalCells[1].textContent = cmSum;
        totalCells[2].textContent = tdSum;
        totalCells[3].textContent = tpSum;
        totalCells[4].textContent = eiSum;
      }
      
      // --- Calcul des totaux globaux (Dept Info) incluant les voeux hors IUT ---
      function updateDeptInfoTotals() {
        var septTable = document.getElementById('table-septembre');
        var janTable  = document.getElementById('table-janvier');
        var horsIutTable = document.getElementById('table_hors_iut');
        var deptTable = document.getElementById('table-dept-info');
        if (!septTable || !janTable || !horsIutTable || !deptTable) return;
        
        // Totaux du tableau SEPTEMBRE
        var septTotalRow = septTable.querySelector('tr.total-row');
        var septCM = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[1].textContent) || 0) : 0;
        var septTD = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[2].textContent) || 0) : 0;
        var septTP = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[3].textContent) || 0) : 0;
        var septEI = septTotalRow ? (parseFloat(septTotalRow.querySelectorAll('td')[4].textContent) || 0) : 0;
        
        // Totaux du tableau JANVIER
        var janTotalRow = janTable.querySelector('tr.total-row');
        var janCM = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[1].textContent) || 0) : 0;
        var janTD = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[2].textContent) || 0) : 0;
        var janTP = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[3].textContent) || 0) : 0;
        var janEI = janTotalRow ? (parseFloat(janTotalRow.querySelectorAll('td')[4].textContent) || 0) : 0;
        
        // Totaux du tableau VOEUX HORS IUT
        var horsIutRows = horsIutTable.querySelectorAll('tbody tr');
        var horsCM = 0, horsTD = 0, horsTP = 0, horsEI = 0;
        horsIutRows.forEach(function(row) {
          var cells = row.querySelectorAll('td');
          // Colonnes : 0: composant, 1: formation, 2: module, 3: CM, 4: TD, 5: TP, 6: EI, etc.
          horsCM += parseFloat((cells[3].querySelector('input') || {}).value) || 0;
          horsTD += parseFloat((cells[4].querySelector('input') || {}).value) || 0;
          horsTP += parseFloat((cells[5].querySelector('input') || {}).value) || 0;
          horsEI += parseFloat((cells[6].querySelector('input') || {}).value) || 0;
        });
        
        // Somme globale
        var deptCM = septCM + janCM + horsCM;
        var deptTD = septTD + janTD + horsTD;
        var deptTP = septTP + janTP + horsTP;
        var deptEI = septEI + janEI + horsEI;
        
        var deptRow = deptTable.querySelector('tbody tr');
        var deptCells = deptRow.querySelectorAll('td');
        deptCells[0].textContent = deptCM;
        deptCells[1].textContent = deptTD;
        deptCells[2].textContent = deptTP;
        deptCells[3].textContent = deptEI;
      }
      
      // Attacher les événements aux boutons d'ajout pour "septembre" et "janvier"
      document.getElementById('add_line_septembre').addEventListener('click', function() {
        addLine('septembre');
      });
      document.getElementById('add_line_janvier').addEventListener('click', function() {
        addLine('janvier');
      });
      
      // Ajout dynamique de lignes pour "hors IUT"
      document.getElementById('add_line_hors_info').addEventListener('click', function() {
        var tableBody = document.getElementById('table_hors_iut').querySelector('tbody');
        var newRow = tableBody.insertRow();
        
        var cellNames = ['composant', 'formation', 'module', 'cm', 'td', 'tp', 'ei', 'total', 'hetd'];
        cellNames.forEach(function(name) {
          var cell = newRow.insertCell();
          var input = document.createElement('input');
          if (['cm', 'td', 'tp', 'ei'].includes(name)) {
            input.type = 'number';
            input.min  = '0';
            input.step = '0.1';
            input.required = true;
            input.addEventListener('input', calculateHorsIUTTotal);
          } else if (['total', 'hetd'].includes(name)) {
            input.type = 'number';
            input.readOnly = true;
          } else {
            input.type = 'text';
            input.required = true;
          }
          input.name = 'hors_iut[' + name + '][]';
          cell.appendChild(input);
        });
        var actionCell = newRow.insertCell();
        var deleteBtn = document.createElement('button');
        deleteBtn.type = 'button';
        deleteBtn.className = 'btn btn-danger btn-sm remove-line';
        deleteBtn.innerHTML = '&times;';
        deleteBtn.addEventListener('click', function() {
          newRow.remove();
          updateTotals('table_hors_iut');
          updateDeptInfoTotals();
        });
        actionCell.appendChild(deleteBtn);
      });
      
      function calculateHorsIUTTotal() {
        var row = this.closest('tr');
        var cm   = parseFloat(row.querySelector('input[name="hors_iut[cm][]"]').value) || 0;
        var td   = parseFloat(row.querySelector('input[name="hors_iut[td][]"]').value) || 0;
        var tp   = parseFloat(row.querySelector('input[name="hors_iut[tp][]"]').value) || 0;
        var ei   = parseFloat(row.querySelector('input[name="hors_iut[ei][]"]').value) || 0;
        var total = cm + td + tp + ei;
        var hetd  = total * 1.5;
        row.querySelector('input[name="hors_iut[total][]"]').value = total.toFixed(1);
        row.querySelector('input[name="hors_iut[hetd][]"]').value = hetd.toFixed(1);
        updateTotals('table_hors_iut');
        updateDeptInfoTotals();
      }
      
      // Pour les lignes générées côté serveur, attacher l'événement change
      document.querySelectorAll('table select').forEach(function(selectElem) {
        selectElem.addEventListener('change', function() { updateLine(this); });
        if (selectElem.value !== "") { updateLine(selectElem); }
      });
      
      // Écouteur pour la suppression des lignes générées côté serveur
      document.addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-line')) {
          var row = e.target.closest('tr');
          if (row) {
            row.remove();
            updateTotals(row.closest('table').id);
            updateDeptInfoTotals();
          }
        }
      });
      
    });
  </script>
  <script>
        // document.addEventListener("DOMContentLoaded", function () {
        //     document.querySelector("button[name='envoyer']").addEventListener("click", function (event) {
        //         event.preventDefault();
        //         if (confirm("Les vœux ont été enregistrés avec succès.\nVoulez-vous télécharger le PDF ?")) {
        //             const form = this.closest("form");
        //             form.action = "src/User/ServicePdf.php";
        //             form.submit();
        //         } else {
        //             window.location.href = "index.php?action=fichePrevisionnelle";
        //         }
        //     });
        // });
    </script>