

<?php
require_once "../server/config.php";

$page = $_GET["page"]; // Obtener el número de página de la solicitud AJAX

$limit = 10; // Número de publicaciones por página
$offset = ($page - 1) * $limit;

$stmt = $conn->prepare(
    "SELECT * FROM publicaciones ORDER BY fecha DESC LIMIT :limit OFFSET :offset"
);
$stmt->bindValue(":limit", $limit, PDO::PARAM_INT);
$stmt->bindValue(":offset", $offset, PDO::PARAM_INT);
$stmt->execute();

while ($dataP = $stmt->fetch()) {
    if ($dataP) {
        /**
         * 
         * Datos del post
         * 
        */
        $ownerID = $dataP["user_id"];
        $postID = $dataP["id"];
        $timestamp = strtotime($dataP['fecha']);

        $contenido = generarHTMLPost($conn, $postID);

        #Datos del usuario
        $row = give_datauser($ownerID, $conn);
        $ownerName = $row["username"];
        $avatarURL = $row["avatar"];

        //Creamos token
        $tokenPost = encriptar($postID, $clave);

?>
<div class="card_publi">
    <div class="card_publi_head">
        <div style="background-image:url(<?= $avatarURL ?>);" class="card_avatar"></div>
        <div class="box_cont_nombre"><?= $ownerName ?>
        <?=badges_user($conn,$ownerID);?>
        </div>
        <div class="box_cont_fecha"><?=getRelativeTime($timestamp)?></div>
    </div>

    <?=
    /**
     * Cargamos contenido de la publicacion
     */
    $contenido
    ?>

    <div class="card_contenido_comentar">
        <input type="hidden" name="postToken" value="<?=$tokenPost?>">
        <i class="bi bi-chat-square"></i> <button tokenpost="<?=$tokenPost?>" style="background: transparent;border: transparent;cursor: pointer;padding: 0;outline: none;" class="card_text_comentar" id="comentar">Comentar</button>
        <?php if(isset($_SESSION['mod']) && $_SESSION['mod']){echo "<span class=\"delete-p\"><a href=\"app/sys-delete-post.php?d=$tokenPost\">Eliminar post</a></span>";}?>
    </div>
</div>
<?php 
    } 
}
?>
