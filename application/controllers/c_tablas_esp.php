<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C_tablas_esp extends CI_Controller 
{
    public function index()
    {
        $menu = $this->M_creador->menu();//Crear menu
        $datos_inicio = '<h1>Tablas de especificaciones</h1>';
        $datos_inicio = $datos_inicio.'<h2>'.$menu.'</h2>';
        $datos_vista = array(
        'datos_inicio'   =>  $datos_inicio,
        'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
    public function listado($pag=null)
    {
        if ($this->session->userdata('id_usuario'))
        {
            //Si tiene sesion iniciada
            $menu = $this->M_creador->menu();//Crear menu
            $datos_inicio = '<h1>Listados de tablas de especificaciones</h1>';
            
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<table width=70% BORDER CELLPADDING=10 CELLSPACING=0>';
            
                    //PENDIENTE!
            
            $datos_inicio = $datos_inicio.'</table>';
            $datos_inicio = $datos_inicio.'</form>';
                   
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        else{header('Location: '.site_url('c_main'));}
    }
    public function agregar()
    {
        $this->load->model('M_tablas_esp');
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $menu = $this->M_creador->menu();//Creador de menu
            
            if(isset($_POST['regresar'])){ header('Location: '.site_url('c_tablas_esp')); }
            //Si existe variable de session de nombre de usuario
            $error = "";
            
            if (isset($_POST['ok']))
            {//Se preciono el boton de cargar
                //echo("type: ".$_FILES["archivo"]["type"]);
                //echo("</br>"); jhddfg
                //echo("name: ".$_FILES["archivo"]["name"]);
                //echo("</br>");
                //echo("size: ".$_FILES["archivo"]["size"]);
                //echo("</br>");
                if (strtoupper(substr($_FILES["archivo"]["name"],-3)) == 'XLS')
                {   
                    move_uploaded_file($_FILES["archivo"]["tmp_name"], "img/temp/temp.xls");
                    $error = $this->M_tablas_esp->procesar_archivo($_POST['asignatura'],$_POST['ciclos']);
                    if ($error == "")
                    {
                        header('Location:'.site_url('c_tablas_esp'));
                        //Registrar agregar tabla
                    $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Agregar Tabla de Esp","Asignatura: ".$_POST['asignatura']." ciclo: ".$_POST['ciclos']);
                    }
                }else
                {
                    $error = '<p class="error">El archivo no tiene extención correcta. Se requiere extención "xls"</p>';
                }
            }
            
            $datos_vista = array(
            'menu'              =>  $menu,
            'algun_error'       =>  $error,
            'carga_asignatura'  =>  $this->M_tablas_esp->cargar_select_asignaturas(),
            'carga_ciclo'       =>  $this->M_tablas_esp->cargar_select_ciclos()
            );
            //Cargar la variable que se pasará a la vista
            $this->load->view('v_carga_tabla_esp',$datos_vista);
        }
        else{header('Location: '.site_url('c_main'));}
    }
    public function usuarios_elaboradores($order = null, $pag = null)
    {
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $this->load->library('pagination');//Libreria de paginador
            $menu = $this->M_creador->menu();//Creador de menu
            
            $datos_inicio = '<h1>Usuarios Elaboradores</h1>';
            
            //Inicia consulta
            /**
            SELECT Br_usuarios.id_usuario, Br_usuarios.nivel_acceso, Bancodereact_users.username, 
            Bancodereact_users.email, Br_asignaturas.asignatura, Br_asignaturas.semestre, 
            Br_tablas_esp.ciclo, Count(Br_tablas_esp.id_tablas_esp) AS CuentaDeid_tablas_esp
            FROM (Br_usuarios LEFT JOIN Bancodereact_users 
            ON Br_usuarios.id_usuario = Bancodereact_users.id) RIGHT JOIN 
            (Br_tablas_esp LEFT JOIN Br_asignaturas 
            ON Br_tablas_esp.id_asignatura = Br_asignaturas.id_asignatura) 
            ON Br_usuarios.id_usuario = Br_tablas_esp.id_usuario_editor
            GROUP BY Br_usuarios.id_usuario, Br_usuarios.nivel_acceso, 
            Bancodereact_users.username, Bancodereact_users.email, Br_asignaturas.asignatura, 
            Br_asignaturas.semestre, Br_tablas_esp.ciclo;
            **/
            $SQL = "SELECT br_usuarios.id_usuario, br_usuarios.nivel_acceso, ";
            $SQL = $SQL."bancodereact_users.username, bancodereact_users.email, ";
            $SQL = $SQL."br_asignaturas.asignatura, br_asignaturas.semestre, br_asignaturas.id_asignatura, ";
            $SQL = $SQL."br_tablas_esp.ciclo, Count(br_tablas_esp.id_tablas_esp) AS reactivos ";
            $SQL = $SQL."FROM (br_usuarios LEFT JOIN bancodereact_users ";
            $SQL = $SQL."ON br_usuarios.id_usuario = bancodereact_users.id) RIGHT JOIN ";
            $SQL = $SQL."(br_tablas_esp LEFT JOIN br_asignaturas ";
            $SQL = $SQL."ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura) ";
            $SQL = $SQL."ON br_usuarios.id_usuario = br_tablas_esp.id_usuario_editor ";
            $SQL = $SQL."GROUP BY br_usuarios.id_usuario, br_usuarios.nivel_acceso, ";
            $SQL = $SQL."bancodereact_users.username, bancodereact_users.email, ";
            $SQL = $SQL."br_asignaturas.asignatura, br_asignaturas.semestre, br_tablas_esp.ciclo ";
            $SQL = $SQL."ORDER BY ".$order;
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            
            $total_rows = $query->num_rows();//Calculado num de registros
            $per_page = 10;//Registros por pagina
            
            //Configuracion del paginador
            $config['base_url'] = site_url('c_tablas_esp/usuarios_elaboradores/'.$order.'/');
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
            
            $datos_inicio = $datos_inicio.'<tr><td align="center">';            
            $datos_inicio = $datos_inicio. $this->pagination->create_links();//Se crean los links del paginador
            $datos_inicio = $datos_inicio.'</td></tr>';   
            $datos_inicio = $datos_inicio.'<tr><td>';
            
            //Inicia tabla de datos
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';  
            //Encabezados:
            $datos_inicio = $datos_inicio.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_elaboradores/id_usuario/'.$pag).'"><font color="#FFFFFF">Id</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_elaboradores/username/'.$pag).'"><font color="#FFFFFF">Nombre</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_elaboradores/email/'.$pag).'"><font color="#FFFFFF">Correo electr&oacutenico</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_elaboradores/asignatura/'.$pag).'"><font color="#FFFFFF">Asignatura</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_elaboradores/semestre/'.$pag).'"><font color="#FFFFFF">Semestre</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_elaboradores/ciclo/'.$pag).'"><font color="#FFFFFF">Ciclo</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_elaboradores/reactivos/'.$pag).'"><font color="#FFFFFF">Reactivos</font></a></th>';
            $datos_inicio = $datos_inicio.'<th width=10%>Acciones</th>';
            $datos_inicio = $datos_inicio.'</tr>';
            //Fin Encabezados
            
            if (isset($pag))//Se agrega LIMIT a la consulta para seleccionar la pagina
            {$SQL = $SQL.' LIMIT '.$pag.', '.$per_page;}//Si se ha seleccionado una pagina
            else
            {$SQL = $SQL.' LIMIT 0, '.$per_page;}//Si es la primera pagina
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)//Recorre la tabla
                {  
                    $datos_inicio = $datos_inicio.'<tr bgcolor="#FAFAFA",';//Color de fondo
                    $datos_inicio = $datos_inicio.' onmouseover="this.style.backgroundColor=\'#F2F2F2\';"';//Color con mause sobre
                    $datos_inicio = $datos_inicio.' onmouseout="this.style.backgroundColor=\'#FAFAFA\';">';//Regresar al mismo color
                    
                    $datos_inicio = $datos_inicio.'<td>'.$row->id_usuario.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->username.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->email.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->asignatura.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->semestre.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->ciclo.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->reactivos.'</td>';
                    $datos_inicio = $datos_inicio.'<td>';
                    if (isset($row->id_usuario))
                    {
                        $datos_inicio = $datos_inicio.'<input type="button" value="Desasignar elaborador" onClick="window.location =\''.  site_url('c_tablas_esp/desasignar_elaborador/'.$row->id_asignatura.'/'.$row->ciclo).'\';"/>';
                    }
                    else
                    {
                        $datos_inicio = $datos_inicio.'<input type="button" value="Asignar elaborador" onClick="window.location =\''.  site_url('c_tablas_esp/asignar_elaborador/'.$row->id_asignatura.'/'.$row->ciclo).'\';"/>';
                    }
                    $datos_inicio = $datos_inicio.'</td>';
                    
                    $datos_inicio = $datos_inicio.'</tr>';
                }
            }
            
            $datos_inicio = $datos_inicio.'</table>';
            //Fin tabla de datos
            $datos_inicio = $datos_inicio.'</td></tr>';
                        
            $datos_inicio = $datos_inicio.'</table>';
            $datos_inicio = $datos_inicio.'</form>';
            $datos_inicio = $datos_inicio.'Se encontraron '.$total_rows.' registros';//comentario opcional
            
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        else{header('Location: '.site_url('c_main'));}//Si no es administrador manda al inicio
    }
    public function asignar_elaborador($id_asignatura = null, $ciclo = null)
    {
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $menu = $this->M_creador->menu();//Creador de menu
            
            $datos_inicio = '<h1>Asignar Elaborador</h1>';
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<table width=50% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            /**
            SELECT Br_tablas_esp.id_asignatura, Br_asignaturas.semestre, 
            Br_tablas_esp.ciclo, Br_asignaturas.componente, Br_asignaturas.asignatura, 
            Count(Br_tablas_esp.id_tablas_esp) AS CuentaDeid_tablas_esp
            FROM Br_tablas_esp INNER JOIN Br_asignaturas 
            ON Br_tablas_esp.id_asignatura = Br_asignaturas.id_asignatura
            GROUP BY Br_tablas_esp.id_asignatura, Br_asignaturas.semestre, 
            Br_tablas_esp.ciclo, Br_asignaturas.componente, Br_asignaturas.asignatura; 
            **/
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_asignaturas.semestre, ";
            $SQL = $SQL."br_tablas_esp.ciclo, br_asignaturas.componente, br_asignaturas.asignatura, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos ";
            $SQL = $SQL."FROM br_tablas_esp INNER JOIN br_asignaturas ";
            $SQL = $SQL."ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.semestre, ";
            $SQL = $SQL."br_tablas_esp.ciclo, br_asignaturas.componente, br_asignaturas.asignatura ";
            $SQL = $SQL."HAVING ((br_tablas_esp.id_asignatura = '".$id_asignatura."') ";
            $SQL = $SQL."AND (br_tablas_esp.ciclo = '".str_replace("%20"," ",$ciclo)."'))";
            //DEBUG $datos_inicio = $datos_inicio.$SQL;
            $query = $this->db->query($SQL);//Ejecuta la consulta
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)
                {   
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Asignatura: </h2></td><td>'.$row->asignatura.'</td></tr>'; 
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Ciclo: </h2></td><td>'.$row->ciclo.'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Semestre: </h2></td><td>'.$row->semestre.'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Componente: </h2></td><td>'.$row->componente.'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Reactivos: </h2></td><td>'.$row->reactivos.'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Asignar usuario: </h2></td><td> desplegable </td></tr>';
                    
                    
                    
                }
            }
            
            $datos_inicio = $datos_inicio.'</table>';
            $datos_inicio = $datos_inicio.'</form>';
            
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        else{header('Location: '.site_url('c_main'));}
    }
}
?>