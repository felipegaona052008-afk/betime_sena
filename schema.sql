-- ============================================================
-- BeTimeSENA – Esquema de Base de Datos
-- Motor: MySQL 8+ | charset: utf8mb4
-- ============================================================

CREATE DATABASE IF NOT EXISTS betimesena
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE betimesena;

-- ------------------------------------------------------------
-- TABLA: usuarios
-- La contraseña se almacena como hash Bcrypt (60 chars).
-- Se NO guarda la contraseña en texto plano bajo ningún motivo.
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    nombre        VARCHAR(80)      NOT NULL,
    apellido      VARCHAR(80)      NOT NULL,
    tipo_doc      ENUM('CC','TI','CE') NOT NULL,
    numero_doc    VARCHAR(20)      NOT NULL,
    correo        VARCHAR(120)     NOT NULL,
    password_hash VARCHAR(72)      NOT NULL  COMMENT 'Bcrypt hash – NUNCA texto plano',
    rol           ENUM('aprendiz','admin') NOT NULL DEFAULT 'aprendiz',
    activo        TINYINT(1)       NOT NULL DEFAULT 1,
    creado_en     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    actualizado_en DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP
                                   ON UPDATE CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_numero_doc (numero_doc),
    UNIQUE KEY uq_correo     (correo)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: actividades
-- Eventos / actividades de bienestar
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS actividades (
    id            INT UNSIGNED     NOT NULL AUTO_INCREMENT,
    titulo        VARCHAR(150)     NOT NULL,
    descripcion   TEXT,
    fecha_inicio  DATE             NOT NULL,
    fecha_fin     DATE,
    lugar         VARCHAR(120),
    imagen        VARCHAR(255),
    tipo          ENUM('deportiva','cultural','academica','recreativa','otra') NOT NULL DEFAULT 'otra',
    activo        TINYINT(1)       NOT NULL DEFAULT 1,
    creado_por    INT UNSIGNED,
    creado_en     DATETIME         NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    FOREIGN KEY fk_actividad_admin (creado_por)
        REFERENCES usuarios(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: inscripciones
-- Aprendiz se inscribe a una actividad (horas de bienestar)
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS inscripciones (
    id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
    usuario_id      INT UNSIGNED NOT NULL,
    actividad_id    INT UNSIGNED NOT NULL,
    horas           DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    estado          ENUM('pendiente','aprobada','rechazada','completada') NOT NULL DEFAULT 'pendiente',
    inscrito_en     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id),
    UNIQUE KEY uq_inscripcion (usuario_id, actividad_id),
    FOREIGN KEY fk_insc_usuario   (usuario_id)   REFERENCES usuarios(id)    ON DELETE CASCADE,
    FOREIGN KEY fk_insc_actividad (actividad_id) REFERENCES actividades(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- TABLA: anuncios
-- Avisos del panel lateral
-- ------------------------------------------------------------
CREATE TABLE IF NOT EXISTS anuncios (
    id          INT UNSIGNED NOT NULL AUTO_INCREMENT,
    titulo      VARCHAR(200) NOT NULL,
    contenido   TEXT,
    activo      TINYINT(1)   NOT NULL DEFAULT 1,
    creado_en   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ------------------------------------------------------------
-- USUARIO ADMIN por defecto  (password: Admin@1234)
-- Bcrypt generado con cost=12
-- CAMBIA ESTA CONTRASEÑA EN PRODUCCIÓN
-- ------------------------------------------------------------
INSERT IGNORE INTO usuarios
    (nombre, apellido, tipo_doc, numero_doc, correo, password_hash, rol)
VALUES
    ('Admin', 'BeTimeSENA', 'CC', '0000000000',
     'admin@betimesena.edu.co',
     '$2y$12$YHnBpXgEhECo7oFd6q9PiO3MBU4gRwFQXzK1yJVqw6K9oVj8y2RKy',
     'admin');

-- Anuncios de ejemplo
INSERT IGNORE INTO anuncios (titulo, contenido) VALUES
    ('Evento cultural', 'Celebración de la diversidad cultural del SENA.'),
    ('Convocatoria deportiva', 'Inscripciones abiertas para el torneo inter-sedes.'),
    ('Nuevo taller disponible', 'Taller de liderazgo y comunicación asertiva.');

-- Actividades de ejemplo
INSERT IGNORE INTO actividades (titulo, descripcion, fecha_inicio, fecha_fin, tipo) VALUES
    ('Jornada Deportiva',
     'Actividad enfocada en el bienestar físico de los aprendices, incluye fútbol, baloncesto y actividades recreativas.',
     '2026-04-15', '2026-04-15', 'deportiva'),
    ('Taller de Liderazgo',
     'Espacio para fortalecer habilidades de liderazgo, trabajo en equipo y comunicación.',
     '2026-04-22', '2026-04-22', 'academica'),
    ('Torneo de Ping-Pong',
     'Torneo competitivo de Ping-Pong, centrado en la recreación y esparcimiento.',
     '2026-05-10', '2026-05-10', 'recreativa');
