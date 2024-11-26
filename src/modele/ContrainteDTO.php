<?php

require_once 'DatabaseConnection.php';
require_once 'Contrainte.php';

class ContrainteDTO {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM contraintes WHERE id_contrainte = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $contrainte = new Contrainte(
                    $data['id_contrainte'],
                    $data['id_enseignant'],
                    $data['jour'],
                    $data['heure_debut'],
                    $data['heure_fin']
                );

                return $contrainte;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM contraintes");
            $contraintes = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $contrainte = new Contrainte(
                    $data['id_contrainte'],
                    $data['id_enseignant'],
                    $data['jour'],
                    $data['heure_debut'],
                    $data['heure_fin']
                );

                $contraintes[] = $contrainte;
            }

            return $contraintes;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function save(Contrainte $contrainte) {
        try {
            if ($contrainte->getIdContrainte()) {
                $stmt = $this->db->prepare("
                    UPDATE contraintes SET
                        id_enseignant = :idEnseignant,
                        jour = :jour,
                        heure_debut = :heureDebut,
                        heure_fin = :heureFin
                    WHERE id_contrainte = :id
                ");
                $stmt->execute([
                    'id' => $contrainte->getIdContrainte(),
                    'idEnseignant' => $contrainte->getIdEnseignant(),
                    'jour' => $contrainte->getJour(),
                    'heureDebut' => $contrainte->getHeureDebut(),
                    'heureFin' => $contrainte->getHeureFin(),
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO contraintes (id_enseignant, jour, heure_debut, heure_fin)
                    VALUES (:idEnseignant, :jour, :heureDebut, :heureFin)
                ");
                $stmt->execute([
                    'idEnseignant' => $contrainte->getIdEnseignant(),
                    'jour' => $contrainte->getJour(),
                    'heureDebut' => $contrainte->getHeureDebut(),
                    'heureFin' => $contrainte->getHeureFin(),
                ]);

                $contrainte->setIdContrainte($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM contraintes WHERE id_contrainte = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }


    public function findByEnseignant($idEnseignant) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM contraintes WHERE id_enseignant = :idEnseignant");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
            $contraintes = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $contrainte = new Contrainte(
                    $data['id_contrainte'],
                    $data['id_enseignant'],
                    $data['jour'],
                    $data['heure_debut'],
                    $data['heure_fin']
                );

                $contraintes[] = $contrainte;
            }

            return $contraintes;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }
}

?>
