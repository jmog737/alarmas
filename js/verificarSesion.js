/**
 * \brief Función que chequea las variables de sesión para saber si la misma aún está activa o si ya expiró el tiempo.
 * @param mensaje {String} String con un mensaje opcional usado para debug.
 * @param cookie {String} String que indica si se debe o no actualizar la expiración de la cookie.
 */
function verificarSesion(mensaje, cookie) {
  var xmlhttp = new XMLHttpRequest();
  if (mensaje !== ''){ 
    const dateTime = Date.now();
    const tiempo = Math.floor(dateTime / 1000);
    //alert(mensaje+': '+tiempo);
  }
  else {
    mensaje = 'XXX';
  }
  /*
  onreadystatechange: Defines a function to be called when the readyState property changes.
  readyState property:
    Holds the status of the XMLHttpRequest.
      0: request not initialized 
      1: server connection established
      2: request received 
      3: processing request 
      4: request finished and response is ready
  */
  xmlhttp.onreadystatechange = function() {
    if (this.readyState === 4 && this.status === 200) {
      var myObj1 = JSON && JSON.parse(this.responseText) || $.parseJSON(this.responseText);
      var user = '';
      var user_id = '';
      var sesion = '';
      var timestamp = '';
      var oldTime = '';
      var usuarioViejo = 'ERROR';
      var duracionSesion = myObj1.duracion;
      if (myObj1.length === 0){
        user = 'ERROR';
        user_id = 0;
        sesion = 'expirada';
        timestamp = 0;
        oldTime = 0;
        usuarioViejo = 'ERROR';
      }
      else {
        user = myObj1.user;
        user_id = myObj1.user_id;
        sesion = myObj1.sesion;
        timestamp = myObj1.time;
        oldTime = myObj1.oldTime;
        usuarioViejo = myObj1.oldUser;
      };
      var temp = String(timestamp).substr(-3);

      if (sesion === 'expirada'){
        var mostrarSesion = '';
        ///Se comenta siguiente línea usada para las pruebas:
        //var tempSesion = prompt('Ingrese el tiempo deseado para la sesión: \n');   
        var horas = Math.floor( duracionSesion / 3600 );  
        var minutos = Math.floor( (duracionSesion % 3600) / 60 );
        var segs = duracionSesion % 60;
        //Anteponiendo un 0 a los minutos si son menos de 10 
        //minutos = minutos < 10 ? '0' + minutos : minutos;
        //Anteponiendo un 0 a los segundos si son menos de 10 
        //segs = segs < 10 ? '0' + segs : segs;
        if (horas === 0){
          if (minutos === 0){
            mostrarSesion = segs+' segs';
          }
          else {
            if (segs === 0){
              mostrarSesion = minutos+'min';
            }
            else {
              mostrarSesion = minutos+'min '+segs+'segs';
            }
          }
        }
        else {
          if ((minutos === 0)&&(segs === 0)){
            mostrarSesion = horas+'h';
          }
          else {
            if (segs === 0){
              mostrarSesion = horas+'h '+minutos+'min';
            }
            else {
              mostrarSesion = horas+'h '+minutos+'min '+segs+'segs';
            }
          }  
        }
        //alert('Motivo: '+user+'\n'+usuarioViejo.toUpperCase()+":\nTú sesión ha estado inactiva por más de "+mostrarSesion+"\nPor favor, por seguridad, ¡vuelve a loguearte!.\n\ntiempo seteado: "+oldTime+'\nactual: '+temp+'\n\nDuración Sesión: '+duracionSesion+'s\nmensaje: '+mensaje);
        alert(usuarioViejo.toUpperCase()+":\n\nTú sesión ha estado inactiva por más de "+mostrarSesion+"\nPor favor, por seguridad, ¡vuelve a loguearte!.\n\n"+'Motivo: '+user+"\nTiempo seteado: "+oldTime+'\nTiempo actual: '+temp);
        window.location.assign("salir.php");
      }
      else {
//        $("#usuarioSesion").val(user);
//        $("#userID").val(user_id);
//        $("#timestampSesion").val(timestamp);
//        $("#main-content").focus();
        //alert('¡Actualicé!\n\nTiempo viejo: '+oldTime+'\nNuevo tiempo: '+temp+'\n\nDuración Sesión: '+duracionSesion+'\nmensaje: '+mensaje+'\n\nsesion: '+sesion+'\nDesde: '+window.location.href);
        
        document.getElementById("usuarioSesion").valueOf(user);
        document.getElementById("userID").valueOf(user_id);
        document.getElementById("timestampSesion").valueOf(timestamp);
      }
    }
  };

  xmlhttp.open("GET", "data/estadoSesion.php?c="+cookie+"", true);
  xmlhttp.send();
}
/********** fin verificarSesion(mensaje, cookie) **********/

