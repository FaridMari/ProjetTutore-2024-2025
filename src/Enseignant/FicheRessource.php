

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Ressource</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width: 60%; margin-bottom: 2%">
    <h1 class="text-center">Fiche Ressource : Emploi du Temps 2024-2025</h1>
    <p class="text-center text-danger">À remplir par le responsable de la ressource</p>

    <form method="post" action="src/Enseignant/traitement.php">
        <div class="mb-3">
            <label for="semester" class="form-label">Semestre :</label>
            <select class="form-select" id="semester" name="semester" required>
                <option value="S1">S1</option>
                <option value="S2">S2</option>
                <option value="S3">S3</option>
                <option value="S4-IL">S4-IL</option>
                <option value="S4-DWM">S4-DWM</option>
                <option value="S4-DACS">S4-DACS</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="resourceName" class="form-label">Nom de la ressource :</label>
            <select class="form-select" id="resourceName" name="resourceName">
                <option value="">-- Sélectionner un nom de ressource --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="resourceCode" class="form-label">Code de la ressource :</label>
            <select class="form-select" id="resourceCode" name="resourceCode" required>
                <option value="">-- Sélectionner un code de ressource --</option>
            </select>
        </div>

        <div class="mb-3">
            <label for="responsibleName" class="form-label">Nom du responsable :</label>
            <input type="text" class="form-control" id="responsibleName" name="responsibleName" placeholder="Nom du responsable" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Téléphone :</label>
            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Numéro de téléphone">
        </div>


        <h4>1. Répartition des heures par semaines :</h4>
        <div id="dynamic-hours-container" class="mb-3">

            <div class="text-center" style="margin-top: 3%">
                <button type="button" class="btn btn-success mb-3" id="add-hour-btn">+ Ajouter une répartition</button>
            </div>

            <div class="dynamic-hour-row mb-2">
                <div class="row gx-2 align-items-end">
                    <div class="col-md-2">
                        <label class="form-label text-center d-block">Semaine début</label>
                        <input type="number" class="form-control" name="week_start[]" placeholder="36" min="1" max="52" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-center d-block">Semaine fin</label>
                        <input type="number" class="form-control" name="week_end[]" placeholder="40" min="1" max="52" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-center d-block">Type d'heure</label>
                        <select class="form-select" name="hour_type[]">
                            <option value="CM">CM</option>
                            <option value="CM">CM</option>
                            <option value="TD">TD</option>
                            <option value="TP">TP</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-center d-block">Heures/sem</label>
                        <input type="number" class="form-control hour-per-week" name="hours_per_week[]" placeholder="4" min="1" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label text-center d-block">Total</label>
                        <input type="text" class="form-control total-hours" name="total_hours[]" placeholder="0" readonly>
                    </div>
                    <div class="col-md-1 text-center">
                        <button type="button" class="btn btn-danger remove-row-btn">
                            <i class="bi bi-x-circle"></i> Supprimer
                        </button>
                    </div>
                    
                </div>
                <hr>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const semesterSelect = document.getElementById('semester');
                const resourceNameSelect = document.getElementById('resourceName');
                const resourceCodeSelect = document.getElementById('resourceCode');
                let courseData = []; // Stocke les données récupérées

                // Fonction pour récupérer et afficher les cours
                function fetchAndDisplayCourses(semester) {
                    // Ajoute 'S' devant le semestre si nécessaire (S1, S2...)
                    const formattedSemester = `S${semester}`;

                    // Réinitialise les listes
                    resourceNameSelect.innerHTML = '<option value="">-- Sélectionner un nom de ressource --</option>';
                    resourceCodeSelect.innerHTML = '<option value="">-- Sélectionner un code de ressource --</option>';

                    if (semester) {
                        fetch(`src/Enseignant/get_cours.php?semester=${semester}`)
                            .then(response => {
                                if (!response.ok) throw new Error(`Erreur HTTP ! statut : ${response.status}`);
                                return response.json();
                            })
                            .then(data => {
                                if (data.error) {
                                    console.error('Erreur :', data.error);
                                } else {
                                    courseData = data; // Stocke les cours récupérés
                                    data.forEach(course => {
                                        const optionName = document.createElement('option');
                                        optionName.value = course.nom_cours;
                                        optionName.textContent = course.nom_cours;
                                        resourceNameSelect.appendChild(optionName);

                                        const optionCode = document.createElement('option');
                                        optionCode.value = course.code_cours; // Utilise code_cours au lieu de id_cours
                                        optionCode.textContent = course.code_cours;
                                        resourceCodeSelect.appendChild(optionCode);
                                    });
                                }
                            })
                            .catch(error => console.error('Erreur lors de la récupération des cours :', error));
                    }
                }

                // Synchronise le code ressource lorsque le nom est sélectionné
                resourceNameSelect.addEventListener('change', function () {
                    const selectedName = this.value;
                    const matchedCourse = courseData.find(course => course.nom_cours === selectedName);
                    if (matchedCourse) resourceCodeSelect.value = matchedCourse.code_cours; // Mise à jour avec code_cours
                });

                // Synchronise le nom de ressource lorsque le code est sélectionné
                resourceCodeSelect.addEventListener('change', function () {
                    const selectedCode = this.value;
                    const matchedCourse = courseData.find(course => course.code_cours === selectedCode);
                    if (matchedCourse) resourceNameSelect.value = matchedCourse.nom_cours;
                });

                // Appel initial lors du chargement de la page
                fetchAndDisplayCourses(semesterSelect.value);

                // Appel lors du changement de semestre
                semesterSelect.addEventListener('change', function () {
                    fetchAndDisplayCourses(this.value);
                });
            });


            document.addEventListener('DOMContentLoaded', function () {
                const addHourBtn = document.getElementById('add-hour-btn');
                const container = document.getElementById('dynamic-hours-container');

                // Fonction pour ajouter une nouvelle ligne
                addHourBtn.addEventListener('click', () => {
                    const template = document.querySelector('.dynamic-hour-row').cloneNode(true);

                    // Réinitialiser les valeurs des inputs dans la nouvelle ligne
                    template.querySelectorAll('input').forEach(input => input.value = '');
                    template.querySelector('.total-hours').value = '0';

                    container.appendChild(template);
                });

                // Fonction pour supprimer une ligne
                container.addEventListener('click', (event) => {
                    if (event.target.classList.contains('remove-row-btn')) {
                        event.target.closest('.dynamic-hour-row').remove();
                    }
                });

                // Calcul dynamique du total d'heures
                container.addEventListener('input', (event) => {
                    if (event.target.classList.contains('hour-per-week') ||
                        event.target.name === 'week_start[]' ||
                        event.target.name === 'week_end[]') {
                        const row = event.target.closest('.dynamic-hour-row');
                        const weekStart = parseInt(row.querySelector('input[name="week_start[]"]').value) || 0;
                        const weekEnd = parseInt(row.querySelector('input[name="week_end[]"]').value) || 0;
                        const hoursPerWeek = parseFloat(row.querySelector('.hour-per-week').value) || 0;

                        const totalWeeks = Math.max(0, weekEnd - weekStart + 1);
                        const totalHours = totalWeeks * hoursPerWeek;

                        row.querySelector('.total-hours').value = totalHours.toFixed(2);
                    }
                });
            });

        </script>


        <!-- Répartition des heures -->
        <!--<div style="margin-left: 2%">
            <h5>Autres spécifications...</h5>
            <div class="mb-3">
                <textarea class="form-control" id="tdTpDetails" name="tdTpDetails" rows="3" placeholder="Exemple : S45 à S48 : Maximum 2 jours entre chaque séance" required></textarea>
            </div>
        </div>-->


        <!-- Réservations DS -->
        <h4>2. Réservations DS :</h4>
        <div class="mb-3">
            <label for="dsDetails" class="form-label">Détail des réservations :</label>
            <textarea class="form-control" id="dsDetails" name="dsDetails" rows="3" placeholder="Indiquez les semaines et la durée pour chaque DS" required></textarea>
        </div>

        <!-- Salles 016 -->
        <h4>3. Salles 016 :</h4>
        <div class="mb-3">
            <label class="form-label">Souhaitez-vous intervenir dans la salle 016 ?</label>
            <div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="salle016" id="prefOui" value="Oui" required>
                    <label class="form-check-label" for="prefOui">Oui, de préférence</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="salle016" id="prefIndiff" value="Indifférent" required>
                    <label class="form-check-label" for="prefIndiff">Indifférent</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="salle016" id="prefNon" value="Non" required>
                    <label class="form-check-label" for="prefNon">Non, salle non adaptée</label>
                </div>
            </div>
        </div>

        <!-- Besoins en salles informatiques -->
        <h4>4. Besoins en chariots ou salles informatiques :</h4>
        <div class="mb-3">
            <label class="form-label">Système souhaité :</label>
            <div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="system" id="windows" value="Windows" required>
                    <label class="form-check-label" for="windows">Windows</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="system" id="linux" value="Linux" required>
                    <label class="form-check-label" for="linux">Linux</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="system" id="indiff" value="Indifférent" required>
                    <label class="form-check-label" for="indiff">Indifférent</label>
                </div>
            </div>
        </div>
        <div class="mb-3">
            <label for="scheduleDetails" class="form-label">Période et nombre d'heures par semaine :</label>
            <input type="text" class="form-control" id="scheduleDetails" name="scheduleDetails" placeholder="Exemple : S36 à S39 : 2h">
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


