# Subir una versión nueva de SIDEc al servidor

Servidor: `servidor2` · `http://10.105.0.47` · SIDEc corre en Docker (`sidec-app-1` y `sidec-db-1`).

Los comandos van en el servidor, salvo el paso 1. Donde dice `CARPETA`, es la carpeta del despliegue de SIDEc (paso 2).

## 1. En el PC: dejar el código en GitHub

```bash
php artisan test          # deben pasar todos
git add -A
git commit -m "..."
git push
```

## 2. En el servidor: ubicar el despliegue

```bash
sudo docker compose ls
```

La columna CONFIG FILES muestra la carpeta del proyecto `sidec`. Entrar a ella y mirar cómo llega el código (un `git pull`, un `code.tgz` o un `deploy.sh`):

```bash
cd CARPETA
ls
```

## 3. Respaldar ANTES de tocar nada

```bash
sudo docker exec sidec-db-1 sh -c 'mariadb-dump --single-transaction -u root -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' > antes-de-actualizar-$(date +%F).sql
ls -lh antes-de-actualizar-*.sql      # debe pesar más de 0
```

## 4. Actualizar el código y reconstruir

Según lo que se vio en el paso 2 (traer el código nuevo), y después:

```bash
sudo docker compose up -d --build
sudo docker compose ps                # los dos contenedores "Up (healthy)"
```

## 5. Migrar la base de datos

Esta versión agrega columnas (papelera y "última modificación"). Sin este paso las páginas dan error 500.

```bash
sudo docker compose exec app php artisan migrate --force
sudo docker compose exec app php artisan optimize:clear
```

## 6. Revisar la configuración

```bash
grep -E "^APP_(ENV|DEBUG|URL)=" production.env
```

Debe decir `APP_ENV=production`, `APP_DEBUG=false` y `APP_URL=http://10.105.0.47`.
Si se edita el archivo: después `sudo chgrp 33 production.env` y `sudo docker compose up -d`.

## 7. Comprobar

```bash
curl -I http://localhost              # 302 Found hacia /login
```

Y desde un PC de la red de la U: abrir `http://10.105.0.47`, iniciar sesión y revisar Bioensayos, Papelera y "Cambiar mi contraseña".

---

## Solo una vez: dejar la base lista para la marcha blanca

Hacerlo DESPUÉS de la capacitación (los datos de demostración sirven para practicar).

**a) Crear la cuenta real de Jefe de Área** (pide nombre, correo y contraseña):

```bash
sudo docker compose exec app php artisan sidec:crear-admin
```

**b) Entrar con esa cuenta** y crear las demás desde la pantalla Usuarios.

**c) Borrar las cuentas de prueba y las muestras de demostración.** Primero el respaldo del paso 3. Después:

```bash
sudo docker compose exec app php artisan tinker
```

```php
App\Models\User::whereIn('email', ['analist@a','supervisor@a','manager@a','areamanager@a'])->get()->each->delete();
App\Models\User::pluck('email');      // deben quedar solo las cuentas reales
```

Las muestras de demostración (M-2026-001 a 014) se borran con cuidado y con el respaldo hecho: pedir los comandos exactos antes de hacerlo.

## Solo una vez: respaldo automático diario

```bash
mkdir -p /home/servidor/sidec-respaldos
cp deploy/respaldo.sh /home/servidor/sidec-respaldos/       # o copiar el archivo desde el repositorio
chmod +x /home/servidor/sidec-respaldos/respaldo.sh
sudo /home/servidor/sidec-respaldos/respaldo.sh             # probarlo a mano
sudo crontab -e                                              # y agregar:
# 0 2 * * * /home/servidor/sidec-respaldos/respaldo.sh >> /home/servidor/sidec-respaldos/respaldo.log 2>&1
```
