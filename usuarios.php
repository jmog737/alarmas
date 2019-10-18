<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file usuarios.php
*  @brief Form para ver todos los usuarios en la base de datos, y acceder a su edición.
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
  require_once('data/camposUsuarios.php');
  
  /// Consulta sólo usuarios activos:
  $consulta = "select idusuario, nombre, apellido, appUser, appPwd, estado, observaciones, tamPagina, limiteSelects from usuarios where estado='activo'";
  
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
  $usuarios = $datos['resultado'];
  $mensajeNuevo = '';

  $tituloPagina = "Listado de Usuarios";
  $tituloReporte = "Datos de los Usuarios";
  $mensajeNuevo = $tituloReporte." (Total: ".$totalDatos.")";
?>
    <main>
      <div id='main-content' class='container-fluid table-responsive'>
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
          
          echo "<form id='frmUsuarios' name='frmUsuarios' method='post' action='editarUsuario.php'>";
          /// Comienzo tabla para mostrar los usuarios:
          echo "<div name='table-content' class='table-responsive'>";
          echo "<table id='tblUsuarios' name='tblUsuarios' class='tabla2 table table-hover w-auto'>";
          echo "<caption>Listado de usuarios.</caption>";
          $i = $primerRegistro;
          $totalCamposMostrar = 1;
            
          /// Muestro el encabezado:
          echo "<thead>";
          echo "<tr>";
          foreach ($camposUsuarios as $key => $value) {   
            if ($camposUsuarios[$key]['mostrarListado'] === 'si'){
              $clase = '';
              $totalCamposMostrar++;
              if ($camposUsuarios[$key]['nombreDB'] === 'id'){
                $clase = "class='tituloTablaIzquierdo'";
              }
              else {
                if ($camposUsuarios[$key]['nombreDB'] === 'accion'){
                  $clase = "class='tituloTablaDerecho'";
                }
              }
              echo "<th scope='row' $clase>".$camposUsuarios[$key]['nombreMostrar']."</th>";
            }
          } /// Fin foreach camposAlarmas para encabezados
          echo "</tr>";
          echo "</thead>";
          /// Fin encabezados
            
          echo "<tbody>";
          /// Comienzo proceso de cada fila:
          foreach ($usuarios as $key1 => $fila ) {
            echo "<tr class='table-success'>";

            $idusuario = $fila['idusuario'];

            foreach ($camposUsuarios as $key => $value) {   
              if ($camposUsuarios[$key]['mostrarListado'] === 'si'){
                $indice = $camposUsuarios[$key]['nombreDB'];

                switch ($indice){
                  case 'id':  echo "<td>".$i."</td>";
                              $i++;
                              break;
                  case 'tamPagina': if ($fila[$indice] === null){
                                      echo "<td>No ingresado</td>";
                                    }   
                                    else {
                                      echo "<td>".$fila[$indice]."</td>";
                                    }
                                    break;         
                  case 'limiteSelects': if ($fila[$indice] === null){
                                          echo "<td>No ingresado</td>";
                                        }
                                        else {
                                          echo "<td>".$fila[$indice]."</td>";
                                        }
                                        break;
                  case 'accion':  $j = $i - 1;
                                  $parUserCodif = "us=".base64_encode($idusuario);
                                  $parOrigen = "&o=".base64_encode('usuario');
                                  $parConsulta = "&cUser=".base64_encode($consulta);

                                  $url = "editarUsuario.php?".$parUserCodif.$parOrigen.$parConsulta;
                                  echo "<td><a href='".$url."' class='btn btn-sm btn-info' role='button' target='_blank'>Editar</a></td>";
                                  break;    
                  case 'nombre':
                  case 'apellido':  echo "<td nowrap>".$fila[$indice]."</td>";
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
          echo "  <tr>";
          echo "    <td class='pieTabla' colspan='$totalCamposMostrar' id='btnExportarUsuarios' name='btnExportar'>";
          echo "      <input type='button' class='btn btn-success btn-sm' value='Exportar'>";
          echo "    </td>";
          echo "  </tr>";
          echo "</tfoot>";
          
          echo "</table>";
          echo "</div>";

          echo "<input type='hidden' name='query' value='".htmlentities($consulta, ENT_QUOTES)."'>";
          echo "<input type='hidden' name='mensaje' value='".$tituloReporte."'>";
          echo "<input type='hidden' name='offset' id='offset' value=''>";
          echo "<input type='hidden' name='page' id='page' value=''>";
          echo "<input type='hidden' name='origen' value='usuarios'>";

          echo "</form>";

          ///********************************************* Comienzo paginación *****************************************************************
          if ($totalPaginas > 1) {
            $paginas = '<div class="pagination" id="paginas">';
            $paginas .= '<input style="display: none" type="text" id="totalPaginas" value="'.$totalPaginas.'">';
            $paginas .= '<input style="display: none" type="text" id="totalRegistros" value="'.$totalDatos.'"><ul>';
            if ($page !== 1) {
              $paginas .= '<li><a name="usuarios" class="paginate anterior" data="'.($page-1).'">Anterior</a></li>';
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
                $paginas .= '<li ><a name="usuarios" class="paginate pageActive'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
              }  
              else {
                //si el índice no corresponde con la página mostrada actualmente,
                //coloco el enlace para ir a esa página
                $paginas .= '<li><a name="usuarios" class="paginate'.$inhabilitarPrimero.$inhabilitarUltimo.'" data="'.$k.'">'.$k.'</a></li>';
              }
            }

            if ($page !== $totalPaginas) {
              $paginas .= '<li><a name="usuarios" class="paginate siguiente" data="'.($page+1).'">Siguiente</a></li>';
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

