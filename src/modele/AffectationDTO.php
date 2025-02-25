<?php

use src\Db\connexionFactory;
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
            // On récupère la valeur saisie dans le champ "idEnseignant" (qui contient en réalité un nom ou plusieurs noms)
            $enseignantField = trim($affectation->getIdEnseignant());
    
            // Si la chaîne contient une virgule, cela signifie que plusieurs enseignants ont été saisis
            if (strpos($enseignantField, ',') !== false) {
                // Création d'une map (nom prenom => id) à partir des utilisateurs
                require_once 'UtilisateurDTO.php';
                $utilisateurDTO = new UtilisateurDTO();
                $utilisateurs = $utilisateurDTO->findAll();
                $teacherMap = [];
                foreach ($utilisateurs as $utilisateur) {
                    // On suppose ici que le format est "Nom Prenom"
                    $key = trim($utilisateur->getNom() . ' ' . $utilisateur->getPrenom());
                    $teacherMap[$key] = $utilisateur->getIdUtilisateur();
                }
    
                // Décomposer la chaîne en plusieurs noms
                $teacherNames = array_map('trim', explode(',', $enseignantField));
    
                // Suppression des affectations existantes pour ce couple (cours, groupe, type d'heure)
                $stmt = $this->db->prepare("
                    DELETE FROM affectations 
                    WHERE id_cours = :idCours 
                      AND id_groupe = :idGroupe 
                      AND type_heure = :typeHeure
                ");
                $stmt->execute([
                    'idCours'   => $affectation->getIdCours(),
                    'idGroupe'  => $affectation->getIdGroupe(),
                    'typeHeure' => $affectation->getTypeHeure()
                ]);
    
                // Insertion d'une affectation pour chaque enseignant saisi
                foreach ($teacherNames as $teacherName) {
                    if (isset($teacherMap[$teacherName])) {
                        $stmtInsert = $this->db->prepare("
                            INSERT INTO affectations (id_enseignant, id_cours, id_groupe, heures_affectees, type_heure)
                            VALUES (:idEnseignant, :idCours, :idGroupe, :heuresAff, :typeHeure)
                        ");
                        $stmtInsert->execute([
                            'idEnseignant' => $teacherMap[$teacherName],
                            'idCours'      => $affectation->getIdCours(),
                            'idGroupe'     => $affectation->getIdGroupe(),
                            'heuresAff'    => $affectation->getHeuresAffectees(),
                            'typeHeure'    => $affectation->getTypeHeure()
                        ]);
                    } else {
                        error_log("Enseignant '$teacherName' non trouvé dans la table utilisateurs.");
                    }
                }
            } else {
                // Cas classique : une seule valeur saisie
                $existing = $this->findByCourseAndGroupAndType(
                    $affectation->getIdCours(),
                    $affectation->getIdGroupe(),
                    $affectation->getTypeHeure()
                );
                if ($existing) {
                    if ($existing->getIdEnseignant() != $affectation->getIdEnseignant()) {
                        $existing->setIdEnseignant($affectation->getIdEnseignant());
                        $stmt = $this->db->prepare("
                            UPDATE affectations SET
                                id_enseignant = :idEnseignant,
                                heures_affectees = :heuresAff
                            WHERE id_affectation = :id
                        ");
                        $stmt->execute([
                            'id'           => $existing->getIdAffectation(),
                            'idEnseignant' => $affectation->getIdEnseignant(),
                            'heuresAff'    => $affectation->getHeuresAffectees()
                        ]);
                    }
                    $affectation->setIdAffectation($existing->getIdAffectation());
                } else {
                    $stmt = $this->db->prepare("
                        INSERT INTO affectations (id_enseignant, id_cours, id_groupe, heures_affectees, type_heure)
                        VALUES (:idEnseignant, :idCours, :idGroupe, :heuresAff, :typeHeure)
                    ");
                    $stmt->execute([
                        'idEnseignant' => $affectation->getIdEnseignant(),
                        'idCours'      => $affectation->getIdCours(),
                        'idGroupe'     => $affectation->getIdGroupe(),
                        'heuresAff'    => $affectation->getHeuresAffectees(),
                        'typeHeure'    => $affectation->getTypeHeure()
                    ]);
                    $affectation->setIdAffectation($this->db->lastInsertId());
                }
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
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

    public function findByUniqueKey($idEnseignant, $idCours, $idGroupe, $typeHeure) {
        try {
            $sql = "SELECT * FROM affectations
                    WHERE id_enseignant = :idEnseignant
                    AND id_cours = :idCours
                    AND id_groupe = :idGroupe
                    AND type_heure = :typeHeure";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                'idEnseignant' => $idEnseignant,
                'idCours'      => $idCours,
                'idGroupe'     => $idGroupe,
                'typeHeure'    => $typeHeure
            ]);

            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                return new Affectation(
                    $row['id_affectation'],
                    $row['id_enseignant'],
                    $row['id_cours'],
                    $row['id_groupe'],
                    $row['heures_affectees'],
                    $row['type_heure']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findByCourseAndGroupAndType($idCours, $idGroupe, $typeHeure) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM affectations WHERE id_cours = :idCours AND id_groupe = :idGroupe AND type_heure = :typeHeure");
            $stmt->execute([
                'idCours' => $idCours,
                'idGroupe' => $idGroupe,
                'typeHeure' => $typeHeure
            ]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($data) {
                return new Affectation(
                    $data['id_affectation'],
                    $data['id_enseignant'],
                    $data['id_cours'],
                    $data['id_groupe'],
                    $data['heures_affectees'],
                    $data['type_heure']
                );
            }
            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function deleteByCourseAndGroupAndType($idCours, $idGroupe, $typeHeure) {
        try {
            $stmt = $this->db->prepare("
                DELETE FROM affectations 
                WHERE id_cours = :idCours 
                  AND id_groupe = :idGroupe 
                  AND type_heure = :typeHeure
            ");
            $stmt->execute([
                'idCours'   => $idCours,
                'idGroupe'  => $idGroupe,
                'typeHeure' => $typeHeure
            ]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function insert(Affectation $affectation) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO affectations (id_enseignant, id_cours, id_groupe, heures_affectees, type_heure)
                VALUES (:idEnseignant, :idCours, :idGroupe, :heuresAff, :typeHeure)
            ");
            $stmt->execute([
                'idEnseignant' => $affectation->getIdEnseignant(),
                'idCours'      => $affectation->getIdCours(),
                'idGroupe'     => $affectation->getIdGroupe(),
                'heuresAff'    => $affectation->getHeuresAffectees(),
                'typeHeure'    => $affectation->getTypeHeure()
            ]);
            $affectation->setIdAffectation($this->db->lastInsertId());
        } catch (PDOException $e) {
            error_log($e->getMessage());
            throw $e;
        }
    }
    
    
}

?>
