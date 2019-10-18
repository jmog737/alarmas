<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file editarUsuario.php
*  @brief Archivo que carga los datos del usuario pasado en la url y permite su edición.
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
  require_once('data/camposUsuarios.php');
  
  if (isset($_GET['us'])||isset($_GET['o'])||isset($_GET['cUser'])){
    if (isset($_GET['us'])){
      $idusuario = base64_decode($_GET['us']);
    }
    if (isset($_GET['o'])){
      $origen = base64_decode($_GET['o']);
      $origenCodif = base64_encode($origen);
    }
    if (isset($_GET['cUser'])){
      $consulta = base64_decode($_GET['cUser']);
      $consultaCodif = $_GET['cUser'];
    }
  }
  if (isset($_POST['query'])) 
    {
    $consulta = html_entity_decode($_POST['query']);
    $idusuario = $_POST['idusuario'];
    
    $nombre = htmlentities($_POST['nombre']);
    $apellido = htmlentities($_POST['apellido']);
    $appUser = htmlentities($_POST['appUser']);
    $tamPaginaUser = htmlentities($_POST['tamPaginaUser']);
    $limiteSelectsUser = htmlentities($_POST['limiteSelectsUser']);
    $observaciones = htmlentities($_POST['observaciones']);
    if ($tamPaginaUser === 'No ingresado'){
      $tamPaginaUser = 50;
    }
    if ($limiteSelectsUser === 'No ingresado'){
      $limiteSelectsUser = 15;
    }
    $query = "update usuarios set nombre=?, apellido=?, appUser=?, tamPagina=?, limiteSelects=?, observaciones=? where idusuario=?";
    $paramUpdate = array($nombre, $apellido, $appUser, $tamPaginaUser, $limiteSelectsUser, $observaciones, $idusuario);
    $log = "SI";
    $resultadoInsert = json_decode(hacerUpdate($query, $log, $paramUpdate), true);
    if ($resultadoInsert === 'ERROR'){
      $mensaje = "Hubo un problema al actualizar los datos.<br>Por favor verifique.";
    }
    else {
      $mensaje = "¡Datos editados correctamente!";
      if (($tamPaginaUser !== $_SESSION['tamPagina'])&&($_SESSION['username'] === $appUser)){
        $_SESSION['tamPagina'] = $tamPaginaUser;
      }
      if (($limiteSelectsUser !== $_SESSION['limiteSelects'])&&($_SESSION['username'] === $appUser)){
        $_SESSION['limiteSelects'] = $limiteSelectsUser;
      }
    }
  }  
      
  /// ***************************************** GENERACIÓN NAVEGACIÓN ************************************************************************
  /// Vuelvo a realizar la consulta para poder obtener el array con los índices y así poder generar la navegación:
  $log1 = "NO";
  $datos = json_decode(hacerSelect($consulta, $log1), true);
  
  $keys = array();
  foreach ($datos['resultado'] as $key0 => $fila0 ) {
    $idusuario0 = $fila0['idusuario'];
    $keys[] = $idusuario0;
  } /// Fin foreach datos para sacar los keys
  
  $id = array_search($idusuario, $keys);
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
    echo "Hubo un error.<br>El índice recibido ($idusuario) no está incluido en el array de índices.<br>Por favor verifique.";
  }
  /// ************************************* FIN GENERACIÓN NAVEGACIÓN ************************************************************************

  /// Luego de la posible actualización, consulto los datos del registro en cuestión: 
  $queryParam = "select idusuario, nombre, apellido, appUser, appPwd, tamPagina, limiteSelects, estado, observaciones from usuarios where idusuario=?";
  $param1 = array($idusuario);
  $log = "NO";
  $datosParam = json_decode(hacerSelect($queryParam, $log, $param1), true);
  $datosMostrar = $datosParam['resultado'][0];

  $nombreOriginal = $datosMostrar['nombre'];
  $apellidoOriginal = $datosMostrar['apellido'];
  $appUserOriginal = $datosMostrar['appUser'];
  $limiteSelectsOriginal = (int)$datosMostrar['limiteSelects'];
  $tamPaginaOriginal = (int)$datosMostrar['tamPagina'];
  $observacionesOriginal = $datosMostrar['observaciones'];
?>
<main>
  <script>
    window.location = '#tituloEditarUsuario';
  </script>
  
  <div id='main-content' class='container-fluid'>
    <h2 id="tituloEditarUsuario">Datos del Usuario <?php echo $nombreOriginal." ".$apellidoOriginal; ?></h2>
    <?php
    if (isset($_POST['query'])){
      echo "<h3>".$mensaje."</h3>";
    }
    ?>
    <br>
    <form id='frmEditarUsuario' name='frmEditarUsuario' method='post'>
      <div name='table-content' class='table-responsive'>
        <table id="tblEditarUsuario" name="tblEditarUsuario"  class='tabla2 table table-hover w-auto'>  
          <caption>Formulario para la edici&oacute;n del usuario</caption>
          
          <thead>
            <tr>
              <th colspan='2' scope='col' class='tituloTabla'>EDITAR USUARIO</th>
            </tr>
          </thead>
          <?php
          $i = 1;

          echo "<tbody>";
          /// Recorro el array con los campos para ver cuales hay que mostrar y cuales no.
          foreach ($camposUsuarios as $key => $value) {   
            if ($camposUsuarios[$key]['mostrarEditar'] === 'si'){
              $indice = $camposUsuarios[$key]['nombreDB'];

              switch ($indice){
                case 'id': break;

                case 'nombre': echo "<tr>
                                      <th scope='row' class='text-left'>Nombre</th>
                                      <td>
                                        <input type='text' name='nombre' id='nombre' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese el nombre.' value='".$datosMostrar[$indice]."'>
                                        <input name='nombreOriginal' id='nombreOriginal' type='hidden' value='".$nombreOriginal."'>  
                                      </td>
                                    </tr>"; 
                              break;
                case 'apellido': echo "<tr>
                                        <th scope='row' class='text-left'>Apellido</th>
                                        <td>
                                          <input type='text' name='apellido' id='apellido' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese el apellido.' value='".$datosMostrar[$indice]."'>
                                          <input name='apellidoOriginal' id='apellidoOriginal' type='hidden' value='".$apellidoOriginal."'>
                                        </td>
                                      </tr>"; 
                                  break;  
                case 'tamPagina': if ($datosMostrar[$indice] === null){
                                    $tamaPag = "No ingresado";
                                  }
                                  else {
                                    $tamaPag = (int)$datosMostrar[$indice];
                                  }
                                  echo "<tr>
                                          <th scope='row' class='text-left'>Tama&nacute;o de P&aacute;gina</th>
                                          <td>
                                            <input type='text' name='tamPaginaUser' id='tamPaginaUser' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese el tamaño para la p&aacute;gina.' value='".$tamaPag."'>
                                            <input name='tamPaginaOriginal' id='tamPaginaOriginal' type='hidden' value='".$tamPaginaOriginal."'>
                                          </td>
                                        </tr>"; 
                                  break; 
                case 'limiteSelects': if ($datosMostrar[$indice] === null){
                                        $limite = "No ingresado";
                                      }
                                      else {
                                        $limite = (int)$datosMostrar[$indice];
                                      }
                                      echo "<tr>
                                              <th scope='row' class='text-left'>L&iacute;mite Selects</th>
                                              <td>
                                                <input type='text' name='limiteSelectsUser' id='limiteSelectsUser' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese el tama&nacute;o para los selects.' value='".$limite."'>
                                                <input name='limiteSelectsOriginal' id='limiteSelectsOriginal' type='hidden' value='".$limiteSelectsOriginal."'>
                                              </td>
                                            </tr>"; 
                                  break; 
                case 'appUser': echo "<tr>
                                        <th scope='row' class='text-left'>Usuario en la App</th>
                                        <td>
                                          <input type='text' name='appUser' id='appUser' class='agrandar form-control form-control-sm' rows='5' placeholder='Ingrese el nombre para el usuario de la app.' value='".$datosMostrar[$indice]."'>
                                          <input name='appUserOriginal' id='appUserOriginal' type='hidden' value='".$appUserOriginal."'>
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
                                <th scope='row' class='text-left'>".$camposUsuarios[$key]['nombreMostrar']."</th>
                                <td>".$datosMostrar[$indice]."</td>
                              </tr>";                 
              } /// Fin switch indice       
            } /// Fin if si mostrarEditar 
          } /// Fin foreach camposUsuarios
          ?>
          </tbody>
          
          <tfoot>
            <tr>
              <td colspan='2' class='pieTabla'>
                <input type='button' class='btn btn-danger' name='btnEditarUsuario' id='btnEditarUsuario' value='EDITAR'>
              </td>
            </tr>
          </tfoot>
          
        </table>
      </div>
    <?php
      echo "<input type='hidden' name='idusuario' value=".$idusuario.">";
      echo "<input type='hidden' name='query' value='".htmlentities($consulta, ENT_QUOTES)."'>";
    ?>
    </form>

    <div id="navegacion" class="pagination">
    <?php
      echo "<ul>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir al primer usuario' href='editarUsuario.php?us=".$primero."&o=".$origenCodif."&cUser=".$consultaCodif."'>|<  </a></li>";
      echo "<li><a class='".$inhabilitarPrimero."' title='Ir al usuario anterior' href='editarUsuario.php?us=".$anterior."&o=".$origenCodif."&cUser=".$consultaCodif."'>  <<  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir al siguiente usuario' href='editarUsuario.php?us=".$siguiente."&o=".$origenCodif."&cUser=".$consultaCodif."'>  >>  </a></li>";
      echo "<li><a class='".$inhabilitarUltimo."' title='Ir al último usuario' href='editarUsuario.php?us=".$ultimo."&o=".$origenCodif."&cUser=".$consultaCodif."'>  >|</a></li>";
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

