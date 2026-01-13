#!/bin/bash
# dev_docker.sh - the script starts PHP API + React SPA (CSR)

set -e

# create credential file for Docker container
mkdir -p docker/tmp
if [ -n "$NOTE_CREDENTIAL_JSON" ] && [ -f "$NOTE_CREDENTIAL_JSON" ]; then
    # If the file exists, copy it
    cp "$NOTE_CREDENTIAL_JSON" "${PWD}/docker/tmp/credential.json"
    echo "✅ Copied existing credential to docker/tmp/credential.json"
else
    # Otherwise, create an empty JSON
    echo "{}" > "${PWD}/docker/tmp/credential.json"
    echo "⚠️ NOTE_CREDENTIAL_JSON not found, created empty docker/tmp/credential.json"
fi


## --- Start frontend and backend Docker container ---

BACKEND_PORT=8001
FRONTEND_PORT=5174

cd docker || exit

echo "=== Stopping old containers if they exist ==="
docker compose down

echo "=== Building and starting containers ==="
docker compose up --build -d

sleep 2

# re-load bashrc config to make sure that credentials are set
if [ -f "$HOME/.bashrc" ]; then
  source "$HOME/.bashrc"
fi

echo "NOTE_CREDENTIAL_JSON=$NOTE_CREDENTIAL_JSON"
echo "SITE_PATH_AND_CREDENTIAL_FILE=$SITE_PATH_AND_CREDENTIAL_FILE"

echo "=== Backend logs (PHP built-in server) ==="
docker compose logs -f backend | \
  sed -E "s/(ERROR|WARNING|CRITICAL)/\x1b[31m\1\x1b[0m/g; \
          s/(INFO|GET|POST|PUT|DELETE)/\x1b[32m\1\x1b[0m/g" &

echo ""
echo "Frontend (Vite): http://localhost:$FRONTEND_PORT"
echo "Backend  (PHP) : http://localhost:$BACKEND_PORT"
echo ""
echo "Press Ctrl+C to stop viewing logs only (Containers will continue running in the background)"

sleep 2

# Move one level up and remove tmp folder safely
cd ..
rm -rf "${PWD}/docker/tmp"
echo "🗑️  docker/tmp removed"
