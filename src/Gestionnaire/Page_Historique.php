<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Gestion des Données</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
</head>
<body>
<div class="container" id="main-content">
    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Enregistrer les données actuelles</h5>
        </div>
        <div class="card-body">
            <form id="saveCurrentDataForm">
                <div class="form-group row">
                    <label for="currentYear" class="col-sm-2 col-form-label">Année Actuelle</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="currentYear" placeholder="Entrez l'année actuelle">
                    </div>
                </div>
                <button type="button" class="btn btn-primary" id="saveCurrentData">Enregistrer</button>
            </form>
        </div>
    </div>

    <div class="card mt-4">
        <div class="card-header">
            <h5 class="card-title">Vider et abonder la base de données</h5>
        </div>
        <div class="card-body">
            <form id="importOldDataForm">
                <div class="form-group row">
                    <label for="oldYear" class="col-sm-2 col-form-label">Année Ancienne</label>
                    <div class="col-sm-10">
                        <input type="number" class="form-control" id="oldYear" placeholder="Entrez l'année ancienne à importer">
                    </div>
                </div>
                <button type="button" class="btn btn-danger" id="clearAndImportData">Vider et Importer</button>
            </form>
        </div>
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
        document.getElementById('saveCurrentData').addEventListener('click', function() {
            const currentYear = document.getElementById('currentYear').value;
            if (!currentYear) {
                toast.show('Veuillez entrer l\'année actuelle.', 'error');
                return;
            }

            fetch('src/gestionnaire/RequeteBD_SaveAllData.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ year: currentYear })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        toast.show('Données actuelles enregistrées avec succès!');
                    } else {
                        toast.show('Erreur lors de l\'enregistrement des données actuelles.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    toast.show('Une erreur s\'est produite.', 'error');
                });
        });

        document.getElementById('clearAndImportData').addEventListener('click', function() {
            const oldYear = document.getElementById('oldYear').value;

            if (!oldYear) {
                toast.show('Veuillez entrer une année ancienne.', 'error');
                return;
            }

            fetch('src/gestionnaire/RequeteBD_ReprendreHistorique.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ year: oldYear })
            })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        toast.show('Base de données vidée et abondée avec succès!');
                    } else {
                        toast.show('Erreur lors du vidage et de l\'importation des données.', 'error');
                    }
                })
                .catch(error => {
                    console.error('Erreur:', error);
                    toast.show('Une erreur s\'est produite.', 'error');
                });
        });
    });
</script>
</body>
</html>
