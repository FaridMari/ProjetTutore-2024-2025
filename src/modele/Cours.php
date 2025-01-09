<?php

class Cours {
    private $idCours;
    private $formation;
    private $semestre;
    private $nomCours;
    private $codeCours;
    private $nbHeuresTotal;
    private $nbHeuresCM;
    private $nbHeuresTD;
    private $nbHeuresTP;
    private $nbHeuresEI;

    public function __construct(
        $idCours = null,
        $formation = null,
        $semestre = null,
        $nomCours = null,
        $codeCours = null,
        $nbHeuresTotal = null,
        $nbHeuresCM = null,
        $nbHeuresTD = null,
        $nbHeuresTP = null,
        $nbHeuresEI = null
    ) {
        $this->idCours = $idCours;
        $this->formation = $formation;
        $this->semestre = $semestre;
        $this->nomCours = $nomCours;
        $this->codeCours = $codeCours;
        $this->nbHeuresTotal = $nbHeuresTotal;
        $this->nbHeuresCM = $nbHeuresCM;
        $this->nbHeuresTD = $nbHeuresTD;
        $this->nbHeuresTP = $nbHeuresTP;
        $this->nbHeuresEI = $nbHeuresEI;
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

    public function getNbHeuresEI() {
        return $this->nbHeuresEI;
    }

    public function setNbHeuresEI($nbHeuresEI) {
        $this->nbHeuresEI = $nbHeuresEI;
    }

    public function getSemestre() {
        return $this->semestre;
    }

    public function setSemestre($semestre) {
        $this->semestre = $semestre;
    }

    public function getFormation() {
        return $this->formation;
    }

    public function setFormation($formation) {
        $this->formation = $formation;
    }
    
    public function getCodeCours() {
        return $this->codeCours;
    }

    public function setCodeCours($codeCours) {
        $this->codeCours = $codeCours;
    }

}

?>
