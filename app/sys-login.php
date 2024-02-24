<?php
require_once('../server/config.php');

#Comenzamos con las consultas
if (isset($_POST['username']) && isset($_POST['password'])) {
    /*
    * Ya los datos del formulario son validos, procedemos
    */
    $user = sanearUsuario($_POST['username']);
    $pass = $_POST['password'];

    if (!empty($user) && !empty($pass)) {
        /*
        * Procede ya que ningun dato esta vacio, realizamos consulta con los datos
        */
        $stmt = $conn->prepare("SELECT * FROM usuarios WHERE username = :usuario");
        $stmt->execute([
            ':usuario' => $user
        ]);
        $dataU = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($dataU) {
            /*
            * El usuario existe por lo tanto, procedemos a comparar contraseñas
            */
           $nameU = $dataU['username'];
           $idU = $dataU['id'];
           $passU = $dataU['password'];
           if ($pass == $passU) {
            /*
            * Los datos ingresados son validos, por lo tanto iniciamos sesion
            */
            $_SESSION['logeado'] = true;
            $_SESSION['usuario'] = $user;
            $_SESSION['session_id'] = $idU;

            header("Location: ../");
            exit();
           } else {
            header("Location: ../?env=4");
            exit();
           }
        } else {
            header("Location: ../?env=3");
            exit();
        }
    } else {
        header("Location: ../?env=2");
        exit();
    }
} else {
    header("Location: ../?env=1");
    exit();
}
?>