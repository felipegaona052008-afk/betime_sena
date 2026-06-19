<?php $pageTitle = 'Registro'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Registro Aprendiz | BeTimeSENA</title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/auth.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-body green-bg">

<?php $err = flash('error'); ?>
<?php if ($err): ?><div class="alert alert-error"><?= e($err) ?></div><?php endif; ?>

<div class="login-container wide">
  <form class="login-box" id="formRegistro" action="<?= BASE_URL ?>/registro" method="POST" novalidate>
    <?= csrf_field() ?>
    <h2>Registro Aprendiz</h2>

    <div class="form-row">
      <div class="form-col">
        <label for="nombre">Nombre</label>
        <input type="text" name="nombre" id="nombre" placeholder="Tu nombre" maxlength="80" autocomplete="given-name" required>
        <span class="field-error" id="err-nombre"></span>
      </div>
      <div class="form-col">
        <label for="apellido">Apellido</label>
        <input type="text" name="apellido" id="apellido" placeholder="Tu apellido" maxlength="80" autocomplete="family-name" required>
        <span class="field-error" id="err-apellido"></span>
      </div>
    </div>

    <label for="tipo_doc">Tipo de documento</label>
    <select name="tipo_doc" id="tipo_doc" required>
      <option value="">Seleccione</option>
      <option value="CC">Cédula</option>
      <option value="TI">Tarjeta de Identidad</option>
      <option value="CE">Cédula de Extranjería</option>
    </select>
    <span class="field-error" id="err-tipo_doc"></span>

    <label for="numero_doc">Número de documento</label>
    <input type="text" name="numero_doc" id="numero_doc" placeholder="Solo dígitos" maxlength="20" pattern="\d{5,20}" autocomplete="off" required>
    <span class="field-error" id="err-numero_doc"></span>

    <label for="correo">Correo electrónico</label>
    <input type="email" name="correo" id="correo" placeholder="ejemplo@correo.com" maxlength="120" autocomplete="email" required>
    <span class="field-error" id="err-correo"></span>

    <label for="password">Contraseña</label>
    <div class="input-group">
      <input type="password" name="password" id="password" placeholder="Mínimo 8 caracteres" maxlength="72" autocomplete="new-password" required>
      <button type="button" class="toggle-pass" data-target="password" aria-label="Ver contraseña"><i class="fas fa-eye"></i></button>
    </div>
    <div class="password-strength" id="passStrength"></div>
    <span class="field-error" id="err-password"></span>

    <label for="confirmar">Confirmar contraseña</label>
    <div class="input-group">
      <input type="password" name="confirmar" id="confirmar" placeholder="Repite tu contraseña" maxlength="72" autocomplete="new-password" required>
      <button type="button" class="toggle-pass" data-target="confirmar" aria-label="Ver contraseña"><i class="fas fa-eye"></i></button>
    </div>
    <span class="field-error" id="err-confirmar"></span>

    <button type="submit" class="btn-submit">Registrarse</button>
    <a href="<?= BASE_URL ?>/login/aprendiz" class="btn-link">¿Ya tienes cuenta? Inicia sesión</a>
    <a href="<?= BASE_URL ?>/" class="btn-volver">← Volver</a>
  </form>
</div>

<script src="<?= BASE_URL ?>/js/validation.js"></script>
<script>initRegistroValidation('formRegistro');</script>
</body>
</html>
