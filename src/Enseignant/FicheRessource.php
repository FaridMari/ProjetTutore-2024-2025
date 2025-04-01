<?php
session_start();
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

$conn = connexionFactory::makeConnection();

$id_utilisateur = $_SESSION['id_utilisateur'];
// Cette requête est utilisée ici pour déterminer le statut de la fiche (verrouillée ou non)
$stmt = $conn->prepare("SELECT * FROM details_cours WHERE id_responsable_module = ?");
$stmt->execute([$id_utilisateur]);
$fiche = $stmt->fetch(PDO::FETCH_ASSOC);

$verrouille = ($fiche && $fiche['statut'] === 'valide');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Ressource : Emploi du Temps 2024-2025</title>
    <style>
        /* ===================== Conteneur principal ===================== */
        .fiche-ressource-container {
            width: 60%;
            margin: 2em auto;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 2em;
            color: #000;
        }
        /* Titre principal (H1) */
        .fiche-ressource-container h1 {
            text-transform: uppercase;
            font-size: 1.8rem;
            margin-bottom: 0.5em;
            color: #000;
        }
        /* Sous-titres (H4) */
        .fiche-ressource-container h4 {
            font-size: 1.2rem;
            margin-top: 2em;
            margin-bottom: 1em;
            color: #000;
        }
        /* Paragraphe d’avertissement */
        .fiche-ressource-container p.text-center.text-danger {
            margin-bottom: 1.5em;
            font-weight: 500;
        }
        /* ===================== Formulaires ===================== */
        .fiche-ressource-container .form-label {
            font-weight: 600;
            color: #000;
        }
        .fiche-ressource-container .form-control,
        .fiche-ressource-container .form-select {
            background-color: #fff;
            border: 1px solid #ddd;
            color: #000;
            border-radius: 5px;
        }
        .fiche-ressource-container .form-control:focus,
        .fiche-ressource-container .form-select:focus {
            outline: none;
            border-color: #FFD400;
            box-shadow: 0 0 0 2px rgba(255,212,0,0.2);
        }
        .fiche-ressource-container .form-check-input:checked {
            background-color: #FFD400;
            border-color: #FFD400;
        }
        /* ===================== Bouton principal ===================== */
        .fiche-ressource-container .btn.btn-primary {
            background-color: #FFEF65;
            color: #000;
            border: none;
            font-weight: 600;
            transition: background-color 0.3s, transform 0.3s;
        }
        .fiche-ressource-container .btn.btn-primary:hover {
            background-color: #FFE74A;
            transform: scale(1.02);
        }
        .fiche-ressource-container .btn.btn-primary:focus,
        .fiche-ressource-container .btn.btn-primary:active {
            background-color: #FFD400;
            outline: none;
            border: none;
            transform: scale(0.98);
        }
        /* ===================== Divers ===================== */
        .repartition-container {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 1em;
        }
        #warning {
            color: #FFC300;
            font-weight: bold;
        }
    </style>
</head>
<body>
<div id="main-content">
    <div class="fiche-ressource-container mt-4">
        <h1 class="text-center">Fiche Ressource : Emploi du Temps 2024-2025</h1>
        <p class="text-center" id="warning">À remplir par le responsable de la ressource</p>
        <?php if ($verrouille): ?>
            <div class="locked-message">
                Cette fiche a été validée et ne peut plus être modifiée.
            </div>
        <?php endif; ?>
        <!-- Début du formulaire global -->
        <form method="post" action="src/Enseignant/traitement.php">
            <!-- Sélection du semestre -->
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
            <!-- Sélection de la ressource (cours) -->
            <div class="mb-3">
                <label for="resourceName" class="form-label">Choix de la ressource :</label>
                <select class="form-select" id="resourceName" name="resourceName">
                    <option value="">-- Sélectionner un nom de ressource --</option>
                </select>
            </div>
            <!-- Sélection du responsable -->
            <div class="mb-3">
                <label for="responsibleName" class="form-label">Nom du responsable :</label>
                <select class="form-select" id="responsibleName" name="responsibleName" required>
                    <option value="">-- Sélectionner un intervenant --</option>
                </select>
            </div>
            <!-- Téléphone (lecture seule) -->
            <div class="mb-3">
                <label for="phone" class="form-label">Téléphone :</label>
                <input type="tel" class="form-control" id="phone" name="phone" placeholder="Numéro de téléphone" readonly>
            </div>
            <!-- Répartition des heures par semaines -->
            <h4 class="mt-4">1. Répartition des heures par semaines :</h4>
            <div id="hours-distribution" class="repartition-container mb-3">
                <!-- Insertion des répartitions -->
            </div>
            <!-- Réservations DS -->
            <h4>2. Réservations DS :</h4>
            <div class="mb-3">
                <label for="dsDetails" class="form-label">Détail des réservations :</label>
                <textarea class="form-control" id="dsDetails" name="dsDetails" rows="3"
                          placeholder="Indiquez les semaines et la durée pour chaque DS"></textarea>
            </div>
            <!-- Commentaire libre -->
            <div class="mb-3">
                <label for="scheduleDetails" class="form-label">Commentaire libre :</label>
                <textarea class="form-control" id="scheduleDetails" name="scheduleDetails" placeholder=""></textarea>
            </div>
            <!-- Salles 016 -->
            <h4>3. Salles 016 :</h4>
            <div class="mb-3">
                <label class="form-label">Souhaitez-vous intervenir dans la salle 016 ?</label>
                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="salle016" id="prefOui" value="Oui">
                        <label class="form-check-label" for="prefOui">Oui, de préférence</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="salle016" id="prefIndiff" value="Indifférent" required>
                        <label class="form-check-label" for="prefIndiff">Indifférent</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="salle016" id="prefNon" value="Non">
                        <label class="form-check-label" for="prefNon">Non, salle non adaptée</label>
                    </div>
                </div>
            </div>
            <!-- Besoins en chariots ou salles informatiques -->
            <h4>4. Besoins en chariots ou salles informatiques :</h4>
            <div class="mb-3">
                <label class="form-label">Système souhaité :</label>
                <div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="system" id="windows" value="Windows">
                        <label class="form-check-label" for="windows">Windows</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="system" id="linux" value="Linux">
                        <label class="form-check-label" for="linux">Linux</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="system" id="indiff" value="Indifférent">
                        <label class="form-check-label" for="indiff">Indifférent</label>
                    </div>
                </div>
            </div>
            <!-- Bouton d’envoi global -->
            <button type="submit" class="btn btn-primary">Enregistrer et valider</button>
        </form>
        <!-- Fin du formulaire global -->
    </div>
</div>

<!-- Script Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const semesterSelect = document.getElementById('semester');
    const resourceNameSelect = document.getElementById('resourceName');
    const hoursDistribution = document.getElementById('hours-distribution');
    const noDataMessage = 'Aucune donnée disponible.';
    let courseData = [];

    // On stocke la promesse du chargement des intervenants
    const intervenantsLoadedPromise = fetchAndDisplayIntervenants();

    function fetchAndDisplayIntervenants() {
        return new Promise((resolve, reject) => {
            const responsibleNameSelect = document.getElementById('responsibleName');
            const telephoneInput = document.getElementById('phone');
            responsibleNameSelect.innerHTML = '<option value="">-- Sélectionner un intervenant --</option>';

            fetch('src/Enseignant/get_intervenants.php')
                .then(response => {
                    if (!response.ok) throw new Error(`Erreur HTTP ! statut : ${response.status}`);
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        console.error('Erreur :', data.error);
                        reject(data.error);
                    } else {
                        telephoneInput.value = '';
                        data.forEach(intervenant => {
                            const option = document.createElement('option');
                            // On suppose ici que get_intervenants.php renvoie le champ id_enseignant (ou id_utilisateur en fallback)
                            option.value = intervenant.id_enseignant || intervenant.id_utilisateur;
                            option.textContent = intervenant.nom + ' ' + intervenant.prenom;
                            option.dataset.telephone = intervenant.telephone;
                            responsibleNameSelect.appendChild(option);
                        });
                        responsibleNameSelect.addEventListener('change', () => {
                            const selectedOption = responsibleNameSelect.selectedOptions[0];
                            telephoneInput.value = selectedOption.dataset.telephone || '';
                        });
                        resolve();
                    }
                })
                .catch(error => {
                    console.error('Erreur lors de la récupération des intervenants :', error);
                    reject(error);
                });
        });
    }

    // Fonction pour récupérer et afficher les cours
    function fetchAndDisplayCourses(semester) {
        resourceNameSelect.innerHTML = '<option value="">-- Sélectionner un nom de ressource --</option>';
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
                        courseData = data;
                        data.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.code_cours;
                            option.textContent = `${course.code_cours} : ${course.nom_cours}`;
                            resourceNameSelect.appendChild(option);
                        });
                    }
                })
                .catch(error => console.error('Erreur lors de la récupération des cours :', error));
        }
    }

    // Fonction pour récupérer et afficher les répartitions (pour affichage complémentaire)
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
                }
            })
            .catch(error => console.error('Erreur lors de la récupération des répartitions :', error));
    }

    function updateRepartitionFields(repartitions) {
        repartitions.sort((a, b) => (a.semaine_debut || 0) - (b.semaine_debut || 0));
        const resourceNameElement = document.getElementById('resourceName');
        const resourceName = resourceNameElement.options[resourceNameElement.selectedIndex]?.text || 'N/A';
        const parent = hoursDistribution.parentElement;
        hoursDistribution.innerHTML = '';

        const existingCourseInfo = document.getElementById('course-overview');
        if (existingCourseInfo) {
            existingCourseInfo.remove();
        }
        const courseInfoDiv = document.createElement('div');
        courseInfoDiv.id = 'course-overview';
        courseInfoDiv.classList.add('mb-3');
        courseInfoDiv.innerHTML = `<p><strong>Ressource :</strong> <span>${resourceName}</span></p>`;
        parent.insertBefore(courseInfoDiv, hoursDistribution);

        if (repartitions.length === 0) {
            hoursDistribution.innerHTML = '<p>Aucune répartition disponible.</p>';
            return;
        }

        repartitions.forEach(repartition => {
            const repartitionDiv = document.createElement('div');
            repartitionDiv.classList.add('repartition-entry', 'fade-in');
            repartitionDiv.innerHTML = `
                <table class="table table-striped">
                    <tr>
                        <th>Semaine de début</th>
                        <td>${repartition.semaine_debut || ''}</td>
                        <th>Semaine de fin</th>
                        <td>${repartition.semaine_fin || ''}</td>
                        <th>Type d'heure</th>
                        <td>${repartition.type_heure || ''}</td>
                        <th>Heures par semaine</th>
                        <td>${repartition.nb_heures_par_semaine || ''}</td>
                    </tr>
                </table>
            `;
            hoursDistribution.appendChild(repartitionDiv);
        });
    }

    // Fonction pour récupérer les détails du cours et préremplir le formulaire s'ils existent
    function fetchAndFillDetails(codeCours) {
        fetch(`src/Enseignant/get_details_cours.php?code_cours=${encodeURIComponent(codeCours)}`)
            .then(response => {
                if (!response.ok) throw new Error(`Erreur HTTP ! statut : ${response.status}`);
                return response.json();
            })
            .then(data => {
                console.log("Détails reçus :", data);
                if (data && !data.error) {
                    // Préremplir dsDetails en retirant le préfixe "DS : " si présent
                    const dsDetailsTextarea = document.getElementById('dsDetails');
                    let detailsText = data.details || '';
                    if (detailsText.startsWith("DS : ")) {
                        detailsText = detailsText.substring(5);
                    }
                    dsDetailsTextarea.value = detailsText;

                    // Préremplir scheduleDetails et extraire la préférence pour salle 016
                    const scheduleTextarea = document.getElementById('scheduleDetails');
                    let equipements = data.equipements_specifiques || '';
                    let scheduleText = '';
                    let salle016Value = '';

                    const salleRegex = /Intervention en salle 016\s*:\s*(Oui, de préférence|Indifférent|Non, salle non adaptée)/i;
                    const salleMatch = equipements.match(salleRegex);
                    if (salleMatch) {
                        salle016Value = salleMatch[1];
                    }
                    const scheduleRegex = /Besoins en chariots ou salles\s*:\s*(.*)/i;
                    const scheduleMatch = equipements.match(scheduleRegex);
                    if (scheduleMatch) {
                        scheduleText = scheduleMatch[1];
                    }
                    scheduleTextarea.value = scheduleText;

                    // Coche la radio correspondante pour salle016
                    if (salle016Value) {
                        if (salle016Value.toLowerCase().includes('oui')) {
                            document.getElementById('prefOui').checked = true;
                        } else if (salle016Value.toLowerCase().includes('indifférent')) {
                            document.getElementById('prefIndiff').checked = true;
                        } else if (salle016Value.toLowerCase().includes('non')) {
                            document.getElementById('prefNon').checked = true;
                        }
                    }

                    // Préremplir le select de l'enseignant avec l'id_responsable_module
                    if (data.id_responsable_module) {
                        console.log("id_responsable_module reçu :", data.id_responsable_module);
                        document.getElementById('responsibleName').value = data.id_responsable_module.toString();
                    }
                } else {
                    // Aucun détail trouvé : vider les champs concernés
                    document.getElementById('dsDetails').value = '';
                    document.getElementById('scheduleDetails').value = '';
                    document.getElementById('prefOui').checked = false;
                    document.getElementById('prefIndiff').checked = false;
                    document.getElementById('prefNon').checked = false;
                }
            })
            .catch(error => console.error('Erreur lors de la récupération des détails du cours :', error));
    }

    // Lors de la sélection d'un cours
    resourceNameSelect.addEventListener('change', function () {
        const selectedCode = this.value;
        const matchedCourse = courseData.find(course => course.code_cours === selectedCode);
        if (matchedCourse) {
            fetchAndDisplayRepartitions(matchedCourse.nom_cours);
            // On attend que le select des intervenants soit chargé avant de préremplir
            intervenantsLoadedPromise.then(() => {
                fetchAndFillDetails(selectedCode);
            });
        } else {
            updateRepartitionFields([]);
            // Si aucun cours n'est sélectionné, vider les champs de détails
            document.getElementById('dsDetails').value = '';
            document.getElementById('scheduleDetails').value = '';
            document.getElementById('prefOui').checked = false;
            document.getElementById('prefIndiff').checked = false;
            document.getElementById('prefNon').checked = false;
        }
    });

    fetchAndDisplayCourses(semesterSelect.value);
    semesterSelect.addEventListener('change', function () {
        fetchAndDisplayCourses(this.value);
    });
});

</script>
</body>
</html>
