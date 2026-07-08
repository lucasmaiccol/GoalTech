CREATE DATABASE sistema_canchas;

USE sistema_canchas;

CREATE TABLE usuarios(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    email VARCHAR(50),
    telefono VARCHAR(20),
    password VARCHAR(250),
    rol VARCHAR(20)
);

CREATE TABLE canchas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    tipo VARCHAR(30),
    precio_hora DECIMAL(10,2),
    estado VARCHAR(20)
);

CREATE TABLE reservas(
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT,
    cancha_id INT,
    fecha DATE,
    hora_inicio TIME,
    hora_fin TIME,
    estado VARCHAR(20),

    FOREIGN KEY(usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY(cancha_id) REFERENCES canchas(id)
);