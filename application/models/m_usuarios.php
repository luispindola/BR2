<?php
class M_usuarios extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    function validar_id($id_usuario)
    {
        $SQL = "SELECT id_usuario FROM br_usuarios WHERE id_usuario = '".$id_usuario."'";
        $query = $this->db->query($SQL);//Ejecuta la consulta
        if ($query->num_rows() > 0)
        {
            //Si encuentra usuario
            return TRUE;
        }
        else
        {
            //No encuentra usuario
            return FALSE;
        }
    }
    function registrar($id_usuario, $seccion, $descripcion)
    {
        //Calcula el siguiente id_reg
        $SQL = "SELECT MAX(id_reg) AS MAX FROM br_registro_act";
        $query = $this->db->query($SQL);//Ejecuta la consulta
        $id_reg = 1;
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $id_reg = $row->MAX + 1;       
            }
        }
        date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
        //Crear registro
        $SQL = "INSERT INTO br_registro_act (";
        $SQL = $SQL."id_reg, id_usuario, seccion, descripcion, fecha) ";
        $SQL = $SQL."VALUES (";
        $SQL = $SQL.$id_reg.", ".$id_usuario." ,'".$seccion."' ,'".$descripcion."','".date("Y-m-d H:i:s")."')";
        $query = $this->db->query($SQL);//Ejecuta la consulta
    }
    function ultimavisita($id_usuario)
    {
        $SQL = "SELECT MAX(fecha) as MAX FROM br_registro_act";
        $query = $this->db->query($SQL);//Ejecuta la consulta
        if ($query->num_rows() > 0)
        {
            foreach ($query->result() as $row)
            {
                $ultima = $row->MAX;       
            }
        }
        return $ultima;
    }
}
?>