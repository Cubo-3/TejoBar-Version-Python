<?php
include "../php/conexion.php"; // Conexión a la BD

// Consulta de torneos
$sql = "SELECT * FROM torneo ORDER BY fecha ASC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Torneos</title>
  <link rel="stylesheet" href="estilos.css">
</head>
<body>
  <header>
    <a href="../index.html" class="logo">TejoBar</a>
    <nav>
      <ul class="menu-top">
        <li><a href="../index.html">🏠Inicio</a></li>
        <li><a href="torneos.php">🥇Torneos</a></li>
        <li><a href="../productos/productos.php">🍻Comida y Bebida</a></li>
        <li><a href="../sesion/login.php">🧾Inicia Sesión</a></li>
      </ul>
    </nav>
  </header>

  <div class="container">
    <!-- Carteles -->
    <div class="carteles">

    </div>

    <!-- Tabla -->
    <div class="tabla">
      <div class="encabezado">
        <div>Número de Equipos</div>
        <div>Hora y Fecha</div>
        <div>Cancha</div>
      </div>

      <?php if ($result->num_rows > 0): ?>
        <?php while($row = $result->fetch_assoc()): ?>
          <div class="fila">
            <div>equipo <?= $row['equipo1'] ?> vs equipo <?= $row['equipo2'] ?>      
            </div>
            <div>
              <?= date("d/m/Y", strtotime($row['fecha'])) ?><br>
              <?= date("h:i A", strtotime($row['fecha'])) ?>
            </div>
            <div><?= $row['cancha'] ?></div>
          </div>
        <?php endwhile; ?>
      <?php else: ?>
        <div class="fila">
          <div colspan="3">No hay torneos registrados</div>
        </div>
      <?php endif; ?>
    </div>

    <!-- Imágenes decorativas -->
    <img src="../img/imgizq.png" alt="Figura izquierda" class="decor decor-left-top">
    <img src="../img/imgizqa.png" alt="Figura izquierda abajo" class="decor decor-left-bottom">
    <img src="../img/imgder.png" alt="Figura derecha" class="decor decor-right-top">
    <img src="../img/imgdera.png" alt="Figura derecha abajo" class="decor decor-right-bottom">
  </div>

  <footer>
    <p>&copy; 2025 TejoBar. Todos los derechos reservados.</p>
  </footer>
</body>
</html>
