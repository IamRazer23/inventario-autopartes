INVENTARIO_AUTOPARTES

La referencia aparecerá correctamente en:

Instalación

Configuración

phpMyAdmin

Database.php

inventario_autopartes.sql

Documentación general

Sistema de Inventario y Catálogo de Autopartes

Aplicación web desarrollada con PHP, MySQL, phpMyAdmin, XAMPP y TailwindCSS (vía CDN).
El proyecto implementa un patrón MVC modular, con módulos para administrador, operador, cliente y público.
Incluye catálogo dinámico, carrito, ventas, comentarios, gestión de usuarios, estadísticas y dashboards.

🚀 Tecnologías utilizadas

PHP 8.x

MySQL / MariaDB

phpMyAdmin

Apache (XAMPP)

JavaScript

TailwindCSS por CDN

MVC Modular

Front Controller

Sistema de logs

🎨 TailwindCSS

Este proyecto NO compila Tailwind ni utiliza archivos CSS locales.
Todas las vistas usan Tailwind mediante CDN:

<script src="https://cdn.tailwindcss.com"></script>

✔ Diseño 100% en Tailwind desde las vistas

📁 Estructura del proyecto
SEMESTRAL-ING-WEB/
│
├── config/
│   ├── config.php
│   └── Database.php
│
├── controllers/
│   ├── AdminController.php
│   ├── AuthController.php
│   ├── AutoparteController.php
│   ├── CarritoController.php
│   ├── CatalogoController.php
│   ├── CategoriaController.php
│   ├── ClienteController.php
│   ├── OperadorController.php
│   └── UsuarioController.php
│
├── core/
├── database/
│   ├── schema.sql
│   └── inventario_autopartes.sql  ← Base de datos oficial del proyecto
│
├── includes/
├── logs/
├── models/
│
├── public/
│   ├── uploads/
│   ├── js/
│   ├── index.php
│
└── views/

🗄️ Base de datos utilizada
Nombre oficial de la base de datos:
inventario_autopartes

Toda la lógica del sistema, controladores, modelos y scripts SQL están construidos sobre esta BD.

database/inventario_autopartes.sql → datos reales exportados

⚙️ Instalación en XAMPP
1. Clonar el repositorio
cd C:\xampp\htdocs
git clone https://github.com/IamRazer23/inventario-autopartes.git

2. Crear la base de datos en phpMyAdmin

Ir a: http://localhost/phpmyadmin

Crear BD con el nombre exacto:

inventario_autopartes


Importar en este orden:

/database/inventario_autopartes.sql 

3. Configurar conexión a MySQL

Archivo: config/Database.php

private $host = "localhost";
private $dbname = "inventario_autopartes";
private $username = "root";
private $password = "";

4. Configurar ruta base

Archivo: config/config.php

define("BASE_URL", "http://localhost/inventario-autopartes/public/");

5. Ejecutar el sistema

Abrir en navegador:

http://localhost/inventario-autopartes/public/

👤 Roles del sistema
Rol	Funciones
Administrador	Inventario, usuarios, categorías, estadísticas
Operador	Inventario, ventas, comentarios
Cliente	Carrito, compras, historial
Público	Navegar catálogo
🧪 Funcionalidades principales
Catálogo

Ver autopartes

Filtros y búsqueda

Vista de detalle

Comentarios y puntuaciones

Carrito y Ventas

Carrito persistente por usuario

Checkout

Historial de compras

Detalle de venta

Panel Administrador

Gestión de categorías

Gestión de autopartes

Gestión de usuarios y roles

Estadísticas

Panel Operador

Inventario

Ventas

Comentarios

Perfil

Panel Cliente

Carrito

Compras

Perfil

Historial

🛠️ Solución de problemas
Tailwind no carga

Confirmar que cada vista incluya:

<script src="https://cdn.tailwindcss.com"></script>

Index no carga correctamente

Usar siempre esta ruta:

/public/index.php

Error de tablas

Importar nuevamente schema.sql.

Pantalla en blanco

Consultar logs:

logs/php_errors.log
logs/errors.log

Pagina no Carga:
Clonar el repositorio dentro de la carpeta htdocs de xampp y crear una carpeta llamada inventario-autopartes y guardar el repo ahi, para solucionar problemas de rutas

🤝 Contribuciones

Crear branch

Commits ordenados

Pull Request

📄 Licencia

Uso académico y personal.