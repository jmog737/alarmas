<?php
if(!isset($_SESSION)) 
  { 
  session_start(); 
} 

if ( isset($_POST["usuario"]) && isset($_POST["password"]) ) {
  unset($_SESSION["username"]);
  try {
    $userDB = htmlentities(trim($_POST['usuario']));
    $pwDB = htmlentities(trim($_POST['password']));
    $pdo = new PDO('mysql:host=localhost;port=3306;dbname=controlalarmas;charset=utf8','conectar', 'conectar');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
  } catch (PDOException $e) {
      $_SESSION['error_msg'] = "¡Error!: ".$e->getMessage();
      return;
      //die();
  }
  
  $sth = $pdo->prepare('SELECT COUNT(*) FROM usuarios WHERE appUser = :user');
  $sth->bindParam(':user', $userDB, PDO::PARAM_STR);
  $sth->execute();
  
  //$sql = "SELECT COUNT(*) FROM usuarios WHERE appUser = '$userDB'";
  //$resultado = $pdo->query($sql);
  $resultado = $sth->execute();
  /// Si no devuelve false quiere decir que hay una coincidencia por lo cual el usuario existe
  if ($resultado !== false) {
    /* Comprobar el número de filas que coinciden con la sentencia SELECT */
    if ($sth->fetchColumn() > 0) {
      $sth1 = $pdo->prepare('SELECT idusuario, appUser, appPwd, nombre, apellido, estado, tamPagina, limiteSelects FROM usuarios WHERE appUser = :usuario');
      $sth1->bindParam(':usuario', $userDB, PDO::PARAM_STR);
      $sth1->execute();
      $row = $sth1->fetch(PDO::FETCH_ASSOC);
      //$stmt = $pdo->query("SELECT idusuario, appUser, appPwd, nombre, apellido, estado FROM usuarios WHERE appUser = '$userDB'");
      //$row = $stmt->fetch(PDO::FETCH_ASSOC);
      
      if ($row['appPwd'] === sha1($pwDB)){
        /// Chequeo si el usuario está o no activo:
        if ($row['estado'] === 'activo'){
          $_SESSION['tiempo'] = time();
          //Si el usuario existe, seteo las variables de sesión y cookies (user_id y username), y lo redirijo a la página principal:
          $_SESSION['user_id'] = $row['idusuario'];
          $_SESSION['username'] = $row['appUser'];
          $_SESSION['usuarioReal'] = $row['nombre']." ".$row['apellido'];
          if (($row['tamPagina'] !== null)&&($row['tamPagina'] !== '')){
            $_SESSION['tamPagina'] = $row['tamPagina'];
          }
          if (($row['limiteSelects'] !== null)&&($row['limiteSelects']) !== ''){
            $_SESSION['limiteSelects'] = $row['limiteSelects'];
          }
          require_once('data/config.php');
          require_once('data/escribirLog.php');
          setcookie('tiempo', time(), time()+TIEMPOCOOKIE);
          $_SESSION["success"] = " -- ¡¡Bienvenid@ ".strtoupper($row['appUser'])."!! --";
          $inicioSesion = "Inició sesión.";
          $log = true;
          escribirLog($inicioSesion, $log);
          header( 'Location: subirArchivo.php' ) ;
          return;
        }
        else {
          $_SESSION['error_msg'] = "Lo siento <font class='usuarioIndex'>".strtoupper($userDB)."</font>, tu estado es <font class='usuarioIndex'>".$row['estado']."</font><br>Por favor consulta con el administrador.<br>";
          header('Location: index.php');
          return;
        }  
      }
      /* Hay usuarios coincidentes, pero no con esa contraseña */
      else {
        $_SESSION['error_msg'] = "Lo siento <font class='usuarioIndex'>".strtoupper($userDB)."</font>, la contraseña ingresada no es correcta.<br>";
        header('Location: index.php');
        return;
      }     
    }
    /* No coincide ningua fila; no hay usuarios */
    else {
      $_SESSION['error_msg'] = "Lo siento, <font class='usuarioIndex'>".strtoupper($userDB)."</font> NO está habilitado para ingresar al programa.<br>";
      header('Location: index.php');
      return;
    }
  }
}
else {
  if (isset($_SESSION['user_id'])) 
    {
    header('Location: subirArchivo.php');
    return;
  }
}
require_once ('head.php');
?>
  <body>
<?php
  require_once ('header.php');
?>
  <main>
    <div id='main-content' class='container-fluid'>
      <br>  
      <h1>Acceso al sistema:</h1>
      <h3>
<?php
  if ( isset($_SESSION['error_msg']) ) {
    echo('<p style="color:white">'.$_SESSION['error_msg']."</p>\n");
    unset($_SESSION['error_msg']);
  }
?>
      </h3>  
      <br>
      <form method='post' name='frmlogin' id='frmLogin'>
        <table class='tabla2' name='tblLogin'> 
          <th colspan='2' class="tituloTabla">INGRESO</th>
          <tr>
              <th align='left'><font class='negra'>Usuario:</font></th>
              <td align='center'><input type='text' name='usuario' title='Ingresar el nombre del usuario' placeholder='Nombre de Usuario' id='nombreUsuario' maxlength='15' size='9' autofocus='true' class='agrandar' value=' '></td>
          </tr>
          <tr>
              <th align='left'><font class='negra'>Password:</font></th>
              <td align='center'><input type='password' name='password' id='password' placeholder="Contraseña" title='Ingresar la contraseña para el usuario' maxlength='15' size='9' class='agrandar' value=''></td>
          </tr>    
          <tr>
              <td colspan='2' class='pieTabla'><input type='submit' value='Log In' name='login' title='Ingresar al sistema' id='login' class='boton' align='center'/></td>
          </tr>
        </table>
      </form>
    </div>
  </main>
<?php
require_once ('footer.php');
?>
</body>
