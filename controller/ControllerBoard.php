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
        $new_board_name = "";
        if ($this->user_logged()) {
            $user = $this->get_user_or_redirect();
            (new View("main_menu"))->show(array("user" => $user, "errors" => $errors, "new_board_name" => $new_board_name, "personal_boards" => Board::get_boards($user), "other_boards" => Board::get_other_boards($user)));
        } else {
            (new View("home"))->show();
        }
    }

    //affichage du tableau donné
    public function board() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $board = board::get_board($_GET["param1"]);
            if(isset($_SESSION['errors'])) 
                $errors = $_SESSION['errors'];
            (new View("board"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
            unset($_SESSION['errors']);
        } 
        else $this->index();
    }

    public function delete() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {

            $board = Board::get_board($_GET["param1"]);

            if($user != $board->author) {
                $errors[] = "You cannot delete someone else's board";
                $_SESSION['errors'] = $errors;
                $this->redirect("board", "board", $board->board_id);
            } else if($board->has_columns()) {
                if(!(isset($_POST['confirmation']) && $_POST['confirmation'])) {
                    $this->redirect("board", "delete_confirm", $board->board_id);
                }
                else {
                    $board = $this->delete_board();
    
                    if ($board) {
                        $this->redirect("board", "index");
                    } else {
                        throw new Exception("Wrong/missing ID or action not permitted");
                    }
                } 
            } else {   
                $_POST['board_id'] = $board->board_id;
                $board = $this->delete_board();
    
                if ($board) {
                    $this->redirect("board", "index");
                } else {
                    throw new Exception("Wrong/missing ID or action not permitted");
                }
            }
        } else {    
            $this->redirect("board", "index");
        }
    }

    //suppression du tableau donné
    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        $board = Board::get_board($_GET["param1"]);

        (new View("board_delete"))->show(array("user" => $user, "board" => $board));
    }

    private function delete_board() {
        $user = $this->get_user_or_redirect();

        if (isset($_POST['board_id']) && $_POST['board_id'] != "") {
            $board_id = $_POST['board_id'];
            $board = Board::get_board($board_id);
            if ($board) {
                return $board->delete($user);
            } 
        }
        return false;
    }

        //ajout d'un tableau
        public function add() {
            $user = $this->get_user_or_redirect();
            $new_board_name = '';
            $errors = [];
            if (isset($_POST['new_board_name'])) {
                $new_board_name = trim($_POST['new_board_name']);
                $board = new Board(NULL,$user, $new_board_name, null);
                $errors = $board->validate_board_name();
                if (count($errors) == 0) { 
                    $board->update(); 
                    $this->redirect("board", "index");
                }
            }
            (new View("main_menu"))->show(array("user" => $user, "new_board_name" => $new_board_name, "errors" => $errors, "personal_boards" => Board::get_boards($user), "other_boards" => Board::get_other_boards($user)));
        }

        
    public static function get_board_if_exist() {
        $board_id = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "" && is_numeric($_GET["param1"])) {
            $board_id = $_GET["param1"];
            $board = Board::get_board($board_id);
            if($board){
                return $board;
            }
        }
        return false;
    }

        public function edit() {
            $user = $this->get_user_or_redirect();
            $board = $this::get_board_if_exist();
            $errors = [];
            if($board){
                (new View("board_edit"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
            }else{
                $this->redirect("board","index");
            }
        }

        public function save() {
            $user = $this->get_user_or_redirect();
            $errors = [];
            if (isset($_POST['board_id']) && isset($_POST['title'])) {
                $board = Board::get_board($_POST['board_id']);
                $board->set_title($_POST["title"]);
                $errors = $board->validate_board_name();
                if (count($errors) == 0) { 
                    $board->update(); 
                    $this->redirect("board", "board", $board->get_board_id());
                }
                (new View("board_edit"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
            }
        }
}
