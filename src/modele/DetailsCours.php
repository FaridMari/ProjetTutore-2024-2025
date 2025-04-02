<?php

class DetailsCours {
    private $idRessource;
    private $idCours;
    private $idResponsableModule;
    private $typeSalle;
    private $equipementsSpecifiques;
    private $ds;
    private $statut;
    private $commentaire;
    private $systeme;

    public function __construct(
        $idRessource = null,
        $idCours = null,
        $idResponsableModule = null,
        $typeSalle = null,
        $equipementsSpecifiques = null,
        $ds = null,
        $statut = 'en attente',
        $commentaire = null,
        $systeme = null
    ) {
        $this->idRessource = $idRessource;
        $this->idCours = $idCours;
        $this->idResponsableModule = $idResponsableModule;
        $this->typeSalle = $typeSalle;
        $this->equipementsSpecifiques = $equipementsSpecifiques;
        $this->ds = $ds;
        $this->statut = $statut;
        $this->commentaire = $commentaire;
        $this->systeme = $systeme;
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

    public function getDs() {
        return $this->ds;
    }

    public function setDs($ds) {
        $this->ds = $ds;
    }

    public function getStatut() {
        return $this->statut;
    }

    public function setStatut($statut) {
        $this->statut = $statut;
    }

    public function getCommentaire() {
        return $this->commentaire;
    }

    public function setCommentaire($commentaire) {
        $this->commentaire = $commentaire;
    }

    public function getSysteme() {
        return $this->systeme;
    }

    public function setSysteme($systeme) {
        $this->systeme = $systeme;
    }
}
?>
