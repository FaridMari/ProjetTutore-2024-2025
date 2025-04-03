<style>
    .btn-group .btn {
        width: 50%;
    }
    .form-group {
        margin-bottom: 1.5rem;
    }
    .modal {
        display: none;
        position: fixed;
        z-index: 1000;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        overflow: auto;
        background-color: rgb(0,0,0);
        background-color: rgba(0,0,0,0.4);
    }
    .modal-content {
        background-color: #fefefe;
        margin: 1em auto;
        padding: 20px;
        border: 1px solid #888;
        width: 50%;
        max-width: 500px;
        box-sizing: border-box;
    }
    .close {
        color: #aaa;
        float: right;
        font-size: 28px;
        font-weight: bold;
    }
    .close:hover,
    .close:focus {
        color: black;
        text-decoration: none;
        cursor: pointer;
    }
</style>
<div id="main-content">
    <div id="tableauCours" class="container mt-5">
        <h1 class="mb-4">Gestion des Cours</h1>
        <div class="input-group mb-3">
            <input type="text" id="searchInput" class="form-control" placeholder="Rechercher par nom ou code du cours">
        </div>
        <div class="btn-group mb-3" role="group">
            <button class="btn btn-success" id="addCoursButton">Ajouter un cours</button>
        </div>

        <!-- Modal for adding a course -->
        <div id="addCoursModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <form id="addCoursForm">
                    <div class="form-group">
                        <label for="formation" class="form-label">Formation</label>
                        <select class="form-control" id="formation" required>
                            <option value="" disabled selected>Sélectionnez une formation</option>
                            <option value="Autre">Autre</option>
                            <option value="BUT S1">BUT S1</option>
                            <option value="BUT S3">BUT S3</option>
                            <option value="BUT S5 DACS">BUT S5 DACS</option>
                            <option value="BUT S5 RA-DWM">BUT S5 RA-DWM</option>
                            <option value="BUT S5 RA-IL">BUT S5 RA-IL</option>
                            <option value="BUT S2">BUT S2</option>
                            <option value="BUT S6 DACS">BUT S6 DACS</option>
                            <option value="BUT S6 RA-IL">BUT S6 RA-IL</option>
                            <option value="BUT S4 DACS">BUT S4 DACS</option>
                            <option value="BUT S4 RA-DWM">BUT S4 RA-DWM</option>
                            <option value="BUT S4 RA-IL">BUT S4 RA-IL</option>
                            <option value="BUT S6 RA">BUT S6 RA</option>
                            <option value="BUT S6 RA-DWM">BUT S6 RA-DWM</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="semestre" class="form-label">Semestre</label>
                        <select class="form-control" id="semestre" required disabled>
                            <option value="" disabled selected>Sélectionnez une formation</option>
                            <option value="1">Semestre 1</option>
                            <option value="2">Semestre 2</option>
                            <option value="3">Semestre 3</option>
                            <option value="4">Semestre 4</option>
                            <option value="5">Semestre 5</option>
                            <option value="6">Semestre 6</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="nom_cours" class="form-label">Nom du Cours</label>
                        <input type="text" class="form-control" id="nom_cours" required>
                    </div>
                    <div class="form-group">
                        <label for="code_cours" class="form-label">Code du Cours</label>
                        <input type="text" class="form-control" id="code_cours" required>
                    </div>
                    <div class="form-group">
                        <label for="nb_heures_total" class="form-label">Heures Totales</label>
                        <input type="number" class="form-control" id="nb_heures_total" value="0" readonly>
                    </div>
                    <div class="form-group">
                        <label for="nb_heures_cm" class="form-label">Heures CM</label>
                        <input type="number" class="form-control" id="nb_heures_cm" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="nb_heures_td" class="form-label">Heures TD</label>
                        <input type="number" class="form-control" id="nb_heures_td" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="nb_heures_tp" class="form-label">Heures TP</label>
                        <input type="number" class="form-control" id="nb_heures_tp" value="0" required>
                    </div>
                    <div class="form-group">
                        <label for="nb_heures_ei" class="form-label">Heures EI</label>
                        <input type="number" class="form-control" id="nb_heures_ei" value="0" required>
                    </div>
                    <div class="btn-group" role="group">
                        <button type="submit" class="btn btn-primary">Ajouter</button>
                        <button type="button" class="btn btn-secondary" id="cancelAddCoursButton">Annuler</button>
                    </div>
                </form>
            </div>
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
                const response = await fetch('src/Gestionnaire/RequeteBD_GetCours.php');
                const cours = await response.json();
                allCours = cours;
                displayCours(cours);
            } catch (error) {
                console.error('Erreur lors du chargement des cours:', error);
            }
        }

        const formationSelect = document.getElementById('formation');
        const semestreSelect = document.getElementById('semestre');

        const formationSemestreMap = {
            "BUT S1": "1",
            "BUT S2": "2",
            "BUT S3": "3",
            "BUT S4 DACS": "4",
            "BUT S4 RA-DWM": "4",
            "BUT S4 RA-IL": "4",
            "BUT S5 DACS": "5",
            "BUT S5 RA-DWM": "5",
            "BUT S5 RA-IL": "5",
            "BUT S6 DACS": "6",
            "BUT S6 RA": "6",
            "BUT S6 RA-DWM": "6",
            "BUT S6 RA-IL": "6"
        };

        formationSelect.addEventListener('change', function() {
            const selectedFormation = formationSelect.value;
            const correspondingSemestre = formationSemestreMap[selectedFormation] || "";
            semestreSelect.value = correspondingSemestre;
        });

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
            <td class="total-heures">${cours.nb_heures_total}</td>
            <td contenteditable="true" class="editable heures">${cours.nb_heures_cm}</td>
            <td contenteditable="true" class="editable heures">${cours.nb_heures_td}</td>
            <td contenteditable="true" class="editable heures">${cours.nb_heures_tp}</td>
            <td contenteditable="true" class="editable heures">${cours.nb_heures_ei}</td>
            <td>
                <div class="btn-group" role="group">
                    <button class="btn btn-primary btn-sm saveButton">Enregistrer</button>
                    <button class="btn btn-danger btn-sm deleteButton">Supprimer</button>
                </div>
            </td>
        `;
                tableBody.appendChild(row);
            });

            // Ajouter un écouteur pour recalculer le total des heures
            document.querySelectorAll('.heures').forEach(cell => {
                cell.addEventListener('input', updateTotalHeures);
            });
        }

        const heuresInputs = ['nb_heures_cm', 'nb_heures_td', 'nb_heures_tp', 'nb_heures_ei'];
        heuresInputs.forEach(id => {
            document.getElementById(id).addEventListener('input', updateTotalHeuresModal);
        });

        function updateTotalHeuresModal() {
            const heuresCM = parseFloat(document.getElementById('nb_heures_cm').value) || 0;
            const heuresTD = parseFloat(document.getElementById('nb_heures_td').value) || 0;
            const heuresTP = parseFloat(document.getElementById('nb_heures_tp').value) || 0;
            const heuresEI = parseFloat(document.getElementById('nb_heures_ei').value) || 0;
            const totalHeures = heuresCM + heuresTD + heuresTP + heuresEI;
            document.getElementById('nb_heures_total').value = totalHeures;
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
            } else if (event.target && event.target.classList.contains('deleteButton')) {
                const row = event.target.closest('tr');
                const idCours = row.getAttribute('data-id');

                if (confirm('Êtes-vous sûr de vouloir supprimer ce cours ?')) {
                    deleteCours(idCours);
                }
            }
        });

        async function saveChanges(coursData) {
            const totalHeures = parseFloat(coursData.nb_heures_cm) + parseFloat(coursData.nb_heures_td) + parseFloat(coursData.nb_heures_tp) + parseFloat(coursData.nb_heures_ei);
            if (totalHeures > parseFloat(coursData.nb_heures_total)) {
                toast.show('Le cumul des heures est supérieur au total', 'error');
                return;
            }

            try {
                const response = await fetch('src/Gestionnaire/RequeteBD_EditCours.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(coursData)
                });

                const result = await response.json();
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

        async function deleteCours(idCours) {
            try {
                const response = await fetch('src/Gestionnaire/RequeteBD_DeleteCours.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id_cours: idCours })
                });

                const result = await response.json();
                if (result.success) {
                    toast.show('Cours supprimé avec succès', 'success');
                    loadCours();
                } else {
                    console.error('Erreur lors de la suppression du cours:', result.error);
                    toast.show('Erreur lors de la suppression du cours', 'error');
                }
            } catch (error) {
                console.error('Erreur lors de la suppression du cours:', error);
                toast.show('Erreur lors de la suppression du cours', 'error');
            }
        }

        const addCoursButton = document.getElementById('addCoursButton');
        const addCoursForm = document.getElementById('addCoursForm');
        const cancelAddCoursButton = document.getElementById('cancelAddCoursButton');
        const modal = document.getElementById('addCoursModal');
        const closeModalButton = document.getElementsByClassName('close')[0];

        addCoursButton.addEventListener('click', function() {
            modal.style.display = 'block';
        });

        closeModalButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });

        cancelAddCoursButton.addEventListener('click', function() {
            modal.style.display = 'none';
        });

        addCoursForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            const coursData = {
                formation: document.getElementById('formation').value,
                semestre: document.getElementById('semestre').value,
                nom_cours: document.getElementById('nom_cours').value,
                code_cours: document.getElementById('code_cours').value,
                nb_heures_total: document.getElementById('nb_heures_total').value,
                nb_heures_cm: document.getElementById('nb_heures_cm').value,
                nb_heures_td: document.getElementById('nb_heures_td').value,
                nb_heures_tp: document.getElementById('nb_heures_tp').value,
                nb_heures_ei: document.getElementById('nb_heures_ei').value
            };

            const totalHeures = parseFloat(coursData.nb_heures_cm) + parseFloat(coursData.nb_heures_td) + parseFloat(coursData.nb_heures_tp) + parseFloat(coursData.nb_heures_ei);
            coursData.nb_heures_total = totalHeures;

            try {
                const response = await fetch('src/Gestionnaire/RequeteBD_AddCours.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(coursData)
                });

                const result = await response.json();
                if (result.success) {
                    toast.show('Cours ajouté avec succès', 'success');
                    loadCours();
                    modal.style.display = 'none';
                } else {
                    console.error('Erreur lors de l\'ajout du cours:', result.error);
                    toast.show('Erreur lors de l\'ajout du cours', 'error');
                }
            } catch (error) {
                console.error('Erreur lors de l\'ajout du cours:', error);
                toast.show('Erreur lors de l\'ajout du cours', 'error');
            }
        });

        // Charger les cours au chargement de la page
        loadCours();
    });
</script>
