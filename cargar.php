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
    <?php
      $seguir = true;
      $finValidacion = false;
      $mostrarError = false;
      
      /// Chequeo variable para saber desde donde vengo; si desde subirArchivo o desde editarAlarma
      /// Si es desde el primero, hago las validaciones y cargo el archivo pasado
      /// En ambos casos, muestro en pantalla los datos
      if (!(isset($_GET['origen']))){
        $nombreArchivo0 = $_FILES['uploadedFile']['name'];
        $temp = explode('.', $nombreArchivo0);
        $nombre = $temp[0];
        $extension = $temp[1];

        /// Chequeo que el archivo no tenga dobe extensión para evitar posibles ataques:
        $totalExtensiones = count($temp);
        if ($totalExtensiones > 2){
          echo "ERROR. El archivo contiene más de una extensión en su nombre.<br>Por favor verifique!.<br>";
          $seguir = false;
          $finValidacion = true;
        }

        if (!($finValidacion)){
          /// Chequeo que la extensión esté dentro de las permitidas:
          /// Array con las extensiones de arhivo permitidas (por ahora solo csv):
          $agujas = array("csv");
          $extMinuscula = mb_strtolower($extension);
          if (!(in_array($extMinuscula, $agujas))){
            echo "¡La extensión <strong>".mb_strtoupper($extension)."</strong> NO es válida!.<br>Por favor verifique!.<br>";
            $seguir = false;
            $finValidacion = true;
          }   
        }

        if (!($finValidacion)){
          /// Validación del tamaño del archivo a subir:
          $tamArchivo = $_FILES['uploadedFile']['size'];
          if ($tamArchivo > TAM_ARCHIVO){
            echo "ERROR. El archivo a subir ($tamArchivo bytes) es mayor al límite permitido por el sistema (".TAM_ARCHIVO." bytes).<br>";
            $seguir = false;
          }
        }
        
        if ($seguir){
          $nombreArchivo = $nombre."_".date("dmY").".".$extension;
          $destino = $dirCargados."\\\\".$nombreArchivo;
          if (!(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $destino))) {
            echo "Hubo un error en la copia del archivo.<br>Por favor verifique.<br>";
            return;
          }
          /// Levanto el nombre del nodo:
          $localidad1 = $_POST['nodo'];
          $_SESSION['nodo'] = $localidad1;
          
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
            try {
              $result1 = $pdo->query($queryCargar);
            } catch (PDOException $e) {
                print "<br><strong>¡ERROR!:</strong> " . $e->getMessage() . "<br>Por favor, ¡verifique que el archivo a cargar tenga datos!.<br>";
                $volver = "<br><a href='subirArchivo.php'>Volver a Inicio</a><br><br>";
                echo $volver;
                die();
            }

            if ($result1 !== FALSE) {
              $dato["resultado"] = "Archivo correctamente subido a la base de datos.";
            }  
            else {
              $dato["resultado"] = "ERROR DURANTE LA CARGA DE ARCHIVO";
              $mostrarError = true;
            } 
          }
          else {
            $dato["resultado"] = "ERROR DURANTE EL BORRADO";
            $mostrarError = true;
          }
        } 
      }
      /// Fin del caso en que vengo desde subirArchivo. Fin de la carga (u error de la misma)
      
      /// Si no hubo problemas con la validación o vengo desde editarAlarma sigo:
      if ($seguir){
        echo "<br>
              <h2>Alarmas del nodo en: <span class='resaltado'>".$_SESSION['nodo']."</span></h2>
              <br>";
        
        /// Si hubo error muestro el mensaje de error. De lo contrario, muestro contenido del archivo:
        if ($mostrarError) {
          echo $dato["resultado"]."<br>";
        }
        else {
          /// Agrego el archivo que reordena los campos:
          require_once('campos.php');
          
          /// Armo la consulta
          $consulta = "select * from alarmasdwdm order by dia desc, hora desc";
          
          /// Hago una pre consulta para saber si hay o no datos (si hay filas):
          $queryTemp = explode('from', $consulta);
          $query1 = "select count(*) from ".$queryTemp[1];
          $totalFilas = $pdo->query($query1)->fetchColumn();

          /// Si hay datos los muestro:
          if ($totalFilas > 0){
            /// Ejecuto la consulta:
            $stmt = $pdo->query($consulta);
            /// Comienzo tabla para mostrar la consulta:
            echo "<table class='tabla2'>";
            echo "<caption>Tabla con las alarmas del nodo</caption><tr>";
            $i = 1;
            $totalCamposMostrar = 1;
            
            /// Muestro el encabezado:
            foreach ($campos as $key => $value) {   
              if ($campos[$key]['mostrar'] === 'si'){
                $clase = '';
                $totalCamposMostrar++;
                if ($campos[$key]['nombre'] === 'id'){
                  $clase = "class='tituloTablaIzquierdo'";
                }
                else {
                  if ($campos[$key]['nombre'] === 'accion'){
                    $clase = "class='tituloTablaDerecho'";
                  }
                }
                echo "<th $clase>".$campos[$key]['nombreMostrar']."</th>";
              }
            }  
            echo "</tr>";
            /// Fin encabezados
            
            /// Comienzo proceso de cada fila:
            while (($fila = $stmt->fetch(PDO::FETCH_ASSOC)) != NULL) {
              /// Extraigo tipo de alarma para poder resaltar en consecuencia:
              $tipoAlarma = $fila['tipoAlarma'];
              switch ($tipoAlarma) {
                case 'CR': $clase = 'alCritica';
                           break;
                case 'MJ': $clase = 'alMajor';
                           break;
                case 'MN': $clase = 'alMinor';
                           break;
                case 'WR': $clase = 'alWarning';
                           break;     
                default: $clase = '';
                         break;
              }
              echo "<tr class='".$clase."'>";
              
              $id = $fila['idalarma'];
              
              foreach ($campos as $key => $value) {   
                if ($campos[$key]['mostrar'] === 'si'){
                  $indice = $campos[$key]['nombre'];
                  
                  if ($indice === 'id'){
                    echo "<td>".$i."</td>";
                    $i++;
                  }
                  else {
                    if ($indice === 'dia'){
                      $dia = $fila[$indice];
                      $temp = explode('-', $dia);
                      $diaMostrar = $temp[2]."/".$temp[1]."/".$temp[0];
                      echo "<td>".$diaMostrar."</td>";
                    }
                    else {
                      if ($indice !== 'accion'){
                        echo "<td>".$fila[$indice]."</td>";
                      }
                    } 
                  }        
                } 
              }
              echo "<td><a href='editarAlarma.php?id=".$id."'>Editar</a></td>";
              echo "</tr>";
            }
            /// Fin del procesamiento de las filas con datos
            
            echo "<tr><td class='pieTabla' colspan='$totalCamposMostrar' id='btnExportar' name='btnExportar'><input type='button' value='Exportar'></td></tr>";
            echo "</table>";
          }
          else {
            echo "¡No hay registros a mostrar!<br>";
          }
        }
      }  
      $volver = "<br><a href='subirArchivo.php'>Volver a Inicio</a><br><br>";
      echo $volver;
    ?>
    </div>      
  </main>      
  <?php
  require_once ('footer.php');
  ?>  
  </body>
</html>


