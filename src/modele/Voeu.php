<?php

class Voeu {
    private $idVoeu;
    private $idEnseignant;
    private $idCours;
    private $idGroupe;
    private $semestre;
    private $nbHeures;

    public function __construct(
        $idVoeu = null,
        $idEnseignant = null,
        $idCours = null,
        $idGroupe = null,
        $semestre = null,
        $nbHeures = null
    ) {
        $this->idVoeu = $idVoeu;
        $this->idEnseignant = $idEnseignant;
        $this->idCours = $idCours;
        $this->idGroupe = $idGroupe;
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

    public function getIdGroupe() {
        return $this->idGroupe;
    }

    public function setIdGroupe($idGroupe) {
        $this->idGroupe = $idGroupe;
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
