<?php

require_once '../src/Db/connexionFactory.php';
require_once 'Groupe.php';

class GroupeDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }


    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM groupes WHERE id_groupe = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $groupe = new Groupe();
                $groupe->setIdGroupe($data['id_groupe']);
                $groupe->setNomGroupe($data['nom_groupe']);
                $groupe->setNiveau($data['niveau']);

                return $groupe;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM groupes");
            $groupes = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $groupe = new Groupe();
                $groupe->setIdGroupe($data['id_groupe']);
                $groupe->setNomGroupe($data['nom_groupe']);
                $groupe->setNiveau($data['niveau']);

                $groupes[] = $groupe;
            }

            return $groupes;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function save(Groupe $groupe) {
        try {
            if ($groupe->getIdGroupe()) {
                $stmt = $this->db->prepare("
                    UPDATE groupes SET
                        nom_groupe = :nomGroupe,
                        niveau = :niveau
                    WHERE id_groupe = :id
                ");
                $stmt->execute([
                    'id' => $groupe->getIdGroupe(),
                    'nomGroupe' => $groupe->getNomGroupe(),
                    'niveau' => $groupe->getNiveau(),
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO groupes (nom_groupe, niveau)
                    VALUES (:nomGroupe, :niveau)
                ");
                $stmt->execute([
                    'nomGroupe' => $groupe->getNomGroupe(),
                    'niveau' => $groupe->getNiveau(),
                ]);
                $groupe->setIdGroupe($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM groupes WHERE id_groupe = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

}

?>
