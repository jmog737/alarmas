<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file buscar.php
*  @brief Archivo que se encarga de ejecutar y mostrar las consultas a la base de datos.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Agosto 2019
*
*******************************************************/

require_once ('data/config.php');
require_once ('data/pdo.php');
?>

<!DOCTYPE html>
<html>
<?php 
require_once ('head.php');
?>
  
<body onload="resizeTextArea()">
<?php
  require_once ('header.php');
  
/// Recupero los parámetros pasados:
if (isset($_POST['mensaje'])){
  $mensaje = $_POST['mensaje'];
};
if (isset($_POST['query'])){
  $consulta = $_POST['query'];
}  
if (isset($_POST['offset'])){
  $offset = $_POST['offset'];
}  

/// Parámetros asociados a la consulta:
if (isset($_POST["param"])){
  $param = $_POST["param"];
  $t = stripos($param, "&");
  if ($t === FALSE){
    $paramArray = unserialize($param);
  }
  else {
    $paramArray = explode("&", $param);
  }
  
  $source = $paramArray[0];
  $inicio = $paramArray[1];
  $fin = $paramArray[2];
  $tipo = $paramArray[3];
  $user = $paramArray[4];
  $nombreAlarma = $paramArray[5];
  $conditionAlarma = $paramArray[6];
  $aidAlarma = $paramArray[7];
  $estado = $paramArray[8];
}

$log = "NO";
/// Armo array con los parámetros según corresponda acorde a la consulta:
$parametros = array();
if ($source !== 'TODOS'){
  $parametros[] = $source;
  $temp = stripos($source, '.csv');
  $temp2 = stripos($source, '.xls');
  if (($temp === FALSE)&&($temp2 === FALSE)){
    $consultaNodo = "select localidad, nombre from nodos where idnodo=?";
    $paramNodo = array($source);
    $datosNodo = json_decode(hacerSelect($consultaNodo, $log, $paramNodo), true);
    $nombreNodo = $datosNodo['resultado'][0]['localidad'];
    $nombreNodoTemp = $datosNodo['resultado'][0]['nombre'];
    $temp11 = explode("#", $nombreNodoTemp);
    $buscar = strpos($nombreNodoTemp, "#OCS");
    if ($buscar !== FALSE){
      $sufijo = "OCS";
    }
    else {
      $sufijo = "PSS32";
    }
    $temp2 = str_replace("-", "", $temp11[0]);
    $nombreCorto = $temp2."_".$sufijo;
    $m0 = explode("[", $mensaje);
    $m1 = explode("]", $m0[1]);
    $mensaje = $m0[0]."[".$nombreCorto."]".$m1[1];
  }
  else {
    $nombreNodo = "archivo";
    $nombreCorto = "archivo";
  }
}
else {
  $nombreNodo = 'TODOS';
  $nombreCorto = 'TODOS';
  /// Consulto por el listado del nodo para poder hacer el "cambio de nodo":                                                                                                                                                                           
  $consultaNodos = "select distinct idnodo, localidad, nombre from nodos";
  $datosNodos = json_decode(hacerSelect($consultaNodos, $log), true);
  $nombreNodos = $datosNodos['resultado'];
  $arrayNodos = array();
  foreach ($nombreNodos as $ind => $valor){
    $arrayNodos[$valor['idnodo']] = $valor['localidad']." [".$valor['nombre']."]";
  }
}

if ($fin === 'FIN'){
  if ($inicio !== 'INICIO'){
    $parametros[] = $inicio;
  }
}
else {
  $parametros[] = $inicio;
  $parametros[] = $fin;
}

if ($tipo !== 'TIPO'){
  $parametros[] = $tipo;
}

if ($user !== 'USUARIO'){
  $parametros[] = $user;
}

if ($nombreAlarma !== 'NOMBRE'){
  $parametros[] = $nombreAlarma;
}

if ($conditionAlarma !== 'CONDITION'){
  $parametros[] = $conditionAlarma;
}

if ($aidAlarma !== 'AID'){
  $parametros[] = $aidAlarma;
}

if ($estado !== 'ESTADO'){
  $parametros[] = $estado;
}

$paramSerial = serialize($parametros);
/// Rearmo la consulta SOLO para conocer el total de datos:
$temp0 = explode("from", $consulta);
$consultaTotal = "select count(*) from".$temp0[1];
$datosTotal = json_decode(hacerSelect($consultaTotal, $log, $parametros), true);
$totalDatos = $datosTotal['rows'];

/// Rearmo la consulta SOLO para conocer el total de alarmas YA procesadas:
$consultaProcesados0 = "select count(*) from".$temp0[1];
$busco = strpos($consultaProcesados0, "where ");
if ($busco !== FALSE){
  $temp00 = explode("where ", $consultaProcesados0);
  $consultaProcesados = $temp00[0]." where estado='Procesada' and ".$temp00[1];
}
else {
  $t0 = explode("from ", $consultaProcesados0);
  $t1 = explode(" ", $t0[1]);
  $tabla = array_shift($t1);
  $t2 = implode(" ", $t1);
  $consultaProcesados = $t0[0]."from ".$tabla." where estado='Procesada' ".$t2;
}
$datosProcesados = json_decode(hacerSelect($consultaProcesados, $log, $parametros), true);
$totalProcesados = $datosProcesados['rows'];

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
$datos = json_decode(hacerSelect($consultaNueva, $log, $parametros), true);
$mensajeNuevo = '';
?>
  
  <main>
    <div id='main-content' class='container-fluid'>
      <br>  
      <h2>Resultado de la consulta:</h2>
      <h3><?php $mensajeNuevo = $mensaje." (Procesadas: ".$totalProcesados."/".$totalDatos.")"; echo $mensajeNuevo ?></h3>
      
      <?php
      /// Si hay datos los muestro:
      if ($totalDatos > 0){
        require_once('data/camposAlarmas.php');
        
        if ($totalPaginas > 1){
          $rango = "<h5 id='rango' class='rango'>(P&aacute;gina ".$page."/".$totalPaginas.": registros del ".$primerRegistro." al ".$ultimoRegistro.")</h5>";   
        }
        else {
          $rango = "<h5 id='rango' class='rango'>(P&aacute;gina ".$page."/".$totalPaginas.": registros del ".$primerRegistro." al ".$ultimoRegistro.")</h5>";
        }
        echo $rango;
        echo "<br>";
        echo "<form id='frmResultado' name='frmResultado' method='post'>";
        
        /// Comienzo tabla para mostrar la consulta:
        echo "<div name='table-content' class='table-responsive'>";
        echo "<table id='tblResultado' name='tblResultado' class='tabla2 table table-hover w-auto'>";
        echo "<caption>Tabla con el resultado de la consulta</caption>";
        
        $i = $primerRegistro;
        $totalCamposMostrar = 0;

        /// Muestro el encabezado:
        echo "<thead>";
        echo "<tr>";
        foreach ($camposAlarmas as $key => $value) {   
          if ($camposAlarmas[$key]['mostrarListado'] === 'si'){
            $clase = "";
            $totalCamposMostrar++;
            if ($camposAlarmas[$key]['nombreDB'] === 'id'){
              $clase .= "class='tituloTablaIzquierdo'";
            }
            else {
              if ($camposAlarmas[$key]['nombreDB'] === 'accion'){
                $clase .= "class='tituloTablaDerecho'";
              }
            }
            echo "<th scope='col' $clase>".$camposAlarmas[$key]['nombreMostrar']."</th>";
          }
        } /// Fin foreach camposAlarmas para encabezados
        echo "</tr>";
        echo "</thead>";
        /// Fin encabezados

        $keys = array();
        foreach ($datos['resultado'] as $key0 => $fila0 ) {
          $idalarma0 = $fila0['idalarma'];
          $keys[] = $idalarma0;
        } /// Fin foreach datos para sacar los keys

        if ($nombreNodo === 'TODOS'){
          $nodoAnterior = '';
        }
        
        echo "<tbody>";
        /// Comienzo proceso de cada fila:
        foreach ($datos['resultado'] as $key1 => $fila ) {
          if (isset($nodoAnterior)&&($nodoAnterior === '')){
            $nodoAnterior = $fila['nodo'];
            echo "<tr><th class='subTituloTabla1' colspan='$totalCamposMostrar'>$arrayNodos[$nodoAnterior]</th></tr>";
          }
          $nodoActual = $fila['nodo'];
          if (isset($nodoAnterior)&&($nodoActual !== $nodoAnterior)){
            $nodoAnterior = $nodoActual;
            echo "<tr><th class='subTituloTabla1' colspan='$totalCamposMostrar'>$arrayNodos[$nodoAnterior]</th></tr>";
            //$i = 1;
          }      
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
              //$nodoActual = $fila['nodo'];
              
              switch ($indice){
                case 'id':  echo "<td nowrap>".$i." - ";
                            echo "<input type='checkbox' name='update' value='".$fila['idalarma']."'>";
                            echo "</td>";
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
                                  echo "  <textarea name='solucion' class='agrandar md-textarea ".$clase."' placeholder='Solucion' title='Solución posible' idalarma=".$fila['idalarma'].">".$val."</textarea>";
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
                                $parOrigen = "&o=".base64_encode('buscar');
                                //$parKeysCodif = "&k=".base64_encode(serialize($keys));
                                $parConsulta = "&cAlarm=".base64_encode($consulta);
                                $parParam = "&pAlarm=".base64_encode($paramSerial);

                                $url = "editarAlarma.php?".$parAlCodif.$parOrigen.$parConsulta.$parParam;
                                echo "<td class='align-middle'><a href='".$url."' role='button' class='btn btn-sm btn-info' target='_blank'>Editar</a></td>";
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
        echo "    <button type='button' title='Para generar el reporte en PDF/EXCEL.' id='btnExportarBuscar' name='btnExportar' class='btn btn-dark-green btn-md' value=''>Exportar</button>";
        echo "    <button type='button' title='Usar para Actualizar SOLO las alarmas que se hayan marcado como similares, y que tienen misma causa/solución.\n\n¡¡¡SOLO hay que agregar la causa/solución en una de ellas!!!.' id='btnActualizarBuscar' name='btnActualizar' class='btn blue accent-4 btn-md white-text' value=''>Actualizar SMILARES</button>";
        echo "    <button type='button' title='Usar para Actualizar TODOS los registros sin importar si tienen misma causa/solución' name='btnActualizarTodo' class='btn btn-md btn-danger white-text' value=''>Actualizar TODOS</button>";
        echo "  </td>";
        echo "</tr>";
        echo "</tfoot>";
        
        echo "</table>";

        echo "<input type='hidden' name='query' id='query' value='".$consulta."'>";
        echo "<input type='hidden' name='param' id='param' value='".$param."'>";
        echo "<input type='hidden' name='paramSerial' id='paramSerial' value='".$paramSerial."'>";
        echo "<input type='hidden' name='offset' id='offset' value=''>";
        echo "<input type='hidden' name='page' id='page' value=''>";
        echo "<input type='hidden' name='mensaje' id='mensaje' value='".$mensaje."'>";
        
        echo "<input type='hidden' name='nodo' value='".$nombreNodo."'>";
        echo "<input type='hidden' name='nodoCorto' value='".$nombreCorto."'>";
        echo "<input type='hidden' name='origen' value='buscar'>";
        
        echo "</form>";
        
        ///********************************************* Comienzo paginación *****************************************************************
        if ($totalPaginas > 1) {
          $paginas = '<div class="pagination" id="paginas">';
          $paginas .= '<input style="display: none" type="text" id="totalPaginas" value="'.$totalPaginas.'">';
          $paginas .= '<input style="display: none" type="text" id="totalRegistros" value="'.$totalDatos.'"><ul>';
          if ($page !== 1) {
            $paginas .= '<li><a name="buscar" class="paginate anterior" data="'.($page-1).'">Anterior</a></li>';
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
              $paginas .= '<li ><a name="buscar" class="paginate pageActive'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
            }  
            else {
              //si el índice no corresponde con la página mostrada actualmente,
              //coloco el enlace para ir a esa página
              $paginas .= '<li><a name="buscar" class="paginate'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
            }
          }
          
          if ($page !== $totalPaginas) {
            $paginas .= '<li><a name="buscar" class="paginate siguiente" data="'.($page+1).'">Siguiente</a></li>';
          } 
          $paginas .= '</ul>';
          $paginas .= '</div><br>';
          echo $paginas;
        }
        echo "</div>";
        ///************************************************** FIN paginación *****************************************************************
      } /// Fin if totalFilas > 0
      else {
        echo "<h3>¡No hay registros a mostrar!</h3><br>";
      } /// Fin else totalFilas > 0
      
    $volver = "<a href='consultas.php'>Volver a Consultas</a><br><br>";
    echo $volver;
    ?>
    </div>      
  </main>      
  <?php
  require_once ('footer.php');
  ?>  
  </body>
</html>