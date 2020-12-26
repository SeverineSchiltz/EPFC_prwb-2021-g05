<?php

require_once 'model/Card.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerCard extends Controller {

    //page d'accueil.     
    public function index() {
        $this->view();
    }

    public static function get_card_if_exist() {
        $card_id = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "" && is_numeric($_GET["param1"])) {
            $card_id = $_GET["param1"];
            $card = Card::get_card($card_id);
            if($card){
                return $card;
            }
        }
        return false;
    }

    public function view() {
        $user = $this->get_user_or_redirect();
        $card = $this::get_card_if_exist();
        if($card){
            (new View("card"))->show(array("card" => $card, "user" => $user));
        }else{
            $this->redirect("board","index");
        }
    }


    public function edit() {
        $user = $this->get_user_or_redirect();
        $card = $this::get_card_if_exist();
        if($card){
            (new View("card_edit"))->show(array("card" => $card, "user" => $user));
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

