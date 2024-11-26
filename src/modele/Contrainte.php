<?php

class Contrainte {
    private $idContrainte;
    private $idEnseignant;
    private $jour;
    private $heureDebut;
    private $heureFin;

    public function __construct(
        $idContrainte = null,
        $idEnseignant = null,
        $jour = null,
        $heureDebut = null,
        $heureFin = null
    ) {
        $this->idContrainte = $idContrainte;
        $this->idEnseignant = $idEnseignant;
        $this->jour = $jour;
        $this->heureDebut = $heureDebut;
        $this->heureFin = $heureFin;
    }

    public function getIdContrainte() {
        return $this->idContrainte;
    }

    public function setIdContrainte($idContrainte) {
        $this->idContrainte = $idContrainte;
    }

    public function getIdEnseignant() {
        return $this->idEnseignant;
    }

    public function setIdEnseignant($idEnseignant) {
        $this->idEnseignant = $idEnseignant;
    }

    public function getJour() {
        return $this->jour;
    }

    public function setJour($jour) {
        $this->jour = $jour;
    }

    public function getHeureDebut() {
        return $this->heureDebut;
    }

    public function setHeureDebut($heureDebut) {
        $this->heureDebut = $heureDebut;
    }

    public function getHeureFin() {
        return $this->heureFin;
    }

    public function setHeureFin($heureFin) {
        $this->heureFin = $heureFin;
    }

}

?>
