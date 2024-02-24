/*
* Funcion que detecta los mensajes de form-env y los desaparece luego de 3 segundos
*/
$(document).ready(function() {
    if ($('.form-env').length > 0) {
      setTimeout(function() {
        $('.form-env').fadeOut('slow', function() {
          $(this).remove();
        });
      }, 3000);
    }
  });
  