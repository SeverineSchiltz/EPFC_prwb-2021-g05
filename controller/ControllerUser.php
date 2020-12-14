<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerUser extends Controller {

    //page d'accueil. 
    public function index() {
        $this->main_menu();
    }

    //affiche le menu principal
    public function main_menu() {
        $user = $this->get_user_or_redirect();
        (new View("main_menu"))->show(array("user" => $user));
    }
}
