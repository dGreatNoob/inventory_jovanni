# Quick Deployment Guide - Staging and Production

## Overview

- **Staging:** Automated deployment via GitHub Actions self-hosted runner (staging server, e.g. 192.168.100.26). Push to `staging` branch.
- **Production:** Automated deployment via a second self-hosted runner on the production server (`/var/www/inventory_jovanni`). Push to `main` or run workflow with environment "production". See [PRODUCTION_RUNNER_SETUP.md](./PRODUCTION_RUNNER_SETUP.md) to set up the production runner.

## How It Works

1. **Push to `staging` branch** → Triggers workflow
2. **Tests run** → Ensures code quality
3. **Package built** → Creates deployment artifact
4. **Self-hosted runner** → Automatically deploys to staging server
5. **Database backup** → Created before deployment
6. **Migrations run** → Database updated automatically
7. **Containers restarted** → Application updated

## Manual Deployment (If Needed)

If you need to deploy manually:

```bash
# SSH into staging server
ssh user@192.168.100.26

# Navigate to project directory
cd /path/to/inventory_jovanni

# Run deployment script
bash scripts/deployment/deploy-staging.sh
```

## What Gets Deployed

- ✅ Database backup (automatic)
- ✅ Code pulled from `staging` branch
- ✅ Docker containers rebuilt/restarted
- ✅ Database migrations run
- ✅ Application cache cleared and rebuilt
- ✅ Health check performed

## Monitoring Deployments

1. **GitHub Actions:** https://github.com/dGreatNoob/inventory_jovanni/actions
2. **Server Logs:** `docker compose logs -f`
3. **Application:** http://192.168.100.26

## Rollback Procedure

If deployment fails:

```bash
# SSH into staging server
ssh user@192.168.100.26
cd /path/to/inventory_jovanni

# Stop containers
docker compose down

# Restore from backup (if needed)
# Check backups directory
ls -lh backups/

# Restore database (if needed)
bash scripts/database/restore-database.sh backups/backup_YYYYMMDD_HHMMSS.sql.gz

# Restart with previous code
git checkout <previous-commit>
docker compose up -d
```

## Troubleshooting

### Deployment Fails

1. Check GitHub Actions logs
2. SSH into server and check:
   ```bash
   docker compose ps
   docker compose logs app
   docker compose logs db
   ```

### Runner Offline

1. Check runner status: https://github.com/dGreatNoob/inventory_jovanni/settings/actions/runners
2. SSH into server:
   ```bash
   sudo systemctl status actions.runner.dGreatNoob-inventory_jovanni.staging-runner.service
   sudo systemctl restart actions.runner.dGreatNoob-inventory_jovanni.staging-runner.service
   ```

### Database Issues

```bash
# Check database container
docker compose ps db
docker compose logs db

# Test connection
docker compose exec app php artisan tinker
# Then: DB::connection()->getPdo();
```

## Backup Location

Backups are stored in: `./backups/` directory on the server.

Format: `backup_YYYYMMDD_HHMMSS.sql.gz`

Last 10 backups are kept automatically.
