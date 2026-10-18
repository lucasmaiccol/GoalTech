-- Script completo de la base de datos de GoalTech. Se importa desde phpMyAdmin (pestaña "Importar").
-- Borra la base si ya existia, asi se puede volver a importar las veces que haga falta.
DROP DATABASE IF EXISTS sistema_canchas;
-- Crea la base de datos con codificacion utf8mb4 (acepta tildes, ñ y emojis).
CREATE DATABASE sistema_canchas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
-- Le dice a MySQL que todo lo que sigue se crea dentro de esta base.
USE sistema_canchas;

-- ============================================================
-- TABLA usuarios: clientes y administradores del sistema.
-- ============================================================
CREATE TABLE usuarios(
    id INT AUTO_INCREMENT PRIMARY KEY,                  -- Identificador unico, se numera solo.
    nombre VARCHAR(50) NOT NULL,                        -- Nombre de la persona, obligatorio.
    email VARCHAR(100) NOT NULL UNIQUE,                 -- Correo obligatorio y UNICO: no se puede repetir.
    telefono VARCHAR(20) NOT NULL,                      -- Telefono de contacto, obligatorio.
    password VARCHAR(255) NOT NULL,                     -- Aca va el HASH de la contraseña, nunca el texto plano.
    rol ENUM('cliente','admin') NOT NULL DEFAULT 'cliente', -- Solo permite estos dos roles; por defecto cliente.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP      -- Fecha y hora en que se creo el usuario (automatica).
);

-- ============================================================
-- TABLA canchas: las canchas que se pueden reservar.
-- ============================================================
CREATE TABLE canchas(
    id INT AUTO_INCREMENT PRIMARY KEY,                  -- Identificador unico, se numera solo.
    nombre VARCHAR(50) NOT NULL,                        -- Nombre de la cancha (ej: "Cancha 1").
    tipo VARCHAR(30) NOT NULL,                          -- Tipo de cancha (ej: "Futbol 5", "Futbol 7").
    precio_hora DECIMAL(10,2) NOT NULL,                 -- Precio por una hora de alquiler.
    descripcion VARCHAR(150) NULL,                      -- Texto corto que se muestra en la tarjeta (techada, luces, etc.). Puede quedar vacio.
    imagen VARCHAR(255) NULL,                           -- Foto de la cancha: un link (https://...) o una ruta dentro de GestorDeCanchaDeFutbol/. Si es NULL se usa una imagen por defecto.
    estado ENUM('activa','inactiva') NOT NULL DEFAULT 'activa' -- Si esta inactiva no se puede reservar.
);

-- ============================================================
-- TABLA reservas: cada turno reservado por un usuario.
-- ============================================================
CREATE TABLE reservas(
    id INT AUTO_INCREMENT PRIMARY KEY,                  -- Identificador unico, se numera solo.
    usuario_id INT NOT NULL,                            -- Quien hizo la reserva.
    cancha_id INT NOT NULL,                             -- Que cancha reservo.
    fecha DATE NOT NULL,                                -- Dia de la reserva.
    hora_inicio TIME NOT NULL,                          -- Hora en que empieza el turno.
    hora_fin TIME NOT NULL,                             -- Hora en que termina el turno (inicio + 1 hora).
    monto DECIMAL(10,2) NOT NULL,                       -- Precio cobrado (se guarda por si luego cambia el precio de la cancha).
    estado ENUM('confirmada','cancelada') NOT NULL DEFAULT 'confirmada', -- Una reserva solo existe si fue "pagada", por eso nace confirmada.
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,     -- Fecha y hora en que se hizo la reserva.
    FOREIGN KEY(usuario_id) REFERENCES usuarios(id),    -- Obliga a que el usuario exista en la tabla usuarios.
    FOREIGN KEY(cancha_id) REFERENCES canchas(id)       -- Obliga a que la cancha exista en la tabla canchas.
);

-- ============================================================
-- TABLA pagos: registro del pago FICTICIO de cada reserva.
-- ============================================================
CREATE TABLE pagos(
    id INT AUTO_INCREMENT PRIMARY KEY,                  -- Identificador unico, se numera solo.
    reserva_id INT NOT NULL,                            -- A que reserva pertenece este pago.
    monto DECIMAL(10,2) NOT NULL,                       -- Importe "pagado".
    metodo VARCHAR(30) NOT NULL,                        -- Metodo de pago (siempre "Tarjeta (simulada)").
    ultimos4 CHAR(4) NOT NULL,                          -- Solo los ultimos 4 digitos de la tarjeta (nunca se guarda el numero completo).
    codigo VARCHAR(20) NOT NULL,                        -- Codigo de comprobante generado, ej: PAGO-A1B2C3D4.
    estado ENUM('aprobado','reembolsado') NOT NULL DEFAULT 'aprobado', -- Pasa a "reembolsado" si se cancela la reserva.
    fecha_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,     -- Fecha y hora del pago.
    FOREIGN KEY(reserva_id) REFERENCES reservas(id) ON DELETE CASCADE -- Si se borra la reserva, se borra su pago tambien.
);

-- ============================================================
-- DATOS DE PRUEBA para poder usar el sistema apenas se importa.
-- ============================================================
-- Usuarios de prueba. Las contraseñas estan guardadas como hash (admin123 y cliente123).
INSERT INTO usuarios (nombre, email, telefono, password, rol) VALUES
('Administrador', 'admin@goaltech.com', '099000000', '$2y$10$.M60dUq7EgI73wWq1vrkZunxROIl.2p/6TrQm5W4CPX4RkFrkM91.', 'admin'),   -- Admin: admin@goaltech.com / admin123
('Cliente Demo', 'cliente@goaltech.com', '099111111', '$2y$10$W2NkGU43VP0JxuCCbvjzPeITNis67Q6wJvfoRh9Qar3uvxUKxCuYu', 'cliente'); -- Cliente: cliente@goaltech.com / cliente123

-- Canchas de prueba para que haya algo para reservar (todas de Futbol 5, cada una con su foto real de Unsplash que el admin puede cambiar).
-- La Cancha 3 queda inactiva para probar ese estado.
INSERT INTO canchas (nombre, tipo, precio_hora, descripcion, imagen, estado) VALUES
('Cancha 1', 'Futbol 5', 800.00, 'Cesped sintetico, ideal para partidos entre amigos.', 'https://images.unsplash.com/photo-1600529533273-938db2591dc4?auto=format&fit=crop&w=1200&q=80', 'activa'),
('Cancha 2', 'Futbol 5', 800.00, 'Perfecta para armar el partido con amigos.', 'https://images.unsplash.com/photo-1632300951015-42d7df909581?auto=format&fit=crop&w=1200&q=80', 'activa'),
('Cancha 3', 'Futbol 5', 900.00, 'En mantenimiento por renovacion del cesped.', 'https://images.unsplash.com/photo-1604412132568-9d5c14de13c4?auto=format&fit=crop&w=1200&q=80', 'inactiva'),
('Cancha 4', 'Futbol 5', 900.00, 'Cesped sintetico premium con lineas nuevas.', 'https://images.unsplash.com/photo-1431324155629-1a6deb1dec8d?auto=format&fit=crop&w=1200&q=80', 'activa'),
('Cancha 5', 'Futbol 5', 900.00, 'Vista despejada y cesped siempre cuidado.', 'https://images.unsplash.com/photo-1510526292299-20af3f62d453?auto=format&fit=crop&w=1200&q=80', 'activa'),
('Cancha 6', 'Futbol 5', 1000.00, 'Amplia y luminosa, con el cesped en perfecto estado.', 'https://images.unsplash.com/photo-1625923541779-630119dfa15d?auto=format&fit=crop&w=1200&q=80', 'activa'),
('Cancha 7', 'Futbol 5', 850.00, 'Cesped en excelente estado, la favorita de dia.', 'https://images.unsplash.com/photo-1630259900856-86e94006646e?auto=format&fit=crop&w=1200&q=80', 'activa'),
('Cancha 8', 'Futbol 5', 1000.00, 'Cesped sintetico de ultima generacion.', 'https://images.unsplash.com/photo-1493538706211-316874a1e8b4?auto=format&fit=crop&w=1200&q=80', 'activa'),
('Cancha 9', 'Futbol 5', 1000.00, 'La mas pedida para los partidos del fin de semana.', 'https://images.unsplash.com/photo-1621844816188-c249945fe28e?auto=format&fit=crop&w=1200&q=80', 'activa');
