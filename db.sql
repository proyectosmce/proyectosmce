-- NOTA HOSTING: en InfinityFree selecciona antes tu base if0_XXXXXXXX_proyectosmce en phpMyAdmin.
-- Si trabajas en local (XAMPP), puedes descomentar las dos líneas siguientes para crearla y usarla.
-- CREATE DATABASE IF NOT EXISTS proyectosmce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- USE proyectosmce;

-- Tabla de proyectos (portafolio)
CREATE TABLE IF NOT EXISTS proyectos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    imagen VARCHAR(255),
    categoria VARCHAR(100),
    url_demo VARCHAR(255),
    url_repo VARCHAR(255),
    cliente VARCHAR(200),
    fecha_completado DATE,
    destacado BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de servicios
CREATE TABLE IF NOT EXISTS servicios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT,
    icono VARCHAR(100),
    precio_desde DECIMAL(10,2),
    destacado BOOLEAN DEFAULT FALSE,
    orden INT DEFAULT 0
) ENGINE=InnoDB;

-- Tabla de mensajes de contacto
CREATE TABLE IF NOT EXISTS mensajes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    telefono VARCHAR(50),
    mensaje TEXT NOT NULL,
    leido BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de citas (agenda de llamadas)
CREATE TABLE IF NOT EXISTS citas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(120) NOT NULL,
    telefono VARCHAR(50),
    servicio VARCHAR(120),
    notas TEXT,
    estado VARCHAR(20) NOT NULL DEFAULT 'pendiente',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    KEY idx_fecha_hora (fecha, hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tabla de usuarios (admin)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    email VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Tabla de pagos de proyectos
CREATE TABLE IF NOT EXISTS proyecto_pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    invoice_number INT NULL UNIQUE,
    cliente VARCHAR(200) NULL,
    proyecto_id INT NULL,
    concepto VARCHAR(200) NOT NULL,
    forma_pago ENUM('contado','cuotas') NOT NULL DEFAULT 'contado',
    cuotas_totales INT NULL,
    cuotas_pendientes INT NULL,
    proxima_cuota DATE NULL,
    monto DECIMAL(12,2) NOT NULL,
    moneda VARCHAR(10) NOT NULL DEFAULT 'COP',
    estado VARCHAR(30) NOT NULL DEFAULT 'recibido',
    metodo VARCHAR(50),
    referencia VARCHAR(100),
    notas TEXT,
    fecha_pago DATE NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (proyecto_id) REFERENCES proyectos(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Si ya tienes la tabla creada y te falta el consecutivo de factura, ejecuta:
-- ALTER TABLE proyecto_pagos ADD COLUMN invoice_number INT NULL UNIQUE;
-- ALTER TABLE proyecto_pagos ADD COLUMN cliente VARCHAR(200) NULL;
-- ALTER TABLE proyecto_pagos ADD COLUMN forma_pago ENUM('contado','cuotas') NOT NULL DEFAULT 'contado';
-- ALTER TABLE proyecto_pagos ADD COLUMN cuotas_totales INT NULL;
-- ALTER TABLE proyecto_pagos ADD COLUMN cuotas_pendientes INT NULL;
-- ALTER TABLE proyecto_pagos ADD COLUMN proxima_cuota DATE NULL;

-- Insertar usuario admin por defecto (contraseña: admin123)
-- IMPORTANTE: Cambiá esta contraseña después
-- INSERT INTO usuarios (username, password_hash, email) 
-- VALUES ('admin', '$2y$10$dPyVtyOAAC5U4uF.SNU3YOyzuZnYGmmugrMG/vsNlmuGnIdQ8YU1.', 'admin@proyectosmce.com');
-- Reemplaza el hash anterior por uno generado para tu clave unica antes de ejecutarlo.

-- Insertar datos de ejemplo
INSERT INTO proyectos (titulo, descripcion, imagen, categoria, destacado, orden) VALUES
('Sistema Inventario Oro Laminado', 'Sistema completo con carrito de compras, gestión de garantías y control de stock para tienda de joyería.', 'inventario-oro.jpg', 'Sistemas Web', TRUE, 1),
('Tienda Online Ropa', 'E-commerce con pasarela de pagos y panel administrativo.', 'tienda-ropa.jpg', 'E-commerce', FALSE, 2),
('Landing Page Inmobiliaria', 'Página profesional para mostrar propiedades y captar leads.', 'inmobiliaria.jpg', 'Landing Page', FALSE, 3);

INSERT INTO servicios (titulo, descripcion, icono, precio_desde, destacado, orden) VALUES
('Desarrollo Web a Medida', 'Sistemas personalizados según tus necesidades. Como el sistema de inventario que ves en el portafolio.', 'code', 1500.00, TRUE, 1),
('Tiendas Online', 'Vende por internet con carrito, pasarela de pagos y administración de productos.', 'shopping-cart', 2000.00, TRUE, 2),
('Sistemas de Inventario', 'Control de stock, ventas, garantías y reportes. Ideal para pequeños negocios.', 'boxes', 1200.00, TRUE, 3),
('Landing Pages', 'Páginas profesionales para campañas de marketing o presentación de servicios.', 'file-alt', 800.00, FALSE, 4);
