<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>TejoBar Puente Aranda</title>
  <link rel="stylesheet" href="{{ asset('newstyle.css') }}"/>
  <link href="https://fonts.googleapis.com/css2?family=Segoe+UI&display=swap" rel="stylesheet" />
  <script src="{{ asset('newjs.js') }}"></script>
</head>

<body>
  <header>
    <a href="{{ route('home') }}" class="logo">TejoBar</a>
    <nav>
      <ul class="menu-top">
        <li><a href="{{ route('home') }}">🏠Inicio</a></li>
        <li><a href="{{ route('torneos.index') }}">🥇Partidos</a></li>
        <li><a href="{{ route('productos.index') }}">🍻Comida y Bebida</a></li>
        @customauth
          <li><a href="{{ route('dashboard') }}">📊Dashboard</a></li>
          <li>
            <form method="POST" action="{{ route('logout') }}" style="display: inline;">
              @csrf
              <button type="submit" style="border: none; background: none; color: inherit; text-decoration: none; cursor: pointer;">
                🚪Cerrar Sesión
              </button>
            </form>
          </li>
        @else
          <li><a href="{{ route('login', ['ref' => request()->fullUrl()]) }}">🔓Iniciar Sesión</a></li>
        @endcustomauth
      </ul>
    </nav>
  </header>

  <div id="loader">
  <div class="spinner"></div>
  </div>

<section class="hero oculto">
  <div class="hero-bg hero-bg1"></div>
  <div class="hero-bg hero-bg2"></div>
  <h1>Bienvenido a TejoBar</h1>
      <p>El mejor lugar en Puente Aranda para disfrutar del tejo, cerveza fría y comida típica colombiana.</p>
</section>

  <section id="partidos" class="section reveal">
    <h2>Partidos de Tejo</h2>
    <p>¡Inscríbete con tus amigos y gana premios!</p>
  </section>

  <section id="menu" class="section1 reveal">
    <h2>Comida y Bebida</h2>
    <div class="grid">

    <a href="{{ route('productos.index') }}" class="card">
      <div class="card-content">
        <h3>Cerveza</h3>
        <p>Variedades Frias, Locales y Nacionales </p>
      </div>
      <div class="card-image" style="background-image: url('{{ asset('img/productos/cerveza.jpg') }}');"></div>
    </a>

    <a href="{{ route('productos.index') }}" class="card">
      <div class="card-content">
        <h3>Gallina Criolla</h3>
        <p>Variedades Frias, Locales y Nacionales </p>
      </div>
      <div class="card-image" style="background-image: url('{{ asset('img/productos/caldo.jpg') }}');"></div>
    </a>

    <a href="{{ route('productos.index') }}" class="card">
      <div class="card-content">
        <h3>Picada</h3>
        <p>Variedades Frias, Locales y Nacionales </p>
      </div>
      <div class="card-image" style="background-image: url('{{ asset('img/productos/picada.jpg') }}');"></div>
    </a>

    <a href="{{ route('productos.index') }}" class="card">
      <div class="card-content">
        <h3>Snacks</h3>
        <p>Variedades Frias, Locales y Nacionales </p>
      </div>
      <div class="card-image" style="background-image: url('{{ asset('img/productos/snacks.jpg') }}');"></div>
    </a>
    </div>
  </section>

<section id="reservas" class="section reveal">
  <h2>inscríbete en un partido 🏆  </h2>
  <p>únete al próximo partido con tu equipo. ¡Es rápido y fácil!</p>
  @customauth
    <a href="{{ route('dashboard') }}" class="boton">Ir al Dashboard</a>
  @else
    <a href="{{ route('register') }}" class="boton">Registrate</a>
  @endcustomauth
</section>

  <section id="contacto" class="section contacto reveal">
    <h2>Contáctanos</h2>
    <p>📍 Puente Aranda, Bogotá<br>📞 123 456 7890</p>
  </section>

  <footer>
    <p>&copy; 2025 TejoBar. Todos los derechos reservados.</p>
  </footer>

</body>

<button id="toggle-btn" onclick="toggleMode()">☀️</button>

</html>
