<?php

class Voeu {
    private $idVoeu;
    private $idEnseignant;
    private $idCours;
    private $remarque;
    private $semestre;
    private $nbCM;
    private $nbTD;
    private $nbTP;
    private $nbEI;

    public function __construct(
        $idVoeu = null,
        $idEnseignant = null,
        $idCours = null,
        $remarque = null,
        $semestre = null,
        $nbCM = null,
        $nbTD = null,
        $nbTP = null,
        $nbEI = null
    ) {
        $this->idVoeu = $idVoeu;
        $this->idEnseignant = $idEnseignant;
        $this->idCours = $idCours;
        $this->remarque = $remarque;
        $this->semestre = $semestre;
        $this->nbCM = $nbCM;
        $this->nbTD = $nbTD;
        $this->nbTP = $nbTP;
        $this->nbEI = $nbEI;
    }

    public function getIdVoeu() {
        return $this->idVoeu;
    }

    public function setIdVoeu($idVoeu) {
        $this->idVoeu = $idVoeu;
    }

    public function getIdEnseignant() {
        return $this->idEnseignant;
    }

    public function setIdEnseignant($idEnseignant) {
        $this->idEnseignant = $idEnseignant;
    }

    public function getIdCours() {
        return $this->idCours;
    }

    public function setIdCours($idCours) {
        $this->idCours = $idCours;
    }

    public function getRemarque() {
        return $this->remarque;
    }
    
    public function setRemarque($remarque) {
        $this->remarque = $remarque;
    }

    public function getSemestre() {
        return $this->semestre;
    }

    public function setSemestre($semestre) {
        $this->semestre = $semestre;
    }

    public function getNbCM() {
        return $this->nbCM;
    }

    public function setNbCM($nbCM) {
        $this->nbCM = $nbCM;
    }

    public function getNbTD() {
        return $this->nbTD;
    }

    public function setNbTD($nbTD) {
        $this->nbTD = $nbTD;
    }

    public function getNbTP() {
        return $this->nbTP;
    }

    public function setNbTP($nbTP) {
        $this->nbTP = $nbTP;
    }

    public function getNbEI() {
        return $this->nbEI;
    }

    public function setNbEI($nbEI) {
        $this->nbEI = $nbEI;
    }
}
?>
