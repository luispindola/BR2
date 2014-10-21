<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C_reactivos extends CI_Controller 
{
    public function index()
    {
        $menu = $this->M_creador->menu();//Crear menu
        $datos_inicio = '<h1>Reactivos</h1>';
        $datos_inicio = $datos_inicio.'<h2>'.$menu.'</h2>';
        $datos_vista = array(
        'datos_inicio'   =>  $datos_inicio,
        'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
    public function agregar()
    {
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $menu = $this->M_creador->menu();//Creador de menu
            $v = '<h1>Agregar reactivos</h1>';
            
            /*
            SELECT br_tablas_esp.id_asignatura, br_asignaturas.asignatura,
            br_tablas_esp.ciclo, Count(br_tablas_esp.id_tablas_esp) AS reactivos, 
            Sum(br_tablas_esp.aprovado) AS reactivos_aprovados, 
            Count(br_reactivos.Id_reactivo) AS reactivos_agregados
            FROM (br_tablas_esp LEFT JOIN br_reactivos 
            ON br_tablas_esp.id_tablas_esp = br_reactivos.id_tablas_esp) 
            INNER JOIN br_asignaturas ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura
            GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.asignatura, br_tablas_esp.ciclo;
            */
            $SQL = "SELECT br_tablas_esp.id_asignatura, br_asignaturas.asignatura, ";
            $SQL = $SQL.'br_tablas_esp.ciclo, Count(br_tablas_esp.id_tablas_esp) AS reactivos, ';
            $SQL = $SQL.'Sum(br_tablas_esp.aprovado) AS reactivos_aprovados, ';
            $SQL = $SQL.'Count(br_reactivos.Id_reactivo) AS reactivos_agregados ';
            $SQL = $SQL.'FROM (br_tablas_esp LEFT JOIN br_reactivos ';
            $SQL = $SQL.'ON br_tablas_esp.id_tablas_esp = br_reactivos.id_tablas_esp) ';
            $SQL = $SQL.'INNER JOIN br_asignaturas ON br_tablas_esp.id_asignatura = br_asignaturas.id_asignatura ';
            $SQL = $SQL.'GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.asignatura, br_tablas_esp.ciclo';
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            
            $total_rows = $query->num_rows();//Calculado num de registros
            $per_page = 10;//Registros por pagina
            
            $v = $v.'<br><br>'.$total_rows;
            
            //Cargar vista
            $datos_vista = array(
            'datos_inicio'   =>  $v,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
    }
    
    
}
?>
