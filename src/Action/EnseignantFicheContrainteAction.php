<?php

namespace src\Action;

class EnseignantFicheContrainteAction extends Action
{
    public function execute(): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Fiche de Vœux</title>
            <!-- 1) Feuille de style globale qui place #menu à gauche et #main-content à droite -->
            <link rel="stylesheet" href="src/Action/layout.css">

            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

        </head>
        <body>
        <?php
        include 'src/Enseignant/NavbarE.html';

        include 'src/Enseignant/FicheContrainte.php';

        ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

}
