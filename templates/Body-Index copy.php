<div id="globalContainer" class="flex_center">
    <div class="flex-coloums">
        <!--Formulario para hacer una publicación-->
        <?php require_once ('templates/form-publi.php'); ?>
        <!--Comienza contenido de las publicaciones-->
        <div class="space"></div>
        <div class="publicaciones" id="publicaciones">
            <div class="container_publis" id="post_users">
                <!-- Aqui cargar ajax -->
            </div>

        </div>
    </div>
</div>

<script>
  
$(document).ready(function() {
  var currentPage = 1; // Página inicial
  var loading = false; // Variable para controlar si ya se está cargando contenido

  // Función para cargar más publicaciones
  function loadMorePosts(scrollLoad) {
    if (!loading) {
      loading = true;
      $.ajax({
        url: 'app/ajax_publicaciones.php', // Ruta a tu archivo PHP para cargar las publicaciones
        type: 'GET',
        data: { page: currentPage },
        dataType: 'html',
        success: function(response) {
          if (response.trim() !== '') {
            if (scrollLoad) {
              // Si es una carga dinámica con scroll, agrega las nuevas publicaciones al principio del contenedor
              $('#post_users').prepend(response);
            } else {
              // Si es la carga inicial de la página, reemplaza todo el contenido del contenedor con las publicaciones obtenidas
              $('#post_users').html(response);
            }
            currentPage++;
            loading = false;
            applyVerMasEvent();
            initializeVideoJS();
          } else {
            console.log('No hay más publicaciones');
            loading = false;
          }
        },
        error: function(xhr, status, error) {
          console.log('Error al cargar las publicaciones: ' + error);
          loading = false;
        }
      });
    }
  }

  // Carga inicial de las publicaciones
  loadMorePosts(false);

  // Carga dinámica con scroll cada 30 segundos
  setInterval(function() {
    loadMorePosts(true);
  }, 30000); // 30 segundos (puedes ajustar este valor según tus necesidades)
});

// Función para aplicar el evento clic del botón "Ver más"
function applyVerMasEvent() {
  var posts = $('.card_publi_contenido');

  Array.from(posts).forEach(function(post) {
    var verMasBtn = $(post).find('.ver-mas');
    var content = $(post).find('.p_cont');

    if (content.prop('scrollHeight') > 70) {
      verMasBtn.show();
    }

    verMasBtn.on('click', function() {
      $(post).toggleClass('expandido');

      if ($(post).hasClass('expandido')) {
        verMasBtn.text('Ver menos');
      } else {
        verMasBtn.text('Ver más');
      }
    });
  });
}

// Función para inicializar Video.js en los elementos de video
function initializeVideoJS() {
  var videos = $('.card_publi_contenido_video video');

  Array.from(videos).forEach(function(video) {
    if (!video.classList.contains('video-js')) {
      video.classList.add('video-js', 'vjs-default-skin');
      video.setAttribute('controls', '');
      video.setAttribute('preload', 'auto');
      video.setAttribute('data-setup', '{}');
      videojs(video);
    }
  });
}



</script>
<?php
/*
* Creamos css solo para staff
*/
if ($_SESSION['mod']) {
  echo '
  <style>
  .delete-p {
      display: block;
      float: right;
  }
  .delete-p a {
    text-decoration: none;
    color: red;
    font-weight: bold;
    background: transparent;
    border: 1px solid red;
    padding: 3px 12px;
    border-radius: 5px;
  }
  </style>
  ';
}
?>
