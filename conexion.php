<?php

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "biblioteca";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}


$host = "localhost";
$usuario = "root";
$contrasenia = "";
$base_de_datos = "domingosavio";

return $conn; 