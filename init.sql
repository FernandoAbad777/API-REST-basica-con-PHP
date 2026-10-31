SET NAMES utf8mb4;

CREATE TABLE IF NOT EXISTS productos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  descripcion VARCHAR(255) DEFAULT NULL,
  precio DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO productos (nombre, descripcion, precio, stock) VALUES
('Mouse inalámbrico', 'Mouse óptico 2.4 GHz', 250.00, 30),
('Teclado mecánico', 'Teclado con switches azules', 950.50, 15),
('Audífonos', 'Audífonos con micrófono', 420.00, 25);
