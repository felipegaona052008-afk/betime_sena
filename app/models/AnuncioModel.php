<?php
// app/models/AnuncioModel.php

require_once __DIR__ . '/../../core/Model.php';

class AnuncioModel extends Model {

    public function getActivos(): array {
        return $this->fetchAll(
            'SELECT id, titulo, contenido FROM anuncios WHERE activo = 1 ORDER BY creado_en DESC LIMIT 6'
        );
    }

    public function create(string $titulo, string $contenido): int {
        $this->execute(
            'INSERT INTO anuncios (titulo, contenido) VALUES (:t, :c)',
            [':t' => $titulo, ':c' => $contenido]
        );
        return (int)$this->lastId();
    }

    public function delete(int $id): int {
        return $this->execute(
            'UPDATE anuncios SET activo = 0 WHERE id=:id',
            [':id' => $id]
        );
    }
}
