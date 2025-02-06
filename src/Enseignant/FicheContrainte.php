<style>
    /* ===================== Container général ===================== */
    .fiche-voeux-container {
        /* Tu peux mettre un fond, ou laisser le body en fond gris très clair
           et ce container en blanc. Ici, on reste sobre, fond blanc. */
        background-color: #fff;
        border-radius: 12px;
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);

        /* Couleur texte */
        color: #000;

        /* On garde les classes Bootstrap .container, .my-5, etc.
           Pour plus d'espace interne, on peut rajouter : */
        padding: 2em 2em;
    }

    /* Titre principal (H1) */
    .fiche-voeux-container h1 {
        text-transform: uppercase;
        color: #000;         /* noir */
        font-size: 1.8rem;
        font-weight: 600;
        letter-spacing: 1px;
        margin-bottom: 1em;
    }

    /* Paragraphe d'intro (le <p> juste avant la table) */
    .fiche-voeux-container p {
        color: #555; /* Légèrement gris */
    }

    /* ===================== Le formulaire ===================== */
    #ficheForm {
        border-radius: 8px;
    }

    /* On surcharge la classe Bootstrap "btn-primary"
       pour la mettre en jaune UL et texte noir */
    #ficheForm .btn.btn-primary {
        background-color: #FFEF65; /* Jaune clair */
        color: #000;              /* texte noir */
        border: none;             /* pas de bordure */
        font-weight: 600;
        transition: 0.3s transform, 0.3s background-color;
    }

    #ficheForm .btn.btn-primary:hover {
        background-color: #FFE74A; /* Jaune + soutenu */
        transform: scale(1.03);
    }

    #ficheForm .btn.btn-primary:active,
    #ficheForm .btn.btn-primary:focus {
        background-color: #FFD400;
        outline: none;
        border: none;
        transform: scale(0.97);
    }

    /* ===================== La table ===================== */
    /* En-tête du tableau (thead) en fond noir, texte blanc */
    .fiche-voeux-container table thead tr {
        background-color: #000;
        color: #fff;
    }

    /* Corps du tableau : on peut faire un 'zebra strip' */
    .fiche-voeux-container table tbody tr:nth-child(odd) {
        background-color: #f9f9f9; /* gris très clair */
    }
    .fiche-voeux-container table tbody tr:nth-child(even) {
        background-color: #fff;
    }

    /* Lorsqu'on survole une ligne du tableau, léger surlignage */
    .fiche-voeux-container table tbody tr:hover {
        background-color: #FFE74A; /* Jaune clair */
    }

    /* Les checkbox centrées (already done by text-center),
       si tu veux un style custom pour la case cochée (form-check-input)
       => surcharge la couleur du check
       (mais c'est plus compliqué, on reste basique).
    */

    /* ===================== Radios, checkboxes ===================== */
    /* Sur Bootstrap 5, les .form-check-input cochées ont un styling
       violet/bleu par défaut. On peut surcharger ainsi : */
    .form-check-input:checked {
        background-color: #FFD400;
        border-color: #FFD400;
    }

    /* ===================== Autres ajustements divers ===================== */
    /* Les <label> => gras,
       ou laisse la police par défaut de Bootstrap. */
    label.form-check-label,
    label.form-label {
        font-weight: 500;
    }

    /* Parfois, #main-content est déjà géré par layout.css
       pour être décalé par rapport à la sidebar.
       On n'y touche pas ici pour ne pas casser. */

</style>

<div id="main-content">
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
        fetch('src/User/GenerePdf.php', {
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
