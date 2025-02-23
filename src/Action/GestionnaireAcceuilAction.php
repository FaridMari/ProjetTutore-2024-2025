<?php

namespace src\Action;

class GestionnaireAcceuilAction extends Action
{
    public function execute(): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Gestion des comptes</title>
            <!-- 1) Feuille de style globale qui place #menu à gauche et #main-content à droite -->
            <link rel="stylesheet" href="src/Action/layout.css">

            <!-- 2) (Facultatif) Bootstrap ou autres bibliothèques CSS -->
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        </head>
        <body>
        <?php
        // 3) Inclure la nav bar (qui contient son propre <style> pour l’esthétique)
        include 'src/Gestionnaire/Navbar_Generique.html';

        // 4) Inclure le contenu principal (déposé dans #main-content par layout.css)
        include 'src/Gestionnaire/Page_Acceuil.php';
        ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}