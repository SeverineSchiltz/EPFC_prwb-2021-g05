
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
            (new View("calendar"))->show(array("user" => $user, "boards" => $calendar->get_boards()));
        } else {
            (new View("home"))->show();
        }
    }

    public function get_events_service() {
        $res = "false";
        if(isset($_POST["boards"]) && $_POST["boards"]){
            $boards_js = json_decode($_POST["boards"]);
            // echo json_encode($board_ids);
            $user = $this->get_user_or_false();
            if($user) {
                $calendar = new Calendar($user);
                $res = $calendar->get_events($boards_js);
            }
        }
        echo $res;
    }
}

