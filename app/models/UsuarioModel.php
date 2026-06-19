<?php
// app/models/UsuarioModel.php

require_once __DIR__ . '/../../core/Model.php';

class UsuarioModel extends Model {

    public function findByDoc(string $tipo_doc, string $numero_doc): array|false {
        return $this->fetchOne(
            'SELECT * FROM usuarios WHERE tipo_doc = :tipo AND numero_doc = :num AND activo = 1 LIMIT 1',
            [':tipo' => $tipo_doc, ':num' => $numero_doc]
        );
    }

    public function findById(int $id): array|false {
        return $this->fetchOne(
            'SELECT id, nombre, apellido, tipo_doc, numero_doc, correo, rol, creado_en FROM usuarios WHERE id = :id LIMIT 1',
            [':id' => $id]
        );
    }

    public function findByEmail(string $correo): array|false {
        return $this->fetchOne(
            'SELECT * FROM usuarios WHERE correo = :correo AND activo = 1 LIMIT 1',
            [':correo' => $correo]
        );
    }

    public function existsDoc(string $numero_doc): bool {
        $row = $this->fetchOne(
            'SELECT id FROM usuarios WHERE numero_doc = :num LIMIT 1',
            [':num' => $numero_doc]
        );
        return $row !== false;
    }

    public function existsEmail(string $correo): bool {
        $row = $this->fetchOne(
            'SELECT id FROM usuarios WHERE correo = :correo LIMIT 1',
            [':correo' => $correo]
        );
        return $row !== false;
    }

    public function register(array $data): int {
        $hash = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]);

        $this->execute(
            'INSERT INTO usuarios (nombre, apellido, tipo_doc, numero_doc, correo, password_hash, rol)
             VALUES (:nombre, :apellido, :tipo, :num, :correo, :hash, "aprendiz")',
            [
                ':nombre'   => $data['nombre'],
                ':apellido' => $data['apellido'],
                ':tipo'     => $data['tipo_doc'],
                ':num'      => $data['numero_doc'],
                ':correo'   => $data['correo'],
                ':hash'     => $hash,
            ]
        );
        return (int)$this->lastId();
    }

    public function verifyPassword(string $plain, string $hash): bool {
        return password_verify($plain, $hash);
    }

    public function updateProfile(int $id, array $data): int {
        return $this->execute(
            'UPDATE usuarios SET nombre=:nombre, apellido=:apellido, correo=:correo WHERE id=:id',
            [':nombre' => $data['nombre'], ':apellido' => $data['apellido'],
             ':correo' => $data['correo'], ':id' => $id]
        );
    }

    public function changePassword(int $id, string $newPassword): int {
        $hash = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12]);
        return $this->execute(
            'UPDATE usuarios SET password_hash=:hash WHERE id=:id',
            [':hash' => $hash, ':id' => $id]
        );
    }

    public function listAprendices(): array {
        return $this->fetchAll(
            "SELECT id, nombre, apellido, tipo_doc, numero_doc, correo, activo, creado_en
             FROM usuarios WHERE rol = 'aprendiz' ORDER BY apellido, nombre"
        );
    }

    public function toggleActivo(int $id): int {
        return $this->execute(
            'UPDATE usuarios SET activo = IF(activo=1, 0, 1) WHERE id=:id',
            [':id' => $id]
        );
    }
}
