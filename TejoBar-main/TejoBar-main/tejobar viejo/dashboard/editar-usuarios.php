<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== "admin") {
    header("Location: ../sesion/login.php");
    exit();
}

$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$idPersona = intval($_SESSION['idPersona']);

$message = "";

// ================== CRUD ==================

// Crear usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crear'])) {
    $nombreU = trim($_POST['nombre']);
    $correoU = trim($_POST['correo']);
    $rolU = trim($_POST['rol']);
    $pass = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO persona (nombre, correo, contraseña, rol) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nombreU, $correoU, $pass, $rolU);

    if ($stmt->execute()) {
        $message = "Usuario creado correctamente.";
    } else {
        $message = "Error al crear: " . $conn->error;
    }
    $stmt->close();
}

// Editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['editar'])) {
    $id = intval($_POST['idPersona']);
    $nombreU = trim($_POST['nombre']);
    $correoU = trim($_POST['correo']);
    $rolU = trim($_POST['rol']);

    if (!empty($_POST['contraseña'])) {
        $pass = password_hash($_POST['contraseña'], PASSWORD_BCRYPT);
        $sql = "UPDATE persona SET nombre=?, correo=?, contraseña=?, rol=? WHERE idPersona=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nombreU, $correoU, $pass, $rolU, $id);
    } else {
        $sql = "UPDATE persona SET nombre=?, correo=?, rol=? WHERE idPersona=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nombreU, $correoU, $rolU, $id);
    }

    if ($stmt->execute()) {
        $message = "Usuario actualizado correctamente.";
    } else {
        $message = "Error al actualizar: " . $conn->error;
    }
    $stmt->close();
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $id = intval($_POST['idPersona']);
    $sql = "DELETE FROM persona WHERE idPersona=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $message = "Usuario eliminado correctamente.";
    } else {
        $message = "Error al eliminar: " . $conn->error;
    }
    $stmt->close();
}

// Obtener todos los usuarios
$sqlUsuarios = "SELECT * FROM persona";
$resUsuarios = $conn->query($sqlUsuarios);
$usuarios = $resUsuarios ? $resUsuarios->fetch_all(MYSQLI_ASSOC) : [];
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tejobar - Editar Usuarios</title>
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
      <a href="dashboard.php" class="list-group-item list-group-item-action bg-light">
        <i class="fas fa-fw fa-tachometer-alt mr-2"></i>Dashboard
      </a>
      <a href="equipos-registrados.php" class="list-group-item list-group-item-action bg-light">
        <i class="fas fa-fw fa-users mr-2"></i>Equipos
      </a>
      <a href="productos.php" class="list-group-item list-group-item-action bg-light">
        <i class="fas fa-fw fa-box mr-2"></i>Productos
      </a>
      <a href="editar-usuarios.php" class="list-group-item list-group-item-action bg-light active">
        <i class="fas fa-fw fa-user-cog mr-2"></i>Editar usuarios
      </a>
      <a href="../index.php" class="list-group-item list-group-item-action bg-light">
        <i class="fas fa-fw fa-home mr-2"></i>Inicio
      </a>
    </div>
  </div>
  <!-- /Sidebar -->

  <!-- Page Content -->
  <div id="page-content-wrapper">
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
      <button class="btn btn-primary" id="menu-toggle"><i class="fas fa-bars"></i></button>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
              <i class="fas fa-user-circle mr-1"></i><?= htmlspecialchars(ucfirst($rol)) ?>
            </a>
            <div class="dropdown-menu dropdown-menu-right">
              <a class="dropdown-item" href="#"><i class="fas fa-user mr-1"></i> Perfil</a>
              <div class="dropdown-divider"></div>
              <a class="dropdown-item" href="../php/logout.php"><i class="fas fa-sign-out-alt mr-1"></i> Cerrar Sesión</a>
            </div>
          </li>
        </ul>
      </div>
    </nav>

    <div class="container-fluid">
      <h1 class="mt-4">Gestión de Usuarios 👥</h1>
      <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <div class="card shadow mb-4">
        <div class="card-header py-3">
          <h6 class="m-0 font-weight-bold text-primary">Usuarios registrados</h6>
        </div>
        <div class="card-body">
          <div class="table-responsive">
            <table class="table table-bordered">
              <thead>
                <tr>
                  <th>ID</th>
                  <th>Nombre</th>
                  <th>Correo</th>
                  <th>Rol</th>
                  <th>Contraseña (opcional)</th>
                  <th>Acciones</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($usuarios as $u): ?>
                <tr>
                  <form method="POST">
                    <td><?= htmlspecialchars($u['idPersona']) ?>
                      <input type="hidden" name="idPersona" value="<?= $u['idPersona'] ?>">
                    </td>
                    <td><input type="text" name="nombre" value="<?= htmlspecialchars($u['nombre']) ?>" class="form-control"></td>
                    <td><input type="email" name="correo" value="<?= htmlspecialchars($u['correo']) ?>" class="form-control"></td>
                    <td>
                      <select name="rol" class="form-control">
                        <option value="jugador" <?= $u['rol']=="jugador"?"selected":"" ?>>Jugador</option>
                        <option value="capitan" <?= $u['rol']=="capitan"?"selected":"" ?>>Capitán</option>
                        <option value="admin" <?= $u['rol']=="admin"?"selected":"" ?>>Admin</option>
                      </select>
                    </td>
                    <td style="position: relative;">
                      <input type="password" name="contraseña" placeholder="Nueva contraseña" class="form-control" id="pass-<?= $u['idPersona'] ?>">
                      <button type="button" onclick="togglePassword(<?= $u['idPersona'] ?>)" 
                              style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); border: none; background: none;">
                        <i class="fas fa-eye"></i>
                      </button>
                    </td>
                    <td>
                      <button type="submit" name="editar" class="btn btn-sm btn-success"><i class="fas fa-save"></i></button>
                      <button type="submit" name="eliminar" class="btn btn-sm btn-danger" onclick="return confirm('¿Eliminar este usuario?');"><i class="fas fa-trash"></i></button>
                    </td>
                  </form>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
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

  function togglePassword(id) {
    const input = document.getElementById('pass-' + id);
    const icon = input.nextElementSibling.querySelector('i');
    if (input.type === 'password') {
      input.type = 'text';
      icon.classList.remove('fa-eye');
      icon.classList.add('fa-eye-slash');
    } else {
      input.type = 'password';
      icon.classList.remove('fa-eye-slash');
      icon.classList.add('fa-eye');
    }
  }
</script>
</body>
</html>
<?php $conn->close(); ?>
