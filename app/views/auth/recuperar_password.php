<?php $pageTitle = 'Recuperar Contraseña'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Recuperar Contraseña | BeTimeSENA</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-body green-bg">

<?php $err = flash('error'); $ok = flash('success'); ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
<?php if ($ok):  ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

<div class="login-container">
  <form class="login-box" id="formRecuperar" action="<?= BASE_URL ?>/recuperar-password" method="POST" novalidate>
    <?= csrf_field() ?>

    <h2>¿Olvidaste tu contraseña?</h2>
    <p style="font-size:13px;color:#555;text-align:center;margin-bottom:18px;line-height:1.6;">
      Ingresa tu correo registrado y te enviaremos un enlace para crear una nueva contraseña.
    </p>

    <label for="correo">Correo electrónico</label>
    <input type="email" name="correo" id="correo"
           placeholder="ejemplo@correo.com" maxlength="120"
           autocomplete="email" required>
    <span class="field-error" id="err-correo"></span>

    <button type="submit" class="btn-submit">
      <i class="fas fa-paper-plane" style="margin-right:8px;"></i>
      Enviar enlace de recuperación
    </button>

    <a href="<?= BASE_URL ?>/login/aprendiz" class="btn-volver">← Volver al inicio de sesión</a>
  </form>
</div>

<script src="<?= BASE_URL ?>/js/validation.js"></script>
<script>
// Validación simple del formulario de recuperación
(function() {
  const form = document.getElementById('formRecuperar');
  if (!form) return;
  form.addEventListener('submit', function(e) {
    const correo = document.getElementById('correo').value.trim();
    const errEl  = document.getElementById('err-correo');
    const emailReg = /^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/;
    if (!emailReg.test(correo)) {
      errEl.textContent = 'Ingresa un correo electrónico válido.';
      errEl.style.display = 'block';
      document.getElementById('correo').classList.add('input-error');
      e.preventDefault();
    } else {
      errEl.style.display = 'none';
      document.getElementById('correo').classList.remove('input-error');
    }
  });
})();
</script>
</body>
</html>
