<?php
namespace src\Action;

class EnseignantPagePrincipalAction extends Action {
    public function execute(): string {
        ob_start();
        include 'src/Enseignant/NavbarE.html';
        return ob_get_clean();
    }
}