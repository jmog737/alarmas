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
/// Parámetros asociados a la consulta:
$param = $_POST['param'];
$paramArray = explode("&", $param);
$source = $paramArray[0];
$inicio = $paramArray[1];
$fin = $paramArray[2];
$tipo = $paramArray[3];
$user = $paramArray[4];

$log = false;
/// Armo array con los parámetros según corresponda acorde a la consulta:
$parametros = array();
if ($source !== 'TODOS'){
  $parametros[] = $source;
  $temp = stripos($source, '.csv');
  if ($temp === FALSE){
    $consultaNodo = "select localidad from nodos where idnodo=?";
    $paramNodo = array($source);
    $datosNodo = json_decode(hacerSelect($consultaNodo, $log, $paramNodo), true);
    $nombreNodo = $datosNodo['resultado'][0]['localidad'];
  }
}
else {
  $nombreNodo = 'TODOS';
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

/// Mando o no el array con los parámetros según la consulta:
//if (count($parametros) === 0){
//  $datos = json_decode(hacerSelect($consulta, $log), true);
//}
//else {
$datos = json_decode(hacerSelect($consulta, $log, $parametros), true);
//}

$totalFilas = $datos['rows'];
          
?>
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid'>
      <br>  
      <h2>Resultado de la consulta:</h2>
      <h3><?php $mensaje .= " (Total: ".$totalFilas.")"; echo $mensaje ?></h3>
      <br>
      <?php
      /// Si hay datos los muestro:
      if ($totalFilas > 0){
        require_once('data/camposAlarmas.php');
        
        echo "<form id='frmResultado' name='frmResultado' method='post' target='_blank' action='exportar.php'>";
        /// Comienzo tabla para mostrar la consulta:
        echo "<table class='tabla2'>";
        echo "<caption>Tabla con el resultado de la consulta</caption>";
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
                                $parOrigen = "&o=".base64_encode('buscar');
                                //$parKeysCodif = "&k=".base64_encode(serialize($keys));
                                $parConsulta = "&c=".base64_encode($consulta);
                                $parParam = "&p=".base64_encode(serialize($parametros));

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

        echo "<input type='hidden' name='consulta' value='".$consulta."'>";
        echo "<input type='hidden' name='param' value='".serialize($parametros)."'>";
        echo "<input type='hidden' name='titulo' value='".$mensaje."'>";
        echo "<input type='hidden' name='nodo' value='".$nombreNodo."'>";
        echo "<input type='hidden' name='origen' value='buscar'>";
        
        echo "</form>";
      } /// Fin if totalFilas > 0
      else {
        echo "<h3>¡No hay registros a mostrar!</h3><br>";
      } /// Fin else totalFilas > 0
      
      
      ?>
      
    <?php
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