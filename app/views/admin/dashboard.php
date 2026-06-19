<?php $pageTitle = 'Panel Administración'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="admin-layout">
  <div class="admin-sidebar">
    <ul>
      <li><a href="#sec-actividades">Actividades</a></li>
      <li><a href="#sec-inscripciones">Inscripciones</a></li>
      <li><a href="#sec-aprendices">Aprendices</a></li>
      <li><a href="#sec-anuncios">Anuncios</a></li>
    </ul>
  </div>

  <div class="admin-main">

    <section id="sec-actividades" class="admin-section">
      <h2>Gestión de Actividades</h2>
      <div class="admin-form-card">
        <h3>Nueva Actividad</h3>
        <form id="formCrearActividad" action="<?= BASE_URL ?>/admin/actividades/crear" method="POST" novalidate>
          <?= csrf_field() ?>
          <div class="form-row">
            <div class="form-col">
              <label>Título</label>
              <input type="text" name="titulo" id="act-titulo" maxlength="150" required>
              <span class="field-error" id="err-act-titulo"></span>
            </div>
            <div class="form-col">
              <label>Tipo</label>
              <select name="tipo" id="act-tipo" required>
                <option value="">Seleccione</option>
                <option value="deportiva">Deportiva</option>
                <option value="cultural">Cultural</option>
                <option value="academica">Académica</option>
                <option value="recreativa">Recreativa</option>
                <option value="otra">Otra</option>
              </select>
              <span class="field-error" id="err-act-tipo"></span>
            </div>
          </div>
          <div class="form-row">
            <div class="form-col">
              <label>Fecha de inicio</label>
              <input type="date" name="fecha_inicio" id="act-fecha-ini" required>
              <span class="field-error" id="err-act-fecha-ini"></span>
            </div>
            <div class="form-col">
              <label>Fecha fin (opcional)</label>
              <input type="date" name="fecha_fin" id="act-fecha-fin">
            </div>
          </div>
          <label>Lugar (opcional)</label>
          <input type="text" name="lugar" maxlength="120">
          <label>Descripción</label>
          <textarea name="descripcion" rows="3" maxlength="1000"></textarea>
          <button type="submit" class="btn-submit">Crear Actividad</button>
        </form>
      </div>

      <div class="table-wrap">
        <table class="tabla-admin">
          <thead><tr><th>Título</th><th>Tipo</th><th>Fecha</th><th>Acciones</th></tr></thead>
          <tbody>
            <?php foreach ($actividades as $act): ?>
            <tr>
              <td><?= e($act['titulo']) ?></td>
              <td><?= e(ucfirst($act['tipo'])) ?></td>
              <td><?= e($act['fecha_inicio']) ?></td>
              <td>
                <form action="<?= BASE_URL ?>/admin/actividades/eliminar" method="POST" onsubmit="return confirm('¿Eliminar?')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$act['id'] ?>">
                  <button type="submit" class="btn-danger-sm">Eliminar</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($actividades)): ?>
              <tr><td colspan="4" class="no-data">No hay actividades.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section id="sec-inscripciones" class="admin-section">
      <h2>Inscripciones Pendientes</h2>
      <div class="table-wrap">
        <table class="tabla-admin">
          <thead><tr><th>Aprendiz</th><th>Doc</th><th>Actividad</th><th>Fecha</th><th>Aprobar Horas</th></tr></thead>
          <tbody>
            <?php foreach ($pendientes as $p): ?>
            <tr>
              <td><?= e($p['nombre'] . ' ' . $p['apellido']) ?></td>
              <td><?= e($p['numero_doc']) ?></td>
              <td><?= e($p['actividad']) ?></td>
              <td><?= e(substr($p['inscrito_en'], 0, 10)) ?></td>
              <td>
                <form action="<?= BASE_URL ?>/admin/horas/aprobar" method="POST" class="form-aprobar">
                  <?= csrf_field() ?>
                  <input type="hidden" name="inscripcion_id" value="<?= (int)$p['id'] ?>">
                  <input type="number" name="horas" min="0.5" max="100" step="0.5" value="2" class="input-horas" required>
                  <button type="submit" class="btn-success-sm">Aprobar</button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($pendientes)): ?>
              <tr><td colspan="5" class="no-data">No hay inscripciones pendientes.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section id="sec-aprendices" class="admin-section">
      <h2>Aprendices Registrados</h2>
      <div class="table-wrap">
        <table class="tabla-admin">
          <thead><tr><th>Nombre</th><th>Doc</th><th>Correo</th><th>Estado</th><th>Acción</th></tr></thead>
          <tbody>
            <?php foreach ($aprendices as $a): ?>
            <tr>
              <td><?= e($a['nombre'] . ' ' . $a['apellido']) ?></td>
              <td><?= e($a['tipo_doc'] . ' ' . $a['numero_doc']) ?></td>
              <td><?= e($a['correo']) ?></td>
              <td><span class="badge badge-<?= $a['activo'] ? 'completada' : 'rechazada' ?>"><?= $a['activo'] ? 'Activo' : 'Inactivo' ?></span></td>
              <td>
                <form action="<?= BASE_URL ?>/admin/aprendices/toggle" method="POST">
                  <?= csrf_field() ?>
                  <input type="hidden" name="id" value="<?= (int)$a['id'] ?>">
                  <button type="submit" class="btn-outline-sm"><?= $a['activo'] ? 'Desactivar' : 'Activar' ?></button>
                </form>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php if (empty($aprendices)): ?>
              <tr><td colspan="5" class="no-data">No hay aprendices.</td></tr>
            <?php endif; ?>
          </tbody>
        </table>
      </div>
    </section>

    <section id="sec-anuncios" class="admin-section">
      <h2>Anuncios</h2>
      <div class="admin-form-card">
        <h3>Nuevo Anuncio</h3>
        <form id="formAnuncio" action="<?= BASE_URL ?>/admin/anuncios/crear" method="POST" novalidate>
          <?= csrf_field() ?>
          <label>Título</label>
          <input type="text" name="titulo" id="anuncio-titulo" maxlength="200" required>
          <span class="field-error" id="err-anuncio-titulo"></span>
          <label>Contenido (opcional)</label>
          <textarea name="contenido" rows="3" maxlength="500"></textarea>
          <button type="submit" class="btn-submit">Publicar</button>
        </form>
      </div>
      <?php foreach ($anuncios as $an): ?>
      <div class="anuncio-admin-card">
        <strong><?= e($an['titulo']) ?></strong>
        <p><?= e($an['contenido'] ?? '') ?></p>
        <form action="<?= BASE_URL ?>/admin/anuncios/eliminar" method="POST">
          <?= csrf_field() ?>
          <input type="hidden" name="id" value="<?= (int)$an['id'] ?>">
          <button type="submit" class="btn-danger-sm">Eliminar</button>
        </form>
      </div>
      <?php endforeach; ?>
    </section>

  </div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.8/css/dataTables.dataTables.min.css">

<!-- jQuery primero -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables JS -->
<script src="https://cdn.datatables.net/2.3.8/js/dataTables.min.js"></script>

<!-- main.js y validation.js DESPUÉS de jQuery -->
<script src="<?= BASE_URL ?>/js/main.js"></script>
<script src="<?= BASE_URL ?>/js/validation.js"></script>
<script>initAdminValidation();</script>

<script>
$(document).ready(function() {
    $('#tablaActividades').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.8/i18n/es-ES.json' },
        pageLength: 10,
        order: [[2, 'desc']]
    });
    $('#tablaInscripciones').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.8/i18n/es-ES.json' },
        pageLength: 10,
        order: [[3, 'asc']]
    });
    $('#tablaAprendices').DataTable({
        language: { url: 'https://cdn.datatables.net/plug-ins/2.3.8/i18n/es-ES.json' },
        pageLength: 10,
        order: [[0, 'asc']]
    });
});
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
