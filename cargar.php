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
      
      if (isset($_GET['i'])){
        $inicio = $_GET['i'];
      } /// Fin if isset($_GET)
      else {
        $inicio = 0;
      } /// Fin else isset($_GET)
      
      /// Chequeo variable para saber desde donde vengo; si desde subirArchivo, desde el menú o desde editarAlarma
      /// Si es desde el primero, hago las validaciones y cargo el archivo pasado
      /// En ambos casos, muestro en pantalla los datos
      if (!(isset($_GET['i']))||($inicio === 1)){    
        $nombreArchivo0 = $_FILES['uploadedFile']['name'];
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
          $agujas = array("csv");
          $extMinuscula = mb_strtolower($extension);
          if (!(in_array($extMinuscula, $agujas))){
            echo "¡La extensión <strong>".mb_strtoupper($extension)."</strong> NO es válida!.<br>Por favor verifique!.<br>";
            $seguir = false;
            $finValidacion = true;
          }   
        } /// Fin validacion: extensión

        if (!($finValidacion)){
          /// Validación del tamaño del archivo a subir:
          $tamArchivo = $_FILES['uploadedFile']['size'];
          if ($tamArchivo > TAM_ARCHIVO){
            echo "ERROR. El archivo a subir ($tamArchivo bytes) es mayor al límite permitido por el sistema (".TAM_ARCHIVO." bytes).<br>";
            $seguir = false;
          }
        } /// Fin validacion: tamaño

        if ($seguir){
          $nombreArchivo = $nombre."_".date("dmY").".".$extension;
          $destino = $dirCargados."\\\\".$nombreArchivo;
          $continuar = true;
          
          if (file_exists($destino)) {
            echo "<br><h3>¡El fichero <span class='negrita'>$nombreArchivo</span> YA se proces&oacute;!.<br>Por favor verifique.</h3>";
            $continuar = false;
          } /// Fin if file_exists 

          if ($continuar === TRUE){
            if (!(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $destino))) {
              echo "Hubo un error en la copia del archivo.<br>Por favor verifique.<br>";
              return;
            } /// Fin if move_uploaded_file
            
            /// Levanto el nombre del nodo y su id.
            /// Como en el POST NO se pasan los atributos del option, se armó antes de enviar un value con todo.
            /// Ahora lo vuelvo a separar:
            $localidadTemp = $_POST['nodo'];
            $temp = explode('---', $localidadTemp);
            $localidad1 = $temp[0];
            $nombreCorto = $temp[1];
            $idnodo = $temp[2];
            $_SESSION['nodo'] = $localidad1;
            $_SESSION['idnodo'] = $idnodo;
            $_SESSION['archivo'] = $nombreArchivo;

            ///Armo consulta para la carga del archivo CSV a la base de datos:
//            $queryCargar = "load data infile '$destino' into table alarmas "
//                    . "fields terminated by ';' "
//                    . "optionally enclosed by '\"' "
//                    . "lines terminated by '\\r\\n' "
//                    . "ignore 3 lines "
//                    . "(nombre, compound, tipoAID, tipoAlarma, tipoCondicion, descripcion, afectacionServicio, @fechaCompleta, ubicacion, direccion, valorMonitoreado, nivelUmbral, periodo, datos, filtroALM, filtroAID) "
//                    . "set Dia=concat(year(now()), '-', substring(trim(@fechaCompleta), 1, 5)), Hora=concat(substring(trim(@fechaCompleta), 8, 2), ':', substring(trim(@fechaCompleta), 11, 2), ':', substring(trim(@fechaCompleta), 14, 2)), causa='', solucion='', usuario=".$_SESSION['user_id'].", nodo=".$_SESSION['idnodo'].", archivo='".$_SESSION['archivo']."', estado='Sin procesar';";
//            
            $queryCargar = "load data infile '$destino' into table alarmas "
                    . "fields terminated by ';' "
                    . "optionally enclosed by '\"' "
                    . "lines terminated by '\\r\\n' "
                    . "ignore 3 lines "
                    . "(nombre, compound, tipoAID, tipoAlarma, tipoCondicion, descripcion, afectacionServicio, @fechaCompleta, ubicacion, direccion, valorMonitoreado, nivelUmbral, periodo, datos, filtroALM, filtroAID) "
                    . "set Dia=concat(year(now()), '-', substring(trim(@fechaCompleta), 1, 5)), Hora=concat(substring(trim(@fechaCompleta), 8, 2), ':', substring(trim(@fechaCompleta), 11, 2), ':', substring(trim(@fechaCompleta), 14, 2)), causa='', solucion='', usuario= :user_id, nodo= :idnodo, fechaCarga= :fechaCarga, archivo= :archivo, estado='Sin procesar';";
            
            $sth = $pdo->prepare($queryCargar);
            $sth->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_STR);
            $sth->bindParam(':idnodo', $_SESSION['idnodo'], PDO::PARAM_STR);
            $sth->bindParam(':archivo', $_SESSION['archivo'], PDO::PARAM_STR);
            $sth->bindParam(':fechaCarga', $fechaCarga, PDO::PARAM_STR);
            
  
            $dato = array();
            try {
              //$result = $pdo->query($queryCargar);
              $result = $sth->execute();
            } catch (PDOException $e) {
                print "<br><strong>¡ERROR!:</strong> " . $e->getMessage() . "<br>Por favor, ¡verifique que el archivo a cargar tenga datos!.<br>";
                $volver = "<br><a href='subirArchivo.php'>Volver a Inicio</a><br><br>";
                echo $volver;
                die();
            } /// Fin catch consulta

            if ($result !== FALSE) {
              $dato["resultado"] = "Archivo correctamente subido a la base de datos.";
            } /// Fin if result !== false: carga correcta.  
            else {
              $dato["resultado"] = "ERROR DURANTE LA CARGA DE ARCHIVO";
              $mostrarError = true;
            } /// Fin result === FALSE: error en la carga del archivo
          }/// Fin if $continuar
          else {
            $seguir = false;
          }    
        }/// Fin if $seguir 
        
      }/// Fin del caso en que vengo desde subirArchivo. Fin de la carga (u error de la misma)
      
      /// Si no hubo problemas con la validación o vengo desde editarAlarma sigo:
      if ($seguir){    
        if (!(isset($_SESSION['nodo']))){
          $queryUltimo = "select nodos.idnodo, nodos.localidad, alarmas.archivo from alarmas inner join nodos on nodos.idnodo=alarmas.nodo order by idalarma desc limit 1";
          $datosUltimo = hacerSelect($queryUltimo);
          $registro = $datosUltimo['resultado'][0];
          $_SESSION['nodo'] = $registro['localidad'];
          $_SESSION['idnodo'] = $registro['idnodo'];
          $_SESSION['archivo'] = $registro['archivo'];
          echo "<h3>Aún no se ha cargado ningún archivo.<br>Se muestran las alarmas del último archivo cargado: ".$_SESSION['archivo']."</h3>";
        } /// Fin if isset $_SESSION

        echo "<br>
              <h2>Alarmas del nodo en: <span class='resaltado'>".$_SESSION['nodo']."</span><span>(".$_SESSION['archivo'].")</span></h2>
              <br>";
        
        /// Si hubo error muestro el mensaje de error. De lo contrario, muestro contenido del archivo:
        if ($mostrarError) {
          echo $dato["resultado"]."<br>";
        } /// Fin if mostrarError
        else {
          /// Agrego el archivo que reordena los campos:
          require_once('data/camposAlarmas.php');
          
          /// Armo la consulta
          $consulta = "select * from alarmas where archivo= ? order by dia desc, hora desc";
          $param = array($_SESSION['archivo']);
          $datos = hacerSelect($consulta, $param);
          $totalFilas = $datos['rows'];
          
          /// Si hay datos los muestro:
          if ($totalFilas > 0){
            echo "<form id='frmCargar' name='frmCargar' method='post' action='exportar.php'>";
            /// Comienzo tabla para mostrar la consulta:
            echo "<table class='tabla2'>";
            echo "<caption>Tabla con las alarmas del nodo</caption>";
            $i = 1;
            $totalCamposMostrar = 1;
            
            /// Muestro el encabezado:
            echo "<tr>";
            foreach ($camposAlarmas as $key => $value) {   
              if ($camposAlarmas[$key]['mostrarListado'] === 'si'){
                $clase = '';
                $totalCamposMostrar++;
                if ($camposAlarmas[$key]['nombreDB'] === 'id'){
                  $clase = "class='tituloTablaIzquierdo'";
                }
                else {
                  if ($camposAlarmas[$key]['nombreDB'] === 'accion'){
                    $clase = "class='tituloTablaDerecho'";
                  }
                }
                echo "<th $clase>".$camposAlarmas[$key]['nombreMostrar']."</th>";
              }
            } /// Fin foreach camposAlarmas para encabezados
            echo "</tr>";
            /// Fin encabezados
            
            $keys = array();
            foreach ($datos['resultado'] as $key0 => $fila0 ) {
              $idalarma0 = $fila0['idalarma'];
              $keys[] = $idalarma0;
            } /// Fin foreach datos para sacar los keys
            
            /// Comienzo proceso de cada fila:
            foreach ($datos['resultado'] as $key1 => $fila ) {
              
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
              } /// Fin switch tipoAlarma

              echo "<tr class='".$clase."'>";
              
              $idalarma = $fila['idalarma'];
                                          
              foreach ($camposAlarmas as $key => $value) {   
                if ($camposAlarmas[$key]['mostrarListado'] === 'si'){
                  $indice = $camposAlarmas[$key]['nombreDB'];
                  
                  switch ($indice){
                    case 'id':  echo "<td>".$i."</td>";
                                $i++;
                                break;
                    case 'dia': $dia = $fila[$indice];
                                $temp = explode('-', $dia);
                                $diaMostrar = $temp[2]."/".$temp[1]."/".$temp[0];
                                echo "<td>".$diaMostrar."</td>";         
                                break;
                    case 'usuario': echo "<td></td>";
                                    break;           
                    case 'nodo':  echo "<td></td>";
                                  break;
                    case 'fechaCarga':  $dia1 = $fila[$indice];
                                        $temp1 = explode('-', $dia1);
                                        $diaMostrar1 = $temp1[2]."/".$temp1[1]."/".$temp1[0];
                                        echo "<td>".$diaMostrar1."</td>";         
                                        break;            
                    case 'causa': if ($fila['causa'] === ''){
                                    echo "<td>No Ingresada</td>";
                                  }
                                  else {
                                    echo "<td>".$fila['causa']."</td>";
                                  }
                                  break;
                    case 'solucion': if ($fila['solucion'] === ''){
                                        echo "<td>No ingresada</td>";
                                      }
                                      else {
                                        echo "<td>".$fila['solucion']."</td>";
                                      }
                                      break;
                    case 'estado':  if ($fila['estado'] === 'Sin procesar'){
                                      $claseEstado = "sinProcesar";
                                    }
                                    else {
                                      $claseEstado = 'procesada';
                                    }
                                    echo "<td name='estado' class='".$claseEstado."'>".$fila[$indice]."</td>";
                                    break;                  
                    case 'accion':  $j = $i - 1;
                                    $parAlCodif = "al=".base64_encode($idalarma);
                                    $parKeysCodif = "&k=".base64_encode(serialize($keys));
                                                                        
                                    $url = "editarAlarma.php?".$parAlCodif.$parKeysCodif;
                                    $parCod = base64_encode($url);//echo "url: ".$url."<br>url encoded: ".$parCod."<br>";
                                    echo "<td><a href='".$url."' target='_blank'>Editar</a></td>";
                                    break;         
                    default:  echo "<td>".$fila[$indice]."</td>";
                              break;
                  } /// Fin switch indice      
                } /// Fin if mostrarListado 
              } /// Fin foreach camposAlarmas
              
              echo "</tr>";
            } /// Fin del procesamiento de las filas con datos
            
            echo "<tr><td class='pieTabla' colspan='$totalCamposMostrar' id='btnExportar' name='btnExportar'><input type='button' value='Exportar'></td></tr>";
            echo "</table>";
            echo "</form>";
          } /// Fin if totalFilas > 0
          else {
            echo "¡No hay registros a mostrar!<br>";
          } /// Fin else totalFilas > 0
          
        } /// Fin else mostrarError
        
      } /// Fin if seguir
      
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


