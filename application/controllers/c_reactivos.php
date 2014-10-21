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
    public function agregar($order = null, $pag = null)
    {
        if ($this->session->userdata('nivel_acceso') == 'Administrador')//Validar nivel de acceso de session
        {
            $this->load->model('M_tablas_esp');
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
            $SQL = $SQL.'GROUP BY br_tablas_esp.id_asignatura, br_asignaturas.asignatura, br_tablas_esp.ciclo ';
            $SQL = $SQL.'ORDER BY '.$order;
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            
            $total_rows = $query->num_rows();//Calculado num de registros
            $per_page = 10;//Registros por pagina
            
            //Configuracion del paginador
            $this->load->library('pagination');//Libreria de paginador
            $config['base_url'] = site_url('c_reactivos/agregar/'.$order.'/');
            $config['uri_segment'] = 4; //Que segmento del URL tiene el num de pagina
            $config['total_rows'] = $total_rows; //total de registros
            $config['per_page'] = $per_page; //registros por pagina
            $config['first_link'] = '1'; //Ir al inicio
            $config['next_link'] = '>>'; //Siguiente pag
            $config['prev_link'] = '<<'; //Pag Anterior
            $config['last_link'] = ceil($total_rows/$per_page);//Ultima pagina (ceil: Redondea hacia arriba)
            $this->pagination->initialize($config); 
            //Termina Configuracion del paginador   
            
            $v=$v.'<form id="form" method="post">';//Se crea una forma post
            $v=$v.'<table width=50% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $v=$v.'<tr><td align="center">';            
            $v=$v. $this->pagination->create_links();//Se crean los links del paginador
            $v=$v.'</td></tr>'; 
            
            $v=$v.'<tr><td>';//Inicia tabla de datos
            $v=$v.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            //Encabezados:
            $v=$v.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
            $v=$v.'<th><a href="'.site_url('c_reactivos/agregar/asignatura/'.$pag).'"><font color="#FFFFFF">Asignatura</font></a></th>';
            $v=$v.'<th><a href="'.site_url('c_reactivos/agregar/ciclo/'.$pag).'"><font color="#FFFFFF">Ciclo</font></a></th>';
            $v=$v.'<th><a href="'.site_url('c_reactivos/agregar/reactivos/'.$pag).'"><font color="#FFFFFF">Reactivos</font></a></th>';
            $v=$v.'<th><a href="'.site_url('c_reactivos/agregar/reactivos_aprovados/'.$pag).'"><font color="#FFFFFF">Reactivos aprovados</font></a></th>';           
            $v=$v.'<th width=10%>Acciones</th>';
            $v=$v.'</tr>';
            //Fin Encabezados
            //Datos
            if (isset($pag))//Se agrega LIMIT a la consulta para seleccionar la pagina
            {$SQL = $SQL.' LIMIT '.$pag.', '.$per_page;}//Si se ha seleccionado una pagina
            else
            {$SQL = $SQL.' LIMIT 0, '.$per_page;}//Si es la primera pagina
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)//Recorre la tabla
                {  
                    $v=$v.'<tr bgcolor="#FAFAFA",';//Color de fondo
                    $v=$v.' onmouseover="this.style.backgroundColor=\'#F2F2F2\';"';//Color con mause sobre
                    $v=$v.' onmouseout="this.style.backgroundColor=\'#FAFAFA\';">';//Regresar al mismo color
                    
                    $v=$v.'<td>'.$row->asignatura.'</td>';
                    $v=$v.'<td>'.$row->ciclo.'</td>';
                    $v=$v.'<td>'.$row->reactivos.'</td>';
                    $v=$v.'<td>'.$row->reactivos_aprovados.'</td>';
                    $v=$v.'<td>';
                    if ($row->reactivos_agregados > 0)
                    {//Tabla de esp ya fue agregada
                        $v=$v.'<input type="button" value="Borrar reactivos" onClick="window.location =\''.  site_url('c_reactivos/borrar_reactivos/'.$row->id_asignatura.'/'.$this->M_tablas_esp->dame_id_ciclo($row->ciclo)).'\';"/>';
                    }
                    else
                    {//Tabla de esp no agregada
                        $v=$v.'<input type="button" value="Agregar reactivos" onClick="window.location =\''.  site_url('c_reactivos/agregar_reactivos/'.$row->id_asignatura.'/'.$this->M_tablas_esp->dame_id_ciclo($row->ciclo)).'\';"/>';
                    }
                    $v=$v.'</td>';
                    $v=$v.'</tr>';
                }
            }
            //Fin Datos
            $v=$v.'</table>';           
            $v=$v.'</td></tr>';//Fin tabla de datos
            $v=$v.'</table>';
            $v=$v.'</form>';
            
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
