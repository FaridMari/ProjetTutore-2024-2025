<?php

namespace src\Action;

class GestionnaireConfigurationPlanningDetaille extends Action
{
//    public function execute() : string
//    {
//        ob_start();
//
//        include 'src/Gestionnaire/Navbar_Top.html';
//        include 'src/Gestionnaire/Page_ConfigurationPlanningDetaille.php';
//
//        return ob_get_clean();
//
//    }



    public function execute(): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Configuration</title>
            <link rel="stylesheet" href="css/InfoSemestreStyle.css">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">

            <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" />

        </head>
        <body>
        <?php
        include 'src/Gestionnaire/Navbar_Top.html';

        include 'src/Gestionnaire/Page_ConfigurationPlanningDetaille.php';
        ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }
}