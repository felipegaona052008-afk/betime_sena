<?php
// core/helpers.php

/**
 * Genera (o recupera) un token CSRF para la sesión actual.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Renderiza un campo CSRF oculto.
 */
function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . csrf_token() . '">';
}

/**
 * ¿Hay un usuario logueado?
 */
function auth(): bool {
    return isset($_SESSION['user']);
}

/**
 * Datos del usuario logueado.
 */
function current_user(): array|null {
    return $_SESSION['user'] ?? null;
}

/**
 * Escapa output para HTML (evita XSS).
 */
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Mensajes flash de sesión.
 */
function flash(string $key, string $message = ''): string {
    if ($message !== '') {
        $_SESSION['flash'][$key] = $message;
        return '';
    }
    $msg = $_SESSION['flash'][$key] ?? '';
    unset($_SESSION['flash'][$key]);
    return $msg;
}
