<div class="p_container">
    <form method="post" enctype="multipart/form-data">
        <h2>Crear una nueva publicacón</h2>
        <div class="p_contenido">
            <textarea name="mensaje" cols="30" rows="10" class="form_textarea" placeholder="¿Que experiencia quieres compartir?"></textarea>
        </div>
        <label class="f-inline p_label_ubicacion"><img src="/assets/images/icons/ubicacion.png" width="25px" /> ¿Donde estas?: <input type="text" name="direccion" class="input-text" placeholder="Dirección o lugar especifico." /></label>
        <div class="p_footer">
            <div class="f-right">
                <input type="file" name="imagen" class="btn-new bg-grisdc subirImage">
                <button type="submit" class="btn-new bg-green">Publicar</button>
            </div>
        </div>
    </form>
</div>
<?php
if (isset($_POST['mensaje']) && isset($_POST['direccion']) && isset($_FILES['imagen'])) {
    $msj = $_POST['mensaje'];
    $direccion = $_POST['direccion'];
    $imagen = $_FILES['imagen'];

    // Validar y sanitizar los datos de entrada
    $msj = trim($msj); // Eliminar espacios en blanco al inicio y final del mensaje
    $direccion = trim($direccion); // Eliminar espacios en blanco al inicio y final de la dirección

    if (!empty($msj) && !empty($direccion)) {
        // Validar el tipo de archivo y el tamaño
        $permitidos = ['png', 'jpg', 'jpeg', 'gif'];
        $nombreArchivo = strtolower($imagen['name']);
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $tamañoMaximo = 10 * 1024 * 1024; // 10 MB

        if (!in_array($extension, $permitidos)) {
            echo 'Error, solo se admiten formates (jpg,png y gif).';
        } else if ($imagen['size'] > $tamañoMaximo) {
            echo 'Error, la imagen pesa demasiado, solo se admiten de 10MB hacia abajo.';
        } elseif (limitCadena($direccion)) {
           echo 'La direccion contiene caractenres no permitidos';
        } else {
            // Ruta de destino donde se guardarán las imágenes
            $directorioDestino = "./uploads_users/";

            // Generar un nombre único para el archivo utilizando uniqid()
            $nombreArchivo = uniqid() . '_' . $imagen['name'];

            // Construir la ruta completa del archivo de destino
            $rutaArchivo = $directorioDestino . $nombreArchivo;

            // Mover el archivo de la ubicación temporal a la ubicación de destino
            if (move_uploaded_file($imagen['tmp_name'], $rutaArchivo)) {
                // Guardar el nombre del archivo en la base de datos
                $stmt = $conn->prepare("INSERT INTO publicaciones (user_id, categoria_id, contenido, direccion, imagen) VALUES (:id, :c_id, :contenido, :direccion, :img)");
                $stmt->execute([
                    ':id' => $myID,
                    ':c_id' => 1,
                    ':contenido' => encriptar($msj, $clave),
                    ':direccion' => encriptar($direccion, $clave),
                    ':img' => $rutaArchivo,
                ]);
            } else {
                echo 'Ocurrio un error al subir la imagen.';
            }
        }
    }
}
?>