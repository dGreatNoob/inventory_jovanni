# Self-Hosted Runner Setup for Staging Server

This guide will help you set up a GitHub Actions self-hosted runner on your staging server (192.168.100.26) to enable automated deployments.

## Prerequisites

- SSH access to staging server (192.168.100.26)
- Docker and Docker Compose installed on the server
- Git installed on the server
- The application repository cloned on the server

## Step 1: Connect to Staging Server

```bash
ssh user@192.168.100.26
```

## Step 2: Install Required Dependencies

```bash
# Update package list
sudo apt-get update

# Install required packages (if not already installed)
sudo apt-get install -y curl wget git docker.io docker-compose

# Add your user to docker group (if needed)
sudo usermod -aG docker $USER
newgrp docker  # or logout/login
```

## Step 3: Create Runner Directory

```bash
# Create directory for the runner
mkdir -p ~/actions-runner
cd ~/actions-runner
```

## Step 4: Download and Install Runner

1. **Get the registration token from GitHub:**
   - Go to: https://github.com/dGreatNoob/inventory_jovanni/settings/actions/runners
   - Click "New self-hosted runner"
   - Select "Linux" and "x64"
   - Copy the registration token shown

2. **Download the runner:**
   ```bash
   # Get latest runner version (check GitHub for latest)
   RUNNER_VERSION="2.311.0"  # Update this to latest version
   curl -o actions-runner.tar.gz -L https://github.com/actions/runner/releases/download/v${RUNNER_VERSION}/actions-runner-linux-x64-${RUNNER_VERSION}.tar.gz
   
   # Extract
   tar xzf actions-runner.tar.gz
   ```

3. **Configure the runner:**
   ```bash
   # Replace YOUR_REGISTRATION_TOKEN with the token from GitHub
   ./config.sh --url https://github.com/dGreatNoob/inventory_jovanni --token YOUR_REGISTRATION_TOKEN --name staging-runner --labels staging,self-hosted,linux,x64 --work _work
   ```

   **Important:** When prompted:
   - **Run as a service?** → Yes
   - **User account?** → Your user account (or root if needed)
   - **Service name?** → `actions.runner.dGreatNoob-inventory_jovanni.staging-runner`

## Step 5: Install as a Service

```bash
# Install the service
sudo ./svc.sh install

# Start the service
sudo ./svc.sh start

# Check status
sudo ./svc.sh status
```

## Step 6: Verify Runner is Online

1. Go to: https://github.com/dGreatNoob/inventory_jovanni/settings/actions/runners
2. You should see "staging-runner" with a green dot (online)

## Step 7: Prepare Deployment Directory

```bash
# Navigate to your application directory (adjust path as needed)
cd /path/to/inventory_jovanni

# Ensure it's on the staging branch
git checkout staging
git pull origin staging

# Create backups directory
mkdir -p backups

# Ensure scripts are executable
chmod +x scripts/deployment/*.sh
```

## Step 8: Configure Environment

Make sure your `.env` file is configured for staging:

```bash
# Edit .env file
nano .env

# Ensure these are set correctly:
# APP_ENV=staging
# DB_CONNECTION=mysql
# DB_HOST=db
# DB_DATABASE=inventory_jovanni_staging
# DB_USERNAME=root
# DB_PASSWORD=your_password
```

## Step 9: Test the Runner

1. **Push a test commit to staging branch:**
   ```bash
   git checkout staging
   echo "# Test" >> README.md
   git add README.md
   git commit -m "Test deployment"
   git push origin staging
   ```

2. **Check GitHub Actions:**
   - Go to: https://github.com/dGreatNoob/inventory_jovanni/actions
   - You should see the workflow running
   - The `deploy-to-staging` job should appear and run on your self-hosted runner

## Troubleshooting

### Runner Not Appearing Online

```bash
# Check service status
sudo ./svc.sh status

# View logs
sudo journalctl -u actions.runner.dGreatNoob-inventory_jovanni.staging-runner.service -f

# Restart service
sudo ./svc.sh restart
```

### Runner Can't Access Docker

```bash
# Ensure user is in docker group
sudo usermod -aG docker $USER
newgrp docker

# Test docker access
docker ps
```

### Deployment Script Fails

```bash
# Check runner logs
cd ~/actions-runner
tail -f _diag/Runner_*.log

# Test deployment script manually
cd /path/to/inventory_jovanni
bash scripts/deployment/deploy-staging.sh
```

### Runner Can't Access Git Repository

```bash
# Ensure git is configured
git config --global user.name "GitHub Actions"
git config --global user.email "actions@github.com"

# Test git access
cd /path/to/inventory_jovanni
git fetch origin staging
```

## Maintenance

### Update Runner

```bash
cd ~/actions-runner
sudo ./svc.sh stop
./config.sh remove --token YOUR_REMOVAL_TOKEN
# Download new version
# Reconfigure
sudo ./svc.sh install
sudo ./svc.sh start
```

### View Runner Logs

```bash
# Service logs
sudo journalctl -u actions.runner.dGreatNoob-inventory_jovanni.staging-runner.service -f

# Runner diagnostic logs
cd ~/actions-runner
tail -f _diag/Runner_*.log
```

## Security Considerations

1. **Firewall:** The runner makes outbound connections to GitHub. No inbound ports need to be opened.

2. **Secrets:** Never commit secrets. Use GitHub Secrets for sensitive data.

3. **Permissions:** The runner runs with the permissions of the user account. Consider using a dedicated user with minimal permissions.

4. **Network:** Since this is on your office network (192.168.100.26), ensure your firewall rules are appropriate.

## Next Steps

Once the runner is set up and tested:

1. The workflow will automatically deploy when you push to `staging` branch
2. Monitor deployments in GitHub Actions tab
3. **Production:** See [PRODUCTION_RUNNER_SETUP.md](./PRODUCTION_RUNNER_SETUP.md) to set up a self-hosted runner on the production server (main branch, `/var/www/inventory_jovanni`)

## Support

If you encounter issues:
1. Check runner logs: `sudo journalctl -u actions.runner.dGreatNoob-inventory_jovanni.staging-runner.service -f`
2. Check GitHub Actions logs in the repository
3. Verify Docker and Git are accessible from the runner user
