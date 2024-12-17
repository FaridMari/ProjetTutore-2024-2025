<?php

require_once '../src/Db/connexionFactory.php';
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
                    $data['id_groupe'],
                    $data['semestre'],
                    $data['nb_heures']
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
                    $data['id_groupe'],
                    $data['semestre'],
                    $data['nb_heures']
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
                        id_groupe = :idGroupe,
                        semestre = :semestre,
                        nb_heures = :nbHeures
                    WHERE id_voeu = :id
                ");
                $stmt->execute([
                    'id' => $voeu->getIdVoeu(),
                    'idEnseignant' => $voeu->getIdEnseignant(),
                    'idCours' => $voeu->getIdCours(),
                    'idGroupe' => $voeu->getIdGroupe(),
                    'semestre' => $voeu->getSemestre(),
                    'nbHeures' => $voeu->getNbHeures(),
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO voeux (id_enseignant, id_cours, id_groupe, semestre, nb_heures)
                    VALUES (:idEnseignant, :idCours, :idGroupe, :semestre, :nbHeures)
                ");
                $stmt->execute([
                    'idEnseignant' => $voeu->getIdEnseignant(),
                    'idCours' => $voeu->getIdCours(),
                    'idGroupe' => $voeu->getIdGroupe(),
                    'semestre' => $voeu->getSemestre(),
                    'nbHeures' => $voeu->getNbHeures(),
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
                    $data['id_groupe'],
                    $data['semestre'],
                    $data['nb_heures']
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
