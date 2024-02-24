

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
        /*
         * Datos del post, desencriptamos
         */

        $contenido = htmlspecialchars($dataP["contenido"]);
        $contenido = nl2br($contenido);

        $ownerID = $dataP["user_id"];
        $postID = $dataP["id"];

        // Buscar los hashtags en el contenido del mensaje
        preg_match_all("/#\w+/", $contenido, $hashtags);
        $hashtags = $hashtags[0]; // Array con los hashtags encontrados

        $stmtUser = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmtUser->execute([
            ":id" => $ownerID,
        ]);
        $row = $stmtUser->fetch();
        $ownerName = $row["username"];
        $avatarURL = $row["avatar"];
        
        #Si es mod
        $stE = $conn->prepare("SELECT * FROM equipo WHERE user_id = :id");
        $stE->execute([':id' => $ownerID]);
        $dataEq = $stE->fetch(PDO::FETCH_ASSOC);

        #Si es verificado
        $stV = $conn->prepare("SELECT * FROM verificados WHERE user_id = :id");
        $stV->execute([':id' => $ownerID]);
        $dataVe = $stV->fetch(PDO::FETCH_ASSOC);

        // Reemplazar los hashtags con enlaces
        foreach ($hashtags as $hashtag) {
            $enlaceHashtag =
                '<a href="' .
                $hashtag .
                '" class="hashtag" style="text-decoration: none;">' .
                $hashtag .
                "</a>";
            $contenido = str_replace($hashtag, $enlaceHashtag, $contenido);
        }

        // Obtener la imagen asociada al post, si existe
        $stmtImage = $conn->prepare("SELECT imagen FROM post_images WHERE post_id = :post_id");
        $stmtImage->execute([
            ":post_id" => $postID,
        ]);
        $imageRow = $stmtImage->fetch();
        $imgPost = $imageRow ? $imageRow["imagen"] : '';

?>
<div class="card_publi">
    <div class="card_publi_head f-inline">
        <div style="background-image:url(<?= $avatarURL ?>);" class="card_avatar"></div>
        <p><?= $ownerName ?>
        <?php if($dataVe){echo '<i class="bi bi-patch-check-fill" style="color: var(--color-verificado);"></i> ';}?>
        <?php if($dataEq){echo '<div class="badge_mod" title="Moderador"></div>';}?>
        </p>
    </div>
    
    <div class="card_publi_contenido">
        <p class="p_cont"><?= formatearTexto($contenido) ?></p>
        <button class="ver-mas">Ver más</button>
    </div>
    
    <?php 
    if (substr($imgPost, -4) === ".mp4") { //card_publi_contenido_video?>
        <video class="card_publi_contenido_video video-js vjs-theme-city" controls preload="auto" data-setup="{}" width="640" height="264">
            <source src="<?= $imgPost ?>" type="video/mp4">
        </video>
        
        
    <?php } else if (!empty($imgPost)) { ?>
        <img src="<?= $imgPost ?>" class="card_publi_contenido_imagen">
    <?php } ?>
    <div class="card_contenido_comentar">
        <i class="bi bi-chat-square"></i> <span class="card_text_comentar">Comentar</span>
        <?php if(isset($_SESSION['mod']) && $_SESSION['mod']){echo "<span class=\"delete-p\"><a href=\"app/sys-delete-post.php?d=$postID\">Eliminar post</a></span>";}?>
    </div>
</div>
<?php 
    } 
}
?>
