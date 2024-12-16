<?php

require_once '../src/Db/connexionFactory.php';
require_once 'Affectation.php';

class AffectationDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }


    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM affectations WHERE id_affectation = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $affectation = new Affectation(
                    $data['id_affectation'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['id_groupe'],
                    $data['heures_affectees'],
                    $data['type_heure']
                );

                return $affectation;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM affectations");
            $affectations = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $affectation = new Affectation(
                    $data['id_affectation'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['id_groupe'],
                    $data['heures_affectees'],
                    $data['type_heure']
                );

                $affectations[] = $affectation;
            }

            return $affectations;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function save(Affectation $affectation) {
        try {
            if ($affectation->getIdAffectation()) {
                $stmt = $this->db->prepare("
                    UPDATE affectations SET
                        id_enseignant = :idEnseignant,
                        id_cours = :idCours,
                        id_groupe = :idGroupe,
                        heures_affectees = :heuresAffectees,
                        type_heure = :typeHeure
                    WHERE id_affectation = :id
                ");
                $stmt->execute([
                    'id' => $affectation->getIdAffectation(),
                    'idEnseignant' => $affectation->getIdEnseignant(),
                    'idCours' => $affectation->getIdCours(),
                    'idGroupe' => $affectation->getIdGroupe(),
                    'heuresAffectees' => $affectation->getHeuresAffectees(),
                    'typeHeure' => $affectation->getTypeHeure(),
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO affectations (id_enseignant, id_cours, id_groupe, heures_affectees, type_heure)
                    VALUES (:idEnseignant, :idCours, :idGroupe, :heuresAffectees, :typeHeure)
                ");
                $stmt->execute([
                    'idEnseignant' => $affectation->getIdEnseignant(),
                    'idCours' => $affectation->getIdCours(),
                    'idGroupe' => $affectation->getIdGroupe(),
                    'heuresAffectees' => $affectation->getHeuresAffectees(),
                    'typeHeure' => $affectation->getTypeHeure(),
                ]);

                $affectation->setIdAffectation($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM affectations WHERE id_affectation = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function findByEnseignant($idEnseignant) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM affectations WHERE id_enseignant = :idEnseignant");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
            $affectations = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $affectation = new Affectation(
                    $data['id_affectation'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['id_groupe'],
                    $data['heures_affectees'],
                    $data['type_heure']
                );

                $affectations[] = $affectation;
            }

            return $affectations;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}

?>
