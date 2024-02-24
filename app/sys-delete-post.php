<?php
require_once('../server/config.php');
$myNameSession = $_SESSION['usuario'];

/*
* Eliminamos posts si hay datos
*/
if ($_SESSION['mod']) {

   if (isset($_GET['d']) && !empty($_GET['d'])) {
    $postId = desencriptar($_GET['d'], $clave);

    // Obtener la ruta de la imagen asociada a la publicación
    $stmtPost = $conn->prepare("SELECT * FROM publicaciones WHERE id = :post_id");
    $stmtPost->execute([':post_id' => $postId]);
    $rowPost = $stmtPost->fetch();

    if ($rowPost) {
      $ownerID = $rowPost['user_id'];
      // Obtener la ruta de la imagen asociada a la publicación
      $stmtImage = $conn->prepare("SELECT imagen FROM post_images WHERE post_id = :post_id");
      $stmtImage->execute([':post_id' => $postId]);
      $rowImage = $stmtImage->fetch();

      // Eliminar la imagen de la tabla "post_images" y del sistema de archivos
      if ($rowImage) {
        $rutaImagen = $rowImage['imagen'];
        $stmtDeleteImage = $conn->prepare("DELETE FROM post_images WHERE post_id = :post_id");
        $stmtDeleteImage->execute([':post_id' => $postId]);
        unlink($rutaImagen);
      }

      // Eliminar la publicación de la tabla "publicaciones"
      $stmt = $conn->prepare("DELETE FROM publicaciones WHERE id = :id LIMIT 1");
      $stmt->execute([':id' => $postId]);
      
      #Guardar log
      $accion = "Elimino el post con la id $postId publicado por el usuario #$ownerID";
      $stsend = $conn->prepare("INSERT INTO logs_mods (mod_user,accion) VALUES (:mod, :accion) ");
      $stsend->execute([
        ':mod' => $myNameSession,
        ':accion' => $accion
      ]);

      header("Location: ../?env=60");
      exit();
    }
  }
} else {
  header("Location: ../");
  exit();
}

?>