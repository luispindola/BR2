<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C_main extends CI_Controller 
{
    public function index()
    {
        include('../spin.php');
        $user = JFactory::getUser();
        if (($user->username) == null)
        {
            $acceso = "<h1>Acceso restingido</h1>";
        }
        else
        {
            
            $this -> load -> model('M_usuarios');
            if ($this -> M_usuarios -> validar_id($user->id))
            {
                //Si encontró usuario
                $this -> M_usuarios -> registrar($user -> id,"Inicio","Inicio del sistema"); //Crea registro de visita
                $acceso = '<h1>Bienvenido</h1>';
                $acceso = $acceso.'<table width=50% BORDER CELLPADDING=10 CELLSPACING=0>';       
                $acceso = $acceso.'<tr><td><h2>Nombre: </h2></td><td>'.$user->username.'</td></tr>'; 
                $acceso = $acceso.'<tr><td><h2>Correo electr&oacute;nico: </h2></td><td>'.$user->email.'</td></tr>'; 
                $SQL = 'SELECT * FROM br_usuarios WHERE id_usuario = '.$user->id;   
                $query = $this->db->query($SQL);//Ejecuta la consulta
                if ($query->num_rows() > 0)
                {
                    foreach ($query->result() as $row)
                    {   
                        $acceso = $acceso.'<tr><td><h2>Tel&eacute;fono movil: </h2></td><td>'.$row->telefono_movil.'</td></tr>';
                        $acceso = $acceso.'<tr><td><h2>Tel&eacute;fono oficina: </h2></td><td>'.$row->telefono_oficina.'</td></tr>';
                        $acceso = $acceso.'<tr><td><h2>Otros datos: </h2></td><td>'.$row->otros_datos.'</td></tr>';
                        $acceso = $acceso.'</table><br/>';   
                    }
                }
                $acceso = $acceso.'Para agregar o modificar su informaci&oacute;n, entre en el men&uacute; Usuario / Informaci&oacute;n de usuario';
                $this->session->set_userdata('id_usuario',$user->id);
                $this->session->set_userdata('username',$user->username);
                $this->session->set_userdata('correo',$user->email);
                $this->session->set_userdata('nivel_acceso',$row->nivel_acceso);
            }
            else 
            {
                //No encontró usuario
                $acceso = '<h1>Usuario no autorizado</h1>';
            }
        }
        $this->load->model('M_creador');
        $menu = $this->M_creador->menu();
        $datos_vista = array(
            'datos_inicio'   =>  $acceso,
            'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
}
?>