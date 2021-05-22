<?php

require_once 'model/Card.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerCard extends Controller {

    //page d'accueil.     
    public function index() {
        $this->view();
    }

    private static function get_card_if_exist() {
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
        if($card && $user->has_permission_aac($card->get_board_id())){
            (new View("card"))->show(array("card" => $card, "user" => $user));
        }else{
            $this->redirect("board","index");
        }
    }


    public function edit() {
        $user = $this->get_user_or_redirect();
        $card = $this::get_card_if_exist();
        $errors = [];
        if($card && $user->has_permission_aac($card->get_board_id())){
            (new View("card_edit"))->show(array("card" => $card, "user" => $user, "errors" => $errors));
        }else{
            $this->redirect("board","index");
        }
    }

    public function save() {
        $user = $this->get_user_or_redirect();
        $errors = [];
        if(isset($_POST["card_id"]) && isset($_POST["title"]) && isset($_POST["body"]) && isset($_POST["due_date"]) ){
            $proposed_title = $_POST["title"];
            $proposed_due_date = $_POST["due_date"];
            $card_id = $_POST["card_id"];
            $card = Card::get_card($card_id);
            if($card && $user->has_permission_aac($card->get_board_id())){
                $card->set_body($_POST["body"]);
                $card->update_content(); 
                $errors = $card->validate_title($proposed_title);
                $errors = array_merge($errors, $card->validate_due_date($proposed_due_date));
                if (count($errors) == 0) { 
                    $card->set_title($_POST["title"]);
                    $card->set_due_date($_POST["due_date"]);
                    $card->update_content(); 
                    $this->redirect("card","view", $card_id);
                }
                (new View("card_edit"))->show(array("card" => $card, "user" => $user, "errors" => $errors, "proposed_title" => $proposed_title));
            }else{
                $this->redirect("board","index");
            }
        }else{
            $this->redirect("board","index");
        }
    }
    
            
    public function move() {
        $user = $this->get_user_or_redirect();
        $card_id = "";
        $errors = [];
        if(isset($_POST["card_id"]) && isset($_POST["direction"])){
            $card_id = $_POST["card_id"];
            $card = Card::get_card($card_id);
            $direction = $_POST["direction"];
            if($user->has_permission_aac($card->get_board_id())){
                if($direction === "up"){
                    $errors =$card->change_position(-1);
                }else if($direction === "down"){
                    $errors =$card->change_position(1);
                }else if($direction === "left"){
                    $errors =$card->change_column(-1);
                }else if($direction === "right"){
                    $errors =$card->change_column(1);
                }
                if (count($errors) == 0) { 
                    $this->redirect("board","board", $card->get_board_id());
                }
                (new View("board"))->show(array("board" => Board::get_board($card->get_board_id()), "user" => $user, "errors" => $errors));
            }else{
                $this->redirect("board","index");
            }
        }else{
            $this->redirect("board","index");
        }
    }
        
    public function add() {
        $user = $this->get_user_or_redirect();
        $card = null;
        $column_id = "";
        $errors = [];
        if(isset($_POST["column_id"]) && isset($_POST["title"])){
            $column_id = $_POST["column_id"];
            $new_card_name = $_POST["title"];
            $column = Column::get_column($column_id);
            $board = Board::get_board($column->get_board_id());
            $position = Card::get_last_card_position_in_column($column_id) + 1;
            $card = new Card(null, $column, $position, $user, null, "", new DateTime("now"));
            $errors = $card->validate_title($new_card_name);
            $card->set_title($new_card_name);
            if (count($errors) == 0 && $user->has_permission_aac($card->get_board_id())) { 
                $card->insert_new_card(); 
                $this->redirect("board","board", $board->get_board_id());
            }
            (new View("board"))->show(array("board" => $board, "user" => $user, "errors" => $errors, "new_card" => $card));
        }else{
            $this->redirect("board","index");
        }
    }

    public function delete_confirm() {
        $user = $this->get_user_or_redirect();
        $card = $this::get_card_if_exist();
        if($card && $user->has_permission_aac($card->get_board_id())){
            (new View("card_delete"))->show(array("card" => $card, "user" => $user));
        }else{
            $this->redirect("board","index");
        }
    }

    public function delete() {
        $user = $this->get_user_or_redirect();
        if(isset($_POST["card_id"])){
            $card = Card::get_card($_POST["card_id"]);
            if($card && $user->has_permission_aac($card->get_board_id())){
                $card->delete(); 
                $this->redirect("board","board", $card->get_board_id());
            }else{
                $this->redirect("board","index");
            }
        }else{
            $this->redirect("board","index");
        }
    }

    public function delete_service(){
        $user = $this->get_user_or_redirect();
        if(isset($_POST["card_id"])){
            $card = Card::get_card($_POST["card_id"]);
            if($card && $user->has_permission_aac($card->get_board_id())){
                $card->delete(); 
                echo "true";
            }else{
                echo "false";
            }
        }else{
            echo "false";
        }
    }

    public function remove_participant() {        
        $user = $this->get_user_or_redirect();
        if(isset($_POST["card_id"]) && isset($_POST["participant_id"])){
            $card = Card::get_card($_POST["card_id"]);
            if($card && $user->has_permission_aac($card->get_board_id())){
                $card->remove_participant($_POST["participant_id"]); 
                $this->redirect("card","edit",$_POST["card_id"]);
            }else{
                $this->redirect("board","index");
            }
        }else{
            $this->redirect("board","index");
        }
    }

    public function add_participant() {     
        $user = $this->get_user_or_redirect();
        if(isset($_POST["card_id"]) && isset($_POST["collaborator_id"])){
            $card = Card::get_card($_POST["card_id"]);
            if($card && $user->has_permission_aac($card->get_board_id())){
                $card->add_participant($_POST["collaborator_id"]); 
                $this->redirect("card","edit",$_POST["card_id"]);
            }else{
                $this->redirect("board","index");
            }
        }else{
            $this->redirect("board","index");
        }
    }

    public function change_cards_in_column_service(){
        $user = $this->get_user_or_redirect();
        if(isset($_POST["column_info"]) && $_POST["column_info"] !== ""){
            $column_info = $_POST["column_info"];
            $column_to_update = Column::get_column($column_info["id"]);
            $cards_id = $column_info["cards_id"];
            if($column_to_update && $user->has_permission_aac($column_to_update->get_board_id())){
                $column_to_update->update_all_cards_position($cards_id);
                echo "true";
            }else{
                echo "false";
            }
        }else{
            echo "false";
        }
    }

    public function available_card_title_service(){
        $user = $this->get_user_or_redirect();
        $res = "true";
        if(isset($_POST["card_title"]) && $_POST["card_title"] !== "" && isset($_POST["board_id"]) && $_POST["board_id"] !== ""){
            if(isset($_POST["board_id"]) && $_POST["board_id"] !== "")
            $card = Card::get_card_by_title_board_id($_POST["card_title"], $_POST["board_id"]);
            if(isset($_POST["card_id"]) && $_POST["card_id"] !== "") {//update of an existing card's title
                if($card && $_POST["card_id"] !== $card->get_card_id()) 
                    $res = "false";
            } else if ($card) //new card's title
                $res = "false"; 
        }
        echo $res;
    }

}

