<?php $pageTitle = 'Calendario'; require_once __DIR__ . '/../layouts/header.php'; ?>

<div class="cal-layout">

  <aside class="cal-aside">
    <h2>Actividades</h2>
    <?php foreach ($actividades as $act): ?>
      <div class="cal-item cal-<?= e($act['tipo']) ?>">
        <strong><?= e($act['titulo']) ?></strong>
        <span><?= e($act['fecha_inicio']) ?></span>
      </div>
    <?php endforeach; ?>
  </aside>

  <div class="cal-main">
    <div class="cal-nav">
      <button id="prevMes">&#8592;</button>
      <h2 id="calTitulo"></h2>
      <button id="nextMes">&#8594;</button>
    </div>
    <div class="calendar-grid-header">
      <div>Dom</div><div>Lun</div><div>Mar</div>
      <div>Mié</div><div>Jue</div><div>Vie</div><div>Sáb</div>
    </div>
    <div class="calendar-grid" id="calGrid"></div>
    <a href="<?= BASE_URL ?>/aprendiz" class="btn-volver">← Volver</a>
  </div>

  <aside class="cal-aside">
    <h2>Anuncios</h2>
    <?php foreach ($anuncios as $an): ?>
      <div class="anuncio"><strong><?= e($an['titulo']) ?></strong></div>
    <?php endforeach; ?>
  </aside>

</div>

<script>
const actividadesCal = <?= json_encode(array_map(fn($a) => [
  'titulo' => $a['titulo'],
  'fecha'  => $a['fecha_inicio'],
  'tipo'   => $a['tipo'],
], $actividades), JSON_UNESCAPED_UNICODE) ?>;

let calYear  = new Date().getFullYear();
let calMonth = new Date().getMonth();

function renderCal() {
  const meses = ['Enero','Febrero','Marzo','Abril','Mayo','Junio',
                 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];
  document.getElementById('calTitulo').textContent = meses[calMonth] + ' ' + calYear;

  const grid = document.getElementById('calGrid');
  grid.innerHTML = '';

  const firstDay    = new Date(calYear, calMonth, 1).getDay();
  const daysInMonth = new Date(calYear, calMonth + 1, 0).getDate();

  for (let i = 0; i < firstDay; i++) {
    const empty = document.createElement('div');
    empty.className = 'cal-cell empty';
    grid.appendChild(empty);
  }

  for (let d = 1; d <= daysInMonth; d++) {
    const cell = document.createElement('div');
    cell.className = 'cal-cell';
    cell.textContent = d;
    const dateStr = calYear + '-' + String(calMonth+1).padStart(2,'0') + '-' + String(d).padStart(2,'0');
    const eventos = actividadesCal.filter(function(a){ return a.fecha === dateStr; });
    if (eventos.length) {
      cell.classList.add('has-event');
      cell.title = eventos.map(function(e){ return e.titulo; }).join(', ');
    }
    grid.appendChild(cell);
  }
}

document.getElementById('prevMes').addEventListener('click', function() {
  calMonth--; if (calMonth < 0) { calMonth = 11; calYear--; } renderCal();
});
document.getElementById('nextMes').addEventListener('click', function() {
  calMonth++; if (calMonth > 11) { calMonth = 0; calYear++; } renderCal();
});

renderCal();
</script>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
