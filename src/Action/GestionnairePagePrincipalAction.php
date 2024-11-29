<?php

namespace src\Action;

class GestionnairePagePrincipalAction extends Action
{
    public function execute() : string
    {
        ob_start();

        include 'src/Gestionnaire/PagePrincipal.php';

        return ob_get_clean();

    }
}