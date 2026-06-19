<?php $pageTitle = 'Mis Horas'; require_once __DIR__ . '/../layouts/header.php'; ?>

<section class="content-section">
  <h1>Mis Horas de Bienestar</h1>

  <div class="horas-resumen">
    <div class="horas-card">
      <i class="fas fa-clock"></i>
      <span class="horas-valor"><?= number_format($totalHoras, 1) ?></span>
      <span class="horas-label">Horas completadas</span>
    </div>
  </div>

  <?php if (empty($horas)): ?>
    <p class="no-data">Aún no tienes inscripciones. <a href="<?= BASE_URL ?>/aprendiz">Ver actividades</a>.</p>
  <?php else: ?>
  <div class="table-wrap">
    <table class="tabla-horas">
      <thead>
        <tr>
          <th>Actividad</th><th>Tipo</th><th>Fecha</th><th>Horas</th><th>Estado</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($horas as $h): ?>
        <tr>
          <td><?= e($h['titulo']) ?></td>
          <td><?= e(ucfirst($h['tipo'])) ?></td>
          <td><?= e($h['fecha_inicio']) ?></td>
          <td><?= number_format($h['horas'], 1) ?></td>
          <td><span class="badge badge-<?= e($h['estado']) ?>"><?= e(ucfirst($h['estado'])) ?></span></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
