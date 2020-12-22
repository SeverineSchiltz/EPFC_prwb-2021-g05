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
        return Card::get_cards($this);
    }

    public function get_last_position() {
        $query = self::execute("select Position from `column` where Board = :id order by Position DESC limit 1", array("id" => $this->board->board_id)); 
        $row = $query->fetch();
        return $row['Position'];
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

    public function move_right() {
        $errors = array();
        $right_column = $this->get_column_board_position($this->board, $this->position+1); //the column to the right, the one we are swapping with
        if($right_column) {
            $right_column->position--;
            $this->position++;
            $right_column->update();
            $this->update();
        } else {
            $errors[] = "Couldn't find a column to the right";
            return $errors;
        }
    }

    public function move_left() {
        $errors = array();
        $left_column = $this->get_column_board_position($this->board, $this->position-1); //the column to the left, the one we are swapping with
        if($left_column) {
            $left_column->position++;
            $this->position--;
            $left_column->update();
            $this->update();
        } else {
            $errors[] = "Couldn't find a column to the left";
            return $errors;
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
}
