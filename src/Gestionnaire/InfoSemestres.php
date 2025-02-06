<div id="main-content">
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
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label>Stage S4 - Début</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Stage S4 - Fin</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Remarque</label>
                        <input type="text" class="form-control">
                    </div>
                </div>
                <div class="row mt-2">
                    <div class="col-md-3">
                        <label>Stage S6 - Début</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Stage S6 - Fin</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label>Remarque</label>
                        <input type="text" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <!-- Ateliers -->
<!--
        <div class="card mb-3" id="atelierSection">
            <div class="card-header">
                <h5 class="card-title">Dates des ateliers</h5>
                <button type="button" class="btn btn-primary" id="ajouterAtelier">Ajouter un atelier</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label>Semestre</label>
                        <select class="form-control">
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="S4 DACS">S4 DACS</option>
                            <option value="S4 RA-DWM">S4 RA-DWM</option>
                            <option value="S4 RA-IL">S4 RA-IL</option>
                            <option value="S5-S6 DACS">S5-S6 DACS</option>
                            <option value="S5-S6 RA-DWM">S5-S6 RA-DWM</option>
                            <option value="S5-S6 RA-IL">S5-S6 RA-IL</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Atelier - Début</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Atelier - Fin</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Remarque</label>
                        <input type="text" class="form-control">
                    </div>
                </div>
            </div>
        </div>

        <div class="card mb-3" id="projetSection">
            <div class="card-header">
                <h5 class="card-title">Dates des projets</h5>
                <button type="button" class="btn btn-primary" id="ajouterProjet">Ajouter un projet</button>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3">
                        <label>Semestre</label>
                        <select class="form-control">
                            <option value="S1">S1</option>
                            <option value="S2">S2</option>
                            <option value="S3">S3</option>
                            <option value="S4 DACS">S4 DACS</option>
                            <option value="S4 RA-DWM">S4 RA-DWM</option>
                            <option value="S4 RA-IL">S4 RA-IL</option>
                            <option value="S5-S6 DACS">S5-S6 DACS</option>
                            <option value="S5-S6 RA-DWM">S5-S6 RA-DWM</option>
                            <option value="S5-S6 RA-IL">S5-S6 RA-IL</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>Projet - Début</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Projet - Fin</label>
                        <input type="date" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label>Remarque</label>
                        <input type="text" class="form-control">
                    </div>
                </div>
            </div>
        </div>
    </div>-->
    <button type="button" class="btn btn-success" id="enregistrer">Enregistrer</button>
    </div>
</div>




    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>

<script>
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
                    }
                });
            })
            .catch(error => console.error("Erreur lors de la récupération des données:", error));
    });

    document.getElementById("enregistrer").addEventListener("click", function () {
        let data = [];

        function addEntry(type, dateDebut, dateFin, description) {
            data.push({
                type: type,
                dateDebut: dateDebut || null,
                dateFin: dateFin || null,
                description: description || ""
            });
        }

        // Récupération des valeurs
        addEntry("Semestre1", document.getElementById("debutSemestreImpair").value, document.getElementById("finSemestreImpair").value, "");
        addEntry("Semestre2", document.getElementById("debutSemestrePair").value, document.getElementById("finSemestrePair").value, "");
        addEntry("VacancesToussaint", document.getElementById("debutToussaint").value, document.getElementById("finToussaint").value, document.getElementById("remarqueToussaint").value);
        addEntry("VacancesNoel", document.getElementById("debutNoel").value, document.getElementById("finNoel").value, document.getElementById("remarqueNoel").value);
        addEntry("VacancesHiver", document.getElementById("debutHiver").value, document.getElementById("finHiver").value, document.getElementById("remarqueHiver").value);
        addEntry("VacancesPrintemps", document.getElementById("debutPrintemps").value, document.getElementById("finPrintemps").value, document.getElementById("remarquePrintemps").value);

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
                alert("Données enregistrées avec succès!");
                console.log(result);
            })
            .catch(error => {
                console.error("Erreur lors de l'enregistrement:", error);
                alert("Une erreur est survenue lors de l'enregistrement des données.");
            });
    });

</script>