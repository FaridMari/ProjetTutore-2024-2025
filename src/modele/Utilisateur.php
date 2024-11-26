<?php

class Utilisateur {
    private $idUtilisateur;
    private $nom;
    private $email;
    private $motDePasse;
    private $role;

    public function __construct($idUtilisateur = null, $nom = null, $email = null, $motDePasse = null, $role = null) {
        $this->idUtilisateur = $idUtilisateur;
        $this->nom = $nom;
        $this->email = $email;
        $this->motDePasse = $motDePasse;
        $this->role = $role;
    }

  
    public function getIdUtilisateur() {
        return $this->idUtilisateur;
    }

    public function setIdUtilisateur($idUtilisateur) {
        $this->idUtilisateur = $idUtilisateur;
    }

    public function getNom() {
        return $this->nom;
    }

    public function setNom($nom) {
        $this->nom = $nom;
    }

    public function getEmail() {
        return $this->email;
    }

    public function setEmail($email) {
        $this->email = $email;
    }

    public function getMotDePasse() {
        return $this->motDePasse;
    }

    public function setMotDePasse($motDePasse) {
        $this->motDePasse = $motDePasse;
    }

    public function getRole() {
        return $this->role;
    }

    public function setRole($role) {
        $this->role = $role;
    }

    public function seConnecter() {
    }

    public function seDeconnecter() {
    }

}

?>
