<?php
// app/controllers/PasswordResetController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../config/mail.php';
require_once __DIR__ . '/../models/PasswordResetModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

// Cargar PHPMailer instalado con Composer
// La carpeta vendor/ está en la raíz del proyecto (un nivel arriba de public/)
require_once __DIR__ . '/../../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class PasswordResetController extends Controller {

    private PasswordResetModel $resetModel;
    private UsuarioModel       $usuarioModel;

    public function __construct() {
        $this->resetModel   = new PasswordResetModel();
        $this->usuarioModel = new UsuarioModel();
    }

    // ── PASO 1: Mostrar formulario para pedir el correo ───────────────────────
    public function solicitarForm(): void {
        $this->view('auth/recuperar_password');
    }

    // ── PASO 2: Recibir correo, generar token y enviar email ──────────────────
    public function solicitarEnvio(): void {
        $this->verifyCsrf();

        $correo = trim($_POST['correo'] ?? '');

        // Validar formato
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            flash('error', 'Ingresa un correo electrónico válido.');
            $this->redirect('/recuperar-password');
        }

        // Buscar el usuario — siempre mostrar el mismo mensaje
        // para no revelar si el correo existe o no (seguridad)
        $usuario = $this->usuarioModel->findByEmail($correo);

        if ($usuario) {
            // Solo generamos token si el usuario existe
            $token = $this->resetModel->crearToken($correo);
            $this->enviarCorreo($correo, $usuario['nombre'], $token);
        }

        // Mismo mensaje siempre, independientemente de si existe o no
        flash('success', 'Si ese correo está registrado, recibirás un enlace en los próximos minutos. Revisa también tu carpeta de spam.');
        $this->redirect('/recuperar-password');
    }

    // ── PASO 3: Mostrar formulario de nueva contraseña ───────────────────────
    public function nuevaPasswordForm(): void {
        $token = $_GET['token'] ?? '';

        if (empty($token)) {
            flash('error', 'Token inválido o faltante.');
            $this->redirect('/recuperar-password');
        }

        // Verificar que el token sea válido antes de mostrar el form
        $correo = $this->resetModel->verificarToken($token);
        if (!$correo) {
            flash('error', 'El enlace ha expirado o ya fue usado. Solicita uno nuevo.');
            $this->redirect('/recuperar-password');
        }

        $this->view('auth/nueva_password', ['token' => $token]);
    }

    // ── PASO 4: Guardar la nueva contraseña ───────────────────────────────────
    public function guardarPassword(): void {
        $this->verifyCsrf();

        $token    = $_POST['token']    ?? '';
        $password = $_POST['password'] ?? '';
        $confirmar = $_POST['confirmar'] ?? '';

        // Validar token
        if (empty($token)) {
            flash('error', 'Token inválido.');
            $this->redirect('/recuperar-password');
        }

        // Verificar token en BD
        $correo = $this->resetModel->verificarToken($token);
        if (!$correo) {
            flash('error', 'El enlace ha expirado o ya fue usado. Solicita uno nuevo.');
            $this->redirect('/recuperar-password');
        }

        // Validar contraseña
        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password)) {
            flash('error', 'Contraseña débil. Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.');
            $this->redirect('/nueva-password?token=' . urlencode($token));
        }

        if ($password !== $confirmar) {
            flash('error', 'Las contraseñas no coinciden.');
            $this->redirect('/nueva-password?token=' . urlencode($token));
        }

        // Buscar usuario por correo
        $usuario = $this->usuarioModel->findByEmail($correo);
        if (!$usuario) {
            flash('error', 'Usuario no encontrado.');
            $this->redirect('/recuperar-password');
        }

        // Cambiar contraseña con bcrypt
        $this->usuarioModel->changePassword($usuario['id'], $password);

        // Invalidar el token (eliminarlo para que no se pueda usar de nuevo)
        $this->resetModel->eliminarToken($token);

        flash('success', '¡Contraseña actualizada correctamente! Ya puedes iniciar sesión.');

        // Redirigir según el rol del usuario
        $ruta = $usuario['rol'] === 'admin' ? '/login/admin' : '/login/aprendiz';
        $this->redirect($ruta);
    }

    // ── HELPER PRIVADO: Enviar el correo con PHPMailer ────────────────────────
    private function enviarCorreo(string $correo, string $nombre, string $token): void {
        $enlace = BASE_URL . '/nueva-password?token=' . urlencode($token);

        $mail = new PHPMailer(true); // true activa las excepciones

        try {
            // Configuración del servidor SMTP
            $mail->isSMTP();
            $mail->Host       = MAIL_HOST;
            $mail->SMTPAuth   = true;
            $mail->Username   = MAIL_USERNAME;
            $mail->Password   = MAIL_PASSWORD;
            $mail->SMTPSecure = MAIL_ENCRYPTION === 'ssl' ? PHPMailer::ENCRYPTION_SMTPS : PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = MAIL_PORT;
            $mail->CharSet    = 'UTF-8';

            // Remitente y destinatario
            $mail->setFrom(MAIL_FROM, MAIL_FROM_NAME);
            $mail->addAddress($correo, $nombre);

            // Contenido del correo (HTML)
            $mail->isHTML(true);
            $mail->Subject = 'Recuperación de contraseña — BeTimeSENA';
            $mail->Body    = $this->plantillaCorreo($nombre, $enlace);
            $mail->AltBody = "Hola $nombre,\n\nPara restablecer tu contraseña entra a este enlace (válido por " . RESET_TOKEN_EXPIRY . " minutos):\n$enlace\n\nSi no solicitaste esto, ignora este correo.\n\nBeTimeSENA";

            $mail->send();

        } catch (Exception $e) {
            // Loguear el error pero NO mostrarlo al usuario (seguridad)
            error_log('[MAIL ERROR] ' . $mail->ErrorInfo);
        }
    }

    // ── Plantilla HTML del correo ─────────────────────────────────────────────
    private function plantillaCorreo(string $nombre, string $enlace): string {
        $expiry  = RESET_TOKEN_EXPIRY;
        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head><meta charset="UTF-8"></head>
        <body style="margin:0;padding:0;background:#080b14;font-family:'Segoe UI',Arial,sans-serif;">
          <div style="max-width:520px;margin:40px auto;background:#0d1117;border:1px solid #1e293b;border-radius:16px;overflow:hidden;">
            <div style="background:linear-gradient(135deg,#00c864,#00ff88);padding:32px;text-align:center;">
              <h1 style="margin:0;color:#000;font-size:1.6rem;font-weight:900;letter-spacing:2px;">BETIMESENA</h1>
              <p style="margin:8px 0 0;color:#000;opacity:.7;font-size:13px;">Plataforma de Bienestar SENA</p>
            </div>
            <div style="padding:36px 32px;">
              <h2 style="color:#f1f5f9;font-size:1.2rem;margin:0 0 12px;">Hola, {$nombre} 👋</h2>
              <p style="color:#94a3b8;font-size:14px;line-height:1.7;margin-bottom:28px;">
                Recibimos una solicitud para restablecer la contraseña de tu cuenta en BeTimeSENA.
                Haz clic en el botón de abajo para crear una nueva contraseña.
                Este enlace es válido por <strong style="color:#00ff88;">{$expiry} minutos</strong>.
              </p>
              <div style="text-align:center;margin:28px 0;">
                <a href="{$enlace}"
                   style="display:inline-block;padding:14px 36px;background:linear-gradient(135deg,#00c864,#00ff88);
                          color:#000;font-weight:700;font-size:15px;text-decoration:none;
                          border-radius:50px;letter-spacing:.5px;">
                  Restablecer contraseña
                </a>
              </div>
              <p style="color:#64748b;font-size:12px;line-height:1.6;margin-top:24px;border-top:1px solid #1e293b;padding-top:20px;">
                Si no solicitaste este cambio, puedes ignorar este correo. Tu contraseña no cambiará.<br><br>
                Si el botón no funciona, copia y pega este enlace en tu navegador:<br>
                <a href="{$enlace}" style="color:#00c864;word-break:break-all;">{$enlace}</a>
              </p>
            </div>
            <div style="background:#080b14;padding:16px;text-align:center;">
              <p style="color:#475569;font-size:11px;margin:0;">© 2026 BeTimeSENA — Todos los derechos reservados</p>
            </div>
          </div>
        </body>
        </html>
        HTML;
    }
}
