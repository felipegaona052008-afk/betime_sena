<?php
// app/controllers/AuthController.php

require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/UsuarioModel.php';

class AuthController extends Controller {

    private UsuarioModel $model;

    public function __construct() {
        $this->model = new UsuarioModel();
    }

    // ── Páginas públicas ──────────────────────────────────────────────────────

   public function index(): void {
    // Siempre muestra la página de inicio
    // El header ya detecta si hay sesión y muestra el menú correcto
    $this->view('public/index');
}

    public function loginAprendizForm(): void {
        $this->view('auth/login_aprendiz');
    }

    public function loginAdminForm(): void {
        $this->view('auth/login_admin');
    }

    public function registroForm(): void {
        $this->view('auth/registro');
    }

    // ── Acciones POST ─────────────────────────────────────────────────────────

    public function loginAprendiz(): void {
        $this->verifyCsrf();   // Formulario del sitio → sí verifica CSRF

        $tipo_doc   = trim($_POST['tipo_doc']   ?? '');
        $numero_doc = trim($_POST['numero_doc'] ?? '');
        $password   = $_POST['password']        ?? '';

        $tiposValidos = ['CC', 'TI', 'CE'];
        if (!in_array($tipo_doc, $tiposValidos, true)) {
            flash('error', 'Tipo de documento inválido.');
            $this->redirect('/login/aprendiz');
        }

        if (!preg_match('/^\d{5,20}$/', $numero_doc)) {
            flash('error', 'Número de documento inválido.');
            $this->redirect('/login/aprendiz');
        }

        $user = $this->model->findByDoc($tipo_doc, $numero_doc);

        if (!$user || !$this->model->verifyPassword($password, $user['password_hash'])) {
            flash('error', 'Credenciales incorrectas.');
            $this->redirect('/login/aprendiz');
        }

        if ($user['rol'] !== 'aprendiz') {
            flash('error', 'Acceso no autorizado.');
            $this->redirect('/login/aprendiz');
        }

        $this->startUserSession($user);
        $this->redirect('/aprendiz');
    }

    public function loginAdmin(): void {
        $this->verifyCsrf();

        $tipo_doc   = trim($_POST['tipo_doc']   ?? '');
        $numero_doc = trim($_POST['numero_doc'] ?? '');
        $password   = $_POST['password']        ?? '';

        $tiposValidos = ['CC', 'CE'];
        if (!in_array($tipo_doc, $tiposValidos, true)) {
            flash('error', 'Tipo de documento inválido.');
            $this->redirect('/login/admin');
        }

        if (!preg_match('/^\d{5,20}$/', $numero_doc)) {
            flash('error', 'Número de documento inválido.');
            $this->redirect('/login/admin');
        }

        $user = $this->model->findByDoc($tipo_doc, $numero_doc);

        if (!$user || !$this->model->verifyPassword($password, $user['password_hash'])) {
            flash('error', 'Credenciales incorrectas.');
            $this->redirect('/login/admin');
        }

        if ($user['rol'] !== 'admin') {
            flash('error', 'No tienes permisos de administrador.');
            $this->redirect('/login/admin');
        }

        $this->startUserSession($user);
        $this->redirect('/admin');
    }

    public function registro(): void {
        $this->verifyCsrf();

        $nombre     = trim($_POST['nombre']     ?? '');
        $apellido   = trim($_POST['apellido']   ?? '');
        $tipo_doc   = trim($_POST['tipo_doc']   ?? '');
        $numero_doc = trim($_POST['numero_doc'] ?? '');
        $correo     = trim($_POST['correo']     ?? '');
        $password   = $_POST['password']        ?? '';
        $confirmar  = $_POST['confirmar']       ?? '';

        $errors = [];

        if (empty($nombre) || !preg_match('/^[\p{L}\s]{2,80}$/u', $nombre))
            $errors[] = 'Nombre inválido (2–80 letras).';

        if (empty($apellido) || !preg_match('/^[\p{L}\s]{2,80}$/u', $apellido))
            $errors[] = 'Apellido inválido (2–80 letras).';

        if (!in_array($tipo_doc, ['CC', 'TI', 'CE'], true))
            $errors[] = 'Tipo de documento inválido.';

        if (!preg_match('/^\d{5,20}$/', $numero_doc))
            $errors[] = 'Número de documento: solo dígitos, 5–20 caracteres.';

        if (!filter_var($correo, FILTER_VALIDATE_EMAIL) || strlen($correo) > 120)
            $errors[] = 'Correo electrónico inválido.';

        if (!preg_match('/^(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/', $password))
            $errors[] = 'Contraseña débil. Mínimo 8 caracteres, 1 mayúscula, 1 número y 1 carácter especial.';

        if ($password !== $confirmar)
            $errors[] = 'Las contraseñas no coinciden.';

        if ($this->model->existsDoc($numero_doc))
            $errors[] = 'Este número de documento ya está registrado.';

        if ($this->model->existsEmail($correo))
            $errors[] = 'Este correo ya está registrado.';

        if (!empty($errors)) {
            flash('error', implode(' | ', $errors));
            $this->redirect('/registro');
        }

        $this->model->register([
            'nombre'     => $nombre,
            'apellido'   => $apellido,
            'tipo_doc'   => $tipo_doc,
            'numero_doc' => $numero_doc,
            'correo'     => $correo,
            'password'   => $password,
        ]);

        flash('success', '¡Registro exitoso! Ya puedes iniciar sesión.');
        $this->redirect('/login/aprendiz');
    }

    public function logout(): void {
        session_destroy();
        $this->redirect('/');
    }

    // ── Helper privado ────────────────────────────────────────────────────────

    private function startUserSession(array $user): void {
        session_regenerate_id(true);
        $_SESSION['user'] = [
            'id'         => $user['id'],
            'nombre'     => $user['nombre'],
            'apellido'   => $user['apellido'],
            'correo'     => $user['correo'],
            'tipo_doc'   => $user['tipo_doc'],
            'numero_doc' => $user['numero_doc'],
            'rol'        => $user['rol'],
        ];
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
}
