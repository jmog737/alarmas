<?php
  if (isset($_SESSION["username"])){
    $user = $_SESSION["username"];
    $estilos = 'styles_'.$user.'.php';
  }  
  if (!isset($_SESSION["username"])||(!file_exists("css/".$estilos))){
    $estilos = 'styles.php';
  }
  $estilos = 'styles.css';
  //$estilos = 'nuevoCSS.css';
  
  require 'vendor/autoload.php';
  require_once 'data/config.php';
?>
<head>
  <link href='images/alarmClock.png' rel='shortcut icon' type='image/png'>
  <link href='images/alarmClock.png' rel='icon' type='image/png'>
  <input id="duracionSesion" name="duracionSesion" type="text" value="<?php echo DURACION?>" style="color: black; display: none">
  <input id="tamPagina" name="tamPagina" type="text" value="<?php echo $_SESSION["tamPagina"] ?>" style="color: black; display: none">
  <input id="limiteSelects" name="limiteSelects" type="text" value="<?php echo $_SESSION["limiteSelects"] ?>" style="color: black; display: none">
  <input id="maxTamPagina" name="maxTamPagina" type="text" value="<?php echo MAX_TAMPAGINA ?>" style="color: black; display: none">
  <input id="maxLimiteSelects" name="maxLimiteSelects" type="text" value="<?php echo MAX_LIMITESELECTS ?>" style="color: black; display: none">
  <title>ALARMAS</title>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0, shrink-to-fit=no'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.2/css/all.css">
  <link rel='stylesheet' href='vendor/twbs/bootstrap/dist/css/bootstrap.min.css'>
  <link rel="stylesheet" href="css/mdb.min.css">
  <link href="css/datatables.min.css" rel="stylesheet">
  <link rel='stylesheet' href="css/<?php echo $estilos ?>" >
  
  <script src='js/verificarSesion.js' type="text/javaScript"></script>
  <?php
  //require_once ('scripts.php');
  ?>
</head>