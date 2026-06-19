<?php $pageTitle = 'Login Aprendiz'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Ingreso Aprendiz | BeTimeSENA</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-body green-bg">

<?php $err = flash('error'); $ok = flash('success'); ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>
<?php if ($ok):  ?><div class="alert alert-success"><?= e($ok) ?></div><?php endif; ?>

<div class="login-container">
  <form class="login-box" id="formLoginAprendiz" action="<?= BASE_URL ?>/login/aprendiz" method="POST" novalidate>
    <?= csrf_field() ?>
    <h2>Ingreso Aprendiz</h2>

    <label for="tipo_doc">Tipo de documento</label>
    <select name="tipo_doc" id="tipo_doc" required>
      <option value="">Seleccione</option>
      <option value="CC">Cédula</option>
      <option value="TI">Tarjeta de Identidad</option>
      <option value="CE">Cédula de Extranjería</option>
    </select>
    <span class="field-error" id="err-tipo_doc"></span>

    <label for="numero_doc">Número de documento</label>
    <input type="text" name="numero_doc" id="numero_doc"
           placeholder="Ingrese su número" maxlength="20"
           pattern="\d{5,20}" autocomplete="username" required>
    <span class="field-error" id="err-numero_doc"></span>

    <label for="password">Contraseña</label>
    <div class="input-group">
      <input type="password" name="password" id="password"
             placeholder="Ingrese su contraseña" maxlength="72"
             autocomplete="current-password" required>
      <button type="button" class="toggle-pass" data-target="password" aria-label="Ver contraseña">
        <i class="fas fa-eye"></i>
      </button>
    </div>
    <span class="field-error" id="err-password"></span>

    <button type="submit" class="btn-submit">Ingresar</button>
    <a href="<?= BASE_URL ?>/recuperar-password" class="btn-link" style="color:#777;">¿Olvidaste tu contraseña?</a>
    <a href="<?= BASE_URL ?>/registro" class="btn-link">¿No tienes cuenta? Regístrate</a>
    <a href="<?= BASE_URL ?>/"         class="btn-volver">← Volver</a>
  </form>
</div>

<script src="<?= BASE_URL ?>/js/validation.js"></script>
<script>initLoginValidation('formLoginAprendiz');</script>
</body>
</html>
