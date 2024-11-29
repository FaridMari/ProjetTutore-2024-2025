<?php
// Enseignant_FicheRessource.php
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Fiche Ressource</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5" style="width: 50%; margin-bottom: 2%">
    <h1 class="text-center">Fiche Ressource : Emploi du Temps 2024-2025</h1>
    <p class="text-center text-danger">À remplir par le responsable de la ressource</p>

    <form method="post" action="traitement.php">
        <div class="mb-3">
            <label for="resourceName" class="form-label">Nom de la ressource :</label>
            <input type="text" class="form-control" id="resourceName" name="resourceName" placeholder="Nom de la ressource" required>
        </div>
        <div class="mb-3">
            <label for="resourceCode" class="form-label">Code de la ressource :</label>
            <input type="text" class="form-control" id="resourceCode" name="resourceCode" placeholder="Code de la ressource" required>
        </div>
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
            <label for="responsibleName" class="form-label">Nom du responsable :</label>
            <input type="text" class="form-control" id="responsibleName" name="responsibleName" placeholder="Nom du responsable" required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Téléphone :</label>
            <input type="tel" class="form-control" id="phone" name="phone" placeholder="Numéro de téléphone" required>
        </div>

        <!-- Répartition des heures -->
        <h4>1. Répartition des heures de TDs et TPs :</h4>
        <div class="mb-3">
            <label for="tdTpDetails" class="form-label">Détail des heures :</label>
            <textarea class="form-control" id="tdTpDetails" name="tdTpDetails" rows="3" placeholder="Exemple : S45 à S48 : 4h de TD réparties en 2 fois 2h, 2h de TP après les TDs" required></textarea>
        </div>

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
            <input type="text" class="form-control" id="scheduleDetails" name="scheduleDetails" placeholder="Exemple : S36 à S39 : 2h" required>
        </div>

        <button type="submit" class="btn btn-primary">Envoyer</button>
    </form>
</div>

<!-- Intégration de Bootstrap JS au cas où-->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
