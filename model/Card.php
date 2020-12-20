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
    public function delete($initiator) {
        if ($this->author == $initiator) {
            self::execute('DELETE FROM card WHERE card_id = :id', array('id' => $this->card_id));
            return $this;
        }
        return false;
    }
}
