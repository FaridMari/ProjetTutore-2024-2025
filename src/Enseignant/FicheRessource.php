<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Ressource</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="src/Enseignant/style_FicheRessource.css">
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
        <div id="hours-distribution" class="repartition-container mb-3">
            <!-- insertions des répartitions -->
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const semesterSelect = document.getElementById('semester');
                const resourceNameSelect = document.getElementById('resourceName');
                const resourceCodeSelect = document.getElementById('resourceCode');
                const courseNameCode = document.getElementById('courseNameCode');
                const hoursDistribution = document.getElementById('hours-distribution');
                const noDataMessage = 'Aucune donnée disponible.';
                let courseData = [];

                // Fonction pour récupérer et afficher les cours
                function fetchAndDisplayCourses(semester) {
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
                                    courseData = data; // cours récupérés
                                    data.forEach(course => {
                                        const optionName = document.createElement('option');
                                        optionName.value = course.nom_cours;
                                        optionName.textContent = course.nom_cours;
                                        resourceNameSelect.appendChild(optionName);

                                        const optionCode = document.createElement('option');
                                        optionCode.value = course.code_cours;
                                        optionCode.textContent = course.code_cours;
                                        resourceCodeSelect.appendChild(optionCode);
                                    });
                                }
                            })
                            .catch(error => console.error('Erreur lors de la récupération des cours :', error));
                    }
                }

                // Fonction pour récupérer et afficher les répartitions
                function fetchAndDisplayRepartitions(nomCours) {
                    if (!nomCours) {
                        updateRepartitionFields([]);
                        return;
                    }

                    fetch(`src/Enseignant/get_repartitions.php?nom_cours=${encodeURIComponent(nomCours)}`)
                        .then(response => {
                            if (!response.ok) throw new Error(`Erreur HTTP ! statut : ${response.status}`);
                            return response.json();
                        })
                        .then(data => {
                            if (data.error) {
                                console.error('Erreur :', data.error);
                                updateRepartitionFields([]);
                            } else {
                                updateRepartitionFields(data);
                                console.log(data); // donnees dans la console
                            }
                        })
                        .catch(error => console.error('Erreur lors de la récupération des répartitions :', error));
                }

                // met à jour les champs de répartition
                function updateRepartitionFields(repartitions) {
                    // Récupère le nom et le code de la ressource depuis le DOM
                    const resourceNameElement = document.getElementById('resourceName');
                    const resourceCodeElement = document.getElementById('resourceCode');
                    const resourceName = resourceNameElement.options[resourceNameElement.selectedIndex]?.text || 'N/A';
                    const resourceCode = resourceCodeElement.options[resourceCodeElement.selectedIndex]?.text || 'N/A';

                    const hoursDistribution = document.getElementById('hours-distribution');

                    const parent = hoursDistribution.parentElement;

                    // efface ce qu'il y a avant
                    hoursDistribution.innerHTML = '';

                    const existingCourseInfo = document.getElementById('course-overview');
                    if (existingCourseInfo) {
                        existingCourseInfo.remove();
                    }

                    const courseInfoDiv = document.createElement('div');
                    courseInfoDiv.id = 'course-overview';
                    courseInfoDiv.classList.add('mb-3');
                    courseInfoDiv.innerHTML = `
        <p><strong>Ressource :</strong> <span>${resourceName}</span></p>
        <p><strong>Code ressource :</strong> <span>${resourceCode}</span></p>
    `;
                    parent.insertBefore(courseInfoDiv, hoursDistribution);

                    if (repartitions.length === 0) {
                        hoursDistribution.innerHTML = '<p>Aucune répartition disponible.</p>';
                        return;
                    }

                    repartitions.forEach((repartition, index) => {
                        const repartitionDiv = document.createElement('div');
                        repartitionDiv.classList.add('repartition-entry', 'fade-in');
                        repartitionDiv.innerHTML = `
            <p><strong>Semaine de début :</strong> <span>${repartition.semaine_debut || ''}</span></p>
            <p><strong>Semaine de fin :</strong> <span>${repartition.semaine_fin || ''}</span></p>
            <p><strong>Type d'heure :</strong> <span>${repartition.type_heure || ''}</span></p>
            <p><strong>Heures par semaine :</strong> <span>${repartition.nb_heures_par_semaine || ''}</span></p>
        `;
                        hoursDistribution.appendChild(repartitionDiv);
                    });
                }

                // Synchronise le code ressource lorsque le nom est sélectionné
                resourceNameSelect.addEventListener('change', function () {
                    const selectedName = this.value;
                    const matchedCourse = courseData.find(course => course.nom_cours === selectedName);
                    if (matchedCourse) {
                        resourceCodeSelect.value = matchedCourse.code_cours;
                        if (courseNameCode) {
                            courseNameCode.textContent = `${matchedCourse.nom_cours} (${matchedCourse.code_cours})`;
                        }
                        fetchAndDisplayRepartitions(matchedCourse.nom_cours);
                    } else {
                        if (courseNameCode) {
                            courseNameCode.textContent = noDataMessage;
                        }
                        fetchAndDisplayRepartitions(null);
                    }
                });

                // Synchronise le nom de ressource lorsque le code est sélectionné
                resourceCodeSelect.addEventListener('change', function () {
                    const selectedCode = this.value;
                    const matchedCourse = courseData.find(course => course.code_cours === selectedCode);
                    if (matchedCourse) {
                        resourceNameSelect.value = matchedCourse.nom_cours;
                        if (courseNameCode) {
                            courseNameCode.textContent = `${matchedCourse.nom_cours} (${matchedCourse.code_cours})`;
                        }
                        fetchAndDisplayRepartitions(matchedCourse.nom_cours);
                    } else {
                        if (courseNameCode) {
                            courseNameCode.textContent = noDataMessage;
                        }
                        fetchAndDisplayRepartitions(null);
                    }
                });

                fetchAndDisplayCourses(semesterSelect.value);

                semesterSelect.addEventListener('change', function () {
                    fetchAndDisplayCourses(this.value);
                });
            });

        </script>

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


