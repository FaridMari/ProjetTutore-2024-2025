<?php

use src\Db\ConnexionFactory; 

require_once 'VoeuHorsIUT.php';

class VoeuHorsIUTDTO {
    private $db;

    public function __construct() {
        $this->db = ConnexionFactory::makeConnection();
    }

    public function findByID($id): ?VoeuHorsIUT {
        try {
            $stmt = $this->db->prepare("SELECT * FROM voeux_hors_iut WHERE id_voeu_hi = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $voeu = new VoeuHorsIUT(
                    $data['id_voeu_hi'],
                    $data['id_enseignant'],
                    $data['composant'],
                    $data['formation'],
                    $data['module'],
                    $data['nb_heures_cm'],
                    $data['nb_heures_td'],
                    $data['nb_heures_tp'],
                    $data['nb_heures_ei'],
                    $data['nb_heures_total']
                );

                return $voeu;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll(): array {
        try {
            $stmt = $this->db->query("SELECT * FROM voeux_hors_iut");
            $voeuxHI = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $voeuHI = new VoeuHorsIUT(
                    $data['id_voeu_hi'],
                    $data['id_enseignant'],
                    $data['composant'],
                    $data['formation'],
                    $data['module'],
                    $data['nb_heures_cm'],
                    $data['nb_heures_td'],
                    $data['nb_heures_tp'],
                    $data['nb_heures_ei'],
                    $data['nb_heures_total']
                );

                $voeuxHI[] = $voeuHI;
            }

            return $voeuxHI;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function save(VoeuHorsIUT $voeuHI): void {
        try {
            if ($voeuHI->getIdVoeuHI()) {
                $stmt = $this->db->prepare("
                    UPDATE voeux_hors_iut
                    SET id_enseignant = :id_enseignant,
                        composant = :composant,
                        formation = :formation,
                        module = :module,
                        nb_heures_cm = :nb_heures_cm,
                        nb_heures_td = :nb_heures_td,
                        nb_heures_tp = :nb_heures_tp,
                        nb_heures_ei = :nb_heures_ei,
                        nb_heures_total = :nb_heures_total
                    WHERE id_voeu_hi = :id_voeu_hi
                ");

                $stmt->execute([
                    'id_voeu_hi' => $voeuHI->getIdVoeuHI(),
                    'id_enseignant' => $voeuHI->getIdEnseignant(),
                    'composant' => $voeuHI->getComposant(),
                    'formation' => $voeuHI->getFormation(),
                    'module' => $voeuHI->getModule(),
                    'nb_heures_cm' => $voeuHI->getNbHeuresCM(),
                    'nb_heures_td' => $voeuHI->getNbHeuresTD(),
                    'nb_heures_tp' => $voeuHI->getNbHeuresTP(),
                    'nb_heures_ei' => $voeuHI->getNbHeuresEI(),
                    'nb_heures_total' => $voeuHI->getNbHeuresTotal()
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO voeux_hors_iut (id_enseignant, composant, formation, module, nb_heures_cm, nb_heures_td, nb_heures_tp, nb_heures_ei, nb_heures_total)
                    VALUES (:id_enseignant, :composant, :formation, :module, :nb_heures_cm, :nb_heures_td, :nb_heures_tp, :nb_heures_ei, :nb_heures_total)
                ");

                $stmt->execute([
                    'id_enseignant' => $voeuHI->getIdEnseignant(),
                    'composant' => $voeuHI->getComposant(),
                    'formation' => $voeuHI->getFormation(),
                    'module' => $voeuHI->getModule(),
                    'nb_heures_cm' => $voeuHI->getNbHeuresCM(),
                    'nb_heures_td' => $voeuHI->getNbHeuresTD(),
                    'nb_heures_tp' => $voeuHI->getNbHeuresTP(),
                    'nb_heures_ei' => $voeuHI->getNbHeuresEI(),
                    'nb_heures_total' => $voeuHI->getNbHeuresTotal()
                ]);

                $voeuHI->setIdVoeuHI($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id): void {
        try {
            $stmt = $this->db->prepare("DELETE FROM voeux_hors_iut WHERE id_voeu_hi = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function findByEnseignant($idEnseignant): array {
        try {
            $stmt = $this->db->prepare("SELECT * FROM voeux_hors_iut WHERE id_enseignant = :id_enseignant");
            $stmt->execute(['id_enseignant' => $idEnseignant]);
            $voeuxHI = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $voeuHI = new VoeuHorsIUT(
                    $data['id_voeu_hi'],
                    $data['id_enseignant'],
                    $data['composant'],
                    $data['formation'],
                    $data['module'],
                    $data['nb_heures_cm'],
                    $data['nb_heures_td'],
                    $data['nb_heures_tp'],
                    $data['nb_heures_ei'],
                    $data['nb_heures_total']
                );

                $voeuxHI[] = $voeuHI;
            }

            return $voeuxHI;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function deleteByEnseignant($idEnseignant): void {
        try {
            $stmt = $this->db->prepare("DELETE FROM voeux_hors_iut WHERE id_enseignant = :idEnseignant");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }
}
