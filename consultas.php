<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file consultas.php
*  @brief Archivo que se encarga de valiadar y generar las consultas a la base de datos.
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

/// Consulta sólo OCSs del área metro:
//$query = "select idnodo, nombre, localidad from nodos where tipo!='PSS 32' and areaMetro=true";
/// Consulta OCSs y PSS32s del área metro:
$query = "select idnodo, nombre, localidad from nodos where areaMetro=true";
$log = "NO";
$datos = json_decode(hacerSelect($query, $log), true);
$localidades = $datos['resultado'];
$totalLocalidades = $datos['rows'];

/// Consulta usuarios:
$query1 = "select idusuario, nombre, apellido from usuarios where estado='activo' order by apellido, nombre";
$datos1 = json_decode(hacerSelect($query1, $log), true);
$usuarios = $datos1['resultado'];
$totalUsuarios = $datos['rows'];

?>
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid table-responsive'>
      <br>  
      <h2>Consultas hist&oacute;ricas:</h2>

      <form method="POST" name='frmConsultas' id='frmConsultas' action="buscar.php">
        <div name='table-content' class='table-responsive'>
          <table id="tblConsultas" name="tblConsultas"  class='tabla2 table table-hover w-auto'>
            <caption>Formulario para hacer consultas hist&oacute;ricas a la base de datos.</caption>
            <thead>
              <tr>
                <th colspan="5" scope='col' class="text-center tituloTabla">CONSULTAS</th>
              </tr>
            </thead>
            
            <tbody>
              <tr>
                <th colspan="5" scope='col' class="subTituloTabla1">FUENTE</th>
              </tr>
              <tr>
                <td >
                  <div class="input-group-text fondoVerde">
                    <input type="radio" name="criterio" id='defaultChecked' checked title="Elegir el origen a consultar. Seleccionar si se quiere buscar por NODO." value="nodo">
                  </div>
                </td>
                <th class='text-left' scope='row'>Nodo:</th>  
                <td colspan="3">
                  <select name="nodo" id="nodo" tabindex="1" class='custom-select-sm custom-select' style="width: 100%" title="Seleccione por favor el nodo del cual consultar.">
                    <option value="todos" nombreCorto="todos">--- TODOS LOS NODOS ---</option>
                    <?php
                    foreach ($localidades as $i => $valor){
                      $loc = $localidades[$i]['localidad'];
                      $nombreCorto = $localidades[$i]['nombre'];
                      $idnodo = $localidades[$i]['idnodo'];
                      echo "<option value='".$idnodo."' nombreCorto='".$nombreCorto."'>".$loc." [".$nombreCorto."]</option>";
                    }
                    ?>
                  </select>
                </td> 
              </tr>
              <tr>
                <td>
                  <div class="input-group-text fondoVerde">
                    <input type="radio" name="criterio" title="Elegir el origen a consultar. Seleccionar si se quiere buscar por ARCHIVO." value="file">
                  </div>
                </td>
                <th class='text-left' scope='row'>Archivo:</th>
                <td colspan="3">
                  <input type="text" tabindex="2" placeholder="Elegir archivo" class='agrandar form-control form-control-sm' title="Elegir el archivo a consultar." name="fileSearch" id="fileSearch" class="agrandar" size="9" onkeyup="showHint(this.value, '#fileSearch', '')">
                </td>                
              </tr>
              <tr>
                <th colspan="5" class="subTituloTabla1" scope='col'>FECHA</th>
              </tr>
              <tr>
                <td>
                  <div class="input-group-text fondoNaranja">    
                    <input type="radio" name="criterioFecha" title="Elegir el período a buscar. Seleccionar si se quiere buscar por fechas." value="intervalo" checked="checked">
                  </div>
                </td>
                <th scope='row' class='text-left'>Entre:</th>
                <td>
                  <input type="date" name="inicio" id="inicio" tabindex="3" title="Elegir la fecha de inicio. Sólo si se optó por una consulta por fechas." tabindex="6" style="width:100%; text-align: center" min="2019-08-01" value="<?php echo date('Y-m-d');?>">
                </td>
                <td>y:</td>
                <td>
                  <input type="date" name="fin" id="fin" tabindex="4" title="Elegir la fecha de finalización. Sólo si se optó por una consulta por fechas." tabindex="7" style="width:100%; text-align: center" min="2019-08-01">
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group-text fondoNaranja">
                    <input type="radio" name="criterioFecha" title="Elegir el período a buscar. Seleccionar si se quiere buscar por mes." value="mes">
                  </div>
                </td>
                <th scope='row' class='text-left'>Mes:</th>
                <td>
                  <select id="mes" name="mes" tabindex="5" class='custom-select-sm custom-select' title="Elegir el mes a buscar. Sólo si se optó por una consulta por mes." tabindex="8" style="width:100%">
                    <option value="todos" selected="yes">--TODOS--</option>
                    <option value="01">Enero</option>
                    <option value="02">Febrero</option>
                    <option value="03">Marzo</option>
                    <option value="04">Abril</option>
                    <option value="05">Mayo</option>
                    <option value="06">Junio</option>
                    <option value="07">Julio</option>
                    <option value="08">Agosto</option>
                    <option value="09">Setiembre</option>
                    <option value="10">Octubre</option>
                    <option value="11">Noviembre</option>
                    <option value="12">Diciembre</option>
                  </select>
                </td>
                <th>Año:</th>
                <td>
                  <select id="año" name="año" tabindex="6" class='custom-select-sm custom-select' title="Elegir el año. Sólo si se optó por una consulta por mes y/o año." tabindex="9" style="width:100%">
                    <option value="2019">2019</option>
                    <option value="2020" selected="yes">2020</option>
                    <option value="2021">2021</option>
                    <option value="2022">2022</option>
                    <option value="2023">2023</option>
                    <option value="2024">2024</option>
                    <option value="2025">2025</option>
                  </select>
                </td>
              </tr>
              <tr>
                <td>
                  <div class="input-group-text fondoNaranja">
                    <input type="radio" name="criterioFecha" title="Elegir el período a buscar. Seleccionar si se quieren TODOS los movimientos" value="todos">
                  </div>
                </td>
                <th scope='row' class='text-left'>TODOS</th>
              </tr>
              <tr>
                <td colspan='5'>
                  <input type="radio" name="origenFecha" id="origenFechaAlarma" value="alarma" class="nomostrar visible">
                  <label for="origenFechaAlarma" title="Elegir si se quiere filtrar por la fecha de la ALARMA.">DE LA ALARMA</label> 
                  <input type="radio" name="origenFecha" id="origenFechaCarga" value="carga" class="nomostrar visible" checked>
                  <label for="origenFechaCarga" title="Elegir si se quiere filtrar por la fecha de CARGA del archivo.">DE CARGA</label> 
                </td>
              </tr>
              <tr>
                <th colspan="5" class="subTituloTabla1" scope='col'>FILTROS</th>
              </tr>
              <tr>
                <th colspan="2" class="text-left" scope='row'>Alarma:</th>  
                <td colspan="3">
                  <select name="alarma" id="alarma" tabindex="7" class='custom-select-sm custom-select' title="Elegir el tipo de alarma.">
                    <option value="todas">--- TODAS ---</option>
                    <option value="CR" title="Alarma CRITICAL">CR</option>
                    <option value="WR" title="Alarma WARNING">WR</option>  
                    <option value="MJ" title="Alarma MAJOR">MJ</option>
                    <option value="MN" title="Alarma MINOR">MN</option>
                    <option value="NA" title="Alarma NOT ALARMED">NA</option>
                    <option value="NR" title="Alarma NOT REPORTED">NR</option>
                  </select>
                </td> 
              </tr>
              <tr>
                <th colspan="2" class="text-left" scope='row'>Nombre Alarma:</th>
                <td colspan="3"><input type="text" tabindex="8" class='agrandar form-control form-control-sm' placeholder="Escribir parte del nombre de la alarma" title="Escribir parte del nombre a buscar." name="nameSearch" id="nameSearch" class="agrandar" size="9"></td>
              </tr>
              <tr>
                <th colspan="2" class="text-left" scope='row'>Condici&oacute;n Alarma:</th>
                <td colspan="3"><input type="text" tabindex="9" class='agrandar form-control form-control-sm' placeholder="Escribir parte de la condición de la alarma" title="Escribir parte de la condición a buscar." name="conditionSearch" id="conditionSearch" class="agrandar" size="9"></td>
              </tr>
              <tr>
                <th colspan="2" class="text-left" scope='row'>AID:</th>
                <td colspan="3"><input type="text" tabindex="10" class='agrandar form-control form-control-sm' placeholder="Escribir parte del AID de la alarma" title="Escribir parte del AID a buscar." name="aidSearch" id="aidSearch" class="agrandar" size="9"></td>
              </tr>
              <tr>
                <th colspan="2" class="text-left" scope='row'>Usuario:</th>  
                <td colspan="3">
                  <select name="usuarios" id="usuarios" tabindex="11" class='custom-select-sm custom-select' title="Elegir el usuario.">
                    <option value="todos">--- TODOS ---</option>
                    <?php
                    foreach ($usuarios as $i => $valor1){
                      $apellidoUsuario = $usuarios[$i]['apellido'];
                      $nombreUsuario = $usuarios[$i]['nombre'];
                      $idusuario = $usuarios[$i]['idusuario'];
                      echo "<option value='".$idusuario."'>".$nombreUsuario." ".$apellidoUsuario."</option>";
                    }
                    ?>
                  </select>
                </td> 
              </tr>
              <tr>
                <th colspan="2" class="text-left" scope='row'>Estado:</th>  
                <td colspan="3">
                  <select name="estado" id="estado" tabindex="12" class='custom-select-sm custom-select' title="Elegir el usuario.">
                    <option value="Todos">--- TODOS ---</option>
                    <option value="Procesada">Procesadas</option>
                    <option value="Sin procesar">SIN Procesar</option>
                  </select>
                </td> 
              </tr>
            </tbody>
            
            <tfoot>
              <tr>
                <td colspan="5" class="pieTabla">
                  <input type="button" class="btn btn-sm btn-danger" tabindex="13" name="buscar" id="buscar" title="Ejecutar la consulta" value="Consultar" align="center">
                </td>
              </tr>
            </tfoot>
            
          </table>
        </div>
        
        <input type="hidden" name="query" id="query" value="">
        <input type="hidden" name="param" id="param" value="">
        <input type="hidden" name="offset" id="offset" value="0">
        <input type="hidden" name="mensaje" id="mensaje" value="">
      </form> 
      
    <?php
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