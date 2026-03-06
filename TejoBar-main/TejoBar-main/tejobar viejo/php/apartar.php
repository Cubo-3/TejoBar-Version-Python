<?php
session_start();
include "../php/conexion.php";

// Verificar login
if (!isset($_SESSION['idPersona'])) {
    header("Location: ../sesion/login.php");
    exit();
}

$idPersona = $_SESSION['idPersona'];
$idProducto = intval($_POST['idProducto']);
$cantidad = intval($_POST['cantidad']);

// Verificar que el producto exista y tenga stock suficiente
$sql = "SELECT stock FROM producto WHERE idProducto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $idProducto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("⚠️ Producto no encontrado.");
}

$producto = $result->fetch_assoc();

// Registrar en la tabla apartados (sin descontar stock todavía)
$sql = "INSERT INTO apartados (idPersona, idProducto, cantidad) VALUES (?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("iii", $idPersona, $idProducto, $cantidad);
$stmt->execute();

// Redirigir al dashboard o a una página de confirmación
header("Location: dashboard.php?msg=Producto apartado con éxito");
exit();
?>
