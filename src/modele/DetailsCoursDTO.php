<?php

use src\Db\connexionFactory;
require_once 'DetailsCours.php';

class DetailsCoursDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }

    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM details_cours WHERE id_ressource = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $detailsCours = new DetailsCours(
                    $data['id_ressource'],
                    $data['id_cours'],
                    $data['id_responsable_module'],
                    $data['type_salle'],
                    $data['equipements_specifiques'],
                    $data['ds'],
                    $data['statut'],
                    $data['commentaire'],
                    $data['systeme']
                );
                return $detailsCours;
            }
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findByCours($idCours) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM details_cours WHERE id_cours = :idCours");
            $stmt->execute(['idCours' => $idCours]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $detailsCours = new DetailsCours(
                    $data['id_ressource'],
                    $data['id_cours'],
                    $data['id_responsable_module'],
                    $data['type_salle'],
                    $data['equipements_specifiques'],
                    $data['ds'],
                    $data['statut'],
                    $data['commentaire'],
                    $data['systeme']
                );
                return $detailsCours;
            }
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function save(DetailsCours $detailsCours) {
        try {
            if ($detailsCours->getIdRessource()) {
                $stmt = $this->db->prepare("
                    UPDATE details_cours SET
                        id_cours = :idCours,
                        id_responsable_module = :idResponsableModule,
                        type_salle = :typeSalle,
                        equipements_specifiques = :equipementsSpecifiques,
                        ds = :ds,
                        statut = :statut,
                        commentaire = :commentaire,
                        systeme = :systeme
                    WHERE id_ressource = :id
                ");
                $stmt->execute([
                    'id' => $detailsCours->getIdRessource(),
                    'idCours' => $detailsCours->getIdCours(),
                    'idResponsableModule' => $detailsCours->getIdResponsableModule(),
                    'typeSalle' => $detailsCours->getTypeSalle(),
                    'equipementsSpecifiques' => $detailsCours->getEquipementsSpecifiques(),
                    'ds' => $detailsCours->getDs(),
                    'statut' => $detailsCours->getStatut(),
                    'commentaire' => $detailsCours->getCommentaire(),
                    'systeme' => $detailsCours->getSysteme()
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO details_cours 
                        (id_cours, id_responsable_module, type_salle, equipements_specifiques, ds, statut, commentaire, systeme)
                    VALUES 
                        (:idCours, :idResponsableModule, :typeSalle, :equipementsSpecifiques, :ds, :statut, :commentaire, :systeme)
                ");
                $stmt->execute([
                    'idCours' => $detailsCours->getIdCours(),
                    'idResponsableModule' => $detailsCours->getIdResponsableModule(),
                    'typeSalle' => $detailsCours->getTypeSalle(),
                    'equipementsSpecifiques' => $detailsCours->getEquipementsSpecifiques(),
                    'ds' => $detailsCours->getDs(),
                    'statut' => $detailsCours->getStatut(),
                    'commentaire' => $detailsCours->getCommentaire(),
                    'systeme' => $detailsCours->getSysteme()
                ]);

                $detailsCours->setIdRessource($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM details_cours WHERE id_ressource = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function findResponsableByCours($idCours) {
        try {
            $stmt = $this->db->prepare("
                SELECT id_responsable_module
                FROM details_cours
                WHERE id_cours = :idCours
                LIMIT 1
            ");
            $stmt->execute(['idCours' => $idCours]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($data) {
                return $data['id_responsable_module'];
            }
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }
}
?>
