<?php

require_once "framework/Model.php";
require_once "Column.php";
require_once "User.php";

class Card extends Model {

    private $card_id;
    private $column;
    private $position;
    private $author;
    private $title;
    private $body;
    private $created_at;
    private $due_date;
    private $last_modified;
    private $cards;

    public function __construct($card_id, $column, $position, $author, $title, $body, $created_at, $last_modified = NULL, $due_date = NULL) {
        $this->card_id = $card_id;
        $this->column =  $column;
        $this->position = $position;
        $this->author = $author;
        $this->title = $title;
        $this->body = $body;
        $this->created_at = $created_at;
        $this->last_modified = $last_modified;
        $this->due_date = $due_date;
    }

    public function get_card_id() {
     return $this->card_id;
    }  
    
    public function get_title() {
        return $this->title;
    } 

    public function get_position() {
        return $this->position;
    } 

    public function get_body() {
        return $this->body;
    } 

    public function get_due_date() {
        return $this->due_date;
    } 

    public function get_formatted_due_date() {
        return MyTools::format_date($this->due_date);
    } 

    public function get_participants() {
        $query = self::execute("select * from `participate` where Card = :id", array("id" => $this->card_id));
        $data = $query->fetchAll();
        $participants = [];
        foreach ($data as $row) {
            $participants[] = User::get_user_by_id($row['Participant']);
        }
        return $participants;
    }

    public function get_last_position() {
        $query = self::execute("select Position from card where `Column` = :id order by Position DESC limit 1", array("id" => $this->column->get_column_id())); 
        $row = $query->fetch();
        return $row['Position'];
    }   

    public function get_first_position() {
        $query = self::execute("select Position from card where `Column` = :id order by Position ASC limit 1", array("id" => $this->column->get_column_id())); 
        $row = $query->fetch();
        return $row['Position'];
    }     

    public static function get_cards($column) {
        $query = self::execute("select * from card where `Column` = :id order by Position ASC", array("id" => $column->get_column_id()));
        $data = $query->fetchAll();
        $cards = [];
        foreach ($data as $row) {
            $cards[] = new Card($row['ID'], $column, $row['Position'], User::get_user_by_id($row['Author']), $row['Title'], $row['Body'], $row['CreatedAt'], $row['ModifiedAt'], $row['DueDate']);
        }
        return $cards;
    }

    public function get_last_modification() {
        return $this->last_modified;
    }
    
    public static function get_card($id) {
        $query = self::execute("select * from card where ID = :id", array("id" => $id));
        $data = $query->fetch(); 
        $cards = [];
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Card($data['ID'], Column::get_column($data['Column']), $data['Position'], User::get_user_by_id($data['Author']), $data['Title'], $data['Body'], $data['CreatedAt'], $data['ModifiedAt'], $data['DueDate']);
        }
    }

    public function get_author_name() {
        return $this->author->get_full_name();
    }

    public function get_column_title() {
        return $this->column->get_title();
    }

    public function get_column_id() {
        return $this->column->get_column_id();
    }

    public function get_board_title() {
        return $this->column->get_board_title();
    }

    public function get_board_id() {
        return $this->column->get_board_id();
    }

    public function get_duration_since_creation() {
        return MyTools::get_duration_since_date($this->created_at);
    }

    public function get_duration_since_last_edit() {     
        return MyTools::get_duration_since_date($this->last_modified);
    }

    public function update_content() {
        $errors = $this->validate();
        if(empty($errors)){
            self::update();
            return true;
        }
        return false;
    }

    private function update() {
        if($this->due_date == null)
            self::execute('UPDATE card SET Title = :title, Body = :body, Position = :position,  `Column`= :column, ModifiedAt = current_timestamp(), DueDate = null WHERE ID = :card_id', array(
                'card_id' => $this->card_id,
                'title' => $this->title,
                'body' => $this->body,
                'position' => $this->position,
                'column' => $this->get_column_id()
            ));
        else
            self::execute('UPDATE card SET Title = :title, Body = :body, Position = :position,  `Column`= :column, ModifiedAt = current_timestamp(), DueDate = :due_date WHERE ID = :card_id', array(
                'card_id' => $this->card_id,
                'title' => $this->title,
                'body' => $this->body,
                'position' => $this->position,
                'column' => $this->get_column_id(),
                'due_date' => $this->due_date
            ));
    }

    public function insert_new_card() {
        $errors = $this->validate_title($this->title);
        if(empty($errors)){
            self::execute('INSERT INTO card (Title, Body, Position, CreatedAt, Author, `Column`) VALUES (:title, :body, :position, current_timestamp(), :author, :column)', array(
                'title' => $this->title,
                'body' => $this->body,
                'position' => $this->position,
                'author' => $this->author->get_user_id(),
                'column' => $this->get_column_id()
            ));
            return true;
        }
        return false;
    }

    public function validate_title($title){
        $errors = array();
        if(!(isset($title) && is_string($title) && strlen($title) > 2 && strlen($title) < 129)){
            $errors[] = "Card title length must be between 3 and 128 characters";
        }
        if($title != $this->title && !$this->validate_unicity_in_board($title)){
            $errors[] = "Title card must be unique on this board.";
        }
        return $errors;
    }

    public function validate_due_date($due_date){
        $errors = array();
        if($this->is_past_created_date($due_date)){
            $errors[] = "Due date must be after the card creation date";
        }
        return $errors;
    }

    private function validate() {
        $errors = array();
        if(!(isset($this->title) && is_string($this->title) && strlen($this->title) > 2 && strlen($this->title) < 129)){
            $errors[] = "Card title length must be between 3 and 128 characters";
        }
        if(!$this->validate_unicity_in_board($this->title)){
            $errors[] = "Card title must be unique on this board.";
        }
        if($this->past_created_date()){
            $errors[] = "Due date must be after the card creation date";
        }
        return $errors;
    }

    public function validate_unicity_in_board($title) {
        $query = self::execute("SELECT ca.ID FROM card ca
                                INNER JOIN `column` co ON ca.Column = co.ID
                                WHERE ca.ID <> :card_id AND ca.Title = :card_title AND 
                                co.Board = :board_id", 
                        array("card_id"=>($this->card_id === null?"0":$this->card_id), "card_title"=>$title, "board_id"=>$this->get_board_id()));
        if ($query->rowCount() == 0) {
            return true;
        } else {
            return false;
        }
    }

    public function set_column($column) {
        $this->column = $column;
    } 

    public function set_title($new_title) {
        $this->title = $new_title;
    } 

    public function set_position($new_position) {
        $this->position = $new_position;
    } 

    public function set_body($new_body) {
        $this->body = $new_body;
    } 

    public function set_due_date($new_due_date) {
        $this->due_date = $new_due_date;
    } 

    private function is_past_due_date($due_date) {
        if($due_date == null) return false;
        return strtotime($due_date) < time();
    }

    private function is_past_created_date($due_date) {
        if($due_date == null) return false;
        return $due_date < $this->created_at;
    }

    public function past_due_date() {
        return $this->is_past_due_date($this->due_date);
    }

    private function past_created_date() {
        return $this->is_past_created_date($this->due_date);
    }

    public static function get_last_card_position_in_column($column_id) {
        $query = self::execute("SELECT MAX(Position) pos FROM card ca WHERE ca.Column = :column_id", 
        array("column_id"=>$column_id));
        $data = $query->fetch();
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data['pos'];
        }
    }

    public function delete() {
        if($this->card_id !== NULL) {
            self::remove_all_participants_in_db($this->get_card_id());
            self::execute('DELETE FROM comment WHERE card = :card_id; DELETE FROM card WHERE ID = :card_id;', array('card_id' => $this->card_id));
            return $this;
        }
        return false;
    }

    public function change_position($dif){
        $errors = array();
        $cards = self::get_cards($this->column);
        $i =0;
        $find = false;
        $num = -1;
        while($i< count($cards) && !$find) {
            if($cards[$i]->card_id === $this->card_id){
                $num = $i;
                $find = true;
            }
            ++$i;
        }
        $new_pos = $num + $dif;
        if($new_pos < 0 || $new_pos >= count($cards)){
            $errors[] = "You cannot move this card there!";
        }
        if(empty($errors)){
            $card_to_exchange = $cards[$new_pos];
            $pos_temp = $this->position;
            $this->position = $card_to_exchange->position;
            $card_to_exchange->position = $pos_temp;
            self::update();
            $card_to_exchange->update();
        }
        return $errors;
    }

    public function change_column($dif){
        $errors = array();
        $this_board = Board::get_board($this->get_board_id());
        $columns = Column::get_columns($this_board);
        $i =0;
        $find = false;
        $num = -1;
        while($i< count($columns) && !$find) {
            if($columns[$i]->get_column_id() === $this->get_column_id()){
                $num = $i;
                $find = true;
            }
            ++$i;
        }
        $new_pos = $num + $dif;
        if($new_pos < 0 || $new_pos >= count($columns)){
            $errors[] = "You cannot move this card there!";
        }
        if(empty($errors)){
            $column_to_exchange = $columns[$new_pos];
            $new_position = self::get_last_card_position_in_column($column_to_exchange->get_column_id()) +1;
            $this->column = $column_to_exchange;
            $this->position = $new_position;
            self::update();
        }
        return $errors;
    }

    public function add_participant($user_id) {
        self::execute('INSERT INTO `participate` (Card, Participant) VALUES (:card_id, :user_id)', array('user_id' => $user_id, 'card_id' => $this->card_id));
    }

    public function remove_participant($user_id) {
        self::execute('DELETE FROM `participate` WHERE Card = :card_id and Participant = :user_id', array('user_id' => $user_id, 'card_id' => $this->card_id));
    }

    public function get_non_participants(){

        $query = self::execute(
            "SELECT Collaborator
            FROM collaborate
            WHERE Board = :board_id AND Collaborator NOT IN 
            (SELECT Participant FROM `participate` WHERE Card = :card_id)
            UNION
            SELECT Owner as Collaborator
            FROM board
            WHERE ID = :board_id AND Owner NOT IN 
            (SELECT Participant FROM `participate` WHERE Card = :card_id)"
            , array("board_id" => $this->get_board_id(), "card_id" => $this->get_card_id())
        );
        $data = $query->fetchAll();
        $non_participants = array();
        foreach ($data as $row) {
            $non_participants[] = User::get_user_by_id($row['Collaborator']);
        }
        return $non_participants;
    }

    public static function remove_all_participants_in_db($card_id){
        self::execute('DELETE FROM participate WHERE Card = :card', array(
            'card' => $card_id));
    }
}