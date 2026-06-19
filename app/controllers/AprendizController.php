<?php
// app/controllers/AprendizController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/ActividadModel.php';
require_once __DIR__ . '/../models/InscripcionModel.php';
require_once __DIR__ . '/../models/AnuncioModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AprendizController extends Controller {

    private ActividadModel   $actModel;
    private InscripcionModel $inscModel;
    private AnuncioModel     $anuncioModel;
    private UsuarioModel     $usuarioModel;

    public function __construct() {
        $this->actModel     = new ActividadModel();
        $this->inscModel    = new InscripcionModel();
        $this->anuncioModel = new AnuncioModel();
        $this->usuarioModel = new UsuarioModel();
    }

    public function dashboard(): void {
        $this->requireRole('aprendiz');
        $user        = current_user();
        $actividades = $this->actModel->getAll();
        $anuncios    = $this->anuncioModel->getActivos();
        $totalHoras  = $this->inscModel->getTotalHoras($user['id']);
        $this->view('aprendiz/dashboard', compact('user', 'actividades', 'anuncios', 'totalHoras'));
    }

    public function eventos(): void {
        $this->requireRole('aprendiz');
        $user        = current_user();
        $actividades = $this->actModel->getAll();
        $anuncios    = $this->anuncioModel->getActivos();
        $this->view('aprendiz/eventos', compact('user', 'actividades', 'anuncios'));
    }

    public function calendario(): void {
        $this->requireRole('aprendiz');
        $user        = current_user();
        $actividades = $this->actModel->getUpcoming();
        $anuncios    = $this->anuncioModel->getActivos();
        $this->view('aprendiz/calendario', compact('user', 'actividades', 'anuncios'));
    }

    public function inscribir(): void {
        $this->requireRole('aprendiz');
        // ── SIN verifyCsrf() aquí ─────────────────────────────────────────────
        // El chatbot llama a este endpoint y no tiene token CSRF.
        // La seguridad la da requireRole() (sesión activa) + validación del ID.

        $user         = current_user();
        $actividad_id = (int)($_POST['actividad_id'] ?? 0);

        if ($actividad_id <= 0) {
            $this->json(['ok' => false, 'msg' => 'Actividad inválida.'], 400);
        }

        $actividad = $this->actModel->getById($actividad_id);
        if (!$actividad) {
            $this->json(['ok' => false, 'msg' => 'Actividad no encontrada.'], 404);
        }

        if ($this->inscModel->estaInscrito($user['id'], $actividad_id)) {
            $this->json(['ok' => false, 'msg' => 'Ya estás inscrito en esta actividad.'], 409);
        }

        $this->inscModel->inscribir($user['id'], $actividad_id);
        $this->json(['ok' => true, 'msg' => '¡Inscripción exitosa!']);
    }

    public function misHoras(): void {
        $this->requireRole('aprendiz');
        $user       = current_user();
        $horas      = $this->inscModel->getMisHoras($user['id']);
        $totalHoras = $this->inscModel->getTotalHoras($user['id']);
        $this->view('aprendiz/mis_horas', compact('user', 'horas', 'totalHoras'));
    }

    public function perfil(): void {
        $this->requireRole('aprendiz');
        $user = current_user();
        $this->view('aprendiz/perfil', compact('user'));
    }

    public function actualizarPerfil(): void {
        $this->requireRole('aprendiz');
        $this->verifyCsrf();

        $user     = current_user();
        $nombre   = trim($_POST['nombre']   ?? '');
        $apellido = trim($_POST['apellido'] ?? '');
        $correo   = trim($_POST['correo']   ?? '');

        $errors = [];
        if (!preg_match('/^[\p{L}\s]{2,80}$/u', $nombre))   $errors[] = 'Nombre inválido.';
        if (!preg_match('/^[\p{L}\s]{2,80}$/u', $apellido)) $errors[] = 'Apellido inválido.';
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL))     $errors[] = 'Correo inválido.';

        if (!empty($errors)) {
            flash('error', implode(' | ', $errors));
            $this->redirect('/aprendiz/perfil');
        }

        $this->usuarioModel->updateProfile($user['id'], compact('nombre', 'apellido', 'correo'));

        $_SESSION['user']['nombre']   = $nombre;
        $_SESSION['user']['apellido'] = $apellido;
        $_SESSION['user']['correo']   = $correo;

        flash('success', 'Perfil actualizado correctamente.');
        $this->redirect('/aprendiz/perfil');
    }
}
