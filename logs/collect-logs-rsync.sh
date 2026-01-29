#!/bin/bash
# Environment is REQUIRED: dev/test/live/or a Multidev
# Usage examples:
#   ./logs/collect-logs-rsync.sh live
#   ENV=live ./logs/collect-logs-rsync.sh
#   ENV=live composer run-script my-script   # if your Composer script calls this file

# Prefer, in order: first argument, then ENV
ENV="${1:-${ENV:-}}"

if [ -z "$ENV" ]; then
    echo "Error: environment (dev/test/live or a Multidev) is required." >&2
    echo "Usage: ./logs/collect-logs-rsync.sh <env>  or  ENV=<env> ./logs/collect-logs-rsync.sh" >&2
    exit 1
fi

# Always work inside the logs directory (where this script lives)
SCRIPT_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")" && pwd)"
LOGS_DIR="$SCRIPT_DIR"

# Site UUID is REQUIRED: Site UUID from Dashboard URL, e.g. 12345678-1234-1234-abcd-0123456789ab
# Prefer (in order): existing SITE_UUID env var, then config.id from .lando.yml.
REPO_ROOT="$(cd "$SCRIPT_DIR/.." && pwd)"
LANDO_YML="$REPO_ROOT/.lando.yml"

if [ -z "${SITE_UUID:-}" ]; then
    if [ -f "$LANDO_YML" ]; then
        # Extract the first `id:` value from .lando.yml (config.id)
        SITE_UUID_FROM_LANDO=$(awk '/^[[:space:]]*id:[[:space:]]*/ {print $2; exit}' "$LANDO_YML")
        if [ -n "$SITE_UUID_FROM_LANDO" ]; then
            SITE_UUID="$SITE_UUID_FROM_LANDO"
        fi
    fi
fi

if [ -z "${SITE_UUID:-}" ]; then
    echo "Error: SITE_UUID is not set and could not be read from .lando.yml (config.id)." >&2
    echo "Set SITE_UUID in the environment or ensure .lando.yml contains a config.id value." >&2
    exit 1
fi

echo "Using SITE_UUID=$SITE_UUID"

########### Additional settings you don't have to change unless you want to ###########
# OPTIONAL: Set AGGREGATE_NGINX to true if you want to aggregate nginx logs.
#  WARNING: If set to true, this will potentially create a large file
AGGREGATE_NGINX=false
# if you just want to aggregate the files already collected, set COLLECT_LOGS to FALSE
COLLECT_LOGS=true
# CLEANUP_AGGREGATE_DIR removes all logs except combined.logs from aggregate-logs directory.
CLEANUP_AGGREGATE_DIR=false


if [ $COLLECT_LOGS == true ]; then
echo "COLLECT_LOGS set to $COLLECT_LOGS. Beginning the process..."
for app_server in $(dig +short -4 appserver.$ENV.$SITE_UUID.drush.in);
do
    rsync -rlvz --size-only --ipv4 --progress -e "ssh -p 2222" "$ENV.$SITE_UUID@$app_server:logs" "$LOGS_DIR/app_server_$app_server"
done

# Include MySQL logs
for db_server in $(dig +short -4 dbserver.$ENV.$SITE_UUID.drush.in);
do
    rsync -rlvz --size-only --ipv4 --progress -e "ssh -p 2222" "$ENV.$SITE_UUID@$db_server:logs" "$LOGS_DIR/db_server_$db_server"
done
else
echo 'skipping the collection of logs..'
fi

if [ $AGGREGATE_NGINX == true ]; then
echo "AGGREGATE_NGINX set to $AGGREGATE_NGINX. Starting the process of combining nginx-access logs..."
mkdir -p "$LOGS_DIR/aggregate-logs"

for d in $(ls -d "$LOGS_DIR"/app*/logs/nginx); do
    for f in $(ls -f "$d"); do
    if [[ $f == "nginx-access.log" ]]; then
        cat "$d/$f" >> "$LOGS_DIR/aggregate-logs/nginx-access.log"
        cat "" >> "$LOGS_DIR/aggregate-logs/nginx-access.log"
    fi
    if [[ $f =~ \.gz ]]; then
        cp -v "$d/$f" "$LOGS_DIR/aggregate-logs/"
    fi
    done
done

echo "unzipping nginx-access logs in aggregate-logs directory..."
for f in $(ls -f "$LOGS_DIR/aggregate-logs"); do
    if [[ $f =~ \.gz ]]; then
    gunzip "$LOGS_DIR/aggregate-logs/$f"
    fi
done

echo "combining all nginx access logs..."
for f in $(ls -f "$LOGS_DIR/aggregate-logs"); do
    cat "$LOGS_DIR/aggregate-logs/$f" >> "$LOGS_DIR/aggregate-logs/combined.logs"
done
echo "the combined logs file can be found in $LOGS_DIR/aggregate-logs/combined.logs"
else
echo "AGGREGATE_NGINX set to $AGGREGATE_NGINX. So we're done."
fi

if [ $CLEANUP_AGGREGATE_DIR == true ]; then
echo "CLEANUP_AGGREGATE_DIR set to $CLEANUP_AGGREGATE_DIR. Cleaning up the aggregate-logs directory"
find "$LOGS_DIR/aggregate-logs/" -name 'nginx-access*' -print -exec rm {} \;
fi
