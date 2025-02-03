<?php

class DetailsCours {
    private $idRessource;
    private $idCours;
    private $idResponsableModule;
    private $typeSalle;
    private $equipementsSpecifiques;
    private $details;

    public function __construct(
        $idRessource = null,
        $idCours = null,
        $idResponsableModule = null,
        $typeSalle = null,
        $equipementsSpecifiques = null,
        $details = null
    ) {
        $this->idRessource = $idRessource;
        $this->idCours = $idCours;
        $this->idResponsableModule = $idResponsableModule;
        $this->typeSalle = $typeSalle;
        $this->equipementsSpecifiques = $equipementsSpecifiques;
        $this->details = $details;
    }


    public function getIdRessource() {
        return $this->idRessource;
    }

    public function setIdRessource($idRessource) {
        $this->idRessource = $idRessource;
    }

    public function getIdCours() {
        return $this->idCours;
    }

    public function setIdCours($idCours) {
        $this->idCours = $idCours;
    }

    public function getIdResponsableModule() {
        return $this->idResponsableModule;
    }

    public function setIdResponsableModule($idResponsableModule) {
        $this->idResponsableModule = $idResponsableModule;
    }

    public function getTypeSalle() {
        return $this->typeSalle;
    }

    public function setTypeSalle($typeSalle) {
        $this->typeSalle = $typeSalle;
    }

    public function getEquipementsSpecifiques() {
        return $this->equipementsSpecifiques;
    }

    public function setEquipementsSpecifiques($equipementsSpecifiques) {
        $this->equipementsSpecifiques = $equipementsSpecifiques;
    }

    public function getDetails() {
        return $this->details;
    }

    public function setRepartitionHeures($details) {
        $this->details = $details;
    }
}

?>
