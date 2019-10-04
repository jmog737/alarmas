<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file testPSS32.php
*  @brief Form para cargar el archivo en la base de datos.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Agosto 2019
*
*******************************************************/
?>

<!DOCTYPE html>
<html>
<?php 
require_once ('head.php');
require_once ('data/config.php');
require_once ('data/pdo.php');
require_once ('data/escribirLog.php');
?>
  <body>
<?php
  require_once ('header.php');
  /// Consulta sólo PSS32s del área metro:
  $query = "select idnodo, nombre, localidad from nodos where tipo='PSS 32' and areaMetro=true";
  
  $log = "NO";
  $datos = json_decode(hacerSelect($query, $log),  true);
  $localidades = $datos['resultado'];
  $totalLocalidades = $datos['rows'];
  
  if (isset($_POST["btnCargarPSS32"])){
    $seguir = true;
    $finValidacion = false;
    $nombreArchivo0 = $_FILES['uploadedFilePSS32']['name'];
    $temp = explode('.', $nombreArchivo0);
    $nombre = $temp[0];
    $extension = $temp[1];
    $fechaCarga = date('Y-m-d');

    /// Chequeo que el archivo no tenga dobe extensión para evitar posibles ataques:
    $totalExtensiones = count($temp);
    if ($totalExtensiones > 2){
      echo "ERROR. El archivo contiene más de una extensión en su nombre.<br>Por favor verifique!.<br>";
      $seguir = false;
      $finValidacion = true;
    } ///Fin totalExtensiones > 2

    if (!($finValidacion)){
      /// Chequeo que la extensión esté dentro de las permitidas:
      /// Array con las extensiones de arhivo permitidas (por ahora solo csv):
      $agujas = array("xls");
      $extMinuscula = mb_strtolower($extension);
      if (!(in_array($extMinuscula, $agujas))){
        echo "¡La extensión <strong>".mb_strtoupper($extension)."</strong> NO es válida!.<br>Por favor verifique!.<br>";
        $seguir = false;
        $finValidacion = true;
      }   
    } /// Fin validacion: extensión

    if (!($finValidacion)){
      /// Validación del tamaño del archivo a subir:
      $tamArchivo = $_FILES['uploadedFilePSS32']['size'];
      if ($tamArchivo > TAM_ARCHIVO){
        echo "ERROR. El archivo a subir ($tamArchivo bytes) es mayor al límite permitido por el sistema (".TAM_ARCHIVO." bytes).<br>";
        $seguir = false;
      }
    } /// Fin validacion: tamaño

    if ($seguir){
      /// Levanto el nombre del nodo y su id.
      /// Como en el POST NO se pasan los atributos del option, se armó antes de enviar un value con todo.
      /// Ahora lo vuelvo a separar:
      $localidadTemp = $_POST['nodo'];
      $temp0 = explode('---', $localidadTemp);
      $localidad1 = $temp0[0];
      $idnodo = $temp0[2];

      $temp1 = explode("#", $temp0[1]);
      $temp2 = explode("-", $temp1[0]);
      $nombreCorto = $temp2[0];

      /// Armo fecha para agregar al nombre del archivo
      if (setlocale(LC_ALL, 'esp') === false){
        echo "Hubo un error con la localía. Por favor verifique que se hayan creado bien las carpetas<br>";
      }

      $dia = strftime("%d", time());
      $mes = substr(ucwords(strftime("%b", time())), 0, 3);
      $year = strftime("%Y", time());
      $fecha = $dia.$mes.$year;    

      $nombreArchivo = $nombreCorto."_".$fecha.".".$extension;

      $rutaCargadosFecha = $dirCargados."/".$fecha;
      if (is_dir($rutaCargadosFecha)){
        //echo "La carpeta del día ya existe: $rutaReporteFecha.<br>";
      }
      else {
        $creoCarpeta0 = mkdir($rutaCargadosFecha);
        if ($creoCarpeta0 === FALSE){
          //echo "Error al crear la carpeta del día: $rutaReporteFecha.<br>";
          $sigo = false;
        }
        else {
          //echo "Carpeta del día creada con éxito: $rutaReporteFecha.<br>";
        }
      }

      $destino = $rutaCargadosFecha."/".$nombreArchivo;
      $continuar = true;

      /// Comento MOMENTÁNEAMENTE la validación de existencia de un archivo previo ya cargado:
//          if (file_exists($destino)) {
//            echo "<br><h3>¡El fichero <span class='negrita'>$nombreArchivo</span> YA se proces&oacute;!.<br>Por favor verifique.</h3>";
//            $continuar = false;
//          } /// Fin if file_exists 

      if ($continuar === TRUE){
        if (!(move_uploaded_file($_FILES['uploadedFilePSS32']['tmp_name'], $destino))) {
          echo "Hubo un error en la copia del archivo.<br>Por favor verifique.<br>";
          return;
        } /// Fin if move_uploaded_file
        
        libxml_use_internal_errors(true);
        $htmlContent = file_get_contents($destino);
        $tempHtml = str_replace("<td/>", "<td>", $htmlContent);
        $nuevoHtml = str_replace("<td><strong></strong></td>", "", $tempHtml);
        
        escribirLog($nuevoHtml);
        $dom = new DOMDocument();
        $dom->loadHTML($nuevoHtml);
        $dom->preserveWhiteSpace = false;
        
        $tables = $dom->getElementsByTagName('table');
        $tablaEncabezado = $tables->item(0);
        $tablaDatos = $tables->item(1);
        
        $filasTemp = $tablaEncabezado->getElementsByTagName('tr');
        $filaEncabezado = $filasTemp->item(0);
        
        $campos = $filaEncabezado->getElementsByTagName('td');
        $camposNombre = array();
        foreach ($campos as $campo) {   
          array_push($camposNombre, $campo->nodeValue); 
        }
        $totalCampos = count($camposNombre);
        
        $filasDatos = $tablaDatos->getElementsByTagName('tr');
        $filas = array();
        foreach ($filasDatos as $fila) {
          $cols = $fila->getElementsByTagName('td');
          $campTemp = array();
          foreach ($cols as $campito){
            array_push($campTemp, $campito->nodeValue); 
          }
          array_push($filas, $campTemp);
        }
        
        $tabla = "<table name='miTabla' id='miTabla' class='tabla2'>";
        $tabla .= "<tr>";
        $tabla .= "<th>Item</th>";
        foreach ($camposNombre as $nombreCampo){
          $tabla .= "<th>".$nombreCampo."</th>";
        }
        $tabla .= "</tr>";
        
        $i = 1;
        foreach ($filas as $index => $miFila){
          $tabla .= "<tr>";
          $tabla .= "<td>".$i."</td>";
          foreach ($miFila as $dato){
            $tabla .= "<td>".$dato."</td>";
          }
          $tabla .= "</tr>";
          $i++;
        }
        $tabla .= "</table>";
        
        echo "<br>".$tabla;
      }
    }  
  }
  
?>
<main>
  <div id='main-content' class='container-fluid'>
    <br>  
    <h2>Cargar archivo:</h2>

    <form method="POST" name='frmSubirPSS32' id='frmSubirPSS32' enctype="multipart/form-data" onSubmit="return validarSubmitCargarPSS32()">
      <table id="tblSubirPSS32" name="tblSubirPSS32" class="tabla2">
      <caption>Formulario para subir el archivo a la base de datos.</caption>
        <tr>
          <th colspan="2" class="centrado tituloTabla">SUBIR ARCHIVO</th>
        </tr>
        <tr>
          <th>
            Archivo:
          </th>  
          <td>
            <input type="file" name="uploadedFilePSS32" id="uploadedFilePSS32" accept=".xls"/>
          </td> 
        </tr>
        <tr>
          <th>
            Nodo:
          </th>  
          <td>
            <select name="nodo" id="nodo" title="Seleccione por favor el nodo del cual se obtuvo el archivo.">
              <option value="nada" nombreCorto="nada">--- Seleccionar NODO ---</option>
              <?php
              foreach ($localidades as $i => $valor){
                $loc = $localidades[$i]['localidad'];
                $nombreCorto = $localidades[$i]['nombre'];
                $idnodo = $localidades[$i]['idnodo'];
                echo "<option value='".$loc."' nombreCorto='".$nombreCorto."' idnodo=".$idnodo.">".$nombreCorto." - ".$loc."</option>";
              }
              ?>
            </select>
          </td> 
        </tr>
        <td colspan="2" class="pieTabla">
          <input type="submit"  class="btn btn-danger" name="btnCargarPSS32" id="btnCargarPSS32" value="CARGAR"/>
        </td>
      </table>  
    </form>
    <br>
  </div>      
</main>
<?php
  require_once ('footer.php');
?>    
  </body>
</html>

