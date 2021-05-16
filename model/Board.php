<?php

require_once "framework/Model.php";
require_once "User.php";
require_once "MyTools.php";

class Board extends Model {

    private $board_id;
    private $author;
    private $title;
    private $created_at;
    private $last_modified;
    private $collaborators;

    public function __construct($board_id, $author, $title, $created_at, $last_modified = NULL) {
        $this->board_id = $board_id;
        $this->author = $author;
        $this->title = $title;
        $this->created_at = $created_at;
        $this->last_modified = $last_modified;
        $this->collaborators = $this->get_collaborators_in_db();
    }

    public function get_title(){
        return $this->title;
    }

    public function get_board_id(){
        return $this->board_id;
    }

    public function set_board_id($id){
        $this->board_id = $id;
    }

    public function get_author_name() {
        return $this->author->get_full_name();
    }

    public function get_author() {
        return $this->author;
    }

    public function get_author_id() {
        return $this->author->get_user_id();
    }

    public function get_collaborators(){
        return $this->collaborators;
    }

    public function get_collaborators_in_db(){
        $query = self::execute("SELECT u.*
                                FROM collaborate c
                                INNER JOIN user u ON u.ID = c.Collaborator
                                WHERE c.Board = :board_id", array("board_id" => $this->get_board_id()));
        $data = $query->fetchAll();
        $collaborators = array();
        foreach ($data as $row) {
            $collaborators[] = new User($row["Mail"], $row["Password"], $row['FullName'], $row['Role'], $row["ID"]);
        }
        return $collaborators;
    }

    public function get_non_collaborators(){

        $query = self::execute("SELECT u.*
                                FROM user u
                                WHERE u.ID <> :owner_id AND u.ID NOT IN 
                                (SELECT c.Collaborator FROM collaborate c WHERE c.Board = :board_id)"
                                , array("board_id" => $this->get_board_id(), "owner_id" => $this->get_author_id()));
        $data = $query->fetchAll();
        $non_collaborators = array();
        foreach ($data as $row) {
            $non_collaborators[] = new User($row["Mail"], $row["Password"], $row['FullName'], $row['Role'], $row["ID"]);
        }
        return $non_collaborators;
    }
    
    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public function validate(){
        $errors = array();
        if(!(isset($this->author) && is_a($this->author,"User") && User::get_user_by_mail($this->author->get_mail()))){
            $errors[] = "Incorrect author";
        }
        if(!(isset($this->title) && is_string($this->title) && strlen($this->title) > 0)){
            $errors[] = "Title must be filled";
        }
        return $errors;
    }

    public function validate_board_name($title){
        $errors = array();
        if(!(isset($title) && is_string($title) && strlen($title) > 2 && strlen($title) < 129)){
            $errors[] = "Board title length must be between 3 and 128 characters";
        }
        if($title != $this->title)
            $errors = array_merge($errors, $this->validate_unicity($title));
        return $errors;
    }

    public static function get_my_boards($user) {
        $query = self::execute("select b.*, u.Mail from board b join user u on b.Owner = u.ID where u.Mail = :mail order by b.ModifiedAt, b.CreatedAt DESC", array("mail" => $user->get_mail()));
        $data = $query->fetchAll();
        $boards = [];
        foreach ($data as $row) {
            $board = new Board($row['ID'], User::get_user_by_mail($row['Mail']), $row['Title'], $row['CreatedAt'], $row['ModifiedAt']);
            $boards[] = $board;
        }
        return $boards;
    }

    public static function get_other_boards($user) {
        $query = self::execute("select b.*, u.Mail from board b join user u on b.Owner = u.ID where u.Mail <> :mail order by b.ModifiedAt, b.CreatedAt DESC", array("mail" => $user->get_mail()));
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

    public static function get_other_shared_boards($user) {
        $query = self::execute(
            "SELECT DISTINCT b.*
            FROM board b
            INNER JOIN collaborate c ON c.Board = b.ID AND c.Collaborator = :userID
            WHERE b.Owner <> :userID
            ORDER BY b.ModifiedAt, b.CreatedAt DESC",
            array(":userID" => $user->get_user_id()));
            $data = $query->fetchAll();
            $boards = [];
            foreach ($data as $row) {
                $board = new Board($row['ID'], User::get_user_by_id($row['Owner']), $row['Title'], $row['CreatedAt'], $row['ModifiedAt']);
                $boards[] = $board;
            }
            return $boards;
    }

    public static function get_other_not_shared_boards($user) {
        $query = self::execute(
            "SELECT DISTINCT b.*
            FROM board b
            WHERE b.Owner <> :userID AND 
            b.ID NOT IN (SELECT Board FROM collaborate WHERE Collaborator =:userID)
            ORDER BY b.ModifiedAt, b.CreatedAt DESC",
        array(":userID" => $user->get_user_id()));
        $data = $query->fetchAll();
        $boards = [];
        foreach ($data as $row) {
            $board = new Board($row['ID'], User::get_user_by_id($row['Owner']), $row['Title'], $row['CreatedAt'], $row['ModifiedAt']);
            $boards[] = $board;
        }
        return $boards;
    }

    public function has_columns() {
        return $this->get_nb_columns()!=0;
    }
   
    //supprimer le tableau
    
    public function delete($initiator) {
        if ($this->author == $initiator || $initiator->is_admin()) {
            self::remove_all_columns_in_board($this->get_board_id());
            self::remove_all_collaborators_in_db($this->get_board_id());
            self::execute('DELETE FROM board WHERE ID = :id', array('id' => $this->get_board_id()));
            return true;
        }
        return false;
    }

    public function update() {
        if($this->board_id == NULL) {
            $errors = $this->validate();
            if(empty($errors)){
                self::execute('INSERT INTO board (Owner, Title) VALUES ((select ID from user where Mail = :mail),:title)', array(
                    'mail' => $this->author->get_mail(),
                    'title' => $this->title
                ));
                $board = self::get_board(self::lastInsertId());
                $this->board_id = $board->get_board_id();
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

    public function set_title($new_title) {
        $this->title = $new_title;
    } 

    private function validate_unicity($title) {
        $errors = [];
        $board = self::get_board_by_title($title);
        if ($board) {
            $errors[] = "The title of the board must be unique";
        } 
        return $errors;
    }

    public static function get_board_by_title($title) {
        $query = self::execute("SELECT * FROM board where Title = :title", array("title"=>$title));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new Board($data['ID'], null, $data['Title'], null);
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
        $query = self::execute("select Position as last_position from `column` where Board = :id order by Position DESC limit 1", array("id" => $this->board_id));
        if ($query->rowCount() == 0) {
            return 0;
        } else {
            $row = $query->fetch();
            return $row['last_position']+1;
        }
    }

    public function get_menu_title() {
        return "Board \"".$this->title."\"";
    }

    public function get_duration_since_creation() {
        return MyTools::get_duration_since_date($this->created_at);
    }

    public function get_last_modification() {
        $last_modified = $this->last_modified;
        foreach($this->get_columns() as $column) 
            $last_modified = $last_modified > $column->get_last_modification() ? $last_modified : $column->get_last_modification();
        return $last_modified;
    }

    public function get_duration_since_last_edit() {        
        return MyTools::get_duration_since_date($this->get_last_modification());
    }

    public function validate_board_new_collaborator($new_collaborator_id){
        $errors = [];
        if(!is_numeric($new_collaborator_id)){
            $errors[] = "Not a user!";
        }
        return $errors;
    }

    public function add_new_collaborator($new_collaborator_id){
        $this->collaborators[] =  User::get_user_by_id($new_collaborator_id);
        self::add_new_collaborator_in_db($this->get_board_id(), $new_collaborator_id);
    }

    public static function add_new_collaborator_in_db($board_id, $new_collaborator_id){
        self::execute('INSERT INTO collaborate (Board, Collaborator) VALUES (:board, :collaborator)', array(
            'board' => $board_id,
            'collaborator' => $new_collaborator_id
        ));
    }

    public function remove_collaborator($collaborator_id){
        $user = User::get_user_by_id($collaborator_id);
        //ne fonctionne pas car pas de redéfinition de la méthode Equals
        /*
        if(($key = array_search($user, $this->collaborators, TRUE)) !== FALSE) {
            unset($this->collaborators[$key]);
            self::remove_collaborator_in_db($this->get_board_id(), $collaborator_id);
        }
        */
        unset($this->collaborators[$user]);
        foreach($this->get_columns() as $column)
                $column->remove_participant($collaborator_id);
        self::remove_collaborator_in_db($this->get_board_id(), $collaborator_id);
    }

    public static function remove_collaborator_in_db($board_id, $collaborator_id){
        self::execute('DELETE FROM collaborate WHERE board = :board AND collaborator = :collaborator', array(
            'board' => $board_id,
            'collaborator' => $collaborator_id
        ));
    }

    public function remove_all_collaborators_in_db(){
        self::execute('DELETE FROM collaborate WHERE board = :board', array(
            'board' => $this->get_board_id()));
    }

    public function remove_all_columns_in_board(){
        $this->remove_all_cards_in_board();
        self::execute('DELETE FROM `column` WHERE board = :board', array(
            'board' => $this->get_board_id()));
    }

    public function remove_all_cards_in_board(){
        self::execute('DELETE FROM card WHERE ID IN (
            SELECT ca.ID FROM Card ca 
            INNER JOIN `column` co ON ca.Column = co.Id 
            WHERE co.board = :board)', array('board' => $this->get_board_id()));
    }

 }