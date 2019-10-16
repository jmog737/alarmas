/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
function todo(){
  $('#dtBasicExample').DataTable({
    columnDefs: [{
    orderable: false,
    targets: 3
    }]
  });
  $('.dataTables_length').addClass('bs-select');
}

$(document).ready(todo());

//$(document).ready(function () {
//  $('#dtBasicExample').DataTable();
//  $('.dataTables_length').addClass('bs-select');
//});

