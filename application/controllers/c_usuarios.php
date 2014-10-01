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
            $acceso = '<h1>Informaci&oacute;n de usuario</h1>';
            $this->load->model('M_usuarios');
            if (isset($_POST['guardar']))//Si el boton Guardar fue presionado
            {
                $SQL = "UPDATE br_usuarios SET ";
                $SQL = $SQL."telefono_movil = '".$_POST['telefono_movil']."', ";
                $SQL = $SQL."telefono_oficina = '".$_POST['telefono_oficina']."', ";
                $SQL = $SQL."otros_datos = '".$_POST['otros_datos']."' ";
                $SQL = $SQL."WHERE id_usuario = '".$this->session->userdata('id_usuario')."'";
                $query = $this->db->query($SQL);
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Información_usuario","Datos de usuario actualizados"); //Crea registro de visita                           
                $acceso = $acceso.'<p><strong><span style="color: #517901">Datos actualizados correctamente.</span></strong></p>';
            }
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
                    $acceso = $acceso.'<tr><td><h2>Nivel de acceso: </h2></td><td>'.$row->nivel_acceso.'</td></tr>'; 
                    $acceso = $acceso.'<tr><td><h2>Tel&eacute;fono movil: </h2></td><td><input maxlength="20" id="telefono_movil" name="telefono_movil" size="1" style="height: 22px; width: 196px" type="text" value="'.$row->telefono_movil.'" /></td></tr>';
                    $acceso = $acceso.'<tr><td><h2>Tel&eacute;fono oficina: </h2></td><td><input maxlength="20" id="telefono_oficina" name="telefono_oficina" size="1" style="height: 22px; width: 196px" type="text" value="'.$row->telefono_oficina.'" /></td></tr>';
                    $acceso = $acceso.'<tr><td><h2>Otros datos: </h2></td><td><textarea cols="47" id="otros_datos" name="otros_datos" rows="6">'.$row->otros_datos.'</textarea></p></td></tr>';                     
                }
            }
            $acceso = $acceso.'<tr><td><h2> </h2></td><td><input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Guardar" /></td></tr>';
            $acceso = $acceso.'</table><br/>'; 
            $acceso = $acceso.'</form>';
            $this->load->model('M_creador');
            $menu = $this->M_creador->menu();//Crear menu
            $datos_vista = array(
            'datos_inicio'   =>  $acceso,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }else{header('Location: index.php');}
    }
    public function registro_act($pag = null,$id_usuario = null)
    {
        $this->load->helper('url');
        $this->load->model("M_creador");
        $this->load->library('pagination');
        
        $menu = $this->M_creador->menu();//Creador de menu
        $acceso = '<h1>Registro de actividad</h1>';
        //--------------
        
        $acceso = $acceso.'<table width=70% BORDER CELLPADDING=10 CELLSPACING=0>';   
        $acceso = $acceso.'<tr><td>';
        $acceso = $acceso.$this->M_creador->desplegable_usuarios();
        $acceso = $acceso.'</tr></td>';
        $acceso = $acceso.'<tr><td>';
        
        //$acceso = $acceso.'Pag = '.$pag;

        $config['base_url'] = site_url('c_usuarios/registro_act/');
        $config['total_rows'] = 200;
        $config['per_page'] = 20; 
        $config['first_link'] = 'Primera';
        $config['next_link'] = '»';
        $config['prev_link'] = '«';
        $config['last_link'] = 'Última';
        $this->pagination->initialize($config); 
        $acceso = $acceso. $this->pagination->create_links();
        
        
        $acceso = $acceso.'</tr></td>';   
        $acceso = $acceso.'</table>';
        
        //--------------
        $datos_vista = array(
        'datos_inicio'   =>  $acceso,
        'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
}
