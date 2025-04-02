<?php

namespace src\Action;

class GestionnaireCoursAction
{
    public function execute(): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Gestion des cours</title>
            <link rel="stylesheet" href="src/Action/layout.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
        </head>
        <body>
        <?php
        include 'src/Gestionnaire/Navbar_Generique.html';

        include 'src/Gestionnaire/Page_GestionCours.php';
        ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}