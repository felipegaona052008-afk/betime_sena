<?php
// core/Model.php

require_once __DIR__ . '/../config/database.php';

abstract class Model {
    protected PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /** SELECT múltiples filas */
    protected function fetchAll(string $sql, array $params = []): array {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** SELECT una sola fila */
    protected function fetchOne(string $sql, array $params = []): array|false {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetch();
    }

    /** INSERT / UPDATE / DELETE – devuelve filas afectadas */
    protected function execute(string $sql, array $params = []): int {
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->rowCount();
    }

    /** Último ID insertado */
    protected function lastId(): string {
        return $this->db->lastInsertId();
    }
}
