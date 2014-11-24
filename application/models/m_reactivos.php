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
     function desplegable_opcion_correcta($opcion_correcta = null)
    {
        $option = '<select id="opcion_correcta" name="opcion_correcta" size="1" style="width: 150px">';
        $option = $option.'<option ';
        if ($opcion_correcta == 'A')
        {$option = $option.'selected="selected" ';}
        $option = $option.'value = "A">A</option>';
        
        $option = $option.'<option ';
        if ($opcion_correcta == 'B')
        {$option = $option.'selected="selected" ';}
        $option = $option.'value = "B">B</option>';
        
        $option = $option.'<option ';
        if ($opcion_correcta == 'C')
        {$option = $option.'selected="selected" ';}
        $option = $option.'value = "C">C</option>';
        
        $option = $option.'<option ';
        if ($opcion_correcta == 'D')
        {$option = $option.'selected="selected" ';}
        $option = $option.'value = "D">D</option>';
        
        if (($opcion_correcta <> 'A') and ($opcion_correcta <> 'B') and ($opcion_correcta <> 'C') and ($opcion_correcta <> 'D'))
        {
            $option = $option.'<option ';
            $option = $option.'selected="selected" ';
            $option = $option.'value = " "> </option>';
        }
        $option = $option.'</select>';
        return $option;
    }
    
    
}
 ?>