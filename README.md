# SIDEc

Sistema de Información del Departamento de Ecotoxicología — Laboratorio de Bioensayos del Centro Regional de Estudios Ambientales (CREA), Universidad Católica de la Santísima Concepción.

Registra el recorrido de cada muestra en el laboratorio: **Recepción → Ingreso de muestra → Bioensayos → Validación → Dashboard**.

## Tecnología

- PHP 8.2, Laravel 8.83, MariaDB / MySQL
- Bootstrap 5.3, DataTables, Chart.js, flatpickr y SweetAlert2 (servidos desde `public/vendor`, sin depender de internet)
- Roles con `spatie/laravel-permission`
- Despliegue con Docker Compose (`Dockerfile` y `compose.yaml`)

## Roles

| Rol | Puede |
|---|---|
| Analista | Ingresar muestras y llenar las planillas de los bioensayos |
| Encargado de área | Lo anterior, validar bioensayos y ver el Dashboard (sin clientes) |
| Supervisor / Jefe de Área | Todo: recepciones, rechazos, usuarios, reabrir y eliminar bioensayos |

Un bioensayo validado queda en solo lectura hasta que un Supervisor o Jefe de Área lo reabre, y no se puede eliminar.

## Requisitos

- PHP 8.2 con las extensiones `pdo_mysql`, `mbstring`, `openssl`, `zip` e `intl` (todas vienen en XAMPP 8.2)
- Composer 2
- MariaDB 10.4+ o MySQL 8
- Git

Las dependencias exigen **PHP 8.2 como mínimo**: con PHP 8.1 `composer install` falla.

## Instalación local

```bash
git clone https://github.com/mgalvez-de/SIDEc.git
cd SIDEc
composer install
cp .env.example .env
php artisan key:generate
```

Crear una base de datos vacía (por ejemplo `sidec`) y configurar en `.env`:

```env
DB_DATABASE=sidec
DB_USERNAME=root
DB_PASSWORD=
```

Después:

```bash
php artisan migrate
php artisan db:seed                              # roles y cuentas de prueba (solo fuera de producción)
php artisan db:seed --class=DemoDataSeeder       # opcional: muestras de demostración (borra los datos)
php artisan serve                                # http://localhost:8000
```

Cuentas de prueba locales: `analist@a`, `manager@a`, `supervisor@a` y `areamanager@a`, todas con la contraseña `12345678`. **En producción no se crean**: la primera cuenta se crea con `php artisan sidec:crear-admin`.

## Despliegue en el servidor (Docker)

Guía completa paso a paso: [`deploy/SUBIR_VERSION.md`](deploy/SUBIR_VERSION.md). En resumen:

```bash
git clone https://github.com/mgalvez-de/SIDEc.git sidec && cd sidec
cp deploy/db.env.example db.env
cp deploy/production.env.example production.env     # completar contraseñas y APP_KEY
docker compose build
docker compose run --rm --no-deps app php artisan key:generate --show
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan sidec:crear-admin
```

Queda en `http://10.105.0.47:8081` (solo red UCSC). `db.env` y `production.env` tienen contraseñas: nunca se suben a GitHub.

## Configuración

| Qué | Dónde |
|---|---|
| Base de datos, URL y modo (local / producción) | `.env` (local) o `production.env` (servidor) |
| Código, versión y vigencia de las 8 hojas de registro oficiales | `config/bioensayos.php` |
| Zona horaria | `config/app.php` (`America/Santiago`) |

En producción: `APP_ENV=production` y `APP_DEBUG=false`.

## Tests

```bash
php artisan test
```

Corren en SQLite en memoria (ver `phpunit.xml`); nunca tocan la base de datos real.

## Dónde está cada cosa

| Qué | Dónde |
|---|---|
| Rutas y qué rol entra a cada una | `routes/web.php` |
| Listado único de bioensayos y reabrir | `app/Http/Controllers/BioassayController.php` |
| Ingreso de muestras y creación de sus bioensayos | `app/Http/Controllers/SampleEntryController.php` |
| Planilla de cada bioensayo | `app/Http/Controllers/*Controller.php` y `resources/views/bioassays/*` |
| Reglas de validar, modificar y eliminar bioensayos | `app/Policies/BioassayPolicy.php` |
| Bloqueo y "última modificación" | `app/Models/Concerns/BioassayRecord.php` |
| Duración exigida de cada ensayo | `DURATION_PHASES` en cada modelo de `app/Models` |
| Hojas de registro oficiales | `config/bioensayos.php` |
| Menú, barra de ubicación y títulos | `app/Support/Navigation.php` |
| Colores institucionales | `public/css/sidec-theme.css` |
| Despliegue y respaldo | `Dockerfile`, `compose.yaml` y `deploy/` |

## Documentación

El manual de ingeniería (arquitectura, base de datos, requisitos y diagramas) y el manual de usuario se entregan aparte, en PDF, junto con su código fuente en LaTeX.
