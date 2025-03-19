<?php

$conex = mysqli_connect("localhost","root","","amigos_de_david");

if (!$conex) {
    die("Error de conexión: " . mysqli_connect_error());
}

?>