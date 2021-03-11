<?php

require_once "framework/Model.php";
require_once "User.php";

class MyTools extends Model {

    public static function get_duration_since_date($date) {
        $date = new DateTime($date);
        $now = new DateTime("now");
        $interval = $date->diff($now);

        if($interval->y>0)
            return $interval->y.($interval->y>1?" years":" year");
        else if($interval->m>0)
            return $interval->m.($interval->m>1?" months":" month");
        else if($interval->d>0)
            return $interval->d.($interval->d>1?" days":" day");
        else if($interval->h>0)
            return $interval->h.($interval->h>1?" hours":" hour");
        else if($interval->i>0)
            return $interval->i.($interval->i>1?" minutes":" minute");
        else
            return " less than a minute";
    }

   

 }