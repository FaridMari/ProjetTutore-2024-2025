<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Heures par semaine</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .current-week {
            background-color: #d4edda !important; /* Couleur pour la semaine actuelle */
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Heures par semaine</h2>
    <table class="table table-bordered mt-3">
        <thead>
        <tr>
            <th>Semaine</th>
            <th>Date de début</th>
            <th>Heures</th>
        </tr>
        </thead>
        <tbody id="tableBody">
        <!-- Les données seront insérées ici -->
        </tbody>
    </table>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const enseignantId = 74; // Remplacez par l'ID de l'enseignant souhaité

        // Récupérer les données de répartition
        fetch('src/Enseignant/RequeteBD_GetRepartition.php')
            .then(response => response.json())
            .then(repartitionData => {
                // Récupérer les vœux de l'enseignant
                return fetch(`src/Enseignant/RequeteBD_GetVoeux.php?enseignant=${enseignantId}`)
                    .then(response => response.json())
                    .then(voeuxData => {
                        return fetch(`src/Gestionnaire/RequeteBD_GetConfigurationPlanningDetaille.php`)
                            .then(response => response.json())
                            .then(config => {
                                return { repartitionData, voeuxData, config };
                            });
                    });
            })
            .then(({ repartitionData, voeuxData, config }) => {
                // Fusionner les données et calculer les heures par semaine
                const heuresParSemaine = {};

                // Filtrer pour obtenir la configuration du "Semestre2"
                const semestre2Config = config.find(item => item.type === 'Semestre2');
                const dateDebutSemestre = new Date(semestre2Config.dateDebut);

                // Créer un ensemble des cours souhaités par l'enseignant
                const voeuxCours = new Set(voeuxData.map(voeu => voeu.id_cours));

                repartitionData.forEach(repartition => {
                    // Vérifier si le cours est dans les vœux de l'enseignant
                    if (voeuxCours.has(repartition.id_cours)) {
                        for (let i = repartition.semaine_debut; i <= repartition.semaine_fin; i++) {
                            const weekNumber = getWeek(new Date(dateDebutSemestre.getFullYear(), 0, 1 + (i - 1) * 7));
                            if (!heuresParSemaine[weekNumber]) {
                                heuresParSemaine[weekNumber] = 0;
                            }
                            heuresParSemaine[weekNumber] += repartition.nb_heures_par_semaine;
                        }
                    }
                });

                // Afficher les données dans le tableau
                const tableBody = document.getElementById('tableBody');
                const currentDate = new Date();
                let currentWeek = getWeek(currentDate);

                // Calculer la semaine de début du semestre
                const startWeek = getWeek(dateDebutSemestre);

                // Commencer à partir de la semaine de début du semestre
                for (let i = 0; i < 52; i++) {
                    let semaine = ((startWeek + i - 1) % 52) + 1; // Garde la numérotation entre 1 et 52

                    const tr = document.createElement('tr');
                    if (semaine === currentWeek) {
                        tr.classList.add('current-week');
                        console.log(`Ajout de la classe current-week pour la semaine ${semaine}`);
                    }

                    // Calculer la date de début de la semaine
                    const dateDebutSemaine = new Date(dateDebutSemestre);
                    dateDebutSemaine.setDate(dateDebutSemestre.getDate() + i * 7);

                    if (semaine != currentWeek) {
                        tr.innerHTML = `
                            <td>Semaine ${getWeek(dateDebutSemaine)}</td>
                            <td>${dateDebutSemaine.toLocaleDateString('fr-FR')}</td>
                            <td>${heuresParSemaine[semaine] || 0}</td>
                        `;
                    } else {
                        console.log("test");
                        tr.innerHTML = `
                        <td class="current-week">Semaine ${ getWeek(dateDebutSemaine)}</td>
                        <td class="current-week">${dateDebutSemaine.toLocaleDateString('fr-FR')}</td>
                        <td class="current-week">${heuresParSemaine[semaine] || 0}</td>
                        `;
                    }

                    tableBody.appendChild(tr);
                }
            })
            .catch(error => console.error('Erreur:', error));
    });

    function getWeek(date) {
        // Calcul de la semaine avec la norme ISO
        const tempDate = new Date(date);
        tempDate.setHours(0, 0, 0, 0);

        tempDate.setDate(tempDate.getDate() + 3 - (tempDate.getDay() + 6) % 7);
        const firstThursday = new Date(tempDate.getFullYear(), 0, 4);
        firstThursday.setDate(firstThursday.getDate() + 3 - (firstThursday.getDay() + 6) % 7);

        const weekNumber = Math.round((tempDate - firstThursday) / (7 * 24 * 60 * 60 * 1000)) + 1;

        return weekNumber;
    }
</script>
</body>
</html>
