<?php

namespace src\Action;

use src\Db\connexionFactory;

class GestionnaireValidationFicheAction extends Action
{
    public function execute(): string
    {
        $conn = connexionFactory::makeConnection();

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type'], $_POST['table'], $_POST['fiche_id'])) {
            $ficheId = $_POST['fiche_id'];
            $table = $_POST['table'];
            $actionType = $_POST['action_type'];

            $id_column = ($table === "voeux") ? "id_voeu" : (($table === "details_cours") ? "id_ressource" : "id_contrainte");

            $stmt = $conn->prepare("UPDATE $table SET statut = ? WHERE $id_column = ?");
            $stmt->execute([$actionType === "valider" ? "validée" : "en attente", $ficheId]);

            $_SESSION['success_message'] = $actionType === "valider" ? "La fiche a été validée avec succès." : "La fiche a été dévalidée.";
            header("Location: index.php?action=ficheEnseignant");
            exit();
        }

        $tables = [
            'contraintes' => [
                'sql' => "SELECT contraintes.*, utilisateurs.nom, utilisateurs.prenom, 'Fiche Contrainte' AS fiche_type FROM contraintes
                         JOIN utilisateurs ON contraintes.id_utilisateur = utilisateurs.id_utilisateur
                         WHERE contraintes.creneau_preference IS NOT NULL
                         ORDER BY contraintes.statut ASC",
                'group_key' => fn($fiche) => $fiche['fiche_type'] . '|' . $fiche['nom'] . '|' . $fiche['prenom']
            ],
            'details_cours' => [
                'sql' => "SELECT details_cours.*, utilisateurs.nom, utilisateurs.prenom, 'Fiche Ressource' AS fiche_type FROM details_cours
                         JOIN enseignants ON details_cours.id_responsable_module = enseignants.id_enseignant
                         JOIN utilisateurs ON enseignants.id_utilisateur = utilisateurs.id_utilisateur
                         ORDER BY details_cours.statut ASC",
                'group_key' => fn($fiche) => $fiche['fiche_type'] . '|' . $fiche['nom'] . '|' . $fiche['prenom']
            ],
            'voeux' => [
                'sql' => "SELECT voeux.*, cours.nom_cours, cours.formation, cours.semestre, cours.code_cours, cours.nb_heures_cm, cours.nb_heures_td, cours.nb_heures_tp, cours.nb_heures_ei,
                                utilisateurs.nom, utilisateurs.prenom, 'Fiche Prévisionnelle' AS fiche_type 
                         FROM voeux
                         JOIN enseignants ON voeux.id_enseignant = enseignants.id_enseignant
                         JOIN utilisateurs ON enseignants.id_utilisateur = utilisateurs.id_utilisateur
                         JOIN cours ON voeux.id_cours = cours.id_cours
                         ORDER BY voeux.statut ASC",
                'group_key' => fn($fiche) => $fiche['fiche_type'] . '|' . $fiche['nom'] . '|' . $fiche['prenom']
            ]
        ];

        $fichesParEnseignant = [];

        foreach ($tables as $table => $data) {
            $stmt = $conn->query($data['sql']);
            while ($fiche = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $key = $data['group_key']($fiche);
                if (!isset($fichesParEnseignant[$key])) {
                    $fichesParEnseignant[$key] = [
                        'nom' => $fiche['nom'],
                        'prenom' => $fiche['prenom'],
                        'fiche_type' => $fiche['fiche_type'],
                        'table' => $table,
                        'statut' => $fiche['statut'],
                        'grouped_fiches' => []
                    ];
                }
                $fichesParEnseignant[$key]['grouped_fiches'][] = $fiche;
            }
        }

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Gestion des Fiches Enseignants</title>
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
                    background-color: #d4edda;
                    color: #155724;
                    padding: 5px 10px;
                    border-radius: 4px;
                    display: inline-block;
                }
                .status-invalid {
                    background-color: #f8d7da;
                    color: #721c24;
                    padding: 5px 10px;
                    border-radius: 4px;
                    display: inline-block;
                }
                .btn-valider {
                    background-color: #28a745;
                    color: white;
                    padding: 6px 12px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .btn-devalider {
                    background-color: #ffc107;
                    color: black;
                    padding: 6px 12px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
                .btn-details, .btn-modifier {
                    margin-left: 5px;
                    background-color: #007bff;
                    color: white;
                    padding: 6px 10px;
                    border: none;
                    border-radius: 4px;
                    cursor: pointer;
                }
            </style>
            <script>
                function filterByType(select) {
                    let type = select.value;
                    document.querySelectorAll('tr[data-type]').forEach(row => {
                        row.style.display = (type === 'all' || row.dataset.type === type) ? '' : 'none';
                    });
                }
            </script>
        </head>
        <body>
        <?php include 'src/Gestionnaire/Navbar_top.html'; ?>
        <div id="main-content">
            <h2>Gestion des Fiches Enseignants</h2>
            <div class="mb-3">
                <label for="filtre">Filtrer par type de fiche :</label>
                <select id="filtre" onchange="filterByType(this)">
                    <option value="all">Toutes</option>
                    <option value="Fiche Contrainte">Fiche Contrainte</option>
                    <option value="Fiche Ressource">Fiche Ressource</option>
                    <option value="Fiche Prévisionnelle">Fiche Prévisionnelle</option>
                </select>
            </div>
            <table>
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Type</th>
                    <th>Statut</th>
                    <th>Actions</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($fichesParEnseignant as $fiche): ?>
                    <tr data-type="<?= htmlspecialchars($fiche['fiche_type']) ?>">
                        <td><?= htmlspecialchars($fiche['nom']) ?></td>
                        <td><?= htmlspecialchars($fiche['prenom']) ?></td>
                        <td><?= htmlspecialchars($fiche['fiche_type']) ?></td>
                        <td>
                            <?php if ($fiche['statut'] === 'validée'): ?>
                                <span class="status-valid">Validée</span>
                            <?php else: ?>
                                <span class="status-invalid">Non validée</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="post" style="display:inline-block;">
                                <input type="hidden" name="fiche_id" value="<?= $fiche['grouped_fiches'][0]['id_contrainte'] ?? $fiche['grouped_fiches'][0]['id_ressource'] ?? $fiche['grouped_fiches'][0]['id_voeu'] ?>">
                                <input type="hidden" name="table" value="<?= $fiche['table'] ?>">
                                <?php if ($fiche['statut'] === 'validée'): ?>
                                    <button type="submit" name="action_type" value="devalider" class="btn-devalider">Dévalider</button>
                                <?php else: ?>
                                    <button type="submit" name="action_type" value="valider" class="btn-valider">Valider</button>
                                <?php endif; ?>
                            </form>
                            <a href="index.php?action=detailsFiche&type=<?= urlencode($fiche['table']) ?>&id=<?= urlencode($fiche['grouped_fiches'][0]['id_contrainte'] ?? $fiche['grouped_fiches'][0]['id_ressource'] ?? $fiche['grouped_fiches'][0]['id_voeu']) ?>" class="btn-details">Détails</a>
                            <a href="index.php?action=modifierFiche&type=<?= urlencode($fiche['table']) ?>&id=<?= urlencode($fiche['grouped_fiches'][0]['id_contrainte'] ?? $fiche['grouped_fiches'][0]['id_ressource'] ?? $fiche['grouped_fiches'][0]['id_voeu']) ?>" class="btn-modifier">Modifier</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
