<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file cargar.php
*  @brief Archivo que se encarga de cargar el archivo en la base de datos.
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
?>
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid'>
      <br>  
      <h1>Resultado de la carga:</h1>
    <?php
      $seguir = true;
      $finValidacion = false;
      
      $nombreArchivo0 = $_FILES['uploadedFile']['name'];
      $temp = explode('.', $nombreArchivo0);
      $nombre = $temp[0];
      $extension = $temp[1];
      
      /// Chequeo que el archivo no tenga dobe extensión para evitar posibles ataques:
      $totalExtensiones = count($temp);
      if ($totalExtensiones > 2){
        echo "ERROR. El archivo contiene más de una extensión en su nombre.<br>Por favor verifique!.";
        $seguir = false;
        $finValidacion = true;
      }
      
      if (!($finValidacion)){
        /// Chequeo que la extensión esté dentro de las permitidas:
        /// Array con las extensiones de arhivo permitidas (por ahora solo csv):
        $agujas = array("csv");
        $extMinuscula = mb_strtolower($extension);
        if (!(in_array($extMinuscula, $agujas))){
          echo "La extensión <strong>".mb_strtoupper($extension)."</strong> NO es válida!.<br>Por favor verifique!.";
          $seguir = false;
          $finValidacion = true;
        }   
      }
      
      if (!($finValidacion)){
        /// Validación del tamaño del archivo a subir:
        $tamArchivo = $_FILES['uploadedFile']['size'];
        if ($tamArchivo > TAM_ARCHIVO){
          echo "ERROR. El archivo a subir ($tamArchivo bytes) es mayor al límite permitido por el sistema (".TAM_ARCHIVO." bytes).";
          $seguir = false;
        }
      }
      
      if ($seguir){
        $mostrarError = false;
        $nombreArchivo = $nombre."_".date("dmY").".".$extension;
        $destino = $dirCargados."\\\\".$nombreArchivo;
        if (!(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $destino))) {
          echo "Hubo un error en la copia del archivo.<br>Por favor verifique.";
          return;
        }
        else {
          //echo "Archivo subido con éxito al servidor!.<br>";//<br>Archivo guardado en: <strong><u>$destino</u></strong><br>";
        }
        ///Armo consulta para borrar los datos que pueda haber de antes:
        $queryBorrar = "truncate alarmasdwdm";

        ///Armo consulta para la carga del archivo CSV a la base de datos:
        $queryCargar = "load data infile '$destino' into table alarmasdwdm "
                . "fields terminated by ';' "
                . "optionally enclosed by '\"' "
                . "lines terminated by '\\r\\n' "
                . "ignore 3 lines "
                . "(nombre, compound, tipoAID, tipoAlarma, tipoCondicion, descripcion, afectacionServicio, @fechaCompleta, ubicacion, direccion, valorMonitoreado, nivelUmbral, periodo, datos, filtroALM, filtroAID) "
                . "set Dia=concat(year(now()), '-', substring(trim(@fechaCompleta), 1, 5)), Hora=concat(substring(trim(@fechaCompleta), 8, 2), ':', substring(trim(@fechaCompleta), 11, 2), ':', substring(trim(@fechaCompleta), 14, 2));";
        //echo "<br>consulta:<br>".$queryCargar."<br>";

        $result = $pdo->query($queryBorrar);
        $dato = array();
        if ($result !== FALSE) {
          $result1 = $pdo->query($queryCargar);
          if ($result1 !== FALSE) {
            $dato["resultado"] = "Archivo correctamente subido a la base de datos.";
          }  
          else {
            $dato["resultado"] = "ERROR DURANTE LA CARGA DE ARCHIVO";
            $mostrarError = true;
          } 
        }
        else {
          $dato["resultado"] = "ERROR BORRADO";
          $mostrarError = true;
        }
        /// Si hubo error muestro el mensaje de error. De lo contrario, muestro contenido del archivo:
        if ($mostrarError) {
          echo $dato["resultado"]."<br>";
        }
        else {
          
        }
      }  
      $volver = "<br><br><a href='subirArchivo.php'>Volver a Inicio</a>";
      echo $volver;
    ?>
    </div>      
  </main>      
  <?php
  require_once ('footer.php');
  ?>  
  </body>
</html>


