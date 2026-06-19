<?php $pageTitle = '404'; require_once __DIR__ . '/layouts/header.php'; ?>

<section class="hero" style="min-height:60vh">
  <h1>404</h1>
  <p>La página que buscas no existe.</p>
  <a href="<?= BASE_URL ?>/" class="btn-primary" style="margin-top:1.5rem">← Inicio</a>
</section>

<?php require_once __DIR__ . '/layouts/footer.php'; ?>
