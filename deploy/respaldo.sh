#!/usr/bin/env bash
#
# Respaldo diario de la base de datos de SIDEc (se ejecuta en el servidor, no en el PC).
#
# Uso manual:        ./respaldo.sh
# Programarlo:       crontab -e   y agregar la línea (todos los días a las 02:00):
#                    0 2 * * * /home/servidor/sidec-respaldos/respaldo.sh >> /home/servidor/sidec-respaldos/respaldo.log 2>&1
#
# Restaurar un respaldo (¡reemplaza los datos actuales!):
#   gunzip -c sidec-2026-11-12_0200.sql.gz | docker exec -i sidec-db-1 sh -c 'mariadb -u root -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"'
#
# Copia de vez en cuando los archivos a otro equipo: si falla el disco del servidor,
# los respaldos que están en ese mismo disco se pierden con él.

set -euo pipefail

CONTENEDOR="${CONTENEDOR:-sidec-db-1}"                 # contenedor de la base de datos (ver: docker ps)
CARPETA="${CARPETA:-/home/servidor/sidec-respaldos}"   # dónde se guardan los respaldos
DIAS="${DIAS:-14}"                                     # cuántos días se conservan

mkdir -p "$CARPETA"
ARCHIVO="$CARPETA/sidec-$(date +%F_%H%M).sql.gz"

# Las credenciales se leen dentro del contenedor: no quedan escritas en este archivo
docker exec "$CONTENEDOR" sh -c 'mariadb-dump --single-transaction -u root -p"$MARIADB_ROOT_PASSWORD" "$MARIADB_DATABASE"' \
    | gzip > "$ARCHIVO.tmp"

# Un respaldo vacío o cortado no reemplaza a uno bueno
if [ "$(gzip -dc "$ARCHIVO.tmp" | head -c 200 | wc -c)" -lt 100 ]; then
    rm -f "$ARCHIVO.tmp"
    echo "$(date '+%F %T') ERROR: el respaldo salió vacío" >&2
    exit 1
fi

mv "$ARCHIVO.tmp" "$ARCHIVO"
find "$CARPETA" -name 'sidec-*.sql.gz' -mtime +"$DIAS" -delete

echo "$(date '+%F %T') Respaldo listo: $ARCHIVO ($(du -h "$ARCHIVO" | cut -f1))"
