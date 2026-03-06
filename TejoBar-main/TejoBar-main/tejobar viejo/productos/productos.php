<?php
session_start();
include "../php/conexion.php"; // conexión a la BD

// Consultar productos
$sql = "SELECT * FROM producto"; 
$result = $conn->query($sql);

// Verificar error en la consulta
if (!$result) {
    die("Error en la consulta: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Productos - TejoBar</title>
  <link rel="stylesheet" href="productos.css" />
</head>
<body>
  <!-- HEADER -->
  <header>
    <a href="../index.html" class="logo">TejoBar</a>
    <nav>
      <ul class="menu-top">
        <li><a href="../index.html">🏠Inicio</a></li>
        <li><a href="../torneos/torneos.php">🥇Torneos</a></li>
        <li><a href="productos.php">🍻Comida y Bebida</a></li>
        <?php if (isset($_SESSION['idPersona'])): ?>
          <li><a href="../php/logout.php">🚪Cerrar Sesión</a></li>
        <?php else: ?>
          <li><a href="../sesion/login.php">🔓Iniciar Sesión</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <!-- PRODUCTOS -->
  <section class="section">
    <h2>Comida y Bebida</h2>
    <div class="grid">
      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <a href="detalles-producto.php?id=<?= $row['idProducto'] ?>" class="card">
            <div class="card-image" style="background-image: url('../img/productos/<?= $row['urlImg'] ?>');"></div>
            <div class="card-content">
              <h3><?= htmlspecialchars($row['nombre']) ?></h3>
              <p>$<?= number_format($row['precio'], 0, ',', '.') ?> - </p>
            </div>
          </a>
        <?php endwhile; ?>
      <?php else: ?>
        <p>No hay productos disponibles.</p>
      <?php endif; ?>
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
<?php $conn->close(); ?>
