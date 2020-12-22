<?php

require_once "framework/Model.php";
require_once "User.php";

class Board extends Model {

    public $board_id;
    public $author;
    public $title;
    public $created_at;
    public $last_modified;

    public function __construct($board_id, $author, $title, $created_at, $last_modified = NULL) {
        $this->board_id = $board_id;
        $this->author = $author;
        $this->title = $title;
        $this->created_at = $created_at;
        $this->last_modified = $last_modified;
    }
    
    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public function validate(){
        $errors = array();
        if(!(isset($this->author) && is_a($this->author,"User") && User::get_user_by_mail($this->author->mail))){
            $errors[] = "Incorrect author";
        }
        if(!(isset($this->title) && is_string($this->title) && strlen($this->title) > 0)){
            $errors[] = "Title must be filled";
        }
        return $errors;
    }

    public static function get_boards($user) {
        $query = self::execute("select b.*, u.Mail from board b join user u on b.Owner = u.ID where u.Mail = :mail order by b.ModifiedAt, b.CreatedAt DESC", array("mail" => $user->mail));
        $data = $query->fetchAll();
        $boards = [];
        foreach ($data as $row) {
            $board = new Board($row['ID'], User::get_user_by_mail($row['Mail']), $row['Title'], $row['CreatedAt'], $row['ModifiedAt']);
            $boards[] = $board;
        }
        return $boards;
    }

    public static function get_other_boards($user) {
        $query = self::execute("select b.*, u.Mail from board b join user u on b.Owner = u.ID where u.Mail <> :mail order by b.ModifiedAt, b.CreatedAt DESC", array("mail" => $user->mail));
        $data = $query->fetchAll();
        $boards = [];
        foreach ($data as $row) {
            $board = new Board($row['ID'], User::get_user_by_mail($row['Mail']), $row['Title'], $row['CreatedAt'], $row['ModifiedAt']);
            $boards[] = $board;
        }
        return $boards;
    }

    public static function get_board($board_id) {
        $query = self::execute("select b.*, u.Mail from board b join user u on b.Owner = u.ID where b.ID = :id", array("id" => $board_id));
        if ($query->rowCount() == 0) {
            return false;
        } else {
            $row = $query->fetch();
            return new Board($row['ID'], User::get_user_by_mail($row['Mail']), $row['Title'], $row['CreatedAt'], $row['ModifiedAt']);
        }
    }
   
    //supprimer le board si l'initiateur en a le droit
    //renvoie le board si ok. false sinon.
    public function delete($initiator) {
        if ($this->author == $initiator) {
            self::execute('DELETE FROM board WHERE board_id = :id', array('id' => $this->board_id));
            return $this;
        }
        return false;
    }

    public function update() {
        if($this->post_id == NULL) {
            $errors = $this->validate();
            if(empty($errors)){
                self::execute('INSERT INTO board (Owner, Title) VALUES ((select ID from user where Mail = :mail),:title)', array(
                    'mail' => $this->author->mail,
                    'title' => $this->title
                ));
                $board = self::get_board(self::lastInsertId());
                $this->board_id = $board->board_id;
                $this->created_at = $board->created_at;
                return $this;
            } else {
                return $errors; //un tableau d'erreurs
            }
        } else {
            $errors = $this->validate();
            if(empty($errors)){
                self::execute('UPDATE board SET Title = :title, ModifiedAt = current_timestamp() WHERE ID = :board_id', array(
                    'board_id' => $this->board_id,
                    'title' => $this->title
                ));
                $board = self::get_board($this->board_id);
                $this->last_modified = $board->lasted_modified;
                return $this;
            } else {
                return $errors; //un tableau d'erreurs
            }
        }
    }

    public function get_columns() {
        return Column::get_columns($this);
    }

    public function get_nb_columns() {
        $query = self::execute("select count(*) as nb_columns from `column` where Board = :id", array("id" => $this->board_id));
        if ($query->rowCount() == 0) {
            return 0;
        } else {
            $row = $query->fetch();
            return $row['nb_columns'];
        }
    }

    public function get_next_column_position() {
        return $this->get_nb_columns();
    }

    public function get_menu_title() {
        return "Board \"".$this->title."\"";
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
 }