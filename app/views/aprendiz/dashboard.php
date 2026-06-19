<?php $pageTitle = 'Mi Inicio'; require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="hero">
  <h1>Bienvenido, <?= e($user['nombre']) ?> 👋</h1>
  <p>Tienes <strong><?= number_format($totalHoras, 1) ?> horas</strong> de bienestar completadas.</p>
  <div class="buscador">
    <input type="text" id="buscadorInput" placeholder="Buscar actividades, eventos..." aria-label="Buscar">
    <button onclick="buscarActividad()" aria-label="Buscar">🔍</button>
  </div>
</section>

<section class="actividades-section">
  <h2>Actividades Disponibles</h2>
  <div class="actividades-grid" id="actividadesGrid">
    <?php foreach ($actividades as $act): ?>
    <div class="actividad-card" data-titulo="<?= e(strtolower($act['titulo'])) ?>">
      <div class="act-tipo act-tipo-<?= e($act['tipo']) ?>"><?= e(ucfirst($act['tipo'])) ?></div>
      <h3><?= e($act['titulo']) ?></h3>
      <p class="act-fecha"><i class="fas fa-calendar"></i> <?= e($act['fecha_inicio']) ?></p>
      <?php if (!empty($act['lugar'])): ?>
        <p class="act-lugar"><i class="fas fa-map-marker-alt"></i> <?= e($act['lugar']) ?></p>
      <?php endif; ?>
      <p class="act-desc"><?= e(mb_substr($act['descripcion'] ?? '', 0, 100)) ?><?= strlen($act['descripcion'] ?? '') > 100 ? '…' : '' ?></p>
      <form action="<?= BASE_URL ?>/aprendiz/inscribir" method="POST" class="form-inscribir">
        <?= csrf_field() ?>
        <input type="hidden" name="actividad_id" value="<?= (int)$act['id'] ?>">
        <button type="submit" class="btn-inscribir">Inscribirme</button>
      </form>
    </div>
    <?php endforeach; ?>
    <?php if (empty($actividades)): ?>
      <p class="no-data">No hay actividades disponibles por el momento.</p>
    <?php endif; ?>
  </div>
</section>

<aside class="anuncios-section">
  <h2>Anuncios</h2>
  <?php foreach ($anuncios as $an): ?>
    <div class="anuncio-card">
      <strong><?= e($an['titulo']) ?></strong>
      <?php if (!empty($an['contenido'])): ?><p><?= e($an['contenido']) ?></p><?php endif; ?>
    </div>
  <?php endforeach; ?>
</aside>

<script>
function buscarActividad() {
  const q = document.getElementById('buscadorInput').value.toLowerCase().trim();
  document.querySelectorAll('.actividad-card').forEach(card => {
    card.style.display = card.dataset.titulo.includes(q) ? '' : 'none';
  });
}
document.getElementById('buscadorInput').addEventListener('input', buscarActividad);
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
