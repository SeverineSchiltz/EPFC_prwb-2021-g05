<?php

require_once 'model/User.php';
require_once 'model/Board.php';
require_once 'model/Column.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerBoard extends Controller {

    //page d'accueil.     
    public function index() {
        $errors = [];
        $success = "";
        if ($this->user_logged()) {
            $user = $this->get_user_or_redirect();
            (new View("main_menu"))->show(array("user" => $user, "errors" => $errors, "success" => $success, "personal_boards" => Board::get_boards($user), "other_boards" => Board::get_other_boards($user)));
        } else {
            (new View("home"))->show();
        }
    }

    //affichage du board donné
    public function board() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $board = board::get_board($_GET["param1"]);
            (new View("board"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
        } 
        else $this->index();
    }
}
