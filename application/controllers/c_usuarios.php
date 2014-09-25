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
            $acceso = $acceso.'<table width=50% BORDER CELLPADDING=10 CELLSPACING=0>';       
            $acceso = $acceso.'<tr><td><h2>Nombre: </h2></td><td>'.$this->session->userdata('username').'</td></tr>'; 
            $acceso = $acceso.'<tr><td><h2>Correo electr&oacute;nico: </h2></td><td>'.$this->session->userdata('correo').'</td></tr>'; 
            $SQL = 'SELECT * FROM br_usuarios WHERE id_usuario = '.$this->session->userdata('id_usuario');   
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
            $datos_vista = array(
            'datos_inicio'   =>  $acceso,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }else{header('Location: index.php');}
    }
}
