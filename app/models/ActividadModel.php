<?php
// app/models/ActividadModel.php

require_once __DIR__ . '/../../core/Model.php';

class ActividadModel extends Model {

    public function getAll(): array {
        return $this->fetchAll(
            'SELECT a.*, u.nombre AS creador_nombre, u.apellido AS creador_apellido
             FROM actividades a
             LEFT JOIN usuarios u ON u.id = a.creado_por
             WHERE a.activo = 1
             ORDER BY a.fecha_inicio DESC'
        );
    }

    public function getById(int $id): array|false {
        return $this->fetchOne(
            'SELECT * FROM actividades WHERE id = :id AND activo = 1 LIMIT 1',
            [':id' => $id]
        );
    }

    public function create(array $data): int {
        $this->execute(
            'INSERT INTO actividades (titulo, descripcion, fecha_inicio, fecha_fin, lugar, imagen, tipo, creado_por)
             VALUES (:titulo, :desc, :fi, :ff, :lugar, :imagen, :tipo, :admin)',
            [
                ':titulo' => $data['titulo'],
                ':desc'   => $data['descripcion'] ?? null,
                ':fi'     => $data['fecha_inicio'],
                ':ff'     => $data['fecha_fin'] ?? null,
                ':lugar'  => $data['lugar'] ?? null,
                ':imagen' => $data['imagen'] ?? null,
                ':tipo'   => $data['tipo'],
                ':admin'  => $data['creado_por'],
            ]
        );
        return (int)$this->lastId();
    }

    public function update(int $id, array $data): int {
        return $this->execute(
            'UPDATE actividades
             SET titulo=:titulo, descripcion=:desc, fecha_inicio=:fi,
                 fecha_fin=:ff, lugar=:lugar, tipo=:tipo
             WHERE id=:id',
            [
                ':titulo' => $data['titulo'],
                ':desc'   => $data['descripcion'] ?? null,
                ':fi'     => $data['fecha_inicio'],
                ':ff'     => $data['fecha_fin'] ?? null,
                ':lugar'  => $data['lugar'] ?? null,
                ':tipo'   => $data['tipo'],
                ':id'     => $id,
            ]
        );
    }

    public function softDelete(int $id): int {
        return $this->execute(
            'UPDATE actividades SET activo = 0 WHERE id = :id',
            [':id' => $id]
        );
    }

    public function getUpcoming(): array {
        return $this->fetchAll(
            'SELECT id, titulo, fecha_inicio, tipo FROM actividades
             WHERE activo = 1 AND fecha_inicio >= CURDATE()
             ORDER BY fecha_inicio ASC LIMIT 30'
        );
    }
}
