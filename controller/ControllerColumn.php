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
            if($board && $user->has_permission_aac($board->get_board_id())){
                $position = $board->get_next_column_position();
                if (isset($_POST['title'])) {
                    $errors = $this->add($board, $position);
                } else {
                    $errors[] = "Title isn't set";
                }
                $title = isset($_POST['title']) && !empty($errors)?$_POST['title']:'';
                (new View("board"))->show(array("board" => $board, "user" => $user, "errors" => $errors, "add_column_title" => $title));
            }else {
                $this->redirect("board", "index");
            }
        } else {
            $this->redirect("board", "index");
        }
    }

    private static function get_column_if_exist() {
        $column_id = "";
        if (isset($_GET["param1"]) && $_GET["param1"] != "" && is_numeric($_GET["param1"])) {
            $column_id = $_GET["param1"];
            $column = Column::get_column($column_id);
            if($column){
                return $column;
            }
        }
        return false;
    }

    //édition de la colonne donnée
    public function edit() {
        $errors = [];
        $user = $this->get_user_or_redirect();
        $column = $this::get_column_if_exist();
        if($column && $user->has_permission_aac($column->get_board_id())){
            (new View("column_edit"))->show(array("user" => $user, "column" => $column, "errors" => $errors));
        }else{
            $this->redirect("board","index");
        }
    }

    public function save() {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if(isset($_POST['column_id']) && isset($_POST['column_title'])) {
            $proposed_title = $_POST["column_title"];
            $column = Column::get_column($_POST['column_id']);
            if($column && $user->has_permission_aac($column->get_board_id()) ) {
                $errors = $column->validate_column_name($proposed_title, $column->get_title());
                if(count($errors) == 0) {
                    $column->set_title($_POST['column_title']);
                    $errors = $column->update();
                    $this->redirect("board", "board", $column->get_board_id());
                }
                else                    
                    (new View("column_edit"))->show(array("user" => $user, "column" => $column, "errors" => $errors, "proposed_title" => $proposed_title));
            }else{
                $this->redirect("board","index");
            }
        }
    }

    //bouger la colonne
    public function move() {
        $user = $this->get_user_or_redirect();
        if ((isset($_POST["board_id"]) && $_POST["board_id"] !== "") 
            && (isset($_POST["direction"]) && $_POST["direction"] !== "") 
            && (isset($_POST["column_id"]) && $_POST["column_id"] !== "")) 
        {
            $board_id = $_POST["board_id"];
            $direction = $_POST["direction"];
            $column_id = $_POST["column_id"];
            $errors = [];
            $column = Column::get_column($column_id);
            if($column && $user->has_permission_aac($column->get_board_id()) ) {
                $errors = $column->move($direction);
                if(count($errors) == 0)
                    $this->redirect("board", "board", $board_id);
                else {
                    $board = Board::get_board($board_id);
                    (new View("board"))->show(array("board" => $board, "user" => $user, "errors" => $errors));
                }
            }else{
                $this->redirect("board", "index");
            }
        } else {
            $this->redirect("board", "index");
        }
    }

    //ajout d'une colonne
    private function add($board, $position) {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if (isset($_POST['title'])) {
            $title = $_POST['title'];
            $column = new Column($board, $position, $title);
            if($column && $user->has_permission_aac($column->get_board_id())){
                $errors = $column->validate();
                if(count($errors) == 0){
                    $column->update();
                    $this->redirect("board", "board", $column->get_board_id());  
                }
            }else {
                $this->redirect("board", "index");
            }
        }
        return $errors;
    }


    //suppression de la colonne donnée
    public function delete() {
        $user = $this->get_user_or_redirect();
        if (isset($_GET["param1"]) && $_GET["param1"] !== "") {
            $column = Column::get_column($_GET["param1"]);
            if($column && $user->has_permission_aac($column->get_board_id())) {
                //on vérifie si la colonne a des cartes associées
                //si oui, demande de confirmation
                if($column->has_cards()) {
                    if(!(isset($_POST['confirmation']) && $_POST['confirmation'])) {
                        $this->redirect("column", "delete_confirm", $column->get_column_id());
                    } else {
                        $column = $this->delete_column();
                        if ($column) {
                            $this->redirect("board", "board", $column->get_board_id());
                        } else {
                            throw new Exception("Wrong/missing ID or action not permitted");
                        }
                    }
                } else {
                    $column->delete();
                    $this->redirect("board", "board", $column->get_board_id());
                }
            } else {    
                $this->redirect("board", "index");
            }
        } else {    
            $this->redirect("board", "index");
        }
    }

    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        $column = $this::get_column_if_exist();
        if($column && $user->has_permission_aac($column->get_board_id())){
            (new View("column_delete"))->show(array("user" => $user, "column" => $column));
        }else{
            $this->redirect("board","index");
        }
    }

    private function delete_column() {
        $user = $this->get_user_or_redirect();
        if (isset($_POST['column_id']) && $_POST['column_id'] != "") {
            $column_id = $_POST['column_id'];
            $column = Column::get_column($column_id);
            if ($column && $user->has_permission_aac($column->get_board_id())) {
                return $column->delete();
            } 
        }
        return false;
    }

    //services

    public function change_columns_in_board_service(){
        $user = $this->get_user_or_redirect();
        if(isset($_POST["board_info"]) && $_POST["board_info"] !== ""){
            $board_info = $_POST["board_info"];
            $board_to_update = Board::get_board($board_info["id"]);
            $columns_id = $board_info["columns_id"];
            $board_to_update->update_all_columns_position($columns_id);
            echo "true";
        }else{
            echo "false";
        }
    }

    public function available_column_title_service(){
        $res = "true";
        if(isset($_POST["column_title"]) && $_POST["column_title"] !== "" && isset($_POST["board_id"]) && $_POST["board_id"] !== ""){
            if(isset($_POST["board_id"]) && $_POST["board_id"] !== "")
            $column = Column::get_column_by_title_board_id($_POST["column_title"], $_POST["board_id"]);
            if(isset($_POST["column_id"]) && $_POST["column_id"] !== "") { //update of an existing column's title
                if($column && $_POST["column_id"] !== $column->get_column_id()) 
                    $res = "false";
            } else if ($column) //new column's title
                $res = "false"; 
        }
        echo $res;
    }
}
