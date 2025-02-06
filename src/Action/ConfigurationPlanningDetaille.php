<?php

namespace src\Action;

class ConfigurationPlanningDetaille extends Action
{
    public function execute() : string
    {
        ob_start();

        include 'src/Gestionnaire/NavBar.html';
        include 'src/Gestionnaire/InfoSemestres.php';

        return ob_get_clean();

    }
}