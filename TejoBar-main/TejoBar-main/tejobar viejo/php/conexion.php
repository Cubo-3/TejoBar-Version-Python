<?php
$host = "localhost";
$user = "hola";       // Usuario de MySQL
$pass = "";   // ← escribe tu clave aquí
$db   = "tejobar";    // Base de datos

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die("❌ Error de conexión: " . $conn->connect_error);
}
?>
