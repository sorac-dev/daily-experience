<?php
require_once '../server/config.php';

// Obtener datos del formulario
$username = $_POST['username'];
$password = md5(sha1(md5($_POST['password'])));


if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    if (isset($username) && isset($password)) {
     if (!empty($username) || !empty($password)) {
         // Consultar en la base de datos
         $sql = "SELECT * FROM usuarios WHERE username = ? AND password = ?";
         $stmt = $conn->prepare($sql);
         $stmt->execute([$username, $password]);
         $user = $stmt->fetch(PDO::FETCH_ASSOC);
 
         if ($user) {
 
             $userID = $user['ID'];
             // Consultar en la base de datos
             $sql = "SELECT * FROM equipo WHERE user_id = :user_id";
             $stmt = $conn->prepare($sql);
             $stmt->execute([
                 ':user_id' => $userID
             ]);
             $dataRango = $stmt->fetch(PDO::FETCH_ASSOC);
 
            if ($dataRango) {
                if ($password == $user['password']) {
                    // Iniciar sesión
                    $_SESSION['session_id'] = $user['id'];
                    $_SESSION['session_name'] = $user['username'];
                    $_SESSION['logeado'] = true;
                
                    // Generar un token único
                    $token = bin2hex(random_bytes(16));
                
                    // Establecer la fecha de expiración a 3 meses en el futuro
                    $expiry = date('Y-m-d H:i:s', strtotime('+3 months'));
                
                    // Almacenar el token en la tabla session_panel
                    $sql = "INSERT INTO session_panel (user_id, token, expiry) VALUES (?, ?, ?)";
                    $stmt = $conn->prepare($sql);
                    $stmt->execute([$user['ID'], $token, $expiry]);
                
                    // Enviar el token al cliente y almacenarlo en una cookie
                    setcookie('session_token', $token, strtotime($expiry), '/');
                    
                
                    header('Location: ../index.php');
                    exit();
                    
                } else {
                     // No coincide password
                     header('Location: ../login.php?error=4');
                     exit();
                 }
            } else {
             // Usuario no es moderador
             header('Location: ../login.php?error=3');
             exit();
            }
         } else {
             // Usuario no encontrado, redirigir al formulario de login
             header('Location: ../login.php?error=1');
             exit();
         }
     } else {
         // Usuario no encontrado, redirigir al formulario de login
         header('Location: ../login.php?error=2');
         exit();
     }
    }
} 
// Si el usuario intenta acceder a este archivo directamente sin haber enviado el formulario,
// redirigirlo al formulario de login
header('Location: ../login.php');
exit;
?>