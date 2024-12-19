<?php

class VoeuHorsIUT {
    private $idVoeuHI;
    private $idEnseignant;
    private $composant;
    private $formation;
    private $module;
    private $nbHeuresCM;
    private $nbHeuresTD;
    private $nbHeuresTP;
    private $nbHeuresEI;
    private $nbHeuresTotal;

    public function __construct(
        $idVoeuHI = null,
        $idEnseignant = null,
        $composant = null,
        $formation = null,
        $module = null,
        $nbHeuresCM = null,
        $nbHeuresTD = null,
        $nbHeuresTP = null,
        $nbHeuresEI = null,
        $nbHeuresTotal = null
    ) {
        $this->idVoeuHI = $idVoeuHI;
        $this->idEnseignant = $idEnseignant;
        $this->composant = $composant;
        $this->formation = $formation;
        $this->module = $module;
        $this->nbHeuresCM = $nbHeuresCM;
        $this->nbHeuresTD = $nbHeuresTD;
        $this->nbHeuresTP = $nbHeuresTP;
        $this->nbHeuresEI = $nbHeuresEI;
        $this->nbHeuresTotal = $nbHeuresTotal;
    }

    public function getIdVoeuHI() {
        return $this->idVoeuHI;
    }

    public function setIdVoeuHI($idVoeuHI) {
        $this->idVoeuHI = $idVoeuHI;
    }

    public function getIdEnseignant() {
        return $this->idEnseignant;
    }

    public function setIdEnseignant($idEnseignant) {
        $this->idEnseignant = $idEnseignant;
    }

    public function getComposant() {
        return $this->composant;
    }

    public function setComposant($composant) {
        $this->composant = $composant;
    }

    public function getFormation() {
        return $this->formation;
    }

    public function setFormation($formation) {
        $this->formation = $formation;
    }

    public function getModule() {
        return $this->module;
    }

    public function setModule($module) {
        $this->module = $module;
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

    public function getNbHeuresTotal() {
        return $this->nbHeuresTotal;
    }

    public function setNbHeuresTotal($nbHeuresTotal) {
        $this->nbHeuresTotal = $nbHeuresTotal;
    }
}