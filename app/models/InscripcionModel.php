<?php
// app/models/InscripcionModel.php

require_once __DIR__ . '/../../core/Model.php';

class InscripcionModel extends Model {

    public function inscribir(int $usuario_id, int $actividad_id): bool {
        try {
            $this->execute(
                'INSERT INTO inscripciones (usuario_id, actividad_id) VALUES (:u, :a)',
                [':u' => $usuario_id, ':a' => $actividad_id]
            );
            return true;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') return false; // Duplicado
            throw $e;
        }
    }

    public function estaInscrito(int $usuario_id, int $actividad_id): bool {
        $row = $this->fetchOne(
            'SELECT id FROM inscripciones WHERE usuario_id=:u AND actividad_id=:a LIMIT 1',
            [':u' => $usuario_id, ':a' => $actividad_id]
        );
        return $row !== false;
    }

    public function getMisHoras(int $usuario_id): array {
        return $this->fetchAll(
            'SELECT i.*, a.titulo, a.fecha_inicio, a.tipo
             FROM inscripciones i
             JOIN actividades a ON a.id = i.actividad_id
             WHERE i.usuario_id = :u
             ORDER BY i.inscrito_en DESC',
            [':u' => $usuario_id]
        );
    }

    public function getTotalHoras(int $usuario_id): float {
        $row = $this->fetchOne(
            "SELECT COALESCE(SUM(horas), 0) AS total
             FROM inscripciones
             WHERE usuario_id = :u AND estado = 'completada'",
            [':u' => $usuario_id]
        );
        return (float)($row['total'] ?? 0);
    }

    public function aprobar(int $inscripcion_id, float $horas): int {
        return $this->execute(
            "UPDATE inscripciones SET estado='completada', horas=:h WHERE id=:id",
            [':h' => $horas, ':id' => $inscripcion_id]
        );
    }

    public function getAllPendientes(): array {
        return $this->fetchAll(
            "SELECT i.id, i.estado, i.horas, i.inscrito_en,
                    u.nombre, u.apellido, u.numero_doc,
                    a.titulo AS actividad
             FROM inscripciones i
             JOIN usuarios u ON u.id = i.usuario_id
             JOIN actividades a ON a.id = i.actividad_id
             WHERE i.estado = 'pendiente'
             ORDER BY i.inscrito_en ASC"
        );
    }
}
