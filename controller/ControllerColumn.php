<?php

require_once 'model/User.php';
require_once 'model/Board.php';
require_once 'model/Column.php';
require_once 'controller/ControllerBoard.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerColumn extends Controller {

    //page d'accueil.     
    public function index() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $board = Board::get_board($_GET["param1"]);
            $position = $board->get_next_column_position();

            if (isset($_POST['title'])) {
                $errors = $this->add($user, $board, $position);
            } else {
                $errors[] = "Title isn't set";
            }
    
            $this->redirect("board", "board", $_GET["param1"]);
        } else {
            $this->redirect("board", "index");
        }
    }

    //affichage de la colonne donnée
    public function view() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $board = board::get_board($_GET["param1"]);
        }
        (new View("column_view"))->show(array("board" => $board, "user" => $user, "columns" => Column::get_columns($board)));
    }

    //ajout d'une colonne
    private function add($user, $board, $position) {
        $errors = [];
        if (isset($_POST['title'])) {
            $title = $_POST['title'];
            $column = new Column($board, $position, $title);
            $errors = $column->validate();
            if(empty($errors)){
                $user->add_column($column);                
            }
        }
        return $errors;
    }
}
