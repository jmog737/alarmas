<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file nodos.php
*  @brief Form para ver todos los nodos en la base de datos, y acceder a su edición.
*  @author Juan Martín Ortega
*  @version 1.0
*  @date Setiembre 2019
*
*******************************************************/
?>

<!DOCTYPE html>
<html>
<?php 
require_once ('head.php');
?>
  <body>
<?php
  require_once ('header.php');
  require_once ('data/pdo.php');
  require_once('data/camposNodos.php');
  
  /// Consulta de todos los nodos:
  $consulta = "select idnodo, nombre, tipo, localidad, ip, areaMetro, observaciones from nodos";
  
  $log = "NO";
  /// Rearmo la consulta SOLO para conocer el total de datos:
  $temp0 = explode("from", $consulta);
  $consultaTotal = "select count(*) from".$temp0[1];
  $datosTotal = json_decode(hacerSelect($consultaTotal, $log), true);
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
  $datos = json_decode(hacerSelect($consultaNueva, $log), true);
  $nodos = $datos['resultado'];
  $mensajeNuevo = '';

  $tituloPagina = "Listado de Nodos";
  $tituloReporte = "Datos de los Nodos";
  $mensajeNuevo = $tituloReporte." (Total: ".$totalDatos.")";
?>
    <main>
      <div id='main-content' class='container-fluid'>
        <br>  
<?php
        echo "<h2>".$tituloPagina."</h2>";
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
          
          echo "<form id='frmNodos' name='frmNodos' method='post' action='editarNodo.php'>";
          /// Comienzo tabla para mostrar los usuarios:
          echo "<table class='tabla2'>";
          echo "<caption>Listado de nodos.</caption>";
          $i = $primerRegistro;
          $totalCamposMostrar = 1;
            
          /// Muestro el encabezado:
          echo "<tr>";
          foreach ($camposNodos as $key => $value) {   
            if ($camposNodos[$key]['mostrarListado'] === 'si'){
              $clase = '';
              $totalCamposMostrar++;
              if ($camposNodos[$key]['nombreDB'] === 'id'){
                $clase = "class='tituloTablaIzquierdo'";
              }
              else {
                if ($camposNodos[$key]['nombreDB'] === 'accion'){
                  $clase = "class='tituloTablaDerecho'";
                }
              }
              echo "<th $clase>".$camposNodos[$key]['nombreMostrar']."</th>";
            }
          } /// Fin foreach camposAlarmas para encabezados
          echo "</tr>";
          /// Fin encabezados
            
          /// Comienzo proceso de cada fila:
          foreach ($nodos as $key1 => $fila ) {
            echo "<tr class='".$clase."'>";

            $idnodo = $fila['idnodo'];

            foreach ($camposNodos as $key => $value) {   
              if ($camposNodos[$key]['mostrarListado'] === 'si'){
                $indice = $camposNodos[$key]['nombreDB'];

                switch ($indice){
                  case 'id':  echo "<td>".$i."</td>";
                              $i++;
                              break;                
                  case 'accion':  $j = $i - 1;
                                  $parUserCodif = "n=".base64_encode($idnodo);
                                  $parOrigen = "&o=".base64_encode('nodo');
                                  $parConsulta = "&cNodo=".base64_encode($consulta);

                                  $url = "editarNodo.php?".$parUserCodif.$parOrigen.$parConsulta;
                                  echo "<td><a href='".$url."' target='_blank'>Editar</a></td>";
                                  break;    
                  case 'nombre':
                  case 'localidad': echo "<td nowrap>".$fila[$indice]."</td>";
                                    break;
//                  case 'ip':  echo "<td><a href='$fila[$indice]' target='_blank'>$fila[$indice]</a></td>";      
//                              break;
                  default:  echo "<td>".$fila[$indice]."</td>";
                            break;
                } /// Fin switch indice      
              } /// Fin if mostrarListado 
            } /// Fin foreach camposAlarmas

            echo "</tr>";
          } /// Fin del procesamiento de las filas con datos

          echo "<tr><td class='pieTabla' colspan='$totalCamposMostrar' id='btnExportarNodos' name='btnExportar'><input type='button' class='btn btn-success' value='Exportar'></td></tr>";
          echo "</table>";

          echo "<input type='hidden' name='query' value='".$consulta."'>";
          echo "<input type='hidden' name='mensaje' value='".$tituloReporte."'>";
          echo "<input type='hidden' name='offset' id='offset' value=''>";
          echo "<input type='hidden' name='page' id='page' value=''>";
          echo "<input type='hidden' name='origen' value='nodos'>";

          echo "</form>";

          ///********************************************* Comienzo paginación *****************************************************************
          if ($totalPaginas > 1) {
            $paginas = '<div class="pagination" id="paginas">';
            $paginas .= '<input style="display: none" type="text" id="totalPaginas" value="'.$totalPaginas.'">';
            $paginas .= '<input style="display: none" type="text" id="totalRegistros" value="'.$totalDatos.'"><ul>';
            if ($page !== 1) {
              $paginas .= '<li><a name="nodos" class="paginate anterior" data="'.($page-1).'">Anterior</a></li>';
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
                $paginas .= '<li ><a name="nodos" class="paginate pageActive'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
              }  
              else {
                //si el índice no corresponde con la página mostrada actualmente,
                //coloco el enlace para ir a esa página
                $paginas .= '<li><a name="nodos" class="paginate'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
              }
            }

            if ($page !== $totalPaginas) {
              $paginas .= '<li><a name="nodos" class="paginate siguiente" data="'.($page+1).'">Siguiente</a></li>';
            } 
            $paginas .= '</ul>';
            $paginas .= '</div><br>';
            echo $paginas;
          }
          ///************************************************** FIN paginación *****************************************************************
        } /// Fin if totalDatos > 0
        else {
          echo "¡No hay registros a mostrar!<br>";
        } /// Fin else totalUsuarios > 0

?>
        <br>
      </div>      
    </main>
<?php
  require_once ('footer.php');
?>    
  </body>
</html>

