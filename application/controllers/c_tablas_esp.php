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
            
            //Llama vista
        }
        else{header('Location: '.site_url('c_main'));}
    }
}
?>