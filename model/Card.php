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
   
    //supprimer la si l'initiateur en a le droit
    //renvoie la carte si ok. false sinon.
    public function delete() {
        self::execute('DELETE FROM card WHERE ID = :id', array('id' => $this->card_id));
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

    public function get_board_title() {
        return $this->column->get_board_title();
    }

    public function get_board() {
        $query = self::execute("SELECT * FROM user u INNER JOIN card c ON c.Author = u.ID where c.ID = :id", array("id" => $this->card_id));
        $data = $query->fetch(); 
        $cards = [];
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return $data['FullName'];
        }
    }

    public function get_duration_since_creation() {
        return $this->get_duration_since_date($this->created_at);
    }

    public function get_duration_since_last_edit() {     
        if($this->last_modified !== null){
            return "Modified ".$this->get_duration_since_date($this->last_modified);
        }
        return "Never modified.";
    }

    public static function get_duration_since_date($date) {
        $date = new DateTime($date);
        $now = new DateTime("now");
        $interval = $date->diff($now);
        $text_duration = "";
        $nb = 0; //afficher que les 2 valeurs les plus élevées si elles sont plus grandes que 0
        if($interval->y>0){
            $text_duration.=$interval->y.($interval->y>1?" years":" year");
            $nb += 1;
        }
        if($interval->m>0){
            $text_duration .= $interval->m.($interval->m>1?" months":" month");
            $nb += 2;
        }
        if($interval->d>0){
            if($nb ===0)
                $text_duration .= $interval->d.($interval->d>1?" days":" day");
            else if($nb ===2)
                $text_duration .= " and ".$interval->d.($interval->d>1?" days":" day");
            $nb += 4;
        }
        if($interval->h>0){
            if($nb ===0)
                $text_duration .= $interval->h.($interval->h>1?" hours":" hour");
            else if($nb ===4)
                $text_duration .= " and ".$interval->h.($interval->h>1?" hours":" hour");
            $nb += 8;
        }
        if($interval->i>0){
            if($nb ===0)
                $text_duration .= $interval->i.($interval->i>1?" minutes":" minute");
            else if($nb ===8)
                $text_duration .= " and ".$interval->i.($interval->i>1?" minutes":" minute");
            $nb += 20;
        }
        if($nb ===0)
            $text_duration .= "less than a minute";
        else
            $text_duration .= " ago.";

        return $text_duration;
    }


}
