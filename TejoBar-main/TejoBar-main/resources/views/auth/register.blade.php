@extends('layouts.app')

@section('title', 'Registro - TejoBar')

@push('styles')
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background-image: url('{{ asset("img/fondo.jpg") }}');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    color: #333;
}

header {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 60px;
  background-color: rgba(0, 0, 0, 0.8);
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 0 20px;
  z-index: 1000;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
}

.logo {
  font-size: 24px;
  font-weight: bold;
  text-decoration: none;
  color: #ffa500;
}

nav ul.menu-top {
  list-style: none;
  display: flex;
  gap: 20px;
}

nav ul.menu-top li a {
  color: #ffa500;
  text-decoration: none;
  font-weight: bold;
  transition: color 0.2s ease;
}

nav ul.menu-top li a:hover {
  color: #ffcc00;
}

* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
  font-family: 'Arial', sans-serif;
}

body {
  background: url('{{ asset("img/fondo.jpg") }}') no-repeat center center fixed;
  background-size: cover;
  height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;
}

.login-container {
  backdrop-filter: blur(4px);
  padding: 20px;
  border-radius: 20px;
}

.login-box {
  background: linear-gradient(to bottom, #ffa500, #131313, #141414);
  padding: 40px;
  border-radius: 20px;
  width: 320px;
  box-shadow: 0 0 15px rgba(0,0,0,0.2);
  text-align: center;
}

.avatar {
  width: 80px;
  height: 80px;
  background-color: white;
  border-radius: 50%;
  margin: 0 auto 20px;
  display: flex;
  justify-content: center;
  align-items: center;
  font-size: 36px;
  color: #ffa500;
}

h2 {
  color: white;
  margin-bottom: 30px;
  font-weight: bold;
}

.input-group {
  position: relative;
  margin-bottom: 20px;
}

.input-group i {
  position: absolute;
  top: 50%;
  left: 10px;
  transform: translateY(-50%);
  color: #ccc;
}

.input-group input, .input-group select {
  width: 100%;
  padding: 10px 10px 10px 35px;
  border: none;
  border-bottom: 1px solid white;
  background: transparent;
  color: white;
  outline: none;
}

.input-group input::placeholder {
  color: #eee;
}

.input-group select option {
  color: black;
}

.btn-login {
  width: 100%;
  padding: 12px;
  background-color: white;
  color: #6c63ff;
  font-weight: bold;
  border: none;
  border-radius: 25px;
  cursor: pointer;
  transition: background-color 0.3s ease;
}

.btn-login:hover {
  background-color: #ddd;
}

.forgot {
  display: block;
  margin-top: 20px;
  color: white;
  font-size: 14px;
  text-decoration: none;
}

.forgot:hover {
  text-decoration: underline;
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
  text-decoration: none;
  color: #ffa500;
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

.error-msg {
  color: #ffdddd;
  background-color: #b30000;
  padding: 8px;
  border-radius: 4px;
  margin-bottom: 10px;
  font-size: 14px;
}

.success-msg {
  color: #ddffdd;
  background-color: #00b300;
  padding: 8px;
  border-radius: 4px;
  margin-bottom: 10px;
  font-size: 14px;
}
</style>
@endpush

@section('content')
<header>
    <a href="{{ route('home') }}" class="logo">TejoBar</a>
    <nav>
        <ul class="menu-top">
            <li><a href="{{ route('home') }}">🏠Inicio</a></li>
            <li><a href="{{ route('torneos.index') }}">🥇Partidos</a></li>
            <li><a href="{{ route('productos.index') }}">🍻Comida y Bebida</a></li>
        </ul>
    </nav>
</header>

<div class="login-container">
    <div class="login-box">
        <div class="avatar">
            <i class="fas fa-user-plus"></i>
        </div>
        <h2>REGISTRO</h2>

        @if ($errors->any())
            <div class="error-msg">
                @foreach ($errors->all() as $error)
                    {{ $error }}
                @endforeach
            </div>
        @endif

        @if (session('success'))
            <div class="success-msg">{{ session('success') }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="nombre" placeholder="Nombre completo" value="{{ old('nombre') }}" required>
            </div>

            <div class="input-group">
                <i class="fas fa-user-tag"></i>
                <select name="rol" required>
                    <option value="">Selecciona tu rol</option>
                    <option value="jugador" {{ old('rol') == 'jugador' ? 'selected' : '' }}>Jugador</option>
                    <option value="capitan" {{ old('rol') == 'capitan' ? 'selected' : '' }}>Capitán</option>
                </select>
            </div>

            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="correo" placeholder="Correo electrónico" value="{{ old('correo') }}" required>
            </div>

            <div class="input-group">
                <i class="fas fa-phone"></i>
                <input type="text" name="numero" placeholder="Número de teléfono" value="{{ old('numero') }}" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="contrasena" placeholder="Contraseña (mín. 4 caracteres)" required>
            </div>

            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="contrasena_confirmation" placeholder="Confirmar contraseña" required>
            </div>

            <button type="submit" class="btn-login">Registrarse</button>
            <a href="{{ route('login') }}" class="forgot">¿Ya tienes cuenta? Inicia sesión</a>
        </form>
    </div>
</div>
@endsection
