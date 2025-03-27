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
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
            <script>
                function filterByType(select) {
                    let type = select.value;
                    document.querySelectorAll('tr[data-type]').forEach(row => {
                        row.style.display = (type === 'all' || row.dataset.type === type) ? '' : 'none';
                    });
                }

                function loadFicheDetails(fiche) {
                    const container = document.getElementById("ficheDetailsContainer");
                    container.innerHTML = "";

                    const title = document.createElement("h5");
                    title.textContent = `${fiche.fiche_type} de ${fiche.prenom} ${fiche.nom}`;
                    container.appendChild(title);

                    let content = "";
                    const grouped = fiche.grouped_fiches;

                    if (fiche.fiche_type === 'Fiche Contrainte') {
                        content += `<div class='mb-2'><strong>Créneaux à éviter :</strong><ul>`;
                        grouped.forEach(c => {
                            if (c.jour && c.heure_debut && c.heure_fin) {
                                content += `<li>L'enseignant souhaite éviter le créneau : ${c.jour} de ${c.heure_debut}h à ${c.heure_fin}h</li>`;
                            }
                        });
                        content += `</ul></div>`;

                        const pref = grouped[0]?.creneau_preference || 'Non précisé';
                        const samedi = grouped[0]?.cours_samedi === 'oui' ?
                            'L’enseignant accepte les cours le samedi.' :
                            'L’enseignant ne souhaite pas de cours le samedi.';

                        content += `<div class='mb-2'><strong>Préférence :</strong> ${pref}</div>`;
                        content += `<div class='mb-2'><strong>Cours le samedi :</strong> ${samedi}</div>`;

                    } else if (fiche.fiche_type === 'Fiche Ressource') {
                        grouped.forEach((r, i) => {
                            content += `<div class='mb-2'><strong>Ressource ${i + 1}</strong><br>`;
                            content += `<strong>Type de salle :</strong> ${r.type_salle}<br>`;
                            content += `<strong>Équipements spécifiques :</strong> ${r.equipements_specifiques}<br>`;
                            content += `<strong>Détails :</strong> ${r.details}</div><hr>`;
                        });

                    } else if (fiche.fiche_type === 'Fiche Prévisionnelle') {
                        grouped.forEach((v, i) => {
                            content += `<div class='mb-2'><strong>Cours ${i + 1}</strong><br>`;
                            content += `<strong>Nom :</strong> ${v.nom_cours}<br>`;
                            content += `<strong>Formation :</strong> ${v.formation} - <strong>Semestre :</strong> ${v.semestre}<br>`;
                            content += `<strong>Code :</strong> ${v.code_cours}<br>`;
                            content += `<strong>Volume horaire demandé :</strong><br>
                                CM : ${v.nb_CM}h | TD : ${v.nb_TD}h | TP : ${v.nb_TP}h | EI : ${v.nb_EI}h`;
                            if (v.remarques) content += `<br><strong>Remarques :</strong> ${v.remarques}`;
                            content += `</div><hr>`;
                        });
                    }

                    container.innerHTML += content;
                }
            </script>
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
                .badge.bg-success { background-color: #28a745; color: white; }
                .badge.bg-warning { background-color: #FFC300; color: black; }
                .badge.bg-danger { background-color: red; color: white; }
                .btn-icon { background: none; border: none; cursor: pointer; font-size: 1.2rem; }
            </style>
        </head>
        <body>

        <?php include 'src/Gestionnaire/Navbar_top.html'; ?>

        <div class="container my-5">
            <h1 class="text-center mb-4"><br>Gestion des Fiches Enseignants</h1>

            <div class="mb-3 text-center">
                <label for="filtre">Filtrer par type de fiche :</label>
                <select id="filtre" onchange="filterByType(this)">
                    <option value="all">Toutes</option>
                    <option value="Fiche Contrainte">Fiche Contrainte</option>
                    <option value="Fiche Ressource">Fiche Ressource</option>
                    <option value="Fiche Prévisionnelle">Fiche Prévisionnelle</option>
                </select>
            </div>

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
                <?php foreach ($fichesParEnseignant as $fiche): ?>
                    <tr data-type="<?= htmlspecialchars($fiche['fiche_type']) ?>">
                        <td><?= htmlspecialchars($fiche['nom']) ?></td>
                        <td><?= htmlspecialchars($fiche['prenom']) ?></td>
                        <td>
                            <button class="btn-icon" data-bs-toggle="modal" data-bs-target="#ficheModal"
                                    onclick='loadFicheDetails(<?= json_encode($fiche) ?>)'>
                                📄 <?= htmlspecialchars($fiche['fiche_type']) ?>
                            </button>
                        </td>
                        <td>
                            <span class="badge <?= $fiche['statut'] === 'validée' ? 'bg-success' : ($fiche['statut'] === 'refuse' ? 'bg-danger' : 'bg-warning') ?>">
                                <?= htmlspecialchars(ucfirst($fiche['statut'])) ?>
                            </span>
                        </td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="fiche_id" value="<?= $fiche['grouped_fiches'][0]['id_contrainte'] ?? $fiche['grouped_fiches'][0]['id_ressource'] ?? $fiche['grouped_fiches'][0]['id_voeu'] ?>">
                                <input type="hidden" name="table" value="<?= $fiche['table'] ?>">
                                <?php if ($fiche['statut'] === 'validée'): ?>
                                    <button type="submit" name="action_type" value="devalider" class="btn btn-warning">Dévalider</button>
                                <?php elseif ($fiche['statut'] === 'en attente'): ?>
                                    <button type="submit" name="action_type" value="valider" class="btn btn-success">Valider</button>
                                <?php else: ?>
                                    <button class="btn btn-secondary" disabled>Action effectuée</button>
                                <?php endif; ?>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>

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

        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
