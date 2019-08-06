<!DOCTYPE html>
<html>
<?php 
require_once ('head.php');
require_once ('data/config.php');
require_once ('data/pdo.php');
?>
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid'>
      <br>  
      <h1>Resultado de la carga:</h1>
<?php
  $nombreArchivo = $_FILES['uploadedFile']['name'];
  $destino = $dirCargados."\\\\".$nombreArchivo;
  if (!(move_uploaded_file($_FILES['uploadedFile']['tmp_name'], $destino))) {
    echo "Hubo un error en la copia del archivo.<br>Por favor verifique.";
    return;
  }
  else {
    echo "Archivo subido con éxito al servidor!.<br>";//<br>Archivo guardado en: <strong><u>$destino</u></strong><br>";
  }
  ///Armo consulta para borrar los datos que pueda haber de antes:
  $queryBorrar = "truncate alarmas";
  
  ///Armo consulta para la carga del archivo CSV a la base de datos:
  $queryCargar = "load data infile '$destino' into table alarmas "
          . "fields terminated by ';' "
          . "optionally enclosed by '\"' "
          . "lines terminated by '\\r\\n' "
          . "ignore 3 lines "
          . "(Nombre, Compound, TipoAID, TipoAlarma, TipoCondicion, Descripcion, AfectacionServicio, @fechaCompleta, Ubicacion, Direccion, ValorMonitoreado, NivelUmbral, Periodo, Datos, FiltroALM, FiltroAID) "
          . "set Dia=concat(year(now()), '-', substring(trim(@fechaCompleta), 1, 5)), Hora=concat(substring(trim(@fechaCompleta), 8, 2), ':', substring(trim(@fechaCompleta), 11, 2), ':', substring(trim(@fechaCompleta), 14, 2));";
  //echo "<br>consulta:<br>".$queryCargar;
  
  $result = $pdo->query($queryBorrar);
  $dato = array();
  if ($result !== FALSE) {
    $result1 = $pdo->query($queryCargar);
    if ($result1 !== FALSE) {
      $dato["resultado"] = "Carga correcta en la base de datos!.";
    }  
    else {
      $dato["resultado"] = "ERROR CARGA DE ARCHIVO";
    } 
  }
  else {
    $dato["resultado"] = "ERROR BORRADO";
  }
echo "Resultado: ".$dato["resultado"];
?>
    </div>      
  </main>        
  </body>
</html>


