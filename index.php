<?php
/*
* Llamamos la estrucutura de la web
*/
require_once("global.php");
require_once('templates/head.php');
if(isset($logeado) && $logeado){
  require_once('templates/navbar.php');
  require_once('templates/Body-Index.php');
} else {
  require_once('templates/Body-Login.php');
}
require_once('templates/footer.php');
?>