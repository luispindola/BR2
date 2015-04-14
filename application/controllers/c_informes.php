<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class C_informes extends CI_Controller 
{
    public function index()
    {
        $menu = $this->M_creador->menu();//Crear menu
        $datos_inicio = '<h1>Informes</h1>';
        $datos_inicio = $datos_inicio.'<h2>'.$menu.'</h2>';
        $datos_vista = array(
        'datos_inicio'   =>  $datos_inicio,
        'menu'           =>  $menu
        );
        $this->load->view('v_limpia',$datos_vista);
    }
    public function listado($order = null)
    {
        if ($this->session->userdata('id_usuario'))
        {
            //Si tiene sesion iniciada
            $menu = $this->M_creador->menu();//Crear menu
            $v = '<h1>'.$this->M_creador->quita_acentos('Listado de ex�menes del banco de reactivos:').'</h1>';
            //-----------------------------------------------------------------------------
            
            //SQL
            /*
            SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, br_reactivos.id_usuario_editor,
            br_reactivos.id_usuario_revisor, Max(br_reactivos.f_edicion) AS M�xDef_edicion, 
            Max(br_reactivos.f_obs) AS M�xDef_obs, Count(br_reactivos.Id) AS reactivos,
            Sum(br_reactivos.aprovado) AS aprovados
            FROM br_tablas_esp INNER JOIN br_reactivos ON br_tablas_esp.id_tablas_esp = br_reactivos.id_tablas_esp
            GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, 
            br_reactivos.id_usuario_editor, br_reactivos.id_usuario_revisor;                          
            */
            $SQL = 'SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, br_reactivos.id_usuario_editor, ';
            $SQL = $SQL.'br_reactivos.id_usuario_revisor, Max(br_reactivos.f_edicion) AS f_edicion, ';
            $SQL = $SQL.'Max(br_reactivos.f_obs) AS f_obs, Count(br_reactivos.id_reactivo) AS reactivos, ';
            $SQL = $SQL.'Sum(br_reactivos.aprovado) AS aprovados ';
            $SQL = $SQL.'FROM br_tablas_esp INNER JOIN br_reactivos ON br_tablas_esp.id_tablas_esp = br_reactivos.id_tablas_esp ';
            $SQL = $SQL.'GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, ';
            $SQL = $SQL.'br_reactivos.id_usuario_editor, br_reactivos.id_usuario_revisor ';
            $SQL = $SQL.'ORDER BY '.$order;
            
            $query = $this->db->query($SQL);//Ejecuta la consulta
            $total_rows = $query->num_rows();//Calculado num de registros
            
            $v = $v.'<form id="form" method="post">';//Se crea una forma post
            $v = $v.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';
            
            $v = $v.'<tr><td align="center">';
            
            //Inicia tabla de datos
            $v = $v.'<table width=100% BORDER CELLPADDING=10 CELLSPACING=0>';  
            //Encabezados:
            $v = $v.'<tr bgcolor="#517901", style="color: #FFFFFF">';//Define fondo y color de letra
            $v = $v.'<th><a href="'.site_url('c_informes/listado/id_asignatura').'"><font color="#FFFFFF">Asignatura</font></a></th>';
            $v = $v.'<th><a href="'.site_url('c_informes/listado/ciclo').'"><font color="#FFFFFF">Ciclo</font></a></th>';
            $v = $v.'<th><a href="'.site_url('c_informes/listado/id_usuario_editor').'"><font color="#FFFFFF">Usuario Editor</font></a></th>';
            $v = $v.'<th><a href="'.site_url('c_informes/listado/id_usuario_revisor').'"><font color="#FFFFFF">Usuario Revisor</font></a></th>';
            $v = $v.'<th><a href="'.site_url('c_informes/listado/f_edicion').'"><font color="#FFFFFF">'.$this->M_creador->quita_acentos('�ltima Edici�n').'</font></a></th>';
            $v = $v.'<th><a href="'.site_url('c_informes/listado/f_obs').'"><font color="#FFFFFF">'.$this->M_creador->quita_acentos('�ltima Revisi�n').'</font></a></th>';
            $v = $v.'<th><a href="'.site_url('c_informes/listado/reactivos').'"><font color="#FFFFFF">Reactivos Solicitados</font></a></th>';
            $v = $v.'<th><a href="'.site_url('c_informes/listado/aprovados').'"><font color="#FFFFFF">Reactivos Aprobados</font></a></th>';            
            $v = $v.'<th width=10%>Reactivos Elaborados</th>';
            $v = $v.'</tr>';
            //Fin Encabezados
            //Inician Datos
            $reactivos_solicitados = 0;
            $reactivos_aprovados = 0;
            if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)//Recorre la tabla
                {  
                    $v=$v.'<tr bgcolor="#FAFAFA",';//Color de fondo
                    $v=$v.' onmouseover="this.style.backgroundColor=\'#F2F2F2\';"';//Color con mause sobre
                    $v=$v.' onmouseout="this.style.backgroundColor=\'#FAFAFA\';">';//Regresar al mismo color
                    
                    $this->load->model('M_tablas_esp');
                    $this->load->model('M_fechas');
                    $this->load->model('M_informes');
                    
                    $v=$v.'<td>'.$this->M_tablas_esp->dame_asignatura($row->id_asignatura).'</td>';
                    $v=$v.'<td>'.$row->ciclo.'</td>';
                    $v=$v.'<td>'.$this->M_usuarios->dame_usuario($row->id_usuario_editor).'</td>';
                    $v=$v.'<td>'.$this->M_usuarios->dame_usuario($row->id_usuario_revisor).'</td>';
                    $v=$v.'<td>'.$this->M_fechas->tiempodesde($row->f_edicion).'</td>';
                    $v=$v.'<td>'.$this->M_fechas->tiempodesde($row->f_obs).'</td>';
                    $v=$v.'<td>'.$row->reactivos.'</td>';
                    $v=$v.'<td>'.$row->aprovados.'</td>';
                    $v=$v.'<td>'.$this->M_informes->dame_reactivos_elaborados($row->id_asignatura, $row->ciclo).'</td>';
                    $reactivos_solicitados = $reactivos_solicitados + $row->reactivos;
                    $reactivos_aprovados = $reactivos_aprovados + $row->aprovados;
                    
                    $v=$v.'</tr>';
                }
            }
            $v=$v.'<td></td>';
            $v=$v.'<td></td>';
            $v=$v.'<td></td>';
            $v=$v.'<td></td>';
            $v=$v.'<td></td>';
            $v=$v.'<td></td>';
            $v=$v.'<td>'.$reactivos_solicitados.'</td>';
            $v=$v.'<td>'.$reactivos_aprovados.'</td>';
            $v=$v.'<td>--</td>';
            
            
            $v=$v.'</table>';           
            $v=$v.'</td></tr>';//Fin tabla de datos
            
            $v = $v.'</td></tr>';
            
            $v = $v.'<tr><td align="center">';
            $v = $v.'Se encontraron '.$total_rows.' '.$this->M_creador->quita_acentos('ex�menes del banco de reactivos:');//comentario opcional
            $v = $v.'</td></tr>';
            
            $v = $v.'</table></form>';
            
            //-----------------------------------------------------------------------------
            //Cargar vista vlimpia
            $datos_vista = array(
            'datos_inicio'   =>  $v,
            'menu'           =>  $menu
            );
            $this->load->view('v_limpia',$datos_vista);
        }
        else{header('Location: '.site_url('c_main'));}
    }
    
}
?>