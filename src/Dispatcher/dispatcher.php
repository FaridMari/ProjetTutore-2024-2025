<?php
namespace src\Dispatcher;


use src\Action\GestionnaireConfigurationPlanningDetaille;
use src\Action\GestionnaireAfficherPageCreerUtilisateurAction;
use src\Action\GestionnaireModifierUtilisateurAction;
use src\Action\EnseignantFicheContrainteAction;
use src\Action\EnseignantFicheRessourceAction;
use src\Action\SigninAction;
use src\Action\GestionnaireGestionCompteUtilisateurAction;
use src\Action\GestionnaireAcceuilAction;
use src\Action\EnseignantPagePrincipalAction;
use src\Action\GestionnaireCreerUtilisateurAction;
use src\Action\GestionnairePlanningDetaille;
use src\Action\EnseignantFicheServiceAction;
use src\Action\GestionnaireSupprimerUtilisateurAction;
use src\Action\GestionnaireFicheRepartitionAction;
use src\Action\EnseignantAccueilAction;
use src\Action\GestionnaireCoursAction;
use src\Action\GestionnaireValidationFicheAction;






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
                $action = new GestionnaireGestionCompteUtilisateurAction();
                echo $action->execute();
                break;
            case 'gestionnairePagePrincipal':
                $action = new GestionnaireAcceuilAction();
                echo $action->execute();
                break;
            case 'enseignantPagePrincipal':
                $action = new EnseignantPagePrincipalAction();
                echo $action->execute();
                break;
            case 'gestionnaireCreerUtilisateurAction':
                $action = new GestionnaireAfficherPageCreerUtilisateurAction();
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
                $action = new GestionnairePlanningDetaille();
                echo $action->execute();
                break;
            case 'ficheDetailles':
                $action = new PlanningDetailleS();
                echo $action->execute();
                break;
            case 'fichePrevisionnelle':
                $action = new EnseignantFicheServiceAction();
                echo $action->execute();
                break;
            case 'ficheRepartition':
                $action = new GestionnaireFicheRepartitionAction();
                echo $action->execute();
                break;
            case 'delete-user':
                $action = new GestionnaireSupprimerUtilisateurAction();
                echo $action->execute();
                break;
            case 'creer-utilisateur':
                $action = new GestionnaireAfficherPageCreerUtilisateurAction();
                echo $action->execute();
                break;
            case 'edit-user':
                $action = new GestionnaireModifierUtilisateurAction();
                echo $action->execute();
                break;
            case 'accueilEnseignant':
                $action = new EnseignantAccueilAction();
                echo $action->execute();
                break;
            case 'deconnexion':
                session_destroy();
                $log = new SigninAction();
                $this->renderPage($log->execute());
                break;
            case 'configurationPlanningD':
                $action = new GestionnaireConfigurationPlanningDetaille();
                echo $action->execute();
                break;
            case 'gestionRessource':
                $action = new GestionnaireCoursAction();
                echo $action->execute();
                break;
            case 'ficheEnseignant':
                $action = new GestionnaireValidationFicheAction();
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
