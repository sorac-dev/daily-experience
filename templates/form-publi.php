<div class="p_container">
    <form method="POST" enctype="multipart/form-data" style="min-width: 520px; overflow-x: hidden;">
        <h2>Crear una nueva publicacón</h2>
        <div class="p_contenido">
            <textarea name="mensaje" cols="30" rows="10" class="form_textarea" placeholder="¿Que experiencia quieres compartir? (Usa **text** para convertir en negrilla o  *text* para convertir en cursiva)"></textarea>
            <p id="character-count"></p>
        </div>
        <label class="f-inline">
            <button type="button" id="nuevo-boton" class="btn-new bg-white subirImage">Subir archivo</button>
            <p id="nombre-archivo"></p>
            <input type="file" name="imagen" id="archivo-oculto" class="btn-new bg-white subirImage" style="display: none;">
        </label>
        <div class="p_footer">
            <div class="f-right">
                <button type="submit" class="btn-new bg-green">Publicar</button>
            </div>
        </div>
        <?php if(isset($mensaje)){echo "<p class='form-env'>$message</p>";} ?>
    </form>
</div>

<script>
document.getElementById("nuevo-boton").addEventListener("click", function() {
    var archivoOculto = document.getElementById("archivo-oculto");
    archivoOculto.click();
});

document.getElementById("archivo-oculto").addEventListener("change", function() {
    var archivoSeleccionado = this.files[0];
    var nombreArchivo = document.getElementById("nombre-archivo");
    nombreArchivo.textContent = archivoSeleccionado.name;
});
</script>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['mensaje'])) {
        $mensaje = $_POST['mensaje'] ?? '';
        $mensaje = trim($mensaje);

        if (!empty($mensaje)) {
            $stmt = $conn->prepare("INSERT INTO publicaciones (user_id, contenido) VALUES (:id, :contenido)");
            $stmt->execute([
                ':id' => $myID,
                ':contenido' => $mensaje,
            ]);

            $postId = $conn->lastInsertId();

            if (isset($_FILES['imagen'])) {
                $imagen = $_FILES['imagen'] ?? null;
                if ($imagen && isset($imagen['tmp_name']) && is_uploaded_file($imagen['tmp_name'])) {
                    $permitidos = ['png', 'jpg', 'jpeg', 'gif', 'mp4', 'jfif', 'webp', 'svg'];
                    $nombreArchivo = strtolower($imagen['name']);
                    $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
                    $tamañoMaximo = 10 * 1024 * 1024; // 10 MB

                    if (in_array($extension, $permitidos) && $imagen['size'] <= $tamañoMaximo) {
                        $directorioDestino = "./uploads_users/" . date('Y/m/d') . "/";
                        if (!file_exists($directorioDestino)) {
                            mkdir($directorioDestino, 0777, true);
                        }

                        $nombreArchivo = uniqid() . '_' . $imagen['name'];
                        $rutaArchivo = $directorioDestino . $nombreArchivo;

                        if (move_uploaded_file($imagen['tmp_name'], $rutaArchivo)) {
                            $stmt = $conn->prepare("INSERT INTO post_images (post_id, imagen) VALUES (:post_id, :imagen)");
                            $stmt->execute([
                                ':post_id' => $postId,
                                ':imagen' => $rutaArchivo,
                            ]);
                            header("Location: /");
                            exit;
                        } else {
                            header("Location: /?env=3");
                            exit;
                        }
                    } else {
                        header("Location: /?env=2");
                        exit;
                    }
                }
            }
            header("Location: /");
            exit;
        } else {
            header("Location: /?env=1");
            exit;
        }
    }
}


if (isset($_GET['env'])) {
    $notifications = [
        1 => 'No puedes dejar ninguna casilla vacia.',
        2 => 'Tipo de archivo no permitido o tamaño excedido.',
        3 => 'Error al procesar la información.',
        4 => 'Esa publicación no existe o fue borrada',
        60 => 'Post eliminado correctamente.',
    ];

    if (isset($notifications[$_GET['env']])) {
        $message = $notifications[$_GET['env']];
        echo "<p class='form-env'>$message</p>";
    }
}
?>
