<?php

namespace src\Action;


class GestionnaireGestionCompteUtilisateurAction extends Action
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
            <link rel="stylesheet" href="src/Action/layout.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
        </head>
        <body>
        <?php
        include 'src/Gestionnaire/Navbar_Generique.html';

        include 'src/Gestionnaire/Page_GestionUtilisateur.php';
        ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

}