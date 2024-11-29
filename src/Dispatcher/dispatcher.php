<?php
namespace src\Dispatcher;


use src\Action\SigninAction;
use src\Dispatcher\GestionCompteUtilisateurAction;




class Dispatcher {
    private $action;

    public function __construct($action) {
        $this->action = $action;
    }

    public function run(): void {
        switch ($this->action) {
            case 'signin':
                $log = new SigninAction();
                $this->renderPage($log->execute());
                break;
            case 'gestionCompteUtilisateur':
                $action = new GestionCompteUtilisateurAction();
                echo $action->execute();
                break;
            default:
                break;
        }
    }

    private function displayDefaultPage(): void {
        $displayTweets = new DisplayTweetsAction();
        $this->renderPage($displayTweets->execute());
    }

    private function renderPage(string $html): void {
        echo $html;
    }
}
