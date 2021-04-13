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
            //(new View("main_menu"))->show(array("user" => $user, "errors" => $errors, "new_board_name" => $new_board_name, "personal_boards" => Board::get_boards($user), "other_boards" => Board::get_other_boards($user)));
            (new View("main_menu"))->show(array("user" => $user, "errors" => $errors, "new_board_name" => $new_board_name, "personal_boards" => Board::get_my_boards($user), "other_shared_boards" => Board::get_other_shared_boards($user), "other_not_shared_boards" => Board::get_other_not_shared_boards($user)));
        } else {
            (new View("home"))->show();
        }
    }

    //affichage du tableau donné
    public function board() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        $board = $this::get_board_if_exist();
        if($board && $user->has_permission($board->get_board_id())){
            (new View("board"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
        }else{
            $this->redirect("board","index");
        }
    }

    public function delete() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {

            $board = Board::get_board($_GET["param1"]);

            if($user != $board->get_author() && !$user->is_admin()) {
                $errors[] = "You cannot delete someone else's board";
                (new View("board"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
            } else if($board->has_columns()) { // s'il y a des colonnes il faut confirmation
                if(!(isset($_POST['confirmation']) && $_POST['confirmation'])) {
                    $this->redirect("board", "delete_confirm", $board->get_board_id()); // pas de confirmation -> redirection
                }
                else {
                    $board = $this->delete_board(); // confirmation -> on delete
    
                    if ($board) {
                        $this->redirect("board", "index");
                    } else {
                        throw new Exception("Wrong/missing ID or action not permitted");
                    }
                } 
            } else {   
                $_POST['board_id'] = $board->get_board_id();
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
        $board = $this::get_board_if_exist();
        if($board && $user->has_permission($board->get_board_id())){
            (new View("board_delete"))->show(array("user" => $user, "board" => $board));
        }else{
            $this->redirect("board","index");
        }
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
            $board = new Board(NULL,$user, null, null);
            $errors = $board->validate_board_name($new_board_name);
            if (count($errors) == 0) { 
                $board->set_title($new_board_name);
                $board->update(); 
                $this->redirect("board", "index");
            }
        }
        (new View("main_menu"))->show(array("user" => $user, "new_board_name" => $new_board_name, "errors" => $errors, "personal_boards" => Board::get_boards($user), "other_boards" => Board::get_other_boards($user)));
    }

        
    private static function get_board_if_exist() {
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
        if($board && $user->has_permission($board->get_board_id())){
            (new View("board_edit"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
        }else{
            $this->redirect("board","index");
        }
    }

    public function save() {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_POST['board_id']) && isset($_POST['title'])) {
            $proposed_title = $_POST["title"];
            $board = Board::get_board($_POST['board_id']);
            $errors = $board->validate_board_name($proposed_title);
            if (count($errors) == 0) { 
                $board->set_title($_POST["title"]);
                $board->update(); 
                $this->redirect("board", "board", $board->get_board_id());
            }
            (new View("board_edit"))->show(array("board" => $board, "user" => $user, "errors" => $errors, "proposed_title" => $proposed_title));
        }
    }

    public function collaborators(){
        $user = $this->get_user_or_redirect();
        $board = $this::get_board_if_exist();
        $errors = [];
        //attention ici, seul l'admin ou l'auteur du tableau peut voir les collaborateurs
        if($board && ($user->is_admin() || $board->get_author_id() === $user->get_user_id())){
            (new View("board_collaborators"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
        }else{
            $this->redirect("board","index");
        }
    }

    public function add_collaborator(){
        $errors = [];
        if (isset($_POST['board_id']) && isset($_POST['new_collaborator_id'])) {
            $new_collaborator_id = $_POST["new_collaborator_id"];
            $board = Board::get_board($_POST['board_id']);
            $errors = $board->validate_board_new_collaborator($new_collaborator_id);
            if (count($errors) == 0) { 
                $board->add_new_collaborator($new_collaborator_id); 
                $this->redirect("board", "collaborators", $board->get_board_id());
            }
            (new View("board_collaborators"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
        }else{
            $this->redirect("board","index");
        }
    }

    public function remove_collaboration_confirm() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "" && isset($_GET["param2"]) && $_GET["param2"] !== "") {
        $board_id = $_GET["param1"];
        $collaborator_id = $_GET["param2"];
        }else{
            $this->redirect("board","index");
        }
        if($user->has_permission($board_id)){
            (new View("collaboration_delete"))->show(array("user" => $user, "board_id" => $board_id, "collaborator_id" => $collaborator_id));
        }else{
            $this->redirect("board","index");
        }
    }

    public function collaborator_delete(){
        $errors = [];
        if (isset($_POST['board_id']) && isset($_POST['collaborator_id'])) {
            $collaborator_id = $_POST["collaborator_id"];
            $board = Board::get_board($_POST['board_id']);
            if (count($errors) == 0) { 
                $board->remove_collaborator($collaborator_id); 
                $this->redirect("board", "collaborators", $board->get_board_id());
            }
            (new View("board_collaborators"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
        }else{
            $this->redirect("board","index");
        }
    }
}
