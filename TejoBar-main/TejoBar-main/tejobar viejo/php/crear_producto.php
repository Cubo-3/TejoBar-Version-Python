<?php
include "conexion.php";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $fecha = $_POST['fecha'];

    $sql = "INSERT INTO producto (nombre, precio, stock, fechaVencimiento) 
            VALUES ('$nombre', '$precio', '$stock', '$fecha')";
    
    if ($conn->query($sql) === TRUE) {
        echo "✅ Producto agregado con éxito.";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
<a href="leer_producto.php">Ver productos</a>
