<?php
include "conexion.php";

$sql = "SELECT * FROM producto";
$result = $conn->query($sql);

echo "<h1>Lista de productos</h1>";
echo "<a href='index.html'>Volver</a><br><br>";

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
              <th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Vencimiento</th><th>Acciones</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row['idProducto']."</td>
                <td>".$row['nombre']."</td>
                <td>".$row['precio']."</td>
                <td>".$row['stock']."</td>
                <td>".$row['fechaVencimiento']."</td>
                <td>
                  <a href='actualizar_producto.php?id=".$row['idProducto']."'>Editar</a> | 
                  <a href='eliminar_producto.php?id=".$row['idProducto']."'>Eliminar</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No hay productos.";
}
?>
<?php
include "conexion.php";

$sql = "SELECT * FROM producto";
$result = $conn->query($sql);

echo "<h1>Lista de productos</h1>";
echo "<a href='index.html'>Volver</a><br><br>";

if ($result->num_rows > 0) {
    echo "<table border='1'>
            <tr>
              <th>ID</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Vencimiento</th><th>Acciones</th>
            </tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr>
                <td>".$row['idProducto']."</td>
                <td>".$row['nombre']."</td>
                <td>".$row['precio']."</td>
                <td>".$row['stock']."</td>
                <td>".$row['fechaVencimiento']."</td>
                <td>
                  <a href='actualizar_producto.php?id=".$row['idProducto']."'>Editar</a> | 
                  <a href='eliminar_producto.php?id=".$row['idProducto']."'>Eliminar</a>
                </td>
              </tr>";
    }
    echo "</table>";
} else {
    echo "No hay productos.";
}
?>
