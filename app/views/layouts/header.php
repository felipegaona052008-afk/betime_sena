<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= isset($pageTitle) ? e($pageTitle) . ' | BeTimeSENA' : 'BeTimeSENA' ?></title>
  <link rel="stylesheet" href="<?= BASE_URL ?>/css/main.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<header class="header">
  <div class="menu-icon" id="menuToggle" aria-label="Menú">☰</div>
  <div class="logo">
    <img src="<?= BASE_URL ?>/imgs/logo.png" alt="BeTimeSENA logo" onerror="this.style.display='none'">
  </div>
  <h1 class="titulo">BETIMESENA</h1>

  <?php if (auth()): $u = current_user(); ?>
  <div class="perfil">
    <div class="perfil-icono" id="perfilToggle" aria-label="Mi perfil">
      <i class="fas fa-user-circle"></i>
    </div>
    <div class="perfil-menu" id="perfilMenu">
      <span class="perfil-nombre"><?= e($u['nombre'] . ' ' . $u['apellido']) ?></span>
      <?php if ($u['rol'] === 'admin'): ?>
        <a href="<?= BASE_URL ?>/admin">Panel Admin</a>
        <a href="<?= BASE_URL ?>/logout">Cerrar sesión</a>
      <?php else: ?>
        <a href="<?= BASE_URL ?>/aprendiz/perfil">Mi Perfil</a>
        <a href="<?= BASE_URL ?>/aprendiz/mis-horas">Mis Horas</a>
        <a href="<?= BASE_URL ?>/logout">Cerrar sesión</a>
      <?php endif; ?>
    </div>
  </div>
  <?php endif; ?>
</header>

<nav class="menu" id="menu" aria-label="Navegación principal">
  <?php if (auth() && current_user()['rol'] === 'admin'): ?>
    <a href="<?= BASE_URL ?>/admin"><i class="fas fa-tachometer-alt"></i> Panel Admin</a>
    <a href="<?= BASE_URL ?>/aprendiz/calendario"><i class="fas fa-calendar"></i> Calendario</a>
    <a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
  <?php elseif (auth()): ?>
    <a href="<?= BASE_URL ?>/aprendiz"><i class="fas fa-home"></i> Inicio</a>
    <a href="<?= BASE_URL ?>/aprendiz/mis-horas"><i class="fas fa-clock"></i> Horas de Bienestar</a>
    <a href="<?= BASE_URL ?>/aprendiz/eventos"><i class="fas fa-star"></i> Eventos</a>
    <a href="<?= BASE_URL ?>/aprendiz/calendario"><i class="fas fa-calendar"></i> Calendario</a>
    <a href="<?= BASE_URL ?>/aprendiz/perfil"><i class="fas fa-user"></i> Mi Perfil</a>
    <a href="<?= BASE_URL ?>/logout"><i class="fas fa-sign-out-alt"></i> Cerrar sesión</a>
  <?php else: ?>
    <a href="<?= BASE_URL ?>/"><i class="fas fa-home"></i> Inicio</a>
    <a href="<?= BASE_URL ?>/aprendiz/eventos"><i class="fas fa-star"></i> Eventos</a>
    <a href="<?= BASE_URL ?>/aprendiz/calendario"><i class="fas fa-calendar"></i> Calendario</a>
    <a href="<?= BASE_URL ?>/login/admin"><i class="fas fa-lock"></i> Administración</a>
  <?php endif; ?>
</nav>