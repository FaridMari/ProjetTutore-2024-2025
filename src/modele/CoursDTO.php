<?php

require_once '../src/Db/connexionFactory.php';
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
                $cours->setNomCours($data['nom_cours']);
                $cours->setNbHeuresTotal($data['nb_heures_total']);
                $cours->setNbHeuresCM($data['nb_heures_cm']);
                $cours->setNbHeuresTD($data['nb_heures_td']);
                $cours->setNbHeuresTP($data['nb_heures_tp']);

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
                $cours->setNomCours($data['nom_cours']);
                $cours->setNbHeuresTotal($data['nb_heures_total']);
                $cours->setNbHeuresCM($data['nb_heures_cm']);
                $cours->setNbHeuresTD($data['nb_heures_td']);
                $cours->setNbHeuresTP($data['nb_heures_tp']);

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
                        nb_heures_total = :nbHeuresTotal,
                        nb_heures_cm = :nbHeuresCM,
                        nb_heures_td = :nbHeuresTD,
                        nb_heures_tp = :nbHeuresTP
                    WHERE id_cours = :id
                ");
                $stmt->execute([
                    'id' => $cours->getIdCours(),
                    'nomCours' => $cours->getNomCours(),
                    'nbHeuresTotal' => $cours->getNbHeuresTotal(),
                    'nbHeuresCM' => $cours->getNbHeuresCM(),
                    'nbHeuresTD' => $cours->getNbHeuresTD(),
                    'nbHeuresTP' => $cours->getNbHeuresTP(),
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO cours (nom_cours, nb_heures_total, nb_heures_cm, nb_heures_td, nb_heures_tp)
                    VALUES (:nomCours, :nbHeuresTotal, :nbHeuresCM, :nbHeuresTD, :nbHeuresTP)
                ");
                $stmt->execute([
                    'nomCours' => $cours->getNomCours(),
                    'nbHeuresTotal' => $cours->getNbHeuresTotal(),
                    'nbHeuresCM' => $cours->getNbHeuresCM(),
                    'nbHeuresTD' => $cours->getNbHeuresTD(),
                    'nbHeuresTP' => $cours->getNbHeuresTP(),
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
                $cours->setNomCours($data['nom_cours']);
                $cours->setNbHeuresTotal($data['nb_heures_total']);
                $cours->setNbHeuresCM($data['nb_heures_cm']);
                $cours->setNbHeuresTD($data['nb_heures_td']);
                $cours->setNbHeuresTP($data['nb_heures_tp']);

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
