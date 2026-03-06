<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Registro</title>
  <link rel="stylesheet" href="sesionstyles.css">
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
        <i class="fas fa-user-plus"></i>
      </div>
      <h2>REGISTRO</h2>

      <?php
      if ($_SERVER["REQUEST_METHOD"] == "POST") {
          $nombre = $_POST['nombre'];
          $rol = $_POST['rol'];
          $email = $_POST['email'];
          $password = $_POST['password'];
          $confirmPassword = $_POST['confirmPassword'];

          if ($password !== $confirmPassword) {
              echo "<div class='error-msg'>Las contraseñas no coinciden.</div>";
          } else {
              // Conexión a BD
              $conn = new mysqli("localhost", "root", "", "tejobar");
              if ($conn->connect_error) {
                  die("Error de conexión: " . $conn->connect_error);
              }

              // Insertar datos
              $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
              $stmt = $conn->prepare("INSERT INTO usuarios (nombre, rol, email, clave) VALUES (?, ?, ?, ?)");
              $stmt->bind_param("ssss", $nombre, $rol, $email, $hashedPassword);

              if ($stmt->execute()) {
                  echo "<div class='success-msg'>Registro exitoso. Ahora puedes <a href='login.php'>iniciar sesión</a>.</div>";
              } else {
                  echo "<div class='error-msg'>Error: " . $conn->error . "</div>";
              }

              $stmt->close();
              $conn->close();
          }
      }
      ?>

      <form method="POST" action="">
        <div class="input-group">
          <i class="fas fa-user"></i>
          <input type="text" name="nombre" placeholder="Nombre completo" required>
        </div>

        <div class="input-group">
          <i class="fas fa-user-tag"></i>
          <select name="rol" required>
            <option value="">Selecciona tu rol</option>
            <option value="jugador">Jugador</option>
            <option value="capitan">Capitán</option>
          </select>
        </div>

        <div class="input-group">
          <i class="fas fa-envelope"></i>
          <input type="email" name="email" placeholder="Correo electrónico" required>
        </div>

        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="password" placeholder="Contraseña (mín. 4 caracteres)" required>
        </div>

        <div class="input-group">
          <i class="fas fa-lock"></i>
          <input type="password" name="confirmPassword" placeholder="Confirmar contraseña" required>
        </div>

        <button type="submit" class="btn-login">Registrarse</button>
        <a href="login.php" class="forgot">¿Ya tienes cuenta? Inicia sesión</a>
      </form>
    </div>
  </div>

</body>
</html>
