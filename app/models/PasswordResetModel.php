<?php
// app/models/PasswordResetModel.php

require_once __DIR__ . '/../../core/Model.php';

class PasswordResetModel extends Model {

    public function crearToken(string $correo): string {
        $token     = bin2hex(random_bytes(32));
        $tokenHash = hash('sha256', $token);
        $expira    = date('Y-m-d H:i:s', time() + (RESET_TOKEN_EXPIRY * 60));

        $this->execute(
            'UPDATE usuarios
             SET reset_token_hash = :hash,
                 reset_expira_en  = :expira
             WHERE correo = :correo',
            [':hash' => $tokenHash, ':expira' => $expira, ':correo' => $correo]
        );

        return $token;
    }

    public function verificarToken(string $token): string|false {
        $tokenHash = hash('sha256', $token);

        $row = $this->fetchOne(
            'SELECT correo, reset_expira_en
             FROM usuarios
             WHERE reset_token_hash = :hash AND activo = 1 LIMIT 1',
            [':hash' => $tokenHash]
        );

        if (!$row) return false;

        if (strtotime($row['reset_expira_en']) < time()) {
            $this->limpiarToken($row['correo']);
            return false;
        }

        return $row['correo'];
    }

    public function eliminarToken(string $token): void {
        $tokenHash = hash('sha256', $token);
        $this->execute(
            'UPDATE usuarios
             SET reset_token_hash = NULL,
                 reset_expira_en  = NULL
             WHERE reset_token_hash = :hash',
            [':hash' => $tokenHash]
        );
    }

    private function limpiarToken(string $correo): void {
        $this->execute(
            'UPDATE usuarios
             SET reset_token_hash = NULL,
                 reset_expira_en  = NULL
             WHERE correo = :correo',
            [':correo' => $correo]
        );
    }
}