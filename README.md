# GoalTech

Sistema web para la gestión de reservas de canchas de fútbol.

## Tecnologías utilizadas

- HTML
- CSS
- PHP
- MySQL
- XAMPP
- Git / GitHub

## Usuarios de prueba

| Rol     | Correo                 | Contraseña   |
|---------|------------------------|--------------|
| Admin   | admin@goaltech.com     | admin123     |
| Cliente | cliente@goaltech.com   | cliente123   |

## Funcionalidades

- **Cliente:** registro, login, reservar una cancha (fecha + horario de 1 hora), **pago simulado con tarjeta ficticia** (la reserva solo se crea si el "pago" se aprueba), ver sus reservas y cancelarlas (el pago pasa a "reembolsado").
- **Admin:** alta, modificación y baja de **reservas**, **canchas** y **usuarios**.
- **Fotos de canchas:** cada cancha tiene su foto real (se ve en el dashboard, en el pago y en el panel). El admin puede elegir una foto de ejemplo o **subir una foto propia** (JPG, PNG o WEBP, máx. 2 MB); las fotos subidas se guardan en `GestorDeCanchaDeFutbol/uploads/canchas/`.

## Estructura

- `index.php`: página de inicio.
- `database/sistema_canchas.sql`: tablas y datos de prueba.
- `GestorDeCanchaDeFutbol/config/`: conexión a la base de datos.
- `GestorDeCanchaDeFutbol/app/models/`: consultas SQL (Usuario, Cancha, Reserva, Pago).
- `GestorDeCanchaDeFutbol/app/controllers/`: reglas del negocio (Auth, Reserva, Admin).
- `GestorDeCanchaDeFutbol/app/views/`: páginas que ve el usuario.
- `GestorDeCanchaDeFutbol/app/includes/`: sesión/permisos y plantilla (header y footer).
- `GestorDeCanchaDeFutbol/css/`: estilos (`estilo.css` formularios, `tema.css` tema compartido de portada/login/registro, `inicio.css` portada, `auth.css` login y registro, `panel.css` dashboard y administración).
- `GestorDeCanchaDeFutbol/img/canchas/`: imágenes de ejemplo de las canchas y fondo.
- `GestorDeCanchaDeFutbol/uploads/canchas/`: fotos subidas por el admin.