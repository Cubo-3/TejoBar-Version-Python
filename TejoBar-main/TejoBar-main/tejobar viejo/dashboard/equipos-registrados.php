<?php
session_start();
include("../php/conexion.php"); // tu conexión a la BD

if (!isset($_SESSION['idPersona']) || !isset($_SESSION['rol'])) {
    header("Location: ../sesion/login.php");
    exit();
}

$rol = $_SESSION['rol'];
$idPersona = $_SESSION['idPersona'];
$equipos = [];

// ADMIN → Todos los equipos
if ($rol == "ADMIN") {
    $sql = "SELECT e.idEquipo, e.nombreEquipo, p.nombre AS capitan
            FROM equipos e
            JOIN personas p ON e.idCapitan = p.idPersona";
    $result = $conn->query($sql);
    while ($row = $result->fetch_assoc()) {
        $equipos[] = $row;
    }
}
// CAPITÁN → Solo su equipo
elseif ($rol == "CAPITAN") {
    $sql = "SELECT e.idEquipo, e.nombreEquipo, p.nombre AS capitan
            FROM equipos e
            JOIN personas p ON e.idCapitan = p.idPersona
            WHERE e.idCapitan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPersona);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipos = $result->fetch_all(MYSQLI_ASSOC);
}
// JUGADOR → El equipo en el que está
elseif ($rol == "JUGADOR") {
    $sql = "SELECT e.idEquipo, e.nombreEquipo, p.nombre AS capitan
            FROM equipos e
            JOIN personas p ON e.idCapitan = p.idPersona
            JOIN jugadores j ON j.idEquipo = e.idEquipo
            WHERE j.idPersona = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $idPersona);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipos = $result->fetch_all(MYSQLI_ASSOC);
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
<div class="bg-light border-right" id="sidebar-wrapper">
    <div class="sidebar-heading">Tejobar</div>
<div class="d-flex" id="wrapper">
  <!-- Sidebar -->
  <div class="bg-light border-right" id="sidebar-wrapper">
    <div class="list-group list-group-flush">


      <?php if ($rol === "capitan"): ?>

        <a href="../../errores/500.html" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-users mr-2"></i>Equipos
        </a>
        <a href="productos.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-box mr-2"></i>Productos
        </a>
      <?php elseif ($rol === "jugador"): ?>
        <a href="equipos-registrados.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-users mr-2"></i>Equipos
        </a>
        <a href="productos.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-box mr-2"></i>Productos
        </a>
      <?php elseif ($rol === "admin"): ?>

        <a href="equipos-registrados.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-users mr-2"></i>Equipos
        </a>
        <a href="productos.php" class="list-group-item list-group-item-action bg-light">
          <i class="fas fa-fw fa-box mr-2"></i>Productos
        </a>

      <?php endif; ?>

      <a href="clasificacion.php" class="list-group-item list-group-item-action bg-light">
        <i class="fas fa-fw fa-chart-line mr-2"></i>Clasificación
      </a>
      <a href="../../index.html" class="list-group-item list-group-item-action bg-light">
        <i class="fas fa-fw fa-home mr-2"></i>Inicio
      </a>
    </div>
  </div>
</div>
</div>


        <div id="page-content-wrapper">
            <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom">
                <button class="btn btn-primary" id="menu-toggle">
                    <i class="fas fa-bars"></i>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <ul class="navbar-nav ml-auto mt-2 mt-lg-0">
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown">
                                <i class="fas fa-user-circle mr-1"></i><?php echo ucfirst($rol); ?>
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

                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">Equipos Registrados</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <?php if (!empty($equipos)): ?>
                                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>Nombre del equipo</th>
                                            <th>Nombre del capitán</th>
                                            <th>ID del equipo</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($equipos as $equipo): ?>
                                            <tr>
                                                <td><?= htmlspecialchars($equipo['nombreEquipo']) ?></td>
                                                <td><?= htmlspecialchars($equipo['capitan']) ?></td>
                                                <td><?= $equipo['idEquipo'] ?></td>
                                                <td>
                                                    <?php if ($rol == "ADMIN"): ?>
                                                        <button class="btn btn-danger btn-sm">Eliminar</button>
                                                    <?php elseif ($rol == "JUGADOR"): ?>
                                                        <button class="btn btn-warning btn-sm">Salir del equipo</button>
                                                    <?php else: ?>
                                                        <span class="text-muted">N/A</span>
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php else: ?>
                                <div class="alert alert-warning">No tienes equipos registrados.</div>
                            <?php endif; ?>
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
    </script>
</body>
</html>
