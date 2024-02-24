<div id="globalContainer" class="flex_center">
    <div class="flex-coloums">
        <?php
          if (!isset($_GET['p'])) {
        ?>
        <!--Formulario para hacer una publicación-->
        <?php require_once ('templates/form-publi.php'); ?>
        <!--Comienza contenido de las publicaciones-->
        <div class="space"></div>
        <div class="publicaciones" id="publicaciones">
            
            <div class="container_publis" id="post_users">
                <!-- Aqui cargar ajax -->
            </div>
            <?php
            } else {
              if (isset($_GET['p']) && !empty($_GET['p'])) {
                $idPost = desencriptar($_GET['p'], $clave);

                $stmt = $conn->prepare(
                  "SELECT * FROM publicaciones WHERE id = :id"
                );

                $stmt->bindValue(":id", $idPost, PDO::PARAM_INT);
                $stmt->execute();
                $dataP = $stmt->fetch();
                if ($dataP) {
                  /*
                  * Datos del post, desencriptamos
                  */

                  $ownerID = $dataP["user_id"];
                  $postID = $dataP["id"];
                  $timestamp2 = strtotime($dataP['fecha']);

                  $contenido = generarHTMLPost($conn, $postID);

                  #Datos del usuario
                  $row = give_datauser($ownerID, $conn);
                  $ownerName = $row["username"];
                  $avatarURL = $row["avatar"];

                ?>
                <div class="space"></div>
                <div class="publicaciones" id="publicaciones">
                    <div class="container_publis">
                        <div class="card_publi">
                          <div class="card_publi_head">
                              <div style="background-image:url(<?= $avatarURL ?>);" class="card_avatar"></div>
                              <div class="box_cont_nombre"><?= $ownerName ?>
                              <?=badges_user($conn,$ownerID);?>
                              </div>
                              <div class="box_cont_fecha"><?=getRelativeTime($timestamp2)?></div>
                          </div>
                            <?=
                            /**
                             * Cargamos contenido de la publicacion
                             */
                            $contenido
                            ?>
                            <div class="box__coments">
                            <?php
                              $stmt = $conn->prepare("SELECT * FROM comentarios WHERE post_id = :idPost ORDER BY fecha DESC");
                              $stmt->execute([
                                  ':idPost' => $idPost
                              ]);
                              while ($dataCom = $stmt->fetch()) {
                                  $userid = $dataCom['user_id'];
                                  $timestamp = strtotime($dataCom['fecha']);
                                  $dataU = give_datauser($userid, $conn);

                                  $contenido = procesarContenido($dataCom["contenido"]);
                                  ?>
                                  <div class="box_com_user">
                                      <div style="background-image: url(<?=$dataU['avatar'];?>);" class="card_avatar"></div>
                                      <div class="box_com_cont">
                                          <div class="box_cont_report"><a href="#" title="Reportar comentario"><i class="bi bi-shield-fill-exclamation"></i></a></div>
                                          <div class="box_cont_nombre"><?=$dataU['username'];?> <?=badges_user($conn,$userid);?></div>
                                          <div class="box_cont_fecha"><?=getRelativeTime($timestamp)?></div>
                                          <div class="box_cont_comentario"><?=$contenido ?> </div>
                                      </div>
                                  </div>
                                  <?php
                              }
                              ?>
                            </div>
                            <div class="box__coments">
                                <form action="app/sys-comentar.php" method="post" id="comentarioForm">
                                    <div class="box_textarea" id="comentario" contenteditable="true"></div>
                                    <input type="hidden" name="comentario" id="contenido_input">
                                    <input type="hidden" name="token" value="<?=$_GET['p']?>">
                                    <button type="submit" class="send_button"><i class="bi bi-send-fill"></i></button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                } else {
                  header("Location: ./?env=4");
                  exit;
                }
              } else {
                header("Location: ./?env=3");
                exit;
              }
             } ?>
        </div>
    </div>
</div>

<script>
var currentPage = 1;
var loading = false;

// Función para cargar más publicaciones
function loadMorePosts() {
  if (!loading) {
    loading = true;
    $.ajax({
      url: 'app/ajax_publicaciones.php',
      type: 'GET',
      data: { page: currentPage },
      dataType: 'html',
      success: function(response) {
        if (response.trim() !== '') {
          $('#post_users').append(response);
          currentPage++;
          loading = false;
          applyVerMasEvent();
        } else {
          // console.log('No hay más publicaciones');
          loading = false;
        }
      },
      error: function(xhr, status, error) {
        // console.log('Error al cargar las publicaciones: ' + error);
        loading = false;
      }
    });
  }
}

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

// Función para detectar el desplazamiento de la página y cargar más publicaciones si es necesario
$(window).scroll(function() {
  if ($(window).scrollTop() + $(window).height() >= $(document).height() - 200) {
    loadMorePosts();
  }
});

// Carga las publicaciones iniciales al cargar la página
loadMorePosts();

// Comentarios
$(document).ready(function() {
    $(document).on('click', '#comentar', function() {
      // Obtener el valor del atributo "tokenpost" del botón
      var postToken = $(this).attr('tokenpost');

      // Verificar si el campo postToken tiene un valor
      if (postToken) {
        // Construir la URL de redirección con el valor de postToken
        var redirectUrl = './?p=' + encodeURIComponent(postToken);

        // Redirigir a la URL
        window.location.href = redirectUrl;
      } else {
        console.error('El campo postToken está vacío.');
      }
    });
  });

  $(document).ready(function() {
    $('#comentario').keydown(function(event) {
      if (event.keyCode == 13 && !event.shiftKey) {
        event.preventDefault();
        $('#comentarioForm').submit();
      }
    });

    $('#comentarioForm').submit(function(event) {
      var comentarioDiv = $('#comentario');
      var contenidoInput = $('#contenido_input');
      var contenido = comentarioDiv.text().trim();

      if (contenido === "") {
        event.preventDefault(); // Evitar el envío del formulario si el contenido está vacío
        return false;
      } else {
        contenidoInput.val(contenido);
        return true; // Permitir el envío del formulario si el contenido no está vacío
      }
    });
  });

</script>
<?php
/*
* Creamos css solo para staff
*/
if (isset($_SESSION['mod'])) {
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