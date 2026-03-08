@extends('layouts.public')

@section('title', $producto->nombre . ' - TejoBar')

@push('styles')
<style>
body {
  margin: 0;
  font-family: 'Segoe UI', sans-serif;
  background-color: #fdf6e3;
  color: #333;
  transition: 1.5s;
}

header {
  background-color: #141414;
  color: #ffa500;
  padding: 15px 30px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  transition: 1.5s;
}

.logo {
  font-size: 28px;
  font-weight: bold;
  text-decoration: none;
  color: #ffa500;
  display: inline-block;
  cursor: pointer;
  position: relative;
  overflow: hidden;
  transition: 1.5s;
}

.logo:hover {
  animation: deslizarLogo 0.5s forwards;
}

@keyframes deslizarLogo {
  0% { transform: translateX(0); opacity: 1; }
  40% { transform: translateX(50px); opacity: 0; }
  50% { transform: translateX(-50px); opacity: 0; }
  100% { transform: translateX(0); opacity: 1; }
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
  display: inline-block;
  transition: transform 0.3s ease;
}

.menu-top li a:hover {
  transform: scale(1.1);
}

.section {
  flex: 1;
  padding: 60px 20px;
  background-color: #6d4700;
  color: #ffffff;
  text-align: center;
  transition: 1.5s;
  padding-bottom: 100rem;
}

footer {
  background-color: #333;
  color: white;
  text-align: center;
  padding: 15px;
  transition: 1.5s;
  position: fixed;
  bottom: 0;
  width: 100%;
}

#toggle-btn {
  position: fixed;
  bottom: 20px;
  right: 20px;
  padding: 12px 18px;
  font-size: 14px;
  border: none;
  border-radius: 25px;
  background-color: #333;
  color: white;
  cursor: pointer;
  box-shadow: 0 4px 8px rgba(0,0,0,0.3);
  z-index: 999;
  transition: background-color 0.3s ease;
}

#toggle-btn:hover {
  background-color: #555;
}

.detalle-producto {
  display: flex;
  flex-wrap: wrap;
  background-color: #1d1c19;
  border-radius: 16px;
  overflow: hidden;
  max-width: 900px;
  margin: 40px auto;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
  transition: 1.5s;
}

.detalle-imagen {
  flex: 1 1 400px;
  max-height: 400px;
  overflow: hidden;
}

.detalle-imagen img {
  width: 100%;
  height: 100%;
  object-fit: cover;
  display: block;
}

.detalle-info {
  flex: 1 1 400px;
  padding: 30px;
  color: #fff;
  display: flex;
  flex-direction: column;
  justify-content: center;
  background-color: #141414;
  transition: 1.5s;
}

.detalle-info h3 {
  font-size: 30px;
  margin-bottom: 15px;
  color: #ffa500;
  transition: 1.5s;
}

.detalle-descripcion {
  font-size: 16px;
  margin-bottom: 20px;
  line-height: 1.6;
  color: #ddd;
  transition: 1.5s;
}

.detalle-precio {
  font-size: 22px;
  font-weight: bold;
  margin-bottom: 15px;
  color: #ffa500;
  transition: 1.5s;
}

.detalle-volumen {
  font-size: 14px;
  color: #bbb;
  transition: 1.5s;
}

input[type="number"] {
  padding: 10px;
  font-size: 16px;
  margin: 10px 0 20px;
  border-radius: 8px;
  border: none;
  text-align: center;
}

.btn-comprar {
  background-color: #ffa500;
  color: #000;
  padding: 12px 28px;
  font-size: 16px;
  font-weight: bold;
  border: none;
  border-radius: 30px;
  cursor: pointer;
  transition: 0.3s;
  text-decoration: none;
  display: inline-block;
}

.btn-comprar:hover {
  background-color: #e69500;
}

/* Light mode ajustes */
.light-mode {
  background-color: #ffffff;
  color: #000;
}

.light-mode header {
  background-color: #f4f4f4;
  color: #333;
  transition: 1.5s;
}

.light-mode .logo {
  color: #ff6600;
  transition: 1.5s;
}

.light-mode nav a {
  color: #ff6600;
  transition: 1.5s;
}

.light-mode .section {
  background-color: #f9f9f9;
  color: #333;
  transition: 1.5s;
}

.light-mode footer {
  background-color: #f4f4f4;
  color: #000;
  transition: 1.5s;
}

.light-mode .detalle-producto {
  background-color: #fdf5e6;
  transition: 1.5s;
}

.light-mode .detalle-info {
  background-color: #fff;
  color: #000;
  transition: 1.5s;
}

.light-mode .detalle-info h3 {
  color: #000000;
  transition: 1.5s;
}

.light-mode .detalle-precio {
  color: #cc6600;
  transition: 1.5s;
}

.light-mode .detalle-descripcion {
  font-size: 16px;
  margin-bottom: 20px;
  line-height: 1.6;
  color: #000000;
  transition: 1.5s;
}

.light-mode input[type="number"] {
  background-color: #afafaf;
  transition: 1.5s;
}
</style>
@endpush

@section('content')
<section class="section">
    <h2>🍴 {{ $producto->nombre }}</h2>
    <div class="detalle-producto">
        <div class="detalle-imagen">
            <img src="{{ asset('img/productos/' . $producto->urlImg) }}" alt="{{ $producto->nombre }}">
        </div>
        <div class="detalle-info">
            <h3>{{ $producto->nombre }}</h3>
            <p class="detalle-precio">
                💲 {{ number_format($producto->precio, 0, ',', '.') }}
            </p>
            <p><strong>Stock disponible:</strong> {{ $producto->stock }}</p>
            <p><strong>Fecha de vencimiento:</strong> {{ $producto->fechaVencimiento->format('d/m/Y') }}</p>

            @if (session('success'))
                <div style="background-color: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #28a745;">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 8px; margin: 15px 0; border-left: 4px solid #dc3545;">
                    {{ session('error') }}
                </div>
            @endif

            @customauth
                @customhasrole('admin')
                    <div style="margin: 20px 0; padding: 15px; background-color: #2a2a2a; border-radius: 8px; border-left: 4px solid #ffa500;">
                        <h4 style="color: #ffa500; margin-bottom: 10px;">👑 Panel de Administración</h4>
                        <p style="color: #ddd; margin-bottom: 15px;">Como administrador, puedes editar este producto.</p>
                        <a href="{{ route('productos.edit', $producto) }}" class="btn-comprar">✏️ Editar Producto</a>
                    </div>
                @endcustomhasrole
                
                @if(session('rol') === 'jugador' || session('rol') === 'capitan')
                    <form method="POST" action="{{ route('apartados.store') }}">
                        @csrf
                        <input type="hidden" name="idProducto" value="{{ $producto->idProducto }}">
                        
                        <label for="cantidad" style="display: block; margin-bottom: 10px; color: #ddd;">Cantidad a apartar:</label>
                        <input type="number" id="cantidad" name="cantidad" min="1" max="{{ $producto->stock }}" value="1" required />
                        <br><br>
                        
                        <button type="submit" class="btn-comprar">🛒 Apartar ahora</button>
                        <p style="font-size: 12px; color: #bbb; margin-top: 10px;">
                            El producto se apartará en tu cuenta y podrás comprarlo más tarde.
                        </p>
                    </form>
                @endif
            @else
                <div style="margin: 20px 0; padding: 15px; background-color: #2a2a2a; border-radius: 8px; border-left: 4px solid #ff6b6b;">
                    <h4 style="color: #ff6b6b; margin-bottom: 10px;">🔒 Acceso Requerido</h4>
                    <p style="color: #ddd; margin-bottom: 15px;">Necesitas iniciar sesión como jugador o capitán para apartar productos.</p>
                    <a href="{{ route('login', ['ref' => request()->fullUrl()]) }}" class="btn-comprar">🔓 Iniciar Sesión</a>
                </div>
            @endcustomauth
        </div>
    </div>
</section>

<button id="toggle-btn">🌞 Modo Claro</button>
@endsection

@push('scripts')
<script>
document.getElementById('toggle-btn').addEventListener('click', function() {
    document.body.classList.toggle('light-mode');
    const btn = this;
    btn.textContent = document.body.classList.contains('light-mode') ? '🌙 Modo Oscuro' : '🌞 Modo Claro';
});
</script>
@endpush
