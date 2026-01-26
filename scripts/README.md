# Scripts Directory

This directory contains utility scripts for managing the Inventory Jovanni application.

## Structure

```
scripts/
├── production/         # Production deployment scripts
├── database/          # Database management scripts
├── troubleshooting/   # Troubleshooting scripts (if needed)
└── setup-local.sh     # Alternative local setup script
```

## Scripts Overview

### Development (Run from project root)

- **`../start-dev.sh`** - Start the development environment
  - Starts Docker containers (MySQL, Redis, phpMyAdmin)
  - Installs dependencies if needed
  - Runs migrations
  - Starts Laravel on port 8000
  
- **`../stop-dev.sh`** - Stop the development environment
  - Stops all Docker containers

### Production Scripts

- **`production/deploy-production.sh`** - Full production deployment
  - Complete deployment pipeline with backups
  - Health checks and verification
  - Permission management
  - Image upload directory setup
  - Usage: `./scripts/production/deploy-production.sh`
  
- **`production/deploy-test-production.sh`** - Test production deployment
  - Similar to production deployment but for testing
  - Includes seeders for test data
  - Usage: `./scripts/production/deploy-test-production.sh`

### Database Scripts

- **`database/restore-database.sh`** - Restore database from backup
  - Restores database from SQL backup
  - Creates pre-restore backup automatically
  - Usage: `./scripts/database/restore-database.sh <backup_file>`

### Alternative Setup

- **`setup-local.sh`** - Alternative local setup (without Docker)
  - Full local environment setup
  - Installs PHP, Composer, Node.js
  - Sets up MySQL database on host
  - Usage: `./scripts/setup-local.sh`

## Current Architecture

The project uses a **host-based development** architecture:

- **Laravel Application**: Runs on host at `localhost:8000`
- **MySQL**: Docker container at `localhost:3307`
- **Redis**: Docker container at `localhost:6380`
- **phpMyAdmin**: Docker container at `localhost:8081`

## Quick Start

1. Start development environment:
   ```bash
   ./start-dev.sh
   ```

2. Access the application:
   - Laravel: http://localhost:8000
   - phpMyAdmin: http://localhost:8081

3. Stop development environment:
   ```bash
   ./stop-dev.sh
   ```

## Notes

- Docker-based troubleshooting scripts were removed as they were incompatible with the new host-based architecture
- The `quick-deploy.sh` script was removed as it was redundant with `deploy-production.sh`
- All scripts maintain their original functionality after being moved to the scripts folder

