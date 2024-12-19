<?php
namespace src\Dispatcher;


use src\Action\EnseignantFIcheContrainteAction;
use src\Action\EnseignantFicheRessourceAction;
use src\Action\SigninAction;
use src\Action\GestionCompteUtilisateurAction;
use src\Action\GestionnairePagePrincipalAction;
use src\Action\EnseignantPagePrincipalAction;
use src\Action\GestionnaireCreerUtilisateurAction;
use src\Action\PlanningDetaille;
use src\Action\FichePrevisionnelleAction;



class Dispatcher {
    private $action;

    public function __construct($action) {
        $this->action = $action;
    }

    public function run(): void {
        session_start();

        // Si un cookie d'utilisateur existe et une session est active
        if (isset($_COOKIE['user_id']) && isset($_SESSION['user_id'])) {
            // Si l'utilisateur est connecté
            $this->navigateToAction();
        } else {
            // Rediriger vers la page de connexion
            $action = new SigninAction();
            echo $action->execute();
        }
    }

    public function navigateToAction(): void {
        error_reporting(E_ALL);
        switch ($this->action) {
            case 'signin':
                $log = new SigninAction();
                $this->renderPage($log->execute());
                break;
            case 'gestionCompteUtilisateur':
                $action = new GestionCompteUtilisateurAction();
                echo $action->execute();
                break;
            case 'gestionnairePagePrincipal':
                $action = new GestionnairePagePrincipalAction();
                echo $action->execute();
                break;
            case 'enseignantPagePrincipal':
                $action = new EnseignantPagePrincipalAction();
                echo $action->execute();
                break;
            case 'gestionnaireCreerUtilisateurAction':
                $action = new GestionnaireCreerUtilisateurAction();
                echo $action->execute();
                break;
            case 'enseignantFicheContrainte':
                $action = new EnseignantFicheContrainteAction();
                echo $action->execute();
                break;
            case 'enseignantFicheRessource':
                $action = new EnseignantFicheRessourceAction();
                echo $action->execute();
                break;
            case 'ficheDetaille':
                $action = new PlanningDetaille();
                echo $action->execute();
                break;
            case 'fichePrevisionnelle':
                $action = new FichePrevisionnelleAction();
                echo $action->execute();
                break;
            default:
                $action = new SigninAction();
                echo $action->execute();
                break;
        }
    }

    private function renderPage(string $html): void {
        echo $html;
    }
}
