@extends('layouts.public')

@section('title', 'Productos - TejoBar')

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

.grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
  gap: 20px;
  margin-top: 30px;
}

.card {
  display: flex;
  background-color: #1d1c19;
  border: 1px solid #ddd;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  overflow: hidden;
  text-decoration: none;
  color: inherit;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  min-height: 160px;
  transition: 1.5s;
}

.card:hover {
  transform: scale(1.01);
  box-shadow: 0 4px 16px rgba(0,0,0,0.15);
}

.card-image {
  width: 140px;
  height: 100%;
  background-size: cover;
  background-position: center;
  flex-shrink: 0;
}

.card-content {
  padding: 20px;
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: center;
  text-align: left;
}

.card-content h3 {
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 8px;
  color: #ffffff;
  transition: 1.5s;
}

.card-content p {
  font-size: 16px;
  color: #ffffff;
  transition: 1.5s;
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

/* Modo Claro */
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

.light-mode .card {
  background-color: #ffffff;
  color: #000;
  border: 1px solid #ccc;
  transition: 1.5s;
}

.light-mode footer {
  background-color: #f4f4f4;
  color: #000;
  transition: 1.5s;
}

.light-mode .card-content {
  background-color: #885d00;
  color: #000;
  transition: 1.5s;
}

.light-mode .card-content h3 {
  color: #000;
  transition: 1.5s;
}
</style>
@endpush

@section('content')
<section class="section">
    <h2>🍻 Comida y Bebida</h2>
    <div class="grid">
        @forelse($productos as $producto)
            <a href="{{ route('productos.show', $producto) }}" class="card">
                <div class="card-image" style="background-image: url('{{ asset('img/productos/' . $producto->urlImg) }}')"></div>
                <div class="card-content">
                    <h3>{{ $producto->nombre }}</h3>
                    <p>💲 {{ number_format($producto->precio, 0, ',', '.') }}</p>
                    <p>Stock: {{ $producto->stock }}</p>
                </div>
            </a>
        @empty
            <div class="card">
                <div class="card-content">
                    <h3>No hay productos disponibles</h3>
                    <p>Próximamente tendremos más productos para ti.</p>
                </div>
            </div>
        @endforelse
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
