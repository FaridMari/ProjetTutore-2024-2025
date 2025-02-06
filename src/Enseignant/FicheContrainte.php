<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fiche de Vœux 2024-2025</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<?php

if (isset($_SESSION['error_message'])): ?>
    <script>
        alert("<?php echo $_SESSION['error_message']; ?>");
    </script>
    <?php unset($_SESSION['error_message']); ?>
<?php endif; ?>

<?php if (isset($_SESSION['success_message'])): ?>
    <script>
        alert("<?php echo $_SESSION['success_message']; ?>");
    </script>
    <?php unset($_SESSION['success_message']); ?>
<?php endif; ?>

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
                </tr>
                </thead>
                <tbody>

                <?php
                $horaires = [
                    "8_10" => "8h-10h",
                    "10_12" => "10h-12h",
                    "14_16" => "14h-16h",
                    "16_18" => "16h-18h"
                ];
                $jours = ["lundi", "mardi", "mercredi", "jeudi", "vendredi"];

                foreach ($horaires as $heure_key => $heure_label) {
                    echo "<tr>";
                    echo "<td>$heure_label</td>";
                    foreach ($jours as $jour) {
                        $name = "{$jour}_{$heure_key}";
                        $checked = (isset($_SESSION['choix_contraintes']) && in_array($name, $_SESSION['choix_contraintes'])) ? 'checked' : '';
                        echo "<td><input type='checkbox' name='contraintes[]' value='$name' onchange='limiterContraintes()' $checked></td>";
                    }
                    echo "</tr>";
                }
                ?>

                </tbody>
            </table>
        </div>

        <div class="mb-3">
            <p>Je préfère, si possible, éviter le créneau :</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="creneau_prefere" value="8h-10h" id="pref_8_10"
                    <?php echo (isset($_SESSION['creneau_prefere']) && $_SESSION['creneau_prefere'] == "8h-10h") ? 'checked' : ''; ?>>
                <label class="form-check-label" for="pref_8_10">de 8h à 10h</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="creneau_prefere" value="16h-18h" id="pref_16_18"
                    <?php echo (isset($_SESSION['creneau_prefere']) && $_SESSION['creneau_prefere'] == "16h-18h") ? 'checked' : ''; ?>>
                <label class="form-check-label" for="pref_16_18">de 16h à 18h</label>
            </div>
        </div>

        <div class="mb-3">
            <p>J'accepte d'avoir cours le samedi :</p>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="cours_samedi" value="oui" id="samedi_oui"
                    <?php echo (isset($_SESSION['cours_samedi']) && $_SESSION['cours_samedi'] == "oui") ? 'checked' : ''; ?>>
                <label class="form-check-label" for="samedi_oui">Oui</label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="cours_samedi" value="non" id="samedi_non"
                    <?php echo (isset($_SESSION['cours_samedi']) && $_SESSION['cours_samedi'] == "non") ? 'checked' : ''; ?>>
                <label class="form-check-label" for="samedi_non">Non</label>
            </div>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-primary" id="validerBtn">Valider</button>
        </div>
    </form>
</div>

<script>
    function limiterContraintes() {
        let checkboxes = document.querySelectorAll('input[type="checkbox"]');
        let checkedCount = 0;

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                checkedCount++;
            }
        });

        if (checkedCount > 4) {
            alert("Vous ne pouvez sélectionner que 4 contraintes au maximum.");
            this.checked = false; // Désactive la dernière case cochée
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        document.querySelectorAll('input[type="checkbox"]').forEach(checkbox => {
            checkbox.addEventListener("change", limiterContraintes);
        });
    });
</script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        document.getElementById('validerBtn').addEventListener('click', function (e) {
            e.preventDefault();

            // Soumettre le formulaire
            const form = document.getElementById('ficheForm');
            form.submit();

            // Générer le PDF après soumission
            setTimeout(() => {
                window.location.href = 'src/User/GenerePdf.php';
            }, 3000); // Attendre 3 secondes après l'enregistrement
        });
    });
</script>

</body>
</html>
