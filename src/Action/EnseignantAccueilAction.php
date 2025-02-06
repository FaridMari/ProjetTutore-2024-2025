<?php

namespace src\Action;

class EnseignantAccueilAction extends Action
{
    public function execute(): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Accueil</title>
            <link rel="stylesheet" href="src/Action/layout.css">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        </head>
        <body>
        <?php
        // Inclure la nav bar latérale
        include 'src/Enseignant/NavbarE.html';

        // Inclure le contenu de la page
        include 'src/Enseignant/Accueil.php';
        ?>
        tyjcujvk;tckty
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}
