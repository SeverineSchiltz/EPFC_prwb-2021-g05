<?php

require_once 'model/Card.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerCard extends Controller {

    //page d'accueil.     
    public function index() {
        $this->view();
    }

    public function view() {
        $user = $this->get_user_or_redirect();
        $card_id = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "" && is_numeric($_GET["param1"])) {
            $card_id = $_GET["param1"];
            $card = Card::get_card($card_id);
            if($card){
                (new View("card"))->show(array("card" => $card, "user" => $user));
            }else{
                $this->redirect("board","index");
            }
        }else{
            $this->redirect("board","index");
        }
    }
    
            
    public function move() {
        $test = $_POST["direction"];
        $id = $_POST["card_id"];
    }
        
    public function add() {

    }

}

