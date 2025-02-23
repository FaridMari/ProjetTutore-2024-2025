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
    'S5-S6 DACS',
    'S5-S6 RA-DWM',
    'S5-S6 RA-IL'
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
// Construction d'une map id_groupe => nom du groupe (ex. "Gr A", "Gr B", etc.)
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
    $tableData[$i] = ['', $groupeName];
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

// Préremplissage avec les affectations existantes (uniquement si une affectation existe)
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
    $colIndex = 2 + $courseIndex * 4 + $colOffset;
    
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
    
    $tableData[$rowIndex][$colIndex] = $teacherName;
}

$prepopulatedData = json_encode($tableData);

$coursArray = [];
foreach ($listeCours as $cours) {
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
        'nbHeuresEI'    => $cours->getNbHeuresEI()
    ];
}

// Pour le dropdown, on souhaite afficher la liste des enseignants issus des voeux pour chaque cours
$voeuxFormation = $voeuDTO->findByFormation($formationCode);
$voeuMapping = []; // clé: "courseId_teacherName" -> remarque
$enseignantsParCours = []; // courseId -> [list teacher names]
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
<!--  <style>-->
<!--    #hot {-->
<!--      margin-top: 20px;-->
<!--      margin-left: 200px;-->
<!--      z-index: 1;-->
<!--      overflow: auto;-->
<!--      height: 450px;-->
<!--    }-->
<!--    .htNonEditable {-->
<!--      background-color: #f0f0f0;-->
<!--      font-weight: bold;-->
<!--      text-align: center;-->
<!--    }-->
<!--    .htMiddle {-->
<!--      vertical-align: middle;-->
<!--    }-->
<!--    .form-label.text-white {-->
<!--      color: white;-->
<!--      margin-right: 10px;-->
<!--    }-->
<!--    .row.mb-3 {-->
<!--      margin-bottom: 1rem;-->
<!--    }-->
<!--    .form-select.w-25 {-->
<!--      width: 25%;-->
<!--    }-->
<!--    .handsontable td, .handsontable th {-->
<!--      font-size: 14px;-->
<!--      padding: 8px;-->
<!--    }-->
<!--    #voeuRemark {-->
<!--      margin-top: 20px;-->
<!--      padding: 10px;-->
<!--      background-color: #34495e;-->
<!--      border-radius: 4px;-->
<!--    }-->
<!--  </style>-->

<style>
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
        margin-bottom: 1rem;
    }
    .form-select.w-25 {
        width: 25%;
    }
    .table-container {
        overflow-x: auto;
        box-sizing: border-box;
    }
    .handsontable td, .handsontable th {
        font-size: 14px;
        padding: 8px;
    }


</style>



<div id="main-content">
  <h1>Fiche Répartition</h1>
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
  </form>
  <h2>Semestre <?php echo htmlspecialchars($formationCode); ?></h2>
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
            }, 5000); // Le toast disparaît après 3 secondes
        }
    }

    const toast = new Toast();
    // Les données pré-remplies issues de PHP
    const data = <?php echo $prepopulatedData; ?>;
    const listeCours = <?php echo json_encode($coursArray); ?>;
    const semester = '<?php echo htmlspecialchars($formationCode); ?>';
    const enseignantsParCours = <?php echo $enseignantsParCoursJson; ?>;
    const voeuMapping = <?php echo $voeuMappingJson; ?>;
    console.log("enseignantsParCours:", enseignantsParCours);
    console.log("voeuMapping:", voeuMapping);
    
    // Construction des en-têtes imbriqués (nested headers)
    const premLigne = [ 'Semestre ' + semester, 'Ressource + SAE' ];
    listeCours.forEach(cours => {
      premLigne.push({ label: cours.nomCours, colspan: 4 });
    });
    
    const responsable = [];
    for (let i = 0; i < listeCours.length; i++) {
      responsable.push({ label: '', colspan: 4 });
    }
    const deuxLigne = ['2024-2025', 'Responsable', ...responsable];
    
    const deuxDemiLigne = ['', 'Code Cours'];
    listeCours.forEach(cours => {
      deuxDemiLigne.push({ label: cours.codeCours, colspan: 4 });
    });
    
    const troisLigne = ['', 'Heures'];
    listeCours.forEach(() => {
      troisLigne.push({ label: 'CM', colspan: 1 });
      troisLigne.push({ label: 'TD', colspan: 1 });
      troisLigne.push({ label: 'TP', colspan: 1 });
      troisLigne.push({ label: 'EI', colspan: 1 });
    });
    
    const cinqLigne = ['', 'Heures totales Etudiants'];
    const sixLigne = ['', 'Heures totales Enseignants'];
    listeCours.forEach(cours => {
      const totalEtud = cours.nbHeuresCM + cours.nbHeuresTD + cours.nbHeuresTP + cours.nbHeuresEI;
      cinqLigne.push({ label: totalEtud, colspan: 4 });
      const totalEns = cours.nbHeuresCM + cours.nbHeuresTD + (cours.nbHeuresTP * 2) + cours.nbHeuresEI;
      sixLigne.push({ label: totalEns, colspan: 4 });
    });
    
    const nestedHeaders = [
      premLigne,
      deuxLigne,
      deuxDemiLigne,
      cinqLigne,
      sixLigne,
      troisLigne
    ];
    
    // Configuration des colonnes : 2 premières fixes, puis 4 colonnes par cours avec dropdown
    const columnsDefs = [
      { type: 'text', readOnly: true },
      { type: 'text', readOnly: true },
    ];
    listeCours.forEach(cours => {
      for (let i = 0; i < 4; i++) {
        // Ajout d'une option vide dans le dropdown
        const courseTeachers = enseignantsParCours[cours.idCours] || [];
        const source = [''].concat(courseTeachers);
        columnsDefs.push({
          type: 'dropdown',
          source: source,
          allowInvalid: false,
          strict: false,
          width: 100,
        });
      }
    });
    
    const container = document.querySelector('#hot');
    const hot = new Handsontable(container, {
      data: data,
      width: '80%',
      height: 510,
      nestedHeaders: nestedHeaders,
      colWidths: 100,        // Toutes les colonnes auront 100px de largeur
      rowHeights: 50, 
      wordWrap: true,
      licenseKey: 'non-commercial-and-evaluation',
      columns: columnsDefs,
      afterChange: function(changes, source) {
        if (source === 'loadData') return;
        changes.forEach(([row, col, oldVal, newVal]) => {
          if (col < 2) return;
          const courseIndex = Math.floor((col - 2) / 4);
          const course = listeCours[courseIndex];
          if (!course) return;
          const key = course.idCours + "_" + newVal;
          const remark = voeuMapping[key] || "";
          document.getElementById('voeuRemark').innerText = remark ? "Remarque : " + remark : "";
        });
      }
    });
    
    // Lors de la sauvegarde, on envoie un objet JSON contenant les données et la formation
    document.getElementById('saveButton').addEventListener('click', function() {
      const payload = {
        data: hot.getData(),
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
            console.log("Affectations enregistrées avec succès.");
            toast.show("Affectations enregistrées avec succès.", 'success');
          } else {
            console.error("Erreur lors de l'enregistrement.");
            toast.show("Erreur lors de l'enregistrement.", 'error');
          }
        } catch (error) {
          console.error("Erreur de parsing JSON :", error);
            toast.show("Erreur lors de l'enregistrement.", 'error');
        }
      })
      .catch(error => {
        console.error("Erreur:", error);
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
