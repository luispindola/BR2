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
    public function listado($order = null, $pag=null)
    {
        if ($this->session->userdata('id_usuario'))
        {
            //Si tiene sesion iniciada
            $menu = $this->M_creador->menu();//Crear menu
            $datos_inicio = '<h1>Listados de tablas de especificaciones</h1>';
            
            /*
            SELECT br_tablas_esp.id_asignatura, br_asignaturas.asignatura, br_tablas_esp.ciclo, 
            br_asignaturas.semestre, br_asignaturas.componente, br_tablas_esp.id_usuario_editor, 
            br_tablas_esp.id_usuario_revisor, Count(br_tablas_esp.id_tablas_esp) AS reactivos, 
            Max(br_tablas_esp.f_creacion) AS f_creacion 
            FROM br_tablas_esp INNER JOIN br_asignaturas 
            ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura
            GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.asignatura, br_tablas_esp.ciclo, 
            br_asignaturas.semestre, br_asignaturas.componente, br_tablas_esp.id_usuario_editor, 
            br_tablas_esp.id_usuario_revisor
            HAVING (((br_tablas_esp.id_usuario_editor)="890") 
            OR ((br_tablas_esp.id_usuario_revisor)="890")); 
            */
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_asignaturas.asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_asignaturas.semestre, br_asignaturas.componente, br_tablas_esp.id_usuario_editor, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_revisor, Count(br_tablas_esp.id_tablas_esp) AS reactivos, ";
            $SQL = $SQL."Max(br_tablas_esp.f_creacion) AS f_creacion ";
            $SQL = $SQL."FROM br_tablas_esp INNER JOIN br_asignaturas ";
            $SQL = $SQL."ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_asignaturas.semestre, br_asignaturas.componente, br_tablas_esp.id_usuario_editor, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_revisor ";
            $SQL = $SQL."HAVING (((br_tablas_esp.id_usuario_editor)= ".$this->session->userdata('id_usuario').") ";
            $SQL = $SQL."OR ((br_tablas_esp.id_usuario_revisor)= ".$this->session->userdata('id_usuario')."))";
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            
            $total_rows = $query->num_rows();//Calculado num de registros
            $per_page = 10;//Registros por pagina
            
            //Configuracion del paginador
            $this->load->library('pagination');//Libreria de paginador
            $config['base_url'] = site_url('c_tablas_esp/listado/'.$order.'/');
            $config['uri_segment'] = 4; //Que segmento del URL tiene el num de pagina
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
            
            //Link del paginador
            $datos_inicio = $datos_inicio.'<tr><td align="center">';            
            $datos_inicio = $datos_inicio. $this->pagination->create_links();//Se crean los links del paginador
            $datos_inicio = $datos_inicio.'</td></tr>'; 
            
            $datos_inicio = $datos_inicio.'<tr><td>';
            
            //Inicia tabla de datos
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';  
            //Encabezados:
            $datos_inicio = $datos_inicio.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/listado/asignatura/'.$pag).'"><font color="#FFFFFF">Asignatura</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/listado/ciclo/'.$pag).'"><font color="#FFFFFF">Ciclo</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/listado/semestre/'.$pag).'"><font color="#FFFFFF">Semestre</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/listado/componente/'.$pag).'"><font color="#FFFFFF">Componente</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/listado/reactivos/'.$pag).'"><font color="#FFFFFF">Reactivos</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/listado/f_creacion/'.$pag).'"><font color="#FFFFFF">Fecha Creaci&oacuten</font></a></th>';
            $datos_inicio = $datos_inicio.'<th width=10%>Editar</th>';
            $datos_inicio = $datos_inicio.'</tr>';
            //Fin Encabezados
            //Inician Datos
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
                    
                    $datos_inicio = $datos_inicio.'<td>'.$row->asignatura.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->ciclo.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->semestre.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->componente.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->reactivos.'</td>';
                    $this->load->model('M_fechas');
                    $datos_inicio = $datos_inicio.'<td>'.$this->M_fechas->tiempodesde($row->f_creacion).'</td>';
                    $datos_inicio = $datos_inicio.'<td>';
                    
                    $this->load->model('M_tablas_esp');
                    if ($row->id_usuario_editor == $this->session->userdata('id_usuario'))
                    {$datos_inicio = $datos_inicio.'<input type="button" value="Entrar como Elaborador" onClick="window.location =\''.  site_url('c_tablas_esp/tabla_esp/elaborador/'.$row->id_asignatura.'/'.$this->M_tablas_esp->dame_id_ciclo($row->ciclo)).'/parcial\';"/>';}  
                    
                    if ($row->id_usuario_revisor == $this->session->userdata('id_usuario'))
                    {$datos_inicio = $datos_inicio.'<input type="button" value="Entrar como Revisor" onClick="window.location =\''.  site_url('c_tablas_esp/tabla_esp/revisor/'.$row->id_asignatura.'/'.$this->M_tablas_esp->dame_id_ciclo($row->ciclo)).'/parcial\';"/>';}                     
                    
                    $datos_inicio = $datos_inicio.'</td>';
                    
                    $datos_inicio = $datos_inicio.'</tr>';
                }
            }
            //Fin Datos
            
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
            
            $datos_inicio = '<h1>Usuarios Elaboradores de Tablas de Especificaciones</h1>';
            
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
            $config['uri_segment'] = 4; //Que segmento del URL tiene el num de pagina
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
            
            $datos_inicio = '<h1>Asignar Elaborador de Tablas de Especificaciones</h1>';
            
            if (isset($_POST['guardar']))//Si se presionó el boton guardar
            {
                /*
                UPDATE Br_tablas_esp SET Br_tablas_esp.id_usuario_editor = "892"
                WHERE (((Br_tablas_esp.id_asignatura)="1") AND ((Br_tablas_esp.ciclo)="2012-2013 NON"));
                */
                $SQL = "UPDATE br_tablas_esp SET br_tablas_esp.id_usuario_editor = '".$_POST['id_usuario']."' ";
                $SQL = $SQL."WHERE ((br_tablas_esp.id_asignatura = ".$id_asignatura.") ";
                $SQL = $SQL."AND (br_tablas_esp.ciclo = '".str_replace("%20"," ",$ciclo)."'))";        
                $query = $this->db->query($SQL);//Ejecuta la consulta
                $this->load->model('M_tablas_esp');
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Asignar elaborador","Asignatura: ".$this->M_tablas_esp->dame_asignatura($id_asignatura)." Ciclo: ".str_replace("%20"," ",$ciclo)." Usuario Asignado: ".$_POST['id_usuario']); //Crea registro de visita                           
                $datos_inicio = $datos_inicio.'<p><strong><span style="color: #517901">Elaborador asignado correctamente.</span></strong></p>';
            }
            
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
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_editor, ";
            $SQL = $SQL."br_tablas_esp.ciclo, br_asignaturas.componente, br_asignaturas.asignatura, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos ";
            $SQL = $SQL."FROM br_tablas_esp INNER JOIN br_asignaturas ";
            $SQL = $SQL."ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_editor, ";
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
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Asignar usuario: </h2></td><td>'.$this->M_creador->desplegable_usuarios($row->id_usuario_editor).'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2> </h2></td><td><input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Guardar" /></td></tr>';
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
    public function desasignar_elaborador($id_asignatura = null, $ciclo = null)
    {
            if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $menu = $this->M_creador->menu();//Creador de menu
            
            $datos_inicio = '<h1>Desasignar Elaborador de Tablas de Especificaciones</h1>';
            
            if (isset($_POST['guardar']))//Si se presionó el boton guardar
            {
                /*
                UPDATE Br_tablas_esp SET Br_tablas_esp.id_usuario_editor = "892"
                WHERE (((Br_tablas_esp.id_asignatura)="1") AND ((Br_tablas_esp.ciclo)="2012-2013 NON"));
                */
                $SQL = "UPDATE br_tablas_esp SET br_tablas_esp.id_usuario_editor = '' ";
                $SQL = $SQL."WHERE ((br_tablas_esp.id_asignatura = ".$id_asignatura.") ";
                $SQL = $SQL."AND (br_tablas_esp.ciclo = '".str_replace("%20"," ",$ciclo)."'))";        
                $query = $this->db->query($SQL);//Ejecuta la consulta
                $this->load->model('M_tablas_esp');
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Desasignar elaborador","Asignatura: ".$this->M_tablas_esp->dame_asignatura($id_asignatura)." Ciclo: ".str_replace("%20"," ",$ciclo)." Usuario DesAsignado"); //Crea registro de visita                           
                $datos_inicio = $datos_inicio.'<p><strong><span style="color: #517901">Elaborador Desasignado correctamente.</span></strong></p>';
            }
            
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
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_editor, ";
            $SQL = $SQL."br_tablas_esp.ciclo, br_asignaturas.componente, br_asignaturas.asignatura, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos ";
            $SQL = $SQL."FROM br_tablas_esp INNER JOIN br_asignaturas ";
            $SQL = $SQL."ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_editor, ";
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
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Usuario asignado: </h2></td><td>'.$this->M_usuarios->dame_usuario($row->id_usuario_editor).'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2> </h2></td><td><input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Desasignar Elaborador" /></td></tr>';
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
    public function usuarios_revisores($order = null, $pag = null)
    {
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $this->load->library('pagination');//Libreria de paginador
            $menu = $this->M_creador->menu();//Creador de menu
            
            $datos_inicio = '<h1>Usuarios Revisores de Tablas de Especificaciones</h1>';
            
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
            $SQL = $SQL."ON br_usuarios.id_usuario = br_tablas_esp.id_usuario_revisor ";
            $SQL = $SQL."GROUP BY br_usuarios.id_usuario, br_usuarios.nivel_acceso, ";
            $SQL = $SQL."bancodereact_users.username, bancodereact_users.email, ";
            $SQL = $SQL."br_asignaturas.asignatura, br_asignaturas.semestre, br_tablas_esp.ciclo ";
            $SQL = $SQL."ORDER BY ".$order;
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            
            $total_rows = $query->num_rows();//Calculado num de registros
            $per_page = 10;//Registros por pagina
            
            //Configuracion del paginador
            $config['base_url'] = site_url('c_tablas_esp/usuarios_elaboradores/'.$order.'/');
            $config['uri_segment'] = 4; //Que segmento del URL tiene el num de pagina
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
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_revisores/id_usuario/'.$pag).'"><font color="#FFFFFF">Id</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_revisores/username/'.$pag).'"><font color="#FFFFFF">Nombre</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_revisores/email/'.$pag).'"><font color="#FFFFFF">Correo electr&oacutenico</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_revisores/asignatura/'.$pag).'"><font color="#FFFFFF">Asignatura</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_revisores/semestre/'.$pag).'"><font color="#FFFFFF">Semestre</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_revisores/ciclo/'.$pag).'"><font color="#FFFFFF">Ciclo</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/usuarios_revisores/reactivos/'.$pag).'"><font color="#FFFFFF">Reactivos</font></a></th>';
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
                        $datos_inicio = $datos_inicio.'<input type="button" value="Desasignar revisor" onClick="window.location =\''.  site_url('c_tablas_esp/desasignar_revisor/'.$row->id_asignatura.'/'.$row->ciclo).'\';"/>';
                    }
                    else
                    {
                        $datos_inicio = $datos_inicio.'<input type="button" value="Asignar revisor" onClick="window.location =\''.  site_url('c_tablas_esp/asignar_revisor/'.$row->id_asignatura.'/'.$row->ciclo).'\';"/>';
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
    public function asignar_revisor($id_asignatura = null, $ciclo = null)
    {
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $menu = $this->M_creador->menu();//Creador de menu
            
            $datos_inicio = '<h1>Asignar Revisor de Tablas de Especificaciones</h1>';
            
            if (isset($_POST['guardar']))//Si se presionó el boton guardar
            {
                /*
                UPDATE Br_tablas_esp SET Br_tablas_esp.id_usuario_editor = "892"
                WHERE (((Br_tablas_esp.id_asignatura)="1") AND ((Br_tablas_esp.ciclo)="2012-2013 NON"));
                */
                $SQL = "UPDATE br_tablas_esp SET br_tablas_esp.id_usuario_revisor = '".$_POST['id_usuario']."' ";
                $SQL = $SQL."WHERE ((br_tablas_esp.id_asignatura = ".$id_asignatura.") ";
                $SQL = $SQL."AND (br_tablas_esp.ciclo = '".str_replace("%20"," ",$ciclo)."'))";        
                $query = $this->db->query($SQL);//Ejecuta la consulta
                $this->load->model('M_tablas_esp');
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Asignar revisor","Asignatura: ".$this->M_tablas_esp->dame_asignatura($id_asignatura)." Ciclo: ".str_replace("%20"," ",$ciclo)." Usuario Asignado: ".$_POST['id_usuario']); //Crea registro de visita                           
                $datos_inicio = $datos_inicio.'<p><strong><span style="color: #517901">Revisor asignado correctamente.</span></strong></p>';
            }
            
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
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_revisor, ";
            $SQL = $SQL."br_tablas_esp.ciclo, br_asignaturas.componente, br_asignaturas.asignatura, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos ";
            $SQL = $SQL."FROM br_tablas_esp INNER JOIN br_asignaturas ";
            $SQL = $SQL."ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_revisor, ";
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
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Asignar usuario: </h2></td><td>'.$this->M_creador->desplegable_usuarios($row->id_usuario_revisor).'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2> </h2></td><td><input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Guardar" /></td></tr>';
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
    public function desasignar_revisor($id_asignatura = null, $ciclo = null)
    {
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $menu = $this->M_creador->menu();//Creador de menu
            
            $datos_inicio = '<h1>Desasignar Revisor de Tablas de Especificaciones</h1>';
            
            if (isset($_POST['guardar']))//Si se presionó el boton guardar
            {
                /*
                UPDATE Br_tablas_esp SET Br_tablas_esp.id_usuario_editor = "892"
                WHERE (((Br_tablas_esp.id_asignatura)="1") AND ((Br_tablas_esp.ciclo)="2012-2013 NON"));
                */
                $SQL = "UPDATE br_tablas_esp SET br_tablas_esp.id_usuario_revisor = '' ";
                $SQL = $SQL."WHERE ((br_tablas_esp.id_asignatura = ".$id_asignatura.") ";
                $SQL = $SQL."AND (br_tablas_esp.ciclo = '".str_replace("%20"," ",$ciclo)."'))";        
                $query = $this->db->query($SQL);//Ejecuta la consulta
                $this->load->model('M_tablas_esp');
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Desasignar revisor","Asignatura: ".$this->M_tablas_esp->dame_asignatura($id_asignatura)." Ciclo: ".str_replace("%20"," ",$ciclo)." Usuario DesAsignado"); //Crea registro de visita                           
                $datos_inicio = $datos_inicio.'<p><strong><span style="color: #517901">Revisor Desasignado correctamente.</span></strong></p>';
            }
            
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
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_revisor, ";
            $SQL = $SQL."br_tablas_esp.ciclo, br_asignaturas.componente, br_asignaturas.asignatura, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos ";
            $SQL = $SQL."FROM br_tablas_esp INNER JOIN br_asignaturas ";
            $SQL = $SQL."ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.semestre, br_tablas_esp.id_usuario_revisor, ";
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
                    $datos_inicio = $datos_inicio.'<tr><td><h2>Usuario asignado: </h2></td><td>'.$this->M_usuarios->dame_usuario($row->id_usuario_revisor).'</td></tr>';
                    $datos_inicio = $datos_inicio.'<tr><td><h2> </h2></td><td><input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Desasignar Revisor" /></td></tr>';
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
    public function tabla_esp($modo = null, $id_asignatura = null, $id_ciclo = null, $order = null, $pag = null)
    {
        if ($modo == 'elaborador')
        {$nivel_autorizado = 'Elaborador';}
        if ($modo == 'revisor')
        {$nivel_autorizado = 'Revisor';}
        if (($this->session->userdata('nivel_acceso') == 'Administrador') OR ($this->session->userdata('nivel_acceso') == $nivel_autorizado))//Validar nivel de acceso de session
        {
            $this->load->model('M_tablas_esp');
            $this->load->model('M_fechas');
            $menu = $this->M_creador->menu();//Creador de menu
            $ciclo = $this->M_tablas_esp->dame_ciclo($id_ciclo);
            
            $datos_inicio = '<h1>Tabla de Especificaciones modo:'.$modo.'</h1>';
            
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $datos_inicio = $datos_inicio.'<tr><td>';
            //Datos encabezado
            /*
            SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, 
            br_tablas_esp.id_usuario_editor, Max(br_tablas_esp.f_edicion) AS f_edicion, 
            br_tablas_esp.id_usuario_revisor, Max(Br_tablas_esp.f_obs) AS f_obs, 
            Count(br_tablas_esp.id_tablas_esp) AS reactivos, 
            Sum(Br_tablas_esp.aprovado) AS reactivos_aprovados
            FROM Br_tablas_esp
            GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, 
            br_tablas_esp.id_usuario_editor, br_tablas_esp.id_usuario_revisor
            HAVING (((br_tablas_esp.id_asignatura)="1") 
            AND ((br_tablas_esp.ciclo)="2012-2013 NON")); 
            */
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_editor, Max(br_tablas_esp.f_edicion) AS f_edicion, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_revisor, Max(br_tablas_esp.f_obs) AS f_obs, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos, ";
            $SQL = $SQL."Sum(br_tablas_esp.aprovado) AS reactivos_aprovados ";
            $SQL = $SQL."FROM br_tablas_esp ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_editor, br_tablas_esp.id_usuario_revisor ";
            $SQL = $SQL."HAVING (((br_tablas_esp.id_asignatura)=".$id_asignatura.") ";
            $SQL = $SQL."AND ((br_tablas_esp.ciclo)='".str_replace("%20"," ",$ciclo)."'))";
            $query = $this->db->query($SQL);//Ejecuta el query
            if ($query->num_rows() > 0)
            {
                $rowEncabezado = $query->row_array();//Carga el registro en un arreglo
            }
            
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Asignatura:</span></big> '.$this->M_tablas_esp->dame_asignatura($rowEncabezado['id_asignatura']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Ciclo:</span></big> '.str_replace("%20"," ",$ciclo).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Usuario elaborador:</span></big> '.$this->M_usuarios->dame_usuario($rowEncabezado['id_usuario_editor']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Fecha &uacuteltima edici&oacuten:</span></big> '.$rowEncabezado['f_edicion'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Usuario revisor:</span></big> '.$this->M_usuarios->dame_usuario($rowEncabezado['id_usuario_revisor']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Fecha &uacuteltima revici&oacuten:</span></big> '.$rowEncabezado['f_obs'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Reactivos:</span></big> '.$rowEncabezado['reactivos'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Reactivos aprovados:</span></big> '.$rowEncabezado['reactivos_aprovados'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            
            $datos_inicio = $datos_inicio.'</table>';
            //Fin encabezado
            $datos_inicio = $datos_inicio.'</td></tr>';
            
            /* SELECCION DE DATOS DE TABLA SELECCIONADA
            SELECT br_tablas_esp.id_tablas_esp, br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, 
            br_tablas_esp.parcial, br_tablas_esp.bloque, br_tablas_esp.secuencia, 
            br_tablas_esp.apr_indi_obj, br_tablas_esp.saberes, br_tablas_esp.dificultad_docente, 
            br_tablas_esp.observaciones_revisor, br_tablas_esp.f_obs, br_tablas_esp.aprovado
            FROM br_tablas_esp
            WHERE (((br_tablas_esp.id_asignatura)="1") 
            AND ((br_tablas_esp.ciclo)="2012-2013 NON"));
            */
            $SQL = "SELECT br_tablas_esp.id_tablas_esp, br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_tablas_esp.parcial, br_tablas_esp.bloque, br_tablas_esp.secuencia, ";
            $SQL = $SQL."br_tablas_esp.apr_indi_obj, br_tablas_esp.saberes, br_tablas_esp.dificultad_docente, ";
            $SQL = $SQL."br_tablas_esp.observaciones_revisor, br_tablas_esp.f_obs, br_tablas_esp.aprovado ";
            $SQL = $SQL."FROM br_tablas_esp ";
            $SQL = $SQL."WHERE (((br_tablas_esp.id_asignatura)= ".$id_asignatura.") ";
            $SQL = $SQL."AND ((br_tablas_esp.ciclo)='".str_replace("%20"," ",$ciclo)."')) ";
            $SQL = $SQL."ORDER BY ".$order;
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            
            $total_rows = $query->num_rows();//Calculado num de registros
            $per_page = 10;//Registros por pagina
            
            //Configuracion del paginador
            $this->load->library('pagination');//Libreria de paginador
            $config['base_url'] = site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/'.$order.'/');
            $config['uri_segment'] = 7; //Que segmento del URL tiene el num de pagina
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
            
            //Inicia tabla de datos
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';  
            //Encabezados:
            $datos_inicio = $datos_inicio.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/parcial/'.$pag).'"><font color="#FFFFFF">Parcial</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/bloque/'.$pag).'"><font color="#FFFFFF">Bloque</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/secuencia/'.$pag).'"><font color="#FFFFFF">Secuencia</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/apr_indi_obj/'.$pag).'"><font color="#FFFFFF">Aprendizaje, Indicadores, Objetivos</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/saberes/'.$pag).'"><font color="#FFFFFF">Saberes</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/dificultad_docente/'.$pag).'"><font color="#FFFFFF">Dificultad</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/f_obs/'.$pag).'"><font color="#FFFFFF">Fecha &uacuteltima observaci&oacuten</font></a></th>';
            $datos_inicio = $datos_inicio.'<th><a href="'.site_url('c_tablas_esp/tabla_esp/'.$modo.'/'.$id_asignatura.'/'.$id_ciclo.'/aprovado/'.$pag).'"><font color="#FFFFFF">Aprovado</font></a></th>';
            $datos_inicio = $datos_inicio.'<th width=10%>Acciones</th>';
            $datos_inicio = $datos_inicio.'</tr>';
            //Fin Encabezados
            
            //Inicio Datos
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
                    
                    $datos_inicio = $datos_inicio.'<td>'.$row->parcial.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->bloque.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->secuencia.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->apr_indi_obj.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->saberes.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->dificultad_docente.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->f_obs.'</td>';
                    $datos_inicio = $datos_inicio.'<td>'.$row->aprovado.'</td>';
                    $datos_inicio = $datos_inicio.'<td>';
                    if ($modo == 'revisor')
                    {$datos_inicio = $datos_inicio.'<input type="button" value="Entrar como revisor" onClick="window.location =\''.  site_url('c_tablas_esp/revisor/'.$row->id_tablas_esp.'/'.$order.'/'.$pag).'\';"/>';}
                    //elaborador
                    if ($modo == 'elaborador')
                    {$datos_inicio = $datos_inicio.'<input type="button" value="Entrar como elaborador" onClick="window.location =\''.  site_url('c_tablas_esp/elaborador/'.$row->id_tablas_esp.'/'.$order.'/'.$pag).'\';"/>';}
                    
                    $datos_inicio = $datos_inicio.'</td>';
                    
                    $datos_inicio = $datos_inicio.'</tr>';
                }
            }
            //Fin Datos
            
            $datos_inicio = $datos_inicio.'</table>';
            //Fin tabla de datos
            
            $datos_inicio = $datos_inicio.'</td></tr>';
            $datos_inicio = $datos_inicio.'<tr><td>';
            $datos_inicio = $datos_inicio.'Se encontraron '.$total_rows.' registros';//comentario opcional
            $datos_inicio = $datos_inicio.'</td></tr>';
            $datos_inicio = $datos_inicio.'</table>';
            $datos_inicio = $datos_inicio.'</form>';
            
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        else
        {
            //header('Location: '.site_url('c_main'));
            $menu = $this->M_creador->menu();//Creador de menu
            $datos_inicio = '<p><strong><span style="color: #517901">Su nivel de usuario es: '.$this->session->userdata('nivel_acceso').' su acceso esta restringido.</span></strong></p>';
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
    }
    public function revisor($id_tablas_esp = null, $order = null, $pag = null)
    {
        if (($this->session->userdata('nivel_acceso') == 'Administrador') OR ($this->session->userdata('nivel_acceso') == 'Revisor'))//Validar nivel de acceso de session
        {
            $this->load->model('M_tablas_esp');
            
            if (isset($_POST['guardar']))
            {
                date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
                $SQL = "UPDATE br_tablas_esp SET ";
                $SQL = $SQL."observaciones_revisor = '".$_POST['observaciones']."', ";
                $SQL = $SQL."aprovado = '".$_POST['aprovado']."', ";
                $SQL = $SQL."f_obs = '".date("Y-m-d H:i:s")."' ";
                $SQL = $SQL."WHERE id_tablas_esp = ".$id_tablas_esp;
                $query = $this->db->query($SQL);//Ejecuta el query
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Observacion a T de E","id_tabla_esp: ".$id_tablas_esp); //Crea registro de visita                           
            }
            
            $registro = $this->M_tablas_esp->dame_registro_br_tablas_esp($id_tablas_esp);
            $menu = $this->M_creador->menu();//Creador de menu
            $datos_inicio = '<h1>Editando Tablas de Especificacionescomo revisor</h1>';
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $datos_inicio = $datos_inicio.'<tr><td>';
            //Seccion de encabezados           
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_editor, Max(br_tablas_esp.f_edicion) AS f_edicion, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_revisor, Max(br_tablas_esp.f_obs) AS f_obs, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos, ";
            $SQL = $SQL."Sum(br_tablas_esp.aprovado) AS reactivos_aprovados ";
            $SQL = $SQL."FROM br_tablas_esp ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_editor, br_tablas_esp.id_usuario_revisor ";
            $SQL = $SQL."HAVING (((br_tablas_esp.id_asignatura)=".$registro['id_asignatura'].") ";
            $SQL = $SQL."AND ((br_tablas_esp.ciclo)='".$registro['ciclo']."'))";
            
            $query = $this->db->query($SQL);//Ejecuta el query
            if ($query->num_rows() > 0)
            {
                $rowEncabezado = $query->row_array();//Carga el registro en un arreglo
            }
            
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Asignatura:</span></big> '.$this->M_tablas_esp->dame_asignatura($rowEncabezado['id_asignatura']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Ciclo:</span></big> '.$rowEncabezado['ciclo'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Usuario elaborador:</span></big> '.$this->M_usuarios->dame_usuario($rowEncabezado['id_usuario_editor']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Fecha &uacuteltima edici&oacuten:</span></big> '.$rowEncabezado['f_edicion'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Usuario revisor:</span></big> '.$this->M_usuarios->dame_usuario($rowEncabezado['id_usuario_revisor']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Fecha &uacuteltima revici&oacuten:</span></big> '.$rowEncabezado['f_obs'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Reactivos:</span></big> '.$rowEncabezado['reactivos'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Reactivos aprovados:</span></big> '.$rowEncabezado['reactivos_aprovados'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            
            $datos_inicio = $datos_inicio.'</table>';
            
            $datos_inicio = $datos_inicio.'</td></tr>';
            //Fin encabezado
            //Area de datos
            $datos_inicio = $datos_inicio.'<tr><td>';
            
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            $datos_inicio = $datos_inicio.'<tr><td widht=40%>';
            
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Parcial:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td>'.$registro['parcial'].'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Bloque:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td>'.$registro['bloque'].'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Secuencia:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td>'.$registro['secuencia'].'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Aprendizaje, Indicadores, Objetivos:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td>'.$registro['apr_indi_obj'].'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Saberes:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td>'.$registro['saberes'].'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Dificultad:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td>'.$registro['dificultad_docente'].'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'</table>';
            
            $datos_inicio = $datos_inicio.'</td><td widht=60%>';
            //Para observaciones
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<h2>Observaciones:</h2>';
            
            
            $datos_inicio = $datos_inicio.'<textarea cols="100" name="observaciones" rows="10" style="height: 166px; width: 100%">'.$registro['observaciones_revisor'].'</textarea>';
            
            $conDatos = "no";
            $datos_inicio = $datos_inicio.'<br><br><h2>Aprovado: </h2>';
            $datos_inicio = $datos_inicio.'<select id="aprovado" name="aprovado" size="1" style="width: 100px">';
            if ($registro['aprovado'] == 1)   
            {
                $conDatos = "si";
                $datos_inicio = $datos_inicio.'<option selected="selected" value="1">Si</option>';
            }
            else
            {
                $datos_inicio = $datos_inicio.'<option value="1">Si</option>';
            }
            if ($registro['aprovado'] == 0)   
            {
                $conDatos = "si";
                $datos_inicio = $datos_inicio.'<option selected="selected" value="0">No</option>';
            }
            else
            {
                $datos_inicio = $datos_inicio.'<option value="0">No</option>';
            }
            if ($conDatos == "no")
            {
                $datos_inicio = $datos_inicio.'<option selected="selected" value=""> </option>';
            }
            
            $datos_inicio = $datos_inicio.'</select><br><br>';
            
            $datos_inicio = $datos_inicio.'<input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Guardar" />    ';
            $datos_inicio = $datos_inicio.'<input id="regresar" name="regresar" size="44" style="height: 33px; width: 179px" type="submit" value="Regresar" />';
            
            if (isset($_POST['regresar']))
                {header('Location: '.  site_url('c_tablas_esp/tabla_esp/revisor/'.$rowEncabezado['id_asignatura'].'/'.$this->M_tablas_esp->dame_id_ciclo($rowEncabezado['ciclo']).'/'.$order.'/'.$pag));}
            if (isset($_POST['guardar']))
                {$datos_inicio = $datos_inicio.'<br><strong><big><span style="color: #517901">Observaciones Guardadas correctamente</span></big></strong>';}
            
            $datos_inicio = $datos_inicio.'</form>';
                   
            $datos_inicio = $datos_inicio.'</td></tr></table>';
            $datos_inicio = $datos_inicio.'</td></tr>';
            //Fin area de datos
            
            $datos_inicio = $datos_inicio.'</table>';
            
            //Cargar vista
            $datos_vista = array(
            'datos_inicio'   =>  $datos_inicio,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        else{header('Location: '.site_url('c_main'));}
    }
    public function elaborador($id_tablas_esp = null, $order = null, $pag = null)
    {
        if (($this->session->userdata('nivel_acceso') == 'Administrador') OR ($this->session->userdata('nivel_acceso') == 'Elaborador'))//Validar nivel de acceso de session
        {
            $this->load->model('M_tablas_esp');
            
            if (isset($_POST['guardar']))
            {
                date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
                $SQL = "UPDATE br_tablas_esp SET ";
                $SQL = $SQL."parcial = '".$_POST['parcial']."', ";
                $SQL = $SQL."bloque = '".$_POST['bloque']."', ";
                $SQL = $SQL."secuencia = '".$_POST['secuencia']."', ";
                $SQL = $SQL."apr_indi_obj = '".$_POST['apr_indi_obj']."', ";
                $SQL = $SQL."saberes = '".$_POST['saberes']."', ";
                $SQL = $SQL."dificultad_docente = '".$_POST['dificultad_docente']."', ";
                $SQL = $SQL."f_edicion = '".date("Y-m-d H:i:s")."' ";
                $SQL = $SQL."WHERE id_tablas_esp = ".$id_tablas_esp;
                $query = $this->db->query($SQL);//Ejecuta el query
                $this->M_usuarios->registrar($this->session->userdata('id_usuario'),"Elaborador a T de E","id_tabla_esp: ".$id_tablas_esp); //Crea registro de visita                           
            }
            
            $registro = $this->M_tablas_esp->dame_registro_br_tablas_esp($id_tablas_esp);
            $menu = $this->M_creador->menu();//Creador de menu
            $datos_inicio = '<h1>Editando Tablas de Especificaciones como elavorador</h1>';
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $datos_inicio = $datos_inicio.'<tr><td>';
            //Seccion de encabezados           
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_editor, Max(br_tablas_esp.f_edicion) AS f_edicion, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_revisor, Max(br_tablas_esp.f_obs) AS f_obs, ";
            $SQL = $SQL."Count(br_tablas_esp.id_tablas_esp) AS reactivos, ";
            $SQL = $SQL."Sum(br_tablas_esp.aprovado) AS reactivos_aprovados ";
            $SQL = $SQL."FROM br_tablas_esp ";
            $SQL = $SQL."GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ";
            $SQL = $SQL."br_tablas_esp.id_usuario_editor, br_tablas_esp.id_usuario_revisor ";
            $SQL = $SQL."HAVING (((br_tablas_esp.id_asignatura)=".$registro['id_asignatura'].") ";
            $SQL = $SQL."AND ((br_tablas_esp.ciclo)='".$registro['ciclo']."'))";
            
            $query = $this->db->query($SQL);//Ejecuta el query
            if ($query->num_rows() > 0)
            {
                $rowEncabezado = $query->row_array();//Carga el registro en un arreglo
            }
            
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Asignatura:</span></big> '.$this->M_tablas_esp->dame_asignatura($rowEncabezado['id_asignatura']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Ciclo:</span></big> '.$rowEncabezado['ciclo'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Usuario elaborador:</span></big> '.$this->M_usuarios->dame_usuario($rowEncabezado['id_usuario_editor']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Fecha &uacuteltima edici&oacuten:</span></big> '.$rowEncabezado['f_edicion'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Usuario revisor:</span></big> '.$this->M_usuarios->dame_usuario($rowEncabezado['id_usuario_revisor']).'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Fecha &uacuteltima revici&oacuten:</span></big> '.$rowEncabezado['f_obs'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Reactivos:</span></big> '.$rowEncabezado['reactivos'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'<td width=25%>';
            $datos_inicio = $datos_inicio.'<strong><big><span style="color: #517901">Reactivos aprovados:</span></big> '.$rowEncabezado['reactivos_aprovados'].'</strong>';
            $datos_inicio = $datos_inicio.'</td>';
            $datos_inicio = $datos_inicio.'</tr>';
            
            $datos_inicio = $datos_inicio.'</table>';
            
            $datos_inicio = $datos_inicio.'</td></tr>';
            //Fin encabezado

            //Area de datos
            $datos_inicio = $datos_inicio.'<form id="form" method="post">';//Se crea una forma post
            $datos_inicio = $datos_inicio.'<tr><td>';
            
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            $datos_inicio = $datos_inicio.'<tr><td widht=40%>';
            
            $datos_inicio = $datos_inicio.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Parcial:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td><input maxlength="2" name="parcial" id="parcial" size="2" type="text" value="'.$registro['parcial'].'" /></td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Bloque:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td><input maxlength="2" name="bloque" id="bloque" size="2" type="text" value="'.$registro['bloque'].'" /></td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Secuencia:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td><input maxlength="2" name="secuencia" id="secuencia" size="2" type="text" value="'.$registro['secuencia'].'" /></td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Aprendizaje, Indicadores, Objetivos:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td><textarea cols="21" id="apr_indi_obj" name="apr_indi_obj" rows="3" style="height: 54px; width: 274px">'.$registro['apr_indi_obj'].'</textarea></td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Saberes:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td><input maxlength="20" name="saberes" id="saberes" size="12" type="text" value="'.$registro['saberes'].'" /></td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'<tr>';
            $datos_inicio = $datos_inicio.'<td width=50%><strong><big><span style="color: #517901">';
            $datos_inicio = $datos_inicio.'Dificultad:';
            $datos_inicio = $datos_inicio.'</span></big></strong></td>';
            $datos_inicio = $datos_inicio.'<td><input maxlength="10" name="dificultad_docente" id="dificultad_docente" size="4" type="text" value="'.$registro['dificultad_docente'].'" /></td>';
            $datos_inicio = $datos_inicio.'</tr>';
            $datos_inicio = $datos_inicio.'</table>';
            
            $datos_inicio = $datos_inicio.'<input id="guardar" name="guardar" size="44" style="height: 33px; width: 179px" type="submit" value="Guardar" />    ';
            $datos_inicio = $datos_inicio.'<input id="regresar" name="regresar" size="44" style="height: 33px; width: 179px" type="submit" value="Regresar" />';            
            
            if (isset($_POST['regresar']))
                {header('Location: '.  site_url('c_tablas_esp/tabla_esp/elaborador/'.$rowEncabezado['id_asignatura'].'/'.$this->M_tablas_esp->dame_id_ciclo($rowEncabezado['ciclo']).'/'.$order.'/'.$pag));}
            if (isset($_POST['guardar']))
                {$datos_inicio = $datos_inicio.'<br><strong><big><span style="color: #517901">Observaciones Guardadas correctamente</span></big></strong>';}
            
            $datos_inicio = $datos_inicio.'</td><td widht=60%>';
            //Para observaciones
                        
            $datos_inicio = $datos_inicio.'<h2>Observaciones de revisor:</h2><br>'.str_replace("\n", "<br>", $registro['observaciones_revisor']).'<br><br>';
            $datos_inicio = $datos_inicio.'<h2>Aprovado:</h2>';
            if ($registro['aprovado'] == 1)
            {$datos_inicio = $datos_inicio.'Si<br><br>';}
            else
            {$datos_inicio = $datos_inicio.'No<br><br>';}
            $datos_inicio = $datos_inicio.'<h2>Fecha de observaci&oacuten: </h2>'.$registro['f_obs'].'<br><br>';
            
            $datos_inicio = $datos_inicio.'</form>';
                   
            $datos_inicio = $datos_inicio.'</td></tr></table>';
            $datos_inicio = $datos_inicio.'</td></tr>';
            //Fin area de datos
             
            
            $datos_inicio = $datos_inicio.'</table>';
            //Cargar vista
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