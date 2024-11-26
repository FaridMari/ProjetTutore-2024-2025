<?php

class Cours {
    private $idCours;
    private $nomCours;
    private $nbHeuresTotal;
    private $nbHeuresCM;
    private $nbHeuresTD;
    private $nbHeuresTP;

    public function __construct(
        $idCours = null,
        $nomCours = null,
        $nbHeuresTotal = null,
        $nbHeuresCM = null,
        $nbHeuresTD = null,
        $nbHeuresTP = null
    ) {
        $this->idCours = $idCours;
        $this->nomCours = $nomCours;
        $this->nbHeuresTotal = $nbHeuresTotal;
        $this->nbHeuresCM = $nbHeuresCM;
        $this->nbHeuresTD = $nbHeuresTD;
        $this->nbHeuresTP = $nbHeuresTP;
    }


    public function getIdCours() {
        return $this->idCours;
    }

    public function setIdCours($idCours) {
        $this->idCours = $idCours;
    }

    public function getNomCours() {
        return $this->nomCours;
    }

    public function setNomCours($nomCours) {
        $this->nomCours = $nomCours;
    }

    public function getNbHeuresTotal() {
        return $this->nbHeuresTotal;
    }

    public function setNbHeuresTotal($nbHeuresTotal) {
        $this->nbHeuresTotal = $nbHeuresTotal;
    }

    public function getNbHeuresCM() {
        return $this->nbHeuresCM;
    }

    public function setNbHeuresCM($nbHeuresCM) {
        $this->nbHeuresCM = $nbHeuresCM;
    }

    public function getNbHeuresTD() {
        return $this->nbHeuresTD;
    }

    public function setNbHeuresTD($nbHeuresTD) {
        $this->nbHeuresTD = $nbHeuresTD;
    }

    public function getNbHeuresTP() {
        return $this->nbHeuresTP;
    }

    public function setNbHeuresTP($nbHeuresTP) {
        $this->nbHeuresTP = $nbHeuresTP;
    }

}

?>
