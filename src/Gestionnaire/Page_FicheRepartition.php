<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

// Inclusion des DTO nécessaires
require_once __DIR__ . '/../modele/CoursDTO.php';
require_once __DIR__ . '/../modele/DetailsCoursDTO.php';
require_once __DIR__ . '/../modele/UtilisateurDTO.php';
require_once __DIR__ . '/../modele/VoeuDTO.php';
require_once __DIR__ . '/../modele/EnseignantDTO.php';
require_once __DIR__ . '/../modele/AffectationDTO.php';
require_once __DIR__ . '/../modele/GroupeDTO.php';

// Instanciation des objets DTO
$coursDTO         = new CoursDTO();
$detailsCoursDTO  = new DetailsCoursDTO();
$utilisateurDTO   = new UtilisateurDTO();
$voeuDTO          = new VoeuDTO();
$enseignantDTO    = new EnseignantDTO();
$affectationDTO   = new AffectationDTO();
$groupeDTO        = new GroupeDTO();

// Définition des formations disponibles
$formationsDisponibles = [
    'S1',
    'S2',
    'S3',
    'S4 DACS',
    'S4 RA-DWM',
    'S4 RA-IL',
    'S5 DACS',
    'S5 RA-DWM',
    'S5 RA-IL',
    'S6 DACS',
    'S6 RA-DWM',
    'S6 RA-IL'
];

// Récupération de la formation (ou semestre) sélectionnée via GET (valeur par défaut 'S1')
if (isset($_GET['semester']) && in_array($_GET['semester'], $formationsDisponibles)) {
    $formationCode = $_GET['semester'];
} else {
    $formationCode = 'S1';
}

// Récupération des cours pour la formation sélectionnée
$listeCours = $coursDTO->findByFormation($formationCode);


// Récupération de toutes les affectations puis filtrage pour ne conserver que celles dont le cours fait partie de la formation
$listeAffectations = $affectationDTO->findAll();
$courseIds = [];
foreach ($listeCours as $cours) {
    $courseIds[] = $cours->getIdCours();
}
$filteredAffectations = array_filter($listeAffectations, function($aff) use ($courseIds) {
    return in_array($aff->getIdCours(), $courseIds);
});

// Récupération des enseignants et utilisateurs pour construire une map : id_enseignant => "Nom Prenom"
$listeEnseignants = $enseignantDTO->findAll();
$listeUtilisateur  = $utilisateurDTO->findAll();
$enseignantsMap = [];
foreach ($listeEnseignants as $enseignant) {
    foreach ($listeUtilisateur as $utilisateur) {
        if ($enseignant->getIdUtilisateur() === $utilisateur->getIdUtilisateur()) {
            $enseignantsMap[(int)$enseignant->getIdEnseignant()] = $utilisateur->getNom() . ' ' . $utilisateur->getPrenom();
        }
    }
}

// Récupération des groupes
$listeGroupes = $groupeDTO->findAll();
// Construction d'une map id_groupe => nom du groupe (ex. "GR A", "GR B", etc.)
$groupNameMapping = [];
foreach ($listeGroupes as $groupe) {
    $groupNameMapping[$groupe->getIdGroupe()] = $groupe->getNomGroupe();
}

// Fixation des groupes à afficher (l'ordre définitif pour Handsontable)
$fixedGroups = ['GR A', 'GR B', 'GR C', 'GR D', 'GR E'];
$rowMapping = [
    'GR A' => 0,
    'GR B' => 1,
    'GR C' => 2,
    'GR D' => 3,
    'GR E' => 4,
];

// --- Construction du tableau prérempli pour Handsontable ---
// Chaque ligne correspond à un groupe et commence par 2 colonnes fixes : [cellule vide, nom du groupe]
$tableData = [];
foreach ($fixedGroups as $i => $groupeName) {
    $tableData[$i] = [$groupeName];
}

// Pour chaque cours, ajoute 4 colonnes (CM, TD, TP, EI) à chaque ligne
$numCourses = count($listeCours);
for ($i = 0; $i < $numCourses; $i++) {
    for ($r = 0; $r < count($tableData); $r++) {
        $tableData[$r] = array_merge($tableData[$r], ['', '', '', '']);
    }
}

// Construction d'un mapping : id_cours => indice dans $listeCours
$courseIndexMapping = [];
foreach ($listeCours as $index => $cours) {
    $courseIndexMapping[$cours->getIdCours()] = $index;
}

// Mapping pour les types d'heures
$offsetMapping = [
    'CM' => 0,
    'TD' => 1,
    'TP' => 2,
    'EI' => 3
];

// Nouveau bloc : collecte des affectations par cellule
$cellValues = [];
foreach ($filteredAffectations as $affectation) {
    $idCours = $affectation->getIdCours();
    if (!isset($courseIndexMapping[$idCours])) {
        continue;
    }
    $courseIndex = $courseIndexMapping[$idCours];
    $typeHeure = strtoupper(trim($affectation->getTypeHeure()));
    if (!isset($offsetMapping[$typeHeure])) {
        continue;
    }
    $colOffset = $offsetMapping[$typeHeure];
    $colIndex = 1 + $courseIndex * 4 + $colOffset;
    
    $idGroupe = $affectation->getIdGroupe();
    if (!isset($groupNameMapping[$idGroupe])) {
        continue;
    }
    $groupeName = $groupNameMapping[$idGroupe];
    if (!isset($rowMapping[$groupeName])) {
        continue;
    }
    $rowIndex = $rowMapping[$groupeName];
    
    $idEnseignant = (int)$affectation->getIdEnseignant();
    if (!isset($enseignantsMap[$idEnseignant])) {
        continue;
    }
    $teacherName = $enseignantsMap[$idEnseignant];
    
    $cellKey = $rowIndex . '_' . $colIndex;
    if (!isset($cellValues[$cellKey])) {
        $cellValues[$cellKey] = [];
    }
    $cellValues[$cellKey][] = $teacherName;
}
// Affecter la valeur concaténée dans le tableau prérempli
foreach ($cellValues as $cellKey => $names) {
    list($rowIndex, $colIndex) = explode('_', $cellKey);
    $tableData[$rowIndex][$colIndex] = implode(', ', $names);
}

$prepopulatedData = json_encode($tableData);

$coursArray = [];
foreach ($listeCours as $cours) {
    $responsable = '';
    $details = $detailsCoursDTO->findByCours($cours->getIdCours());
    if ($details) {
        $responsableId = $details->getIdResponsableModule();
        if (isset($enseignantsMap[$responsableId])) {
            $responsable = $enseignantsMap[$responsableId];
        }
    }
    
    $coursArray[] = [
        'idCours'       => $cours->getIdCours(),
        'formation'     => $cours->getFormation(),
        'semestre'      => $cours->getSemestre(),
        'nomCours'      => $cours->getNomCours(),
        'codeCours'     => $cours->getCodeCours(),
        'nbHeuresTotal' => $cours->getNbHeuresTotal(),
        'nbHeuresCM'    => $cours->getNbHeuresCM(),
        'nbHeuresTD'    => $cours->getNbHeuresTD(),
        'nbHeuresTP'    => $cours->getNbHeuresTP(),
        'nbHeuresEI'    => $cours->getNbHeuresEI(),
        'responsable'   => $responsable
    ];
}

// Préparation des données pour le dropdown
$voeuxFormation = $voeuDTO->findByFormation($formationCode);
$voeuMapping = [];
$enseignantsParCours = [];
foreach ($voeuxFormation as $voeu) {
    $courseId = $voeu->getIdCours();
    $teacherId = (int)$voeu->getIdEnseignant();
    if (!isset($enseignantsMap[$teacherId])) {
        continue;
    }
    $teacherName = $enseignantsMap[$teacherId];
    $key = $courseId . '_' . $teacherName;
    $voeuMapping[$key] = $voeu->getRemarque();
    
    if (!isset($enseignantsParCours[$courseId])) {
        $enseignantsParCours[$courseId] = [];
    }
    if (!in_array($teacherName, $enseignantsParCours[$courseId])) {
        $enseignantsParCours[$courseId][] = $teacherName;
    }
}

$voeuMappingJson = json_encode($voeuMapping);
$enseignantsParCoursJson = json_encode($enseignantsParCours);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Fiche Répartition</title>
  <style>
    #main-content {
        margin: 0;
        margin-top: 40px;
        padding: 30px;
    }
    #hot {
        margin-top: 20px;
    }
    .htNonEditable {
        background-color: #f0f0f0;
        font-weight: bold;
        text-align: center;
    }
    .htMiddle {
        vertical-align: middle;
    }
    .form-label.text-white {
        color: white;
        margin-right: 10px;
    }
    .row.mb-3 {
        display: flex;
        margin-bottom: 1rem;
        justify-content: center;
        align-items: center;
    }
    .form-select.w-25 {
        width: 25%;
    }
    .handsontable td, .handsontable th {
        font-size: 14px;
        padding: 8px;
    }
    /* Augmentation de la taille des dropdowns via CSS */
    .handsontable .htAutocompleteEditor,
    .handsontable .htAutocompleteHolder {
        min-width: 150px !important;
        font-size: 16px;
    }
    
    .export-buttons {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 20px;
        gap: 15px;
        width: 100%;
    }

    .export-btn {
        padding: 10px 18px;
        background-color: #3a86ff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 4em;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: 10%;
    }

    .export-btn:hover {
        background-color: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 6px 8px rgba(0, 0, 0, 0.15);
    }

    .export-btn:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    /* Style spécifique pour chaque bouton */
    #exportCSV {
        background-color: #4caf50;
    }

    #exportCSV:hover {
        background-color: #388e3c;
    }

    #exportXLSX {
        background-color: #2196f3;
    }

    #exportXLSX:hover {
        background-color: #1565c0;
    }

    /* Ajouter des icônes avec pseudo-éléments */
    .export-btn {
        padding: 8px 16px;        /
        background-color: #3a86ff;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2em;          
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        width: auto;               
    }

    #exportCSV::before {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm6 8h-4v-2h4v2zm0-4h-4v-2h4v2zm-1-5V4l5 5h-5z"/></svg>');
    }

    #exportXLSX::before {
        background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="white"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8l-6-6zM9 17H7v-2h2v2zm0-4H7v-2h2v2zm0-4H7V7h2v2zm6 8h-4v-2h4v2zm0-4h-4v-2h4v2zm-1-5V4l5 5h-5z"/></svg>');
    }

  

</style>

  <!-- Inclusion des fichiers CSS/JS de Handsontable -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.css">
  <script src="https://cdn.jsdelivr.net/npm/handsontable/dist/handsontable.full.min.js"></script>
</head>
<body>
<div id="main-content">
  <form method="GET" action="">
    <div class="row mb-3">
      <label for="semester" class="form-label text-white">Choisir le semestre :</label>
      <select class="form-select w-25" id="semester" name="semester" required onchange="this.form.submit()">
        <?php
          foreach ($formationsDisponibles as $formation) {
              $value = htmlspecialchars($formation);
              $label = htmlspecialchars($formation);
              $selected = ($formation === $formationCode) ? 'selected' : '';
              echo "<option value='{$value}' {$selected}>{$label}</option>";
          }
        ?>
      </select>
    </div>
    <div class="export-buttons">
        <button id="exportCSV" class="export-btn">Export CSV</button>
        <button id="exportXLSX" class="export-btn">Export XLSX</button>
    </div>
  </form>
  <div id="hot"></div>
  <div id="voeuRemark"></div>
  <button id="saveButton" class="btn btn-primary mt-3">Enregistrer les Affectations</button>
</div>

<script>
    class Toast {
        constructor() {
            this.toastElement = document.createElement('div');
            this.toastElement.className = 'toast';
            document.body.appendChild(this.toastElement);
        }
        show(message, type = 'success') {
            this.toastElement.textContent = message;
            this.toastElement.className = `toast show ${type}`;
            setTimeout(() => {
                this.toastElement.className = this.toastElement.className.replace('show', '');
            }, 5000);
        }
    }
    const toast = new Toast();

    // Données pré-remplies issues de PHP
    const data = <?php echo $prepopulatedData; ?>;
    const listeCours = <?php echo json_encode($coursArray); ?>;
    const semester = '<?php echo htmlspecialchars($formationCode); ?>';
    const enseignantsParCours = <?php echo $enseignantsParCoursJson; ?>;
    const voeuMapping = <?php echo $voeuMappingJson; ?>;
    console.log(listeCours);

    // Paramètre : nombre de cours par tableau (chunk)
    const coursesPerChunk = 3;
    const totalCourses = listeCours.length;
    const numChunks = Math.ceil(totalCourses / coursesPerChunk);

    // Tableau pour stocker manuellement les instances Handsontable
    const hotInstances = [];
    const mainContainer = document.getElementById('hot');
    mainContainer.innerHTML = '';

    // Création des instances Handsontable pour chaque chunk
    for (let chunkIndex = 0; chunkIndex < numChunks; chunkIndex++) {
      const chunkStart = chunkIndex * coursesPerChunk;
      const chunkEnd = Math.min(chunkStart + coursesPerChunk, totalCourses);
      const coursesChunk = listeCours.slice(chunkStart, chunkEnd);
      const tableDataChunk = data.map(row => {
        const startIndex = 2 + chunkStart * 4;
        const endIndex = startIndex + (coursesChunk.length * 4);
        return row.slice(0, 1).concat(row.slice(1 + chunkStart * 4, 1 + chunkStart * 4 + coursesChunk.length * 4));

      });

      const premLigne = ['Ressource + SAE' ];
      coursesChunk.forEach(cours => {
        premLigne.push({ label: cours.nomCours, colspan: 4 });
      });
      const responsable = [];
      coursesChunk.forEach(cours => {
      responsable.push({ label: cours.responsable || '', colspan: 4 });
      });
      const deuxLigne = ['Responsable', ...responsable];
      const deuxDemiLigne = ['Code Cours'];
      coursesChunk.forEach(cours => {
        deuxDemiLigne.push({ label: cours.codeCours, colspan: 4 });
      });
      const troisLigne = ['Heures'];
      coursesChunk.forEach(() => {
        troisLigne.push({ label: 'CM', colspan: 1 });
        troisLigne.push({ label: 'TD', colspan: 1 });
        troisLigne.push({ label: 'TP', colspan: 1 });
        troisLigne.push({ label: 'EI', colspan: 1 });
      });
      const cinqLigne = ['Heures totales Etudiants'];
      const sixLigne = ['Heures totales Enseignants'];
      coursesChunk.forEach(cours => {
        const totalEtud = cours.nbHeuresCM + cours.nbHeuresTD + cours.nbHeuresTP + cours.nbHeuresEI;
        cinqLigne.push({ label: totalEtud, colspan: 4 });
        const totalEns = cours.nbHeuresCM + cours.nbHeuresTD + (cours.nbHeuresTP * 2) + cours.nbHeuresEI;
        sixLigne.push({ label: totalEns, colspan: 4 });
      });
      const nestedHeaders = [ premLigne, deuxLigne, deuxDemiLigne, cinqLigne, sixLigne, troisLigne ];

      // Définition des colonnes pour ce chunk
      const columnsDefs = [
        { type: 'text', readOnly: true },

      ];
      coursesChunk.forEach(cours => {
        for (let i = 0; i < 4; i++) {
          const courseTeachers = enseignantsParCours[cours.idCours] || [];
          const source = [''].concat(courseTeachers);
          columnsDefs.push({
            type: 'autocomplete',
            source: source,
            allowInvalid: true,
            strict: false,
            width: 150,
          });
        }
      });

      const tableDiv = document.createElement('div');
      tableDiv.style.marginBottom = '20px';
      mainContainer.appendChild(tableDiv);

      const hot = new Handsontable(tableDiv, {
        data: tableDataChunk,
        width: '100%',
        height: 510,
        stretchH: 'all',
        nestedHeaders: nestedHeaders,
        colWidths: 150,
        rowHeights: 50,
        wordWrap: true,
        licenseKey: 'non-commercial-and-evaluation',
        columns: columnsDefs,
        afterChange: function(changes, source) {
          if (source === 'loadData') return;
          changes.forEach(([row, col, oldVal, newVal]) => {
            if (col < 2) return;
            const courseChunkIndex = Math.floor((col - 2) / 4);
            const globalCourseIndex = chunkStart + courseChunkIndex;
            const course = listeCours[globalCourseIndex];
            if (!course) return;
            const key = course.idCours + "_" + newVal;
            const remark = voeuMapping[key] || "";
            document.getElementById('voeuRemark').innerText = remark ? "Remarque : " + remark : "";
          });
        },
        afterGetColHeader: function () {
            const headerRows = tableDiv.querySelectorAll('.ht_clone_top thead tr');

            headerRows.forEach((row, rowIndex) => {
                if (rowIndex === 0) {
                    row.querySelectorAll('th').forEach((th) => {
                        th.style.backgroundColor = '#007bff';
                        th.style.color = '#ffffff';
                    });
                } else if (rowIndex === 1) {
                    row.querySelectorAll('th').forEach((th) => {
                        th.style.backgroundColor = '#ffc107';
                        th.style.color = '#000000';
                    });
                } else if (rowIndex === 2) {
                    row.querySelectorAll('th').forEach((th) => {
                        th.style.backgroundColor = '#28a745';
                        th.style.color = '#ffffff';
                    });
                }
            });
        }
      });
      hotInstances.push(hot);
    }

    // Sauvegarde : fusionner les données de toutes les instances avant envoi
    document.getElementById('saveButton').addEventListener('click', function() {
      let fullData = [];
      if (hotInstances.length > 0) {
        fullData = hotInstances[0].getData();
      }
      for (let i = 1; i < hotInstances.length; i++) {
        const instanceData = hotInstances[i].getData();
        for (let r = 0; r < instanceData.length; r++) {
          fullData[r] = fullData[r].concat(instanceData[r].slice(2));
        }
      }
      console.log(fullData);
      const payload = {
        data: fullData,
        formation: semester
      };
      console.log(payload);
      fetch('src/Gestionnaire/RequeteBD_UpdateFicheRepartition.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload)
      })
      .then(response => response.text())
      .then(responseText => {
        console.log(responseText);
        try {
          const jsonData = JSON.parse(responseText);
          if (jsonData.success) {
            toast.show("Affectations enregistrées avec succès.", 'success');
          } else {
            toast.show("Erreur lors de l'enregistrement.", 'error');
          }
        } catch (error) {
          toast.show("Erreur lors de l'enregistrement.", 'error');
        }
      })
      .catch(error => {
        toast.show("Erreur lors de l'enregistrement.", 'error');
      });
    });

    const select = document.querySelector('#semester');
    select.addEventListener('change', () => {
      const semester = select.value;
      window.location.href = `index.php?action=ficheRepartition&semester=${semester}`;
    });
</script>
</body>
</html>
