<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planning</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.css">
    <script src="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.js"></script>
</head>
<style>
    body {
        padding-right: 150px;
        font-family: 'Poppins', sans-serif;
        background-color: #34495e;
        display: flex;
        flex-direction : column;
        align-items: center;
        justify-content: center;

    }

    .htCore th {
        white-space: normal !important;
        word-wrap: break-word !important;
    }


    #example1 {

        width: 80% !important;
        max-width: 90vw;
    }


</style>
<body class="bg-dark text-center">
    <div class="container my-4">
        <h1 class="text-white mb-4">Gestion du Planning</h1>

        <!-- Sélecteur de semestre -->
        <div class="row mb-3">
            <label for="semester" class="form-label text-white">Choisir le semestre :</label>
            <select class="form-select w-25" id="semester" name="semester" required>
                <?php

                use src\Db\connexionFactory;
                $bdd = connexionFactory::makeConnection();
                $semester = $_GET['semester'] ?? 'S1';
                $coursListSansSae = $bdd->query(
                    "SELECT code_cours, nom_cours, nb_heures_cm, nb_heures_td, nb_heures_tp, semestre, nb_heures_total, nb_heures_ei
                        FROM cours WHERE formation = 'BUT " . $_GET['semester'] . "' AND code_cours LIKE 'R%'"
                )->fetchAll(PDO::FETCH_ASSOC);

                //code cours a un s au lieu d'un r
                $coursListSae = $bdd->query(
                    "SELECT code_cours, nom_cours, nb_heures_cm, nb_heures_td, nb_heures_tp, semestre, nb_heures_total, nb_heures_ei
                        FROM cours WHERE formation = 'BUT " . $_GET['semester'] . "' AND code_cours LIKE 'S%'"
                )->fetchAll(PDO::FETCH_ASSOC);

                $coursList = array_merge($coursListSansSae, $coursListSae);

                $repartition = $bdd->query(
                    'SELECT DISTINCT concat(substring(utilisateurs.nom, 1, 1),substring(utilisateurs.prenom,1,1)) as responsable, cours.nom_cours  ,cours.semestre,  cours.nb_heures_total, cours.nb_heures_cm, cours.nb_heures_tp, cours.nb_heures_ei, cours.nb_heures_td, repartition_heures.type_heure, repartition_heures.nb_heures_par_semaine, repartition_heures.semaine_debut, repartition_heures.semaine_fin
                                FROM cours
                                INNER JOIN repartition_heures ON cours.id_cours = repartition_heures.id_cours
                                INNER JOIN details_cours ON cours.id_cours = details_cours.id_cours
                                INNER JOIN enseignants ON details_cours.id_responsable_module = enseignants.id_enseignant
                                INNER JOIN utilisateurs ON enseignants.id_utilisateur = utilisateurs.id_utilisateur')->fetchAll(PDO::FETCH_ASSOC);

                $repartition2 = $bdd->query(
                    'SELECT DISTINCT cours.nom_cours  ,cours.semestre,  cours.nb_heures_total, cours.nb_heures_cm, cours.nb_heures_tp, cours.nb_heures_ei, cours.nb_heures_td, repartition_heures.type_heure, repartition_heures.nb_heures_par_semaine, repartition_heures.semaine_debut, repartition_heures.semaine_fin
                                FROM cours
                                INNER JOIN repartition_heures ON cours.id_cours = repartition_heures.id_cours')->fetchAll(PDO::FETCH_ASSOC);

                $formations = [
                    ['semestre' => 'S1'],
                    ['semestre' => 'S2'],
                    ['semestre' => 'S3'],
                    ['semestre' => 'S4 DACS'],
                    ['semestre' => 'S4 RA-DWM'],
                    ['semestre' => 'S4 RA-IL'],
                    ['semestre' => 'S5-S6 DACS'],
                    ['semestre' => 'S5-S6 RA-DWM'],
                    ['semestre' => 'S5-S6 RA-IL'],
                ];

                // Génération des options du dropdown
                foreach ($formations as $formation) {
                    $selected = ($formation['semestre'] == $_GET['semester']) ? 'selected' : '';
                    echo "<option value='{$formation['semestre']}' $selected>{$formation['semestre']}</option>";
                }
                ?>
            </select>
        </div>


    </div>
    <div id="example1"></div>
</body>

<script>
    const dateDebutSemestre = new Date('2024-09-02');
    const dateFinSemestre = new Date('2025-01-24');
    const semaine = 36;
    const nbSemaine = 24;



    const data = [
        ['Semestre', 'Nom Cours', 'Heures CM', 'Heures TD', 'Heures TP'],
        ['S1', 'Cours 1', 30, 20, 15],
        ['S1', 'Cours 2', 25, 25, 10],
        ['S2', 'Cours 3', 20, 30, 20]
    ];

    const repartitionData = <?php echo json_encode($repartition); ?>;
    const coursList = <?php echo json_encode($coursList); ?>;
    const coursListSansSae = <?php echo json_encode($coursListSansSae); ?>;
    const coursListSae = <?php echo json_encode($coursListSae); ?>;
    const formations = <?php echo json_encode($formations); ?>;
    const semester = '<?php echo $_GET['semester']; ?>';
    const repartitionSansProf = <?php echo json_encode($repartition2); ?>;


    colCours = [];
    for (let i = 0; i < coursListSansSae.length; i++) {
        colCours.push(coursListSansSae[i].nom_cours);
    }
    premLigne = ['','','Semestre' + semester, 'Ressource + SAE'];
    for (let i = 0; i < colCours.length; i++) {
        premLigne.push({ label: colCours[i], colspan: 3 });
    }
    premLigne.push({label: 'SAE', colspan: coursListSansSae.length*3});
    const responsable = [];
    for (let i = 0; i<coursListSansSae.length; i++){
        ajouter = false;
        for (let j = 0; j<repartitionData.length; j++){
            if (coursListSansSae[i].nom_cours === repartitionData[j].nom_cours){
                responsable.push({label: repartitionData[j].responsable, colspan: 3});
                ajouter = true;
                break;
            }
        }
        if (!ajouter){
            responsable.push({label: '', colspan: 3});
        }
    }
    for (let i = 0; i<coursListSae.length; i++){
        ajouter = false;
        for (let j = 0; j<repartitionData.length; j++){
            if (coursListSae[i].nom_cours === repartitionData[j].nom_cours){
                responsable.push({label: repartitionData[j].responsable, colspan: 3});
                ajouter = true;
                break;
            }
        }
        if (!ajouter){
            responsable.push({label: '', colspan: 3});
        }
    }

    deuxLigne = ['','','2024-2025', 'Responsable'];
    for (let i = 0; i < responsable.length; i++) {
        deuxLigne.push(responsable[i]);
    }
    deuxDemiLigne = ['','','', 'Code Cours'];

    for (let i = 0; i < coursListSansSae.length; i++) {
        deuxDemiLigne.push({ label: coursListSansSae[i].code_cours, colspan: 3 });
    }
    for (let i = 0; i < coursListSae.length; i++) {
        deuxDemiLigne.push({ label: coursListSae[i].code_cours, colspan: 3 });
    }


    troisLigne = ['','','', 'PN'];
    for (let i = 0; i < colCours.length; i++) {
        troisLigne.push({ label: 'CM', colspan: 1 });
        troisLigne.push({ label: 'TD', colspan: 1 });
        troisLigne.push({ label: 'TP', colspan: 1 });
    }
    for (let i = 0; i < coursListSae.length; i++) {
        troisLigne.push({ label:coursListSae[i].nom_cours, colspan: 3 });
    }
    quatreLigne = ['','','', ''];
    for (let i = 0; i < colCours.length; i++) {
        for (let j = 0; j < coursListSansSae.length; j++) {
            if (colCours[i] === coursListSansSae[j].nom_cours) {
                quatreLigne.push(coursListSansSae[j].nb_heures_cm);
                quatreLigne.push(coursListSansSae[j].nb_heures_td);
                quatreLigne.push(coursListSansSae[j].nb_heures_tp);
            }
        }
    }
    for (let j = 0; j < coursListSae.length; j++) {
        quatreLigne.push({label: coursListSae[j].nb_heures_total, colspan: 3});
    }

    cinqLigne = ['','','', 'Heures totales Etudiants'];
    sixLigne = ['','','', 'Heures totales Enseignants'];
    for (let i = 0; i < colCours.length; i++) {
        for (let j = 0; j < coursListSansSae.length; j++) {
            if (colCours[i] === coursListSansSae[j].nom_cours) {
                cinqLigne.push({label: coursListSansSae[j].nb_heures_total, colspan: 3});
                nbSixLigne = +coursListSansSae[j].nb_heures_cm + +coursListSansSae[j].nb_heures_td + +coursListSansSae[j].nb_heures_tp*2;
                sixLigne.push({label: nbSixLigne, colspan: 3});
            }
        }
    }
    for (let j = 0; j < coursListSae.length; j++) {
        cinqLigne.push({label: "", colspan: 3});
        sixLigne.push({label: "", colspan: 3});
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

    // Création du tableau des datas
    //Ligne = vide | semaineActuelle | semaineActuelle en date DD/MM/YYYY | texte vide | si dans repartition heures il y a des données pour ce cours pour les cm ajouté | pour les td | pour les tp | recommencer pour chaque cours
    //pour chaque semaine
    let dataT = [];
    let semaineActuelle = semaine;
    for (let i = 0; i < nbSemaine; i++) {
        let total = 0;
        let semaineData = [];
        let vacToussaint = 43;
        let vacNoel = 51;
        let vacNoelFin = 52;
        let vacHiver = 7;
        let vacPrintemps = 15;
        let vacPrintempsFin = 16;
        let estVacances = [vacToussaint, vacNoel, vacNoelFin, vacHiver, vacPrintemps, vacPrintempsFin].includes(semaineActuelle);
        let dateActuelleStr = new Date(dateDebutSemestre).toLocaleDateString('fr-FR');

        // Gestion des colonnes fixes pour chaque semaine
        if (estVacances) {
            semaineData.push("", semaineActuelle, dateActuelleStr, "Vacances");
        } else {
            semaineData.push("", semaineActuelle, dateActuelleStr, "");
        }

        // Construire la liste des colonnes pour les cours (y compris SAEs)
        for (let sae of coursListSae) {
            colCours.push(sae.nom_cours);
        }

        for (let cours of colCours) {
            let valueCM = "";
            let valueTD = "";
            let valueTP = "";
            let valueEI = "";
            let cmAjoute = false;
            let tdAjoute = false;
            let tpAjoute = false;
            let eiAjoute = false;
            let estSae = coursListSae.some(sae => sae.nom_cours === cours); // Vérifie si le cours est une SAE

            for (let rep of repartitionSansProf) {
                if (cours === rep.nom_cours && rep.semaine_debut <= semaineActuelle && rep.semaine_fin >= semaineActuelle) {
                    if (!estVacances) {
                        if (estSae) { // Si c'est une SAE, uniquement gérer EI
                            if (rep.type_heure === 'CM') {
                                valueEI = rep.nb_heures_par_semaine;
                                eiAjoute = true;
                            }
                        } else { // Sinon, gérer les types standards (CM, TD, TP)
                            if (rep.type_heure === 'CM') {
                                valueCM = rep.nb_heures_par_semaine;
                                cmAjoute = true;
                            } else if (rep.type_heure === 'TD') {
                                valueTD = rep.nb_heures_par_semaine;
                                tdAjoute = true;
                            } else if (rep.type_heure === 'TP') {
                                valueTP = rep.nb_heures_par_semaine;
                                tpAjoute = true;
                            }
                        }
                    }
                }
            }

            // Ajouter les valeurs CM, TD, TP ou combiner sur 3 colonnes pour les SAEs
            if (estSae && eiAjoute) {
                semaineData.push(valueEI);
                semaineData.push("");
                semaineData.push("");
            } else {
                semaineData.push(valueCM, valueTD, valueTP);
            }
        }

        dataT.push(semaineData);

        // Passer à la semaine suivante
        semaineActuelle = (semaineActuelle >= 52) ? 1 : semaineActuelle + 1;
        dateDebutSemestre.setDate(dateDebutSemestre.getDate() + 7);
    }
    let mergeCells = [];
    let colStart = 4+ coursListSansSae.length*3; // Colonne de départ de la fusion
    let colspan = 3;

    let currentCol = colStart;
    for (let i = 0; i < nbSemaine; i++) {
        for (let j = 0; j < coursListSae.length; j++) {
            mergeCells.push({ row: i, col: currentCol, rowspan: 1, colspan: colspan });
            currentCol += colspan;
        }
        currentCol = colStart;
    }
    let pendingChanges = []; // Stocke les changements en attente
    let debounceTimer = null; // Timer pour limiter les requêtes

    const container = document.querySelector('#example1');
    const planning = new Handsontable(container, {
        data: dataT,
        width: 50,
        nestedHeaders: trueNH,
        wordWrap: true,
        mergeCells: mergeCells,
        licenseKey: 'non-commercial-and-evaluation',
        afterChange: (changes, source) => {
            if (source === 'loadData' || !changes) return; // Ignorer les changements initiaux ou vides

            // Ajouter les changements au tableau en attente
            changes.forEach(([row, col, oldValue, newValue]) => {
                if (oldValue !== newValue) {
                    pendingChanges.push({ row, col, newValue });
                }
            });

            // Déclencher un enregistrement avec un délai (debounce)
            if (!debounceTimer) {
                debounceTimer = setTimeout(() => {
                    saveAllData(pendingChanges); // Appeler la fonction pour enregistrer en lot
                    pendingChanges = []; // Réinitialiser les changements
                    debounceTimer = null; // Réinitialiser le timer
                }, 7000); // 1000ms = 1 seconde
            }
        },

    });

    function saveAllData() {
        // Préparez toutes les données pour l'envoi
        const repartitions = [];
        let finalData = [];

        dataT.forEach((row, rowIndex) => {
            colCours.forEach((coursCode, colIndex) => {
                // Pour chaque type d'heure (CM, TD, TP)
                ['CM', 'TD', 'TP'].forEach((typeHeure, typeIndex) => {
                    // Calcul de l'indice correct pour récupérer les données dans dataT
                    const colData = dataT[rowIndex][4 + colIndex * 3 + typeIndex]; // Colonne correspondante pour CM, TD, TP
                    if (colData) {
                        const semaineDebut = row[1]; // Semaine de début (colonne dédiée)
                        const semaineFin = semaineDebut; // Par défaut, semaineFin = semaineDebut

                        // Ajout de la répartition dans le tableau
                        repartitions.push({
                            semaineDebut: semaineDebut,
                            semaineFin: semaineFin,
                            //code du cours qui se trouve dans la ligne 3 du header du tableau
                            codeCours: trueNH[2][4 + colIndex].label,
                            typeHeure: typeHeure, // Type d'heure (CM, TD, TP)
                            nbHeures: colData, // Nombre d'heures par semaine
                            semestre: semester // Semestre actuel
                        });
                    }
                });
            });
        });

        const mergedRepartitions = [];
        let currentRepartition = null;
        repartitions.sort((a, b) => {
            if (a.codeCours !== b.codeCours) {
                return a.codeCours.localeCompare(b.codeCours); // Tri par cours
            }
            if (a.typeHeure !== b.typeHeure) {
                return a.typeHeure.localeCompare(b.typeHeure); // Tri par type d'heure (CM, TD, TP)
            }
            return a.semaineDebut - b.semaineDebut; // Tri par semaine de début
        });

        repartitions.forEach((repartition, index) => {
            // Si une répartition est identique à la précédente
            if (currentRepartition &&
                currentRepartition.codeCours === repartition.codeCours &&
                currentRepartition.typeHeure === repartition.typeHeure &&
                currentRepartition.nbHeures === repartition.nbHeures) {

                // Si elles sont consécutives (semaineFin de la précédente égale à semaineDebut de la nouvelle)
                if (currentRepartition.semaineFin + 1 === repartition.semaineDebut) {
                    // On fusionne en mettant à jour la semaine de fin
                    currentRepartition.semaineFin = repartition.semaineFin;
                } else {
                    // Si elles ne sont pas consécutives, on les ajoute séparément
                    mergedRepartitions.push(currentRepartition);
                    currentRepartition = { ...repartition }; // Nouvelle répartition
                }
            } else {
                // Si la répartition est différente, on ajoute la précédente (si elle existe) et on commence une nouvelle
                if (currentRepartition) {
                    mergedRepartitions.push(currentRepartition);
                }
                currentRepartition = { ...repartition }; // Créer une copie de la répartition actuelle
            }
        });

// Ajouter la dernière répartition à la liste
        if (currentRepartition) {
            mergedRepartitions.push(currentRepartition);
        }
        sendRepartitionData(mergedRepartitions,semester);
    }

    function sendRepartitionData(repartition,semester) {
        fetch('src/Gestionnaire/UpdateRepartition.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // Assurez-vous que le serveur peut accepter des données JSON
            },
            body: JSON.stringify({ repartition,semester})

        })
            .then(response => response.text())
            .then(data => {
                try {
                    const jsonData = JSON.parse(data); // Essayer de parser en JSON
                    if (jsonData.success) {
                        console.log("Répartition insérée avec succès.");
                    } else {
                        console.error("Erreur lors de l'insertion de la répartition.");
                    }
                } catch (error) {
                    console.error("Erreur de parsing JSON :", error);
                }
            })
            .catch(error => {
                console.error("Erreur:", error);
            });
    }

    container.addEventListener('wheel', (event) => {
        event.preventDefault(); // Empêche le défilement de la page

        // Obtenir les coordonnées des cellules sélectionnées
        const selectedRanges = planning.getSelected();

        if (selectedRanges) {
            selectedRanges.forEach((range) => {
                const [startRow, startCol, endRow, endCol] = range;

                // Parcourir toutes les cellules sélectionnées
                for (let row = startRow; row <= endRow; row++) {
                    for (let col = startCol; col <= endCol; col++) {
                        let currentValue = parseInt(planning.getDataAtCell(row, col) || '0', 10); // Obtenir la valeur actuelle (ou 0 si vide)
                        const delta = event.deltaY > 0 ? -1 : 1; // -1 pour scroll bas, +1 pour scroll haut
                        currentValue = Math.max(0, currentValue + delta); // Ajuster la valeur sans descendre en dessous de 0
                        planning.setDataAtCell(row, col, currentValue); // Mettre à jour la cellule
                    }
                }
            });
        }
    });


    const select = document.querySelector('#semester');
    select.addEventListener('change', () => {
        const semester = select.value;
        window.location.href = `index.php?action=ficheDetaille&semester=${semester}`;
    });
</script>
</html>
