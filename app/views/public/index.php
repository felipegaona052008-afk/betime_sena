<?php $pageTitle = 'Inicio'; require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="hero">
  <h1>Consulta tus Horas de Bienestar</h1>
  <p>Plataforma oficial de registro y seguimiento de actividades de bienestar SENA.</p>
  <p>Inscríbete en eventos, acumula horas y consulta tu progreso.</p>
  <div class="botones">
    <a href="<?= BASE_URL ?>/login/aprendiz" class="btn-primary">Iniciar Sesión</a>
    <a href="<?= BASE_URL ?>/registro"       class="btn-outline">Registrarse</a>
  </div>
</section>

<section class="carru">
  <h1>ACTIVIDADES</h1>
  <div class="carrusel">
    <div class="card"><div class="card-label">Virtual</div></div>
    <div class="card"><div class="card-label">Presencial</div></div>
    <div class="card"><div class="card-label">Idiomas</div></div>
    <div class="card"><div class="card-label">Empresarial</div></div>
  </div>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>