<?php

namespace src\Action;

use src\Db\connexionFactory;

class GestionCompteUtilisateurAction extends Action
{
    public function execute() : string
    {
        ob_start();

        include 'src/Gestionnaire/GestionCompte.php';

        return ob_get_clean();

    }
}