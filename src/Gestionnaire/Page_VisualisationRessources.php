<?php
require_once __DIR__ . '/../Db/connexionFactory.php';
use src\Db\connexionFactory;

try {
    $bdd = connexionFactory::makeConnection();

    $requete = "SELECT semestre, code_cours, nom_cours FROM cours EXCEPT 
    (SELECT semestre, code_cours, nom_cours from cours WHERE nom_cours = \"Autre (préciser dans les remarques)\"
    UNION
    SELECT semestre, code_cours, nom_cours from cours WHERE nom_cours = \"Forfait suivi de stage\");";

    $stmt = $bdd->query("$requete");
    $coursList = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    echo "Erreur : " . $e->getMessage();
}
?>


<style>
        #main-content {
            font-family: Arial, sans-serif;
            background: #f9f9f9;
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }
        th {
            background: #f0f0f0;
        }
        .status-valid {
            background-color: #d4edda; /* Vert pâle */
            color: #155724;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
        .status-invalid {
            background-color: #f8d7da; /* Rouge pâle */
            color: #721c24;
            padding: 5px 10px;
            border-radius: 4px;
            display: inline-block;
        }
    </style>



<div id="main-content">
    <h2>Fiches ressources des modules</h2>
    <table style="width:100%; border-collapse: collapse; margin-top: 20px;">
        <thead>
        <tr style="background-color: #f0f0f0;">
            <th style="padding: 10px; border: 1px solid #ddd;">Semestre</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Code cours</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Nom du cours</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Responsable</th>
            <th style="padding: 10px; border: 1px solid #ddd;">Statut</th>

        </tr>
        </thead>
        <tbody>
        <?php foreach ($coursList as $cours): ?>
            <tr>
                <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($cours['semestre']) ?></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($cours['code_cours']) ?></td>
                <td style="padding: 10px; border: 1px solid #ddd;"><?= htmlspecialchars($cours['nom_cours']) ?></td>
                <td style="padding: 10px; border: 1px solid #ddd;">...</td>
                <td style="padding: 10px; border: 1px solid #ddd;">
                    <span style="background-color: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px;">Non validée</span>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>











<!--    <div id="main-content">-->
<!--        <h2>Fiches ressources des modules</h2>-->
<!--        <table style="width:100%; border-collapse: collapse; margin-top: 20px;">-->
<!--            <thead>-->
<!--            <tr style="background-color: #f0f0f0;">-->
<!--                <th style="padding: 10px; border: 1px solid #ddd;">Semestre</th>-->
<!--                <th style="padding: 10px; border: 1px solid #ddd;">Code cours</th>-->
<!--                <th style="padding: 10px; border: 1px solid #ddd;">Nom du cours</th>-->
<!--                <th style="padding: 10px; border: 1px solid #ddd;">Responsable</th>-->
<!--                <th style="padding: 10px; border: 1px solid #ddd;">Statut</th>-->
<!--            </tr>-->
<!--            </thead>-->
<!--            <tbody>-->
<!--            <tr>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">S3</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">INF302</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">Programmation Web</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">Camille Lefevre</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">-->
<!--                    <span style="background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px;">Validée</span>-->
<!--                </td>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">S4</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">INF401</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">Sécurité des systèmes</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">Paul Durand</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">-->
<!--                    <span style="background-color: #f8d7da; color: #721c24; padding: 4px 8px; border-radius: 4px;">Non validée</span>-->
<!--                </td>-->
<!--            </tr>-->
<!--            <tr>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">S2</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">MAT201</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">Maths discrètes</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">Sophie Bernard</td>-->
<!--                <td style="padding: 10px; border: 1px solid #ddd;">-->
<!--                    <span style="background-color: #d4edda; color: #155724; padding: 4px 8px; border-radius: 4px;">Validée</span>-->
<!--                </td>-->
<!--            </tr>-->
<!--            </tbody>-->
<!--        </table>-->
<!--    </div>-->
