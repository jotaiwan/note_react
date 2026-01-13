# Docker support for Note React

This folder contains compose files and helper files to run the project in Docker.

Available compositions:

- `docker-compose.yml` (development)
  - Services: `backend` (PHP built-in server), `frontend` (Vite dev server)
  - Usage (from repo root):
    - docker-compose -f docker/docker-compose.yml up --build
    - Backend will be available on http://localhost:8001
    - Frontend will be available on http://localhost:5174

Notes:
- The Apache container mounts the local project into the container. For production, build and copy artifacts instead of mounting.
- Environment variables (NOTE_DATA_FILE, NOTE_CREDENTIAL_JSON) should be set on the host or passed into containers.
- The project stores notes in file(s) under `$HOME_NOTES` by default (see README for details). Ensure mounted container has appropriate ownership/permissions for writable `var/` and data files.
