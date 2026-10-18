# GoalTech

Sistema web para la gestión de reservas de canchas de fútbol.

## Tecnologías utilizadas

- HTML
- CSS
- PHP
- MySQL
- XAMPP
- Git / GitHub


## Base de datos (paso obligatorio)

1. Con Apache y MySQL iniciados en XAMPP, abrir `http://localhost/phpmyadmin`.
2. Ir a la pestaña **Importar**, elegir el archivo `database/sistema_canchas.sql` y presionar **Importar**.
   (El script borra y vuelve a crear la base `sistema_canchas`, se puede importar las veces que haga falta).
3. Si tu MySQL tiene contraseña para `root`, cambiarla en `GestorDeCanchaDeFutbol/config/conexion.php`.

## Usuarios de prueba

| Rol     | Correo                 | Contraseña   |
|---------|------------------------|--------------|
| Admin   | admin@goaltech.com     | admin123     |
| Cliente | cliente@goaltech.com   | cliente123   |

## Funcionalidades

- **Cliente:** registro, login, reservar una cancha (fecha + horario de 1 hora), **pago simulado con tarjeta ficticia** (la reserva solo se crea si el "pago" se aprueba), ver sus reservas y cancelarlas (el pago pasa a "reembolsado").
- **Admin:** alta, modificación y baja de **reservas**, **canchas** y **usuarios**.
- **Fotos de canchas:** cada cancha tiene su foto real (se ve en el dashboard, en el pago y en el panel). El admin puede elegir una foto de ejemplo o **subir una foto propia** (JPG, PNG o WEBP, máx. 2 MB); las fotos subidas se guardan en `GestorDeCanchaDeFutbol/uploads/canchas/`.
- Las fotos de ejemplo se cargan desde Unsplash (hace falta internet). Si una foto no carga, se muestra automáticamente una imagen de respaldo guardada en el proyecto.
- Pago de prueba: tarjeta `4242 4242 4242 4242`, vencimiento futuro (ej: `08/30`) y cualquier CVV de 3 dígitos. No se guarda el número completo, solo los últimos 4 dígitos.

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

## Requisitos

Para ejecutar el proyecto se necesita tener instalado:

- XAMPP
- Git
- Un navegador web
- Visual Studio Code u otro editor de código

## Cómo ejecutar el proyecto

Requisitos:

- XAMPP instalado
- Git instalado
- Un navegador web

Pasos para descargar y ejecutar el proyecto:

1. Abrir una terminal.

2. Ir a la carpeta `htdocs` de XAMPP:

cd C:\xampp\htdocs

3. Clonar el repositorio:

git clone https://github.com/lucasmaiccol/GoalTech.git

4. Entrar a la carpeta del proyecto:

cd GoalTech

5. Abrir XAMPP.

6. Iniciar los servicios:

Apache
MySQL

7. Abrir el navegador e ingresar a:

http://localhost/GoalTech/index.php

Si el proyecto ya está descargado:

1. Abrir XAMPP.
2. Iniciar Apache y MySQL.
3. Abrir el navegador.
4. Ingresar a:

http://localhost/GoalTech/index.php

Para subir cambios a GitHub:

1. Abrir una terminal en la carpeta del proyecto:

cd C:\xampp\htdocs\GoalTech

2. Revisar los cambios:

git status

3. Preparar los archivos:

git add .

4. Guardar una nueva versión:

git commit -m "Descripcion del cambio"

5. Subir los cambios a GitHub:

git push
## Créditos de las fotos

Fotos de [Unsplash](https://unsplash.com) (licencia gratuita, uso comercial permitido) de: Jan Piatkowski, Falaq Lazuardi, Qurratul Ayin Sadia, Abigail Keenan, Willian Justen de Vasconcellos, Didier Bn, Peter Burdon, Ricardo Gomez Angel y Marcel Eberle.

## Si ya tenías una versión anterior de la base de datos

Si al agregar una cancha aparece el error `Unknown column 'descripcion'`, la base de datos es de una versión vieja. Hay dos opciones:

1. **Reimportar** `database/sistema_canchas.sql` (borra y recrea todo, incluidas las reservas de prueba), o
2. **Conservar tus datos** importando `database/actualizar_canchas.sql`, que solo agrega las columnas nuevas.
