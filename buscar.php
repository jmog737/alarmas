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
?>

<!DOCTYPE html>
<html>
<?php 
require_once ('head.php');
require_once ('data/config.php');
require_once ('data/pdo.php');

/// Recupero los parámetros pasados:
$mensaje = $_POST['mensaje'];
$consulta = $_POST['query'];
$offset = $_POST['offset'];

/// Parámetros asociados a la consulta:
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

$log = "NO";
/// Armo array con los parámetros según corresponda acorde a la consulta:
$parametros = array();
if ($source !== 'TODOS'){
  $parametros[] = $source;
  $temp = stripos($source, '.csv');
  if ($temp === FALSE){
    $consultaNodo = "select localidad, nombre from nodos where idnodo=?";
    $paramNodo = array($source);
    $datosNodo = json_decode(hacerSelect($consultaNodo, $log, $paramNodo), true);
    $nombreNodo = $datosNodo['resultado'][0]['localidad'];
    $nombreNodoTemp = $datosNodo['resultado'][0]['nombre'];
    $temp11 = explode("#", $nombreNodoTemp);
    $temp21 = explode("-", $temp11[0]);
    $nombreCorto = $temp21[0];
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

$paramSerial = serialize($parametros);
/// Rearmo la consulta SOLO para conocer el total de datos:
$temp0 = explode("from", $consulta);
$consultaTotal = "select count(*) from".$temp0[1];
$datosTotal = json_decode(hacerSelect($consultaTotal, $log, $parametros), true);
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
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid'>
      <br>  
      <h2>Resultado de la consulta:</h2>
      <h3><?php $mensajeNuevo = $mensaje." (Total: ".$totalDatos.")"; echo $mensajeNuevo ?></h3>
      
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
        echo "<table class='tabla2'>";
        echo "<caption>Tabla con el resultado de la consulta</caption>";
        $i = $primerRegistro;
        $totalCamposMostrar = 1;

        /// Muestro el encabezado:
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

        if ($nombreNodo === 'TODOS'){
          $nodoAnterior = '';
        }
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
            case 'CR': $clase = 'alCritica';
                       break;
            case 'MJ': $clase = 'alMajor';
                       break;
            case 'MN': $clase = 'alMinor';
                       break;
            case 'WR': $clase = 'alWarning';
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
                                $parOrigen = "&o=".base64_encode('buscar');
                                //$parKeysCodif = "&k=".base64_encode(serialize($keys));
                                $parConsulta = "&c=".base64_encode($consulta);
                                $parParam = "&p=".base64_encode($paramSerial);

                                $url = "editarAlarma.php?".$parAlCodif.$parOrigen.$parConsulta.$parParam;
                                echo "<td><a href='".$url."' target='_blank'>Editar</a></td>";
                                break;         
                default:  echo "<td>".$fila[$indice]."</td>";
                          break;
              } /// Fin switch indice      
            } /// Fin if mostrarListado 
          } /// Fin foreach camposAlarmas

          echo "</tr>";
        } /// Fin del procesamiento de las filas con datos

        echo "<tr><td class='pieTabla' colspan='$totalCamposMostrar' id='btnExportarBuscar' name='btnExportar'><input type='button' class='btn btn-success' value='Exportar'></td></tr>";
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