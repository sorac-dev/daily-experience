<div id="globalContainer" class="flex_center">
      <div class="flex-coloums">
          <!--Formulario para hacer una publicación-->
          <?php require_once ('templates/form-publi.php'); 
          function formatearTexto($texto) {
            // Convertir **text** en negrita
            $texto = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $texto);
          
            // Convertir *text* en cursiva
            $texto = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $texto);
          
            // Buscar texto en cursiva dentro de negrita y formatearlo
            $texto = preg_replace_callback('/<strong>(.*?)<\/strong>/', function($matches) {
              $texto = $matches[1];
              $texto = formatearTexto($texto); // Llamada recursiva para formatear el texto en negrita
              return "<strong>$texto</strong>";
            }, $texto);
          
            return $texto;
          }
          
          ?>
          <!--Comienza contenido de las publicaciones-->
          <div class="space"></div>
          <div class="publicaciones" id="publicaciones">
            <div class="container_publis" id="pubis_user">
                <?php
                $stmt = $conn->prepare("SELECT * FROM publicaciones ORDER BY fecha DESC");
                $stmt->execute();

                while ($dataP = $stmt->fetch()) {
                    if ($dataP) {
                        /*
                        * Datos del post, desencriptamos
                        */
                            
                            $contenido = htmlspecialchars($dataP['contenido']);
                            $contenido = nl2br($contenido);

                            $imgPost = $dataP['imagen'];
                            $ownerID = $dataP['user_id'];
                            $postID = $dataP['id'];

                            // Buscar los hashtags en el contenido del mensaje
                            preg_match_all('/#\w+/', $contenido, $hashtags);
                            $hashtags = $hashtags[0]; // Array con los hashtags encontrados

                            $stmtUser = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
                            $stmtUser->execute([
                                ':id' => $ownerID
                            ]);
                            $row = $stmtUser->fetch();
                            $ownerName = $row['username'];
                            $avatarURL = $row['avatar'];

                            // Reemplazar los hashtags con enlaces
                            foreach ($hashtags as $hashtag) {
                                $enlaceHashtag = '<a href="' . $hashtag . '" class="hashtag" style="text-decoration: none;">' . $hashtag . '</a>';
                                $contenido = str_replace($hashtag, $enlaceHashtag, $contenido);
                            }
                        ?>
                        <div class="card_publi">
                            <div class="card_publi_head f-inline">
                                <div style="background-image:url(<?=$avatarURL?>);" class="card_avatar"></div>
                                <p><?=$ownerName?> <i class="bi bi-patch-check-fill" style="color: var(--color-verificado);"></i> <div class="badge_mod" title="Moderador"></div></p>
                            </div>
                            <div class="card_publi_contenido">
                                <p class="p_cont"><?=formatearTexto($contenido);?></p>
                                <button class="ver-mas">Ver más</button>
                            </div>
                            <?php
                            if (substr($imgPost, -4) === '.mp4') {
                            ?>
                            <link href="https://vjs.zencdn.net/8.3.0/video-js.css" rel="stylesheet" />

                            <link href="https://unpkg.com/@videojs/themes@1/dist/city/index.css"rel="stylesheet"/>

                            <video id="videoPost" class="card_publi_contenido_video video-js vjs-theme-city" height="320" controls>
                            <source src="<?=$imgPost?>" type="video/mp4">
                            </video>
                            <?php } else { ?>
                            <img src="<?=$imgPost?>" alt="comida" class="card_publi_contenido_imagen">
                            <?php } ?>
                            <div class="card_contenido_comentar">
                                <i class="bi bi-chat-right-text-fill"></i> <span class="card_text_comentar">Comentar</span>
                            </div>
                        </div>
                    <?php } }?>
            </div>
          </div>
      </div>
    </div>
<style>
      .card_publi_contenido .p_cont {
        max-height: 70px; /* Altura máxima del contenido visible */
        overflow: hidden;
        transition: max-height 0.3s ease; /* Transición suave al expandir/cerrar */
      }
      .card_publi_contenido.expandido .p_cont {
         max-height: none; /* Expandir el contenido al máximo */
     }
     .card_publi_contenido .ver-mas {
        display: none; /* Ocultar el botón inicialmente */
        cursor: pointer;
        background: transparent;
        border: 1px solid;
        color: #7289da;
        border-radius: 4px;
    }
</style>

<script >
// Obtener todos los elementos con la clase "post"
 var posts = document.getElementsByClassName("card_publi_contenido");

// Iterar sobre cada post y agregar el evento clic al botón "Ver más"
Array.from(posts).forEach(function(post) {
    var verMasBtn = post.querySelector(".ver-mas");
    var content = post.querySelector(".p_cont");

    // Verificar si el contenido excede la altura máxima
    if (content.scrollHeight > 70) {
        verMasBtn.style.display = "block"; // Mostrar el botón
    }

    verMasBtn.addEventListener("click", function() {
        post.classList.toggle("expandido");

        if (post.classList.contains("expandido")) {
            verMasBtn.textContent = "Ver menos";
        } else {
            verMasBtn.textContent = "Ver más";
        }
    });
});

document.addEventListener('DOMContentLoaded', function () {
    const videoElement = document.getElementById('videoPost');
    
    if (videoElement) {
        const player = videojs(videoElement);
    }
});

</script>
<script src="https://vjs.zencdn.net/8.3.0/video.min.js"></script>