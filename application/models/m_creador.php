<?php
class M_creador extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    function menu()
    {
        $this->load->helper('url');
        $menu = '<ul>';
        $menu = $menu.'<li><a href="'.site_url('c_main').'">Inicio</a></li>';
        
        if ($this->session->userdata('id_usuario'))
        {//Muestra el resto del menu si hay variables de session
            $menu = $menu.'<li><a href="'.site_url('c_usuarios').'">Usuarios</a>';
                $menu = $menu.'<ul>';
                $menu = $menu.'<li><a href="'.site_url('c_usuarios/informacion_usuario').'">Informaci&oacute;n de usuario</a></li>';
                if ($this->session->userdata('nivel_acceso') == 'Administrador')
                {$menu = $menu.'<li><a href="'.site_url('c_usuarios/registro_act').'">Registro de Actividad</a></li>';}
                if ($this->session->userdata('nivel_acceso') == 'Administrador')
                {$menu = $menu.'<li><a href="'.site_url('c_usuarios/agregar_usuarios/id').'">Agregar Usuarios</a></li>';}
                $menu = $menu.'</ul>';
            $menu = $menu.'</li>';
        }
        
        if ($this->session->userdata('id_usuario'))
        {//Muestra el resto del menu si hay variables de session
            $menu = $menu.'<li><a href="'.site_url('c_tablas_esp').'">Tablas de Especificaciones</a>';
                $menu = $menu.'<ul>';
                $menu = $menu.'<li><a href="'.site_url('c_tablas_esp/listado').'">Informaci&oacute;n de usuario</a></li>';
                
                $menu = $menu.'</ul>';
            $menu = $menu.'</li>';
        }
        
        $menu = $menu.'</ul>';
        return $menu;
    }
    function desplegable_usuarios($id_usuario = null)
    {
        $option = '<select id="id_usuario" name="id_usuario" size="1" style="width: 350px">';
        $SQL = 'SELECT br_usuarios.id_usuario, bancodereact_users.name ';
        $SQL = $SQL.'FROM br_usuarios LEFT JOIN bancodereact_users ';
        $SQL = $SQL.'ON br_usuarios.id_usuario = bancodereact_users.id';
        $query = $this->db->query($SQL);//Ejecuta la consulta
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {   
                $option = $option.'<option ';
                if ($id_usuario == $row->id_usuario)
                {
                    $option = $option.'selected="selected" ';
                }
                $option = $option.'value = "'.$row->id_usuario.'">';
                $option = $option.$row->name.'</option>';              
            }
            $option = $option.'<option ';
            if ($id_usuario==null)
            {
            $option = $option.'selected="selected" ';
            }
            $option = $option.'value="Todos">Todos</option>';
        }
        $option = $option.'</select>';
        return $option;
    }
    function desplegable_nivel_acceso($nivel_acceso = null)
    {
        $option = '<select id="nivel_acceso" name="nivel_acceso" size="1" style="width: 350px">';
        $option = $option.'<option ';
        if ($nivel_acceso == 'Elaborador')
        {$option = $option.'selected="selected" ';}
        $option = $option.'value = "Elaborador">Elaborador</option>';
        
        $option = $option.'<option ';
        if ($nivel_acceso == 'Revisor')
        {$option = $option.'selected="selected" ';}
        $option = $option.'value = "Revisor">Revisor</option>';
        
        $option = $option.'<option ';
        if ($nivel_acceso == 'Administrador')
        {$option = $option.'selected="selected" ';}
        $option = $option.'value = "Administrador">Administrador</option>';
        
        if (($nivel_acceso <> 'Elaborador') and ($nivel_acceso <> 'Revisor') and ($nivel_acceso <> 'Administrador'))
        {
            $option = $option.'<option ';
            $option = $option.'selected="selected" ';
            $option = $option.'value = "elegir">Elija un Nivel de acceso</option>';
        }
        $option = $option.'</select>';
        return $option;
    }
    
}
?>