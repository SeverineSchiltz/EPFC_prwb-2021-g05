
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
            /*$acces_board = array();
            $acces_board[] = Board::get_my_boards($user);
            $acces_board[] = Board::get_other_shared_boards($user);
            (new View("calendar"))->show(array("user" => $user, "acces_board" => $acces_board));*/
            //(new View("calendar"))->show(array("user" => $user, "personal_boards" => Board::get_my_boards($user), "other_shared_boards" => Board::get_other_shared_boards($user), "other_not_shared_boards" => Board::get_other_not_shared_boards($user)));
            (new View("calendar"))->show(array("user" => $user, "boards_json" => $calendar->get_boards_with_cards_as_json()));
        } else {
            (new View("home"))->show();
        }
    }

}

