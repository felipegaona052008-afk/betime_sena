<?php
/**
 * BeTimeSENA – Punto de entrada único
 * Funciona sin mod_rewrite ni .htaccess
 */

declare(strict_types=1);

session_start();

// Composer autoload — carga PHPMailer y cualquier librería instalada
// vendor/ está en la raíz del proyecto, un nivel arriba de public/
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
}

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../core/helpers.php';
require_once __DIR__ . '/../core/Router.php';
require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../core/Model.php';

// ── RUTAS ────────────────────────────────────────────────────────────────────
$router = new Router();

// Página pública de inicio
$router->get('/',                            ['AuthController', 'index']);

// Auth – formularios
$router->get('/login/aprendiz',              ['AuthController', 'loginAprendizForm']);
$router->get('/login/admin',                 ['AuthController', 'loginAdminForm']);
$router->get('/registro',                    ['AuthController', 'registroForm']);

// Auth – acciones POST
$router->post('/login/aprendiz',             ['AuthController', 'loginAprendiz']);
$router->post('/login/admin',                ['AuthController', 'loginAdmin']);
$router->post('/registro',                   ['AuthController', 'registro']);
$router->get('/logout',                      ['AuthController', 'logout']);

// ── RECUPERACIÓN DE CONTRASEÑA ────────────────────────────────────────────────
// Paso 1: Formulario para ingresar correo
$router->get('/recuperar-password',          ['PasswordResetController', 'solicitarForm']);
// Paso 2: Procesar correo y enviar email
$router->post('/recuperar-password',         ['PasswordResetController', 'solicitarEnvio']);
// Paso 3: Formulario para ingresar nueva contraseña (llega desde el link del email)
$router->get('/nueva-password',              ['PasswordResetController', 'nuevaPasswordForm']);
// Paso 4: Guardar la nueva contraseña
$router->post('/nueva-password',             ['PasswordResetController', 'guardarPassword']);

// Aprendiz
$router->get('/aprendiz',                    ['AprendizController', 'dashboard']);
$router->get('/aprendiz/eventos',            ['AprendizController', 'eventos']);
$router->get('/aprendiz/calendario',         ['AprendizController', 'calendario']);
$router->get('/aprendiz/mis-horas',          ['AprendizController', 'misHoras']);
$router->get('/aprendiz/perfil',             ['AprendizController', 'perfil']);
$router->post('/aprendiz/inscribir',         ['AprendizController', 'inscribir']);
$router->post('/aprendiz/perfil/actualizar', ['AprendizController', 'actualizarPerfil']);

// Admin
$router->get('/admin',                       ['AdminController', 'dashboard']);
$router->post('/admin/actividades/crear',    ['AdminController', 'crearActividad']);
$router->post('/admin/actividades/eliminar', ['AdminController', 'eliminarActividad']);
$router->post('/admin/horas/aprobar',        ['AdminController', 'aprobarHoras']);
$router->post('/admin/aprendices/toggle',    ['AdminController', 'toggleAprendiz']);
$router->post('/admin/anuncios/crear',       ['AdminController', 'crearAnuncio']);
$router->post('/admin/anuncios/eliminar',    ['AdminController', 'eliminarAnuncio']);

// Chatbot
$router->post('/chatbot/webhook',            ['ChatbotController', 'webhook']);

$router->dispatch();
