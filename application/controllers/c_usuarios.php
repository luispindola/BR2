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
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Usuarios","Datos de usuario actualizados"); //Crea registro de visita                           
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
    public function registro_act($pag = null)
    {
        $this->load->helper('url');
        $this->load->model("M_creador");
        $this->load->library('pagination');
        
        if (isset($_POST['buscar']))//Si se preciona e boton Buscar carga variable de session
        {
            if ($_POST['id_usuario'] <> 'Todos')
            {
                $this->session->set_userdata('id_usuario_temp',$_POST['id_usuario']);
            }
            else
            {
                $this->session->unset_userdata('id_usuario_temp');
            }
        }
        
        $menu = $this->M_creador->menu();//Creador de menu
        $acceso = '<h1>Registro de actividad</h1>';
        //---Inicia Consulta-----------
        $SQL = 'SELECT br_registro_act.id_reg, bancodereact_users.name, ';
        $SQL = $SQL.'bancodereact_users.email, br_registro_act.seccion, ';
        $SQL = $SQL.'br_registro_act.seccion, br_registro_act.descripcion, ';
        $SQL = $SQL.'br_registro_act.fecha, br_registro_act.id_usuario ';
        $SQL = $SQL.'FROM br_registro_act LEFT JOIN bancodereact_users ';
        $SQL = $SQL.'ON br_registro_act.id_usuario = bancodereact_users.id';
        if ($this->session->userdata('id_usuario_temp'))
        {
            $SQL = $SQL.' WHERE id_usuario = '.$this->session->userdata('id_usuario_temp');
        }
        //---Fin Consulta
        $query = $this->db->query($SQL);//Ejecuta la consulta
        $total_rows = $query->num_rows();//Calculado num de registros
        $per_page = 20;//Registros por pagina
        //Configuracion del paginador
        $config['base_url'] = site_url('c_usuarios/registro_act/');
        $config['total_rows'] = $total_rows; //total de registros
        $config['per_page'] = $per_page; //registros por pagina
        $config['first_link'] = '1'; //Ir al inicio
        $config['next_link'] = '>>'; //Siguiente pag
        $config['prev_link'] = '<<'; //Pag Anterior
        $config['last_link'] = ceil($total_rows/$per_page);//Ultima pagina (ceil: Redondea hacia arriba)
        $this->pagination->initialize($config); 
        //Termina Configuracion del paginador   
        $acceso = $acceso.'<form id="form" method="post">';//Se crea una forma post
        $acceso = $acceso.'<table width=70% BORDER CELLPADDING=10 CELLSPACING=0>';   
        $acceso = $acceso.'<tr><td align="center">Seleccione un usuario: ';
        //Se crea el desplegable con los usuarios registrados
        $acceso = $acceso.$this->M_creador->desplegable_usuarios($this->session->userdata('id_usuario_temp'));
        $acceso = $acceso.'<br/><input id="buscar" name="buscar" size="44" style="height: 33px; width: 179px" type="submit" value="Buscar" />';
        $acceso = $acceso.'</td></tr>';
        $acceso = $acceso.'<tr><td align="center">';
        //Se crean los links del paginador
        $acceso = $acceso. $this->pagination->create_links();//Se crean los links del paginador
        $acceso = $acceso.'</td></tr>';   
        $acceso = $acceso.'<tr><td>';
        //Aqui se inicia la tabla
        if (isset($pag))//Se agrega LIMIT a la consulta para seleccionar la pagina
        {
            $SQL = $SQL.' LIMIT '.$pag.', '.$per_page;//Si se ha seleccionado una pagina
        }
        else
        {
            $SQL = $SQL.' LIMIT 0, '.$per_page;//Si es la primera pagina
        }
        $query = $this->db->query($SQL);//Ejecuta la consulta
        $acceso = $acceso.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';  
        $acceso = $acceso.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
        $acceso = $acceso.'<th>Id</th>';
        $acceso = $acceso.'<th>Nombre</th>';
        $acceso = $acceso.'<th>Correo-e</th>';
        $acceso = $acceso.'<th>Secci&oacute;n</th>';
        $acceso = $acceso.'<th>Actividad</th>';
        $acceso = $acceso.'<th>Fecha</th>';
        $acceso = $acceso.'</tr>';
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {   
                $acceso = $acceso.'<tr bgcolor="#FAFAFA",';//Color de fondo
                $acceso = $acceso.' onmouseover="this.style.backgroundColor=\'#F2F2F2\';"';//Color con mause sobre
                $acceso = $acceso.' onmouseout="this.style.backgroundColor=\'#FAFAFA\';">';//Regresar al mismo color
                $acceso = $acceso.'<td>'.$row->id_reg.'</td>';
                $acceso = $acceso.'<td>'.$row->name.'</td>';
                $acceso = $acceso.'<td>'.$row->email.'</td>';
                $acceso = $acceso.'<td>'.$row->seccion.'</td>';
                $acceso = $acceso.'<td>'.$row->descripcion.'</td>';
                $acceso = $acceso.'<td>'.$row->fecha.'</td>';
                $acceso = $acceso.'</tr>';
            }
        }
        $acceso = $acceso.'</table>';
        //Aqui finaliza la tabla
        $acceso = $acceso.'</td></tr>';  
        $acceso = $acceso.'<tr><td align="center">';
        //Se crean los links del paginador
        $acceso = $acceso. $this->pagination->create_links();//Se crean los links del paginador
        $acceso = $acceso.'</td></tr>';  
        $acceso = $acceso.'</table>';
        $acceso = $acceso.'</form>';
        $acceso = $acceso.'Se encontraron '.$total_rows.' registros';//comentario opcional
        //$acceso = $acceso.'<br/>'.$SQL;//Debug
        //--------------
        
        $datos_vista = array(
        'datos_inicio'   =>  $acceso,
        'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
}
