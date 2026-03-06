document.addEventListener('DOMContentLoaded', () => {
  const toggleBtn = document.getElementById('toggle-btn');

  toggleBtn.addEventListener('click', () => {
    document.body.classList.toggle('light-mode');

    if (document.body.classList.contains('light-mode')) {
      toggleBtn.textContent = '🌙 Modo Oscuro';
    } else {
      toggleBtn.textContent = '🌞 Modo Claro';
    }
  });
});
