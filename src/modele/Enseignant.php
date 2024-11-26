<?php

class Enseignant {
    private $idEnseignant;
    private $heuresAffectees;
    private $statut;
    private $totalHetd;

    public function __construct(
        $idEnseignant = null,
        $heuresAffectees = 0,
        $statut = null,
        $totalHetd = 0
    ) {
        $this->idEnseignant = $idEnseignant;
        $this->heuresAffectees = $heuresAffectees;
        $this->statut = $statut;
        $this->totalHetd = $totalHetd;
    }


    public function getIdEnseignant() {
        return $this->idEnseignant;
    }

    public function setIdEnseignant($idEnseignant) {
        $this->idEnseignant = $idEnseignant;
    }

    public function getHeuresAffectees() {
        return $this->heuresAffectees;
    }

    public function setHeuresAffectees($heuresAffectees) {
        $this->heuresAffectees = $heuresAffectees;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function getTotalHetd() {
        return $this->totalHetd;
    }

    public function setTotalHetd($totalHetd) {
        $this->totalHetd = $totalHetd;
    }

}

?>
