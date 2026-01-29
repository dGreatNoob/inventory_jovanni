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

## 9. When deployments run

- **Push to `main`** → workflow runs tests, builds package, then **deploy-to-production** runs on the production runner (pull, composer, npm build, docker up).
- **Manual run** → Actions → "Build and Package for Deployment" → Run workflow, choose **Environment: production** → production runner runs the deploy.
- **Tag `prod-*`** → same as manual production.

## 10. Troubleshooting

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

## Summary

| Item        | Value                          |
|------------|---------------------------------|
| Runner name| `production-runner`             |
| Labels     | `production`, `self-hosted`, `linux`, `x64` |
| App path   | `/var/www/inventory_jovanni`    |
| Compose    | `docker-compose.prod.yml`       |
| Branch     | `main`                          |

After setup, push to `main` or run the workflow with environment "production" to deploy automatically on this server.
