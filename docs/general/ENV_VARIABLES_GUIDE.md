# Environment Variables Guide & Application Setup

## 📋 Overview

This guide explains all environment variables in your `.env.local` file and how to run the Inventory Jovanni application using the `start-dev.sh` script.

---

## 🚀 How to Run the Application

### Quick Start

```bash
# Make sure the script is executable
chmod +x start-dev.sh

# Run the startup script
./start-dev.sh
```

### What `start-dev.sh` Does

1. **Checks Docker** - Verifies Docker is running
2. **Starts Docker Services** - Launches MySQL, Redis, and phpMyAdmin containers
3. **Waits for Services** - Ensures MySQL and Redis are ready before proceeding
4. **Fixes Permissions** - Sets proper permissions for Laravel storage directories
5. **Installs Dependencies** - Runs `composer install` and `npm install` if needed
6. **Runs Migrations** - Executes database migrations
7. **Clears Caches** - Clears Laravel config and cache
8. **Starts Laravel Server** - Runs `php artisan serve` on port 8000

### Access Points

After running `start-dev.sh`, you can access:

- **Laravel App**: `http://localhost:8000` (or your WSL IP)
- **phpMyAdmin**: `http://localhost:8081`
- **MySQL**: `localhost:3307` (external port)
- **Redis**: `localhost:6380` (external port)

---

## 🔧 Environment Variables Explained

### Application Identity

```env
APP_NAME="Inventory Jovanni"
```
- **Purpose**: Application name displayed in the UI
- **Example**: Used in emails, page titles, etc.

```env
APP_ENV=local
```
- **Purpose**: Environment mode (local, staging, production)
- **Values**: `local`, `staging`, `production`
- **Note**: Affects error reporting and debugging

```env
APP_KEY=base64:xFOCHxqN1ALCeUH5ACAQgzO7y5VTky8rRI5x3t74z0w=
```
- **Purpose**: Encryption key for Laravel (sessions, cookies, etc.)
- **Important**: Never share this! Generate new one if compromised
- **Generate**: `php artisan key:generate`

```env
APP_DEBUG=true
```
- **Purpose**: Enable/disable debug mode
- **⚠️ Security**: Set to `false` in production!
- **Local**: `true` shows detailed error pages
- **Production**: `false` shows generic error pages

---

### Application URLs

```env
APP_URL=http://localhost:3001
```
- **Purpose**: Base URL for your application
- **⚠️ Issue Found**: Your `.env.local` has port `3001`, but `start-dev.sh` runs on port `8000`
- **Fix**: Change to `http://localhost:8000` to match the dev server
- **Production**: Set to your actual domain (e.g., `https://yourdomain.com`)

```env
VITE_DEV_SERVER_URL=http://localhost:5173
```
- **Purpose**: Vite development server URL for hot module replacement
- **Used for**: Frontend asset compilation (CSS/JS)
- **Default**: Port 5173 is Vite's default

---

### Localization

```env
APP_LOCALE=en
APP_FALLBACK_LOCALE=en
APP_FAKER_LOCALE=en_US
```
- **Purpose**: Language and locale settings
- **APP_LOCALE**: Default language for the app
- **APP_FALLBACK_LOCALE**: Language to use if translation missing
- **APP_FAKER_LOCALE**: Locale for generating fake data (testing)

---

### Database Configuration (Local)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=inventory_jovanni
DB_USERNAME=root
DB_PASSWORD=password
```

**⚠️ Configuration Mismatch Detected!**

Your `.env.local` shows:
- `DB_PORT=3306` 
- `DB_USERNAME=root`
- `DB_PASSWORD=password`

But `docker-compose.yml` exposes MySQL on:
- **Port 3307** (external) → 3306 (internal)
- **Root password**: `rootsecret`

**Recommended Fix for `.env.local`:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307          # ← Match Docker external port
DB_DATABASE=inventory_jovanni
DB_USERNAME=root
DB_PASSWORD=rootsecret  # ← Match Docker root password
```

**Alternative (if using Docker internal network):**
```env
DB_HOST=db           # ← Docker service name
DB_PORT=3306         # ← Docker internal port
```

---

### Database Configuration (Production - Commented)

```env
# DB_CONNECTION=mysql
# DB_HOST=db
# DB_PORT=3307
# DB_DATABASE=spcdb
# DB_USERNAME=jovanni
# DB_PASSWORD=root
```
- **Purpose**: Production database settings (currently commented out)
- **Note**: Uncomment and adjust when deploying to production

---

### Session Configuration

```env
SESSION_DRIVER=database
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
```

- **SESSION_DRIVER**: Where sessions are stored (`database`, `file`, `redis`, `cookie`)
- **SESSION_LIFETIME**: Session timeout in minutes (120 = 2 hours)
- **SESSION_ENCRYPT**: Encrypt session data (set `true` in production)
- **SESSION_PATH**: Cookie path
- **SESSION_DOMAIN**: Cookie domain (null = current domain)

---

### Broadcasting (Livewire/WebSockets)

```env
BROADCAST_CONNECTION=reverb
```
- **Purpose**: Real-time broadcasting system
- **Reverb**: Laravel's WebSocket server for Livewire updates

```env
REVERB_APP_ID=116692
REVERB_APP_KEY=y6tzoywvblbve39gvsuh
REVERB_APP_SECRET=geerxqbgevoymkkzmx5f
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http
```
- **Purpose**: Reverb WebSocket server configuration
- **REVERB_HOST**: WebSocket server hostname
- **REVERB_PORT**: WebSocket server port
- **REVERB_SCHEME**: `http` or `https`

---

### File Storage

```env
FILESYSTEM_DISK=local
```
- **Purpose**: Default storage disk
- **Options**: `local`, `public`, `s3` (AWS S3)
- **Local**: Stores in `storage/app/`
- **Public**: Stores in `storage/app/public/` (accessible via web)

---

### Queue Configuration

```env
QUEUE_CONNECTION=database
# QUEUE_CONNECTION=redis
```
- **Purpose**: Queue driver for background jobs
- **Current**: Using database (simpler, no extra service)
- **Redis**: Faster, better for production (requires Redis running)

---

### Cache Configuration

```env
CACHE_STORE=database
# CACHE_STORE=redis
```
- **Purpose**: Cache storage driver
- **Current**: Using database
- **Redis**: Faster, recommended for production

---

### Redis Configuration (Currently Commented)

```env
# REDIS_CLIENT=phpredis
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379
```

**⚠️ Configuration Mismatch!**

Your `.env` has Redis configured, but:
- Docker exposes Redis on port **6380** (external) → 6379 (internal)
- **Fix**: Use `REDIS_PORT=6380` for local development

**Recommended for `.env.local`:**
```env
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6380    # ← Match Docker external port
```

---

### Mail Configuration

```env
MAIL_MAILER=log
MAIL_SCHEME=null
MAIL_HOST=127.0.0.1
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_FROM_ADDRESS="hello@example.com"
MAIL_FROM_NAME="${APP_NAME}"
```
- **MAIL_MAILER**: `log` writes emails to log files (good for dev)
- **Production**: Use `smtp`, `mailgun`, `ses`, etc.
- **MAIL_FROM_ADDRESS**: Default sender email
- **MAIL_FROM_NAME**: Default sender name

---

### AWS S3 (Optional - for file storage)

```env
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false
```
- **Purpose**: AWS S3 configuration for cloud file storage
- **Current**: Empty (not configured)
- **Use case**: Store product images in S3 instead of local storage

---

### Logging

```env
LOG_CHANNEL=stack
LOG_STACK=single
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug
```
- **LOG_CHANNEL**: Logging driver (`stack`, `single`, `daily`, `slack`, etc.)
- **LOG_LEVEL**: `debug`, `info`, `warning`, `error`, `critical`
- **Local**: `debug` shows all logs
- **Production**: Use `error` or `critical`

---

## 🔍 Configuration Issues Found

### 1. APP_URL Mismatch
- **`.env.local`**: `http://localhost:3001`
- **`start-dev.sh`**: Runs on port `8000`
- **Fix**: Change `APP_URL=http://localhost:8000` in `.env.local`

### 2. Database Port Mismatch
- **`.env.local`**: `DB_PORT=3306`
- **Docker**: Exposes MySQL on port `3307`
- **Fix**: Change to `DB_PORT=3307` OR use `DB_HOST=db` with port `3306` (Docker internal)

### 3. Redis Port Mismatch (if using Redis)
- **`.env`**: `REDIS_PORT=6379`
- **Docker**: Exposes Redis on port `6380`
- **Fix**: Change to `REDIS_PORT=6380` for local development

---

## 📝 Recommended `.env.local` Configuration

Based on your `docker-compose.yml` and `start-dev.sh`:

```env
APP_NAME="Inventory Jovanni"
APP_ENV=local
APP_KEY=base64:xFOCHxqN1ALCeUH5ACAQgzO7y5VTky8rRI5x3t74z0w=
APP_DEBUG=true

# Fix: Match the port start-dev.sh uses
APP_URL=http://localhost:8000
VITE_DEV_SERVER_URL=http://localhost:5173

# Database - Option 1: Use Docker external port
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307              # ← Docker external port
DB_DATABASE=inventory_jovanni
DB_USERNAME=root
DB_PASSWORD=rootsecret    # ← Match docker-compose.yml

# Database - Option 2: Use Docker service name (if app runs in Docker)
# DB_HOST=db              # ← Docker service name
# DB_PORT=3306            # ← Docker internal port

# Redis (if using)
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6380           # ← Docker external port
REDIS_PASSWORD=null

# Rest of your config...
```

---

## 🐳 Docker Services Overview

### MySQL Container
- **Container**: `inventory-jovanni-db`
- **Image**: `mysql:8.0`
- **External Port**: `3307` → Internal: `3306`
- **Database**: `inventory_jovanni`
- **Root Password**: `rootsecret`
- **User**: `root` (password: `password` in docker-compose, but root uses `rootsecret`)

### Redis Container
- **Container**: `inventory-jovanni-redis`
- **Image**: `redis:7-alpine`
- **External Port**: `6380` → Internal: `6379`
- **Purpose**: Caching, queues, sessions (if configured)

### phpMyAdmin Container
- **Container**: `inventory-jovanni-phpmyadmin`
- **Image**: `phpmyadmin/phpmyadmin:latest`
- **Port**: `8081`
- **Access**: `http://localhost:8081`
- **Login**: Uses MySQL credentials

---

## 🚦 Running the Application - Step by Step

### 1. Start Docker Services
```bash
docker compose up -d
```

### 2. Wait for Services (or use start-dev.sh)
```bash
# Check MySQL
docker exec inventory-jovanni-db mysqladmin ping -h localhost -u root -prootsecret

# Check Redis
docker exec inventory-jovanni-redis redis-cli ping
```

### 3. Install Dependencies
```bash
composer install
npm install
```

### 4. Setup Laravel
```bash
# Generate app key (if needed)
php artisan key:generate

# Create storage symlink
php artisan storage:link

# Run migrations
php artisan migrate

# Clear caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
```

### 5. Start Development Server
```bash
# Option 1: Use the script
./start-dev.sh

# Option 2: Manual start
php artisan serve --host=0.0.0.0 --port=8000
```

### 6. Start Vite (for frontend assets)
```bash
# In a separate terminal
npm run dev
```

---

## 🔐 Security Notes

1. **Never commit `.env` or `.env.local`** to version control
2. **APP_DEBUG=false** in production
3. **APP_KEY** must be unique and secret
4. **Database passwords** should be strong
5. **SESSION_ENCRYPT=true** in production

---

## 🆘 Troubleshooting

### Database Connection Issues
```bash
# Test MySQL connection
mysql -h 127.0.0.1 -P 3307 -u root -prootsecret inventory_jovanni

# Check Docker logs
docker logs inventory-jovanni-db
```

### Port Already in Use
```bash
# Check what's using port 8000
lsof -i :8000

# Kill the process or change port in start-dev.sh
```

### Permission Issues
```bash
# Fix storage permissions
chmod -R 775 storage bootstrap/cache
chown -R $USER:www-data storage bootstrap/cache
```

---

## 📚 Additional Resources

- **Laravel Docs**: https://laravel.com/docs
- **Docker Compose**: https://docs.docker.com/compose/
- **Livewire**: https://livewire.laravel.com/docs

