<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Vœux 2024-2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-5">
    <h1 class="text-center mb-4">Fiche de Vœux</h1>
    <form id="ficheForm" action="src/Enseignant/EnregistrerContraintes.php" method="post" class="bg-white p-4 shadow-sm rounded">
        <p class="mb-4">Indiquez les plages horaires durant lesquelles vous ne pouvez pas enseigner :</p>
        <div class="table-responsive">
            <table class="table table-bordered text-center">
                <thead>
                <tr>
                    <th></th>
                    <th>Lundi</th>
                    <th>Mardi</th>
                    <th>Mercredi</th>
                    <th>Jeudi</th>
                    <th>Vendredi</th>
                    <th>Samedi</th>
                </tr>
                </thead>
                <tbody>
                <tr>
                    <td>8h-10h</td>
                    <td><input type="checkbox" name="lundi_8_10"></td>
                    <td><input type="checkbox" name="mardi_8_10"></td>
                    <td><input type="checkbox" name="mercredi_8_10"></td>
                    <td><input type="checkbox" name="jeudi_8_10"></td>
                    <td><input type="checkbox" name="vendredi_8_10"></td>
                    <td><input type="checkbox" name="samedi_8_10"></td>
                </tr>
                <tr>
                    <td>10h-12h</td>
                    <td><input type="checkbox" name="lundi_10_12"></td>
                    <td><input type="checkbox" name="mardi_10_12"></td>
                    <td><input type="checkbox" name="mercredi_10_12"></td>
                    <td><input type="checkbox" name="jeudi_10_12"></td>
                    <td><input type="checkbox" name="vendredi_10_12"></td>
                    <td><input type="checkbox" name="samedi_10_12"></td>
                </tr>
                <tr>
                    <td>14h-16h</td>
                    <td><input type="checkbox" name="lundi_14_16"></td>
                    <td><input type="checkbox" name="mardi_14_16"></td>
                    <td><input type="checkbox" name="mercredi_14_16"></td>
                    <td><input type="checkbox" name="jeudi_14_16"></td>
                    <td><input type="checkbox" name="vendredi_14_16"></td>
                    <td><input type="checkbox" name="samedi_14_16"></td>
                </tr>
                <tr>
                    <td>16h-18h</td>
                    <td><input type="checkbox" name="lundi_16_18"></td>
                    <td><input type="checkbox" name="mardi_16_18"></td>
                    <td><input type="checkbox" name="mercredi_16_18"></td>
                    <td><input type="checkbox" name="jeudi_16_18"></td>
                    <td><input type="checkbox" name="vendredi_16_18"></td>
                    <td><input type="checkbox" name="samedi_16_18"></td>
                </tr>
                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <p>Je préfère, si possible, éviter le créneau :</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="creneau_prefere" value="8h-10h" id="pref_8_10">
                <label class="form-check-label" for="pref_8_10">de 8h à 10h</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="creneau_prefere" value="16h-18h" id="pref_16_18">
                <label class="form-check-label" for="pref_16_18">de 16h à 18h</label>
            </div>
        </div>

        <div class="mb-3">
            <p>J'accepte d'avoir cours le samedi :</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="cours_samedi" value="oui" id="samedi_oui" required>
                <label class="form-check-label" for="samedi_oui">Oui</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="cours_samedi" value="non" id="samedi_non">
                <label class="form-check-label" for="samedi_non">Non</label>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary" id="validerBtn">Valider</button>
        </div>
    </form>
</div>

<script>
    // Ajoutez un événement au bouton "Valider"
    document.getElementById('validerBtn').addEventListener('click', function (e) {
        // Bloquez le comportement par défaut de soumission
        e.preventDefault();

        // Soumettre le formulaire à EnregistrerContraintes.php
        const form = document.getElementById('ficheForm');
        form.submit();

        // Créer une requête parallèle pour générer le PDF
        const formData = new FormData(form); // Récupérer les données du formulaire
        fetch('../ProjetTutore-2024-2025/src/User/GenerePdf.php', {
            method: 'POST',
            body: formData
        })
            .then(response => {
                if (response.ok) {
                    return response.blob(); // Obtenir le fichier PDF
                }
                throw new Error('Erreur lors de la génération du PDF.');
            })
            .then(blob => {
                // Télécharger le fichier PDF
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = 'FicheDeVoeux.pdf';
                document.body.appendChild(a);
                a.click();
                document.body.removeChild(a);
                window.URL.revokeObjectURL(url);
            })
            .catch(error => {
                console.error(error);
                alert('Une erreur est survenue lors de la génération du PDF.');
            });
    });
</script>
</body>
</html>
