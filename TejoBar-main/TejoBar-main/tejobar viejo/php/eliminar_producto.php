<?php
include "conexion.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "DELETE FROM producto WHERE idProducto=$id";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Producto eliminado.";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>
<a href="leer_producto.php">Volver</a>
