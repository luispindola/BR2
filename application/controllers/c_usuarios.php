<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C_usuarios extends CI_Controller 
{
    public function index()
    {
        $this->load->model('M_creador');
        $menu = $this->M_creador->menu();//Crear menu
        $acceso = '<h1>Usuarios</h1>';
        $datos_vista = array(
        'datos_inicio'   =>  $acceso,
        'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
    public function informacion_usuario()
    {
        if ($this->session->userdata('id_usuario'))
        {
            //Si tiene sesion iniciada
            $this->load->model('M_usuarios');
            $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Información_usuario","Pantalla de Edición de datos"); //Crea registro de visita
            $this->load->model('M_creador');
            $menu = $this->M_creador->menu();//Crear menu           
            $acceso = '<h1>Informaci&oacute;n de usuario</h1>';
            $acceso = $acceso.'<form id="form" method="post">';
            $acceso = $acceso.'<table width=50% BORDER CELLPADDING=10 CELLSPACING=0>';       
            $acceso = $acceso.'<tr><td><h2>Nombre: </h2></td><td>'.$this->session->userdata('username').'</td></tr>'; 
            $acceso = $acceso.'<tr><td><h2>Correo electr&oacute;nico: </h2></td><td>'.$this->session->userdata('correo').'</td></tr>'; 
            $SQL = 'SELECT * FROM br_usuarios WHERE id_usuario = '.$this->session->userdata('id_usuario');   
            $query = $this->db->query($SQL);//Ejecuta la consulta
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)
                {   
                    $acceso = $acceso.'<tr><td><h2>Tel&eacute;fono movil: </h2></td><td><input maxlength="20" id="telefono_movil" name="telefono_movil" size="1" style="height: 22px; width: 196px" type="text" value="'.$row->telefono_movil.'" /></td></tr>';
                    $acceso = $acceso.'<tr><td><h2>Tel&eacute;fono oficina: </h2></td><td><input maxlength="20" id="telefono_oficina" name="telefono_oficina" size="1" style="height: 22px; width: 196px" type="text" value="'.$row->telefono_oficina.'" /></td></tr>';
                    $acceso = $acceso.'<tr><td><h2>Otros datos: </h2></td><td><textarea cols="47" id="otros_datos" name="otros_datos" rows="6">'.$row->otros_datos.'</textarea></p></td></tr>';                     
                }
            }
            $acceso = $acceso.'<tr><td><h2> </h2></td><td><input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Guardar" /></td></tr>';
            $acceso = $acceso.'</table><br/>'; 
            $acceso = $acceso.'</form>';
            $datos_vista = array(
            'datos_inicio'   =>  $acceso,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }else{header('Location: index.php');}
    }
}
