<?php
class M_tablas_esp extends CI_Model 
{
    function __construct()
    {
        parent::__construct();
    }
    function cargar_select_asignaturas()
    {
        $ssql = "SELECT * FROM br_asignaturas";
        $query = $this->db->query($ssql);//Ejecuta el query
        if ($query->num_rows() > 0)
        {
            $salida='<select id="adignatura" name="asignatura" size="1">';
            foreach ($query->result() as $row)
            {
                $salida=$salida.'<option value="'.$row->id_asignatura.'">';
                $salida=$salida.$row->asignatura;
                $salida=$salida.'</option>';
            }
            $salida = $salida.'</select>';
        }
        return $salida;
   }
    function cargar_select_ciclos()
    {
         $ssql = "SELECT * FROM br_ciclos";
         $query = $this->db->query($ssql);//Ejecuta el query
         if ($query->num_rows() > 0)
         {
             $salida='<select id="ciclos" name="ciclos" size="1">';
             foreach ($query->result() as $row)
             {
                 $salida=$salida.'<option value="'.$row->ciclo.'">';
                 $salida=$salida.$row->ciclo;
                 $salida=$salida.'</option>';
             }
             $salida = $salida.'</select>';
         }
         return $salida;
    }
    function procesar_archivo($asignatura, $ciclo)
    {
         require_once 'application/libraries/excel/reader.php';
         $data = new Spreadsheet_Excel_Reader();
         $data->setOutputEncoding('CP1251');
         $data->read('img/temp/temp.xls');
         error_reporting(E_ALL ^ E_NOTICE);
         $error = "";
         if (
             (strtoupper($data->sheets[0]['cells'][1][1]) == 'PARCIAL') &&
             (strtoupper($data->sheets[0]['cells'][1][2]) == 'BLOQUE') &&
             (strtoupper($data->sheets[0]['cells'][1][3]) == 'SECUENCIA') &&
             (strtoupper($data->sheets[0]['cells'][1][4]) == 'APRENDIZAJE, INDICADORES, OBJETIVOS') &&
             (strtoupper($data->sheets[0]['cells'][1][5]) == 'SABERES') &&
             (strtoupper($data->sheets[0]['cells'][1][6]) == 'BAJA') &&
             (strtoupper($data->sheets[0]['cells'][1][7]) == 'MEDIA') &&
             (strtoupper($data->sheets[0]['cells'][1][8]) == 'ALTA')
             )
         {//Tiene correcto los encabezados
             //Checar si la asignatura ciclo existe
             $SQL = "SELECT * FROM br_tablas_esp WHERE ((id_asignatura = '".$asignatura."') AND (ciclo = '".$ciclo."'))";
             $query = $this->db->query($SQL);//Ejecuta el query
             if ($query->row_array())
             {//Si existe el con
                 //Error Encabezados incorrectos
                 $error = '<p class="error">La asignatura y ciclo escolar ya existe en base de datos</p>';
             }
             else
             {//No existe
                 $this->load->model('M_tablas_esp');
                 $this->load->model('M_creador');
                 $id_tablas_esp = $this->M_tablas_esp->obtener_ultimo_id_tablas_esp();
                 $renglonExcel = 1;
                 while ($data->sheets[0]['cells'][$renglonExcel][1]<>"")
                 {
                     //echo($data->sheets[0]['cells'][$renglonExcel][4]);//DEBUG
                     $renglonExcel++;
                     if ($data->sheets[0]['cells'][$renglonExcel][6]<>"")
                     {//Existen X en BAJA
                         $baja = trim(str_replace(" ","",$data->sheets[0]['cells'][$renglonExcel][6]));
                         $baja = strtoupper($baja);
                         for ($i = 1; $i <= strlen($baja) ; $i++ )
                         {
                             $id_tablas_esp++;
                             $SQL = "INSERT INTO br_tablas_esp (id_tablas_esp, id_asignatura, ciclo, f_creacion, id_usuario_creador, ";
                             $SQL = $SQL."parcial, bloque, secuencia, apr_indi_obj, saberes, dificultad_docente) VALUES ";
                             $SQL = $SQL."(";
                             $SQL = $SQL.$id_tablas_esp.", ";
                             $SQL = $SQL.$asignatura.", ";
                             $SQL = $SQL."'".$ciclo."', ";
                             date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
                             $SQL = $SQL."'".date("Y-m-d H:i:s")."', ";
                             $SQL = $SQL.$this->session->userdata('id_usuario').", ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][1]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][2]."', "; 
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][3]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][4]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][5]."', ";
                             $SQL = $SQL."'BAJA'";
                             $SQL = $SQL.")";
                             $query = $this->db->query($SQL);//Ejecuta el query
                             //echo (' baja:'.$i);
                         }
                         //echo $baja."-";        
                     }
                     if ($data->sheets[0]['cells'][$renglonExcel][7]<>"")
                     {//Existen X en MEDIA
                         $media = trim(str_replace(" ","",$data->sheets[0]['cells'][$renglonExcel][7]));
                         $media = strtoupper($media);
                         for ($i = 1; $i <= strlen($media) ; $i++ )
                         {
                             $id_tablas_esp++;
                             $SQL = "INSERT INTO br_tablas_esp (id_tablas_esp, id_asignatura, ciclo, f_creacion, id_usuario_creador, ";
                             $SQL = $SQL."parcial, bloque, secuencia, apr_indi_obj, saberes, dificultad_docente) VALUES ";
                             $SQL = $SQL."(";
                             $SQL = $SQL.$id_tablas_esp.", ";
                             $SQL = $SQL.$asignatura.", ";
                             $SQL = $SQL."'".$ciclo."', ";
                             date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
                             $SQL = $SQL."'".date("Y-m-d H:i:s")."', ";
                             $SQL = $SQL.$this->session->userdata('id_usuario').", ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][1]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][2]."', "; 
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][3]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][4]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][5]."', ";
                             $SQL = $SQL."'MEDIA'";
                             $SQL = $SQL.")";
                             $query = $this->db->query($SQL);//Ejecuta el query
                             //echo (' media:'.$i);
                         }
                         //echo $media."-";        
                     }
                     if ($data->sheets[0]['cells'][$renglonExcel][8]<>"")
                     {//Existen X en ALTA
                         $alta = trim(str_replace(" ","",$data->sheets[0]['cells'][$renglonExcel][8]));
                         $alta = strtoupper($alta);
                         for ($i = 1; $i <= strlen($alta) ; $i++ )
                         {
                             $id_tablas_esp++;
                             $SQL = "INSERT INTO br_tablas_esp (id_tablas_esp, id_asignatura, ciclo, f_creacion, id_usuario_creador, ";
                             $SQL = $SQL."parcial, bloque, secuencia, apr_indi_obj, saberes, dificultad_docente) VALUES ";
                             $SQL = $SQL."(";
                             $SQL = $SQL.$id_tablas_esp.", ";
                             $SQL = $SQL.$asignatura.", ";
                             $SQL = $SQL."'".$ciclo."', ";
                             date_default_timezone_set('America/Los_Angeles'); //Establece Zona horaria
                             $SQL = $SQL."'".date("Y-m-d H:i:s")."', ";
                             $SQL = $SQL.$this->session->userdata('id_usuario').", ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][1]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][2]."', "; 
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][3]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][4]."', ";
                             $SQL = $SQL."'".$data->sheets[0]['cells'][$renglonExcel][5]."', ";
                             $SQL = $SQL."'ALTA'";
                             $SQL = $SQL.")";
                             $query = $this->db->query($SQL);//Ejecuta el query
                             //echo (' alta:'.$i);
                         }
                         //echo $alta."-";   
                     }
                 }
             }

         }else
         {//Error Encabezados incorrectos
             $error = '<p class="error">El archivo no tiene los encabezados de columna requeridos.</p>';   
         }
         $this->load->helper('file');//Cargar Helper file
         delete_files('img/temp/');//Borrar archivo (todos los del directorio)
         if ($error <> "")
         {return $error;}
    }
   function obtener_ultimo_id_tablas_esp()
   {
        $ssql = "SELECT MAX(id_tablas_esp) AS max_id FROM br_tablas_esp";
        $query = $this->db->query($ssql);//Ejecuta el query
        $row = $query->row_array();//Carga el registro en un arreglo
        return $row['max_id'];
   }
   function dame_asignatura($id_asignatura)
   {
       $SQL = "SELECT asignatura FROM br_asignatura WHERE id_asignatura = ".$id_asignatura;
       $query = $this->db->query($SQL);//Ejecuta el query
       $row = $query->row_array();//Carga el registro en un arreglo
       return $row['asignatura'];
   }
}
?>
