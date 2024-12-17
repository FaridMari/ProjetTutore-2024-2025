<?php

namespace src\Action;

class PlanningDetaille extends Action
{
    public function execute() : string
    {
        ob_start();

        include 'src/Gestionnaire/NavBar.html';
        include 'src/Gestionnaire/PlanningDetaille.php';

        return ob_get_clean();

    }
}