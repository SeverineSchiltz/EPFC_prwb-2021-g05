<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerBoard extends Controller {

    //page d'accueil.     
    public function index() {
        if ($this->user_logged()) {
            $user = $this->get_user_or_redirect();
            (new View("main_menu"))->show(array("user" => $user));
        } else {
            (new View("home"))->show();
        }
    }
}
