<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C_main extends CI_Controller 
{
    public function index()
    {
        include('../spin.php');
        $user = JFactory::getUser();
        if (($user->username) == null)//chekar session iniciada en Joomla
        {//No hay session de joomla
            $datos_inicio = "<h1>Acceso restingido</h1>";
            $this->session->unset_userdata('id_usuario');
            $this->session->unset_userdata('username');
            $this->session->unset_userdata('correo');
            $this->session->unset_userdata('nivel_acceso');
        }
        else//Si existe session en joomla
        {        
            if ($this -> M_usuarios -> validar_id($user->id))
            {//Usuario Incluido en el BdeR
                //Si encontró usuario
                $datos_inicio = '<h1>Bienvenido</h1>';
                $datos_inicio = $datos_inicio.'<table width=50% BORDER CELLPADDING=10 CELLSPACING=0>';       
                $datos_inicio = $datos_inicio.'<tr><td><h2>Nombre: </h2></td><td>'.$user->username.'</td></tr>'; 
                $datos_inicio = $datos_inicio.'<tr><td><h2>Correo electr&oacute;nico: </h2></td><td>'.$user->email.'</td></tr>'; 
                $SQL = 'SELECT * FROM br_usuarios WHERE id_usuario = '.$user->id;   
                $query = $this->db->query($SQL);//Ejecuta la consulta
                if ($query->num_rows() > 0)
                {
                    foreach ($query->result() as $row)
                    {   
                        $datos_inicio = $datos_inicio.'<tr><td width=50%><h2>Tel&eacute;fono movil: </h2></td><td>'.$row->telefono_movil.'</td></tr>';
                        $datos_inicio = $datos_inicio.'<tr><td><h2>Tel&eacute;fono oficina: </h2></td><td>'.$row->telefono_oficina.'</td></tr>';
                        $datos_inicio = $datos_inicio.'<tr><td><h2>Otros datos: </h2></td><td>'.$row->otros_datos.'</td></tr>';
                        $this->load->model('M_fechas');
                        $datos_inicio = $datos_inicio.'<tr><td><h2>Ultima visita: </h2></td><td>'.$this->M_fechas->tiempodesde($this->M_usuarios->ultimavisita($user->id)).'</td></tr>';
                        $datos_inicio = $datos_inicio.'</table><br/>';
                    }
                }
                $datos_inicio = $datos_inicio.'Para agregar o modificar su informaci&oacute;n, entre en el men&uacute; Usuario / Informaci&oacute;n de usuario';
                //Cargar datos de session de CodeIgniter
                $this->session->set_userdata('id_usuario',$user->id);
                $this->session->set_userdata('username',$user->username);
                $this->session->set_userdata('correo',$user->email);
                $this->session->set_userdata('nivel_acceso',$row->nivel_acceso);
                $this->M_usuarios->registrar($user->id,"Inicio","Inicio del sistema"); //Crea registro de visita
            }
            else 
            {
                //No encontró usuario
                $datos_inicio = '<h1>Usuario no autorizado</h1>';
                $this->session->unset_userdata('id_usuario');
                $this->session->unset_userdata('username');
                $this->session->unset_userdata('correo');
                $this->session->unset_userdata('nivel_acceso');
                $this->M_usuarios->registrar($user->id,"Inicio","Usuario no Autorizado"); //Crea registro de visita
            }
        }
        $menu = $this->M_creador->menu();
        $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
}
?>