<?php
class M_reactivos extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    function obtener_ultimo_id_reactivos()
    {
         $ssql = "SELECT MAX(id_reactivo) AS max_id FROM br_reactivos";
         $query = $this->db->query($ssql);//Ejecuta el query
         $row = $query->row_array();//Carga el registro en un arreglo
         return $row['max_id'];
    }
    function dame_registro_br_reactivos($id_tablas_esp = null)
    {
        $SQL = "SELECT * FROM br_reactivos WHERE id_tablas_esp = ".$id_tablas_esp;
        $query = $this->db->query($SQL);//Ejecuta el query
        if ($query->num_rows() > 0)
        {
            $row = $query->row_array();//Carga el registro en un arreglo
            return $row;
        }
    }
    
    
}
 ?>