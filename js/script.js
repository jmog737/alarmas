/**
///  \file script.js
///  \brief Archivo que contiene todas las funciones de Javascript.
///  \author Juan Martín Ortega
///  \version 1.0
///  \date Setiembre 2017
*/

/**
\brief Función que se ejecuta al cargar la página.
En la misma se ve primero desde que página se llamó, y en base a eso
se llama a la función correspondiente para cargar lo que corresponda (actividades, referencias, etc.)
Además, en la función también están los handlers para los distintos eventos jquery.
*/
function todo () {
  ///Levanto la url actual: 
  var urlActual = jQuery(location).attr('pathname');
  var parametros = jQuery(location).attr('search');
  var remplaza = /\+|%20/g;
  if (parametros) {
    //parametros = unescape(parametros);
    parametros = parametros.replace(remplaza, " ");
  }
  var res = urlActual.split("/");
  var tam = res.length;
  var dir = res[tam-1];
  ///Según en que url esté, es lo que se carga:
  switch (dir) {
    case "movimiento.php":  {
                            if (parametros) {
                              var temp = parametros.split('?');
                              var temp1 = temp[1].split('&');
                              var temp2 = temp1[0].split('=');
                              var temp3 = temp1[1].split('=');
                              var temp4 = temp1[2].split('=');
                              var temp5 = temp1[3].split('=');
                              var h = temp2[1]; 
                              var tipo = decodeURI(temp4[1]);
                              var fecha = decodeURI(temp5[1]);
                              var idprod = parseInt(temp3[1], 10);
                              setTimeout(function(){cargarMovimiento("#main-content", h, idprod, tipo, fecha)}, 100);                                          
                            }
                            else {
                              setTimeout(function(){cargarMovimiento("#main-content", "", "-1", "", "")}, 100);
                            }
                            break;    
                          }
    case "index.php": break;
                                    
    case "producto.php":  if (parametros){
                                          var temp = parametros.split('?');
                                          var temp1 = temp[1].split('=');
                                          var id = temp1[1];
                                          setTimeout(function(){cargarProducto(id, "#content")}, 100);
                                          setTimeout(function(){cargarBusquedaProductos("#selector")}, 100);                                          
                                          setTimeout(function(){habilitarProducto()}, 450);
                                          setTimeout(function(){$("#comentarios").focus()}, 460);
                                        }
                          else {
                            setTimeout(function(){cargarBusquedaProductos("#selector")}, 100);
                            setTimeout(function(){cargarProducto(0, "#content")}, 100);
                          }
                          break;                                                                      
    case "busquedas.php": {
                          if (parametros) {
                            var temp = parametros.split('?');
                            var temp1 = temp[1].split('&');
                            var temp2 = temp1[0].split('=');
                            var temp3 = temp1[1].split('=');
                            var temp4 = temp1[2].split('=');
                            var temp5 = temp1[3].split('=');
                            var temp6 = temp1[4].split('=');
                            var temp7 = temp1[5].split('=');
                            var temp8 = temp1[6].split('=');
                            var temp9 = temp1[7].split('=');
                            var temp10 = temp1[8].split('=');
                            var temp11 = temp1[9].split('=');
                            var temp12 = temp1[10].split('=');
                            var temp13 = temp1[11].split('=');
                            var temp14 = temp1[12].split('=');
                            var temp15 = temp1[13].split('=');

                            var hint = temp2[1];
                            var tipMov = temp3[1];
                            var zip = temp4[1];
                            var planilla = temp5[1];
                            var marcaAgua = temp6[1];
                            var id = temp7[1];
                            var ent = decodeURIComponent(temp8[1]);
                            var p = temp9[1];
                            var d1 = temp10[1];
                            var d2 = temp11[1];
                            var tipo = decodeURI(temp12[1]);
                            var user = temp13[1];
                            var estadoMov = temp14[1];
                            var mostrarEstado = temp15[1];
                            //alert('hint: '+hint+'\ntipoMov: '+tipMov+'\nids: '+id+'\nent: '+ent+'\nzip: '+zip+'\nplanilla: '+planilla+'\nmarcaAgua: '+marcaAgua+'\np: '+p+'\nd1: '+d1+'\nd2: '+d2+'\ntipo: '+tipo+'\nuser: '+user);
                            setTimeout(function(){cargarFormBusqueda("#fila", hint, tipMov, id, ent, zip, planilla, marcaAgua, p, d1, d2, tipo, user, estadoMov, mostrarEstado)}, 30); 
                          }
                          else {
                            setTimeout(function(){cargarFormBusqueda("#fila", '', '', '', '', '', '', '', '', '', '', '', '', '', '')}, 30);
                          }
                          break;
                          }
    case "estadisticas.php":  if (parametros) {
                                              var temp = parametros.split('?');
                                              var temp1 = temp[1].split('&');
                                              var tama = temp1.length;
                                              var hacerGrafica = '0';
                                              if (tama !== 1){
                                                var temp2 = temp1[0].split('=');
                                                hacerGrafica = temp2[1];
                                              }
 
                                             if (hacerGrafica ===  '1') {
                                                setTimeout(function(){cargarGrafica("#main-content")}, 100);
                                              }
                                              else {
                                                setTimeout(function(){cargarFormEstadisticas("#main-content")}, 100);
                                              }
                                            }
                              else {
                                setTimeout(function(){cargarFormEstadisticas("#main-content")}, 100);
                              }  
                              break;  
    case "editarMovimiento.php":  if (parametros) {
                                                  var temp = parametros.split('?');
                                                  var temp1 = temp[1].split('=');
                                                  var idmov = temp1[1];
                                                  setTimeout(function(){cargarEditarMovimiento(idmov, "#main-content")}, 30);
                                                }
                                  else {
                                      setTimeout(function(){cargarEditarMovimiento(-1, "#main-content")}, 1000);
                                    }  
                                  break;                                       
    default: break;
  }  

/*****************************************************************************************************************************
/// Comienzan las funciones que manejan los eventos relacionados al RESALTADO de los input.
******************************************************************************************************************************
*/
}

/**
 * \brief Función que envuelve todos los eventos JQUERY con sus respectivos handlers.
 */
$(document).on("ready", todo());
/********** fin on("ready", todo()) **********/