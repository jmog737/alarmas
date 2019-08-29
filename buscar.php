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

$mensaje = $_POST['mensaje'];
$consulta = $_POST['query'];
$log = false;
$datos = json_decode(hacerSelect($consulta, $log), true);
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
      <h3><?php echo $mensaje." (Total: ".$totalFilas.")" ?></h3>
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
                                $parKeysCodif = "&k=".base64_encode(serialize($keys));

                                $url = "editarAlarma.php?".$parAlCodif.$parOrigen.$parKeysCodif;
                                echo "<td><a href='".$url."' target='_blank'>Editar</a></td>";
                                break;         
                default:  echo "<td>".$fila[$indice]."</td>";
                          break;
              } /// Fin switch indice      
            } /// Fin if mostrarListado 
          } /// Fin foreach camposAlarmas

          echo "</tr>";
        } /// Fin del procesamiento de las filas con datos

        echo "<tr><td class='pieTabla' colspan='$totalCamposMostrar' id='btnExportar' name='btnExportar'><input type='button' class='btn btn-success' value='Exportar'></td></tr>";
        echo "</table>";

        echo "<input type='hidden' name='consulta' value='".$consulta."'>"
          . "<input type='hidden' name='origen' value='buscar'>";

        echo "</form>";
      } /// Fin if totalFilas > 0
      else {
        echo "¡No hay registros a mostrar!<br>";
      } /// Fin else totalFilas > 0
      
      
      ?>
      
    <?php
    $volver = "<br><a href='consultas.php'>Volver a Consultas</a><br><br>";
    echo $volver;
    ?>
    </div>      
  </main>      
  <?php
  require_once ('footer.php');
  ?>  
  </body>
</html>