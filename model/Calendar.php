<?php

    require_once "framework/Model.php";
    require_once "Board.php";
    require_once "User.php";

    class Calendar extends Model {

        private $user;
        private $boardColors = array("#1F77B4", "#AEC7E8", "#FF7F0E", "#FFBB78", "#2CA02C", "#98DF8A", "#D62728", "#FF9896", "#9467BD", "#C5B0D5", "#8C564B", "#C49C94", "#E377C2", "#F7B6D2", "#7F7F7F", "#C7C7C7", "#BCBD22", "#DBDB8D", "#17BECF", "#9EDAE5");

        public function __construct($user) {
            $this->user = $user;
        }

        private function get_random_color() {
            return $this->boardColors[array_rand($this->boardColors)];
        }

        public function get_boards(){
            $boards = [];
            foreach (Board::get_my_boards($this->user) as $myBoard)
            {
                $boards[] = $this->get_board_js($myBoard, "my_boards");
            }
            foreach (Board::get_other_shared_boards($this->user) as $otherBoard)
            {
                $boards[] = $this->get_board_js($otherBoard, "other_boards");
            }
            if($this->user->is_admin()){
                foreach (Board::get_other_not_shared_boards($this->user) as $not_shared_Board)
                {
                    $boards[] = $this->get_board_js($not_shared_Board, "not_shared_boards");
                }
            }
            return json_encode($boards);
        }

        private function get_board_js($board, $type){
            $board_js =  array(
                "id" => $board->get_board_id(),
                "color" => $this->get_random_color(),
                "title" => $board->get_title(),
                "type" => $type
            );
            return $board_js;
        }

        public function get_events($boards_js) {
            $events = [];
            foreach($boards_js as $bjs) {
                $board = Board::get_board($bjs->id);
                $cards = $board->get_cards();
                foreach($cards as $card) 
                    if($card->get_due_date() != null)
                        $events[] = array(
                            "id" => $card->get_card_id(),
                            "title" => $card->get_title(),
                            "start" => $card->get_due_date(),
                            "backgroundColor" => $bjs->color,
                            "className" => $card->past_due_date() ? "redBorder" : "blackBorder",
                            "textColor" => "white",
                            "description" => $card->get_body()
                        );
            }

            return json_encode($events);
        }
    }

?>

    