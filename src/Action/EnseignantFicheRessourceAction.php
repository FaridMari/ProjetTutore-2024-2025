<?php

namespace src\Action;

class EnseignantFicheRessourceAction extends Action
{
    public function execute(): string
    {
        ob_start();
        include 'src/Enseignant/FicheRessource.php';
        return ob_get_clean();
    }

}
