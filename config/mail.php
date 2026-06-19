<?php
// config/mail.php
// ── Configuración de PHPMailer para recuperación de contraseña ──────────────
// IMPORTANTE: Cambia estos valores con los datos reales de tu correo
// Para Gmail necesitas activar "Contraseñas de aplicación" en tu cuenta Google

define('MAIL_HOST',       'smtp.gmail.com');   // Servidor SMTP
define('MAIL_PORT',       587);                // Puerto SMTP (587 = TLS, 465 = SSL)
define('MAIL_USERNAME',   'betimesena@gmail.com');  // ← CAMBIA ESTO
define('MAIL_PASSWORD',   'axtdnrqnnecodqxh'); // ← CAMBIA ESTO (contraseña de app Gmail)
define('MAIL_FROM',       'tucorreo@gmail.com');  // ← CAMBIA ESTO
define('MAIL_FROM_NAME',  'BeTimeSENA');
define('MAIL_ENCRYPTION', 'tls');              // 'tls' o 'ssl'

// Tiempo en minutos que dura válido el token de recuperación
define('RESET_TOKEN_EXPIRY', 6); // 60 minutos
