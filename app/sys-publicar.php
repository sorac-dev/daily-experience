<?php
require_once('../server/config.php');

if (isset($_POST['mensaje']) && isset($_FILES['imagen'])) {
    $msj = $_POST['mensaje'];
    $imagen = $_FILES['imagen'];

    // Validar y sanitizar los datos de entrada
    $msj = trim($msj); // Eliminar espacios en blanco al inicio y final del mensaje
    

    if (!empty($msj)) {
        // Validar el tipo de archivo y el tamaño
        $permitidos = ['png', 'jpg', 'jpeg', 'gif', 'mp4'];
        $nombreArchivo = strtolower($imagen['name']);
        $extension = pathinfo($nombreArchivo, PATHINFO_EXTENSION);
        $tamañoMaximo = 10 * 1024 * 1024; // 10 MB

        if (!in_array($extension, $permitidos)) {
            header("Location: ../?env=3");
            exit();
        } else if ($imagen['size'] > $tamañoMaximo) {
            header("Location: ../?env=2");
            exit();
        } else {
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
                // Guardar el nombre del archivo en la base de datos
                $stmt = $conn->prepare("INSERT INTO publicaciones (user_id, categoria_id, contenido, imagen) VALUES (:id, :c_id, :contenido, :img)");
                $stmt->execute([
                    ':id' => $myID,
                    ':c_id' => 1,
                    ':contenido' => encriptar($msj, $clave),
                    ':img' => $rutaArchivo,
                ]);

                if (!$stmt) {
                    $errorInfo = $stmt->errorInfo();
                    $errorMessage = $errorInfo[2];
                    echo "Error al ejecutar la consulta: " . $errorMessage;
                    exit();
                }

            } else {
                //header("Location: ../?env=1");
                echo 'error archivo';
                exit();
            }
        }
    }
}

?>