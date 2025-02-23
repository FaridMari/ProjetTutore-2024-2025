<?php

namespace src\Action;

class PlanningDetaille extends Action
{
    public function execute(): string
    {
        ob_start();
        ?>
        <!DOCTYPE html>
        <html lang="fr">
        <head>
            <meta charset="UTF-8">
            <title>Planning détaillé</title>
            <link rel="stylesheet" href="src/Action/layout_top.css">
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/handsontable.min.css" />
            <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/handsontable/styles/ht-theme-main.min.css" />
            <script src="https://cdn.jsdelivr.net/npm/handsontable@12.1.0/dist/handsontable.full.min.js"></script>
        </head>
        <body>

        <?php
        // Inclure la nav bar top
        include 'src/Gestionnaire/Navbar_Top.html';

        // Inclure le contenu de la page
        include 'src/Gestionnaire/Page_PlanningDetaille.php';

        ?>
        </body>
        </html>
        <?php
        return ob_get_clean();
    }

}