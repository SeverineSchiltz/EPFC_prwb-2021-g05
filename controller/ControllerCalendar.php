
<?php

require_once 'model/User.php';
require_once 'framework/View.php';
require_once 'framework/Controller.php';
require_once 'model/Board.php';
require_once 'model/Calendar.php';

class ControllerCalendar extends Controller {

    //si l'utilisateur est connecté, redirige vers sa page principale.
    //sinon, produit la vue d'accueil.
    public function index() {
        $this->redirect("board", "index");
    }

    public function calendar() {
        $user = $this->get_user_or_redirect();
        $calendar = new Calendar($user);
        if ($this->user_logged()) {
            (new View("calendar"))->show(array("user" => $user, "boards" => $calendar->get_boards_with_events()));
        } else {
            (new View("home"))->show();
        }
    }

}

