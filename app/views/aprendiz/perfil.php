<?php $pageTitle = 'Mi Perfil'; require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="content-section perfil-section">
  <div class="perfil-card">
    <div class="avatar"><i class="fas fa-user-circle"></i></div>
    <h2><?= e($user['nombre'] . ' ' . $user['apellido']) ?></h2>
    <p class="rol-tag">Aprendiz</p>

    <form id="formPerfil" action="<?= BASE_URL ?>/aprendiz/perfil/actualizar" method="POST" novalidate>
      <?= csrf_field() ?>
      <label for="nombre">Nombre</label>
      <input type="text" name="nombre" id="nombre" value="<?= e($user['nombre']) ?>" maxlength="80" required>
      <span class="field-error" id="err-nombre"></span>

      <label for="apellido">Apellido</label>
      <input type="text" name="apellido" id="apellido" value="<?= e($user['apellido']) ?>" maxlength="80" required>
      <span class="field-error" id="err-apellido"></span>

      <label>Tipo de documento</label>
      <input type="text" value="<?= e($user['tipo_doc']) ?>" disabled>

      <label>Número de documento</label>
      <input type="text" value="<?= e($user['numero_doc']) ?>" disabled>

      <label for="correo">Correo electrónico</label>
      <input type="email" name="correo" id="correo" value="<?= e($user['correo']) ?>" maxlength="120" required>
      <span class="field-error" id="err-correo"></span>

      <button type="submit" class="btn-submit">Guardar cambios</button>
    </form>
  </div>
</section>

<script src="<?= BASE_URL ?>/js/validation.js"></script>
<script>initPerfilValidation('formPerfil');</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
