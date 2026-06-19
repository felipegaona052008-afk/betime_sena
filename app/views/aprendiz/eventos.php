<?php $pageTitle = 'Eventos'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="eventos-layout">

  <aside class="anuncios-lateral">
    <h3>Anuncios</h3>
    <?php foreach ($anuncios as $an): ?>
      <div class="anuncio"><strong><?= e($an['titulo']) ?></strong></div>
    <?php endforeach; ?>
  </aside>

  <main class="eventos-blog">
    <h1>Actividades de Bienestar</h1>
    <?php foreach ($actividades as $act): ?>
    <div class="actividad-blog">
      <div class="act-info">
        <h2><?= e($act['titulo']) ?></h2>
        <p class="fecha"><i class="fas fa-calendar-alt"></i> Fecha de inicio: <?= e($act['fecha_inicio']) ?></p>
        <p><?= e($act['descripcion'] ?? '') ?></p>
        <?php if (auth()): ?>
        <form action="<?= BASE_URL ?>/aprendiz/inscribir" method="POST" class="form-inscribir-blog">
          <?= csrf_field() ?>
          <input type="hidden" name="actividad_id" value="<?= (int)$act['id'] ?>">
          <button type="submit" class="btn-inscribir">Inscribirme</button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if (empty($actividades)): ?>
      <p class="no-data">No hay eventos disponibles.</p>
    <?php endif; ?>
  </main>

  <aside class="anuncios-lateral">
    <h3>Importante</h3>
    <div class="anuncio"><strong>Inscripciones abiertas</strong></div>
    <div class="anuncio"><strong>Próximos eventos</strong></div>
    <div class="anuncio"><strong>Noticias del centro</strong></div>
  </aside>

</div>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
