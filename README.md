# SIDEc

Sistema de Información del Departamento de Ecotoxicología — Laboratorio de Bioensayos del Centro Regional de Estudios Ambientales (CREA), Universidad Católica de la Santísima Concepción.

Registra el recorrido de cada muestra en el laboratorio: **Recepción → Ingreso de muestra → Bioensayos → Validación → Dashboard**.

## Tecnología

- PHP 8.2, Laravel 8, MariaDB / MySQL
- Bootstrap 5, DataTables, Chart.js, flatpickr y SweetAlert2 (servidos desde `public/vendor`, sin depender de internet)
- Roles con `spatie/laravel-permission`

## Roles

| Rol | Puede |
|---|---|
| Analista | Ingresar muestras y llenar las planillas de los bioensayos |
| Encargado de área | Lo anterior, validar bioensayos y ver el Dashboard (sin clientes) |
| Supervisor / Jefe de Área | Todo: recepciones, rechazos, usuarios, reabrir y eliminar bioensayos |

Un bioensayo validado queda en solo lectura hasta que un Supervisor o Jefe de Área lo reabre, y no se puede eliminar.

## Instalación local

```bash
composer install
cp .env.example .env
php artisan key:generate
# configurar la base de datos en .env
php artisan migrate
php artisan db:seed                              # roles y cuentas de prueba
php artisan db:seed --class=DemoDataSeeder       # opcional: muestras de demostración
php artisan serve
```

En producción: `APP_ENV=production`, `APP_DEBUG=false` y cambiar o desactivar las cuentas de prueba.

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
| Planilla de cada bioensayo | `app/Http/Controllers/*Controller.php` y `resources/views/bioassays/*` |
| Reglas de validar, modificar y eliminar bioensayos | `app/Policies/BioassayPolicy.php` |
| Bloqueo y "última modificación" | `app/Models/Concerns/BioassayRecord.php` |
| Duración exigida de cada ensayo | `DURATION_PHASES` en cada modelo de `app/Models` |
| Menú, barra de ubicación y títulos | `app/Support/Navigation.php` |
| Colores institucionales | `public/css/sidec-theme.css` |
