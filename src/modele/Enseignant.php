<?php

class Enseignant {
    private $idEnseignant;
    private $idUtilisateur;
    private $heuresAffectees;
    private $statut;
    private $totalHetd;

    public function __construct(
        $idEnseignant = null,
        $idUtilisateur = null,
        $heuresAffectees = 0,
        $statut = null,
        $totalHetd = 0
    ) {
        $this->idEnseignant = $idEnseignant;
        $this->idUtilisateur = $idUtilisateur;
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

    public function getIdUtilisateur() {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur($idUtilisateur) {
        $this->idUtilisateur = $idUtilisateur;
    }

}

?>
