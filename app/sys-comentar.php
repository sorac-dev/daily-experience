<?php
require_once('../server/config.php');
#header("Location: ../?env=1");
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   if (isset($_POST['comentario']) && isset($_POST['token'])) {
    if (!empty($_POST['comentario']) && !empty($_POST['token'])) {
        $myID = $_SESSION['session_id'];
        $token = $_POST['token'];
        $urlToken = urlencode($token);
        $idPost = desencriptar($token, $clave);
        $parametro = $_POST['comentario'];

        $stmt = $conn->prepare(
            "SELECT * FROM publicaciones WHERE id = :id"
          );

          $stmt->bindValue(":id", $idPost, PDO::PARAM_INT);
          $stmt->execute();
          $dataP = $stmt->fetch();

        if ($dataP) {
            $stmt = $conn->prepare("INSERT INTO comentarios (`user_id`, `post_id`, `contenido`) VALUES (:userid, :postid, :comentario)");
            $stmt->bindParam(':userid', $myID, PDO::PARAM_INT);
            $stmt->bindParam(':postid', $idPost, PDO::PARAM_INT);
            $stmt->bindParam(':comentario', $parametro, PDO::PARAM_STR);
            $stmt->execute();


            header("Location: ../?p=$urlToken");
            #exit();
        }
    }
   }
}
?>