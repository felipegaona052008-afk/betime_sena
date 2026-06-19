/**
 * BeTimeSENA – main.js
 * Comportamiento global: menú, perfil, inscripciones AJAX.
 */

'use strict';

document.addEventListener('DOMContentLoaded', function () {

  // ── Menú lateral ──
  const menuToggle = document.getElementById('menuToggle');
  const menu       = document.getElementById('menu');
  if (menuToggle && menu) {
    menuToggle.addEventListener('click', () => menu.classList.toggle('active'));
    document.addEventListener('click', e => {
      if (!menu.contains(e.target) && !menuToggle.contains(e.target))
        menu.classList.remove('active');
    });
  }

  // ── Perfil dropdown ──
  const perfilToggle = document.getElementById('perfilToggle');
  const perfilMenu   = document.getElementById('perfilMenu');
  if (perfilToggle && perfilMenu) {
    perfilToggle.addEventListener('click', e => {
      e.stopPropagation();
      perfilMenu.classList.toggle('active');
    });
    document.addEventListener('click', () => perfilMenu.classList.remove('active'));
  }

  // ── Inscripción AJAX ──
  document.querySelectorAll('.form-inscribir, .form-inscribir-blog').forEach(form => {
    form.addEventListener('submit', async function (e) {
      e.preventDefault();

      const fd  = new FormData(this);
      const btn = this.querySelector('button[type="submit"]');
      if (btn) btn.disabled = true;

      try {
        const res  = await fetch('/aprendiz/inscribir', {
          method: 'POST',
          body:   fd,
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
        });
        const data = await res.json();
        showToast(data.msg, data.ok ? 'success' : 'error');
        if (data.ok && btn) {
          btn.textContent = '✓ Inscrito';
          btn.disabled    = true;
        } else if (btn) {
          btn.disabled = false;
        }
      } catch (err) {
        showToast('Error de conexión. Intenta de nuevo.', 'error');
        if (btn) btn.disabled = false;
      }
    });
  });

  // ── Auto-dismiss flash alerts ──
  document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => el.classList.add('fade-out'), 4000);
    setTimeout(() => el.remove(), 4600);
  });
});

// ── Toast notification ──
function showToast(message, type = 'success') {
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  // textContent evita XSS
  toast.textContent = message;
  document.body.appendChild(toast);
  requestAnimationFrame(() => toast.classList.add('show'));
  setTimeout(() => {
    toast.classList.remove('show');
    setTimeout(() => toast.remove(), 400);
  }, 3500);
}
