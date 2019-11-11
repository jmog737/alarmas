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
        var mensajeExpirada = '';
        var tituloExpirada = '';
        if (usuarioViejo === 'NO LOGUEADO'){
          mensajeExpirada = "NO has iniciado sesión<br>Por favor, loguéate.";
          tituloExpirada = "ATENCIÓN";
        }
        else {
          var mostrarSesion = '';
          tituloExpirada = '¡¡ATENCIÓN '+usuarioViejo.toUpperCase()+'!!';
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
          
          if (user === 'COOKIE'){
            mensajeExpirada = "Sesión inactiva "+mostrarSesion+".<br>Por seguridad, ¡vuelve a loguearte!.<br><br>"+'Motivo: '+user;
            
          }
          else {
            mensajeExpirada = "Sesión inactiva "+mostrarSesion+".<br>Por seguridad, ¡vuelve a loguearte!.<br><br>"+'Motivo: '+user+"<br>Último tiempo: "+oldTime+'<br>Tiempo actual: '+temp;
          }   
        }
        $("#tituloAdvertencia").html(tituloExpirada);
        $("#mensajeAdvertencia").html(mensajeExpirada);
        $("#modalAdvertencia").modal("show");
        $("#caller").val("cookie");
      }
      else {
        document.getElementById("usuarioSesion").value = user;
        document.getElementById("userID").value = user_id;
        document.getElementById("timestampSesion").value = timestamp;
      }
    }
  };

  xmlhttp.open("GET", "data/estadoSesion.php?c="+cookie+"", true);
  xmlhttp.send();
}
/********** fin verificarSesion(mensaje, cookie) **********/

