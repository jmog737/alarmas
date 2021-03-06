<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 
/**
******************************************************
*  @file subirArchivo.php
*  @brief Form para cargar el archivo en la base de datos.
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
?>
  <body>
<?php
  require_once ('header.php');
  //$query = "select distinct localidad from nodos order by localidad asc";
  /// Consulta sólo OCSs del área metro:
  //$query = "select idnodo, nombre, localidad from nodos where tipo!='PSS 32' and areaMetro=true";
  
  /// Consulta OCSs y PSS32s del área metro:
  $query = "select idnodo, nombre, localidad from nodos where areaMetro=true";
  
  /// Consulta todos pero sólo del área metro y agrupadas por localidad:
  //$query = "select idnodo, nombre, localidad from nodos where areaMetro=true group by localidad";
  
  /// Consulta sólo OCSs del área metro, pero sin agrupar por localidad:
  //$query = "select idnodo, nombre, localidad from nodos where areaMetro=true";
  
  require_once ('data/pdo.php');
  $log = "NO";
  $datos = json_decode(hacerSelect($query, $log),  true);
  $localidades = $datos['resultado'];
  $totalLocalidades = $datos['rows'];
?>
    <main>
      <div id='main-content' class='container-fluid table-responsive'>
        <br>  
        <h2>Cargar archivo:</h2>
        
        <form method="POST" name='frmSubir' id='frmSubir' action="cargar.php" enctype="multipart/form-data">
          <div id='table-content' class='table-responsive'>
            <table id="estadisticas" name="estadisticas" class="tabla2 table table-hover w-auto">
              <caption>Formulario para subir el archivo a la base de datos.</caption>
              <thead>
                <tr>
                  <th colspan="2" scope='col' class="centrado tituloTabla">SUBIR ARCHIVO</th>
                </tr>
              </thead>
              
              <tbody>
                <tr>
                  <th scope='row' class='text-left'>Archivo:</th>  
                  <td>
                    <div class="custom-file">
                      <input type="file" name="uploadedFile" id="uploadedFile" class='custom-file-input'  lang="es" accept=".csv, .xls"/>
                      <label class="custom-file-label" for="uploadedFile">Seleccionar archivo</label>
                    </div>
                  </td> 
                </tr>
                <tr>
                  <th scope='row' class='text-left'>Nodo:</th>  
                  <td>
                    <select name="nodo" id="nodo" class='custom-select-sm custom-select' title="Seleccione por favor el nodo del cual se obtuvo el archivo.">
                      <option value="nada" nombreCorto="nada">--- Seleccionar NODO ---</option>
                      <?php
                      foreach ($localidades as $i => $valor){
                        $loc = $localidades[$i]['localidad'];
                        $nombreCorto = $localidades[$i]['nombre'];
                        $idnodo = $localidades[$i]['idnodo'];
                        echo "<option value='".$loc."' nombreCorto='".$nombreCorto."' idnodo=".$idnodo.">".$nombreCorto." - ".$loc."</option>";
                        //echo "<option value='".$loc."---".$nombreCorto."---".$idnodo."'>".$nombreCorto." - ".$loc."</option>";
                      }
                      ?>
                    </select>
                  </td> 
                </tr>
              </tbody>
              
              <tfoot>
                <tr>
                  <td colspan="2" class="pieTabla">
                    <input type="button"  class="btn btn-sm btn-danger" name="btnCargar" id="btnCargar" value="CARGAR" />
                  </td>
                </tr>
              </tfoot>
                
            </table>  
          </div>
        </form>
        <br>
      </div>      
    </main>
<?php
  require_once ('footer.php');
?>    
  </body>
</html>

