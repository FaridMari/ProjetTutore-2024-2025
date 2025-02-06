<?php

use src\Db\connexionFactory;
require_once 'Voeu.php';

class VoeuDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }

    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM voeux WHERE id_voeu = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $voeu = new Voeu(
                    $data['id_voeu'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['remarques'],
                    $data['semestre'],
                    $data['nb_CM'],
                    $data['nb_TD'],
                    $data['nb_TP'],
                    $data['nb_EI']
                );

                return $voeu;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM voeux");
            $voeux = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $voeu = new Voeu(
                    $data['id_voeu'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['remarques'],
                    $data['semestre'],
                    $data['nb_CM'],
                    $data['nb_TD'],
                    $data['nb_TP'],
                    $data['nb_EI']
                );

                $voeux[] = $voeu;
            }

            return $voeux;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function save(Voeu $voeu) {
        try {
            if ($voeu->getIdVoeu()) {
                $stmt = $this->db->prepare("
                    UPDATE voeux SET
                        id_enseignant = :idEnseignant,
                        id_cours = :idCours,
                        remarques = :remarque,
                        semestre = :semestre,
                        nb_CM = :nbCM,
                        nb_TD = :nbTD,
                        nb_TP = :nbTP,
                        nb_EI = :nbEI
                    WHERE id_voeu = :id
                ");
                $stmt->execute([
                    'id' => $voeu->getIdVoeu(),
                    'idEnseignant' => $voeu->getIdEnseignant(),
                    'idCours' => $voeu->getIdCours(),
                    'remarque' => $voeu->getRemarque(),
                    'semestre' => $voeu->getSemestre(),
                    'nbCM' => $voeu->getNbCM(),
                    'nbTD' => $voeu->getNbTD(),
                    'nbTP' => $voeu->getNbTP(),
                    'nbEI' => $voeu->getNbEI(),
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO voeux (id_enseignant, id_cours, remarques, semestre, nb_CM, nb_TD, nb_TP, nb_EI)
                    VALUES (:idEnseignant, :idCours, :remarque, :semestre, :nbCM, :nbTD, :nbTP, :nbEI)
                ");
                $stmt->execute([
                    'idEnseignant' => $voeu->getIdEnseignant(),
                    'idCours' => $voeu->getIdCours(),
                    'remarque' => $voeu->getRemarque(),
                    'semestre' => $voeu->getSemestre(),
                    'nbCM' => $voeu->getNbCM(),
                    'nbTD' => $voeu->getNbTD(),
                    'nbTP' => $voeu->getNbTP(),
                    'nbEI' => $voeu->getNbEI(),
                ]);

                $voeu->setIdVoeu($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM voeux WHERE id_voeu = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function findByEnseignant($idEnseignant) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM voeux WHERE id_enseignant = :idEnseignant");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
            $voeux = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $voeu = new Voeu(
                    $data['id_voeu'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['remarques'],
                    $data['semestre'],
                    $data['nb_CM'],
                    $data['nb_TD'],
                    $data['nb_TP'],
                    $data['nb_EI']
                );

                $voeux[] = $voeu;
            }

            return $voeux;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function deleteByEnseignant($idEnseignant) {
        try {
            $stmt = $this->db->prepare("DELETE FROM voeux WHERE id_enseignant = :idEnseignant");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function findByFormation($formationCode) {
        try {
            $formationFull = "BUT " . $formationCode;
            $stmt = $this->db->prepare("
                SELECT v.*
                FROM voeux v
                INNER JOIN cours c ON v.id_cours = c.id_cours
                WHERE c.formation = :formation
            ");
            $stmt->execute(['formation' => $formationFull]);
            $voeux = [];
    
            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $voeu = new Voeu(
                    $data['id_voeu'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['remarques'],
                    $data['semestre'],
                    $data['nb_CM'],
                    $data['nb_TD'],
                    $data['nb_TP'],
                    $data['nb_EI']
                );
    
                $voeux[] = $voeu;
            }
    
            return $voeux;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
    
}
?>
