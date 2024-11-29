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
        <form action="" method="post" class="bg-white p-4 shadow-sm rounded">
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
                        <tr>
                            <td>8h-10h</td>
                            <td><input type="checkbox" name="lundi_8_10"></td>
                            <td><input type="checkbox" name="mardi_8_10"></td>
                            <td><input type="checkbox" name="mercredi_8_10"></td>
                            <td><input type="checkbox" name="jeudi_8_10"></td>
                            <td><input type="checkbox" name="vendredi_8_10"></td>
                        </tr>
                        <tr>
                            <td>10h-12h</td>
                            <td><input type="checkbox" name="lundi_10_12"></td>
                            <td><input type="checkbox" name="mardi_10_12"></td>
                            <td><input type="checkbox" name="mercredi_10_12"></td>
                            <td><input type="checkbox" name="jeudi_10_12"></td>
                            <td><input type="checkbox" name="vendredi_10_12"></td>
                        </tr>
                        <tr>
                            <td>14h-16h</td>
                            <td><input type="checkbox" name="lundi_14_16"></td>
                            <td><input type="checkbox" name="mardi_14_16"></td>
                            <td><input type="checkbox" name="mercredi_14_16"></td>
                            <td><input type="checkbox" name="jeudi_14_16"></td>
                            <td><input type="checkbox" name="vendredi_14_16"></td>
                        </tr>
                        <tr>
                            <td>16h-18h</td>
                            <td><input type="checkbox" name="lundi_16_18"></td>
                            <td><input type="checkbox" name="mardi_16_18"></td>
                            <td><input type="checkbox" name="mercredi_16_18"></td>
                            <td><input type="checkbox" name="jeudi_16_18"></td>
                            <td><input type="checkbox" name="vendredi_16_18"></td>
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

            <button type="submit" class="btn btn-primary">Valider</button>
        </form>
    </div>
</body>
</html>
