<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerMain extends Controller {

    //si l'utilisateur est connecté, redirige vers sa page principale.
    //sinon, produit la vue d'accueil.
    public function index() {
        $this->redirect("board", "index");
    }

}
