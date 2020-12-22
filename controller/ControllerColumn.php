<?php

require_once 'model/User.php';
require_once 'model/Board.php';
require_once 'model/Column.php';
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
            $_SESSION['errors'] = $errors;
            $this->redirect("board", "board", $_GET["param1"]);
        } else {
            $this->redirect("board", "index");
        }
    }

    //édition de la colonne donnée
    public function edit() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $column = Column::get_column($_GET["param1"]);
        }
        (new View("column_edit"))->show(array("board" => $board, "user" => $user, "columns" => Column::get_columns($board)));
    }

    //bouger la colonne
    public function move() {
        $this->get_user_or_redirect();
        if ((isset($_POST["board_id"]) && $_POST["board_id"] !== "") 
            && (isset($_POST["direction"]) && $_POST["direction"] !== "") 
            && (isset($_POST["column_id"]) && $_POST["column_id"] !== "")) 
        {
            $board_id = $_POST["board_id"];
            $direction = $_POST["direction"];
            $column_id = $_POST["column_id"];

            $column = Column::get_column($column_id);
            
            if($direction === "left")
                $errors = $column->move_left();
            else if($direction === "right")
                $errors = $column->move_right();

            
            $_SESSION['errors'] = $errors;
            $this->redirect("board", "board", $board_id);
        } else {
            $this->redirect("board", "index");
        }
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
