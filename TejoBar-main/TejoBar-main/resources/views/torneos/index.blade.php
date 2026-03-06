@extends('layouts.public')

@section('title', 'Partidos - TejoBar')

@push('styles')
<style>
/* Reset */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

header {
  background-color: #141414;
  color: #ffa500;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
}

.logo {
  font-size: 28px;
  font-weight: bold;
  text-decoration: none;
  color: #ffa500;
}

nav ul {
  list-style: none;
  display: flex;
  gap: 20px;
  padding: 0;
  margin: 0;
}

nav a {
  color: #ffa500;
  text-decoration: none;
  font-weight: bold;
}

body, html {
  font-family: 'Segoe UI', serif;
  font-weight: bold;
  background: url('{{ asset("img/fondotorneo.png") }}') no-repeat center center fixed;
  background-size: cover;
  min-height: 100vh;
}

/* Contenedor principal */
.container {
  position: relative;
  width: 100%;
  padding-bottom: 1rem;
}

/* Carteles */
.carteles {
  display: flex;
  justify-content: center;
  gap: 2rem;
  flex-wrap: wrap;
  margin-bottom: 3rem;
}

.cartel {
  background: url('{{ asset("img/cartel.png") }}') no-repeat center center;
  background-size: contain;
  width: 10rem;
  height: 10rem;
  display: flex;
  align-items: center;
  justify-content: center;
  text-decoration: none;
}

.cartel p {
  font-weight: bold;
  font-size: 14px;
  text-align: center;
  color: #000;
}

/* Tabla */
.tabla {
  max-width: 900px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 1rem;
}

.encabezado, .fila {
  display: grid;
  grid-template-columns: 1fr 2fr 1fr;
  gap: 1rem;
}

.encabezado div {
  background-color: #d4af75;
  padding: 10px;
  font-weight: bold;
  text-align: center;
  border-radius: 4px;
}

.fila div {
  background-color: #fdf1de;
  padding: 10px;
  text-align: center;
  border-radius: 4px;
}

/* Imágenes decorativas */
.decor {
  position: absolute;
  width: 100px;
  z-index: 1;
}

.decor-left-top {
  top: 50px;
  left: 20px;
}

.decor-left-bottom {
  bottom: 20px;
  left: 30px;
}

.decor-right-top {
  top: 50px;
  right: 20px;
}

.decor-right-bottom {
  bottom: 20px;
  right: 30px;
}

/* Responsivo */
@media (max-width: 768px) {
  .carteles {
    flex-direction: column;
    align-items: center;
    gap: 1.5rem;
  }

  .cartel {
    width: 10rem;
    height: 10rem;
  }

  .encabezado, .fila {
    grid-template-columns: 1fr;
    text-align: center;
  }

  .decor {
    width: 60px;
  }

  .decor-left-top, .decor-right-top {
    top: 10px;
  }

  .decor-left-bottom, .decor-right-bottom {
    bottom: 10px;
  }
}

.menu-top li a {
  display: inline-block;
  transition: transform 0.3s ease;
}

.menu-top li a:hover {
  transform: scale(1.1);
}

.logo {
  display: inline-block;
  transition: none;
  cursor: pointer;
  position: relative;
  overflow: hidden;
}

.logo:hover {
  animation: deslizarLogo 0.5s forwards;
}

@keyframes deslizarLogo {
  0% {
    transform: translateX(0);
    opacity: 1;
  }
  40% {
    transform: translateX(50px);
    opacity: 0;
  }
  50% {
    transform: translateX(-50px);
    opacity: 0;
  }
  100% {
    transform: translateX(0);
    opacity: 1;
  }
}

html, body {
  height: 100%;
  margin: 0;
  display: flex;
  flex-direction: column;
}

.container {
  flex: 1;
}

footer {
  background-color: #333;
  color: white;
  text-align: center;
  padding: 15px;
  position: relative;
  bottom: 0;
  width: 100%;
}
</style>
@endpush

@section('content')
<!-- Mensajes de sesión -->
@if(session('success'))
    <div class="alert alert-success" style="background-color: #d4edda; color: #155724; padding: 15px; margin: 20px; border: 1px solid #c3e6cb; border-radius: 5px; text-align: center;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger" style="background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border: 1px solid #f5c6cb; border-radius: 5px; text-align: center;">
        {{ session('error') }}
    </div>
@endif

<!-- Carteles -->
<div class="carteles">
    <!-- Aquí se pueden agregar carteles adicionales si es necesario -->
</div>

<!-- Tabla -->
<div class="tabla">
    <div class="encabezado">
        <div>Número de Equipos</div>
        <div>Hora y Fecha</div>
        <div>Cancha</div>
    </div>

    @if($torneos->count() > 0)
        @foreach($torneos as $torneo)
            <div class="fila">
                <div>
                    Equipo {{ $torneo->equipo1->nombreEquipo ?? $torneo->equipo1 }} 
                    vs 
                    Equipo {{ $torneo->equipo2->nombreEquipo ?? $torneo->equipo2 }}
                </div>
                <div>
                    {{ $torneo->fecha->format('d/m/Y') }}<br>
                    {{ $torneo->fecha->format('h:i A') }}
                </div>
                <div>{{ $torneo->cancha->disponibilidad ?? 'Cancha ' . $torneo->cancha }}</div>
            </div>
        @endforeach
    @else
        <div class="fila">
            <div colspan="3">No hay partidos registrados</div>
        </div>
    @endif
</div>

<!-- Imágenes decorativas -->
<img src="{{ asset('img/imgizq.png') }}" alt="Figura izquierda" class="decor decor-left-top">
<img src="{{ asset('img/imgizqa.png') }}" alt="Figura izquierda abajo" class="decor decor-left-bottom">
<img src="{{ asset('img/imgder.png') }}" alt="Figura derecha" class="decor decor-right-top">
<img src="{{ asset('img/imgdera.png') }}" alt="Figura derecha abajo" class="decor decor-right-bottom">
@endsection
