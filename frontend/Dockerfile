FROM node:20

WORKDIR /var/www/note_react

COPY package.json package-lock.json* ./

RUN npm install

COPY . .

EXPOSE 5174

# Start Vite (must listen on 0.0.0.0)
# Use the PORT env var when provided so the container can match the host mapping
CMD ["sh", "-c", "npm run dev -- --host 0.0.0.0 --port ${PORT:-5173}"]
