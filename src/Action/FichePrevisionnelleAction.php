<?php

namespace src\Action;

class FichePrevisionnelleAction extends Action
{
    public function execute() : string
    {
        ob_start();
        include __DIR__ . '/../Enseignant/FichePrevisionnelle.php';
        return ob_get_clean();

    }
}