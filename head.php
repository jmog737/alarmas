<?php
  $estilos = 'styles.css';
  require 'vendor/autoload.php';
  require_once 'data/config.php';
?>
<head>
  <link href='images/alarmClock.png' rel='shortcut icon' type='image/png'>
  <link href='images/alarmClock.png' rel='icon' type='image/png'>
  <input id="duracionSesion" name="duracionSesion" type="text" value="<?php echo DURACION?>" style="color: black; display: none">
  <title>ALARMAS</title>
  <meta charset='UTF-8'>
  <meta name='viewport' content='width=device-width, initial-scale=1.0'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <link rel='stylesheet' href='vendor/twbs/bootstrap/dist/css/bootstrap.min.css'>
  <link rel='stylesheet' href="css/<?php echo $estilos ?>" >
  <script src="vendor/components/jquery/jquery.min.js"></script>
  <!--<script src='js/popper.min.js'></script>-->
  <script src='vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js'></script>  
  <script src='js/script.js' type="text/javaScript"></script>
</head>
