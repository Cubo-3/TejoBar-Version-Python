<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['rol']) || !isset($_SESSION['idPersona'])) {
    header("Location: ../sesion/login.php");
    exit();
}

$rol = strtoupper($_SESSION['rol']); // para evitar problemas de minúsculas
$idPersona = $_SESSION['idPersona'];

// --- CRUD SOLO PARA ADMIN ---
if ($rol == "ADMIN") {
    // Crear producto
    if (isset($_POST['crear'])) {
        $nombre = $_POST['nombre'];
        $stock = $_POST['stock'];
        $precio = $_POST['precio'];
        $fecha = $_POST['fechaVencimiento'];

        $urlImg = "";
        if (!empty($_FILES['urlImg']['name'])) {
            $urlImg = basename($_FILES['urlImg']['name']);
            $destino = "../uploads/" . $urlImg;
            move_uploaded_file($_FILES['urlImg']['tmp_name'], $destino);
        }

        $sqlInsert = "INSERT INTO producto (nombre, stock, precio, fechaVencimiento, urlImg) 
                      VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sqlInsert);
        $stmt->bind_param("sidss", $nombre, $stock, $precio, $fecha, $urlImg);
        $stmt->execute();
    }

    // Editar producto
    if (isset($_POST['editar'])) {
        $id = $_POST['idProducto'];
        $nombre = $_POST['nombre'];
        $stock = $_POST['stock'];
        $precio = $_POST['precio'];
        $fecha = $_POST['fechaVencimiento'];

        // mantener la imagen si no se sube una nueva
        $urlImg = $_POST['urlImg'];
        if (!empty($_FILES['urlImg']['name'])) {
            $urlImg = basename($_FILES['urlImg']['name']);
            $destino = "../uploads/" . $urlImg;
            move_uploaded_file($_FILES['urlImg']['tmp_name'], $destino);
        }

        $sqlUpdate = "UPDATE producto 
                      SET nombre=?, stock=?, precio=?, fechaVencimiento=?, urlImg=? 
                      WHERE idProducto=?";
        $stmt = $conn->prepare($sqlUpdate);
        $stmt->bind_param("sidssi", $nombre, $stock, $precio, $fecha, $urlImg, $id);
        $stmt->execute();
    }

    // Eliminar producto
    if (isset($_POST['eliminar'])) {
        $id = $_POST['idProducto'];
        $sqlDelete = "DELETE FROM producto WHERE idProducto=?";
        $stmt = $conn->prepare($sqlDelete);
        $stmt->bind_param("i", $id);
        $stmt->execute();
    }
}

// --- Consultar productos apartados del usuario ---
$sqlApartados = "SELECT a.idApartado, p.nombre, p.stock, p.fechaVencimiento, p.precio, 
                        a.cantidad, a.fechaApartado, a.estado
                 FROM apartados a
                 JOIN producto p ON a.idProducto = p.idProducto
                 WHERE a.idPersona = ? AND a.estado = 'pendiente'";

$stmt = $conn->prepare($sqlApartados);
if (!$stmt) {
    die("Error en la consulta SQL de apartados: " . $conn->error);
}
$stmt->bind_param("i", $idPersona);
$stmt->execute();
$apartados = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);

// --- Consultar todos los productos (solo para ADMIN) ---
$productos = [];
if ($rol == "ADMIN") {
    $sqlProductos = "SELECT * FROM producto";
    $result = $conn->query($sqlProductos);
    if ($result) {
        $productos = $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tejobar - Panel de Control</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles35.css">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="d-flex" id="wrapper">

  <!-- Sidebar -->
  <div class="bg-light border-right" id="sidebar-wrapper">
    <div class="sidebar-heading">Tejobar</div>
    <div class="list-group list-group-flush">
      <?php if ($rol === "CAPITAN"): ?>
        <a href="dashboard.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-users mr-2"></i>Tu equipo
        </a>
        <a href="productos.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-box mr-2"></i>Productos
        </a>
      <?php elseif ($rol === "JUGADOR"): ?>
        <a href="dashboard.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-users mr-2"></i>Tu equipo
        </a>
        <a href="productos.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-box mr-2"></i>Productos
        </a>
      <?php elseif ($rol === "ADMIN"): ?>
        <a href="equipos-registrados.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-users mr-2"></i>Equipos
        </a>
        <a href="productos.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-box mr-2"></i>Productos
        </a>
 
      <?php endif; ?>

      <a href="../index.php" class="list-group-item list-group-item-action bg-light">
        <i class="fas fa-fw fa-home mr-2"></i>Inicio
      </a>
    </div>
  </div>

  <!-- Contenido -->
  <div id="page-content-wrapper">
      <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
          <button class="btn btn-primary" id="menu-toggle">
              <i class="fas fa-bars"></i>
          </button>

          <div class="collapse navbar-collapse" id="navbarSupportedContent">
              <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                  <li class="nav-item dropdown">
                      <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                          <i class="fas fa-user-circle mr-1"></i>
                          <?php if ($rol == "ADMIN") echo "Administrador"; ?>
                          <?php if ($rol == "CAPITAN") echo "Capitán"; ?>
                          <?php if ($rol == "JUGADOR") echo "Jugador"; ?>
                      </a>
                      <div class="dropdown-menu dropdown-menu-right">
                          <a class="dropdown-item" href="#"><i class="fas fa-user mr-1"></i> Perfil</a>
                          <div class="dropdown-divider"></div>
                          <a class="dropdown-item" href="../php/logout.php">
                              <i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión
                          </a>
                      </div>
                  </li>
              </ul>
          </div>
      </nav>

      <div class="container-fluid">
          <h1 class="mt-4">Panel de Control Principal</h1>
          <p>El mejor servicio de Tejo al alcance de tu mano</p>
          <br><hr>

          <?php if ($rol == "ADMIN"): ?>
              <!-- ADMIN: CRUD -->
              <div class="card mb-4">
                <div class="card-header">Registrar nuevo producto</div>
                <div class="card-body">
                  <form method="post" enctype="multipart/form-data">
                    <div class="form-row">
                      <div class="col"><input type="text" name="nombre" class="form-control" placeholder="Nombre" required></div>
                      <div class="col"><input type="number" name="stock" class="form-control" placeholder="Stock" required></div>
                      <div class="col"><input type="number" step="0.01" name="precio" class="form-control" placeholder="Precio" required></div>
                      <div class="col"><input type="date" name="fechaVencimiento" class="form-control" required></div>
                      <div class="col"><input type="file" name="urlImg" class="form-control-file"></div>
                      <div class="col"><button type="submit" name="crear" class="btn btn-success">Agregar</button></div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="card shadow mb-4">
                  <div class="card-header py-3">
                      <h6 class="m-0 font-weight-bold text-primary">Lista de Productos</h6>
                  </div>
                  <div class="card-body">
                      <?php if (!empty($productos)): ?>
                          <table class="table table-bordered">
                              <thead>
                              <tr>
                                  <th>ID</th>
                                  <th>Nombre</th>
                                  <th>Stock</th>
                                  <th>Precio</th>
                                  <th>Fecha Vencimiento</th>
                                  <th>Imagen</th>
                                  <th>Acciones</th>
                              </tr>
                              </thead>
                              <tbody>
                              <?php foreach ($productos as $p): ?>
                                  <tr>
                                    <form method="post" enctype="multipart/form-data">
                                      <td><?= $p['idProducto'] ?><input type="hidden" name="idProducto" value="<?= $p['idProducto'] ?>"></td>
                                      <td><input type="text" name="nombre" value="<?= htmlspecialchars($p['nombre']) ?>" class="form-control"></td>
                                      <td><input type="number" name="stock" value="<?= $p['stock'] ?>" class="form-control"></td>
                                      <td><input type="number" step="0.01" name="precio" value="<?= $p['precio'] ?>" class="form-control"></td>
                                      <td><input type="date" name="fechaVencimiento" value="<?= $p['fechaVencimiento'] ?>" class="form-control"></td>
                                      <td>
                                        <?php if (!empty($p['urlImg'])): ?>
                                          <img src="../img/productos/<?= htmlspecialchars($p['urlImg']) ?>" width="50">
                                        <?php endif; ?>
                                        <input type="hidden" name="urlImgActual" value="<?= htmlspecialchars($p['urlImg']) ?>">
                                        <input type="file" name="urlImg" class="form-control-file mt-1">
                                      </td>
                                      <td>
                                        <button type="submit" name="editar" class="btn btn-warning btn-sm">Editar</button>
                                        <button type="submit" name="eliminar" class="btn btn-danger btn-sm" onclick="return confirm('¿Seguro de eliminar este producto?')">Eliminar</button>
                                      </td>
                                    </form>
                                  </tr>
                              <?php endforeach; ?>
                              </tbody>
                          </table>
                      <?php else: ?>
                          <div class="alert alert-warning">No hay productos registrados.</div>
                      <?php endif; ?>
                  </div>
              </div>
          <?php elseif ($rol == "CAPITAN"): ?>
              <!-- CAPITAN: Apartados -->
              <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Mis Apartados</h6></div>
                <div class="card-body">
                  <?php if (!empty($apartados)): ?>
                      <table class="table table-bordered">
                        <thead>
                          <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th><th>Fecha</th></tr>
                        </thead>
                        <tbody>
                        <?php 
                        $totalGeneral=0;
                        foreach ($apartados as $a):
                          $subtotal=$a['precio']*$a['cantidad'];
                          $totalGeneral+=$subtotal;
                        ?>
                          <tr>
                            <td><?= htmlspecialchars($a['nombre']) ?></td>
                            <td><?= $a['cantidad'] ?></td>
                            <td>$<?= number_format($a['precio'],0,',','.') ?></td>
                            <td>$<?= number_format($subtotal,0,',','.') ?></td>
                            <td><?= $a['fechaApartado'] ?></td>
                          </tr>
                        <?php endforeach; ?>
                        </tbody>
                      </table>
                      <div style="text-align:right;">
                        <strong>Total: $<?= number_format($totalGeneral,0,',','.') ?></strong>
                      </div>
                  <?php else: ?>
                      <div class="alert alert-warning">No tienes productos apartados.</div>
                  <?php endif; ?>
                </div>
              </div>
          <?php elseif ($rol == "JUGADOR"): ?>
              <!-- JUGADOR: solo lectura -->
              <div class="card shadow mb-4">
                <div class="card-header py-3"><h6 class="m-0 font-weight-bold text-primary">Mis Apartados</h6></div>
                <div class="card-body">
                  <?php if (!empty($apartados)): ?>
                      <table class="table table-bordered">
                        <thead>
                          <tr><th>Producto</th><th>Cantidad</th><th>Precio</th><th>Total</th><th>Fecha</th></tr>
                        </thead>
                        <tbody>
                        <?php 
                        $totalGeneral=0;
                        foreach ($apartados as $a):
                          $subtotal=$a['precio']*$a['cantidad'];
                          $totalGeneral+=$subtotal;
                        ?>
                          <tr>
                            <td><?= htmlspecialchars($a['nombre']) ?></td>
                            <td><?= $a['cantidad'] ?></td>
                            <td>$<?= number_format($a['precio'],0,',','.') ?></td>
                            <td>$<?= number_format($subtotal,0,',','.') ?></td>
                            <td><?= $a['fechaApartado'] ?></td>
                          </tr>
                        <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                          <tr>
                            <td colspan="3"><strong>Total</strong></td>
                            <td colspan="2"><strong>$<?= number_format($totalGeneral,0,',','.') ?></strong></td>
                          </tr>
                        </tfoot>
                      </table>
                  <?php else: ?>
                      <div class="alert alert-warning">No tienes productos apartados.</div>
                  <?php endif; ?>
                </div>
              </div>
          <?php endif; ?>
      </div>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
    $("#menu-toggle").click(function(e) {
        e.preventDefault();
        $("#wrapper").toggleClass("toggled");
    });
</script>
</body>
</html>
