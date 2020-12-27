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

    public function __construct($board, $position, $title, $created_at = NULL, $column_id = NULL, $last_modified = NULL) {
        $this->board = $board;
        $this->position = $position;
        $this->title = $title;
        $this->created_at = $created_at;
        $this->column_id = $column_id;
        $this->last_modified = $last_modified;
    }

    public function get_title(){
        return $this->title;
    }

    public function get_column_id(){
        return $this->column_id;
    }

    public function get_board_id(){
        return $this->board->get_board_id();
    }

    public static function get_columns($board) {
        $query = self::execute("select * from `column` where Board = :board_id order by Position ASC", array("board_id" => $board->board_id));
        $data = $query->fetchAll();
        $columns = [];
        foreach ($data as $row) {
            $columns[] = new Column($board, $row['Position'], $row['Title'], $row['CreatedAt'], $row['ID'], $row['ModifiedAt']);
        }
        return $columns;
    }

    public static function get_column($column_id) {
        $query = self::execute("select * from `column` where ID = :id", array("id" => $column_id));
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Column(Board::get_board($row['Board']), $row['Position'], $row['Title'], $row['CreatedAt'], $row['ID'], $row['ModifiedAt']);
        }
    }

    public static function get_column_board_position($board, $position) {
        $query = self::execute("select * from `column` where Board = :board and Position = :position", array("board" => $board->board_id, "position" => $position));
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Column(Board::get_board($row['Board']), $row['Position'], $row['Title'], $row['CreatedAt'], $row['ID'], $row['ModifiedAt']);
        }
    }

    //supprimer la colonne
    public function delete() {
        foreach($this->get_cards() as $card)
            $card->delete();
        self::execute('DELETE FROM `column` WHERE ID = :id', array('id' => $this->column_id));
        return $this;
    }

    public function get_cards() {
        return Card::get_cards($this);
    }

    public function get_nb_cards() {
        $query = self::execute("select count(*) as nb_cards from card where `Column` = :id", array("id" => $this->column_id));
        if ($query->rowCount() == 0) {
            return 0;
        } else {
            $row = $query->fetch();
            return $row['nb_cards'];
        }
    }

    public function has_cards() {
        return $this->get_nb_cards()!=0;
    }

    public function get_last_position() {
        $query = self::execute("select Position from `column` where Board = :id order by Position DESC limit 1", array("id" => $this->board->board_id)); 
        $row = $query->fetch();
        return $row['Position'];
    }

    public function get_first_position() {
        $query = self::execute("select Position from `column` where Board = :id order by Position ASC limit 1", array("id" => $this->board->board_id)); 
        $row = $query->fetch();
        return $row['Position'];
    }

    public function get_last_modification() {
        $last_modified = $this->last_modified;
        foreach($this->get_cards() as $card) 
            $last_modified = $last_modified > $card->get_last_modification() ? $last_modified : $card->get_last_modification();
        return $last_modified;
    }

    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public function validate(){
        $errors = array();
        if(!(isset($this->board) && is_a($this->board,"Board") && Board::get_board($this->board->board_id))){
            $errors[] = "Incorrect board";
        }
        if(!(isset($this->title) && is_string($this->title) && strlen($this->title) >= 3)){
            $errors[] = "Title must be at least 3 characters long";
        }
        if(!(isset($this->position) && is_numeric($this->position))){
            $errors[] = "Position must be numeric";
        }
        return $errors;
    }

    public function move($direction) {
        $errors = array();
        if($direction === 'right')
            $other_column = $this->get_column_right($this->board, $this->position); //the column to the right, the one we are swapping with
        else if($direction === 'left')
            $other_column = $this->get_column_left($this->board, $this->position); //the column to the left, the one we are swapping with
        else {
            $errors[] = "No direction given";
            return $errors;
        }
        if($other_column) {
            $temp = $other_column->position;
            $other_column->position = $this->position;
            $this->position = $temp;
            $other_column->update();
            $this->update();
        } else {
            $errors[] = "Couldn't find a column to the ".$direction;
            return $errors;
        }
    }

    private function get_column_left($board, $position) {
        $query = self::execute("select * from `column` where Board = :board_id and Position < :position order by Position DESC limit 1", array("board_id" => $board->board_id, "position" => $position));
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Column(Board::get_board($row['Board']), $row['Position'], $row['Title'], $row['CreatedAt'], $row['ID'], $row['ModifiedAt']);
        }
    }

    private function get_column_right($board, $position) {
        $query = self::execute("select * from `column` where Board = :board_id and Position > :position order by Position ASC limit 1", array("board_id" => $board->board_id, "position" => $position));
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Column(Board::get_board($row['Board']), $row['Position'], $row['Title'], $row['CreatedAt'], $row['ID'], $row['ModifiedAt']);
        }
    }

    public function update() {
        if($this->column_id == NULL) {
            $errors = $this->validate();
            if(empty($errors)){
                self::execute('INSERT INTO `column` (Board, Position, Title) VALUES (:board,:position,:title)', array(
                    'board' => $this->board->board_id,
                    'position' => $this->position,
                    'title' => $this->title
                ));
                $column = self::get_column(self::lastInsertId());
                $this->column_id = $column->column_id;
                $this->created_at = $column->created_at;
                return $this;
            } else {
                return $errors; //un tableau d'erreurs
            }
        } else {
            $errors = $this->validate();
            if(empty($errors)){
                self::execute('UPDATE `column` SET Position = :position, Title = :title, ModifiedAt = current_timestamp() WHERE ID = :id', array(
                    'id' => $this->column_id,
                    'position' => $this->position,
                    'title' => $this->title
                ));
                $column = self::get_column($this->column_id);
                $this->last_modified = $column->last_modified;
                return $this;
            } else {
                return $errors; //un tableau d'erreurs
            }
        }
    }

    public function get_menu_title() {
        return "Column \"".$this->title."\"";
    }

    public function get_duration_since_creation() {
        return $this->get_duration_since_date($this->created_at);
    }

    public function get_duration_since_last_edit() {        
        return $this->get_duration_since_date($this->last_modified);
    }

    public function get_duration_since_date($date) {
        $date = new DateTime($date);
        $now = new DateTime("now");
        $interval = $date->diff($now);

        if($interval->y>0)
            return $interval->y.($interval->y>1?" years":" year");
        else if($interval->m>0)
            return $interval->m.($interval->m>1?" months":" month");
        else if($interval->d>0)
            return $interval->d.($interval->d>1?" days":" day");
        else if($interval->h>0)
            return $interval->h.($interval->h>1?" hours":" hour");
        else if($interval->i>0)
            return $interval->i.($interval->i>1?" minutes":" minute");
        else
            return " less than a minute";
    }

    public function get_board_title() {
        return $this->board->get_title();
    }

}
