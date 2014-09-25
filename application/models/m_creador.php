<?php
class M_creador extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    function menu()
    {
        $this->load->helper('url');
        $menu = '<ul>';
        $menu = $menu.'<li><a href="'.site_url('c_main').'">Inicio</a></li>';
        $menu = $menu.'<li><a href="'.site_url('c_usuarios').'">Usuarios</a>';
            $menu = $menu.'<ul>';
            $menu = $menu.'<li><a href="'.site_url('c_usuarios/informacion_usuario').'">Informaci&oacute;n de usuario</a></li>';
            $menu = $menu.'</ul>';
        $menu = $menu.'</li>';
        $menu = $menu.'</ul>';
        return $menu;
    }
}
?>