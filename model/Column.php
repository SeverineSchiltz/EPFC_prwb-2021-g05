<?php

require_once "framework/Model.php";
require_once "Board.php";
require_once "Card.php";

class Column extends Model {

    public $column_id;
    public $board;
    public $position;
    public $title;
    public $created_at;
    public $last_modified;

    public function __construct($column_id, $board, $position, $title, $created_at, $last_modified = NULL) {
        $this->column_id = $column_id;
        $this->board = $board;
        $this->position = $position;
        $this->title = $title;
        $this->created_at = $created_at;
        $this->last_modified = $last_modified;
    }

    public static function get_columns($board) {
        $query = self::execute("select * from `column` where Board = :board_id order by Position ASC", array("board_id" => $board->board_id));
        $data = $query->fetchAll();
        $columns = [];
        foreach ($data as $row) {
            $columns[] = new Column($row['ID'], $board, $row['Position'], $row['Title'], $row['CreatedAt'], $row['ModifiedAt']);
        }
        return $columns;
    }

    //supprimer la colonne si l'initiateur en a le droit
    //renvoie la colonne si ok. false sinon.
    public function delete($initiator) {
        if ($this->author == $initiator) {
            self::execute('DELETE FROM `column` WHERE column_id = :id', array('id' => $this->column_id));
            return $this;
        }
        return false;
    }

    public function get_cards() {
        $query = self::execute("select * from card where `Column` = :id order by Position ASC", array("id" => $this->column_id));
        $data = $query->fetchAll();
        $cards = [];
        foreach ($data as $row) {
            $cards[] = new Card($row['ID'], $this, $row['Position'], $row['Author'], $row['Title'], $row['Body'], $row['CreatedAt'], $row['ModifiedAt']);
        }
        return $cards;
    }

    public function get_last_position() {
        $query = self::execute("select Position from `column` where Board = :id order by Position DESC limit 1", array("id" => $this->board->board_id)); 
        $row = $query->fetch();
        return $row['Position'];
    }
}
