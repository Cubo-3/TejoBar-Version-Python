<?php
$host = "localhost";
$user = "root";       // Usuario de MySQL (por defecto)
$pass = "";           // Sin contraseña (por defecto)
$db   = "tejobar";    // Base de datos

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
?>
