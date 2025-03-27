<style>
    #main-content {
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        margin: 0;
        padding: 30px;
        box-sizing: border-box;
    }

    .container.my-4 {
        max-width: 90%;
        margin: 0 auto;
    }

    h1 {
        font-size: 2rem;
        margin-bottom: 1em;
        color: #fff;
    }

    .row.mb-3 {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-bottom: 1rem;
    }

    .form-label.text-white {
        color: #fff;
        margin-right: 1em;
        font-size: large;
    }

    #semester {
        font-size: x-large;
    }
    .form-select.w-25 {
        width: 25%;
    }

    #example1 {
        height: 100%;
        width: 100%;
    }

    .handsontable {
        height: 100%;
        position: relative;
    }


    .handsontable td, .handsontable th {
        font-size: 14px;
        padding: 8px;
        white-space: normal;
        word-wrap: break-word;
    }

    .vacation {
        background-color: #d4edda !important;
    }

    .total-exceeded {
        background-color: #ffcccc !important;
    }


</style>

<div id="main-content">
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

                $configurationPlanningDetailleData = $bdd->query(
                    'SELECT * FROM configurationplanningdetaille')->fetchAll(PDO::FETCH_ASSOC);

                $formations = [
                    ['semestre' => 'S1'],
                    ['semestre' => 'S2'],
                    ['semestre' => 'S3'],
                    ['semestre' => 'S4 DACS'],
                    ['semestre' => 'S5 DACS'],
                    ['semestre' => 'S6 DACS'],
                    ['semestre' => 'S4 RA-DWM'],
                    ['semestre' => 'S5 RA-DWM'],
                    ['semestre' => 'S6 RA-DWM'],
                    ['semestre' => 'S4 RA-IL'],
                    ['semestre' => 'S5 RA-IL'],
                    ['semestre' => 'S6 RA-IL'],
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
    <div  id="example1" class="hot ht-theme-main disable-auto-theme"></div>
</div>


<script>
    const repartitionData = <?php echo json_encode($repartition); ?>;
    const coursList = <?php echo json_encode($coursList); ?>;
    const coursListSansSae = <?php echo json_encode($coursListSansSae); ?>;
    const coursListSae = <?php echo json_encode($coursListSae); ?>;
    const formations = <?php echo json_encode($formations); ?>;
    const semester = '<?php echo $_GET['semester']; ?>';
    const repartitionSansProf = <?php echo json_encode($repartition2); ?>;

    let configData = <?php echo json_encode($configurationPlanningDetailleData); ?>;

    let semaine = 0;
    let nbSemaine = 10;
    let semaineData = [];
    let vacToussaint = 0;
    let vacNoel = 0;
    let vacNoelFin = 0;
    let vacHiver = 0;
    let vacPrintemps = 0;
    let vacPrintempsFin = 0;
    let allVacances = [];
    let dateDebutSemestre = new Date();
    let dateFinSemestre = new Date();
    let dateDebutSemestre1 = new Date();
    let dateFinSemestre1 = new Date();
    let dateDebutSemestre2 = new Date();
    let dateFinSemestre2 = new Date();
    let nbSemaine1 = 0;
    let nbSemaine2 = 0;
    let vacToussaintFinS = 0;
    let vacNoelFinS = 0;
    let vacHiverFinS = 0;
    let vacPrintempsFinS = 0;

    // Assumons que `configData` est une liste d'objets contenant des informations sur les semestres et les vacances
    let evenements = [];

    for (let item of configData) {
        switch (item.type) {
            case 'Semestre1':
                // Affectation des valeurs pour le semestre 1
                const semestre1 = item;
                dateDebutSemestre1 = new Date(semestre1.dateDebut);
                dateFinSemestre1 = new Date(semestre1.dateFin);
                nbSemaine1 = semestre1.nbSemaines;
                break;

            case 'Semestre2':
                // Affectation des valeurs pour le semestre 2
                const semestre2 = item;
                dateDebutSemestre2 = new Date(semestre2.dateDebut);
                dateFinSemestre2 = new Date(semestre2.dateFin);
                nbSemaine2 = semestre2.nbSemaines;
                break;

            case 'VacancesToussaint':
                // Affectation des valeurs pour les vacances de la Toussaint
                const vacancesToussaint = item;
                vacToussaintDebut = new Date(vacancesToussaint.dateDebut);
                vacToussaintFin = new Date(vacancesToussaint.dateFin);
                vacToussaint = getWeek(vacToussaintDebut);
                vacToussaintFinS = getWeek(vacToussaintFin);
                allVacances.push(vacToussaint);
                allVacances.push(vacToussaintFinS);
                break;

            case 'VacancesNoel':
                // Affectation des valeurs pour les vacances de Noël
                const vacancesNoel = item;
                vacNoelDebut = new Date(vacancesNoel.dateDebut);
                vacNoelFin = new Date(vacancesNoel.dateFin);
                vacNoel = getWeek(vacNoelDebut);
                vacNoelFinS = getWeek(vacNoelFin);
                allVacances.push(vacNoel);
                allVacances.push(vacNoelFinS);
                break;

            case 'VacancesHiver':
                // Affectation des valeurs pour les vacances d'hiver
                const vacancesHiver = item;
                vacHiverDebut = new Date(vacancesHiver.dateDebut);
                vacHiverFin = new Date(vacancesHiver.dateFin);
                vacHiver = getWeek(vacHiverDebut);
                vacHiverFinS = getWeek(vacHiverFin);
                allVacances.push(vacHiver);
                allVacances.push(vacHiverFinS);
                break;

            case 'VacancesPrintemps':
                // Affectation des valeurs pour les vacances de printemps
                const vacancesPrintemps = item;
                vacPrintempsDebut = new Date(vacancesPrintemps.dateDebut);
                vacPrintempsFin = new Date(vacancesPrintemps.dateFin);
                vacPrintemps = getWeek(vacPrintempsDebut);
                vacPrintempsFinS = getWeek(vacPrintempsFin);
                allVacances.push(vacPrintemps);
                allVacances.push(vacPrintempsFinS);
                break;
            default:
                // Ajouter l'événement à la liste des événements et calculer les semaines
                const eventType = item.type;
                const eventDebut = new Date(item.dateDebut);
                const eventFin = new Date(item.dateFin);
                const eventWeekStart = getWeek(eventDebut);
                const eventWeekEnd = getWeek(eventFin);
                evenements.push({
                    eventType: eventType,
                    dateDebut: eventDebut,
                    dateFin: eventFin,
                    description: item.description,
                    weeks: [eventWeekStart, eventWeekEnd],
                    couleur: item.couleur,
                    checkbox: item.modifiable,
                });
                break;
        }
    }

    // Déterminer quel semestre utiliser
    if (semester === 'S1' || semester === 'S3') {
        semaine = getWeek(dateDebutSemestre2);
        nbSemaine = nbSemaine2;
        dateDebutSemestre = dateDebutSemestre2;
        dateFinSemestre = dateFinSemestre2;
    } else {
        semaine = getWeek(dateDebutSemestre1); // Semaine de début pour le semestre 1
        nbSemaine = nbSemaine1;
        dateDebutSemestre = dateDebutSemestre1;
        dateFinSemestre = dateFinSemestre1;
    }

    // Fonction pour calculer la semaine de l'année
    function getWeek(date) {
        //Calcul de la semaine avec la norme iso
        const tempDate = new Date(date);
        tempDate.setHours(0, 0, 0, 0);

        tempDate.setDate(tempDate.getDate() + 3 - (tempDate.getDay() + 6) % 7);
        const firstThursday = new Date(tempDate.getFullYear(), 0, 4);
        firstThursday.setDate(firstThursday.getDate() + 3 - (firstThursday.getDay() + 6) % 7);

        const weekNumber = Math.round((tempDate - firstThursday) / (7 * 24 * 60 * 60 * 1000)) + 1;

        return weekNumber;
    }

    colCours = [];
    for (let i = 0; i < coursListSansSae.length; i++) {
        colCours.push(coursListSansSae[i].nom_cours);
    }
    let premLigne = ['', '', 'Semestre' + semester, 'Ressource + SAE'];
    for (let i = 0; i < colCours.length; i++) {
        premLigne.push({ label: colCours[i], colspan: 3 });
    }
    premLigne.push({ label: 'SAE', colspan: coursListSae.length });
    let responsable = [];
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
                responsable.push({label: repartitionData[j].responsable, colspan: 1});
                ajouter = true;
                break;
            }
        }
        if (!ajouter){
            responsable.push({label: '', colspan: 1});
        }
    }

    let deuxLigne = ['','','2024-2025', 'Responsable'];
    for (let i = 0; i < responsable.length; i++) {
        deuxLigne.push(responsable[i]);
    }

    let deuxDemiLigne = ['','','', 'Code Cours'];

    for (let i = 0; i < coursListSansSae.length; i++) {
        deuxDemiLigne.push({ label: coursListSansSae[i].code_cours, colspan: 3 });
    }
    for (let i = 0; i < coursListSae.length; i++) {
        deuxDemiLigne.push({ label: coursListSae[i].code_cours, colspan: 1 });
    }


    let troisLigne = ['','','', 'PN'];
    for (let i = 0; i < colCours.length; i++) {
        troisLigne.push({ label: 'CM', colspan: 1 });
        troisLigne.push({ label: 'TD', colspan: 1 });
        troisLigne.push({ label: 'TP', colspan: 1 });
    }
    for (let i = 0; i < coursListSae.length; i++) {
        troisLigne.push({ label:coursListSae[i].nom_cours, colspan: 1 });
    }
    let quatreLigne = ['','','', ''];
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
        quatreLigne.push({label: coursListSae[j].nb_heures_total, colspan: 1});
    }

    let cinqLigne = ['','','', 'Heures totales Etudiants'];
    let sixLigne = ['','','', 'Heures totales Enseignants'];
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
        cinqLigne.push({label: "", colspan: 1});
        sixLigne.push({label: "", colspan: 1});
    }



    // Ajouter un en-tête pour la colonne de totaux
    premLigne.push({ label: 'Total', colspan: 1 });
    deuxLigne.push({ label: '', colspan: 1 });
    deuxDemiLigne.push({ label: '', colspan: 1 });
    troisLigne.push({ label: '', colspan: 1 });
    quatreLigne.push({ label: '', colspan: 1 });
    cinqLigne.push({ label: '', colspan: 1 });
    sixLigne.push({ label: '', colspan: 1 });


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
    let descriptions = [];
    for (let item of configData) {
        if (item.type === 'Description') {
            const descriptionDebut = new Date(item.dateDebut);
            const descriptionFin = new Date(item.dateFin);
            const descriptionWeekStart = getWeek(descriptionDebut);
            const descriptionWeekEnd = getWeek(descriptionFin);
            descriptions.push({
                dateDebut: descriptionDebut,
                dateFin: descriptionFin,
                description: item.description,
                weeks: [descriptionWeekStart, descriptionWeekEnd]
            });
        }
    }

    let dataT = [];
    let semaineActuelle = semaine;
    let colCoursTotaux = [];

    // Définir clairement les colonnes SAE
    let colonneSAE = [];
    for (let sae of coursListSae) {
        colonneSAE.push(sae.nom_cours);
    }

    // Ne pas modifier colCours ici, on garde la séparation
    // On ne veut pas ajouter les SAE à colCours

    for (let i = 0; i < nbSemaine; i++) {
        let estVacances = allVacances.includes(semaineActuelle);
        let dateActuelleStr = new Date(dateDebutSemestre).toLocaleDateString('fr-FR');
        let semaineData = [];
        let estDescription = descriptions.some(desc => desc.weeks.includes(semaineActuelle));
        let estEvenement = evenements.some(event => event.weeks.includes(semaineActuelle));

        // Gestion des colonnes fixes pour chaque semaine
        if (estVacances) {
            semaineData.push("", semaineActuelle, dateActuelleStr, "Vacances");
        } else if (estDescription) {
            semaineData.push("", semaineActuelle, dateActuelleStr, descriptions.find(desc => desc.weeks.includes(semaineActuelle)).description);
        } else if (estEvenement) {
            console.log("Evenement Trouvé pour semaine : " + semaineActuelle);
            semaineData.push("", semaineActuelle, dateActuelleStr, evenements.find(event => event.weeks.includes(semaineActuelle)).eventType);
        }
        else {
            semaineData.push("", semaineActuelle, dateActuelleStr, "");
        }

        // Construire la liste des colonnes pour les cours normaux (non SAE)
        for (let cours of colCours) {
            let valueCM = "";
            let valueTD = "";
            let valueTP = "";

            for (let rep of repartitionSansProf) {
                if (cours === rep.nom_cours && rep.semaine_debut <= semaineActuelle && rep.semaine_fin >= semaineActuelle) {
                    if (!estVacances) {
                        if (rep.type_heure === 'CM') {
                            valueCM = rep.nb_heures_par_semaine;
                        } else if (rep.type_heure === 'TD') {
                            valueTD = rep.nb_heures_par_semaine;
                        } else if (rep.type_heure === 'TP') {
                            valueTP = rep.nb_heures_par_semaine;
                        }
                    }
                }
            }

            // Ajouter les valeurs CM, TD, TP
            semaineData.push(valueCM !== 0 ? valueCM : '', valueTD !== 0 ? valueTD : '', valueTP !== 0 ? valueTP : '');
        }

        // Ajouter les colonnes pour les SAE séparément
        for (let sae of coursListSae) {
            let valueEI = "";

            for (let rep of repartitionSansProf) {
                if (sae.nom_cours === rep.nom_cours && rep.semaine_debut <= semaineActuelle && rep.semaine_fin >= semaineActuelle) {
                    if (!estVacances) {
                        valueEI = rep.nb_heures_par_semaine;
                    }
                }
            }

            semaineData.push(valueEI !== 0 ? valueEI : '');
        }

        dataT.push(semaineData);

        // Passer à la semaine suivante
        semaineActuelle = (semaineActuelle >= 52) ? 1 : semaineActuelle + 1;
        dateDebutSemestre.setDate(dateDebutSemestre.getDate() + 7);
    }

    // Calculer les index des colonnes pour les formules de somme
    const indexDebutColonnes = 4; // Premier index après les colonnes fixes
    const indexFinCours = indexDebutColonnes + (colCours.length * 3) - 1; // Dernier index des cours normaux
    const indexFinSae = indexFinCours + coursListSae.length; // Dernier index incluant les SAE

    // Lettres pour la formule de somme des cours (sans les SAE)
    let lettrestart = Handsontable.helper.spreadsheetColumnLabel(indexDebutColonnes);
    let lettreend = Handsontable.helper.spreadsheetColumnLabel(indexFinSae);

    // Ajouter une colonne de totaux à chaque ligne
    for (let i = 0; i < dataT.length; i++) {
        let totalFormula = `=SUM(${lettrestart}${i + 1}:${lettreend}${i + 1})`;
        dataT[i].push(totalFormula);
    }

    // Ajouter une ligne vide pour les totaux
    let totalRow = Array(dataT[0].length).fill('');
    totalRow[0] = 'Total';
    dataT.push(totalRow);

    let pendingChanges = []; // Stocke les changements en attente
    let debounceTimer = null; // Timer pour limiter les requêtes

    const container = document.querySelector('#example1');
    const hyperformulaInstance = HyperFormula.buildEmpty({
        // to use an external HyperFormula instance,
        // initialize it with the `'internal-use-in-handsontable'` license key
        licenseKey: 'internal-use-in-handsontable',
    });

    // Définir les propriétés des colonnes
    let columnsDefs = [];
    // Les 4 premières colonnes sont en lecture seule
    for (let i = 0; i < 4; i++) {
        columnsDefs.push({readOnly: true});
    }
    // Colonnes pour les cours normaux (3 colonnes par cours: CM, TD, TP)
    for (let i = 0; i < colCours.length; i++) {
        for (let j = 0; j < 3; j++) { // CM, TD, TP
            columnsDefs.push({readOnly: false});
        }
    }
    // Colonnes pour les SAE (1 colonne par SAE)
    for (let i = 0; i < coursListSae.length; i++) {
        columnsDefs.push({readOnly: false});
    }
    // Dernière colonne (total) en lecture seule
    columnsDefs.push({readOnly: true});

    // Ajouter une mise en forme conditionnelle pour mettre en rouge si > 32h (à gérer dans l'affichage)
    const planning = new Handsontable(container, {
        data: dataT,
        nestedHeaders: trueNH,
        stretchH: 'column',
        formulas: {
            engine: hyperformulaInstance,
        },
        columns: columnsDefs,
        wordWrap: true,
        licenseKey: 'non-commercial-and-evaluation',
        afterChange: (changes, source) => {
            if (source === 'loadData' || !changes) return;

            changes.forEach(([row, col, oldValue, newValue]) => {
                if (oldValue !== newValue) {
                    pendingChanges.push({ row, col, newValue });
                }
            });

            if (!debounceTimer) {
                debounceTimer = setTimeout(() => {
                    saveAllData(pendingChanges);
                    pendingChanges = [];
                    debounceTimer = null;
                }, 3000);
            }
        },
        cells: function(row, col) {
            const cellProperties = {};

            // Dernière ligne (Total) toujours en lecture seule
            if (row == dataT.length - 1) {
                cellProperties.readOnly = true;
            }

            // Mise en forme conditionnelle pour les vacances et autres périodes spéciales
            if (dataT[row]) {
                const cellValue = dataT[row][3]; // 4ème colonne (index 3)

                if (cellValue === "Vacances") {
                    cellProperties.className = 'vacation';
                    cellProperties.readOnly = true;
                }else if (cellValue !== "") {
                    const event = evenements.find(event => event.weeks.includes(dataT[row][1]));
                    if (event) {
                        const className = generateColorClass(event.couleur);
                        cellProperties.className = className;
                        cellProperties.readOnly = !!event.readOnly;
                    }
                }
            }

            // Mise en forme conditionnelle pour la colonne "Total"
            if (col === dataT[0].length - 1 && row < dataT.length - 1) { // Dernière colonne (Total)
                const cellAddress = { sheet: 0, row, col };
                const totalValue = hyperformulaInstance.getCellValue(cellAddress); // Obtenir la valeur calculée
                if (totalValue > 32) {
                    cellProperties.className = (cellProperties.className || '') + ' total-exceeded';
                } else {
                    cellProperties.className = (cellProperties.className || '') + ' total-normal';
                }
            }

            // Mise en forme conditionnelle pour la ligne "Total"
            if (row === dataT.length - 1 && col >= 4) { // Dernière ligne (Total) et colonnes des cours
                cellProperties.className = 'total-row';
            }

            return cellProperties;
        },
        afterGetColHeader: function() {
            const headerRows = container.querySelectorAll('.ht_clone_top thead tr');

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

    // Ajouter les formules des totaux en une seule opération
    const totalRowIndex = dataT.length - 1;
    const totalColIndex = dataT[0].length - 1;

    planning.batch(() => {
        // Pour chaque colonne de données (à partir de la colonne 4)
        for (let i = 4; i < totalColIndex; i++) {
            let totalFormula = `=SUM(${Handsontable.helper.spreadsheetColumnLabel(i)}1:${Handsontable.helper.spreadsheetColumnLabel(i)}${totalRowIndex})`;
            planning.setDataAtCell(totalRowIndex, i, totalFormula);
        }
    });

    function generateColorClass(color) {
        const className = `color-${color.replace("#", "")}`;

        if (!document.querySelector(`.${className}`)) {
            const style = document.createElement("style");
            style.innerHTML = `.${className} { background-color: ${color} !important; }`;
            document.head.appendChild(style);
        }

        return className;
    }

    function saveAllData() {
        // Préparez toutes les données pour l'envoi
        const repartitions = [];
        const descriptions = [];

        // Parcourir toutes les cellules modifiées et les ajouter à la liste sans la dernière colonne de total
        for (let rowIndex = 0; rowIndex < dataT.length - 1; rowIndex++) { // Exclure la dernière ligne
            const row = dataT[rowIndex];

            // Parcourir les colonnes des cours (sans SAE)
            let currentCol = 4; // Commencer à la colonne 4 (première colonne après la description)

            for (let colIndex = 0; colIndex < coursListSansSae.length; colIndex++) {
                // Pour chaque type d'heure (CM, TD, TP)
                ['CM', 'TD', 'TP'].forEach((typeHeure, typeIndex) => {
                    // Récupérer la valeur de la cellule
                    let colData = row[currentCol + typeIndex];
                    if (typeof colData === 'string' && colData.includes('=')) {
                        colData = null;
                    }

                    if (colData) {
                        const semaineDebut = row[1]; // Semaine de début (colonne dédiée)
                        const semaineFin = semaineDebut; // Par défaut, semaineFin = semaineDebut

                        // Ajout de la répartition dans le tableau
                        repartitions.push({
                            semaineDebut: semaineDebut,
                            semaineFin: semaineFin,
                            codeCours: coursListSansSae[colIndex].code_cours, // Code du cours directement depuis coursListSansSae
                            typeHeure: typeHeure, // Type d'heure (CM, TD, TP)
                            nbHeures: colData, // Nombre d'heures par semaine
                            semestre: semester // Semestre actuel
                        });
                    }
                });
                currentCol += 3; // Avancer de 3 colonnes (CM, TD, TP)
            }

            // Parcourir les colonnes des SAE
            for (let saeIndex = 0; saeIndex < coursListSae.length; saeIndex++) {
                let colData = row[currentCol + saeIndex]; // Colonne correspondante pour SAE
                if (typeof colData === 'string' && colData.includes('=')) {
                    colData = null;
                }

                if (colData) {
                    const semaineDebut = row[1]; // Semaine de début (colonne dédiée)
                    const semaineFin = semaineDebut; // Par défaut, semaineFin = semaineDebut

                    // Ajout de la répartition dans le tableau
                    repartitions.push({
                        semaineDebut: semaineDebut,
                        semaineFin: semaineFin,
                        codeCours: coursListSae[saeIndex].code_cours, // Utiliser le code du cours SAE
                        typeHeure: 'EI', // Type d'heure pour SAE
                        nbHeures: colData, // Nombre d'heures par semaine
                        semestre: semester // Semestre actuel
                    });
                }
            }

            // Traitement des descriptions
            if (row[3]) {
                const dateDebut = row[2].split('/').reverse().join('-'); // Convertir dd/mm/yyyy en yyyy-mm-dd
                const dateFin = new Date(dateDebut);
                dateFin.setDate(dateFin.getDate() + 6);
                descriptions.push({
                    dateDebut: dateDebut,
                    dateFin: dateFin.toISOString().split('T')[0],
                    description: row[3]
                });
            }
        }

        // Ajouter les descriptions
        sendRepartitionData(mergeRepartitions(repartitions), semester);
        sendDescriptionsData(descriptions, semester);
        toast.show('Données enregistrées avec succès', 'success');
    }




    function mergeRepartitions(repartitions) {
        // Trier les répartitions par codeCours, typeHeure, nbHeures, puis semaineDebut
        repartitions.sort((a, b) => {
            if (a.codeCours !== b.codeCours) {
                return a.codeCours.localeCompare(b.codeCours);
            }
            if (a.typeHeure !== b.typeHeure) {
                return a.typeHeure.localeCompare(b.typeHeure);
            }
            if (a.nbHeures !== b.nbHeures) {
                return a.nbHeures - b.nbHeures;
            }
            return a.semaineDebut - b.semaineDebut;
        });

        const mergedRepartitions = [];
        let currentRepartition = null;

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

        // Ajouter la dernière répartition si elle existe
        if (currentRepartition) {
            mergedRepartitions.push(currentRepartition);
        }

        return mergedRepartitions;
    }

    function sendRepartitionData(repartition,semester) {
        fetch('src/Gestionnaire/RequeteBD_UpdateRepartionsHeures.php', {
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

    function sendDescriptionsData(descriptions, semester) {
        fetch('src/Gestionnaire/RequeteBD_UpdateDescriptions.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json', // Assurez-vous que le serveur peut accepter des données JSON
            },
            body: JSON.stringify({ descriptions, semester })
        })
            .then(response => response.text())
            .then(data => {
                try {
                    const jsonData = JSON.parse(data); // Essayer de parser en JSON
                    if (jsonData.success) {
                        console.log("Descriptions insérées avec succès.");
                    } else {
                        console.error("Erreur lors de l'insertion des descriptions.");
                    }
                } catch (error) {
                    console.error("Erreur de parsing JSON :", error);
                }
            })
            .catch(error => {
                console.error("Erreur:", error);
            });
    }

    let eventTexts = evenements.map(event => event.checkbox === 0 ? event.eventType : "");
    //Enlever les valeurs vides
    eventTexts = eventTexts.filter(eventText => eventText !== "");
    container.addEventListener('wheel', (event) => {

        event.preventDefault(); // Empêche le défilement de la page
        const selectedRanges = planning.getSelected();
        if (!selectedRanges) return;
        //Texts des evenements si checkbox = 0
        console.log(eventTexts);
        // Utiliser batch pour regrouper les modifications
        planning.batch(() => {
            selectedRanges.forEach(([startRow, startCol, endRow, endCol]) => {
                for (let row = startRow; row <= endRow; row++) {
                    for (let col = startCol; col <= endCol; col++) {
                        if (dataT[row] && (dataT[row][3] !== "Vacances" ) && !eventTexts.includes(dataT[row][3]) && col >= 4 && col < totalColIndex && row < totalRowIndex) {
                            let currentValue = parseInt(planning.getDataAtCell(row, col) || '0', 10);
                            const delta = event.deltaY > 0 ? -1 : 1; // -1 pour scroll bas, +1 pour scroll haut
                            currentValue = Math.max(0, currentValue + delta); // Ajuster la valeur sans descendre en dessous de 0
                            planning.setDataAtCell(row, col, currentValue); // Mettre à jour la cellule
                        }
                    }
                }
            });
        });
    });


    const select = document.querySelector('#semester');
    select.addEventListener('change', () => {
        const semester = select.value;
        window.location.href = `index.php?action=ficheDetaille&semester=${semester}`;
    });

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


</script>
</html>
