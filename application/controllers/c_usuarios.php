<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C_usuarios extends CI_Controller 
{
    public function index()
    {
        $this->load->model('M_creador');
        $menu = $this->M_creador->menu();//Crear menu
        $datos_inicio = '<h1>Usuarios</h1>';
        $datos_vista = array(
        'datos_inicio'   =>  $datos_inicio,
        'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
    public function informacion_usuario()
    {
        if ($this->session->userdata('id_usuario'))
        {
            //Si tiene sesion iniciada
            $datos_inicio = '<h1>Informaci&oacute;n de usuario</h1>';
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
                $datos_inicio = $datos_inicio.'<p><strong><span style="color: #517901">Datos actualizados correctamente.</span></strong></p>';
            }
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';
            $datos_inicio = $datos_inicio.'<table width=50% BORDER CELLPADDING=10 CELLSPACING=0>';       
            $datos_inicio = $datos_inicio.'<tr><td width=50%><h2>Nombre: </h2></td><td>'.$this->session->userdata('username').'</td></tr>'; 
            $datos_inicio = $datos_inicio.'<tr><td><h2>Correo electr&oacute;nico: </h2></td><td>'.$this->session->userdata('correo').'</td></tr>'; 
            $SQL = 'SELECT * FROM br_usuarios WHERE id_usuario = '.$this->session->userdata('id_usuario');   
            $query = $this->db->query($SQL);//Ejecuta la consulta
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)
                {   
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Nivel de acceso: </h2></td><td>'.$row->nivel_acceso.'</td></tr>'; 
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Tel&eacute;fono movil: </h2></td><td><input maxlength="20" id="telefono_movil" name="telefono_movil" size="1" style="height: 22px; width: 196px" type="text" value="'.$row->telefono_movil.'" /></td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Tel&eacute;fono oficina: </h2></td><td><input maxlength="20" id="telefono_oficina" name="telefono_oficina" size="1" style="height: 22px; width: 196px" type="text" value="'.$row->telefono_oficina.'" /></td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Otros datos: </h2></td><td><textarea cols="47" id="otros_datos" name="otros_datos" rows="6">'.$row->otros_datos.'</textarea></p></td></tr>';                     
                }
            }
            $datos_inicio = $datos_inicio.'<tr><td><h2> </h2></td><td><input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Guardar" /></td></tr>';
            $datos_inicio = $datos_inicio.'</table><br/>'; 
            $datos_inicio = $datos_inicio.'</form>';
            $this->load->model('M_creador');
            $menu = $this->M_creador->menu();//Crear menu
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }else{header('Location: index.php');}
    }
    public function registro_act($pag=null)
    {
        if ($this->session->userdata('id_usuario'))//Validar session
        {//la session es correcta:
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
            $datos_inicio = '<h1>Registro de actividad</h1>';
            
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
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<table width=70% BORDER CELLPADDING=10 CELLSPACING=0>';   
            $datos_inicio = $datos_inicio.'<tr><td align="center">Seleccione un usuario: ';
            //Se crea el desplegable con los usuarios registrados
            $datos_inicio = $datos_inicio.$this->M_creador->desplegable_usuarios($this->session->userdata('id_usuario_temp'));
            $datos_inicio = $datos_inicio.'<br/><input id="buscar" name="buscar" size="44" style="height: 33px; width: 179px" type="submit" value="Buscar" />';
            $datos_inicio = $datos_inicio.'</td></tr>';
            $datos_inicio = $datos_inicio.'<tr><td align="center">';
            //Se crean los links del paginador
            $datos_inicio = $datos_inicio. $this->pagination->create_links();//Se crean los links del paginador
            $datos_inicio = $datos_inicio.'</td></tr>';   
            $datos_inicio = $datos_inicio.'<tr><td>';
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
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';  
            $datos_inicio = $datos_inicio.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
            $datos_inicio = $datos_inicio.'<th>Id</th>';
            $datos_inicio = $datos_inicio.'<th>Nombre</th>';
            $datos_inicio = $datos_inicio.'<th>Correo-e</th>';
            $datos_inicio = $datos_inicio.'<th>Secci&oacute;n</th>';
            $datos_inicio = $datos_inicio.'<th>Actividad</th>';
            $datos_inicio = $datos_inicio.'<th>Fecha</th>';
            $datos_inicio = $datos_inicio.'</tr>';
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)
                {   
                    $datos_inicio = $datos_inicio.'<tr bgcolor="#FAFAFA",';//Color de fondo
                    $datos_inicio = $datos_inicio.' onmouseover="this.style.backgroundColor=\'#F2F2F2\';"';//Color con mause sobre
                    $datos_inicio = $datos_inicio.' onmouseout="this.style.backgroundColor=\'#FAFAFA\';">';//Regresar al mismo color
                    $datos_inicio = $datos_inicio.'<td>'.$row->id_reg.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->name.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->email.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->seccion.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->descripcion.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->fecha.'</td>';
                    $datos_inicio = $datos_inicio.'</tr>';
                }
            }
            $datos_inicio = $datos_inicio.'</table>';
            //Aqui finaliza la tabla
            $datos_inicio = $datos_inicio.'</td></tr>';  
            $datos_inicio = $datos_inicio.'<tr><td align="center">';
            //Se crean los links del paginador
            $datos_inicio = $datos_inicio. $this->pagination->create_links();//Se crean los links del paginador
            $datos_inicio = $datos_inicio.'</td></tr>';  
            $datos_inicio = $datos_inicio.'</table>';
            $datos_inicio = $datos_inicio.'</form>';
            $datos_inicio = $datos_inicio.'Se encontraron '.$total_rows.' registros';//comentario opcional
            //$datos_inicio = $datos_inicio.'<br/>'.$SQL;//Debug
            //--------------
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        //Session no existe
        else{header('Location: index.php');}
    }
    public function agregar_usuarios($order = null, $pag=null)
    {
        if ($this->session->userdata('id_usuario'))//Validar session
        {//la session es correcta:
            $this->load->helper('url');
            $this->load->model("M_creador");
            $this->load->library('pagination');
            $menu = $this->M_creador->menu();//Creador de menu
            
            $datos_inicio = '<h1>Agregar usuarios</h1>';
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<table width=70% BORDER CELLPADDING=10 CELLSPACING=0>';  
            
            //Inicia consulta
            $SQL = "SELECT br_usuarios.id_usuario, bancodereact_users.name, ";
            $SQL = $SQL."bancodereact_users.id, ";
            $SQL = $SQL."bancodereact_users.email, br_usuarios.nivel_acceso ";
            $SQL = $SQL."FROM bancodereact_users LEFT JOIN br_usuarios ";
            $SQL = $SQL."ON (bancodereact_users.id = br_usuarios.id_usuario) ";
            $SQL = $SQL."ORDER BY ".$order;
                        
            $query = $this->db->query($SQL);//Ejecuta la consulta
            $total_rows = $query->num_rows();//Calculado num de registros
            $per_page = 20;//Registros por pagina
            //Configuracion del paginador
            $config['base_url'] = site_url('c_usuarios/agregar_usuarios/'.$order.'/');
            $config['total_rows'] = $total_rows; //total de registros
            $config['per_page'] = $per_page; //registros por pagina
            $config['first_link'] = '1'; //Ir al inicio
            $config['next_link'] = '>>'; //Siguiente pag
            $config['prev_link'] = '<<'; //Pag Anterior
            $config['last_link'] = ceil($total_rows/$per_page);//Ultima pagina (ceil: Redondea hacia arriba)
            $this->pagination->initialize($config); 
            //Termina Configuracion del paginador   
            $datos_inicio = $datos_inicio.'<tr><td align="center">';            
            $datos_inicio = $datos_inicio. $this->pagination->create_links();//Se crean los links del paginador
            $datos_inicio = $datos_inicio.'</td></tr>';   
            $datos_inicio = $datos_inicio.'<tr><td>';
            
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
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';  
            $datos_inicio = $datos_inicio.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_usuarios/agregar_usuarios/id/'.$pag).'"><font color="#FFFFFF">Id</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_usuarios/agregar_usuarios/name/'.$pag).'"><font color="#FFFFFF">Nombre</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_usuarios/agregar_usuarios/email/'.$pag).'"><font color="#FFFFFF">Correo-e</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_usuarios/agregar_usuarios/nivel_acceso/'.$pag).'"><font color="#FFFFFF">Nivel de acceso</font></a></th>';
            $datos_inicio = $datos_inicio.'<th>Acciones</th>';
            $datos_inicio = $datos_inicio.'</tr>';
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)
                {   
                    $datos_inicio = $datos_inicio.'<tr bgcolor="#FAFAFA",';//Color de fondo
                    $datos_inicio = $datos_inicio.' onmouseover="this.style.backgroundColor=\'#F2F2F2\';"';//Color con mause sobre
                    $datos_inicio = $datos_inicio.' onmouseout="this.style.backgroundColor=\'#FAFAFA\';">';//Regresar al mismo color
                    $datos_inicio = $datos_inicio.'<td>'.$row->id.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->name.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->email.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->nivel_acceso.'</td>';
                    $datos_inicio = $datos_inicio.'<td><input type="button" value="Agregar o excluir usuario" onClick="window.location =\'algo.php\';"/> </td>';
                    $datos_inicio = $datos_inicio.'</tr>';
                }
            }
            $datos_inicio = $datos_inicio.'</table>';
            
            $datos_inicio = $datos_inicio.'</td></tr>';
            $datos_inicio = $datos_inicio.'</table>';
            $datos_inicio = $datos_inicio.'</form>';
            $datos_inicio = $datos_inicio.'Se encontraron '.$total_rows.' registros';//comentario opcional
            //$datos_inicio = $datos_inicio.'<br/>'.$SQL;//Debug
            
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        //Session no existe
        else{header('Location: index.php');}
    }
}