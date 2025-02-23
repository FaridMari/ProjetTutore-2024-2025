<?php

namespace src\Action;

class GestionnaireConfigurationPlanningDetaille extends Action
{
    public function execute() : string
    {
        ob_start();

        include 'src/Gestionnaire/Navbar_Top.html';
        include 'src/Gestionnaire/Page_ConfigurationPlanningDetaille.php';

        return ob_get_clean();

    }
}