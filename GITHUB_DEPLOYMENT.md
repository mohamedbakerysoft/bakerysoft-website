# GitHub deployment workflow for tools.bakerysoft.net

This repository now contains the Laravel tools platform under `tools-platform/`.

## Recommended workflow

1. Develop locally on your own machine
2. Test locally
3. Commit and push to GitHub
4. Raspberry Pi pulls the latest `main`
5. Run deployment script on the Raspberry Pi

## One-time local setup

If Git commands fail on macOS because of the Xcode license, run:

```bash
sudo xcodebuild -license accept
```

## One-time GitHub push

From the repository root:

```bash
git remote add origin <YOUR_GITHUB_REPO_URL>
git branch -M main
git add .
git commit -m "Build Arabic Laravel tools platform"
git push -u origin main
```

## One-time Raspberry Pi setup

Clone the GitHub repository onto the Raspberry Pi:

```bash
chmod +x scripts/setup_github_source_on_rpi.sh
REPO_URL=<YOUR_GITHUB_REPO_URL> ./scripts/setup_github_source_on_rpi.sh
```

This will create:

- source checkout: `/var/www/tools.bakerysoft.net/source`

The live deployed app remains:

- live app: `/var/www/tools.bakerysoft.net/platform`

## First deploy from the Git checkout on the Pi

SSH into the Pi:

```bash
ssh jarvis@jarvis.local
cd /var/www/tools.bakerysoft.net/source
git pull origin main
chmod +x scripts/server_deploy_tools_platform_from_repo.sh
./scripts/server_deploy_tools_platform_from_repo.sh
```

## Daily workflow

Local machine:

```bash
cd /path/to/bakerysoft-website
git add .
git commit -m "Describe your change"
git push origin main
```

Raspberry Pi:

```bash
ssh jarvis@jarvis.local
cd /var/www/tools.bakerysoft.net/source
git pull origin main
./scripts/server_deploy_tools_platform_from_repo.sh
```

## Optional GitHub Actions automation

This repository includes:

- `.github/workflows/deploy-tools-platform.yml`

To use it, add these GitHub Actions secrets:

- `TOOLS_PI_HOST`
- `TOOLS_PI_USER`
- `TOOLS_PI_SSH_KEY`

Then every push to `main` affecting `tools-platform/` can deploy automatically.

## Important note

Do not develop directly on the Raspberry Pi except for urgent hotfixes.
The correct path is:

- develop locally
- commit and push
- pull and deploy on the Pi
