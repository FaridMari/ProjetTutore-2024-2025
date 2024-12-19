<?php

namespace src\Action;

class EnseignantFIcheContrainteAction extends Action
{
    public function execute(): string
    {
        ob_start();
        include 'src/Enseignant/FicheContrainte.php';
        return ob_get_clean();
    }

}
