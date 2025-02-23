<?php

namespace src\Action;

class PlanningDetailleS extends Action
{
    public function execute() : string
    {
        ob_start();

        include 'src/Gestionnaire/Navbar_Generique.html';
        include 'src/Gestionnaire/PlanningDetailleS.php';

        return ob_get_clean();

    }
}