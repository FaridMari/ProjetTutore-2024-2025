<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Cours</title>
</head>
<body>
<div class="container mt-5">
    <h1 class="mb-4">Gestion des Cours</h1>
    <div class="input-group mb-3">
        <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par nom ou code du cours">
    </div>
    <table class="table table-bordered table-hover">
        <thead>
        <tr>
            <th>ID</th>
            <th>Formation</th>
            <th>Semestre</th>
            <th>Nom du Cours</th>
            <th>Code du Cours</th>
            <th>Heures Totales</th>
            <th>Heures CM</th>
            <th>Heures TD</th>
            <th>Heures TP</th>
            <th>Heures EI</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="coursTableBody">
        <!-- Les données des cours seront affichées ici -->
        </tbody>
    </table>
</div>

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

    document.addEventListener('DOMContentLoaded', function() {
        let allCours = [];

        async function loadCours() {
            try {
                const response = await fetch('src/Gestionnaire/getCours.php');
                const cours = await response.json();
                allCours = cours;
                displayCours(cours);
            } catch (error) {
                console.error('Erreur lors du chargement des cours:', error);
            }
        }

        function displayCours(cours) {
            const tableBody = document.getElementById('coursTableBody');
            tableBody.innerHTML = '';
            cours.forEach(cours => {
                const row = document.createElement('tr');
                row.setAttribute('data-id', cours.id_cours);
                row.innerHTML = `
                <td>${cours.id_cours}</td>
                <td contenteditable="true" class="editable">${cours.formation}</td>
                <td contenteditable="true" class="editable">${cours.semestre}</td>
                <td contenteditable="true" class="editable">${cours.nom_cours}</td>
                <td contenteditable="true" class="editable">${cours.code_cours}</td>
                <td contenteditable="true" class="editable">${cours.nb_heures_total}</td>
                <td contenteditable="true" class="editable">${cours.nb_heures_cm}</td>
                <td contenteditable="true" class="editable">${cours.nb_heures_td}</td>
                <td contenteditable="true" class="editable">${cours.nb_heures_tp}</td>
                <td contenteditable="true" class="editable">${cours.nb_heures_ei}</td>
                <td>
                    <button class="btn btn-primary btn-sm saveButton">Enregistrer</button>
                </td>
            `;
                tableBody.appendChild(row);
            });
        }

        document.getElementById('searchInput').addEventListener('input', function() {
            const searchInput = document.getElementById('searchInput').value.toLowerCase();
            const filteredCours = allCours.filter(cours =>
                cours.nom_cours.toLowerCase().includes(searchInput) ||
                cours.code_cours.toLowerCase().includes(searchInput)
            );
            displayCours(filteredCours);
        });

        document.getElementById('coursTableBody').addEventListener('click', function(event) {
            if (event.target && event.target.classList.contains('saveButton')) {
                const row = event.target.closest('tr');
                const idCours = row.getAttribute('data-id');
                const coursData = {
                    id_cours: idCours,
                    formation: row.children[1].textContent,
                    semestre: row.children[2].textContent,
                    nom_cours: row.children[3].textContent,
                    code_cours: row.children[4].textContent,
                    nb_heures_total: row.children[5].textContent,
                    nb_heures_cm: row.children[6].textContent,
                    nb_heures_td: row.children[7].textContent,
                    nb_heures_tp: row.children[8].textContent,
                    nb_heures_ei: row.children[9].textContent
                };

                saveChanges(coursData);
            }
        });

        async function saveChanges(coursData) {
            const totalHeures = parseFloat(coursData.nb_heures_cm) + parseFloat(coursData.nb_heures_td) + parseFloat(coursData.nb_heures_tp) + parseFloat(coursData.nb_heures_ei);
            if (totalHeures > parseFloat(coursData.nb_heures_total)) {
                toast.show('Le cumul des heures est supérieur au total', 'error');
                return;
            }

            try {
                const response = await fetch('src/Gestionnaire/editCours.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(coursData)
                });

                const result = await response.json();
                console.log("Réponse du serveur :", result); // Ajoute ceci pour voir la réponse côté JS

                if (result.success) {
                    toast.show('Cours mis à jour avec succès', 'success');
                    loadCours();
                } else {
                    console.error('Erreur lors de la mise à jour du cours:', result.error);
                    toast.show('Erreur lors de la mise à jour du cours', 'error');
                }
            } catch (error) {
                console.error('Erreur lors de la mise à jour du cours:', error);
                toast.show('Erreur lors de la mise à jour du cours', 'error');
            }
        }

        // Charger les cours au chargement de la page
        loadCours();
    });
</script>
</body>
</html>
