<?php
require_once('server/config.php');
if (isset($_SESSION['logeado'])) {
    /*
    * Ya que detectamos que hay una sesion abierta, procedemos a hacer consulta de los datos
    */

    #Creamos variables, para facilitar
    $logeado = $_SESSION['logeado'];
    $myNameSession = $_SESSION['usuario'];
    
    #Consulta al a tabla de usuarios, con el usuario logeado
    $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :user LIMIT 1");
    $stmt->execute([
        ':user' => $myNameSession
    ]);
    $myData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    #Si los datos en sesion, existe procedera a sacarlos
    if (isset($myData) && $myData) {
        $myID = $myData['id'];
        $myNombre = $myData['username'];
        $myEmail = $myData['email'];
        $myAvatar = $myData['avatar'];

        $stmt2 = $conn->prepare("SELECT * FROM equipo WHERE user_id = :id");
        $stmt2->execute([':id' => $myID]);
        $dataE = $stmt2->fetch(PDO::FETCH_ASSOC);

        if ($dataE) {
            $_SESSION['mod'] = true;
            $soymod = $_SESSION['mod'];
        }
    }
}

?>