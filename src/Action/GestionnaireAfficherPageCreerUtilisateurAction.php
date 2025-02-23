<?php

namespace src\Action;

class GestionnaireAfficherPageCreerUtilisateurAction extends Action
{
    public function execute() : string
    {
        ob_start();
        include 'src/Gestionnaire/Page_CreationUtilisateur.php';
        return ob_get_clean();

    }

}