# Branch Testing Strategy

This document outlines the recommended testing strategy for different branches in the inventory_jovanni project.

## Branch Overview

- **`dev`** - Development branch for active feature development
- **`staging`** - Pre-production testing branch (if exists) or staging environment via tags
- **`main`** - Production-ready code

## Recommended Testing Strategy

### 🟢 `dev` Branch (Development)

**Purpose:** Catch bugs early during active development

**Tests to Run:**
- ✅ **All Feature Tests** - Full test suite
- ✅ **All Unit Tests** - Complete coverage
- ✅ **Database Migrations** - Ensure migrations work
- ✅ **Asset Building** - Verify frontend builds correctly
- ⚠️ **Linter** - Optional (can be non-blocking)

**Workflow Configuration:**
```yaml
# tests.yml should run on dev
on:
  push:
    branches: [dev, main]
  pull_request:
    branches: [dev, main]
```

**Rationale:**
- Developers need fast feedback
- Catch integration issues early
- Prevent broken code from reaching staging

**Current Status:** ✅ Configured correctly

---

### 🟡 Staging Branch/Environment

**Purpose:** Final validation before production

**Tests to Run:**
- ✅ **Full Test Suite** - All tests must pass
- ✅ **Smoke Tests** - Critical path verification
- ✅ **Integration Tests** - End-to-end workflows
- ✅ **Asset Building** - Production build verification
- ✅ **Deployment Package** - Verify package creation
- ❌ **Linter** - Optional (usually not needed at this stage)

**Workflow Configuration:**
```yaml
# deploy.yml triggers on staging tags
on:
  push:
    tags:
      - 'staging-*'
  workflow_dispatch:
    inputs:
      environment:
        options: [staging, production]
```

**Rationale:**
- Last quality gate before production
- Verify deployment artifacts
- Test production-like environment

**Current Status:** ✅ Configured via tags (`staging-*`)

---

### 🔴 `main` Branch (Production)

**Purpose:** Ensure production code is always stable

**Tests to Run:**
- ✅ **Full Test Suite** - All tests must pass
- ✅ **Critical Path Tests** - Essential functionality
- ✅ **Regression Tests** - Prevent breaking changes
- ✅ **Deployment Verification** - Package integrity
- ⚠️ **Linter** - Optional (code style already established)

**Workflow Configuration:**
```yaml
# tests.yml runs on main
# deploy.yml triggers on production tags
on:
  push:
    tags:
      - 'prod-*'
      - 'v*'
```

**Rationale:**
- Production stability is critical
- Prevent regressions
- Ensure deployment readiness

**Current Status:** ✅ Configured correctly

---

## Current Workflow Setup

### Tests Workflow (`tests.yml`)

**Runs on:**
- Push to `dev` ✅
- Push to `main` ✅
- PRs to `dev` ✅
- PRs to `main` ✅

**What it does:**
1. Sets up PHP 8.3, Node.js 22, MySQL 8.0
2. Installs dependencies
3. Runs migrations and seeding
4. Builds assets
5. Runs full test suite

**Recommendation:** ✅ Keep as-is - Good coverage for dev and main

---

### Deploy Workflow (`deploy.yml`)

**Runs on:**
- Manual trigger (workflow_dispatch) ✅
- Git tags: `staging-*` ✅
- Git tags: `prod-*` ✅
- Git tags: `v*` ✅

**What it does:**
1. Runs tests (unless skipped)
2. Builds frontend assets
3. Creates deployment package
4. Uploads artifact

**Recommendation:** ✅ Keep as-is - Perfect for staging/production

---

### Linter Workflow (`lint.yml`)

**Runs on:**
- Push to `dev` ✅
- Push to `main` ✅
- PRs to `dev` ✅
- PRs to `main` ✅

**What it does:**
1. Formats code with Laravel Pint
2. (Optional) Auto-commits changes

**Recommendation:** 
- ⚠️ Make non-blocking (don't require for PR merges)
- Or disable if causing issues

---

## Recommended Test Coverage by Branch

| Branch | Unit Tests | Feature Tests | Integration | Smoke Tests | Linter | Deploy Package |
|--------|-----------|--------------|-------------|-------------|--------|----------------|
| `dev` | ✅ All | ✅ All | ✅ All | ✅ Critical | ⚠️ Optional | ❌ No |
| `staging` | ✅ All | ✅ All | ✅ All | ✅ Critical | ❌ No | ✅ Yes |
| `main` | ✅ All | ✅ All | ✅ All | ✅ Critical | ⚠️ Optional | ✅ Yes |

---

## Test Types Explained

### Unit Tests
- Test individual components in isolation
- Fast execution
- Should run on all branches

### Feature Tests
- Test complete workflows
- Database interactions
- Should run on all branches

### Integration Tests
- Test multiple components together
- End-to-end scenarios
- Critical for staging and main

### Smoke Tests
- Quick verification of critical paths
- Fast feedback
- Essential for staging/main

---

## Workflow Optimization Recommendations

### For `dev` Branch

**Current:** ✅ Good
- Full test suite runs
- Fast feedback for developers
- Catches issues early

**Optional Improvements:**
- Add test coverage reporting
- Run tests in parallel for faster execution
- Add test result summaries

### For Staging (via tags)

**Current:** ✅ Good
- Tests run before deployment package creation
- Full validation before staging deployment

**Optional Improvements:**
- Add staging-specific environment tests
- Verify staging database migrations
- Test staging configuration

### For `main` Branch

**Current:** ✅ Good
- Tests run on every push
- Deployment packages created on tags

**Optional Improvements:**
- Require tests to pass before merging PRs
- Add production smoke tests
- Verify production configuration

---

## Quick Reference

### Running Tests Locally

```bash
# Run all tests
./vendor/bin/pest

# Run specific test suite
./vendor/bin/pest --testsuite=Feature
./vendor/bin/pest --testsuite=Unit

# Run specific test
./vendor/bin/pest --filter "product can be created"
```

### Triggering Workflows

**Tests:**
- Automatically on push/PR to `dev` or `main`
- No manual trigger needed

**Deploy:**
- Manual: GitHub Actions → "Build and Package for Deployment" → Run workflow
- Automatic: Push tag `staging-v1.0.0` or `prod-v1.0.0`

**Linter:**
- Automatically on push/PR to `dev` or `main`
- Can be disabled if not needed

---

## Summary

### ✅ Current Setup (Recommended)

- **`dev`**: Full test suite ✅
- **`staging`**: Full test suite + deployment package ✅
- **`main`**: Full test suite + deployment package ✅

### 🎯 Your Current Configuration is Good!

Your workflows are already configured optimally:
- Tests run on `dev` and `main` ✅
- Deployment packages created for staging/production ✅
- Linter runs but can be made optional ✅

**No changes needed** - your current setup follows best practices!

---

**Last Updated:** January 2026
