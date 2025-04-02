<?php

// Active l'affichage des erreurs (pour le développement)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Démarrer la session si nécessaire
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Inclusion des fichiers de classes
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
      $total = $v->getNbCM() + $v->getNbTD() + $v->getNbTP() + $v->getNbEI();
      return [
          'ressource' => $cours ? $cours->getNomCours() : 'Inconnu',
          'semestre'  => $v->getSemestre(),
          'cm'        => $v->getNbCM(),
          'td'        => $v->getNbTD(),
          'tp'        => $v->getNbTP(),
          'ei'        => $v->getNbEI(),
          'remarque'  => $v->getRemarque(),
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
          'remarque'  => $v->getRemarque(),
          'total'     => $total,
      ];
  }, $voeuxJanvier),
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

$septembreCount = isset($_POST['septembre_count']) ? intval($_POST['septembre_count']) : max(1, count($voeuxSeptembre));
$janvierCount   = isset($_POST['janvier_count'])   ? intval($_POST['janvier_count'])   : max(1, count($voeuxJanvier));
$horsIUTCount   = max(1, count($existingVoeuxHorsIUT));

$postData = $_POST;
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    // Initialisation des tableaux pour SEPTEMBRE et JANVIER
    $postData['septembre'] = [
        'id'         => [],
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
        $postData['septembre']['id'][] = $v->getIdVoeu();
        $postData['septembre']['ressource'][] = $cours ? $cours->getNomCours() : '';
        $postData['septembre']['remarques'][]  = $v->getRemarque();
        $postData['septembre']['formation'][]  = '';
        $postData['septembre']['semestre'][]   = $v->getSemestre();
        $postData['septembre']['cm'][]         = $v->getNbCM();
        $postData['septembre']['td'][]         = $v->getNbTD();
        $postData['septembre']['tp'][]         = $v->getNbTP();
        $postData['septembre']['ei'][]         = $v->getNbEI();
    }
    
    $postData['janvier'] = [
        'id'         => [],
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
        $postData['janvier']['id'][] = $v->getIdVoeu();
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
        'total'     => []
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
        $voeuHorsIUTDTO->deleteByEnseignant($idEnseignant);
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
            // Gestion des voeux de SEPTEMBRE et JANVIER en mode CRUD
            $existing = [];
            foreach ($voeuDTO->findByEnseignant($idEnseignant) as $v) {
                $existing[$v->getIdVoeu()] = $v;
            }
            
            foreach (['septembre', 'janvier'] as $period) {
                // Récupération sécurisée des données pour cette période
                $data = $_POST[$period] ?? [];
                $ids  = isset($data['id']) && is_array($data['id']) ? $data['id'] : [];
                $ressources = $data['ressource'] ?? [];
            
                if (!empty($ressources)) {
                    foreach ($ressources as $i => $nomCours) {
                        // Utilisation sécurisée de l'ID
                        $idVoeu = $ids[$i] ?? '';
                        
                        // Traitement de la ligne
                        // Exemple : récupération du cours et autres champs
                        $nomCours = trim($nomCours);
                        if ($nomCours === '') continue;
                        
                        // Récupération du champ caché course_id (si présent)
                        $courseId = $data['course_id'][$i] ?? '';
                        if ($courseId) {
                            $cours = $coursDTO->findById($courseId);
                        } else {
                            $coursFound = $coursDTO->findByName($nomCours);
                            if (empty($coursFound)) continue;
                            $cours = $coursFound[0];
                        }
                        
                        $remarque = trim($data['remarques'][$i] ?? '');
                        $semestre = $data['semestre'][$i] ?? '';
                        $nbCM = (isset($data['cm'][$i]) && $data['cm'][$i] !== '') ? floatval($data['cm'][$i]) : $cours->getNbHeuresCM();
                        $nbTD = (isset($data['td'][$i]) && $data['td'][$i] !== '') ? floatval($data['td'][$i]) : $cours->getNbHeuresTD();
                        $nbTP = (isset($data['tp'][$i]) && $data['tp'][$i] !== '') ? floatval($data['tp'][$i]) : $cours->getNbHeuresTP();
                        $nbEI = (isset($data['ei'][$i]) && $data['ei'][$i] !== '') ? floatval($data['ei'][$i]) : $cours->getNbHeuresEI();
                        
                        // Création et sauvegarde du voeu
                        $voeu = new Voeu(
                            $idVoeu ?: null,
                            $idEnseignant,
                            $cours->getIdCours(),
                            $remarque,
                            $semestre,
                            $nbCM,
                            $nbTD,
                            $nbTP,
                            $nbEI
                        );
                        $voeuDTO->save($voeu);
                        
                        // Pour la mise à jour, on supprime l'entrée existante
                        if ($idVoeu) {
                            unset($existing[$idVoeu]);
                        }
                    }
                }
            }
            

            
            
            // Supprimer les voeux non soumis
            foreach (array_keys($existing) as $toDelete) {
                $voeuDTO->delete($toDelete);
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
 * Fonction pour générer les lignes des tableaux pour SEPTEMBRE/JANVIER
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
    $ids        = $data['id'] ?? [];
    
    for ($i = 0; $i < $count; $i++) {
        $selectedCours = $ressources[$i] ?? '';
        $remarque = htmlspecialchars($remarques[$i] ?? '');
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
        $valCM = (isset($cms[$i]) && $cms[$i] !== '') ? $cms[$i] : $defaultCM;
        $valTD = (isset($tds[$i]) && $tds[$i] !== '') ? $tds[$i] : $defaultTD;
        $valTP = (isset($tps[$i]) && $tps[$i] !== '') ? $tps[$i] : $defaultTP;
        $valEI = (isset($eis[$i]) && $eis[$i] !== '') ? $eis[$i] : $defaultEI;
        
        echo '<tr>';
            echo '<input type="hidden" name="' . $type . '[id][]" value="' . htmlspecialchars($ids[$i] ?? '') . '">';
            echo '<input type="hidden" name="' . $type . '[course_id][]" value="">';
            echo '<td><input type="text" name="' . $type . '[formation][]" value="' . htmlspecialchars($formations[$i] ?? '') . '" readonly></td>';
            echo '<td><select name="' . $type . '[ressource][]">';
                echo '<option value="">-- Sélectionner un cours --</option>';
                foreach ($coursList as $cours) {
                    if (in_array($cours->getSemestre(), $allowedSemesters)) {
                        $selected = ($cours->getNomCours() === $selectedCours) ? 'selected' : '';
                        $optionDisplay = $cours->getCodeCours() . ' - ' . $cours->getNomCours();
                        // Ajout de l'attribut data-id avec l'identifiant du cours
                        echo '<option value="' . htmlspecialchars($cours->getNomCours()) . '" data-id="' . htmlspecialchars($cours->getIdCours()) . '" ' . $selected . '>' . htmlspecialchars($optionDisplay) . '</option>';
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
 * Fonction pour générer les lignes du tableau "hors IUT"
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

// Inclusion de la vue (HTML/CSS/JS)
include 'fiche_previsionnelle_view.php';
