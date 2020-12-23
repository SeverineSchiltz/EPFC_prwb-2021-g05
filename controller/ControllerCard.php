<?php

require_once 'model/Card.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerCard extends Controller {

    //page d'accueil.     
    public function index() {

    }

    public function view() {
        $user = $this->get_user_or_redirect();
        $card_id = "";
        $errors = [];
        $card_id = $_GET["param1"];
        $card = Card::get_card($card_id);
        if (!isset($_GET["param1"]) || $_GET["param1"] == "") {
            $card_id = $_GET["param1"];
            $card = Card::get_card($card_id);
        }
        (new View("card"))->show(array("card" => $card, "user" => $user, "errors" => $errors));
    }
    
            
    public function move() {
        $test = $_POST["direction"];
        $id = $_POST["card_id"];
    }
        
    public function add() {

    }

}

