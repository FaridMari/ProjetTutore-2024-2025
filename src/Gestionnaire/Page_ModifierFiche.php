<?php
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

function generateTableRows(string $type, array $coursList, int $count, array $postData): void
{
    $allowedSemesters = $type === 'septembre' ? ['1', '3', '5'] : ['2', '4', '6'];
    $data = $postData[$type] ?? [];
    $ressources = $data['ressource'] ?? [];
    $remarques = $data['remarques'] ?? [];
    $formations = $data['formation'] ?? [];
    $semestres = $data['semestre'] ?? [];
    $cms = $data['cm'] ?? [];
    $tds = $data['td'] ?? [];
    $tps = $data['tp'] ?? [];
    $eis = $data['ei'] ?? [];
    $ids = $data['id'] ?? [];

    for ($i = 0; $i < $count; $i++) {
        $selectedCours = $ressources[$i] ?? '';
        $remarque = htmlspecialchars($remarques[$i] ?? '');
        $defaultCM = $defaultTD = $defaultTP = $defaultEI = '';

        if (!empty($selectedCours)) {
            foreach ($coursList as $cours) {
                if ($cours->getNomCours() === $selectedCours) {
                    $defaultCM = $cours->getNbHeuresCM();
                    $defaultTD = $cours->getNbHeuresTD();
                    $defaultTP = $cours->getNbHeuresTP();
                    $defaultEI = $cours->getNbHeuresEI();
                    break;
                }
            }
        }

        $valCM = (isset($cms[$i]) && $cms[$i] !== '') ? $cms[$i] : $defaultCM;
        $valTD = (isset($tds[$i]) && $tds[$i] !== '') ? $tds[$i] : $defaultTD;
        $valTP = (isset($tps[$i]) && $tps[$i] !== '') ? $tps[$i] : $defaultTP;
        $valEI = (isset($eis[$i]) && $eis[$i] !== '') ? $eis[$i] : $defaultEI;

        echo '<tr>';
        echo '<input type="hidden" name="' . $type . '[id][]" value="' . htmlspecialchars($ids[$i] ?? '') . '">';
        echo '<td><input type="text" name="' . $type . '[formation][]" value="' . htmlspecialchars($formations[$i] ?? '') . '" readonly></td>';
        echo '<td><input type="text" name="' . $type . '[ressource][]" value="' . htmlspecialchars($selectedCours) . '"></td>';
        echo '<td><input type="text" name="' . $type . '[semestre][]" value="' . htmlspecialchars($semestres[$i] ?? '') . '" readonly></td>';
        echo '<td><input type="number" name="' . $type . '[cm][]" value="' . htmlspecialchars($valCM) . '"></td>';
        echo '<td><input type="number" name="' . $type . '[td][]" value="' . htmlspecialchars($valTD) . '"></td>';
        echo '<td><input type="number" name="' . $type . '[tp][]" value="' . htmlspecialchars($valTP) . '"></td>';
        echo '<td><input type="number" name="' . $type . '[ei][]" value="' . htmlspecialchars($valEI) . '"></td>';
        echo '<td><input type="text" name="' . $type . '[remarques][]" value="' . $remarque . '"></td>';
        echo '<td><button type="button" class="remove-line">×</button></td>';
        echo '</tr>';
    }
}


$conn = connexionFactory::makeConnection();

if (!isset($_SESSION)) session_start();

$type = $_GET['type'] ?? '';
$id = $_GET['id'] ?? '';
$table = $_GET['table'] ?? '';
$id_utilisateur = $_GET['id'] ?? '';


echo "<h2 class=\"title-custom\">Modification de la $type</h2>";

if ($type === 'Fiche Prévisionnelle') {
    require_once __DIR__ . '/../modele/CoursDTO.php';
    require_once __DIR__ . '/../modele/EnseignantDTO.php';
    require_once __DIR__ . '/../modele/VoeuDTO.php';
    require_once __DIR__ . '/../modele/VoeuHorsIUTDTO.php';
    require_once __DIR__ . '/../modele/Voeu.php';
    require_once __DIR__ . '/../modele/VoeuHorsIUT.php';

    echo "<style>
        h2, h3 {
            color: #003366;
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ccc;
            text-align: center;
        }
        th {
            background-color: #003366;
            color: white;
        }
        input[type='text'], input[type='number'] {
            width: 100%;
            padding: 6px;
            box-sizing: border-box;
        }
        button.remove-line {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            cursor: pointer;
            border-radius: 4px;
        }
        .btn-submit {
            background-color: #28a745;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }
    </style>";

    $coursDTO         = new CoursDTO();
    $enseignantDTO    = new EnseignantDTO();
    $voeuDTO          = new VoeuDTO();
    $voeuHorsIUTDTO   = new VoeuHorsIUTDTO();

    $conn = connexionFactory::makeConnection();
    $idEnseignant = $_GET['id'] ?? null;

    if (!$idEnseignant) {
        echo "<div class='alert alert-danger'>ID enseignant manquant.</div>";
        return;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['envoyer'])) {
        $voeuDTO->deleteByEnseignant($idEnseignant);
        $voeuHorsIUTDTO->deleteByEnseignant($idEnseignant);

        foreach (['septembre', 'janvier'] as $periode) {
            $donnees = $_POST[$periode] ?? [];
            $ressources = $donnees['ressource'] ?? [];

            for ($i = 0; $i < count($ressources); $i++) {
                $nomCours = trim($ressources[$i]);
                if ($nomCours === '') continue;

                $coursTrouves = $coursDTO->findByName($nomCours);
                if (!$coursTrouves) continue;
                $cours = $coursTrouves[0];

                $voeu = new Voeu(
                    null,
                    $idEnseignant,
                    $cours->getIdCours(),
                    trim($donnees['remarques'][$i] ?? ''),
                    $donnees['semestre'][$i] ?? '',
                    floatval($donnees['cm'][$i] ?? 0),
                    floatval($donnees['td'][$i] ?? 0),
                    floatval($donnees['tp'][$i] ?? 0),
                    floatval($donnees['ei'][$i] ?? 0)
                );

                $voeuDTO->save($voeu);
            }
        }

        $hors = $_POST['hors_iut'] ?? [];
        $composants = $hors['composant'] ?? [];

        for ($i = 0; $i < count($composants); $i++) {
            $composant = trim($composants[$i]);
            if ($composant === '') continue;

            $voeuHI = new VoeuHorsIUT(
                null,
                $idEnseignant,
                $composant,
                trim($hors['formation'][$i] ?? ''),
                trim($hors['module'][$i] ?? ''),
                floatval($hors['cm'][$i] ?? 0),
                floatval($hors['td'][$i] ?? 0),
                floatval($hors['tp'][$i] ?? 0),
                floatval($hors['ei'][$i] ?? 0),
                floatval($hors['total'][$i] ?? 0)
            );

            $voeuHorsIUTDTO->save($voeuHI);
        }

        header("Location: ?type=Fiche Prévisionnelle&id=$idEnseignant&success=1");
        exit;
    }

    $stmt = $conn->prepare("SELECT statut FROM voeux WHERE id_enseignant = :id");
    $stmt->bindValue(':id', $idEnseignant, PDO::PARAM_INT);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $isLocked = isset($row['statut']) && $row['statut'] === 'validée';

    if ($isLocked) {
        echo "<div class='alert alert-warning'>Cette fiche est déjà validée et ne peut plus être modifiée.</div>";
        return;
    }

    $coursList = $coursDTO->findAll();
    $voeux = $voeuDTO->findByEnseignant($idEnseignant);
    $voeuxHorsIUT = $voeuHorsIUTDTO->findByEnseignant($idEnseignant);

    $voeuxSeptembre = [];
    $voeuxJanvier = [];
    foreach ($voeux as $v) {
        if (in_array($v->getSemestre(), ['1', '3', '5'])) {
            $voeuxSeptembre[] = $v;
        } else {
            $voeuxJanvier[] = $v;
        }
    }

    $septembreCount = max(1, count($voeuxSeptembre));
    $janvierCount = max(1, count($voeuxJanvier));
    $horsIUTCount = max(1, count($voeuxHorsIUT));

    $postData = [];
    include __DIR__ . '/../Enseignant/fiche_previsionnelle_view.php';
}

elseif ($type === 'Fiche Ressource') {
    if (!$id_utilisateur || $table !== 'details_cours') {
        die("Paramètres invalides.");
    }

    $stmtUser = $conn->prepare("SELECT responsable, nom, prenom FROM utilisateurs WHERE id_utilisateur = ?");
    $stmtUser->execute([$id_utilisateur]);
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user || strtolower(trim($user['responsable'])) !== 'oui') {
        die("L'utilisateur n'est pas responsable de ressource.");
    }

    $stmtFiche = $conn->prepare("SELECT d.* FROM utilisateurs u 
        INNER JOIN enseignants e ON u.id_utilisateur = e.id_utilisateur 
        INNER JOIN details_cours d ON e.id_enseignant = d.id_responsable_module 
        WHERE u.id_utilisateur = ?");
    $stmtFiche->execute([$id_utilisateur]);
    $fiche = $stmtFiche->fetch(PDO::FETCH_ASSOC);

    if (!$fiche) {
        die("Aucune fiche ressource trouvée pour cet utilisateur responsable.");
    }

    $verrouille = ($fiche['statut'] === 'validée');

    $stmtIntervenants = $conn->query("SELECT id_enseignant, nom, prenom FROM enseignants INNER JOIN utilisateurs ON enseignants.id_utilisateur = utilisateurs.id_utilisateur");
    $intervenants = $stmtIntervenants->fetchAll(PDO::FETCH_ASSOC);
    ?>
    <!DOCTYPE html>
    <html lang="fr">
    <head>
        <meta charset="UTF-8">
        <title>Fiche Ressource : Emploi du Temps 2024-2025</title>
        <style>
            /* ===================== Conteneur principal ===================== */
            .fiche-ressource-container {
                width: 80%;
                max-width: 900px;
                margin: 3em auto;
                background-color: #ffffff;
                border-radius: 12px;
                box-shadow: 0 6px 18px rgba(0,0,0,0.15);
                padding: 2.5em;
                color: #2d3748;
                font-family: 'Segoe UI', Roboto, 'Helvetica Neue', sans-serif;
                position: relative;
            }
            .title-custom {
                text-transform: uppercase;
                font-size: 1.7rem;
                margin-bottom: 1.2em;
                color: #1a202c;
                text-align: center;
                font-weight: 700;
                padding-bottom: 0.5em;
                border-bottom: 2px solid #edf2f7;
            }

            /* Sous-titres (H4) */
            .fiche-ressource-container h4 {
                font-size: 1.1rem;
                margin-top: 2em;
                margin-bottom: 1em;
                color: #1a202c;
                font-weight: 600;
            }

            /* Paragraphe d'avertissement */
            .fiche-ressource-container p.text-center.text-danger {
                margin-bottom: 1.5em;
                font-weight: 500;
                color: #e53e3e;
            }

            /* ===================== Formulaires ===================== */
            .fiche-ressource-container .form-label {
                font-weight: 600;
                color: #4a5568;
                margin-top: 1.2em;
                display: block;
                margin-bottom: 0.5em;
            }

            .fiche-ressource-container .form-control,
            .fiche-ressource-container .form-select {
                width: 100%;
                padding: 0.75em 1em;
                background-color: #f7fafc;
                border: 1px solid #e2e8f0;
                color: #2d3748;
                border-radius: 6px;
                font-size: 0.95rem;
                transition: all 0.3s ease;
                margin-bottom: 1.2em;
            }

            .fiche-ressource-container textarea.form-control {
                min-height: 150px;
                resize: vertical;
                font-size: 1rem;
            }

            .fiche-ressource-container .form-control:focus,
            .fiche-ressource-container .form-select:focus {
                outline: none;
                border-color: #FFD400;
                box-shadow: 0 0 0 3px rgba(255, 212, 0, 0.2);
                background-color: #fff;
            }

            .fiche-ressource-container .form-check {
                margin-bottom: 0.7em;
                display: flex;
                align-items: center;
            }

            .fiche-ressource-container .form-check input[type="radio"] {
                width: 18px;
                height: 18px;
                margin-right: 0.7em;
                cursor: pointer;
            }

            .fiche-ressource-container .form-check input[type="radio"]:checked {
                background-color: #FFD400;
                border-color: #FFD400;
            }

            /* ===================== Bouton principal ===================== */
            .fiche-ressource-container .btn.btn-primary {
                background-color: #FFEF65;
                color: #000;
                border: none;
                font-weight: 600;
                padding: 0.8em 1.8em;
                border-radius: 6px;
                margin-top: 1.5em;
                cursor: pointer;
                font-size: 1rem;
                transition: all 0.25s ease;
                display: inline-block;
                width: auto;
                text-align: center;
            }

            .fiche-ressource-container .btn.btn-primary:hover {
                background-color: #FFE74A;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(255, 212, 0, 0.2);
            }

            .fiche-ressource-container .btn.btn-primary:focus,
            .fiche-ressource-container .btn.btn-primary:active {
                background-color: #FFD400;
                outline: none;
                border: none;
                transform: translateY(1px);
                box-shadow: 0 2px 4px rgba(255, 212, 0, 0.2);
            }

            /* Bouton retour */
            .btn-back {
                background-color: #e2e8f0;
                color: #2d3748;
                border: none;
                font-weight: 600;
                padding: 0.8em 1.8em;
                border-radius: 6px;
                margin-top: 1.5em;
                margin-right: 1em;
                cursor: pointer;
                font-size: 1rem;
                transition: all 0.25s ease;
                display: inline-block;
                width: auto;
                text-align: center;
            }

            .btn-back:hover {
                background-color: #cbd5e0;
                transform: translateY(-2px);
                box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            }

            .btn-back:focus,
            .btn-back:active {
                background-color: #a0aec0;
                outline: none;
                border: none;
                transform: translateY(1px);
            }

            /* Conteneur de boutons */
            .buttons-container {
                display: flex;
                flex-wrap: wrap;
                gap: 1em;
            }

            /* ===================== Divers ===================== */
            .repartition-container {
                background-color: #f8fafc;
                border: 1px solid #e2e8f0;
                border-radius: 8px;
                padding: 1.5em;
                margin-bottom: 1.5em;
            }

            #warning {
                color: #FFC300;
                font-weight: 600;
                margin-bottom: 1em;
                padding: 0.7em;
                background-color: #fffaf0;
                border-left: 4px solid #FFD400;
                border-radius: 4px;
            }

            /* Responsive adjustments */
            @media (max-width: 768px) {
                .fiche-ressource-container {
                    width: 95%;
                    padding: 1.5em;
                }

                .fiche-ressource-container h1 {
                    font-size: 1.5rem;
                }

                .fiche-ressource-container textarea.form-control {
                    min-height: 120px;
                }
            }
        </style>
    </head>
    <body>
    <div class="fiche-ressource-container mt-4">
        <form method="post" action="../../src/Enseignant/traitement.php">
            <input type="hidden" name="id_utilisateur" value="<?= htmlspecialchars($id_utilisateur) ?>">

            <div class="mb-3">
                <label for="responsibleName" class="form-label">Nom du responsable :</label>
                <select class="form-select" id="responsibleName" name="responsibleName" required <?= $verrouille ? 'disabled' : '' ?>>
                    <option value="">-- Sélectionner un intervenant --</option>
                    <?php foreach ($intervenants as $interv): ?>
                        <option value="<?= $interv['id_enseignant'] ?>" <?= $fiche['id_responsable_module'] == $interv['id_enseignant'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($interv['nom'] . ' ' . $interv['prenom']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <label for="dsDetails" class="form-label">Détail des réservations :</label>
            <textarea class="form-control" id="dsDetails" name="dsDetails" rows="3" <?= $verrouille ? 'disabled' : '' ?>><?= htmlspecialchars($fiche['ds'] ?? '') ?></textarea>

            <label for="scheduleDetails" class="form-label">Commentaire libre :</label>
            <textarea class="form-control" id="scheduleDetails" name="scheduleDetails" rows="3" <?= $verrouille ? 'disabled' : '' ?>><?= htmlspecialchars($fiche['commentaire'] ?? '') ?></textarea>

            <label class="form-label">Souhaitez-vous intervenir dans la salle 016 ?</label>
            <?php
            $equipements = $fiche['equipements_specifiques'] ?? '';
            $sallePref = '';
            if (preg_match('/salle 016\s*:\s*(.*)/i', $equipements, $matches)) {
                $sallePref = strtolower(trim($matches[1]));
            }
            ?>
            <div class="form-check">
                <input type="radio" name="salle016" value="Oui" <?= str_contains($sallePref, 'oui') ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Oui, de préférence
            </div>
            <div class="form-check">
                <input type="radio" name="salle016" value="Indifférent" <?= str_contains($sallePref, 'indiff') ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Indifférent
            </div>
            <div class="form-check">
                <input type="radio" name="salle016" value="Non" <?= str_contains($sallePref, 'non') ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Non, salle non adaptée
            </div>

            <label class="form-label">Système souhaité :</label>
            <div class="form-check">
                <input type="radio" name="systeme" value="Windows" <?= strtolower($fiche['systeme']) === 'windows' ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Windows
            </div>
            <div class="form-check">
                <input type="radio" name="systeme" value="Linux" <?= strtolower($fiche['systeme']) === 'linux' ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Linux
            </div>
            <div class="form-check">
                <input type="radio" name="systeme" value="Indifférent" <?= strtolower($fiche['systeme']) === 'indifférent' ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Indifférent
            </div>

            <div class="buttons-container">
                <button type="button" onclick="window.location.href='../../index.php?action=ficheEnseignant'" class="btn-back">Retour</button>
                <?php if (!$verrouille): ?>
                    <button type="submit" class="btn btn-primary">Enregistrer et valider</button>
                <?php endif; ?>
            </div>
        </form>
    </div>
    </body>
    </html>
<?php
    }elseif ($type === 'Fiche Contrainte') {

    $id_utilisateur = $id;
    if (!$id_utilisateur) {
        echo "<div class='alert alert-danger'>ID utilisateur manquant.</div>";
        return;
    }
    $check = $conn->prepare("SELECT COUNT(*) as nb FROM contraintes WHERE id_utilisateur = ?");
    $check->execute([$id_utilisateur]);
    $count = $check->fetch(PDO::FETCH_ASSOC)['nb'] ?? 0;

    if ($count > 0) {
        $conn->prepare("UPDATE contraintes SET modification_en_cours = 1 WHERE id_utilisateur = ?")->execute([$id_utilisateur]);
    } else {
        $conn->prepare("INSERT INTO contraintes (id_utilisateur, modification_en_cours, statut) VALUES (?, 1, 'en attente')")->execute([$id_utilisateur]);
    }


    // Récupérer les infos de l'enseignant
    $stmtEnseignant = $conn->prepare("SELECT nom, prenom FROM utilisateurs WHERE id_utilisateur = ?");
    $stmtEnseignant->execute([$id_utilisateur]);
    $enseignant = $stmtEnseignant->fetch(PDO::FETCH_ASSOC);

    if (!$enseignant) {
        echo "<div class='alert alert-danger'>Erreur : enseignant introuvable pour l’ID $id_utilisateur.</div>";
        return;
    }

    // Récupérer toutes les contraintes de l'utilisateur
    $stmt = $conn->prepare("SELECT jour, heure_debut, creneau_preference, cours_samedi, commentaire FROM contraintes WHERE id_utilisateur = ?");
    $stmt->execute([$id_utilisateur]);
    $contraintesResult = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $contraintesChecked = [];
    $creneauPrefere = null;
    $coursSamedi = null;
    $commentaire = null;

    foreach ($contraintesResult as $row) {
        $jour = $row['jour'];
        $heure = $row['heure_debut'] . '_' . (intval($row['heure_debut']) + 2);
        $contraintesChecked[] = $jour . '_' . $heure;

        if ($creneauPrefere === null) $creneauPrefere = $row['creneau_preference'];
        if ($coursSamedi === null) $coursSamedi = $row['cours_samedi'];
        if ($commentaire === null) $commentaire = $row['commentaire'];
    }

    // Vérifier si la fiche est validée
    $verrouille = false;
    $stmtVerif = $conn->prepare("SELECT statut, date_validation FROM contraintes WHERE id_utilisateur = ? LIMIT 1");
    $stmtVerif->execute([$id_utilisateur]);
    $contrainte = $stmtVerif->fetch(PDO::FETCH_ASSOC);
    if ($contrainte && $contrainte['statut'] === 'validée') {
        $verrouille = true;
    }

    // Nombre max de contraintes autorisé
    $stmt2 = $conn->prepare("SELECT nb_contrainte FROM enseignants WHERE id_utilisateur = ?");
    $stmt2->execute([$id_utilisateur]);
    $nb_contrainte = $stmt2->fetch(PDO::FETCH_ASSOC)['nb_contrainte'] ?? 5;
    ?>

    <div class="fiche-container">
        <div class="enseignant-info">
            <?= htmlspecialchars($enseignant['prenom'] . ' ' . $enseignant['nom']) ?>
        </div>

        <?php if ($verrouille): ?>
            <div class="alert alert-warning">Cette fiche a été validée et ne peut plus être modifiée.</div>
            <?php if (!empty($contrainte['date_validation'])): ?>
                <div class="alert alert-info">Fiche remplie le <?= date('d/m/Y à H:i', strtotime($contrainte['date_validation'])) ?>.</div>
            <?php endif; ?>
        <?php endif; ?>

        <form method="post" action="../../src/Enseignant/EnregistrerContraintes.php">
            <input type="hidden" name="modification_gestionnaire" value="1">
            <input type="hidden" name="id_utilisateur" value="<?= $id_utilisateur ?>">

            <p>Indiquez les plages horaires durant lesquelles vous ne pouvez pas enseigner :</p>
            <table>
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
                $horaires = ["8_10" => "8h-10h", "10_12" => "10h-12h", "14_16" => "14h-16h", "16_18" => "16h-18h"];
                $jours = ["lundi", "mardi", "mercredi", "jeudi", "vendredi"];

                foreach ($horaires as $heure_key => $heure_label) {
                    echo "<tr><td>$heure_label</td>";
                    foreach ($jours as $jour) {
                        $name = "{$jour}_{$heure_key}";
                        $checked = in_array($name, $contraintesChecked) ? 'checked' : '';
                        $disabled = $verrouille ? 'disabled' : '';
                        echo "<td><input type='checkbox' name='contraintes[]' value='$name' $checked $disabled></td>";
                    }
                    echo "</tr>";
                }
                ?>
                </tbody>
            </table>

            <p>Je préfère, si possible, éviter le créneau :</p>
            <label><input type="radio" name="creneau_prefere" value="8h-10h" <?= ($creneauPrefere === "8h-10h") ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> 8h-10h</label><br>
            <label><input type="radio" name="creneau_prefere" value="16h-18h" <?= ($creneauPrefere === "16h-18h") ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> 16h-18h</label><br>

            <p>J’accepte d’avoir cours le samedi matin :</p>
            <label><input type="radio" name="cours_samedi" value="oui" <?= ($coursSamedi === "oui") ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Oui</label><br>
            <label><input type="radio" name="cours_samedi" value="non" <?= ($coursSamedi === "non") ? 'checked' : '' ?> <?= $verrouille ? 'disabled' : '' ?>> Non</label>

            <div style="margin-top: 1em">
                <label for="commentaire">Commentaire :</label><br>
                <textarea name="commentaire" id="commentaire" rows="4" <?= $verrouille ? 'disabled' : '' ?>><?= htmlspecialchars($commentaire ?? '') ?></textarea>
            </div>
            <?php if (!$verrouille): ?>
                <div style="display: flex; gap: 10px; justify-content: center; margin-top: 1em;">
                    <a href="../../index.php?action=ficheEnseignant" class="btn-submit" style="text-align:center; text-decoration: none;">Retour</a>
                    <button type="submit" class="btn-submit">Valider la modification</button>
                </div>
            <?php endif; ?>

        </form>
    </div>

    <script>
        const nbContraintes = <?= $nb_contrainte ?>;
        function limiterContraintes() {
            const checkboxes = document.querySelectorAll('input[type="checkbox"]');
            let checkedCount = 0;

            checkboxes.forEach(cb => { if (cb.checked) checkedCount++; });

            if (checkedCount > nbContraintes) {
                alert("Vous ne pouvez sélectionner que " + nbContraintes + " contraintes au maximum.");
                event.target.checked = false;
            }
        }

        document.addEventListener("DOMContentLoaded", () => {
            document.querySelectorAll('input[type="checkbox"]').forEach(cb => {
                cb.addEventListener("change", limiterContraintes);
            });
        });
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            margin: 2em;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h1 {
            color: #000;
            margin-bottom: 1em;
        }

        .fiche-container {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            padding: 2em;
            max-width: 1000px;
            width: 100%;
            color: #000;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 1em;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        textarea {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 4px;
            padding: 10px;
        }

        .btn-submit, .btn-download {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 1em;
            font-weight: bold;
            border-radius: 5px;
            border: none;
            cursor: pointer;
        }

        .btn-submit {
            background-color: #fff495;
            color: #000;
        }

        .btn-submit:hover {
            background-color: #FFEF65;
        }

        .btn-download {
            background-color: #FFEF65;
            color: #000;
            width: 25%;
            margin: 0 auto;
        }

        .alert {
            padding: 10px;
            margin-bottom: 1em;
            border-radius: 4px;
            text-align: center;
        }

        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
    <script>
        window.addEventListener("beforeunload", function () {
            navigator.sendBeacon("../../src/Enseignant/libererFiche.php", new URLSearchParams({
                id_utilisateur: "<?= $id_utilisateur ?>"
            }));
        });
    </script>
    <?php
} else {
    echo "<p>Type de fiche inconnu.</p>";
}

?>
