<?php
class M_fechas extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    function tiempodesde($date)
    {// en el formato date("Y-m-d H:i:s")
        //if (isset($date)<>"0000-00-00 00:00:00")
        //{
            date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
            if(empty($date)) {
                return "Fecha no proporcionada";
            }
            $periods         = array("segundo", "minuto", "hora", "d&iacutea", "semana", "mes", "año", "decada");
            $lengths         = array("60","60","24","7","4.35","12","10");
            $now             = time();
            $unix_date         = strtotime($date);
               // check validity of date
            if(empty($unix_date)) {    
                return "Formato incorrecto";
            }
            // is it future date or past date
            if($now > $unix_date) 
            {    
                $difference     = $now - $unix_date;
                $tense         = "Hace";
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
        //}
        //else
        //{
        //    return "Nunca";
        //}
            
    }
    function mktime($fecha_SQL = null)
    {
        //Formato: 2014-10-04 00:46:42
        //int mktime ([ int $hour = date("H") [, int $minute = date("i") [, int $second = date("s") 
        //[, int $month = date("n") [, int $day = date("j") [, int $year = date("Y") 
        //[, int $is_dst = -1 ]]]]]]] )
        
        
    }
}
?>