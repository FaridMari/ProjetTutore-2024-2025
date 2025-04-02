<?php

namespace src\Action;

use src\Db\connexionFactory;

class GestionnaireValidationFicheAction extends Action
{
    public function execute(): string
    {
        $conn = connexionFactory::makeConnection();

        // Gestion validation / dévalidation
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['action_type'], $_POST['table'], $_POST['fiche_id'])) {
            $ficheId = $_POST['fiche_id'];
            $table = $_POST['table'];
            $actionType = $_POST['action_type'];

            $id_column = ($table === "voeux") ? "id_voeu" : (($table === "details_cours") ? "id_ressource" : "id_contrainte");

            $stmt = $conn->prepare("UPDATE $table SET statut = ? WHERE $id_column = ?");
            $stmt->execute([$actionType === "valider" ? "validée" : "en attente", $ficheId]);

            $_SESSION['success_message'] = $actionType === "valider"
                ? "La fiche a été validée avec succès." : "La fiche a été dévalidée.";
            header("Location: index.php?action=ficheEnseignant");
            exit();
        }

        // --- Regrouper les fiches remplies ---
        $tables = [
            'contraintes' => [
                'sql' => "SELECT contraintes.*, utilisateurs.nom, utilisateurs.prenom, 'Fiche Contrainte' AS fiche_type FROM contraintes
                          JOIN utilisateurs ON contraintes.id_utilisateur = utilisateurs.id_utilisateur
                          WHERE contraintes.creneau_preference IS NOT NULL
                          ORDER BY contraintes.statut ASC",
                'group_key' => fn($fiche) => $fiche['fiche_type'] . '|' . $fiche['nom'] . '|' . $fiche['prenom']
            ],
            'details_cours' => [
                'sql' => "SELECT details_cours.*, cours.nom_cours, utilisateurs.nom, utilisateurs.prenom, 'Fiche Ressource' AS fiche_type 
                          FROM details_cours
                          JOIN cours ON details_cours.id_cours = cours.id_cours
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

        $GLOBALS['fichesParEnseignant'] = $fichesParEnseignant;

        // --- Trouver enseignants n'ayant pas rempli certains types de fiches ---
        $allEnseignants = $conn->query("SELECT id_utilisateur, nom, prenom FROM utilisateurs WHERE role = 'enseignant'")->fetchAll(\PDO::FETCH_ASSOC);

        $enseignantsSansFiche = [];

        foreach ($allEnseignants as $enseignant) {
            $id = $enseignant['id_utilisateur'];

            $types = [
                ['type' => 'Fiche Contrainte', 'query' => "SELECT COUNT(*) FROM contraintes WHERE id_utilisateur = ?"],
                ['type' => 'Fiche Ressource', 'query' => "SELECT COUNT(*) FROM details_cours dc JOIN enseignants e ON dc.id_responsable_module = e.id_enseignant WHERE e.id_utilisateur = ?"],
                ['type' => 'Fiche Prévisionnelle', 'query' => "SELECT COUNT(*) FROM voeux v JOIN enseignants e ON v.id_enseignant = e.id_enseignant WHERE e.id_utilisateur = ?"]
            ];

            foreach ($types as $type) {
                $stmt = $conn->prepare($type['query']);
                $stmt->execute([$id]);
                if ($stmt->fetchColumn() == 0) {
                    $enseignantsSansFiche[] = [
                        'id_utilisateur' => $id,
                        'nom' => $enseignant['nom'],
                        'prenom' => $enseignant['prenom'],
                        'type_fiche' => $type['type']
                    ];
                }
            }
        }

        $GLOBALS['enseignantsSansFiche'] = $enseignantsSansFiche;

        ob_start();
        include 'src/Gestionnaire/Page_ValidationFiche.php';
        return ob_get_clean();
    }
}
