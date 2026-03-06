<?php
session_start();
include "../php/conexion.php";

// Verificar ID válido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("⚠️ Producto no válido.");
}

$id = intval($_GET['id']);
$sql = "SELECT * FROM producto WHERE idProducto = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("⚠️ Producto no encontrado.");
}

$producto = $result->fetch_assoc();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= htmlspecialchars($producto['nombre']) ?> - TejoBar</title>
  <link rel="stylesheet" href="productos.css" />
</head>
<body>
  <!-- HEADER -->
  <header>
    <a href="../index.html" class="logo">TejoBar</a>
    <nav>
      <ul class="menu-top">
        <li><a href="../index.html">🏠Inicio</a></li>
        <li><a href="../torneos/torneos.html">🥇Torneos</a></li>
        <li><a href="productos.php">🍻Comida y Bebida</a></li>
        <?php if (isset($_SESSION['idPersona'])): ?>
          <li><a href="../php/logout.php">🚪Cerrar Sesión</a></li>
        <?php else: ?>
          <li><a href="../sesion/login.php">🔓Iniciar Sesión</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <!-- DETALLE DEL PRODUCTO -->
  <section class="section">
    <h2>🍴 <?= htmlspecialchars($producto['nombre']) ?></h2>
    <div class="detalle-producto">
      <div class="detalle-imagen">
        <img src="../img/productos/<?= htmlspecialchars($producto['urlImg']) ?>" 
             alt="<?= htmlspecialchars($producto['nombre']) ?>">
      </div>
      <div class="detalle-info">
        <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
        <p class="detalle-precio">
          💲 <?= number_format($producto['precio'], 0, ',', '.') ?>
        </p>
        <p><strong>Stock disponible:</strong> <?= $producto['stock'] ?></p>

        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" min="1" max="<?= $producto['stock'] ?>" value="1" />
        <br>

        <?php if (isset($_SESSION['idPersona'])): ?>
          <a href="comprar.php?id=<?= $producto['idProducto'] ?>" class="btn-comprar">🛒 Apartar ahora</a>
        <?php else: ?>
          <a href="../sesion/login.php" class="btn-comprar">🔓 Inicia sesión para apartar</a>
        <?php endif; ?>
      </div>
    </div>
  </section>

  <!-- FOOTER -->
  <footer>
    <p>&copy; 2025 TejoBar. Todos los derechos reservados.</p>
  </footer>

  <button id="toggle-btn">🌞 Modo Claro</button>
  <script src="modo-claro.js"></script>
</body>
</html>
