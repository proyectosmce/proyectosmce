<?php

function payment_column_exists(mysqli $conn, string $columnName): bool
{
    $safeColumn = $conn->real_escape_string($columnName);
    $result = $conn->query("SHOW COLUMNS FROM proyecto_pagos LIKE '{$safeColumn}'");

    return $result instanceof mysqli_result && $result->num_rows > 0;
}

function ensureProjectPaymentsSchema(mysqli $conn): void
{
    static $schemaReady = false;

    if ($schemaReady) {
        return;
    }

    $conn->query("
        CREATE TABLE IF NOT EXISTS proyecto_pagos (
            id INT AUTO_INCREMENT PRIMARY KEY,
            invoice_number INT NULL UNIQUE,
            proyecto_id INT NULL,
            cliente VARCHAR(200) NULL,
            forma_pago ENUM('contado','cuotas') NOT NULL DEFAULT 'contado',
            proxima_cuota DATE NULL,
            concepto VARCHAR(200) NOT NULL,
            monto DECIMAL(12,2) NOT NULL,
            moneda VARCHAR(10) NOT NULL DEFAULT 'COP',
            estado VARCHAR(30) NOT NULL DEFAULT 'recibido',
            metodo VARCHAR(50) NULL,
            referencia VARCHAR(100) NULL,
            notas TEXT NULL,
            fecha_pago DATE NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE SET NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $missingColumns = [
        'invoice_number' => "ALTER TABLE proyecto_pagos ADD COLUMN invoice_number INT NULL UNIQUE AFTER id",
        'cliente' => "ALTER TABLE proyecto_pagos ADD COLUMN cliente VARCHAR(200) NULL AFTER proyecto_id",
        'forma_pago' => "ALTER TABLE proyecto_pagos ADD COLUMN forma_pago ENUM('contado','cuotas') NOT NULL DEFAULT 'contado' AFTER cliente",
        'proxima_cuota' => "ALTER TABLE proyecto_pagos ADD COLUMN proxima_cuota DATE NULL AFTER forma_pago",
        'concepto' => "ALTER TABLE proyecto_pagos ADD COLUMN concepto VARCHAR(200) NOT NULL AFTER cliente",
        'moneda' => "ALTER TABLE proyecto_pagos ADD COLUMN moneda VARCHAR(10) NOT NULL DEFAULT 'COP' AFTER monto",
        'estado' => "ALTER TABLE proyecto_pagos ADD COLUMN estado VARCHAR(30) NOT NULL DEFAULT 'recibido' AFTER moneda",
        'metodo' => "ALTER TABLE proyecto_pagos ADD COLUMN metodo VARCHAR(50) NULL AFTER estado",
        'referencia' => "ALTER TABLE proyecto_pagos ADD COLUMN referencia VARCHAR(100) NULL AFTER metodo",
        'notas' => "ALTER TABLE proyecto_pagos ADD COLUMN notas TEXT NULL AFTER referencia",
        'fecha_pago' => "ALTER TABLE proyecto_pagos ADD COLUMN fecha_pago DATE NOT NULL AFTER notas",
        'updated_at' => "ALTER TABLE proyecto_pagos ADD COLUMN updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at",
    ];

    foreach ($missingColumns as $column => $sql) {
        if (!payment_column_exists($conn, $column)) {
            $conn->query($sql);
        }
    }

    $schemaReady = true;
}

function paymentStatusOptions(): array
{
    return [
        'recibido' => 'Recibido',
        'pendiente' => 'Pendiente',
        'parcial' => 'Pago parcial',
        'reembolsado' => 'Reembolsado',
    ];
}

function paymentMethodOptions(): array
{
    return [
        'transferencia' => 'Transferencia',
        'efectivo' => 'Efectivo',
        'tarjeta' => 'Tarjeta',
        'nequi' => 'Nequi / billetera',
        'paypal' => 'PayPal',
        'stripe' => 'Stripe',
        'otro' => 'Otro',
    ];
}

function paymentCurrencyOptions(): array
{
    return [
        'COP' => 'COP',
        'USD' => 'USD',
        'EUR' => 'EUR',
    ];
}

function payment_format_amount(float $amount, string $currency = 'COP'): string
{
    $cleanCurrency = strtoupper(trim($currency ?: 'COP'));
    $formatted = number_format($amount, 2, ',', '.');

    return $cleanCurrency . ' ' . $formatted;
}
?>
