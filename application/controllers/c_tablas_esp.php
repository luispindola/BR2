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
        //PENDIENTE!
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
            $SQL = $SQL."br_asignaturas.asignatura, br_asignaturas.semestre, ";
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
}
?>