<?php

namespace src\Action;

class GestionnaireCreerUtilisateurAction extends Action
{
    public function execute() : string
    {
        ob_start();
        include 'src/Gestionnaire/CreationUtilisateur.php';
        return ob_get_clean();

    }

}