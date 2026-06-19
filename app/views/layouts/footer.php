<?php $err = flash('error'); $ok = flash('success'); ?>
<?php if ($err): ?>
  <div class="alert alert-error" role="alert"><?= e($err) ?></div>
<?php endif; ?>
<?php if ($ok): ?>
  <div class="alert alert-success" role="alert"><?= e($ok) ?></div>
<?php endif; ?>

<footer class="footer">
  <div class="footer-container">
    <div class="footer-brand">
      <h2>BeTimeSENA</h2>
      <p>Transformando bienestar y educación digital.</p>
    </div>
    <div class="footer-links">
      <h3>Secciones</h3>
      <ul>
        <li><a href="<?= BASE_URL ?>/">Inicio</a></li>
        <li><a href="<?= BASE_URL ?>/aprendiz/eventos">Eventos</a></li>
        <li><a href="<?= BASE_URL ?>/aprendiz/calendario">Calendario</a></li>
      </ul>
    </div>
    <div class="footer-social">
      <h3>Síguenos</h3>
      <div class="social-icons">
        <a href="https://www.facebook.com/SENA/?locale=es_LA" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
        <a href="https://www.instagram.com/senacomunica/?hl=es-la" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
        <a href="https://x.com/SENAComunica" aria-label="Twitter/X"><i class="fab fa-x-twitter"></i></a>
        <a href="#" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
      </div>
    </div>
  </div>
  <div class="footer-bottom">
    <p>© 2026 BeTimeSENA | Todos los derechos reservados</p>
  </div>
</footer>


<script src="https://cdn.botpress.cloud/webchat/v3.6/inject.js"></script>
<script src="https://files.bpcontent.cloud/2026/05/14/17/20260514174127-E5E38XS8.js" defer></script>
    

<script src="<?= BASE_URL ?>/js/main.js"></script>
</body>
</html>
