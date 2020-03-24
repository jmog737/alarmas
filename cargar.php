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
require_once ('data/cargarArchivo.php');
?>
  <body onload="resizeTextArea()">
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid table-responsive'>
    <?php
      $seguir = true;
      $finValidacion = false;
      $mostrarError = false;
      $mensaje = '';
      
      if (isset($_GET['i'])){
        $inicio = $_GET['i'];
      } /// Fin if isset($_GET)
      else {
        $inicio = 0;
      } /// Fin else isset($_GET)
      
      /// Chequeo variable para saber desde donde vengo; si desde subirArchivo, desde el menú, desde editarAlarma o desde el propio cargar
      /// Si es desde el primero, hago las validaciones y cargo el archivo pasado
      /// En todos los casos, muestro en pantalla los datos
      if (((!(isset($_GET['i']))||($inicio === 1))&&(!isset($_POST['offset'])))){
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
          $agujas = array("csv", "xls");
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
          /// Levanto el nombre del nodo y su id.
          /// Como en el POST NO se pasan los atributos del option, se armó antes de enviar un value con todo.
          /// Ahora lo vuelvo a separar:
          $localidadTemp = $_POST['nodo'];
          $temp0 = explode('---', $localidadTemp);
          $localidad1 = $temp0[0];
          $idnodo = $temp0[2];
          
          $buscar = strpos($temp0[1], "#OCS");
          if ($buscar !== FALSE){
            $sufijo = "OCS";
          }
          else {
            $sufijo = "PSS32";
          }
          $temp1 = explode("#", $temp0[1]);
          $temp2 = str_replace("-", "", $temp1[0]);
          $nombreCorto = $temp2."_".$sufijo;
          
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
            if (!(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $destino))) {
              echo "Hubo un error en la copia del archivo.<br>Por favor verifique.<br>";
              return;
            } /// Fin if move_uploaded_file
            
            $_SESSION['nodo'] = $localidad1;
            $_SESSION['nodoCorto'] = $nombreCorto;
            $_SESSION['idnodo'] = $idnodo;
            $_SESSION['archivo'] = $nombreArchivo;
        
            $carga = cargarArchivo($destino);
            if ($carga !== false){
              if ($carga["exito"] === false){
                $mostrarError = true;
                $mensajeError = "<strong>¡ERROR!:</strong> ".$mensaje."<br>";
              } /// Fin if exito === false ---> Si dio false indica que hubo un error con alguno de los registros o uno inesperado en el fgets
              else {
                if (($carga["duplicados"] === 0)&&($carga["errores"] === 0)){
                  $mensaje = "<h3>".$carga["mensaje"]."</h3>";
                }
                else {
                  
                  $mensaje = "<mark>Alarmas NUEVAS: ".$carga["cargados"]."</mark><span class='badge  badge-success'>NUEVAS</span><br>";
                  $mensaje .= "Alarmas duplicadas: ".$carga["duplicados"]."<br>";
                  $mensaje .= $carga["mensaje"]."<br>";
                  $mensaje .= "<br>Total de alarmas: ".$carga["lineas"]."<br>";
                }   
              } /// Fin del ese if exito === false      
            } /// Fin if ($carga !== false) - si es false indica que dio error file_exists
            else {
              $mensaje = "<br><strong>¡ERROR!:</strong> No existe el archivo $destino.";
              echo "<h3>".$mensaje."</h3><br>";
              $seguir = false;
            }
          }/// Fin if $continuar
          else {
            $seguir = false;
          }    
        }/// Fin if $seguir 
        
      }/// Fin del caso en que vengo desde subirArchivo. Fin de la carga (u error de la misma)
     
      /// Si no hubo problemas con la validación o vengo desde editarAlarma sigo:
      if ($seguir){    
        if (!(isset($_SESSION['nodo']))){
          $queryUltimo = "select nodos.idnodo, nodos.nombre, nodos.localidad, alarmas.archivo from alarmas inner join nodos on nodos.idnodo=alarmas.nodo order by idalarma desc limit 1";
          $log = "NO";
          $datosUltimo = json_decode(hacerSelect($queryUltimo, $log), true);
          $registro = $datosUltimo['resultado'][0];
          $temp11 = explode("#", $registro['nombre']);
          $temp21 = str_replace("-", "", $temp11[0]);
          $buscar = strpos($registro['nombre'], "#OCS");
          if ($buscar !== FALSE){
            $sufijo = "OCS";
          }
          else {
            $sufijo = "PSS32";
          }
          $nombreCorto = $temp21."_".$sufijo;

          $_SESSION['nodo'] = $registro['localidad'];
          $_SESSION['nodoCorto'] = $nombreCorto;
          $_SESSION['idnodo'] = $registro['idnodo'];
          $_SESSION['archivo'] = $registro['archivo'];
          $mensaje = "<h3>Aún no se han cargado archivos.<br>Se muestran las alarmas del último archivo cargado: ".$_SESSION['archivo']."</h3>";
          $tituloPagina = "Resultado de la &uacute;ltima carga";
        } /// Fin if isset $_SESSION
        else {
          $tituloPagina = "Resultado de la carga:";
        }

        /// Si hubo error muestro el mensaje de error. De lo contrario, muestro contenido del archivo:
        if ($mostrarError) {
          echo $mensajeError;
        } /// Fin if mostrarError
        else {
          echo "<h2>".$tituloPagina."</h2>";
          /// Agrego el archivo que reordena los campos:
          require_once('data/camposAlarmas.php');
          
          /// Armo la consulta
          $consulta = "select * from alarmas where archivo= ? order by dia desc, hora desc, idalarma";
          $param = array($_SESSION['archivo']);
          /// Serializo los parámetros para poder pasarlos en el post:
          $paramSerial = serialize($param);
          
          $log = "NO";
          /// Rearmo la consulta SOLO para conocer el total de datos:
          $temp0 = explode("from", $consulta);
          $consultaTotal = "select count(*) from".$temp0[1];
          $datosTotal = json_decode(hacerSelect($consultaTotal, $log, $param), true);
          $totalDatos = $datosTotal['rows'];
          
          $tamPagina = $_SESSION['tamPagina'];
          $totalPaginas = (int)ceil($totalDatos/$tamPagina);
          if (!isset($_POST['page'])){
            $page = 1;
          }
          else {
            $page = (int)$_POST['page'];
          }
          if ($page === $totalPaginas){
            $ultimoRegistro = $totalDatos;
          }
          else {
            $ultimoRegistro = $page*$tamPagina;
          }
          if (isset($_POST['offset'])){
            $offset = $_POST['offset'];
          }
          else {
            $offset = "-1";
          }
          
          $primerRegistro = ($page-1)*$tamPagina + 1;
          if ($tamPagina > $totalDatos){
            $ultimoRegistro = $totalDatos;
            $consultaNueva = $consulta;
          }
          else {
            $consultaNueva = $consulta." limit ".$tamPagina;
            if ($offset !== "-1"){
              $consultaNueva .= " offset ".$offset;
            }
          }

          /// Ejecuto la consulta regular para recuperar los datos, PERO la limito a mostrar la primer página:
          $datos = json_decode(hacerSelect($consultaNueva, $log, $param), true);
          $mensajeNuevo = '';
          
          //$tituloReporte = "Alarmas del archivo ".$_SESSION['archivo']." [".$_SESSION['nodo']."]";
          $tituloReporte = "Alarmas cargadas en ".$_SESSION['nodo']." [desde: ".$_SESSION['archivo']."]";
          $mensajeNuevo = $tituloReporte."<br>(Total: ".$totalDatos.")";
          
          echo "<h4>".$mensaje."</h4><br>";
          if ((isset($carga))&&($carga["largoRegistros"] !== '')){
            echo "<h3>Los siguientes registros NO fueron cargados y requieren su atención:</h3><br>";
            echo "<h5>".$carga["largoRegistros"]."</h5><br>";
          }
          echo "<h3>".$mensajeNuevo."</h3>";
          echo "<br>";
          
          
          /// Si hay datos los muestro:
          if ($totalDatos > 0){
            if ($totalPaginas > 1){
              $rango = "<h5 id='rango' class='rango'>(P&aacute;gina ".$page."/".$totalPaginas.": registros del ".$primerRegistro." al ".$ultimoRegistro.")</h5>";   
            }
            else {
              $rango = "<h5 id='rango' class='rango'>(P&aacute;gina ".$page."/".$totalPaginas.": registros del ".$primerRegistro." al ".$ultimoRegistro.")</h5>";
            }
            echo $rango;
            echo "<br>";
            
            echo "<form id='frmCargar' name='frmCargar' method='post'>";
            /// Comienzo tabla para mostrar la consulta:
            /// Comienzo tabla para mostrar la consulta:
            echo "<div name='table-content' class='table-responsive'>";
            echo "<table id='tblCargar' name='tblCargar' class='tabla2 table table-hover w-auto'>";
            echo "<caption>Tabla con las alarmas del nodo</caption>";
            $i = $primerRegistro;
            $totalCamposMostrar = 1;
            
            /// Muestro el encabezado:
            echo "<thead>";
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
                echo "<th scope='row' $clase>".$camposAlarmas[$key]['nombreMostrar']."</th>";
              }
            } /// Fin foreach camposAlarmas para encabezados
            echo "</tr>";
            echo "</thead>";
            /// Fin encabezados
            
//            $keysAlarmas = array();
//            foreach ($datos['resultado'] as $key0 => $fila0 ) {
//              $idalarma0 = $fila0['idalarma'];
//              $keysAlarmas[] = $idalarma0;
//            } /// Fin foreach datos para sacar los keys
            echo "<tbody>";
            /// Comienzo proceso de cada fila:
            foreach ($datos['resultado'] as $key1 => $fila ) {
              
              /// Extraigo tipo de alarma para poder resaltar en consecuencia:
              $tipoAlarma = $fila['tipoAlarma'];
              switch ($tipoAlarma) {
                case 'CR': $clase = 'table-danger';
                           break;
                case 'MJ': $clase = 'alMajor';
                           break;
                case 'MN': $clase = 'table-warning';
                           break;
                case 'WR': $clase = 'table-info';
                           break;    
                case 'NA': $clase = 'alNotAlarmed';
                           break; 
                case 'NR': $clase = 'alNotReported';
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
                    case 'id':  echo "<td nowrap>".$i." - <input type='checkbox' name='update' value='".$fila['idalarma']."'></td>";
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
                                    $val = '';  
                                  }
                                  else {
                                    $val = $fila['causa'];
                                  }
                                  echo "<td>";
                                  echo "  <textarea name='causa' class='agrandar md-textarea ".$clase."' placeholder='Causa' title='Causa posible' idalarma=".$fila['idalarma'].">".$val."</textarea>";
                                  echo "</td>";
                                  break;
                    case 'solucion': if ($fila['solucion'] === ''){
                                        $val = '';  
                                      }
                                      else {
                                        $val = $fila['solucion'];
                                      }
                                      echo "<td>";
                                      echo "  <textarea name='solucion' class='agrandar md-textarea ".$clase."' placeholder='Solución' title='Solución posible' idalarma=".$fila['idalarma'].">".$val."</textarea>";
                                      echo "</td>";
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
                                    $parOrigen = "&o=".base64_encode('cargar');
                                    $parConsulta = "&cAlarm=".base64_encode($consulta);
                                    $paramCodif = "&pAlarm=".base64_encode($paramSerial);
                                    //$parKeysCodif = "&kAlarm=".base64_encode(serialize($keysAlarmas));
                                                                        
                                    //$url = "editarAlarma.php?".$parAlCodif.$parOrigen.$parKeysCodif;
                                    $url = "editarAlarma.php?".$parAlCodif.$parOrigen.$parConsulta.$paramCodif;
                                    //$parCod = base64_encode($url);//echo "url: ".$url."<br>url encoded: ".$parCod."<br>";
                                    echo "<td><a href='".$url."' role='button' class='btn btn-sm btn-info' target='_blank'>Editar</a></td>";
                                    break;         
                    default:  echo "<td>".$fila[$indice]."</td>";
                              break;
                  } /// Fin switch indice      
                } /// Fin if mostrarListado 
              } /// Fin foreach camposAlarmas
              
              echo "</tr>";
            } /// Fin del procesamiento de las filas con datos
            echo "</tbody>";
            
            echo "<tfoot>";           
            echo "<tr>";
            echo "  <td class='pieTabla' colspan='$totalCamposMostrar'>";
            echo "    <button type='button' title='Para generar el reporte en PDF/EXCEL.' id='btnExportarCargar' name='btnExportar' class='btn btn-dark-green btn-md' value=''>Exportar</button>";
            echo "    <button type='button' title='Usar para Actualizar SOLO las alarmas QUE SE HAYAN MARCADO como similares, y que tienen misma causa/solución.\n\n¡¡¡SOLO hay que agregar la causa/solución en una de ellas!!!.' id='btnActualizarCargar' name='btnActualizar' class='btn blue accent-4 btn-md white-text' value=''>Actualizar SMILARES</button>";
            echo "    <button type='button' title='Usar para Actualizar TODOS los registros sin importar si tienen misma causa/solución' id='btnActualizarTodoCargar' name='btnActualizarTodo' class='btn btn-md btn-danger white-text' value=''>Actualizar TODOS</button>";
            echo "  </td>";
            echo "</tr>";    
            echo "</tfoot>";
            
            echo "</table>";
            echo "</div>";
            
            echo "<input type='hidden' name='query' value='".$consulta."'>";
            echo "<input type='hidden' name='param' value='".$paramSerial."'>";
            echo "<input type='hidden' name='mensaje' value='".$tituloReporte."'>";
            echo "<input type='hidden' name='nodo' value='".$_SESSION['nodo']."'>";
            echo "<input type='hidden' name='nodoCorto' value='".$_SESSION['nodoCorto']."'>";
            echo "<input type='hidden' name='offset' id='offset' value=''>";
            echo "<input type='hidden' name='page' id='page' value=''>";
            echo "<input type='hidden' name='origen' value='cargar'>";
            
            echo "</form>";
            
            ///********************************************* Comienzo paginación *****************************************************************
            if ($totalPaginas > 1) {
              $paginas = '<div class="pagination" id="paginas">';
              $paginas .= '<input style="display: none" type="text" id="totalPaginas" value="'.$totalPaginas.'">';
              $paginas .= '<input style="display: none" type="text" id="totalRegistros" value="'.$totalDatos.'"><ul>';
              if ($page !== 1) {
                $paginas .= '<li><a name="cargar" class="paginate anterior" data="'.($page-1).'">Anterior</a></li>';
              }

              for ($k=1;$k<=$totalPaginas;$k++) {
                if (($page === 1)&&($k === 1)){
                  $inhabilitarPrimero = ' inhabilitar';
                }
                else {
                  $inhabilitarPrimero = '';
                }
                if (($page === $totalPaginas)&&($k === $totalPaginas)){
                  $inhabilitarUltimo = ' inhabilitar';
                }
                else {
                  $inhabilitarUltimo = '';
                }
                if ($page === $k) {
                  //si muestro el índice de la página actual, no coloco enlace
                  $paginas .= '<li ><a name="cargar" class="paginate pageActive'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
                }  
                else {
                  //si el índice no corresponde con la página mostrada actualmente,
                  //coloco el enlace para ir a esa página
                  $paginas .= '<li><a name="cargar" class="paginate'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
                }
              }

              if ($page !== $totalPaginas) {
                $paginas .= '<li><a name="cargar" class="paginate siguiente" data="'.($page+1).'">Siguiente</a></li>';
              } 
              $paginas .= '</ul>';
              $paginas .= '</div><br>';
              echo $paginas;
            }
            ///************************************************** FIN paginación *****************************************************************
          } /// Fin if totalFilas > 0
          else {
            echo "<h3>¡No hay registros a mostrar!</h3><br>";
          } /// Fin else totalFilas > 0       
        } /// Fin else mostrarError       
      } /// Fin if seguir
      
      $volver = "<a href='subirArchivo.php'>Volver a Inicio</a><br><br>";
      echo $volver;
    ?>
    </div>      
  </main>      
  <?php
  require_once ('footer.php');
  ?>  
  </body>
</html>


