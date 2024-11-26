<?php

class Affectation {
    private $idAffectation;
    private $idEnseignant;
    private $idCours;
    private $idGroupe;
    private $heuresAffectees;
    private $typeHeure;

    public function __construct(
        $idAffectation = null,
        $idEnseignant = null,
        $idCours = null,
        $idGroupe = null,
        $heuresAffectees = null,
        $typeHeure = null
    ) {
        $this->idAffectation = $idAffectation;
        $this->idEnseignant = $idEnseignant;
        $this->idCours = $idCours;
        $this->idGroupe = $idGroupe;
        $this->heuresAffectees = $heuresAffectees;
        $this->typeHeure = $typeHeure;
    }

    public function getIdAffectation() {
        return $this->idAffectation;
    }

    public function setIdAffectation($idAffectation) {
        $this->idAffectation = $idAffectation;
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

    public function getHeuresAffectees() {
        return $this->heuresAffectees;
    }

    public function setHeuresAffectees($heuresAffectees) {
        $this->heuresAffectees = $heuresAffectees;
    }

    public function getTypeHeure() {
        return $this->typeHeure;
    }

    public function setTypeHeure($typeHeure) {
        $this->typeHeure = $typeHeure;
    }

}

?>
