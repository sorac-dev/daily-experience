<div class="p_container">
    <form method="POST" enctype="multipart/form-data" style="min-width: 520px; overflow-x: hidden;">
        <h2>Crear una nueva publicacón</h2>
        <div class="p_contenido">
            <textarea name="mensaje" cols="30" rows="10" class="form_textarea" placeholder="¿Que experiencia quieres compartir? (Usa **text** para convertir en negrilla o  *text* para convertir en cursiva)"></textarea>
            <p id="character-count"></p>
        </div>
        <label class="f-inline">
            <button type="button" id="nuevo-boton" class="btn-new bg-grisdc subirImage">Subir archivo</button>
            <p id="nombre-archivo"></p>
            <input type="file" name="imagen" id="archivo-oculto" class="btn-new bg-grisdc subirImage" style="display: none;">
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
    $mensaje = $_POST['mensaje'];
    $imagen = $_FILES['imagen'];

    // Validar y sanitizar los datos de entrada
    $mensaje = trim($mensaje); // Eliminar espacios en blanco al inicio y final del mensaje

    if (!empty($mensaje) && isset($imagen['tmp_name']) && is_uploaded_file($imagen['tmp_name'])) {
        $permitidos = ['png', 'jpg', 'jpeg', 'gif', 'mp4'];
        $nombreArchivo = strtolower($imagen['name']);
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $tamañoMaximo = 10 * 1024 * 1024; // 10 MB

        if (in_array($extension, $permitidos) && $imagen['size'] <= $tamañoMaximo) {
            // Ruta de destino donde se guardarán las imágenes
            $directorioDestino = "./uploads_users/" . date('Y/m/d') . "/";

            // Crear directorios si no existen
            if (!file_exists($directorioDestino)) {
                mkdir($directorioDestino, 0777, true);
            }

            // Generar un nombre único para el archivo utilizando uniqid()
            $nombreArchivo = uniqid() . '_' . $imagen['name'];

            // Construir la ruta completa del archivo de destino
            $rutaArchivo = $directorioDestino . $nombreArchivo;

            // Mover el archivo de la ubicación temporal a la ubicación de destino
            if (move_uploaded_file($imagen['tmp_name'], $rutaArchivo)) {
                // Insertar datos en la base de datos
                $stmt = $conn->prepare("INSERT INTO publicaciones (user_id, categoria_id, contenido, imagen) VALUES (?, ?, ?, ?)");
                $stmt->execute([$myID, 1, $mensaje, $rutaArchivo]);
                header("Location: /");
            } else {
                header("Location: /?env=3");
            }
        } else {
            header("Location: /?env=2");
        }
    } else {
        header("Location: /?env=1");
    }
}


if (isset($_GET['env'])) {
  $notifications = array(
      1 => 'No puedes dejar ninguna casilla vacia.',
      2 => 'Tipo de archivo no permitido o tamaño excedido.',
      3 => 'Error al procesar la información.',
  );

  if (isset($notifications[$_GET['env']])) {
      $message = $notifications[$_GET['env']];
      echo "<p class='form-env'>$message</p>";
  }
}
?>
