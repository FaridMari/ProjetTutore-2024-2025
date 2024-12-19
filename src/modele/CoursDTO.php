<?php

use src\Db\connexionFactory;
require_once 'Cours.php';

class CoursDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }


    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM cours WHERE id_cours = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $cours = new Cours();
                $cours->setIdCours($data['id_cours']);
                $cours->setFormation($data['formation']);
                $cours->setNomCours($data['nom_cours']);
                $cours->setSemestre($data['semestre']);
                $cours->setNbHeuresTotal($data['nb_heures_total']);
                $cours->setNbHeuresCM($data['nb_heures_cm']);
                $cours->setNbHeuresTD($data['nb_heures_td']);
                $cours->setNbHeuresTP($data['nb_heures_tp']);
                $cours->setNbHeuresEI($data['nb_heures_ei']);

                return $cours;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM cours");
            $coursList = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cours = new Cours();
                $cours->setIdCours($data['id_cours']);
                $cours->setFormation($data['formation']);
                $cours->setNomCours($data['nom_cours']);
                $cours->setSemestre($data['semestre']);
                $cours->setNbHeuresTotal($data['nb_heures_total']);
                $cours->setNbHeuresCM($data['nb_heures_cm']);
                $cours->setNbHeuresTD($data['nb_heures_td']);
                $cours->setNbHeuresTP($data['nb_heures_tp']);
                $cours->setNbHeuresEI($data['nb_heures_ei']);

                $coursList[] = $cours;
            }

            return $coursList;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function save(Cours $cours) {
        try {
            if ($cours->getIdCours()) {
                $stmt = $this->db->prepare("
                    UPDATE cours SET
                        nom_cours = :nomCours,
                        formation = :formation,
                        semestre = :semestre,
                        nb_heures_total = :nbHeuresTotal,
                        nb_heures_cm = :nbHeuresCM,
                        nb_heures_td = :nbHeuresTD,
                        nb_heures_tp = :nbHeuresTP,
                        nb_heures_ei = :nbHeuresEI
                    WHERE id_cours = :id
                ");
                $stmt->execute([
                    'id' => $cours->getIdCours(),
                    'formation' => $cours->getFormation(),
                    'nomCours' => $cours->getNomCours(),
                    'semestre' => $cours->getSemestre(),
                    'nbHeuresTotal' => $cours->getNbHeuresTotal(),
                    'nbHeuresCM' => $cours->getNbHeuresCM(),
                    'nbHeuresTD' => $cours->getNbHeuresTD(),
                    'nbHeuresTP' => $cours->getNbHeuresTP(),
                    'nbHeuresEI' => $cours->getNbHeuresEI(),
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO cours (formation, semestre, nom_cours, nb_heures_total, nb_heures_cm, nb_heures_td, nb_heures_tp, nb_heures_ei)
                    VALUES (:formation, :semestre, :nomCours, :nbHeuresTotal, :nbHeuresCM, :nbHeuresTD, :nbHeuresTP, :nbHeuresEI)
                ");
                $stmt->execute([
                    'nomCours' => $cours->getNomCours(),
                    'formation' => $cours->getFormation(),
                    'semestre' => $cours->getSemestre(),
                    'nbHeuresTotal' => $cours->getNbHeuresTotal(),
                    'nbHeuresCM' => $cours->getNbHeuresCM(),
                    'nbHeuresTD' => $cours->getNbHeuresTD(),
                    'nbHeuresTP' => $cours->getNbHeuresTP(),
                    'nbHeuresEI' => $cours->getNbHeuresEI(),
                ]);

                $cours->setIdCours($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM cours WHERE id_cours = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }


    public function findByName($nomCours) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM cours WHERE nom_cours LIKE :nomCours");
            $stmt->execute(['nomCours' => '%' . $nomCours . '%']);
            $coursList = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $cours = new Cours();
                $cours->setIdCours($data['id_cours']);
                $cours->setFormation($data['formation']);
                $cours->setSemestre($data['semestre']);
                $cours->setNomCours($data['nom_cours']);
                $cours->setNbHeuresTotal($data['nb_heures_total']);
                $cours->setNbHeuresCM($data['nb_heures_cm']);
                $cours->setNbHeuresTD($data['nb_heures_td']);
                $cours->setNbHeuresTP($data['nb_heures_tp']);
                $cours->setNbHeuresEI($data['nb_heures_ei']);

                $coursList[] = $cours;
            }

            return $coursList;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}

?>
