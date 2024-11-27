<?php
namespace src\Dispatcher;


use src\Action\SigninAction;




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
