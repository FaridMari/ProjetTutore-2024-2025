<?php

use src\Db\connexionFactory;
require_once 'Enseignant.php';
require_once 'Utilisateur.php';
require_once 'UtilisateurDTO.php';

class EnseignantDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }

    public function findById($idEnseignant) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nom, u.email, u.mot_de_passe, u.role
                FROM enseignants e
                INNER JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
                WHERE e.id_enseignant = :idEnseignant
            ");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $enseignant = new Enseignant(
                    $data['id_enseignant'],
                    $data['heures_affectees'],
                    $data['statut'],
                    $data['total_hetd'],

                );

                return $enseignant;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function save(Enseignant $enseignant, Utilisateur $utilisateur) {
        try {
            $this->db->beginTransaction();
            $idEnseignant = $utilisateur->getIdUtilisateur();
            $enseignant->setIdEnseignant($idEnseignant);

            if ($this->existsInEnseignants($idEnseignant)) {
                $stmt = $this->db->prepare("
                    UPDATE enseignants SET
                        id_utilisateur = :idUtilisateur,
                        heures_affectees = :heuresAffectees,
                        statut = :statut,
                        total_hetd = :totalHetd
                    WHERE id_enseignant = :idEnseignant
                ");
                $stmt->execute([
                    'idEnseignant' => $idEnseignant,
                    'idUtilisateur' => $utilisateur->getIdUtilisateur(),
                    'heuresAffectees' => $enseignant->getHeuresAffectees(),
                    'statut' => $enseignant->getStatut(),
                    'totalHetd' => $enseignant->getTotalHetd()
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO enseignants (id_enseignant, id_utilisateur, heures_affectees, statut, total_hetd)
                    VALUES (:idEnseignant, :idUtilisateur, :heuresAffectees, :statut, :totalHetd)
                ");
                $stmt->execute([
                    'idEnseignant' => $idEnseignant,
                    'idUtilisateur' => $utilisateur->getIdUtilisateur(),
                    'heuresAffectees' => $enseignant->getHeuresAffectees(),
                    'statut' => $enseignant->getStatut(),
                    'totalHetd' => $enseignant->getTotalHetd()
                ]);
            }

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw $e;
        }
    }

    public function delete($idEnseignant) {
        try {
            $this->db->beginTransaction();

            $stmt = $this->db->prepare("DELETE FROM enseignants WHERE id_enseignant = :idEnseignant");
            $stmt->execute(['idEnseignant' => $idEnseignant]);

            $utilisateurDTO = new UtilisateurDTO();
            $utilisateurDTO->delete($idEnseignant);

            $this->db->commit();
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log($e->getMessage());
            throw $e;
        }
    }

    private function existsInEnseignants($idEnseignant) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM enseignants WHERE id_enseignant = :idEnseignant");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }

    public function findByUtilisateurId($idUtilisateur) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nom, u.email, u.mot_de_passe, u.role
                FROM enseignants e
                INNER JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
                WHERE e.id_utilisateur = :idUtilisateur
            ");
            $stmt->execute(['idUtilisateur' => $idUtilisateur]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
    
            if ($data) {
                $enseignant = new Enseignant(
                    $data['id_enseignant'],
                    $data['heures_affectees'],
                    $data['statut'],
                    $data['total_hetd']
                );
                return $enseignant;
            }
    
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nom, u.email, u.mot_de_passe, u.role
                FROM enseignants e
                INNER JOIN utilisateurs u ON e.id_utilisateur = u.id_utilisateur
            ");
            $stmt->execute();
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $enseignants = [];
            foreach ($results as $data) {
                $enseignant = new Enseignant(
                    $data['id_enseignant'],
                    $data['id_utilisateur'],
                    $data['heures_affectees'],
                    $data['statut'],
                    $data['total_hetd']
                );
                $enseignants[] = $enseignant;
            }

            return $enseignants;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    
}

?>
