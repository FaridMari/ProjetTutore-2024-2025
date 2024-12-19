<?php

use src\Db\connexionFactory;
require_once 'Utilisateur.php';

class UtilisateurDTO {
    private $db;

    public function __construct() {
        $this->db = connexionFactory::makeConnection();
    }


    public function findById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE id_utilisateur = :id");
            $stmt->execute(['id' => $id]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                $utilisateur = new Utilisateur();
                $utilisateur->setIdUtilisateur($data['id_utilisateur']);
                $utilisateur->setNom($data['nom']);
                $utilisateur->setEmail($data['email']);
                $utilisateur->setMotDePasse($data['mot_de_passe']);
                $utilisateur->setRole($data['role']);

                return $utilisateur;
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    public function findAll() {
        try {
            $stmt = $this->db->query("SELECT * FROM utilisateurs");
            $utilisateurs = [];

            while ($data = $stmt->fetch(PDO::FETCH_ASSOC)) {
                $utilisateur = new Utilisateur();
                $utilisateur->setIdUtilisateur($data['id_utilisateur']);
                $utilisateur->setNom($data['nom']);
                $utilisateur->setEmail($data['email']);
                $utilisateur->setMotDePasse($data['mot_de_passe']);
                $utilisateur->setRole($data['role']);

                $utilisateurs[] = $utilisateur;
            }

            return $utilisateurs;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return [];
        }
    }

    public function save(Utilisateur $utilisateur) {
        try {
            if ($utilisateur->getIdUtilisateur()) {
                $stmt = $this->db->prepare("UPDATE utilisateurs SET nom = :nom, email = :email, mot_de_passe = :motDePasse, role = :role WHERE id_utilisateur = :id");
                $stmt->execute([
                    'id' => $utilisateur->getIdUtilisateur(),
                    'nom' => $utilisateur->getNom(),
                    'email' => $utilisateur->getEmail(),
                    'motDePasse' => $utilisateur->getMotDePasse(),
                    'role' => $utilisateur->getRole(),
                ]);
            } else {
                $stmt = $this->db->prepare("INSERT INTO utilisateurs (nom, email, mot_de_passe, role) VALUES (:nom, :email, :motDePasse, :role)");
                $stmt->execute([
                    'nom' => $utilisateur->getNom(),
                    'email' => $utilisateur->getEmail(),
                    'motDePasse' => $utilisateur->getMotDePasse(),
                    'role' => $utilisateur->getRole(),
                ]);

                $utilisateur->setIdUtilisateur($this->db->lastInsertId());
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->db->prepare("DELETE FROM utilisateurs WHERE id_utilisateur = :id");
            $stmt->execute(['id' => $id]);
        } catch (PDOException $e) {
            error_log($e->getMessage());
        }
    }


    public function inscrireUtilisateur(Utilisateur $utilisateur) {
        if ($this->emailExiste($utilisateur->getEmail())) {
            throw new Exception("Un utilisateur avec cet email existe déjà.");
        }

        $motDePasseHashe = password_hash($utilisateur->getMotDePasse(), PASSWORD_DEFAULT);
        $utilisateur->setMotDePasse($motDePasseHashe);

        if (!$utilisateur->getRole()) {
            $utilisateur->setRole('utilisateur');
        }

        $this->save($utilisateur);
    }

    public function authentifier($email, $motDePasse) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM utilisateurs WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $data = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($data) {
                if (password_verify($motDePasse, $data['mot_de_passe'])) {
                    $utilisateur = new Utilisateur();
                    $utilisateur->setIdUtilisateur($data['id_utilisateur']);
                    $utilisateur->setNom($data['nom']);
                    $utilisateur->setEmail($data['email']);
                    $utilisateur->setRole($data['role']);

                    return $utilisateur;
                } else {
                    return null;
                }
            }

            return null;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return null;
        }
    }

    private function emailExiste($email) {
        try {
            $stmt = $this->db->prepare("SELECT COUNT(*) FROM utilisateurs WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $count = $stmt->fetchColumn();

            return $count > 0;
        } catch (PDOException $e) {
            error_log($e->getMessage());
            return false;
        }
    }
}

?>
