<?php

namespace src\Action;

use src\Db\connexionFactory;
use PDOException;

class EnseignantChangerPassAction extends Action
{
    public function execute(): string
    {
        if (!isset($_SESSION['user_id'])) {
            header("Location: login.php");
            exit();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $conn = connexionFactory::makeConnection();
                $stmt = $conn->prepare("SELECT email FROM utilisateurs WHERE id_utilisateur = :id_utilisateur");
                $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], \PDO::PARAM_INT);
                $stmt->execute();
                $user = $stmt->fetch(\PDO::FETCH_ASSOC);

                if ($user) {
                    $email = $user['email'];
                    $token = bin2hex(random_bytes(16));
                    $expiry = date('Y-m-d H:i:s', time() + (24 * 60 * 60));

                    $stmt = $conn->prepare("UPDATE utilisateurs SET reset_token = :token, reset_token_expiration = :expiry WHERE id_utilisateur = :id_utilisateur");
                    $stmt->bindParam(':token', $token, \PDO::PARAM_STR);
                    $stmt->bindParam(':expiry', $expiry, \PDO::PARAM_STR);
                    $stmt->bindParam(':id_utilisateur', $_SESSION['user_id'], \PDO::PARAM_INT);
                    $stmt->execute();

                    $host = $_SERVER['HTTP_HOST'];
                    $path = dirname($_SERVER['PHP_SELF']);
                    $passwordLink = "http://$host$path/src/Enseignant/Page_Password.php?token=" . urlencode($token) . "&email=" . urlencode($email);

                    $message = "Bonjour,\n\nCliquez sur le lien suivant pour changer votre mot de passe. Ce lien est valable 24 heures :\n$passwordLink";
                    $subject = "Changer votre mot de passe";
                    $headers = "From: no-reply@$host";
                    //echo script console.log($message);
                    echo "<script>console.log($message);</script>";
                    if (mail($email, $subject, $message, $headers)) {
                        //Affucher toutes les données du mail
                        echo "<script>console.log('Host : $host');</script>";
                        echo "<script>console.log('Path : $path');</script>";
                        echo "<script>console.log('Lien de réinitialisation : $passwordLink');</script>";
                        echo "<script>console.log('Email envoyé à : $email');</script>";
                        echo "<script>console.log('Sujet : $subject');</script>";
                        echo "<script>console.log('Message : $message');</script>";
                        echo "<script>console.log('En-têtes : $headers');</script>";

                        return "<script>alert('Email envoyé avec succès.'); window.location.href = 'index.php?action=profilEnseignant';</script>";
                    } else {
                        return "<script>alert('Erreur lors de l\'envoi de l\'email.');
                            //attendre 2 secondes avant de rediriger
                            window.location.href = 'index.php?action=profilEnseignant';</script>";
                    }
                } else {
                    return "<script>alert('Utilisateur introuvable.'); window.location.href = 'index.php?action=profilEnseignant';</script>";
                }
            } catch (PDOException $e) {
                return "<script>alert('Une erreur est survenue : " . addslashes($e->getMessage()) . "'); window.location.href = 'index.php?action=profilEnseignant';</script>";
            }
        }

        return "Veuillez soumettre le formulaire.";
    }
}