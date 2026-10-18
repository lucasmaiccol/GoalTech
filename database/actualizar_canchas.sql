-- actualizar_canchas.sql: agrega a la tabla "canchas" las columnas nuevas (descripcion e imagen) SIN borrar tus datos.
-- Usalo solo si ya tenias la base creada con una version anterior. Se importa desde phpMyAdmin (pestaña "Importar").
USE sistema_canchas;                                                    -- Selecciona la base de datos del proyecto.
ALTER TABLE canchas ADD COLUMN descripcion VARCHAR(150) NULL AFTER precio_hora; -- Texto corto que se muestra en la tarjeta de la cancha.
ALTER TABLE canchas ADD COLUMN imagen VARCHAR(255) NULL AFTER descripcion;      -- Foto de la cancha (link https://... o ruta dentro del proyecto).
