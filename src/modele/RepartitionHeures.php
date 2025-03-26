<?php

class RepartitionHeures {
    private $idRepartition;
    private $idCours;
    private $semaineDebut;
    private $semaineFin;
    private $nbHeuresSemaine;
    private $semestre;


    public function __construct($idRepartition = null, $idCours = null, $semaineDebut = null, $semaineFin = null, $nbHeuresSemaine = null, $semestre = null) {
        $this->idRepartition = $idRepartition;
        $this->idCours = $idCours;
        $this->semaineDebut = $semaineDebut;
        $this->semaineFin = $semaineFin;
        $this->nbHeuresSemaine = $nbHeuresSemaine;
        $this->semestre = $semestre;
    }

    public function getIdRepartition() {
        return $this->idRepartition;
    }

    public function setIdRepartition($idRepartition) {
        $this->idRepartition = $idRepartition;
    }

    public function getIdCours() {
        return $this->idCours;
    }

    public function setIdCours($idCours) {
        $this->idCours = $idCours;
    }

    public function getSemaineDebut() {
        return $this->semaineDebut;
    }

    public function setSemaineDebut($semaineDebut) {
        $this->semaineDebut = $semaineDebut;
    }   

    public function getSemaineFin() {
        return $this->semaineFin;
    }

    public function setSemaineFin($semaineFin) {
        $this->semaineFin = $semaineFin;
    }

    public function getNbHeuresSemaine() {
        return $this->nbHeuresSemaine;
    }

    public function setNbHeuresSemaine($nbHeuresSemaine) {
        $this->nbHeuresSemaine = $nbHeuresSemaine;
    }

    public function getSemestre() {
        return $this->semestre;
    }

    public function setSemestre($semestre) {
        $this->semestre = $semestre;
    }
    
}

?>