<?php

require_once 'DatabaseConnection.php';
require_once 'Enseignant.php';
require_once 'Utilisateur.php';
require_once 'UtilisateurDTO.php';

class EnseignantDTO {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance()->getConnection();
    }

    public function findById($idEnseignant) {
        try {
            $stmt = $this->db->prepare("
                SELECT e.*, u.nom, u.email, u.mot_de_passe, u.role
                FROM enseignants e
                INNER JOIN utilisateurs u ON e.id_enseignant = u.id_utilisateur
                WHERE e.id_enseignant = :idEnseignant
            ");
            $stmt->execute(['idEnseignant' => $idEnseignant]);
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

    public function save(Enseignant $enseignant, Utilisateur $utilisateur) {
        try {
            $this->db->beginTransaction();


            $enseignant->setIdEnseignant($utilisateur->getIdUtilisateur());

            if ($this->existsInEnseignants($enseignant->getIdEnseignant())) {
                $stmt = $this->db->prepare("
                    UPDATE enseignants SET
                        heures_affectees = :heuresAffectees,
                        statut = :statut,
                        total_hetd = :totalHetd
                    WHERE id_enseignant = :idEnseignant
                ");
                $stmt->execute([
                    'idEnseignant' => $enseignant->getIdEnseignant(),
                    'heuresAffectees' => $enseignant->getHeuresAffectees(),
                    'statut' => $enseignant->getStatut(),
                    'totalHetd' => $enseignant->getTotalHetd()
                ]);
            } else {
                $stmt = $this->db->prepare("
                    INSERT INTO enseignants (id_enseignant, heures_affectees, statut, total_hetd)
                    VALUES (:idEnseignant, :heuresAffectees, :statut, :totalHetd)
                ");
                $stmt->execute([
                    'idEnseignant' => $enseignant->getIdEnseignant(),
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

}

?>
