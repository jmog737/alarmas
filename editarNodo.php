<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file editarNodo.php
*  @brief Archivo que carga los datos del nodo pasado en la url y permite su edición.
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
  require_once('data/pdo.php');
  require_once('data/camposNodos.php');
  
  if (isset($_GET['n'])||isset($_GET['o'])||isset($_GET['cNodo'])){
    if (isset($_GET['n'])){
      $idnodo = base64_decode($_GET['n']);
    }
    if (isset($_GET['o'])){
      $origen = base64_decode($_GET['o']);
      $origenCodif = base64_encode($origen);
    }
    if (isset($_GET['cNodo'])){
      $consulta = base64_decode($_GET['cNodo']);
      $consultaCodif = $_GET['cNodo'];
    }
  }
  if (isset($_POST['query'])) 
    {
    $consulta = html_entity_decode($_POST['query']);
    $idnodo = $_POST['idnodo'];
    
    $nombre = htmlentities($_POST['nombre']);
    $localidad = htmlentities($_POST['localidad']);
    $ip = htmlentities($_POST['ip']);
    $areaMetro = htmlentities($_POST['areaMetro']);
    if (($areaMetro === "SI")||($areaMetro === "si")||($areaMetro === "Si")||($areaMetro === "sI")||($areaMetro === "1")){
      $areaMetro = 1;
    }
    else {
      $areaMetro = 0;
    }
    $tipoNodo = htmlentities($_POST['tipoNodo']);
    $observaciones = htmlentities($_POST['observaciones']);
    
    $query = "update nodos set nombre=?, localidad=?, ip=?, areaMetro=?, tipo=?, observaciones=? where idnodo=?";
    $paramUpdate = array($nombre, $localidad, $ip, $areaMetro, $tipoNodo, $observaciones, $idnodo);
    $log = "SI";
    $resultadoInsert = json_decode(hacerUpdate($query, $log, $paramUpdate), true);
    if ($resultadoInsert === 'ERROR'){
      $mensaje = "Hubo un problema al actualizar los datos.<br>Por favor verifique.";
    }
    else {
      $mensaje = "¡Datos editados correctamente!";
    }
  }  
      
  /// ***************************************** GENERACIÓN NAVEGACIÓN ************************************************************************
  /// Vuelvo a realizar la consulta para poder obtener el array con los índices y así poder generar la navegación:
  $log1 = "NO";
  $datos = json_decode(hacerSelect($consulta, $log1), true);
  
  $keys = array();
  foreach ($datos['resultado'] as $key0 => $fila0 ) {
    $idnodo0 = $fila0['idnodo'];
    $keys[] = $idnodo0;
  } /// Fin foreach datos para sacar los keys
  
  $id = array_search($idnodo, $keys);
  $idMostrar = $id + 1;
  $totalFilas = count($keys);
  $primero = base64_encode($keys[0]);
  $ultimo = base64_encode($keys[$totalFilas-1]);
  $inhabilitarPrimero = '';
  $inhabilitarUltimo = '';

  if ($id !== false){
    if ($id === 0){
      $anterior = base64_encode($keys[$id]);
      $inhabilitarPrimero = 'inhabilitar';
    }
    else {
      $anterior = base64_encode($keys[$id-1]);
    }
    if ($id === ($totalFilas-1)){
      $siguiente = base64_encode($keys[$totalFilas-1]);
      $inhabilitarUltimo = 'inhabilitar';
    }
    else {
      $siguiente = base64_encode($keys[$id+1]);
    }
  }
  else {
    echo "Hubo un error.<br>El índice recibido ($idnodo) no está incluido en el array de índices.<br>Por favor verifique.";
  }
  /// ************************************* FIN GENERACIÓN NAVEGACIÓN ************************************************************************

  /// Luego de la posible actualización, consulto los datos del registro en cuestión: 
  $queryParam = "select idnodo, nombre, localidad, ip, areaMetro, tipo, observaciones from nodos where idnodo=?";
  $param1 = array($idnodo);
  $log = "NO";
  $datosParam = json_decode(hacerSelect($queryParam, $log, $param1), true);
  $datosMostrar = $datosParam['resultado'][0];

  $nombreOriginal = $datosMostrar['nombre'];
  $localidadOriginal = $datosMostrar['localidad'];
  $ipOriginal = $datosMostrar['ip'];
  $areaMetroOriginal = $datosMostrar['areaMetro'];
  if (($areaMetroOriginal === "SI")||($areaMetroOriginal === "si")||($areaMetroOriginal === "Si")||($areaMetroOriginal === "sI")||($areaMetroOriginal === "1")){
      $areaMetroOriginal = "SI";
    }
    else {
      $areaMetroOriginal = "NO";
    }
  $tipoOriginal = $datosMostrar['tipo'];
  $observacionesOriginal = $datosMostrar['observaciones'];
?>
<main>
  <script>
    window.location = '#tituloEditarNodo';
  </script>
  
  <div id='main-content' class='container-fluid'>
    <h2 id="tituloEditarNodo">Datos del Nodo <?php echo $idMostrar.": ".$nombreOriginal." [".$localidadOriginal."]"; ?></h2>
    <?php
    if (isset($_POST['query'])){
      echo "<br><h3>".$mensaje."</h3>";
    }
    ?>
    <br>
    <form id='frmEditarNodo' name='frmEditarNodo' method='post'>
      <div name='table-content' class='table-responsive'>
        <table name='tblEditarNodo' id='tblEditarNodo' class='tabla2 table table-hover w-auto'>
          <caption>Formulario para la edici&oacute;n del nodo</caption>
          
          <thead>
            <tr>
              <th colspan='2' scope='col' class='tituloTabla'>EDITAR NODO</th>
            </tr>
          </thead>
          
          <?php
          $i = 1;
          
          echo "<tbody>";
          /// Recorro el array con los campos para ver cuales hay que mostrar y cuales no.
          foreach ($camposNodos as $key => $value) {   
            if ($camposNodos[$key]['mostrarEditar'] === 'si'){
              $indice = $camposNodos[$key]['nombreDB'];

              switch ($indice){
                case 'id': break;

                case 'nombre':  echo "<tr>
                                        <th scope='row' class='text-left'>Nombre</th>
                                        <td>
                                          <input type='text' name='nombre' id='nombre' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese el nombre.' value='".$datosMostrar[$indice]."'>
                                          <input name='nombreOriginal' id='nombreOriginal' type='hidden' value='".$nombreOriginal."'>  
                                        </td>
                                    </tr>"; 
                                break;
                case 'localidad': echo "<tr>
                                          <th scope='row' class='text-left'>Localidad</th>
                                          <td>
                                            <input type='text' name='localidad' id='localidad' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese la localidad.' value='".$datosMostrar[$indice]."'>
                                            <input name='localidadOriginal' id='localidadOriginal' type='hidden' value='".$localidadOriginal."'>
                                          </td>
                                        </tr>"; 
                                  break;  
                case 'ip':  echo "<tr>
                                    <th scope='row' class='text-left'>IP</th>
                                    <td>
                                      <input type='text' name='ip' id='ip' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese la IP.' value='".$datosMostrar[$indice]."'>
                                      <input name='ipOriginal' id='ipOriginal' type='hidden' value='".$ipOriginal."'>
                                    </td>
                                  </tr>"; 
                            break; 
                case 'areaMetro': if ($datosMostrar[$indice] === "1"){
                                    $areaMetroMostrar = "SI";
                                  }
                                  else {
                                    $areaMetroMostrar = "NO";
                                  }
                                  echo "<tr>
                                          <th scope='row' class='text-left'>&Aacute;rea Metro</th>
                                          <td>
                                            <input type='text' name='areaMetro' id='areaMetro' class='agrandar form-control form-control-sm' rows='5' placeholder='Indique si el nodo pertenece a la zona metro.' value='".$areaMetroMostrar."'>
                                            <input name='areaMetroOriginal' id='areaMetroOriginal' type='hidden' value='".$areaMetroOriginal."'>
                                          </td>
                                        </tr>"; 
                                  break;          
                case 'tipo':  echo "<tr>
                                      <th scope='row' class='text-left'>Tipo</th>
                                      <td>
                                        <input type='text' name='tipoNodo' id='tipoNodo' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese el tipo de dispositivo.' value='".$datosMostrar[$indice]."'>
                                        <input name='tipoOriginal' id='tipoOriginal' type='hidden' value='".$tipoOriginal."'>
                                      </td>
                                    </tr>"; 
                              break; 
                case 'observaciones': echo "<tr>
                                              <th scope='row' class='text-left'>Observaciones</th>
                                              <td>
                                                <input type='text' name='observaciones' id='observaciones' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese las obsevaciones.' value='".$datosMostrar[$indice]."'>
                                                <input name='observacionesOriginal' id='observacionesOriginal' type='hidden' value='".$observacionesOriginal."'>
                                              </td>
                                            </tr>"; 
                                      break;                 
                case 'accion': break;                 
                default: echo "<tr>
                                <th scope='row' class='text-left'>".$camposNodos[$key]['nombreMostrar']."</th>
                                <td>".$datosMostrar[$indice]."</td>
                              </tr>";                 
              } /// Fin switch indice       
            } /// Fin if si mostrarEditar 
          } /// Fin foreach camposNodos
          ?>
          </tbody>
        
          <tfoot>
            <tr>
              <td colspan='2' class='pieTabla'>
                <input type='button' class='btn btn-sm btn-blue accent-4' name='btnEditarNodo' id='btnEditarNodo' value='EDITAR'>
              </td>
            </tr>
          </tfoot>  
          
        </table>
      </div>
     
    <?php
      echo "<input type='hidden' name='idnodo' value=".$idnodo.">";
      echo "<input type='hidden' name='query' value='".htmlentities($consulta, ENT_QUOTES)."'>";
    ?>
    </form>

    <div id="navegacion" class="pagination">
    <?php
      echo "<ul>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir al primer nodo' href='editarNodo.php?n=".$primero."&o=".$origenCodif."&cNodo=".$consultaCodif."'>|<  </a></li>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir al nodo anterior' href='editarNodo.php?n=".$anterior."&o=".$origenCodif."&cNodo=".$consultaCodif."'>  <<  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir al siguiente nodo' href='editarNodo.php?n=".$siguiente."&o=".$origenCodif."&cNodo=".$consultaCodif."'>  >>  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir al último nodo' href='editarNodo.php?n=".$ultimo."&o=".$origenCodif."&cNodo=".$consultaCodif."'>  >|</a></li>";
      echo "</ul>";
    ?>
    </div>
    <?php
      $volver = '<br><a href="javascript:close()">Cerrar y volver al listado</a><br><br>';
      echo $volver;
    ?>
    <br>
  </div>      
</main>      
<?php
require_once ('footer.php');
?>  
</body>
</html>

