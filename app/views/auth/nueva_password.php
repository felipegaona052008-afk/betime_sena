<?php $pageTitle = 'Nueva Contraseña'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva Contraseña | BeTimeSENA</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-body green-bg">

<?php $err = flash('error'); $ok = flash('success'); ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
<?php if ($ok):  ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

<div class="login-container">
  <form class="login-box" id="formNuevaPass" action="<?= BASE_URL ?>/nueva-password" method="POST" novalidate>
    <?= csrf_field() ?>

    <!-- Token oculto — se envía con el formulario para verificar en el controller -->
    <input type="hidden" name="token" value="<?= e($token) ?>">

    <h2>Crear nueva contraseña</h2>
    <p style="font-size:13px;color:#555;text-align:center;margin-bottom:18px;line-height:1.6;">
      Tu nueva contraseña debe tener mínimo 8 caracteres, una mayúscula, un número y un carácter especial.
    </p>

    <label for="password">Nueva contraseña</label>
    <div class="input-group">
      <input type="password" name="password" id="password"
             placeholder="Mínimo 8 caracteres" maxlength="72"
             autocomplete="new-password" required>
      <button type="button" class="toggle-pass" data-target="password" aria-label="Ver contraseña">
        <i class="fas fa-eye"></i>
      </button>
    </div>
    <div class="password-strength" id="passStrength"></div>
    <span class="field-error" id="err-password"></span>

    <label for="confirmar">Confirmar contraseña</label>
    <div class="input-group">
      <input type="password" name="confirmar" id="confirmar"
             placeholder="Repite tu contraseña" maxlength="72"
             autocomplete="new-password" required>
      <button type="button" class="toggle-pass" data-target="confirmar" aria-label="Ver contraseña">
        <i class="fas fa-eye"></i>
      </button>
    </div>
    <span class="field-error" id="err-confirmar"></span>

    <button type="submit" class="btn-submit">
      <i class="fas fa-lock" style="margin-right:8px;"></i>
      Guardar nueva contraseña
    </button>

    <a href="<?= BASE_URL ?>/recuperar-password" class="btn-volver">← Solicitar nuevo enlace</a>
  </form>
</div>

<script src="<?= BASE_URL ?>/js/validation.js"></script>
<script>
// Reutilizamos el indicador de fortaleza y toggle de validation.js
const passInput = document.getElementById('password');
if (passInput) {
  passInput.addEventListener('input', function() {
    checkPasswordStrength(this.value);
  });
}

document.getElementById('formNuevaPass').addEventListener('submit', function(e) {
  let valid = true;

  const pass     = document.getElementById('password').value;
  const confirmar = document.getElementById('confirmar').value;
  const errPass   = document.getElementById('err-password');
  const errConf   = document.getElementById('err-confirmar');

  const regex = /^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/;

  // Limpiar errores previos
  errPass.style.display = 'none';
  errConf.style.display = 'none';
  document.getElementById('password').classList.remove('input-error');
  document.getElementById('confirmar').classList.remove('input-error');

  if (!regex.test(pass)) {
    errPass.textContent = 'Mín. 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.';
    errPass.style.display = 'block';
    document.getElementById('password').classList.add('input-error');
    valid = false;
  }

  if (pass !== confirmar) {
    errConf.textContent = 'Las contraseñas no coinciden.';
    errConf.style.display = 'block';
    document.getElementById('confirmar').classList.add('input-error');
    valid = false;
  }

  if (!valid) e.preventDefault();
});
</script>
</body>
</html>
