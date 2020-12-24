<?php

require_once "framework/Model.php";
require_once "Board.php";

class Card extends Model {

    public $card_id;
    public $column;
    public $position;
    public $author;
    public $title;
    public $body;
    public $created_at;
    public $last_modified;
    public $cards;

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
   
    //supprimer la si l'initiateur en a le droit
    //renvoie la carte si ok. false sinon.
    public function delete() {
        self::execute('DELETE FROM card WHERE ID = :id', array('id' => $this->card_id));
    }

    public function get_last_position() {
        $query = self::execute("select Position from card where `Column` = :id order by Position DESC limit 1", array("id" => $this->column->column_id)); 
        $row = $query->fetch();
        return $row['Position'];
    }   

    public function get_first_position() {
        $query = self::execute("select Position from card where `Column` = :id order by Position ASC limit 1", array("id" => $this->column->column_id)); 
        $row = $query->fetch();
        return $row['Position'];
    }     

    public static function get_cards($column) {
        $query = self::execute("select * from card where `Column` = :id order by Position ASC", array("id" => $column->column_id));
        $data = $query->fetchAll();
        $cards = [];
        foreach ($data as $row) {
            $cards[] = new Card($row['ID'], $column, $row['Position'], $row['Author'], $row['Title'], $row['Body'], $row['CreatedAt'], $row['ModifiedAt']);
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
            return new Card($data['ID'], $data['Column'], $data['Position'], $data['Author'], $data['Title'], $data['Body'], $data['CreatedAt'], $data['ModifiedAt']);
        }
    }

    public function get_author_name() {
        $query = self::execute("SELECT * FROM user u INNER JOIN card c ON c.Author = u.ID where c.ID = :id", array("id" => $this->card_id));
        $data = $query->fetch(); 
        $cards = [];
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data['FullName'];
        }
    }

}
