# SIDEc en el servidor: instalar y actualizar

Servidor: `servidor2` · `10.105.0.47` · SIDEc se abre en **http://10.105.0.47:8081** (solo desde la red de la U).

SIDEc corre en Docker con dos contenedores: `sidec-app-1` (la aplicación) y `sidec-db-1` (la base de datos MariaDB).
Todo se arma con los archivos `Dockerfile` y `compose.yaml` de este repositorio.

---

## A. Primera instalación (servidor nuevo)

Requisitos: el servidor ya tiene Docker, Docker Compose y Git, y tu usuario está en el grupo `docker`.

### 1. Bajar el código

```bash
cd ~
git clone https://github.com/mgalvez-de/SIDEc.git sidec
cd sidec
```

Si el repositorio es privado, GitHub pide usuario y un **token** en vez de contraseña
(GitHub → Settings → Developer settings → Personal access tokens, con permiso de solo lectura sobre el repositorio).

### 2. Crear la configuración (estos dos archivos NO se suben a GitHub)

```bash
cp deploy/db.env.example db.env
cp deploy/production.env.example production.env
nano db.env              # inventar dos contraseñas largas
nano production.env      # DB_PASSWORD igual a MARIADB_PASSWORD de db.env
chmod 600 db.env production.env
```

### 3. Construir la imagen y generar la clave de la aplicación

```bash
docker compose build
docker compose run --rm --no-deps app php artisan key:generate --show
```

Copiar lo que imprime (empieza con `base64:`) y pegarlo en `production.env` después de `APP_KEY=`.

### 4. Levantar y crear las tablas

```bash
docker compose up -d
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force      # crea los 4 roles (en producción no crea cuentas de prueba)
```

### 5. Crear la primera cuenta (Jefe de Área)

```bash
docker compose exec app php artisan sidec:crear-admin
```

Pide nombre, correo y contraseña. Con esa cuenta se crean las demás desde la pantalla **Usuarios**.

### 6. Comprobar

```bash
docker compose ps                  # los dos contenedores "Up", la base de datos "(healthy)"
curl -I http://localhost:8081      # 302 Found hacia /login
```

Desde un PC conectado a la red de la U: abrir `http://10.105.0.47:8081` e iniciar sesión.

### 7. Respaldo automático diario (una sola vez)

```bash
mkdir -p ~/sidec-respaldos
cp deploy/respaldo.sh ~/sidec-respaldos/
chmod +x ~/sidec-respaldos/respaldo.sh
CARPETA=~/sidec-respaldos ~/sidec-respaldos/respaldo.sh     # probarlo a mano
crontab -e
```

Y agregar esta línea (respaldo todos los días a las 02:00; cambiar `servidor` si el usuario es otro):

```
0 2 * * * CARPETA=/home/servidor/sidec-respaldos /home/servidor/sidec-respaldos/respaldo.sh >> /home/servidor/sidec-respaldos/respaldo.log 2>&1
```

---

## B. Actualizar a una versión nueva

### 1. En el PC: dejar el código en GitHub

```bash
php artisan test          # deben pasar todas
git add -A
git commit -m "..."
git push
```

### 2. En el servidor: respaldar, actualizar y migrar

```bash
cd ~/sidec
CARPETA=~/sidec-respaldos ~/sidec-respaldos/respaldo.sh   # respaldo ANTES de tocar nada
git pull
docker compose up -d --build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan optimize:clear
```

Los datos no se pierden al actualizar: la base de datos y los archivos viven en volúmenes aparte.

---

## Si algo falla

| Lo que pasa | Qué hacer |
|---|---|
| Error 500 en la página | Ver el registro: `docker compose exec app tail -n 50 storage/logs/laravel.log` |
| `No application encryption key` | Falta `APP_KEY` en `production.env` (paso A.3) y luego `docker compose up -d` |
| Cambié `production.env` y no pasa nada | `restart` no relee el archivo: usar `docker compose up -d` |
| `SQLSTATE ... Connection refused` | Revisar `DB_HOST=db` y que la contraseña sea igual en `production.env` y `db.env` |
| No abre desde mi PC | Estar en la red de la U, y que `docker compose ps` muestre el puerto 8081 |

**Nunca usar `docker compose down -v`**: la `-v` borra la base de datos para siempre.

Restaurar un respaldo (reemplaza los datos actuales):

```bash
gunzip -c ~/sidec-respaldos/sidec-AAAA-MM-DD_HHMM.sql.gz | docker exec -i sidec-db-1 sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"'
```
