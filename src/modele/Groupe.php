<?php

class Groupe {
    private $idGroupe;
    private $nomGroupe;
    private $niveau;

    public function __construct($idGroupe = null, $nomGroupe = null, $niveau = null) {
        $this->idGroupe = $idGroupe;
        $this->nomGroupe = $nomGroupe;
        $this->niveau = $niveau;
    }


    public function getIdGroupe() {
        return $this->idGroupe;
    }

    public function setIdGroupe($idGroupe) {
        $this->idGroupe = $idGroupe;
    }

    public function getNomGroupe() {
        return $this->nomGroupe;
    }

    public function setNomGroupe($nomGroupe) {
        $this->nomGroupe = $nomGroupe;
    }

    public function getNiveau() {
        return $this->niveau;
    }

    public function setNiveau($niveau) {
        $this->niveau = $niveau;
    }

}

?>
