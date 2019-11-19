<header>
  <nav id='header-nav' class='navbar navbar-expand' role="navigation">
    <div class='container-fluid'>
      <div class='navbar-header'>
        <a href='index.php' title="Ir al inicio" class='float-left d-none d-sm-block'>
          <div id='logo-img' class='d-none d-sm-block img-fluid'></div>
        </a>

        <div class='navbar-brand'>
          <a href='index.php' title="Ir al Inicio"><h1>MONITOREO DE ALARMAS</h1></a>
        </div>

        <button id='navbarToggle' type='button' class='navbar-toggler' data-toggle='collapse' data-target='#collapsable-nav' aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">  
          <span class="navbar-toggler-icon"></span>
        </button>
        
      </div>

      <div id='collapsable-nav' class='collapse navbar-collapse align-middle'>
        <ul id='nav-list' class='navbar-nav ml-auto my-auto pr-2 pb-2'>
        <?php
          //require_once 'data/config.php';
          if (isset($_SESSION['user_id'])) 
            {
          ?> 
          <li id='navHomeButton' class='nav-item active d-none d-sm-block align-middle' >
            <a class="nav-link" href="index.php"><img src="images/home.png" alt="HOME" title="Ir al inicio"></a>  
          </li>
          <li id='navMenuButton' class="nav-item dropdown align-bottom">            
            <a href="#" class="nav-link dropdown-toggle d-none d-sm-block pt-3" title="Desplegar el MENU" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
              Menu
            </a>
            <div id="drop" class="dropdown-menu dropdown-menu-right  " aria-labelledby="navbarDropdown">
              <a class="dropdown-item" title="Ver alarmas del archivo cargado" href="cargar.php?i=1">Archivo Cargado</a>
              <a class="dropdown-item" title="Realizar Consultas" href="consultas.php">Consultas</a>
              <a class="dropdown-item" title="Ver Nodos" href="nodos.php">Nodos</a>
              <a class="dropdown-item" title="Ver Usuarios" href="usuarios.php">Usuarios</a>
<!--              <div class="dropdown-divider"></div>
              <a class="dropdown-item" title="Ver Estad&iacute;siticas" href="estadisticas.php">Estadisticas</a>-->
<!--              <li role="separator" class="divider"></li>
              <li><a href="#">Administrar</a></li>-->
            </div>
            
            <a class="d-block d-sm-none" title="Ver alarmas del archivo cargado" href="cargar.php?i=1">Archivo Cargado</a>
            <a class='d-block d-sm-none' title="Realizar Consultas" href="consultas.php">Consultas</a>
            <a class='d-block d-sm-none' title="Ver Nodos" href="nodos.php">Nodos</a>
            <a class='d-block d-sm-none' title="Ver Usuarios" href="usuarios.php">Usuarios</a>
            <!--<a class='d-block d-sm-none' title="Ver Estad&iacute;siticas" href="estadisticas.php">Estadisticas</a>--> 
          </li>
          <?php
          }
          else{
          ?>  
            
          <?php
          }
          ?>  
        </ul><!-- #nav-list -->
      </div><!-- .collapse .navbar-collapse -->
    </div><!-- .container -->
  </nav><!-- #header-nav -->
  
  <script type="text/javascript">
    var dir = window.location.pathname;
    var temp = dir.split("/");
    var tam = temp.length;
    var pagina = temp[tam-1];
    
    if (pagina !== 'index.php'){
      verificarSesion('', 's');
      var duracion0 = <?php echo DURACION ?>;
      /// Se agrega un tiempo extra cosa de estar seguro que venció el tiempo (si queda en el límite habrá veces 
      ///que lo detecta y otras que no teniendo que esperar nuevamente un tiempo de DURACION para volver a probar
      var tiempoChequeo = parseInt(duracion0*1000, 10)+2000;
      
      setInterval(function(){
        verificarSesion('¡Llamé desde setInterval!', 'n');
      }, tiempoChequeo);
    }
    else {
      /// Se agrega para sobre escribir el autocompletado del navegador con el usuario y contraseña:
      //setTimeout(function(){vaciarFrmLogin()}, 600);
    }
  </script>
</header>

<?php 
require_once("scripts.php");
require_once("alertas.php");
?>
