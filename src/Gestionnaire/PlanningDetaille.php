<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Planning détaillé</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css" >
</head>
<style>
    body {
        margin-left: 200px;
        padding: 0;
        font-family: 'Poppins', sans-serif;
        background-color: #34495e;
        display: flex;
        flex-direction : column;
        align-items: center;
        justify-content: center;
    }


    .table th {
        background-color: #f2c94c !important;
    }

    .table-sm td {
        font-size: 12px;
    }

    select {
        margin-bottom: 20px;
    }

    .tdInvisible{
        background-color: white !important;
        border: white !important;

    }

    .trInvisible{
        border-top: white !important;
        border-bottom: white !important;
        background-color: white !important;
    }

</style>
<body>
<div class="container my-4">
    <h1 class="text-center text-white">Planning Détaillé</h1>
    <div class="row mb-3">
        <label for="semester" class="form-label text-white">Choisir le semestre :</label>
        <select class="form-select w-25" id="semester" name="semester" required>
            <?php
            use src\Db\connexionFactory;
            $bdd = connexionFactory::makeConnection();
            $formations = $bdd->query('SELECT DISTINCT semestre FROM cours');
            foreach ($formations as $formation) {
                $selected = ($formation['semestre'] == $_GET['semester']) ? 'selected' : '';
                echo "<option value='{$formation['semestre']}' $selected>{$formation['semestre']}</option>";
            }
            $repartition = $bdd->query(
                'SELECT DISTINCT concat(substring(utilisateurs.nom, 1, 1),substring(utilisateurs.prenom,1,1)) as responsable, cours.nom_cours  ,cours.semestre,  cours.nb_heures_total, cours.nb_heures_cm, cours.nb_heures_tp, cours.nb_heures_ei, cours.nb_heures_td, repartition_heures.type_heure, repartition_heures.nb_heures_par_semaine, repartition_heures.semaine_debut, repartition_heures.semaine_fin
                            FROM cours
                            INNER JOIN repartition_heures ON cours.id_cours = repartition_heures.id_cours
                            INNER JOIN details_cours ON cours.id_cours = details_cours.id_cours
                            INNER JOIN enseignants ON details_cours.id_responsable_module = enseignants.id_enseignant
                            INNER JOIN utilisateurs ON enseignants.id_utilisateur = utilisateurs.id_utilisateur')->fetchAll(PDO::FETCH_ASSOC);

            ?>

        </select>
    </div>

    <div id="tableau">
        <table class="table table-bordered table-striped text-center">
            <thead class="table-warning">
            <tr class="trInvisible">
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>

                <?php
                echo "<th>Semestre " . preg_replace('/[^0-9]/', '', $_GET['semester']) . " BUT </th>";
                ?>
                <th>Ressources + SAE</th>
                <?php
                $coursList = $bdd->query(
                    "SELECT nom_cours, nb_heures_cm, nb_heures_td, nb_heures_tp, semestre, nb_heures_total, nb_heures_ei
                    FROM cours WHERE semestre = '{$_GET['semester']}'"
                )->fetchAll(PDO::FETCH_ASSOC);

                foreach ($coursList as $cours) {
                    echo "<th>{$cours['nom_cours']}</th>";
                }
                ?>
                <td class="tdInvisible"></td>
            </tr>
            <tr class="trInvisible">
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td>2024-2025</td>
                <td>Responsable</td>
                <?php
                foreach ($coursList as $cours) {
                    $tdAjoute = false;
                    foreach ($repartition as $rep) {
                        if ($cours['nom_cours'] == $rep['nom_cours']) {
                            echo "<td>{$rep['responsable']}</td>";
                            $tdAjoute = true;
                            break;
                        }
                    }
                    if (!$tdAjoute) {
                        echo "<td></td>";
                    }
                }
                ?>
                <td class="tdInvisible"></td>
            </tr>
            <tr class="trInvisible">
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td style="background-color: #2e86c1; border-color: #2e86c1">code res/matière/SAE</td>
                <?php
                foreach ($coursList as $cours) {
                    echo "<td style='background-color: #2e86c1 !important; border-color: #2e86c1'></td>";
                }
                ?>
                <td class="tdInvisible"></td>
            </tr>
            </thead>
            <tbody>
            <tr class="trInvisible">
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td>PN dont TP adapté</td>
                <?php
                foreach ($coursList as $cours) {
                    echo "
                        <td>
                            <table class='table table-sm mb-0'>
                                <tr>
                                    <td>CM</td>
                                    <td>TD</td>
                                    <td>TP</td>
                                    
                                </tr>
                                <tr>
                                    <td>{$cours['nb_heures_cm']}</td>
                                    <td>{$cours['nb_heures_td']}</td>
                                    <td>{$cours['nb_heures_tp']}</td>
                                </tr>
                                
                            </table>
                        </td>";
                }
                ?>
                <td></td>
            </tr>
            <tr class="trInvisible">
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td>Heures totales ET.h. ENS</td>
                <?php
                $total = 0;
                foreach ($coursList as $cours) {
                    echo "<td>{$cours['nb_heures_total']}</td>";
                    $total += $cours['nb_heures_total'];
                }
                echo "<td>$total</td>";
                ?>
            </tr>
            <tr class="trInvisible">
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td class="tdInvisible"></td>
                <td>H. reservées SAE (*)</td>
                <?php
                $total = 0;
                foreach ($coursList as $cours) {
                    echo "<td>{$cours['nb_heures_ei']}</td>";
                }
                echo "<td>$total</td>";
                ?>
            </tr>

            <!-- partie a double entrée -->
            <?php

            $totauxParCours = [];

            foreach ($coursList as $cours) {
                // Initialiser les totaux pour ce cours
                $totauxParCours[$cours['nom_cours']] = [
                    'CM' => 0,
                    'TD' => 0,
                    'TP' => 0,
                ];
            }

            // Début du semestre 1 : semaine 36 (02/09/2024)
            // Début du semestre 2 : semaine 3 (13/01/2025)
            if ($_GET['semester'] == 'S1' || $_GET['semester'] == 'S3' || 'S5' == substr($_GET['semester'], 0, 2) ) {
                $dateDebut = new DateTime('2024-09-02');
                $semaineActuelle = 36;
            } else {
                $dateDebut = new DateTime('2025-01-13');
                $semaineActuelle = 3;
            }
            $nbSemaines = 20;
            $compteurCyclique = 1;
            $incrementeur = 1;
            $vacToussaint = 44;
            $vacNoel = 52;
            $vacNoelFin = 1;
            $vacHiver = 8;
            $vacPrintemps = 15;
            $vacPrintempsFin = 16;



            for ($i = 0; $i < $nbSemaines; $i++) {
                $total = 0;
                if ($semaineActuelle == $vacToussaint || $semaineActuelle == $vacNoel || $semaineActuelle == $vacNoelFin || $semaineActuelle == $vacHiver || $semaineActuelle == $vacPrintemps || $semaineActuelle == $vacPrintempsFin) {
                    echo "<tr>";
                    echo "<td style='background-color: rgb(123,183,125) !important; border-color: #7bb77d'>$incrementeur</td>";
                    echo "<td style='background-color: rgb(123,183,125) !important; border-color: #7bb77d'>$compteurCyclique </td>";
                    echo "<td style='background-color: rgb(123,183,125) !important; border-color: #7bb77d'>$semaineActuelle </td>";
                    echo "<td style='background-color: rgb(123,183,125) !important; border-color: #7bb77d'>" . $dateDebut->format('d/m/Y') . "</td>";
                    echo "<td id='modifiable je suppose' style='background-color: rgb(123,183,125) !important; border-color: #7bb77d'> </td>";
                } else {
                    echo "<tr>";
                    echo "<td>" . $incrementeur . "</td>";
                    echo "<td>" . $compteurCyclique . "</td>";
                    echo "<td>" . $semaineActuelle . "</td>";
                    echo "<td>" . $dateDebut->format('d/m/Y') . "</td>";
                    echo "<td id='modifiable je suppose'></td>";
                }

                foreach ($coursList as $cours) {
                    $tdAjoute = false;
                    $tpAjoute = false;
                    $cmAjoute = false;

                    $valueCM = '';
                    $valueTD = '';
                    $valueTP = '';

                    // On ouvre une cellule de tableau
                    if ($semaineActuelle == $vacToussaint || $semaineActuelle == $vacNoel || $semaineActuelle == $vacNoelFin || $semaineActuelle == $vacHiver || $semaineActuelle == $vacPrintemps || $semaineActuelle == $vacPrintempsFin) {
                        echo "<td style='background-color: rgb(123,183,125) !important; border-color: #7bb77d'>";
                    } else {
                        echo "<td>";
                    }

                    // On crée un tableau à l'intérieur de chaque cellule pour afficher les heures par type de cours
                    echo "<table class='table table-sm mb-1' style='border: transparent; '>";
                    echo "<tr>";

                    // On parcourt la répartition des heures
                    foreach ($repartition as $rep) {
                        if ($cours['nom_cours'] == $rep['nom_cours']) {
                            if ($rep['semaine_debut'] <= $semaineActuelle && $rep['semaine_fin'] >= $semaineActuelle) {
                                if ($semaineActuelle != $vacToussaint && $semaineActuelle != $vacNoel && $semaineActuelle != $vacNoelFin && $semaineActuelle != $vacHiver && $semaineActuelle != $vacPrintemps && $semaineActuelle != $vacPrintempsFin) {
                                    if ($rep['type_heure'] == 'CM' && !$cmAjoute) {
                                        $valueCM = $rep['nb_heures_par_semaine'];
                                        $cmAjoute = true;
                                    }
                                    if ($rep['type_heure'] == 'TD' && !$tdAjoute) {
                                        $valueTD = $rep['nb_heures_par_semaine'];
                                        $tdAjoute = true;
                                    }
                                    if ($rep['type_heure'] == 'TP' && !$tpAjoute) {
                                        $valueTP = $rep['nb_heures_par_semaine'];
                                        $tpAjoute = true;
                                    }

                                }
                            }
                        }

                    }

                    echo "<td style='width: 33% ;background-color: transparent !important;'>$valueCM  </td><td style='width: 33%;background-color: transparent !important;'>$valueTD</td><td style='width: 33%;background-color: transparent !important;'>$valueTP</td>";
                    $valueCM = ($valueCM == '') ? 0 : $valueCM;
                    $valueTD = ($valueTD == '') ? 0 : $valueTD;
                    $valueTP = ($valueTP == '') ? 0 : $valueTP;
                    $totauxParCours[$cours['nom_cours']]['CM'] += $valueCM;
                    $totauxParCours[$cours['nom_cours']]['TD'] += $valueTD;
                    $totauxParCours[$cours['nom_cours']]['TP'] += $valueTP;
                    echo "</tr>";
                    echo "</table>";
                    $total += $valueCM + $valueTD + $valueTP;

                    // On ferme la cellule de tableau
                    echo "</td>";
                }
                if ($semaineActuelle == $vacToussaint || $semaineActuelle == $vacNoel || $semaineActuelle == $vacNoelFin || $semaineActuelle == $vacHiver || $semaineActuelle == $vacPrintemps || $semaineActuelle == $vacPrintempsFin) {
                    echo "<td style='background-color: rgb(123,183,125) !important; border-color: #7bb77d'></td>";
                } else {
                    echo "<td>$total</td>";
                }
                echo "</tr>";

                // Passer à la semaine suivante
                $dateDebut->modify('+7 days');
                $semaineActuelle = ($semaineActuelle >= 52) ? 1 : $semaineActuelle + 1;
                $compteurCyclique = ($compteurCyclique % 4) + 1;
                $incrementeur++;
            }

            echo "<tr class='table-warning'>
                    <td colspan='5'>Totaux</td>";
                        foreach ($coursList as $cours) {
                            $totaux = $totauxParCours[$cours['nom_cours']];
                            echo "<td>
                        <table class='table table-sm mb-1' style='border: transparent;'>
                            <tr>
                                <td style='width: 33%;background-color: transparent !important;'>{$totaux['CM']}</td>
                                <td style='width: 33%;background-color: transparent !important;'>{$totaux['TD']}</td>
                                <td style='width: 33%;background-color: transparent !important;'>{$totaux['TP']}</td>
                            </tr>
                        </table>
                      </td>";
            }
            echo "<td></td>";
            echo "</tr>";
            ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    const select = document.querySelector('#semester');
    select.addEventListener('change', () => {
        const semester = select.value;
        window.location.href = `index.php?action=ficheDetaille&semester=${semester}`;
    });
</script>
</body>
</html>
