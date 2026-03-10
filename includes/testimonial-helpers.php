<?php

function testimonial_column_exists(mysqli $conn, string $columnName): bool
{
    $safeColumn = $conn->real_escape_string($columnName);
    $result = $conn->query("SHOW COLUMNS FROM testimonios LIKE '{$safeColumn}'");

    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function ensureTestimonialsSchema(mysqli $conn): void
{
    static $schemaReady = false;

    if ($schemaReady) {
        return;
    }

    $conn->query("
        CREATE TABLE IF NOT EXISTS testimonios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            nombre VARCHAR(100) NOT NULL,
            cargo VARCHAR(100),
            empresa VARCHAR(100),
            testimonio TEXT NOT NULL,
            foto VARCHAR(255),
            valoracion INT DEFAULT 5,
            proyecto_id INT,
            destacado BOOLEAN DEFAULT FALSE,
            aprobado BOOLEAN NOT NULL DEFAULT FALSE,
            orden INT DEFAULT 0,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $missingColumns = [
        'cargo' => "ALTER TABLE testimonios ADD COLUMN cargo VARCHAR(100) NULL AFTER nombre",
        'empresa' => "ALTER TABLE testimonios ADD COLUMN empresa VARCHAR(100) NULL AFTER cargo",
        'foto' => "ALTER TABLE testimonios ADD COLUMN foto VARCHAR(255) NULL AFTER testimonio",
        'valoracion' => "ALTER TABLE testimonios ADD COLUMN valoracion INT DEFAULT 5 AFTER foto",
        'destacado' => "ALTER TABLE testimonios ADD COLUMN destacado BOOLEAN DEFAULT FALSE AFTER proyecto_id",
        'aprobado' => "ALTER TABLE testimonios ADD COLUMN aprobado BOOLEAN NOT NULL DEFAULT FALSE AFTER destacado",
        'orden' => "ALTER TABLE testimonios ADD COLUMN orden INT DEFAULT 0 AFTER aprobado",
    ];

    foreach ($missingColumns as $column => $sql) {
        if (!testimonial_column_exists($conn, $column)) {
            $conn->query($sql);
        }
    }

    $conn->query('ALTER TABLE testimonios MODIFY aprobado BOOLEAN NOT NULL DEFAULT FALSE');

    $schemaReady = true;
}

function getPendingTestimonialsCount(mysqli $conn): int
{
    ensureTestimonialsSchema($conn);

    $result = $conn->query('SELECT COUNT(*) AS total FROM testimonios WHERE aprobado = 0');

    if (!$result instanceof mysqli_result) {
        return 0;
    }

    $row = $result->fetch_assoc();
    $result->free();

    return (int) ($row['total'] ?? 0);
}
