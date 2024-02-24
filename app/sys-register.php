<?php
#Archivo conexion
require_once '../server/config.php';

/*
* Verificar que formulario, que sea correctamente enviado
*/
if (isset($_POST['username']) && isset($_POST['correo']) && isset($_POST['password']) && isset($_POST['genero'])) {
    $user = $_POST['username'];
    $userSano = sanearUsuario($user);
    $correo = $_POST['correo'];
    $correoSano = sanearCorreo($correo);
    $password = $_POST['password'];
    $genero = $_POST['genero'];

    /*
    * Este condicional es para verificar que no este ningun dato vacio
    */
    if (!empty($user) && !empty($correo) && !empty($password) && !empty($genero)) {
        /*
        * Vamos a verificar que $correo sea verdaderamente ese formato
        */
        if (filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            /*
            * Hacemos consulta de correo, para verificar si existe
            */
            $consulta = $conn->prepare("SELECT email FROM usuarios WHERE email = :email");
            $consulta->execute([
                ':email' => $correo
            ]);
            $emailExiste = $consulta->fetch(PDO::FETCH_ASSOC);

            #Correo no existe y continuamos
            if (!$emailExiste) {
                /*
                * Hacemos consulta de username, para verificar si existe
                */
                $consulta = $conn->prepare("SELECT username FROM usuarios WHERE username = :usuario");
                $consulta->execute([
                    ':usuario' => $user
                ]);
                $userExiste = $consulta->fetch(PDO::FETCH_ASSOC);

                #Usuario no existe y continuamos
                if (!$userExiste) {
                    /*
                    * Que el usuario sea mayor a 3 letras
                    */
                    if (strlen($user) > 3) {
                        /*
                        * Que la contraseña sea mayor a 8 digitos
                        */
                        if (strlen($password) > 7) {
                            /*
                            * Verificamos que el genero sea el que esta alli
                            */
                            if ((is_numeric($genero)) && ($genero > 0 && $genero < 3)) {

                                function selectAvatar($genero) {
                                    if ($genero == 1) {
                                        $ruta = '../assets/images/avatar/male/';
                                    } elseif ($genero == 2) {
                                        $ruta = '../assets/images/avatar/female/';
                                    } else {
                                        return '../assets/images/avatar/noavatar.png'; // Si el género es 3, se asigna el avatar "noavatar.png"
                                    }
                                    
                                    $cantidadAvatares = 10; // Cantidad de avatares disponibles en las carpetas male y female
                                    
                                    $avatarIndex = mt_rand(1, $cantidadAvatares); // Generar un índice aleatorio
                                    
                                    $avatar = $ruta . $avatarIndex . '.png'; // Construir la ruta completa del avatar
                                    
                                    if (file_exists($avatar)) {
                                        return $avatar;
                                    }
                                    
                                    return './assets/images/avatar/noavatar.png'; // Si el archivo no existe, se asigna el avatar "noavatar.png"
                                }

                                #Seleccionamos avatar
                                $avatar = selectAvatar($genero);
                                $avatar = ltrim($avatar, '.');
                                $avatar = "."."$avatar";
                                /*
                                * Verificamos que el usuario tenga caracteres permitidos
                                */
                                if ($userSano != "") {
                                    /*
                                    * Verificamos que el correo tenga caracteres correctos
                                    */
                                    if ($correoSano != "") {
                                        
                                       /*
                                       * Creamos la cuenta del usuario
                                       */
                                       $stmt = $conn->prepare("INSERT INTO usuarios (username,`password`,email,genero,avatar,pais_registro,ip_registro) VALUES (:username,:pass,:email,:genero,:avatar,:pais,:ip)");
                                       $stmt->execute([
                                        ':username' => $userSano,
                                        ':pass' => $password,
                                        ':email' => $correoSano,
                                        ':genero' => $genero,
                                        ':avatar' => $avatar,
                                        ':pais' => "No disponible",
                                        ':ip' => $ipActual
                                        ]);

                                        /*
                                        * Iniciamos sesion automaticamente si se envia todo correcta al a db
                                        */
                                        if ($stmt->rowCount() > 0) {
                                            $_SESSION['logeado'] = true;
                                            $_SESSION['usuario'] = $userSano;
                                            
                                            header("Location: ../");
                                            exit();
                                        }
                                        
                                    } else {
                                        header("Location: ../r?env=10");
                                        exit();
                                    }
                                } else {
                                    header("Location: ../r?env=9");
                                    exit();
                                }
                            } else {
                                header("Location: ../r?env=8");
                                exit();
                            }
                        } else {
                            header("Location: ../r?env=7");
                            exit();
                        }
                    } else {
                        header("Location: ../r?env=6");
                        exit();
                    }
                } else {
                    header("Location: ../r?env=5");
                    exit();
                }
            } else {
                header("Location: ../r?env=4");
                exit();
            }
        } else {
            header("Location: ../r?env=3");
            exit();
        }
    } else {
        header("Location: ../r?env=2");
        exit();
    }
} else {
    header("Location: ../r?env=1");
    exit();
}

header("Location: ../r");
exit();
?>