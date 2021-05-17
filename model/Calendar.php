<?php

    require_once "framework/Model.php";
    require_once "Board.php";
    require_once "User.php";

    class Calendar extends Model {

        private $user;
        private $boardColors = array( "#1F77B4", "#AEC7E8", "#FF7F0E", "#FFBB78", "#2CA02C", "#98DF8A", "#D62728", "#FF9896", "#9467BD", "#C5B0D5", "#8C564B", "#C49C94", "#E377C2", "#F7B6D2", "#7F7F7F", "#C7C7C7", "#BCBD22", "#DBDB8D", "#17BECF", "#9EDAE5");

        public function __construct($user) {
            $this->user = $user;
        }

        private function get_random_color() {
            return json_encode($this->boardColors[array_rand($this->boardColors)]);
        }

        public function get_boards_with_cards_as_json(){
            $strBoard = "";
            foreach (Board::get_my_boards($this->user) as $myBoard)
            {
                $strBoard .= $this->get_one_board_with_cards_as_json($myBoard, "my_boards").",";
            }
            foreach (Board::get_other_shared_boards($this->user) as $otherBoard)
            {
                $strBoard .= $this->get_one_board_with_cards_as_json($otherBoard, "other_boards").",";
            }
            if($this->user->is_admin()){
                foreach (Board::get_other_not_shared_boards($this->user) as $not_shared_Board)
                {
                    $strBoard .= $this->get_one_board_with_cards_as_json($not_shared_Board, "not_shared_boards").",";
                }
            }
            if($strBoard !== "")
                $strBoard = substr($strBoard,0,strlen($strBoard)-1);
            return "[$strBoard]";
        }

        public function get_one_board_with_cards_as_json($board, $type){
            $strBoard = "";
            $board_id = json_encode($board->get_board_id());
            $board_title = json_encode($board->get_title());
            $board_type = json_encode($type);
            $color = $this->get_random_color();
            $strCards = "";
            foreach ($board->get_columns() as $column)
            {
                foreach ($column->get_cards() as $card)
                {
                    if($card->get_due_date() != null){
                        $card_id = json_encode($card->get_card_id());
                        $card_title = json_encode($card->get_title());
                        $card_body = json_encode($card->get_body());
                        $card_due_date = json_encode($card->get_due_date());
                        $strCard = "{\"card_id\":$card_id,\"card_title\":$card_title,\"card_body\":$card_body,\"card_due_date\":$card_due_date},"; 
                        $strCards .= $strCard;
                    }
                }
            }
            if($strCards !== "")
                $strCards = substr($strCards,0,strlen($strCards)-1);
            $strCards = "[$strCards]";

            $strBoard = "{\"board_id\":$board_id,\"board_title\":$board_title,\"board_type\":$board_type,\"color\":$color, \"cards\":$strCards}"; 
            return $strBoard;
        }
    }

?>

    