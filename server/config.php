<?php
/*
* Config web
*/
$nombreWeb = "Sociedad Libre";
$nombreWebPegado = "SociedadLibre";
/*
* Conexion a la base de datos
*/
$host = "localhost";
$username = "root";
$password = "";
$dbname = "daily";

//time zone en colombia
date_default_timezone_set('America/Bogota');

try {
  $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
  $options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
  ];
  $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
  echo 'Error de conexión: ' . $e->getMessage();
  exit;
}

session_start();

/*
* Datos geolocalizacion
*/
if (isset($_SERVER['HTTP_CF_CONNECTING_IP'])) {
  // La solicitud proviene de Cloudflare
  $ipActual = $_SERVER['HTTP_CF_CONNECTING_IP'];
} else {
  // La solicitud no proviene de Cloudflare
  $ipActual = $_SERVER['REMOTE_ADDR'];
}
/*
* Funciones
*/
function sanearUsuario($usuario) {
  // Expresión regular para encontrar caracteres no permitidos
  $patron = '/[^a-zA-Z0-9_.]/';

  // Saneamos el usuario eliminando los caracteres no permitidos
  $usuario_saneado = preg_replace($patron, '', $usuario);

  // Eliminamos los espacios
  $usuario_saneado = str_replace(' ', '', $usuario_saneado);

  return $usuario_saneado;
}
function sanearCorreo($correo) {
  // Expresión regular para encontrar caracteres no permitidos
  $patron = '/[^a-zA-Z0-9_.@]/';

  // Saneamos el correo eliminando los caracteres no permitidos
  $correo_saneado = preg_replace($patron, '', $correo);

  // Eliminamos los espacios
  $correo_saneado = str_replace(' ', '', $correo_saneado);

  return $correo_saneado;
}

// Clave secreta para el cifrado
$clave = "0#&vTXrutcDPjeLm9zK6g2)vRmb2QK5G";

// Función para encriptar los datos
function encriptar($datos, $clave) {
  $method = "AES-256-CBC";
  $ivLength = openssl_cipher_iv_length($method);
  $iv = openssl_random_pseudo_bytes($ivLength);
  $ciphertext = openssl_encrypt($datos, $method, $clave, OPENSSL_RAW_DATA, $iv);
  $encryptedData = $iv . $ciphertext;
  return base64_encode($encryptedData);
}

// Función para desencriptar los datos
function desencriptar($datos, $clave) {
    $method = "AES-256-CBC";
    $ivLength = openssl_cipher_iv_length($method);
    $encryptedData = base64_decode($datos);
    $iv = substr($encryptedData, 0, $ivLength);
    $ciphertext = substr($encryptedData, $ivLength);
    $decryptedData = openssl_decrypt($ciphertext, $method, $clave, OPENSSL_RAW_DATA, $iv);
    return $decryptedData;
}

function generarClave($longitud = 32) {
  $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%^&*()';
  $clave = '';
  $numCaracteres = strlen($caracteres);

  for ($i = 0; $i < $longitud; $i++) {
      $indice = random_int(0, $numCaracteres - 1);
      $clave .= $caracteres[$indice];
  }

  return $clave;
}

function limitCadena($cadena) {
  // Patrón de caracteres permitidos
  $patron = '/^[a-zA-Z0-9\/\.\:#-]+$/';

  // Verificar si la cadena coincide con el patrón
  if (preg_match($patron, $cadena)) {
      // No contiene caracteres especiales
      return false;
  } else {
      // Contiene caracteres especiales
      return true;
  }
}

function formatearTexto($texto) {
  // Convertir **text** en negrita
  $texto = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $texto);

  // Convertir *text* en cursiva
  $texto = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $texto);

  // Buscar texto en cursiva dentro de negrita y formatearlo
  $texto = preg_replace_callback('/<strong>(.*?)<\/strong>/', function($matches) {
    $texto = $matches[1];
    $texto = formatearTexto($texto); // Llamada recursiva para formatear el texto en negrita
    return "<strong>$texto</strong>";
  }, $texto);

  return $texto;
}

function give_datauser($iduser, $conn) {
  $stmt = $conn->prepare("SELECT * FROM usuarios WHERE id = :id");
  $stmt->execute([
      ':id' => $iduser
  ]);
  $dataF = $stmt->fetch(PDO::FETCH_ASSOC);

  return $dataF;
}

function getRelativeTime($timestamp) {
  $currentTime = time();
  $timeDiff = $currentTime - $timestamp;

  if ($timeDiff < 60) {
      return "hace unos segundos";
  } elseif ($timeDiff < 3600) {
      $minutes = floor($timeDiff / 60);
      return "hace " . $minutes . " minuto(s)";
  } elseif ($timeDiff < 86400) {
      $hours = floor($timeDiff / 3600);
      return "hace " . $hours . " hora(s)";
  } elseif ($timeDiff < 604800) {
      $days = floor($timeDiff / 86400);
      return "hace " . $days . " día(s)";
  } elseif ($timeDiff < 2592000) {
      $weeks = floor($timeDiff / 604800);
      return "hace " . $weeks . " semana(s)";
  } elseif ($timeDiff < 31536000) {
      $months = floor($timeDiff / 2592000);
      return "hace " . $months . " mes(es)";
  } else {
      $years = floor($timeDiff / 31536000);
      return "hace " . $years . " año(s)";
  }
}

function procesarContenido($contenido) {
  // Convertir caracteres especiales a entidades HTML
  $contenido = htmlspecialchars($contenido);

  // Agregar saltos de línea HTML
  $contenido = nl2br($contenido);

  // Buscar los hashtags en el contenido del mensaje
  preg_match_all("/#\w+/", $contenido, $hashtags);
  $hashtags = $hashtags[0]; // Array con los hashtags encontrados

  // Reemplazar los hashtags con enlaces
  foreach ($hashtags as $hashtag) {
      $enlaceHashtag =
          '<a href="../' .
          $hashtag .
          '" class="hashtag" style="text-decoration: none;">' .
          $hashtag .
          "</a>";
      $contenido = str_replace($hashtag, $enlaceHashtag, $contenido);
  }

  // Convertir **text** en negrita
  $contenido = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $contenido);

  // Convertir *text* en cursiva
  $contenido = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $contenido);

  // Buscar texto en negrita dentro de cursiva y formatearlo
  $contenido = preg_replace_callback('/<em>(.*?)<\/em>/', function($matches) {
      $texto = $matches[1];
      $texto = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $texto);
      return "<em>$texto</em>";
  }, $contenido);

  // Buscar texto en cursiva dentro de negrita y formatearlo
  $contenido = preg_replace_callback('/<strong>(.*?)<\/strong>/', function($matches) {
      $texto = $matches[1];
      $texto = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $texto);
      return "<strong>$texto</strong>";
  }, $contenido);

  return $contenido;
}

function badge_moderador($conn, $userid) {
  // Realizar la consulta SQL para verificar si el usuario es un moderador
  $stE = $conn->prepare("SELECT * FROM equipo WHERE user_id = :id");
  $stE->execute([':id' => $userid]);
  $dataEq = $stE->fetch(PDO::FETCH_ASSOC);

  // Verificar si el usuario es un moderador y devolver el badge correspondiente
  if ($dataEq) {
      $badgeMod = '<div class="badge_mod" title="Moderador"></div>';
  } else {
      $badgeMod = ''; // No es moderador, el badge está vacío
  }

  return $badgeMod;
}
function badge_verificado($conn, $userid) {
  #Si es verificado
  $stV = $conn->prepare("SELECT * FROM verificados WHERE user_id = :id");
  $stV->execute([':id' => $userid]);
  $dataVe = $stV->fetch(PDO::FETCH_ASSOC);

  // Verificar si el usuario es un moderador y devolver el badge correspondiente
  if ($dataVe) {
      $badgeVerificado = '<i class="bi bi-patch-check-fill" style="color: var(--color-verificado);"></i>';
  } else {
      $badgeVerificado = ''; // No es moderador, el badge está vacío
  }

  return $badgeVerificado;
}

function badges_user($conn, $userId) {
  $badgeUsuario = '';

  // Verificar si el usuario está verificado
  $stV = $conn->prepare("SELECT * FROM verificados WHERE user_id = :id");
  $stV->execute([':id' => $userId]);
  $dataVe = $stV->fetch(PDO::FETCH_ASSOC);

  if ($dataVe) {
      $badgeUsuario .= '<i class="bi bi-patch-check-fill" style="color: var(--color-verificado);"></i>';
  }

  // Verificar si el usuario es un moderador
  $stE = $conn->prepare("SELECT * FROM equipo WHERE user_id = :id");
  $stE->execute([':id' => $userId]);
  $dataEq = $stE->fetch(PDO::FETCH_ASSOC);

  if ($dataEq) {
      $badgeUsuario .= '<div class="badge_mod" title="Moderador"></div>';
  }

  return $badgeUsuario;
}
function postMultimedia($conn, $postID) {
  // Obtener la imagen asociada al post, si existe
  $stmtImage = $conn->prepare("SELECT imagen FROM post_images WHERE post_id = :post_id");
  $stmtImage->execute([
      ":post_id" => $postID,
  ]);
  $imageRow = $stmtImage->fetch();
  $imgPost = $imageRow ? $imageRow["imagen"] : '';

  $imagenHTML = '';

  if (substr($imgPost, -4) === ".mp4") {
      // Mostrar video si la imagen es un archivo de video
      $imagenHTML = '<video class="card_publi_contenido_video video-js vjs-theme-city" controls preload="auto" data-setup="{}" width="640" height="264">';
      $imagenHTML .= '<source src="' . $imgPost . '" type="video/mp4">';
      $imagenHTML .= '</video>';
  } else if (!empty($imgPost)) {
      // Mostrar imagen si la imagen no es un archivo de video
      $imagenHTML = '<img src="' . $imgPost . '" class="card_publi_contenido_imagen">';
  }

  return $imagenHTML;
}
function generarHTMLPost($conn, $postID) {
  // Obtener los datos de la tabla "publicaciones"
  $stmtPublicaciones = $conn->prepare("SELECT contenido FROM publicaciones WHERE id = :post_id");
  $stmtPublicaciones->execute([
      ":post_id" => $postID,
  ]);
  $dataP = $stmtPublicaciones->fetch();

  if ($dataP) {
      // Procesar el contenido del post
      $contenido = htmlspecialchars($dataP["contenido"]);
      $contenido = nl2br($contenido);

      // Buscar los hashtags en el contenido del mensaje
      preg_match_all("/#\w+/", $contenido, $hashtags);
      $hashtags = $hashtags[0]; // Array con los hashtags encontrados

      // Reemplazar los hashtags con enlaces
      foreach ($hashtags as $hashtag) {
          $enlaceHashtag =
              '<a href="../' .
              $hashtag .
              '" class="hashtag" style="text-decoration: none;">' .
              $hashtag .
              "</a>";
          $contenido = str_replace($hashtag, $enlaceHashtag, $contenido);
      }

      // Convertir **text** en negrita
      $contenido = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $contenido);

      // Convertir *text* en cursiva
      $contenido = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $contenido);

      // Buscar texto en negrita dentro de cursiva y formatearlo
      $contenido = preg_replace_callback('/<em>(.*?)<\/em>/', function($matches) {
          $texto = $matches[1];
          $texto = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $texto);
          return "<em>$texto</em>";
      }, $contenido);

      // Buscar texto en cursiva dentro de negrita y formatearlo
      $contenido = preg_replace_callback('/<strong>(.*?)<\/strong>/', function($matches) {
          $texto = $matches[1];
          $texto = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $texto);
          return "<strong>$texto</strong>";
      }, $contenido);

      // Obtener la imagen asociada al post, si existe
      $stmtImage = $conn->prepare("SELECT imagen FROM post_images WHERE post_id = :post_id");
      $stmtImage->execute([
          ":post_id" => $postID,
      ]);
      $imageRow = $stmtImage->fetch();
      $imgPost = $imageRow ? $imageRow["imagen"] : '';

      // Generar el HTML completo
      $html = '<div class="card_publi_contenido">';
      $html .= '<p class="p_cont">' . $contenido . '</p>';
      $html .= '<button class="ver-mas">Ver más</button>';

      if (substr($imgPost, -4) === ".mp4") {
          // Mostrar video si la imagen es un archivo de video
          $html .= '<video class="card_publi_contenido_video video-js vjs-theme-city" controls preload="auto" data-setup="{}" width="640" height="264">';
          $html .= '<source src="' . $imgPost . '" type="video/mp4">';
          $html .= '</video>';
      } else if (!empty($imgPost)) {
          // Mostrar imagen si la imagen no es un archivo de video
          $html .= '<img src="' . $imgPost . '" class="card_publi_contenido_imagen">';
      }

      $html .= '</div>';

      return $html;
  } else {
      return "No se encontraron datos para el post con ID: " . $postID;
  }
}

?>