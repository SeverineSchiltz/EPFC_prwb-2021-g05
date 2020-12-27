<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';

class ControllerUser extends Controller {

    //si l'utilisateur est connecté, redirige vers sa page principale.
    //sinon, produit la vue d'accueil.
    public function index() {
        $this->redirect("board", "index");
    }
    
    //gestion de la connexion d'un utilisateur
    public function login() {
        $mail = '';
        $password = '';
        $errors = [];
        if (isset($_POST['mail']) && isset($_POST['password'])) { //note : pourraient contenir des chaînes vides
            $mail = $_POST['mail'];
            $password = $_POST['password'];

            $errors = User::validate_login($mail, $password);
            if (empty($errors)) {
                $this->log_user(User::get_user_by_mail($mail));
            }
        }
        (new View("signin"))->show(array("mail" => $mail, "password" => $password, "errors" => $errors));
    }

    //gestion de l'inscription d'un utilisateur
    public function signup() {
        $mail = '';
        $name = '';
        $password = '';
        $password_confirm = '';
        $errors = [];

        if (isset($_POST['mail']) && isset($_POST['name']) && isset($_POST['password']) && isset($_POST['password_confirm'])) {
            $mail = trim($_POST['mail']);
            $name = trim($_POST['name']);
            $password = $_POST['password'];
            $password_confirm = $_POST['password_confirm'];

            $user = new User($mail, Tools::my_hash($password), $name);
            $errors = $user->validate(); //valide le mail et le full name
            $errors = array_merge($errors, User::validate_passwords($password, $password_confirm));
            if (count($errors) == 0) { 
                $user->update(); 
                $this->log_user(User::get_user_by_mail($mail)); //va rechercher l'utilisateur avec l'id
            }
        }
        (new View("signup"))->show(array("mail" => $mail, "name" => $name, "password" => $password, 
                                         "password_confirm" => $password_confirm, "errors" => $errors));
    }
}
