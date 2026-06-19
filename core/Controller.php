<?php
// core/Controller.php

abstract class Controller {

    protected function view(string $viewPath, array $data = []): void {
        extract($data, EXTR_SKIP);
        $file = __DIR__ . '/../app/views/' . $viewPath . '.php';
        if (!file_exists($file)) {
            throw new RuntimeException("Vista no encontrada: {$viewPath}");
        }
        require $file;
    }

    // Redirect usando BASE_URL automáticamente
    protected function redirect(string $path): void {
        header('Location: ' . BASE_URL . $path);
        exit;
    }

    protected function json(array $data, int $status = 200): void {
        http_response_code($status);
        header('Content-Type: application/json; charset=utf-8');
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        exit;
    }

    protected function requireRole(string $role): void {
        if (!isset($_SESSION['user']) || $_SESSION['user']['rol'] !== $role) {
            $this->redirect('/');
        }
    }

    protected function verifyCsrf(): void {
        $token = $_POST['_csrf'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? '');
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(403);
            die(json_encode(['error' => 'Token CSRF inválido.']));
        }
    }

    protected function sanitize(string $value): string {
        return htmlspecialchars(trim($value), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }
}
