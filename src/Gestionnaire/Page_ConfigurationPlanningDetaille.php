<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Configuration</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/InfoSemestreStyle.css">
</head>
<body>
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

        <!-- Événements -->
        <div class="card mb-3" id="eventSection">
            <div class="card-header">
                <h5 class="card-title">Dates des événements</h5>
                <button type="button" class="btn btn-primary" id="ajouterEvenement">Ajouter un événement</button>
            </div>
            <div class="card-body" id="eventContainer">
                <!-- Les lignes d'événements seront ajoutées ici -->
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
        fetch("src/Gestionnaire/RequeteBD_GetConfigurationPlanningDetaille.php")
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

                        default:
                            if (!["VacancesToussaint", "VacancesHiver", "VacancesPrintemps", "VacancesNoel", "Semestre1", "Semestre2", "Description"].includes(item.type)) {
                                addRow("eventContainer", item);
                            }
                            break;
                    }
                });
            })
            .catch(error => console.error("Erreur lors de la récupération des données:", error));
    });

    let data = [];

    function addEntry(semestre, type, dateDebut, dateFin, description, titre, couleur, checkbox) {
        data.push({
            semestre: semestre,
            type: type,
            dateDebut: dateDebut || null,
            dateFin: dateFin || null,
            description: description || "",
            titre: titre || "",
            couleur: couleur || "",
            checkbox: checkbox || false
        });
    }

    document.getElementById("enregistrer").addEventListener("click", function () {
        // Vider la liste data avant de collecter les données
        data = [];

        // Récupération des valeurs fixes
        addEntry(null, "Semestre1", document.getElementById("debutSemestreImpair").value, document.getElementById("finSemestreImpair").value, "", "", "", false);
        addEntry(null, "Semestre2", document.getElementById("debutSemestrePair").value, document.getElementById("finSemestrePair").value, "", "", "", false);
        addEntry(null, "VacancesToussaint", document.getElementById("debutToussaint").value, document.getElementById("finToussaint").value, document.getElementById("remarqueToussaint").value, "", "", false);
        addEntry(null, "VacancesNoel", document.getElementById("debutNoel").value, document.getElementById("finNoel").value, document.getElementById("remarqueNoel").value, "", "", false);
        addEntry(null, "VacancesHiver", document.getElementById("debutHiver").value, document.getElementById("finHiver").value, document.getElementById("remarqueHiver").value, "", "", false);
        addEntry(null, "VacancesPrintemps", document.getElementById("debutPrintemps").value, document.getElementById("finPrintemps").value, document.getElementById("remarquePrintemps").value, "", "", false);

        // Récupération des valeurs dynamiques
        collectDynamicData("eventContainer");

        console.log(data);
        fetch("src/gestionnaire/RequeteBD_UpdateConfigurationPlanningDetaille.php", {
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

    function collectDynamicData(containerId) {
        const container = document.getElementById(containerId);
        const rows = container.querySelectorAll(".row");

        rows.forEach(row => {
            const semestre = row.querySelector("select.form-control").value;
            const dateDebut = row.querySelector("input[type='date']").value;
            const dateFin = row.querySelectorAll("input[type='date']")[1].value;
            const description = row.querySelector("input[type='text']").value;
            const type = row.querySelector("input[type='text'].titre").value;
            const couleur = row.querySelector("input[type='color']").value;
            const modifiable = row.querySelector("input[type='checkbox']").checked;

            addEntry(semestre, type, dateDebut, dateFin, description, "", couleur,modifiable );
        });
    }

    document.getElementById("ajouterEvenement").addEventListener("click", function () {
        addRow("eventContainer", {});
    });

    function addRow(containerId, item) {
        const container = document.getElementById(containerId);
        // Ajouter remarque vide si elle n'existe pas
        if (!item.description) {
            item.description = "";
        }
        if (!item.type) {
            item.type = "";
        }
        if (!item.couleur) {
            item.couleur = "#ffffff";
        }
        if (!item.checkbox || item.checkbox === "0") {
            item.checkbox = false;
        }else{
            item.checkbox = true;
        }

        // Créer une nouvelle ligne
        const row = document.createElement("div");
        row.classList.add("row", "mb-3");

        // Ajouter les colonnes pour le semestre, les dates, la remarque, le titre, la couleur et la checkbox
        row.innerHTML = `
            <div class="col-md-3">
                <label>Semestre</label>
                <select class="form-control">
                    <option value="S1" ${item.semestre === "S1" ? "selected" : ""}>S1</option>
                    <option value="S2" ${item.semestre === "S2" ? "selected" : ""}>S2</option>
                    <option value="S3" ${item.semestre === "S3" ? "selected" : ""}>S3</option>
                    <option value="S4 DACS" ${item.semestre === "S4 DACS" ? "selected" : ""}>S4 DACS</option>
                    <option value="S5 DACS" ${item.semestre === "S5 DACS" ? "selected" : ""}>S5 DACS</option>
                    <option value="S6 DACS" ${item.semestre === "S6 DACS" ? "selected" : ""}>S6 DACS</option>
                    <option value="S4 RA-DWM" ${item.semestre === "S4 RA-DWM" ? "selected" : ""}>S4 RA-DWM</option>
                    <option value="S5 RA-DWM" ${item.semestre === "S5 RA-DWM" ? "selected" : ""}>S5 RA-DWM</option>
                    <option value="S6 RA-DWM" ${item.semestre === "S6 RA-DWM" ? "selected" : ""}>S6 RA-DWM</option>
                    <option value="S4 RA-IL" ${item.semestre === "S4 RA-IL" ? "selected" : ""}>S4 RA-IL</option>
                    <option value="S5 RA-IL" ${item.semestre === "S5 RA-IL" ? "selected" : ""}>S5 RA-IL</option>
                    <option value="S6 RA-IL" ${item.semestre === "S6 RA-IL" ? "selected" : ""}>S6 RA-IL</option>
                </select>
            </div>
            <div class="col-md-3">
                <label>Événement - Début</label>
                <input type="date" class="form-control" value="${item.dateDebut}">
            </div>
            <div class="col-md-3">
                <label>Événement - Fin</label>
                <input type="date" class="form-control" value="${item.dateFin}">
            </div>
            <div class="col-md-3">
                <label>Remarque</label>
                <input type="text" class="form-control" value="${item.description}">
            </div>
            <div class="col-md-3">
                <label>Titre</label>
                <input type="text" class="form-control titre" value="${item.type}">
            </div>
            <div class="col-md-3">
                <label>Couleur</label>
                <input type="color" class="form-control " value="${item.couleur}" style="padding: 0em !important; height: 50% !important;">
            </div>
            <div class="col-md-1">
                <label>Checkbox</label>
                <input type="checkbox" class="form-control" ${item.checkbox ? "checked" : ""} style="padding: 1em !important; height: 30% !important;">
            </div>
        `;
        // Ajouter la ligne au conteneur
        container.appendChild(row);
    }
</script>
</body>
</html>
