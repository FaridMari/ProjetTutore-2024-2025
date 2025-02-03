<?php
namespace src\Action;

class FicheRepartitionAction extends Action {

    public function execute() : string {
        ob_start();

        include 'src/Gestionnaire/NavBar.html';
        include 'src/Gestionnaire/FicheRepartition.php';
        
        return ob_get_clean();

    }
}
?>