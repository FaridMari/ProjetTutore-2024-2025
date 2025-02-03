<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);
require_once __DIR__ . '/../modele/CoursDTO.php';
require_once __DIR__ . '/../modele/DetailsCoursDTO.php';
require_once __DIR__ . '/../modele/UtilisateurDTO.php';
require_once __DIR__ . '/../modele/VoeuDTO.php';
require_once __DIR__ . '/../modele/EnseignantDTO.php';

// Instanciation des DTOs
$coursDTO = new CoursDTO();
$detailsCoursDTO = new DetailsCoursDTO();
$utilisateurDTO = new UtilisateurDTO();
$voeuDTO = new VoeuDTO();
$enseignantDTO = new EnseignantDTO();

// Définir les formations disponibles
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

// Récupérer la formation sélectionnée ou définir la valeur par défaut
if (isset($_GET['semester']) && in_array($_GET['semester'], $formationsDisponibles)) {
    $formationCode = $_GET['semester'];
} else {
    $formationCode = 'S1'; // valeur par défaut
}

$listeCours = $coursDTO->findByFormation($formationCode);
$voeuxFormation = $voeuDTO->findByFormation($formationCode);
$listeEnseignants = $enseignantDTO->findAll();
$listeUtilisateur = $utilisateurDTO->findAll();

foreach ($listeEnseignants as $enseignant) {
    foreach ($listeUtilisateur as $utilisateur) {
        if ($enseignant->getIdUtilisateur() === $utilisateur->getIdUtilisateur()) {
            $nom = $utilisateur->getNom();
            $prenom = $utilisateur->getPrenom();
            $enseignantsMap[$enseignant->getIdEnseignant()] = $nom . ' ' . $prenom;
        }
    }
   
}
$voeux = [];
foreach ($voeuxFormation as $voeu) {
    $voeux[] = [
        'idVoeu' => $voeu->getIdVoeu(),
        'idEnseignant' => $voeu->getIdEnseignant(),
        'idCours' => $voeu->getIdCours(),
        'remarque' => $voeu->getRemarque(),
        'semestre' => $voeu->getSemestre(),
        'nbHeures' => $voeu->getNbHeures(),
    ];
}

$enseignantsParCours = [];
foreach ($voeux as $voeu) {
    $idCours = $voeu['idCours'];
    $idEnseignant = $voeu['idEnseignant'];
    
    if (!isset($enseignantsParCours[$idCours])) {
        $enseignantsParCours[$idCours] = [];
    }
    
    if (isset($enseignantsMap[$idEnseignant])) {
        if (!in_array($enseignantsMap[$idEnseignant], $enseignantsParCours[$idCours])) {
            $enseignantsParCours[$idCours][] = $enseignantsMap[$idEnseignant];
        }
    }
}



// Convertir les objets Cours en tableaux associatifs pour JSON
$coursArray = [];
foreach ($listeCours as $cours) {
    $coursArray[] = [
        'idCours' => $cours->getIdCours(),
        'formation' => $cours->getFormation(),
        'semestre' => $cours->getSemestre(),
        'nomCours' => $cours->getNomCours(),
        'codeCours' => $cours->getCodeCours(),
        'nbHeuresTotal' => $cours->getNbHeuresTotal(),
        'nbHeuresCM' => $cours->getNbHeuresCM(),
        'nbHeuresTD' => $cours->getNbHeuresTD(),
        'nbHeuresTP' => $cours->getNbHeuresTP(),
        'nbHeuresEI' => $cours->getNbHeuresEI()
    ];
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Fiche Répartition</title>
  <!-- Inclure Handsontable CSS -->
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.css"
  />
  <!-- Inclure Handsontable JS -->
  <script
    src="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.js"
  ></script>
  <!-- Optionnel : Inclure Bootstrap CSS pour le style -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />
  <style>
      body {
          margin: 20px;
          font-family: Arial, sans-serif;
          background-color: #2c3e50; /* Exemple de fond sombre pour contraster avec le texte */
          color: white; /* Couleur du texte pour les labels */
      }
      #hot {
          margin-top: 20px;
          z-index: 1;
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
        font-size: 14px; /* Augmente un peu la taille */
        padding: 8px;    /* Espace intérieur dans chaque cellule */
    }
  </style>
</head>
<body>
    <h1>Fiche Répartition</h1>

    <!-- Formulaire pour sélectionner la formation -->
    <form method="GET" action="">
        <div class="row mb-3">
            <label for="semester" class="form-label text-white">Choisir le semestre :</label>
            <select class="form-select w-25" id="semester" name="semester" required onchange="this.form.submit()">
                <?php
                foreach ($formationsDisponibles as $formation) {
                    // Utiliser htmlspecialchars pour éviter les injections XSS
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
    <button id="saveButton" class="btn btn-primary mt-3">Enregistrer les Affectations</button>

    <script>
        // Récupérer les données PHP encodées en JSON
        const listeCours = <?php echo json_encode($coursArray); ?>;
        const semester = '<?php echo htmlspecialchars($formationCode); ?>';
        const enseignantsParCours = <?php echo json_encode($enseignantsParCours); ?>;
        const voeuFormation = <?php echo json_encode($voeuxFormation); ?>;
        console.log(voeuFormation);

        const enseignantsMap = <?php echo json_encode($enseignantsMap); ?>;
        const enseignantsInverseMap = {};
        Object.entries(enseignantsMap).forEach(([id, fullName]) => {
            enseignantsInverseMap[fullName] = Number(id);
        });


        // Extraire les noms des cours pour les en-têtes
        const colCours = listeCours.map(cours => cours.nomCours);

        premLigne = [ 'Semestre ' + semester, 'Ressource + SAE'];
        for (let i = 0; i < colCours.length; i++) {
            premLigne.push({ label: colCours[i], colspan: 4 });
        }

        responsable = [];
        for (let i = 0; i < listeCours.length; i++) {
            responsable.push({label: '', colspan: 4});
        }
        deuxLigne = ['2024-2025', 'Responsable'];
        for (let i = 0; i < responsable.length; i++) {
            deuxLigne.push(responsable[i]);
        }

        deuxDemiLigne = ['', 'Code Cours'];
        for (let i = 0; i < listeCours.length; i++) {
            for (let nomCours of colCours) {
                if (nomCours === listeCours[i].nomCours) {
                    deuxDemiLigne.push({ label: listeCours[i].codeCours, colspan: 4 });
                }
            }
        }

        troisLigne = ['', 'Heures'];
        for (let i = 0; i < listeCours.length; i++) {
            troisLigne.push({ label: 'CM', colspan: 1 });
            troisLigne.push({ label: 'TD', colspan: 1 });
            troisLigne.push({ label: 'TP', colspan: 1 });
            troisLigne.push({ label: 'EI', colspan: 1 });
        }
        quatreLigne = ['',''];
        for (let i = 0; i < colCours.length; i++) {
            for (let j = 0; j < listeCours.length; j++) {
                if (colCours[i] === listeCours[j].nomCours) {
                    quatreLigne.push(listeCours[j].nbHeuresCM);
                    quatreLigne.push(listeCours[j].nbHeuresTD);
                    quatreLigne.push(listeCours[j].nbHeuresTP);
                    quatreLigne.push(listeCours[j].nbHeuresEI);
                }
            }
        }

        cinqLigne = ['', 'Heures totales Etudiants'];
        sixLigne = ['', 'Heures totales Enseignants'];
        for (let i = 0; i < colCours.length; i++) {
            for (let j = 0; j < listeCours.length; j++) {
                if (colCours[i] === listeCours[j].nomCours) {
                    cinqLigne.push({label : listeCours[j].nbHeuresCM + listeCours[j].nbHeuresTD + listeCours[j].nbHeuresTP + listeCours[j].nbHeuresEI, colspan : 4});
                    nbSixLigne = 0
                    nbSixLigne += listeCours[j].nbHeuresCM + listeCours[j].nbHeuresTD + listeCours[j].nbHeuresTP * 2 + listeCours[j].nbHeuresEI;
                    sixLigne.push({label: nbSixLigne, colspan: 4});
                }
            }
        }


        trueNH = [
        premLigne,
        deuxLigne,
        deuxDemiLigne,
        cinqLigne,
        sixLigne,
        troisLigne,
        quatreLigne,
        ]

        data1 = ['','Gr A'];
        data2 = ['', 'Gr B'];
        data3 = ['','Gr C'];
        data4 = ['','Gr D'];
        data5 = ['','Gr E'];
        for (let i = 0; i < colCours.length; i++) {
            for (let j = 0; j < 4; j++) {
                data1.push('');
                data2.push('');
                data3.push('');
                data4.push('');
                data5.push('');
            }
        }

        const data = [
            data1,
            data2,
            data3,
            data4,
            data5,
        ];

        const columnsDefs = [
            { type: 'text', readOnly: true },
            { type: 'text', readOnly: true },
        ];

        listeCours.forEach((cours) => {
            for (let i = 0; i < 4; i++) {
                const enseignants = enseignantsParCours[cours.idCours] || [];
                columnsDefs.push({
                        type: 'dropdown',
                        source: enseignants,
                        allowInvalid: false, 
                        strict: true,
                        width: 100,
                    });
            }
        })

        const container = document.querySelector('#hot');
        const planning = new Handsontable(container, {
        data: data,
        width: '70%',
        nestedHeaders: trueNH,
        wordWrap: true,
        licenseKey: 'non-commercial-and-evaluation',
        columns: columnsDefs,
    });

    document.getElementById('saveButton').addEventListener('click', function() {
        const data = planning.getData();
        const affectations = [];

        data.forEach((row,rowIndex) => {
            for (let i = 0; i < listeCours.length; i++) {
                ['CM', 'TD', 'TP', 'EI'].forEach((typeHeure, typeHeureIndex) => {
                    const enseignant = data[rowIndex][2 + i * 4 + typeHeureIndex];
                    if (enseignant && enseignant.trim() !== '') {
                        const heuresEns = listeCours[i].nbHeuresCM + listeCours[i].nbHeuresTD + listeCours[i].nbHeuresTP * 2 + listeCours[i].nbHeuresEI;
                        const groupe = ['Gr A', 'Gr B', 'Gr C', 'Gr D', 'Gr E'][rowIndex];
                        nbHeures = listeCours[i][`nbHeures${typeHeure}`];
                        if (typeHeure == 'TP') {
                            nbHeures *=  2;
                        }
                        enseignantId = enseignantsInverseMap[enseignant];
                        affectations.push({
                            idCours: listeCours[i].idCours,
                            typeHeure: typeHeure,
                            enseignant: enseignantId,
                            heures: nbHeures,
                            groupe: groupe,
                            semestre: semester,
                        });
                    }
                });
            }
        });
        console.log(affectations);
        sendAffectations(affectations);
    });

    function sendAffectations(affectations) {
        fetch('src/Gestionnaire/UpdateAffectations.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(affectations)
        })
            .then(response => response.text())
            .then(data => {
                console.log(data);
                try {
                    const jsonData = JSON.parse(data);
                    if (jsonData.success) {
                        console.log("Affectation insérée avec succès.");
                    } else {
                        console.error("Erreur lors de l'insertion de l'affectation.");
                    }
                } catch (error) {
                    console.error("Erreur de parsing JSON :", error);
                }
            })
            .catch(error => {
                console.error("Erreur:", error);
            });
    }

    const select = document.querySelector('#semester');
    select.addEventListener('change', () => {
        const semester = select.value;
        window.location.href = `index.php?action=ficheRepartition&semester=${semester}`;
    });

    </script>
</body>
</html>
