# Self-Hosted Runner Setup for Production

This guide sets up a GitHub Actions self-hosted runner on your **production server** so that pushes to `main` (or manual runs with environment "production") automatically deploy the app at `/var/www/inventory_jovanni`.

## Prerequisites

- Access to the production server (e.g. via AnyDesk or SSH)
- Docker and Docker Compose installed
- Git installed
- App already cloned at `/var/www/inventory_jovanni` with `.env` configured
- DNS/network working (e.g. `ping github.com` succeeds)

## 1. Install dependencies (if needed)

```bash
sudo apt-get update
sudo apt-get install -y curl wget git docker.io docker-compose
sudo usermod -aG docker $USER
# Log out and back in (or run newgrp docker) so docker runs without sudo
```

## 2. Create runner directory

Use a dedicated directory (not inside the app repo):

```bash
mkdir -p ~/actions-runner
cd ~/actions-runner
```

## 3. Get the runner from GitHub

1. Open: **https://github.com/dGreatNoob/inventory_jovanni/settings/actions/runners**
2. Click **"New self-hosted runner"**
3. Choose **Linux** and **x64**
4. Copy the **registration token** shown (it expires quickly)

## 4. Download and extract the runner

Use the **exact URL** shown on the GitHub runner page, or:

```bash
# Replace with the version shown on the GitHub runner setup page
RUNNER_VERSION="2.311.0"
curl -o actions-runner.tar.gz -L "https://github.com/actions/runner/releases/download/v${RUNNER_VERSION}/actions-runner-linux-x64-${RUNNER_VERSION}.tar.gz"
tar xzf actions-runner.tar.gz
```

## 5. Configure the runner (production)

Run (replace `YOUR_REGISTRATION_TOKEN` with the token from step 3):

```bash
./config.sh --url https://github.com/dGreatNoob/inventory_jovanni \
  --token YOUR_REGISTRATION_TOKEN \
  --name production-runner \
  --labels production,self-hosted,linux,x64 \
  --work _work
```

When prompted:

- **Run as a service?** → **Yes**
- **User account?** → The user that owns `/var/www/inventory_jovanni` and can run Docker (e.g. `jovanni`)
- **Service name?** → Accept default: `actions.runner.dGreatNoob-inventory_jovanni.production-runner`

## 6. Install and start the service

```bash
sudo ./svc.sh install
sudo ./svc.sh start
sudo ./svc.sh status
```

You should see the service active.

## 7. Verify runner in GitHub

1. Go to: **https://github.com/dGreatNoob/inventory_jovanni/settings/actions/runners**
2. You should see **production-runner** with a green dot (Idle).

## 8. Prepare the app directory

The deployment script runs from the repo checkout and then operates on `/var/www/inventory_jovanni`. Ensure:

```bash
cd /var/www/inventory_jovanni
git remote -v   # should point to github.com:dGreatNoob/inventory_jovanni
# Ensure .env exists and is configured for production
ls -la .env
# Runner user must be able to read/write this directory and run docker
```

**Important:** Your `.env` file must have Docker-compatible database settings:
```bash
# In .env file, ensure these are set for Docker:
DB_HOST=db              # NOT 127.0.0.1 or localhost (use Docker service name)
DB_PORT=3306            # NOT 3307 (use Docker internal port)
DB_USERNAME=root        # Must be root for Docker MySQL (jovanni user only exists on first DB init)
DB_PASSWORD=rootsecret  # Must match MYSQL_ROOT_PASSWORD / docker-compose
```

The deployment script will automatically set these for Docker. Using `DB_USERNAME=jovanni` (from .env.example) causes "Connection refused" because that user may not exist in the container.

## 9. When deployments run

- **Push to `main`** → workflow runs tests, builds package, then **deploy-to-production** runs on the production runner (pull, composer, npm build, docker up).
- **Manual run** → Actions → "Build and Package for Deployment" → Run workflow, choose **Environment: production** → production runner runs the deploy.
- **Tag `prod-*`** → same as manual production.

## 10. Ensure Docker Auto-Starts on Boot

To ensure your app automatically comes back online after a server restart:

**1. Enable Docker service to start on boot:**
```bash
sudo systemctl enable docker
sudo systemctl enable docker.socket
```

**2. Verify Docker starts automatically:**
```bash
sudo systemctl is-enabled docker
# Should output: enabled
```

**3. Test auto-restart behavior:**
```bash
# Restart Docker (containers should auto-restart due to restart: always)
sudo systemctl restart docker

# Check containers are running
cd /var/www/inventory_jovanni
docker compose -f docker-compose.prod.yml ps
```

**How it works:**
- Your `docker-compose.prod.yml` has `restart: always` for all services
- When Docker starts (after reboot), it automatically restarts containers with `restart: always`
- Containers start in dependency order (db → redis → app → nginx)
- Your app will be accessible once all containers are healthy

**Note:** The first time after reboot, containers may take 30-60 seconds to fully start (especially database initialization).

## 11. Troubleshooting

**Runner offline**

```bash
cd ~/actions-runner
sudo ./svc.sh status
sudo ./svc.sh restart
sudo journalctl -u actions.runner.dGreatNoob-inventory_jovanni.production-runner.service -f
```

**Deploy script fails**

```bash
# Run the script manually as the runner user
cd /var/www/inventory_jovanni
bash /path/to/scripts/deployment/deploy-production.sh
# Or from a checkout:
PROJECT_DIR=/var/www/inventory_jovanni bash scripts/deployment/deploy-production.sh
```

**Permission denied on /var/www/inventory_jovanni**

The user that runs the runner service must own or have write access to `/var/www/inventory_jovanni` and be in the `docker` group:

```bash
sudo chown -R jovanni:jovanni /var/www/inventory_jovanni
sudo usermod -aG docker jovanni
```

**Containers don't auto-start after reboot**

Ensure Docker service is enabled:
```bash
sudo systemctl enable docker
sudo systemctl enable docker.socket
sudo systemctl status docker
```

Verify containers have `restart: always` in `docker-compose.prod.yml` (they already do).

**Container restart loop (app container keeps restarting)**

Check container logs to see why it's failing:
```bash
cd /var/www/inventory_jovanni
docker compose -f docker-compose.prod.yml logs app
# Or last 50 lines:
docker compose -f docker-compose.prod.yml logs --tail=50 app
```

Common causes:
- **Database connection issues** - Most common! Check `.env` file:
  ```bash
  # WRONG (for Docker):
  DB_HOST=127.0.0.1
  DB_PORT=3307
  
  # CORRECT (for Docker):
  DB_HOST=db
  DB_PORT=3306
  ```
  The deployment script fixes this automatically, but verify your `.env` has `DB_HOST=db` not `127.0.0.1`.
- Missing APP_KEY (run: `docker compose -f docker-compose.prod.yml exec app php artisan key:generate`)
- Permission issues on storage/ directory
- Missing dependencies or build errors

**"Connection refused" database errors**

If you see `SQLSTATE[HY000] [2002] Connection refused` (or log shows `'jovanni'` as DB user):
1. **Use root for Docker:** In `.env` set `DB_USERNAME=root` and `DB_PASSWORD=rootsecret`. The MySQL container always has root; a custom user (e.g. jovanni) is only created on first DB volume init, so Laravel must use root.
   ```bash
   sed -i 's/^DB_USERNAME=.*/DB_USERNAME=root/' .env
   sed -i 's/^DB_PASSWORD=.*/DB_PASSWORD=rootsecret/' .env
   docker compose -f docker-compose.prod.yml exec app php artisan config:clear
   docker compose -f docker-compose.prod.yml restart app
   ```
2. Check `.env` has `DB_HOST=db` (Docker service name), not `127.0.0.1`
3. Verify database container is running: `docker compose -f docker-compose.prod.yml ps db`
4. Test: `docker compose -f docker-compose.prod.yml exec app php artisan tinker --execute="DB::connection()->getPdo(); echo 'DB OK';"`

**SSL/HTTPS error when accessing**

The app runs on HTTP (port 80), not HTTPS. If you see SSL errors:
- Use `http://` not `https://` (e.g., `http://192.168.0.100`)
- Clear browser cache or try incognito mode
- Some browsers auto-redirect to HTTPS - disable this or use HTTP explicitly

**500 Server Error when accessing the app**

1. **Check Laravel logs** (most important):
   ```bash
   cd /var/www/inventory_jovanni
   # From host:
   tail -100 storage/logs/laravel.log
   # Or from inside app container:
   docker compose -f docker-compose.prod.yml exec app cat storage/logs/laravel.log | tail -100
   ```
   The log will show the real exception (e.g. missing APP_KEY, permission denied, DB error).

2. **Common causes and fixes:**
   - **Missing or empty APP_KEY**
     ```bash
     docker compose -f docker-compose.prod.yml exec app php artisan key:generate --force
     ```
   - **Wrong APP_URL** – Set in `.env` to your actual URL (e.g. `APP_URL=http://192.168.0.100` or your domain).
   - **Storage/cache permissions**
     ```bash
     docker compose -f docker-compose.prod.yml exec app chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
     docker compose -f docker-compose.prod.yml exec app chmod -R 775 /var/www/storage /var/www/bootstrap/cache
     ```
   - **Config cache out of date**
     ```bash
     docker compose -f docker-compose.prod.yml exec app php artisan config:clear
     docker compose -f docker-compose.prod.yml exec app php artisan config:cache
     ```
   - **Session/cache driver (Redis)** – If Redis is down, set in `.env`: `CACHE_STORE=file` and `SESSION_DRIVER=file` temporarily to test.

3. **Temporarily enable debug** (only for debugging, then turn off):
   In `.env`: `APP_DEBUG=true` then reload. You’ll see the error on screen. Set back to `APP_DEBUG=false` when done.

**App not accessible from other devices**

1. Check firewall allows port 80:
   ```bash
   sudo ufw status
   sudo ufw allow 80/tcp
   ```

2. Verify nginx is listening on all interfaces (it is: `listen 0.0.0.0:80`)

3. Check server IP:
   ```bash
   hostname -I
   ```

4. Test from another device: `http://SERVER_IP` (replace SERVER_IP with actual IP)

## Summary

| Item        | Value                          |
|------------|---------------------------------|
| Runner name| `production-runner`             |
| Labels     | `production`, `self-hosted`, `linux`, `x64` |
| App path   | `/var/www/inventory_jovanni`    |
| Compose    | `docker-compose.prod.yml`       |
| Branch     | `main`                          |

After setup, push to `main` or run the workflow with environment "production" to deploy automatically on this server.
