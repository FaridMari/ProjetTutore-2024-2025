<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Affichage des Semestres</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />

    <link rel="stylesheet" href="css/InfoSemestreStyle.css">
</head>

<body>

<style>

</style>

<div class="container" id="container">
    <div id="infoContainer" class="mt-4">
        <!-- Informations générales -->
        <div class="card mb-3">
            <div class="card-header">
                <h5 class="card-title">Dates générales du semestre</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-3"><strong></strong></div>
                    <div class="col-md-3"><strong>Date début</strong></div>
                    <div class="col-md-3"><strong>Date fin</strong></div>
                    <div class="col-md-3"><strong>Remarque</strong></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Semestre Impair:</strong></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="debutSemestrePair"></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="finSemestrePair"></div>

                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Semestre Pair:</strong></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="debutSemestreImpair"></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="finSemestreImpair"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Vacances de la Toussaint:</strong></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="debutToussaint"></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="finToussaint"></div>
                    <div class="col-md-3"><input type="text" class="form-control" id="remarqueToussaint"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Vacances de Noël:</strong></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="debutNoel"></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="finNoel"></div>
                    <div class="col-md-3"><input type="text" class="form-control" id="remarqueNoel"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Vacances d'Hiver:</strong></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="debutHiver"></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="finHiver"></div>
                    <div class="col-md-3"><input type="text" class="form-control" id="remarqueHiver"></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-3"><strong>Vacances de Printemps:</strong></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="debutPrintemps"></div>
                    <div class="col-md-3"><input type="date" class="form-control" id="finPrintemps"></div>
                    <div class="col-md-3"><input type="text" class="form-control" id="remarquePrintemps"></div>
                </div>
            </div>
        </div>

        <!-- Stages -->
        <div class="card mb-3" id="stageSection">
            <div class="card-header">
                <h5 class="card-title">Dates des stages</h5>
                <button type="button" class="btn btn-primary" id="ajouterStage">Ajouter un stage</button>
            </div>
            <div class="card-body" id="stageContainer">
                <!-- Les lignes de stages seront ajoutées ici -->
            </div>
        </div>

        <!-- Ateliers -->
        <div class="card mb-3" id="atelierSection">
            <div class="card-header">
                <h5 class="card-title">Dates des ateliers</h5>
                <button type="button" class="btn btn-primary" id="ajouterAtelier">Ajouter un atelier</button>
            </div>
            <div class="card-body" id="atelierContainer">
                <!-- Les lignes d'ateliers seront ajoutées ici -->
            </div>
        </div>

        <div class="card mb-3" id="projetSection">
            <div class="card-header">
                <h5 class="card-title">Dates des projets</h5>
                <button type="button" class="btn btn-primary" id="ajouterProjet">Ajouter un projet</button>
            </div>
            <div class="card-body" id="projetContainer">
                <!-- Les lignes de projets seront ajoutées ici -->
            </div>
        </div>

        <button type="button" class="btn btn-success" id="enregistrer">Enregistrer</button>
    </div>
</div>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

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
            }, 3000); // Le toast disparaît après 3 secondes
        }
    }

    const toast = new Toast();
    document.addEventListener("DOMContentLoaded", function () {
        fetch("src/Gestionnaire/get_info_semestres.php")
            .then(response => response.json())
            .then(data => {
                data.forEach(item => {
                    switch (item.type) {
                        case "Semestre1":
                            document.getElementById("debutSemestreImpair").value = item.dateDebut;
                            document.getElementById("finSemestreImpair").value = item.dateFin;
                            break;
                        case "Semestre2":
                            document.getElementById("debutSemestrePair").value = item.dateDebut;
                            document.getElementById("finSemestrePair").value = item.dateFin;
                            break;
                        case "VacancesToussaint":
                            document.getElementById("debutToussaint").value = item.dateDebut;
                            document.getElementById("finToussaint").value = item.dateFin;
                            document.getElementById("remarqueToussaint").value = item.description;
                            break;
                        case "VacancesNoel":
                            document.getElementById("debutNoel").value = item.dateDebut;
                            document.getElementById("finNoel").value = item.dateFin;
                            document.getElementById("remarqueNoel").value = item.description;
                            break;
                        case "VacancesHiver":
                            document.getElementById("debutHiver").value = item.dateDebut;
                            document.getElementById("finHiver").value = item.dateFin;
                            document.getElementById("remarqueHiver").value = item.description;
                            break;
                        case "VacancesPrintemps":
                            document.getElementById("debutPrintemps").value = item.dateDebut;
                            document.getElementById("finPrintemps").value = item.dateFin;
                            document.getElementById("remarquePrintemps").value = item.description;
                            break;
                        case "Atelier":
                            addRow("atelierContainer", "Atelier", item);
                            break;
                        case "Stage":
                            addRow("stageContainer", "Stage", item);
                            break;
                        case "Projet":
                            addRow("projetContainer", "Projet", item);
                            break;
                    }
                });
            })
            .catch(error => console.error("Erreur lors de la récupération des données:", error));
    });

    let data = [];
    function addEntry(semestre , type, dateDebut, dateFin, description) {
        data.push({
            semestre:semestre,
            type: type,
            dateDebut: dateDebut || null,
            dateFin: dateFin || null,
            description: description || ""
        });
    }

    document.getElementById("enregistrer").addEventListener("click", function () {
        // Récupération des valeurs fixes
        addEntry(null,"Semestre1", document.getElementById("debutSemestreImpair").value, document.getElementById("finSemestreImpair").value, "");
        addEntry(null,"Semestre2", document.getElementById("debutSemestrePair").value, document.getElementById("finSemestrePair").value, "");
        addEntry(null,"VacancesToussaint", document.getElementById("debutToussaint").value, document.getElementById("finToussaint").value, document.getElementById("remarqueToussaint").value);
        addEntry(null,"VacancesNoel", document.getElementById("debutNoel").value, document.getElementById("finNoel").value, document.getElementById("remarqueNoel").value);
        addEntry(null,"VacancesHiver", document.getElementById("debutHiver").value, document.getElementById("finHiver").value, document.getElementById("remarqueHiver").value);
        addEntry(null,"VacancesPrintemps", document.getElementById("debutPrintemps").value, document.getElementById("finPrintemps").value, document.getElementById("remarquePrintemps").value);

        // Récupération des valeurs dynamiques
        collectDynamicData("atelierContainer", "Atelier");
        collectDynamicData("stageContainer", "Stage");
        collectDynamicData("projetContainer", "Projet");

        console.log(data);
        fetch("src/gestionnaire/update_info_semestres.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(result => {
                console.log(result);
                toast.show("Les données ont été enregistrées avec succès", "success");
            })
            .catch(error => {
                console.error("Erreur lors de l'enregistrement:", error);
                toast.show("Une erreur s'est produite lors de l'enregistrement", "error");
            });
    });

    function collectDynamicData(containerId, type) {
        const container = document.getElementById(containerId);
        const rows = container.querySelectorAll(".row");

        rows.forEach(row => {
            const semestre = row.querySelector("select.form-control").value;
            const dateDebut = row.querySelector("input[type='date']").value;
            const dateFin = row.querySelectorAll("input[type='date']")[1].value;
            const description = row.querySelector("input[type='text']").value;

            addEntry(semestre,type, dateDebut, dateFin, description);
        });
    }


    document.getElementById("ajouterAtelier").addEventListener("click", function () {
        addRow("atelierContainer", "Atelier", {});
    });

    document.getElementById("ajouterProjet").addEventListener("click", function () {
        addRow("projetContainer", "Projet", {});
    });

    document.getElementById("ajouterStage").addEventListener("click", function () {
        addRow("stageContainer", "Stage", {});
    });

    function addRow(containerId, type, item) {
        const container = document.getElementById(containerId);
        //Ajouter remarque vide si elle n'existe pas
        if (!item.description) {
            item.description = "";
        }

        // Créer une nouvelle ligne
        const row = document.createElement("div");
        row.classList.add("row", "mb-3");

        // Ajouter les colonnes pour le semestre, les dates et la remarque
        row.innerHTML = `
            <div class="col-md-3">
                <label>Semestre</label>
                <select class="form-control">
                    <option value="S1" ${item.semestre === "S1" ? "selected" : ""}>S1</option>
                    <option value="S2" ${item.semestre === "S2" ? "selected" : ""}>S2</option>
                    <option value="S3" ${item.semestre === "S3" ? "selected" : ""}>S3</option>
                    <option value="S4 DACS" ${item.semestre === "S4 DACS" ? "selected" : ""}>S4 DACS</option>
                    <option value="S4 RA-DWM" ${item.semestre === "S4 RA-DWM" ? "selected" : ""}>S4 RA-DWM</option>
                    <option value="S4 RA-IL" ${item.semestre === "S4 RA-IL" ? "selected" : ""}>S4 RA-IL</option>
                    <option value="S5-S6 DACS" ${item.semestre === "S5-S6 DACS" ? "selected" : ""}>S5-S6 DACS</option>
                    <option value="S5-S6 RA-DWM" ${item.semestre === "S5-S6 RA-DWM" ? "selected" : ""}>S5-S6 RA-DWM</option>
                    <option value="S5-S6 RA-IL" ${item.semestre === "S5-S6 RA-IL" ? "selected" : ""}>S5-S6 RA-IL</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>${type} - Début</label>
                <input type="date" class="form-control" value="${item.dateDebut}">
            </div>
            <div class="col-md-3">
                <label>${type} - Fin</label>
                <input type="date" class="form-control" value="${item.dateFin}">
            </div>
            <div class="col-md-3">
                <label>Remarque</label>
                <input type="text" class="form-control" value="${item.description}">
            </div>
        `;

        // Ajouter la ligne au conteneur
        container.appendChild(row);
    }


</script>


</body>
</html>
