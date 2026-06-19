<?php
// app/controllers/AdminController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/ActividadModel.php';
require_once __DIR__ . '/../models/InscripcionModel.php';
require_once __DIR__ . '/../models/AnuncioModel.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AdminController extends Controller {

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
        $this->requireRole('admin');
        $user        = current_user();
        $actividades = $this->actModel->getAll();
        $aprendices  = $this->usuarioModel->listAprendices();
        $pendientes  = $this->inscModel->getAllPendientes();
        $anuncios    = $this->anuncioModel->getActivos();
        $this->view('admin/dashboard', compact('user', 'actividades', 'aprendices', 'pendientes', 'anuncios'));
    }

    // ── Actividades ───────────────────────────────────────────────────────────

    public function crearActividad(): void {
        $this->requireRole('admin');
        $this->verifyCsrf();

        $titulo      = trim($_POST['titulo']       ?? '');
        $descripcion = trim($_POST['descripcion']  ?? '');
        $fecha_ini   = trim($_POST['fecha_inicio'] ?? '');
        $fecha_fin   = trim($_POST['fecha_fin']    ?? '') ?: null;
        $lugar       = trim($_POST['lugar']        ?? '') ?: null;
        $tipo        = trim($_POST['tipo']         ?? '');

        $errors = [];
        if (empty($titulo) || strlen($titulo) > 150) $errors[] = 'Título requerido (máx. 150 caracteres).';
        if (empty($fecha_ini) || !strtotime($fecha_ini)) $errors[] = 'Fecha de inicio inválida.';
        $tiposValidos = ['deportiva', 'cultural', 'academica', 'recreativa', 'otra'];
        if (!in_array($tipo, $tiposValidos, true)) $errors[] = 'Tipo de actividad inválido.';

        if (!empty($errors)) {
            flash('error', implode(' | ', $errors));
            $this->redirect('/admin');
        }

        $user = current_user();
        $this->actModel->create([
            'titulo'       => $titulo,
            'descripcion'  => $descripcion,
            'fecha_inicio' => $fecha_ini,
            'fecha_fin'    => $fecha_fin,
            'lugar'        => $lugar,
            'tipo'         => $tipo,
            'creado_por'   => $user['id'],
            'imagen'       => null,
        ]);

        flash('success', 'Actividad creada correctamente.');
        $this->redirect('/admin');
    }

    public function eliminarActividad(): void {
        $this->requireRole('admin');
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) $this->actModel->softDelete($id);

        flash('success', 'Actividad eliminada.');
        $this->redirect('/admin');
    }

    // ── Inscripciones ─────────────────────────────────────────────────────────

    public function aprobarHoras(): void {
        $this->requireRole('admin');
        $this->verifyCsrf();

        $inscripcion_id = (int)($_POST['inscripcion_id'] ?? 0);
        $horas          = (float)($_POST['horas'] ?? 0);

        if ($inscripcion_id <= 0 || $horas <= 0 || $horas > 100) {
            flash('error', 'Datos de aprobación inválidos.');
            $this->redirect('/admin');
        }

        $this->inscModel->aprobar($inscripcion_id, $horas);
        flash('success', 'Horas aprobadas correctamente.');
        $this->redirect('/admin');
    }

    // ── Aprendices ────────────────────────────────────────────────────────────

    public function toggleAprendiz(): void {
        $this->requireRole('admin');
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) $this->usuarioModel->toggleActivo($id);

        flash('success', 'Estado del aprendiz actualizado.');
        $this->redirect('/admin');
    }

    // ── Anuncios ──────────────────────────────────────────────────────────────

    public function crearAnuncio(): void {
        $this->requireRole('admin');
        $this->verifyCsrf();

        $titulo    = trim($_POST['titulo']    ?? '');
        $contenido = trim($_POST['contenido'] ?? '');

        if (empty($titulo) || strlen($titulo) > 200) {
            flash('error', 'Título del anuncio inválido.');
            $this->redirect('/admin');
        }

        $this->anuncioModel->create($titulo, $contenido);
        flash('success', 'Anuncio publicado.');
        $this->redirect('/admin');
    }

    public function eliminarAnuncio(): void {
        $this->requireRole('admin');
        $this->verifyCsrf();

        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) $this->anuncioModel->delete($id);

        flash('success', 'Anuncio eliminado.');
        $this->redirect('/admin');
    }
}
