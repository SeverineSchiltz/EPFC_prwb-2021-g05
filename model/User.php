<?php

require_once "framework/Model.php";

class User extends Model {

    public $mail;
    public $hashed_password;

    public function __construct($mail, $hashed_password) {
        $this->mail = $mail;
        $this->hashed_password = $hashed_password;
    }

    public function update() {
        if(self::get_user_by_mail($this->mail))
            self::execute("UPDATE User SET password=:password WHERE mail=:mail ", 
                          array("mail"=>$this->mail, "password"=>$this->hashed_password));
        else
            self::execute("INSERT INTO User(mail,password) VALUES(:mail,:password)", 
                          array("mail"=>$this->mail, "password"=>$this->hashed_password));
        return $this;
    }

    public static function get_user_by_mail($mail) {
        $query = self::execute("SELECT * FROM User where mail = :mail", array("mail"=>$mail));
        $data = $query->fetch(); // un seul résultat au maximum
        if ($query->rowCount() == 0) {
            return false;
        } else {
            return new User($data["Mail"], $data["Password"]);
        }
    }

    public static function get_users() {
        $query = self::execute("SELECT * FROM User", array());
        $data = $query->fetchAll();
        $results = [];
        foreach ($data as $row) {
            $results[] = new User($row["Mail"], $row["Password"]);
        }
        return $results;
    }

    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    //ne s'occupe que de la validation "métier" des champs obligatoires (le mail)
    //les autres champs (mot de passe, description et image) sont gérés par d'autres
    //méthodes.
    public function validate(){
        $errors = array();
        if (!(isset($this->mail) && is_string($this->mail) && strlen($this->mail) > 0)) {
            $errors[] = "mail is required.";
        } if (!(isset($this->mail) && is_string($this->mail) && strlen($this->mail) >= 3 && strlen($this->mail) <= 16)) {
            $errors[] = "mail length must be between 3 and 16.";
        } if (!(isset($this->mail) && is_string($this->mail) && preg_match("/^[a-zA-Z][a-zA-Z0-9]*$/", $this->mail))) {
            $errors[] = "mail must start by a letter and must contain only letters and numbers.";
        }
        return $errors;
    }
    
    private static function validate_password($password){
        $errors = [];
        if (strlen($password) < 8 || strlen($password) > 16) {
            $errors[] = "Password length must be between 8 and 16.";
        } if (!((preg_match("/[A-Z]/", $password)) && preg_match("/\d/", $password) && preg_match("/['\";:,.\/?\\-]/", $password))) {
            $errors[] = "Password must contain one uppercase letter, one number and one punctuation mark.";
        }
        return $errors;
    }
    
    public static function validate_passwords($password, $password_confirm){
        $errors = User::validate_password($password);
        if ($password != $password_confirm) {
            $errors[] = "You have to enter twice the same password.";
        }
        return $errors;
    }
    
    public static function validate_unicity($mail){
        $errors = [];
        $user = self::get_user_by_mail($mail);
        if ($user) {
            $errors[] = "This user already exists.";
        } 
        return $errors;
    }

    //indique si un mot de passe correspond à son hash
    private static function check_password($clear_password, $hash) {
        return $hash === Tools::my_hash($clear_password);
    }
    
    //renvoie un tableau d'erreur(s) 
    //le tableau est vide s'il n'y a pas d'erreur.
    public static function validate_login($mail, $password) {
        $errors = [];
        $user = User::get_user_by_mail($mail);
        if ($user) {
            if (!self::check_password($password, $user->hashed_password)) {
                $errors[] = "Wrong password. Please try again.";
            }
        } else {
            $errors[] = "Can't find a user with the mail '$mail'. Please sign up.";
        }
        return $errors;
    }
}
