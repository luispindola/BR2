<?php
class M_fechas extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    function tiempodesde($date)
    {// en el formato date("Y-m-d H:i:s")
        date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
        if(empty($date)) {
            return "No date provided";
        }
        $periods         = array("segundo", "minuto", "hora", "día", "semana", "mes", "año", "decada");
        $lengths         = array("60","60","24","7","4.35","12","10");
        $now             = time();
        $unix_date         = strtotime($date);
           // check validity of date
        if(empty($unix_date)) {    
            return "Bad date";
        }
        // is it future date or past date
        if($now > $unix_date) 
        {    
            $difference     = $now - $unix_date;
            $tense         = "hace";
        } else 
        {
            $difference     = $unix_date - $now;
            $tense         = "a partir de ahora";
        }
        for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) 
        {
            $difference /= $lengths[$j];
        }
        $difference = round($difference);
        if($difference != 1) 
        {
            $periods[$j].= "s";
        }
        return " {$tense} $difference $periods[$j]";
    }
}
?>