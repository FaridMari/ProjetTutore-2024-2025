<?php

namespace src\Action;

use src\Db\connexionFactory;

class GestionnaireValidationFicheAction extends Action
{
    public function execute(): string
    {
        $conn = connexionFactory::makeConnection();

        // Validation ou refus d'une fiche
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type'], $_POST['table'], $_POST['fiche_id'])) {
            $ficheId = $_POST['fiche_id'];
            $table = $_POST['table'];
            $actionType = $_POST['action_type']; // "valider" ou "refuser"

            $id_column = ($table === "voeux") ? "id_voeu" : (($table === "details_cours") ? "id_details" : "id_contrainte");

            $stmt = $conn->prepare("UPDATE $table SET statut = ? WHERE $id_column = ?");
            $stmt->execute([$actionType === "valider" ? "valide" : "refuse", $ficheId]);

            $_SESSION['success_message'] = $actionType === "valider" ? "La fiche a été validée avec succès." : "La fiche a été refusée.";
            header("Location: index.php?action=ficheEnseignant");
            exit();
        }

        // Récupération des fiches
        $tables = [
            'contraintes' => "SELECT contraintes.*, utilisateurs.nom, utilisateurs.prenom, 'Fiche Contrainte' AS fiche_type FROM contraintes
                              JOIN utilisateurs ON contraintes.id_utilisateur = utilisateurs.id_utilisateur where 
                              contraintes.creneau_preference IS NOT NULL
                              ORDER BY contraintes.statut ASC",

            'details_cours' => "SELECT details_cours.*, utilisateurs.nom, utilisateurs.prenom, 'Fiche Ressource' AS fiche_type FROM details_cours
                                JOIN enseignants ON details_cours.id_responsable_module = enseignants.id_enseignant
                                JOIN utilisateurs ON enseignants.id_utilisateur = utilisateurs.id_utilisateur
                                ORDER BY details_cours.statut ASC",

            'voeux' => "SELECT voeux.*, cours.nom_cours, utilisateurs.nom, utilisateurs.prenom, 'Fiche Prévisionnelle' AS fiche_type FROM voeux
                        JOIN enseignants ON voeux.id_enseignant = enseignants.id_enseignant
                        JOIN utilisateurs ON enseignants.id_utilisateur = utilisateurs.id_utilisateur
                        JOIN cours ON voeux.id_cours = cours.id_cours
                        ORDER BY voeux.statut ASC"
        ];

        $fichesParEnseignant = [];
        foreach ($tables as $table => $query) {
            $stmt = $conn->query($query);
            while ($fiche = $stmt->fetch(\PDO::FETCH_ASSOC)) {
                $key = $fiche['nom'] . '|' . $fiche['prenom']; // Clé unique par enseignant
                if (!isset($fichesParEnseignant[$key])) {
                    $fichesParEnseignant[$key] = [
                        'nom' => $fiche['nom'],
                        'prenom' => $fiche['prenom'],
                        'fiches' => []
                    ];
                }
                $fiche['table'] = $table;
                $fichesParEnseignant[$key]['fiches'][] = $fiche;
            }
        }

        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Gestion des Fiches Enseignants</title>
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

            <style>
                .table th {
                    background-color: #000;
                    color: #fff;
                    text-transform: uppercase;
                    font-weight: bold;
                }

                .table tbody tr:hover {
                    background-color: #FFE74A;
                    cursor: pointer;
                }

                .badge.bg-success {
                    background-color: #28a745;
                    color: white;
                }

                .badge.bg-warning {
                    background-color: #FFC300;
                    color: black;
                }

                .badge.bg-danger {
                    background-color: red;
                    color: white;
                }

                .btn-icon {
                    background: none;
                    border: none;
                    cursor: pointer;
                    font-size: 1.2rem;
                }
            </style>
        </head>
        <body>

        <?php include 'src/Gestionnaire/Navbar_top.html'; ?>

        <div class="container my-5">
            <h1 class="text-center mb-4"><br>Gestion des Fiches Enseignants</h1>

            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Fiches</th>
                    <th>Statut</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($fichesParEnseignant as $enseignant): ?>
                    <?php foreach ($enseignant['fiches'] as $fiche): ?>
                        <tr>
                            <td><?= htmlspecialchars($enseignant['nom']) ?></td>
                            <td><?= htmlspecialchars($enseignant['prenom']) ?></td>
                            <td>
                                <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#ficheModal"
                                        onclick="loadFicheDetails(<?= htmlspecialchars(json_encode($fiche)) ?>)">
                                    📄 <?= htmlspecialchars($fiche['fiche_type']) ?>
                                </button>
                            </td>
                            <td>
                                <span class="badge <?= $fiche['statut'] === 'valide' ? 'bg-success' : ($fiche['statut'] === 'refuse' ? 'bg-danger' : 'bg-warning') ?>">
                                    <?= htmlspecialchars(ucfirst($fiche['statut'])) ?>
                                </span>
                            </td>
                            <td>
                                <?php if ($fiche['statut'] === 'en attente'): ?>
                                    <form method="post">
                                        <input type="hidden" name="fiche_id" value="<?= $fiche['id_contrainte'] ?? $fiche['id_ressource'] ?? $fiche['id_voeu'] ?>">
                                        <input type="hidden" name="table" value="<?= $fiche['table'] ?>">
                                        <button type="submit" name="action_type" value="valider" class="btn btn-success">Valider</button>
                                        <button type="submit" name="action_type" value="refuser" class="btn btn-danger">Refuser</button>
                                    </form>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Action effectuée</button>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Modale pour afficher la fiche -->
        <div class="modal fade" id="ficheModal" tabindex="-1" aria-labelledby="ficheModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ficheModalLabel">Détails de la Fiche</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                    </div>
                    <div class="modal-body" id="ficheDetailsContainer"></div>
                </div>
            </div>
        </div>

        <script>
            function loadFicheDetails(fiche) {
                let content = `<p><strong>Type :</strong> ${fiche.fiche_type}</p>`;

                for (let key in fiche) {
                    if (!['nom', 'prenom', 'fiche_type', 'statut', 'table'].includes(key)) {
                        content += `<p><strong>${key.replace('_', ' ').toUpperCase()} :</strong> ${fiche[key]}</p>`;
                    }
                }

                document.getElementById("ficheDetailsContainer").innerHTML = content;
            }
        </script>

        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
