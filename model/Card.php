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
    private $last_modified;
    private $cards;

    public function __construct($card_id, $column, $position, $author, $title, $body, $created_at, $last_modified = NULL) {
        $this->card_id = $card_id;
        $this->column =  $column;
        $this->position = $position;
        $this->author = $author;
        $this->title = $title;
        $this->body = $body;
        $this->created_at = $created_at;
        $this->last_modified = $last_modified;
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
            $cards[] = new Card($row['ID'], $column, $row['Position'], User::get_user_by_id($row['Author']), $row['Title'], $row['Body'], $row['CreatedAt'], $row['ModifiedAt']);
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
            return new Card($data['ID'], Column::get_column($data['Column']), $data['Position'], User::get_user_by_id($data['Author']), $data['Title'], $data['Body'], $data['CreatedAt'], $data['ModifiedAt']);
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
        $errors = $this->validate_title($this->title);
        if(empty($errors)){
            self::update($this);
            return true;
        }
        return false;
    }

    public static function update($card) {
        self::execute('UPDATE card SET Title = :title, Body = :body, Position = :position,  `Column`= :column, ModifiedAt = current_timestamp() WHERE ID = :card_id', array(
            'card_id' => $card->card_id,
            'title' => $card->title,
            'body' => $card->body,
            'position' => $card->position,
            'column' => $card->get_column_id()
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

    public function set_title($new_title) {
        $this->title = $new_title;
    } 

    public function set_body($new_body) {
        $this->body = $new_body;
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
            self::update($this);
            self::update($card_to_exchange);
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
            self::update($this);
        }
        return $errors;
    }

}