# GitHub Actions Workflows Overview

This document explains what each GitHub Actions workflow does and whether they're necessary.

## Available Workflows

### 1. `tests.yml` - Automated Testing

**Purpose:** Runs your test suite on every push/PR to `dev` and `main` branches.

**What it does:**
- Sets up PHP 8.2, Node.js 22, and MySQL 8.0
- Installs dependencies
- Runs database migrations and seeding
- Builds frontend assets
- Runs all tests using Pest

**Is it necessary?**
- ✅ **Recommended** - Catches bugs before they reach production
- ✅ **Quality gate** - Ensures code changes don't break existing functionality
- ⚠️ **Can be disabled** - But not recommended for production code

**When it runs:**
- Push to `dev` or `main`
- Pull requests targeting `dev` or `main`

**How to disable:**
- Delete `.github/workflows/tests.yml`
- Or modify triggers to only run on specific branches

---

### 2. `lint.yml` - Code Style Linter

**Purpose:** Automatically formats code using Laravel Pint (PHP code formatter).

**What it does:**
- Sets up PHP 8.2
- Installs dependencies
- Runs `vendor/bin/pint` to format code
- (Optional) Auto-commits formatting changes

**Is it necessary?**
- ⚠️ **Optional** - Helps maintain consistent code style
- ✅ **Useful** - Prevents style debates in PRs
- ❌ **Can cause issues** - If composer.lock is out of sync

**When it runs:**
- Push to `dev` or `main`
- Pull requests targeting `dev` or `main`

**How to disable:**
- Delete `.github/workflows/lint.yml`
- Or comment out the workflow file

**Recommendation:**
- Keep it but make it **non-blocking** (don't require it to pass for merges)
- Or run it manually only when needed

---

### 3. `deploy.yml` - Build and Package for Deployment

**Purpose:** Creates deployment packages for staging/production.

**What it does:**
- Runs tests (unless skipped)
- Builds frontend assets
- Creates deployment package (zip file)
- Includes Docker files, scripts, and documentation
- Uploads as GitHub Actions artifact

**Is it necessary?**
- ✅ **Essential** - This is your deployment automation
- ✅ **Required** - For on-premise deployment workflow
- ❌ **Cannot disable** - This is your main deployment tool

**When it runs:**
- Manual trigger (workflow_dispatch)
- Git tags (`v*`, `staging-*`, `prod-*`)

**How to disable:**
- Don't trigger it manually
- Don't push tags
- But you'll lose deployment automation

---

## Workflow Recommendations

### For Active Development

**Keep all workflows** but make them non-blocking:
- Tests: Keep for quality assurance
- Linter: Keep but make optional (don't block PRs)
- Deploy: Keep (essential)

### For Minimal CI/CD

**Keep only deploy workflow:**
- Delete `lint.yml` (optional code formatting)
- Keep `tests.yml` but make it optional
- Keep `deploy.yml` (essential)

### For Maximum Quality

**Keep all workflows and make them required:**
- Tests: Required for PR merges
- Linter: Required for PR merges
- Deploy: Manual trigger only

---

## Fixing Common Issues

### Composer Lock File Errors

**Error:** `Your lock file does not contain a compatible set of packages`

**Solution:** Update composer.lock
```bash
composer update --lock
git add composer.lock
git commit -m "Update composer.lock"
```

**Or fix in workflow:**
The workflows now automatically update the lock file if install fails.

### Workflow Failing Unnecessarily

**Option 1: Make workflows optional**
- Don't require them to pass for PR merges
- Use them as warnings only

**Option 2: Fix the underlying issues**
- Update composer.lock
- Fix test failures
- Fix linting errors

**Option 3: Disable specific workflows**
- Delete or rename the workflow file
- Or modify triggers to never run

---

## Quick Reference

| Workflow | Purpose | Necessary? | Can Disable? |
|----------|---------|------------|--------------|
| `tests.yml` | Run tests | Recommended | Yes |
| `lint.yml` | Format code | Optional | Yes |
| `deploy.yml` | Build packages | Essential | No (for deployment) |

---

**Last Updated:** January 2026
