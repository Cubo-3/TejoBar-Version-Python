
  function toggleMode() {
    document.body.classList.toggle('light-mode');

    const btn = document.getElementById('toggle-btn');
    btn.textContent = document.body.classList.contains('light-mode') ? '🌙' : '☀️';
  }


document.addEventListener("DOMContentLoaded", () => {
  const imagenes = [
    "img/fondo.jpg",
    "img/fondo2.jpg"
  ];

  const bg1 = document.querySelector('.hero-bg1');
  const bg2 = document.querySelector('.hero-bg2');
  const hero = document.querySelector('.hero');
  const loader = document.getElementById('loader');
  let index = 0;
  let active = true;

  // Precarga de imágenes
  imagenes.forEach(src => {
    const img = new Image();
    img.src = src;
  });

  // Cargar la imagen inicial
  const imgInicial = new Image();
  imgInicial.src = imagenes[0];
  imgInicial.onload = () => {
    bg1.style.backgroundImage = `url('${imagenes[0]}')`;
    hero.classList.remove('oculto');
    loader.style.display = 'none';
  };

  // Cambiar imágenes cada 3 segundos
  setInterval(() => {
    index = (index + 1) % imagenes.length;
    const nuevaImagen = imagenes[index];

    if (active) {
      bg2.style.backgroundImage = `url('${nuevaImagen}')`;
      bg2.style.opacity = 1;
      bg1.style.opacity = 0;
    } else {
      bg1.style.backgroundImage = `url('${nuevaImagen}')`;
      bg1.style.opacity = 1;
      bg2.style.opacity = 0;
    }

    active = !active;
  }, 3000);
});




document.addEventListener("DOMContentLoaded", () => {
  const elementos = document.querySelectorAll('.reveal');

  const mostrarElemento = () => {
    elementos.forEach(el => {
      const rect = el.getBoundingClientRect();
      if (rect.top < window.innerHeight - 100) {
        el.classList.add('visible');
      }
    });
  };

  window.addEventListener('scroll', mostrarElemento);
  mostrarElemento(); // Mostrar los que ya están a la vista al cargar
});
