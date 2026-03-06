<?php
include "conexion.php";

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $res = $conn->query("SELECT * FROM producto WHERE idProducto=$id");
    $row = $res->fetch_assoc();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['idProducto'];
    $nombre = $_POST['nombre'];
    $precio = $_POST['precio'];
    $stock = $_POST['stock'];
    $fecha = $_POST['fecha'];

    $sql = "UPDATE producto SET nombre='$nombre', precio='$precio', stock='$stock', fechaVencimiento='$fecha' WHERE idProducto=$id";
    if ($conn->query($sql) === TRUE) {
        echo "✅ Producto actualizado.";
    } else {
        echo "❌ Error: " . $conn->error;
    }
}
?>

<form method="post">
    <input type="hidden" name="idProducto" value="<?= $row['idProducto'] ?>">
    Nombre: <input type="text" name="nombre" value="<?= $row['nombre'] ?>"><br>
    Precio: <input type="number" name="precio" value="<?= $row['precio'] ?>"><br>
    Stock: <input type="number" name="stock" value="<?= $row['stock'] ?>"><br>
    Fecha Vencimiento: <input type="date" name="fecha" value="<?= $row['fechaVencimiento'] ?>"><br>
    <input type="submit" value="Actualizar">
</form>
<a href="leer_producto.php">Volver</a>
