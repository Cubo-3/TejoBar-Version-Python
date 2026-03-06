<?php
session_start();
include("../php/conexion.php");

if (!isset($_SESSION['rol']) || !isset($_SESSION['idPersona'])) {
    header("Location: ../sesion/login.php");
    exit();
}

$rol = $_SESSION['rol'];
$nombre = $_SESSION['nombre'];
$idPersona = intval($_SESSION['idPersona']);

$message = "";

// --- Acción: salir del equipo (jugador) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['salir_equipo'])) {
    $sqlDelete = "DELETE FROM jugador_equipo WHERE idJugador = ?";
    $stmtDel = $conn->prepare($sqlDelete);
    if ($stmtDel) {
        $stmtDel->bind_param("i", $idPersona);
        if ($stmtDel->execute()) {
            $message = "Has salido del equipo correctamente.";
        }
        $stmtDel->close();
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Error al salir del equipo: " . $conn->error;
    }
}

// --- Acción: expulsar jugador (capitán) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expulsar_jugador'])) {
    $idJugadorExpulsar = intval($_POST['idJugador']);
    $sqlKick = "DELETE FROM jugador_equipo WHERE idJugador = ?";
    $stmtKick = $conn->prepare($sqlKick);
    if ($stmtKick) {
        $stmtKick->bind_param("i", $idJugadorExpulsar);
        if ($stmtKick->execute()) {
            $message = "Jugador expulsado correctamente.";
        }
        $stmtKick->close();
        header("Location: dashboard.php");
        exit();
    } else {
        $message = "Error al expulsar: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Tejobar - Dashboard</title>
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
  <a href="dashboard.php" class="list-group-item list-group-item-action bg-light active">
    <i class="fas fa-fw fa-tachometer-alt mr-2"></i>Dashboard
  </a>

  <?php if ($rol === "capitan"): ?>

    <a href="productos.php" class="list-group-item list-group-item-action bg-light">
      <i class="fas fa-fw fa-box mr-2"></i>Productos
    </a>

  <?php elseif ($rol === "jugador"): ?>

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

    <!-- Solo visible para admin -->
    <a href="editar-usuarios.php" class="list-group-item list-group-item-action bg-light">
      <i class="fas fa-fw fa-user-cog mr-2"></i>Editar usuarios
    </a>

  <?php endif; ?>

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
      <h1 class="mt-4">Bienvenido <?= htmlspecialchars($nombre) ?> 👋</h1>
      <p>Tu rol es: <strong><?= htmlspecialchars(ucfirst($rol)) ?></strong></p>

      <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
      <?php endif; ?>

      <!-- ===================== JUGADOR ===================== -->
      <?php if ($rol === "jugador" || $rol === "capitan"): ?>
        <div class="card shadow mb-4">
          <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
              <?= $rol === "capitan" ? "Gestión de tu Equipo" : "Próximos Partidos" ?>
            </h6>
          </div>
          <div class="card-body">
            <?php
            $sqlEquipo = "
              SELECT je.idEquipo, je.esCapitan, e.nombreEquipo
              FROM jugador_equipo je
              INNER JOIN equipo e ON je.idEquipo = e.idEquipo
              WHERE je.idJugador = ?
              LIMIT 1
            ";
            $stmtEq = $conn->prepare($sqlEquipo);
            $stmtEq->bind_param("i", $idPersona);
            $stmtEq->execute();
            $resEq = $stmtEq->get_result();

            if ($resEq && $resEq->num_rows > 0) {
                $rowEq = $resEq->fetch_assoc();
                $idEquipo = (int)$rowEq['idEquipo'];
                $nombreEquipo = $rowEq['nombreEquipo'];

                echo "<p>Perteneces al equipo: <strong>" . htmlspecialchars($nombreEquipo) . "</strong></p>";

                if ($rol === "jugador") {
                    echo '<form method="POST" onsubmit="return confirm(\'¿Seguro que quieres salir del equipo?\');">
                            <button type="submit" name="salir_equipo" class="btn btn-danger mb-3">
                              <i class="fas fa-sign-out-alt"></i> Salir del equipo
                            </button>
                          </form>';
                }

                if ($rol === "capitan") {
                    // Listar jugadores del equipo
                    $sqlJug = "SELECT p.idPersona, p.nombre 
                               FROM jugador_equipo je
                               INNER JOIN persona p ON je.idJugador = p.idPersona
                               WHERE je.idEquipo = ?";
                    $stmtJug = $conn->prepare($sqlJug);
                    $stmtJug->bind_param("i", $idEquipo);
                    $stmtJug->execute();
                    $resJug = $stmtJug->get_result();

                    echo "<h5>Jugadores de tu equipo</h5>";
                    echo "<div class='table-responsive'><table class='table table-bordered'>
                            <thead><tr><th>ID</th><th>Nombre</th><th>Acción</th></tr></thead><tbody>";
                    while ($j = $resJug->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($j['idPersona']) . "</td>";
                        echo "<td>" . htmlspecialchars($j['nombre']) . "</td>";
                        echo "<td>";
                        if ($j['idPersona'] != $idPersona) {
                            echo "<form method='POST' style='display:inline;' 
                                        onsubmit='return confirm(\"¿Expulsar a este jugador?\");'>
                                    <input type='hidden' name='idJugador' value='" . intval($j['idPersona']) . "'>
                                    <button type='submit' name='expulsar_jugador' class='btn btn-sm btn-danger'>
                                      <i class='fas fa-user-times'></i> Expulsar
                                    </button>
                                  </form>";
                        } else {
                            echo "<span class='text-muted'>Tú</span>";
                        }
                        echo "</td></tr>";
                    }
                    echo "</tbody></table></div>";
                }

                // Partidos del equipo
                $sqlPartidos = "
                  SELECT t.idPartido, t.fecha, e1.nombreEquipo AS equipo1, e2.nombreEquipo AS equipo2, c.disponibilidad AS cancha
                  FROM torneo t
                  INNER JOIN equipo e1 ON t.equipo1 = e1.idEquipo
                  INNER JOIN equipo e2 ON t.equipo2 = e2.idEquipo
                  INNER JOIN cancha c ON t.cancha = c.idCancha
                  WHERE t.equipo1 = ? OR t.equipo2 = ?
                  ORDER BY t.fecha ASC
                ";
                $stmtPart = $conn->prepare($sqlPartidos);
                $stmtPart->bind_param("ii", $idEquipo, $idEquipo);
                $stmtPart->execute();
                $resPart = $stmtPart->get_result();

                echo "<h5 class='mt-4'>Próximos Partidos</h5>";
                if ($resPart && $resPart->num_rows > 0) {
                    echo "<div class='table-responsive'><table class='table table-bordered'>
                            <thead><tr>
                              <th>ID Partido</th>
                              <th>Fecha</th>
                              <th>Equipo 1</th>
                              <th>Equipo 2</th>
                              <th>Cancha</th>
                            </tr></thead><tbody>";
                    while ($p = $resPart->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($p['idPartido']) . "</td>";
                        echo "<td>" . htmlspecialchars($p['fecha']) . "</td>";
                        echo "<td>" . htmlspecialchars($p['equipo1']) . "</td>";
                        echo "<td>" . htmlspecialchars($p['equipo2']) . "</td>";
                        echo "<td>" . htmlspecialchars($p['cancha']) . "</td>";
                        echo "</tr>";
                    }
                    echo "</tbody></table></div>";
                } else {
                    echo "<p>No hay partidos programados para tu equipo.</p>";
                }
            } else {
                echo "<div class='alert alert-warning'>No estás en un equipo aún.</div>";
            }
            ?>
          </div>
        </div>
      <?php endif; ?>
      <!-- ===================== /JUGADOR & CAPITAN ===================== -->

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
<?php $conn->close(); ?>
