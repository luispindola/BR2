<?php
class M_informes extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    
    function dame_reactivos_elaborados($id_asignatura = null, $ciclo = null)
    {
        /*SQL
         * 
        SELECT Reactivos_no_Nulos.id_asignatura, Reactivos_no_Nulos.ciclo, 
        Count(Reactivos_no_Nulos.CuentaDeId) AS elaborados
        FROM (        
        SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, Count(br_reactivos.Id) AS CuentaDeId
        FROM br_tablas_esp INNER JOIN br_reactivos ON br_tablas_esp.id_tablas_esp=br_reactivos.id_tablas_esp
        GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, br_reactivos.pregunta
        HAVING (((br_reactivos.pregunta) Is Not Null))         
        ) AS Reactivos_no_Nulos
        GROUP BY Reactivos_no_Nulos.id_asignatura, Reactivos_no_Nulos.ciclo
        HAVING (((Reactivos_no_Nulos.id_asignatura)="1") AND 
        ((Reactivos_no_Nulos.ciclo)="2014-2015 PAR EXAM 2"))        
         * 
         */
        $SQL = 'SELECT Reactivos_no_Nulos.id_asignatura, Reactivos_no_Nulos.ciclo, ';
        $SQL = $SQL.'Count(Reactivos_no_Nulos.CuentaDeId) AS elaborados ';
        $SQL = $SQL.'FROM (';
        $SQL = $SQL.'SELECT br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, Count(br_reactivos.id_reactivo) AS CuentaDeId ';
        $SQL = $SQL.'FROM br_tablas_esp INNER JOIN br_reactivos ON br_tablas_esp.id_tablas_esp=br_reactivos.id_tablas_esp ';
        $SQL = $SQL.'GROUP BY br_tablas_esp.id_asignatura, br_tablas_esp.ciclo, br_reactivos.pregunta ';
        $SQL = $SQL.'HAVING (((br_reactivos.pregunta) Is Not Null))';
        $SQL = $SQL.') AS Reactivos_no_Nulos ';
        $SQL = $SQL.'GROUP BY Reactivos_no_Nulos.id_asignatura, Reactivos_no_Nulos.ciclo ';
        $SQL = $SQL.'HAVING (((Reactivos_no_Nulos.id_asignatura)="'.$id_asignatura.'") AND ';
        $SQL = $SQL.'((Reactivos_no_Nulos.ciclo)="'.$ciclo.'"))  ';
        $query = $this->db->query($SQL);//Ejecuta la consulta
        if ($query->num_rows() > 0)
            {
                foreach ($query->result() as $row)//Recorre la tabla
                {
                    return $row->elaborados;
                }
            }
    }
    
}
?>