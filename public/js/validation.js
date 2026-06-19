/**
 * BeTimeSENA – validation.js
 * Validación del lado del cliente para todos los formularios.
 * Previene XSS mostrando mensajes controlados, nunca ejecuta HTML externo.
 */

'use strict';

// ─── UTILIDADES ─────────────────────────────────────────────────────────────

/**
 * Muestra un mensaje de error junto al campo.
 * Usa textContent (nunca innerHTML) para prevenir XSS.
 */
function showError(fieldId, message) {
  const el = document.getElementById('err-' + fieldId);
  if (el) {
    el.textContent = message;
    el.style.display = 'block';
  }
  const field = document.getElementById(fieldId);
  if (field) field.classList.add('input-error');
}

function clearError(fieldId) {
  const el = document.getElementById('err-' + fieldId);
  if (el) {
    el.textContent = '';
    el.style.display = 'none';
  }
  const field = document.getElementById(fieldId);
  if (field) field.classList.remove('input-error');
}

function clearAllErrors(formId) {
  const form = document.getElementById(formId);
  if (!form) return;
  form.querySelectorAll('.field-error').forEach(el => {
    el.textContent = '';
    el.style.display = 'none';
  });
  form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
}

// ─── REGLAS DE VALIDACIÓN ───────────────────────────────────────────────────

const RULES = {
  nombre:      { pattern: /^[\p{L}\s]{2,80}$/u,  msg: 'Nombre inválido (2–80 letras, sin números).' },
  apellido:    { pattern: /^[\p{L}\s]{2,80}$/u,  msg: 'Apellido inválido (2–80 letras).' },
  numero_doc:  { pattern: /^\d{5,20}$/,           msg: 'Solo dígitos, entre 5 y 20 caracteres.' },
  correo:      { pattern: /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/, msg: 'Ingresa un correo electrónico válido.' },
  password:    { pattern: /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/,
                 msg: 'Mín. 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.' },
};

function validateField(id, value) {
  if (!value || value.trim() === '') return 'Este campo es obligatorio.';
  const rule = RULES[id];
  if (rule && !rule.pattern.test(value.trim())) return rule.msg;
  return null;
}

// ─── INDICADOR DE FORTALEZA DE CONTRASEÑA ───────────────────────────────────

function checkPasswordStrength(password) {
  const bar = document.getElementById('passStrength');
  if (!bar) return;

  let score = 0;
  if (password.length >= 8)  score++;
  if (/[A-Z]/.test(password)) score++;
  if (/\d/.test(password))    score++;
  if (/[\W_]/.test(password)) score++;

  const labels  = ['', 'Débil', 'Regular', 'Buena', 'Fuerte'];
  const classes = ['', 'weak',  'fair',    'good',  'strong'];

  bar.className   = 'password-strength ' + (classes[score] || '');
  bar.textContent  = score ? 'Fortaleza: ' + labels[score] : '';
}

// ─── TOGGLE MOSTRAR / OCULTAR CONTRASEÑA ────────────────────────────────────

document.addEventListener('click', function (e) {
  const btn = e.target.closest('.toggle-pass');
  if (!btn) return;
  const targetId = btn.dataset.target;
  const input    = document.getElementById(targetId);
  if (!input) return;
  const isPassword = input.type === 'password';
  input.type = isPassword ? 'text' : 'password';
  const icon = btn.querySelector('i');
  if (icon) {
    icon.className = isPassword ? 'fas fa-eye-slash' : 'fas fa-eye';
  }
});

// ─── FORMULARIO: LOGIN (aprendiz o admin) ────────────────────────────────────

function initLoginValidation(formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener('submit', function (e) {
    clearAllErrors(formId);
    let valid = true;

    const tipoDoc  = form.querySelector('[name="tipo_doc"]')?.value;
    const numDoc   = form.querySelector('[name="numero_doc"]')?.value;
    const password = form.querySelector('[name="password"]')?.value;

    if (!tipoDoc) {
      showError('tipo_doc', 'Selecciona un tipo de documento.');
      valid = false;
    }

    const numErr = validateField('numero_doc', numDoc);
    if (numErr) { showError('numero_doc', numErr); valid = false; }

    if (!password || password.length < 1) {
      showError('password', 'Ingresa tu contraseña.');
      valid = false;
    }

    if (!valid) e.preventDefault();
  });

  // Live validation
  addLiveValidation(form, ['numero_doc']);
}

// ─── FORMULARIO: REGISTRO ───────────────────────────────────────────────────

function initRegistroValidation(formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  // Strength meter on keyup
  const passInput = document.getElementById('password');
  if (passInput) {
    passInput.addEventListener('input', () => checkPasswordStrength(passInput.value));
  }

  form.addEventListener('submit', function (e) {
    clearAllErrors(formId);
    let valid = true;

    const fields = ['nombre', 'apellido', 'numero_doc', 'correo', 'password'];
    fields.forEach(id => {
      const val = form.querySelector(`[name="${id}"]`)?.value ?? '';
      const err = validateField(id, val);
      if (err) { showError(id, err); valid = false; }
    });

    const tipoDoc = form.querySelector('[name="tipo_doc"]')?.value;
    if (!tipoDoc) {
      showError('tipo_doc', 'Selecciona un tipo de documento.');
      valid = false;
    }

    const pass     = document.getElementById('password')?.value ?? '';
    const confirm  = document.getElementById('confirmar')?.value ?? '';
    if (pass && confirm && pass !== confirm) {
      showError('confirmar', 'Las contraseñas no coinciden.');
      valid = false;
    }

    if (!valid) e.preventDefault();
  });

  addLiveValidation(form, ['nombre', 'apellido', 'numero_doc', 'correo']);
}

// ─── FORMULARIO: PERFIL ─────────────────────────────────────────────────────

function initPerfilValidation(formId) {
  const form = document.getElementById(formId);
  if (!form) return;

  form.addEventListener('submit', function (e) {
    clearAllErrors(formId);
    let valid = true;

    ['nombre', 'apellido', 'correo'].forEach(id => {
      const val = form.querySelector(`[name="${id}"]`)?.value ?? '';
      const err = validateField(id, val);
      if (err) { showError(id, err); valid = false; }
    });

    if (!valid) e.preventDefault();
  });

  addLiveValidation(form, ['nombre', 'apellido', 'correo']);
}

// ─── ADMIN VALIDATIONS ──────────────────────────────────────────────────────

function initAdminValidation() {

  // Crear actividad
  const fAct = document.getElementById('formCrearActividad');
  if (fAct) {
    fAct.addEventListener('submit', function (e) {
      clearAllErrors('formCrearActividad');
      let valid = true;

      const titulo = document.getElementById('act-titulo')?.value ?? '';
      if (!titulo.trim() || titulo.length > 150) {
        showError('act-titulo', 'Título requerido (máx. 150 caracteres).');
        valid = false;
      }

      const tipo = document.getElementById('act-tipo')?.value;
      if (!tipo) {
        showError('act-tipo', 'Selecciona un tipo de actividad.');
        valid = false;
      }

      const fechaIni = document.getElementById('act-fecha-ini')?.value;
      if (!fechaIni) {
        showError('act-fecha-ini', 'Fecha de inicio requerida.');
        valid = false;
      }

      const fechaFin = document.getElementById('act-fecha-fin')?.value;
      if (fechaFin && fechaIni && fechaFin < fechaIni) {
        showError('act-fecha-ini', 'La fecha fin no puede ser anterior a la fecha de inicio.');
        valid = false;
      }

      if (!valid) e.preventDefault();
    });
  }

  // Crear anuncio
  const fAnuncio = document.getElementById('formAnuncio');
  if (fAnuncio) {
    fAnuncio.addEventListener('submit', function (e) {
      clearAllErrors('formAnuncio');
      const titulo = document.getElementById('anuncio-titulo')?.value ?? '';
      if (!titulo.trim() || titulo.length > 200) {
        showError('anuncio-titulo', 'Título requerido (máx. 200 caracteres).');
        e.preventDefault();
      }
    });
  }
}

// ─── LIVE VALIDATION HELPER ─────────────────────────────────────────────────

function addLiveValidation(form, fieldIds) {
  fieldIds.forEach(id => {
    const input = form.querySelector(`[name="${id}"]`);
    if (!input) return;
    input.addEventListener('blur', function () {
      const err = validateField(id, this.value);
      if (err) showError(id, err);
      else     clearError(id);
    });
    input.addEventListener('input', function () {
      if (this.classList.contains('input-error')) {
        const err = validateField(id, this.value);
        if (!err) clearError(id);
      }
    });
  });
}

// ─── MENÚ LATERAL & PERFIL (global) ─────────────────────────────────────────

document.addEventListener('DOMContentLoaded', function () {
  const menuToggle = document.getElementById('menuToggle');
  const menu       = document.getElementById('menu');
  const perfilToggle = document.getElementById('perfilToggle');
  const perfilMenu   = document.getElementById('perfilMenu');

  if (menuToggle && menu) {
    menuToggle.addEventListener('click', () => menu.classList.toggle('active'));
    document.addEventListener('click', e => {
      if (!menu.contains(e.target) && !menuToggle.contains(e.target)) {
        menu.classList.remove('active');
      }
    });
  }

  if (perfilToggle && perfilMenu) {
    perfilToggle.addEventListener('click', e => {
      e.stopPropagation();
      perfilMenu.classList.toggle('active');
    });
    document.addEventListener('click', () => perfilMenu.classList.remove('active'));
  }

  // Auto-dismiss flash alerts
  document.querySelectorAll('.alert').forEach(el => {
    setTimeout(() => el.style.opacity = '0', 4000);
    setTimeout(() => el.remove(),             4500);
  });
});
