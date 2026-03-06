<?php
session_start();
include "../php/conexion.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Buscar usuario por correo
    $sql = "SELECT * FROM persona WHERE correo = ?";
    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();

            // Verificación de contraseña (texto plano en BD)
            if ($user['contrasena'] === $password) {
                $_SESSION['idPersona'] = $user['idPersona'];
                $_SESSION['nombre'] = $user['nombre'];
                $_SESSION['rol'] = $user['rol'];

                // 🚀 Redirigir siempre al dashboard unificado
                header("Location: ../dashboard/dashboard.php");
                exit();
            } else {
                $error = "❌ Contraseña incorrecta.";
            }
        } else {
            $error = "❌ Usuario no encontrado.";
        }

        $stmt->close();
    } else {
        $error = "❌ Error en la consulta SQL.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Login</title>
  <link rel="stylesheet" href="sesionstyles.css"> <!-- corregido -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>
  <header>
    <a href="../index.html" class="logo">TejoBar</a>
    <nav>
      <ul class="menu-top">
        <li><a href="../index.html">🏠Inicio</a></li>
        <li><a href="../torneos/torneos.html">🥇Torneos</a></li>
        <li><a href="../productos/productos.html">🍻Comida y Bebida</a></li>
      </ul>
    </nav>
  </header>

  <div class="login-container">
    <div class="login-box">
      <div class="avatar">
        <i class="fas fa-mountain"></i>
      </div>
      <h2>LOG IN</h2>

      <?php if (!empty($error)): ?>
        <div class="error-msg"><?= $error ?></div>
      <?php endif; ?>

      <form method="POST" action="">
        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="Correo electrónico" required>
        </div>
        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Contraseña" required>
        </div>

        <button type="submit" class="btn-login">Login</button>
        <a href="register.html" class="forgot">¿No tienes Cuenta? Registrate</a>
      </form>
    </div>
  </div>
</body>
</html>
