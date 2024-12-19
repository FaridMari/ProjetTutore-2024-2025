<?php

class Voeu {
    private $idVoeu;
    private $idEnseignant;
    private $idCours;
    private $semestre;
    private $nbHeures;
    private $remarque;

    public function __construct(
        $idVoeu = null,
        $idEnseignant = null,
        $idCours = null,
        $remarque = null,
        $semestre = null,
        $nbHeures = null
    ) {
        $this->idVoeu = $idVoeu;
        $this->idEnseignant = $idEnseignant;
        $this->idCours = $idCours;
        $this->remarque = $remarque;
        $this->semestre = $semestre;
        $this->nbHeures = $nbHeures;
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

    public function getNbHeures() {
        return $this->nbHeures;
    }

    public function setNbHeures($nbHeures) {
        $this->nbHeures = $nbHeures;
    }
}

?>
