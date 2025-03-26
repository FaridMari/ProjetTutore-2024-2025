<?php

use src\Db\connexionFactory;
require_once 'RepartitionHeures.php';

class RepartitionHeuresDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }

    // Retourne tous les enregistrements sous forme d'objets RepartitionHeures
    public function findAll() {
        $stmt = $this->db->query("SELECT * FROM repartition_heures");
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new RepartitionHeures(
                $row['idRepartition'],
                $row['idCours'],
                $row['semaineDebut'],
                $row['semaineFin'],
                $row['nbHeuresSemaine'],
                $row['semestre']
            );
        }
        return $result;
    }

    // Retourne un enregistrement par son idRepartition
    public function findByID($id) {
        $stmt = $this->db->prepare("SELECT * FROM repartition_heures WHERE idRepartition = ?");
        $stmt->execute([$id]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            return new RepartitionHeures(
                $row['idRepartition'],
                $row['idCours'],
                $row['semaineDebut'],
                $row['semaineFin'],
                $row['nbHeuresSemaine'],
                $row['semestre']
            );
        }
        return null;
    }

    // Crée un nouvel enregistrement et met à jour l'objet avec l'id généré
    public function create(RepartitionHeures $repartition) {
        $stmt = $this->db->prepare("INSERT INTO repartition_heures (idCours, semaineDebut, semaineFin, nbHeuresSemaine, semestre) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([
            $repartition->getIdCours(),
            $repartition->getSemaineDebut(),
            $repartition->getSemaineFin(),
            $repartition->getNbHeuresSemaine(),
            $repartition->getSemestre()
        ]);
        // Mise à jour de l'objet avec l'id généré par la base de données
        $repartition->setIdRepartition($this->db->lastInsertId());
        return $repartition;
    }

    // Met à jour un enregistrement existant
    public function save(RepartitionHeures $repartition) {
        $stmt = $this->db->prepare("UPDATE repartition_heures SET idCours = ?, semaineDebut = ?, semaineFin = ?, nbHeuresSemaine = ?, semestre = ? WHERE idRepartition = ?");
        return $stmt->execute([
            $repartition->getIdCours(),
            $repartition->getSemaineDebut(),
            $repartition->getSemaineFin(),
            $repartition->getNbHeuresSemaine(),
            $repartition->getSemestre(),
            $repartition->getIdRepartition()
        ]);
    }

    // Supprime un enregistrement par son idRepartition
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM repartition_heures WHERE idRepartition = ?");
        return $stmt->execute([$id]);
    }

    // Retourne tous les enregistrements correspondant à un idCours donné
    public function findByIdCours($idCours) {
        $stmt = $this->db->prepare("SELECT * FROM repartition_heures WHERE idCours = ?");
        $stmt->execute([$idCours]);
        $result = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $result[] = new RepartitionHeures(
                $row['idRepartition'],
                $row['idCours'],
                $row['semaineDebut'],
                $row['semaineFin'],
                $row['nbHeuresSemaine'],
                $row['semestre']
            );
        }
        return $result;
    }
}

?>
