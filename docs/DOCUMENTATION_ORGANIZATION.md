# Documentation Organization

This document describes how documentation files are organized in the `docs/` folder.

## Organization Structure

### Root Level (`docs/`)
- **README.md** - Main documentation index
- **DOCUMENTATION_ORGANIZATION.md** - This file

### General Documentation (`docs/general/`)
System-wide documentation that applies across all modules.

#### Core Files
- `DEVELOPMENT_SETUP.md` - Development environment setup
- `TEST_SETUP.md` - Test setup and configuration guide
- `ENV_VARIABLES_GUIDE.md` - Environment variables configuration
- `start_dev_analysis_report.md` - Development startup analysis
- `MCP_GITHUB_SETUP.md` - MCP GitHub integration setup
- `WSL-PORT-FORWARDING.md` - WSL port forwarding configuration

#### Subdirectories
- **testing/** - Testing documentation
  - `TEST_FAILURES_ANALYSIS.md` - Test failure analysis and fixes
  - `TEST_DATABASE_SETUP.md` - Test database setup instructions
  
- **deployment/** - Deployment and DevOps documentation
  - `DEVOPS_IMPROVEMENTS.md` - DevOps improvements summary
  - `PRODUCTION_READINESS.md` - Production readiness checklist
  - `PRODUCTION_DEPLOYMENT_GUIDE.md` - Production deployment guide
  - `LOCAL_DEPLOYMENT_GUIDE.md` - Local deployment guide
  - `DOCKER_DEPLOYMENT.md` - Docker deployment documentation
  - `DOCKER_DEPLOYMENT_GUIDE.md` - Comprehensive Docker guide
  - `PRODUCTION_PERMISSION_GUIDE.md` - Production permissions setup

- **troubleshooting/** - Troubleshooting guides
  - `COMPOSER_TROUBLESHOOTING.md` - Composer troubleshooting

### Module-Specific Documentation

#### Product Management (`docs/product-management/`)
- Module documentation, implementation plans, barcode system, category hierarchy
- **Recently moved:** `product_form_reorder_plan.md`, `product_supplier_code_rename_plan.md`

#### Allocation System (`docs/allocation-system/`)
- Allocation workflow documentation
- **Recently moved:** `allocation_workflow_flowchart.md`

#### Database Cleanup (`docs/db_cleanup/`)
- Database structure analysis and cleanup documentation
- **Recently moved:** `DATABASE_STRUCTURE_ANALYSIS.md`

#### Project Management (`docs/project-management/`)
- Project guidelines and refactoring plans
- **branches/** - Branch management documentation
  - `BRANCH_READY_FOR_MERGE.md` - Branch merge readiness
  - `BRANCH_UPDATE_NOTICE.md` - Branch update notifications

## File Movement Summary

The following files were moved from the repository root to organized locations:

### From Root → `docs/general/testing/`
- `TEST_FAILURES_ANALYSIS.md`
- `TEST_DATABASE_SETUP.md`

### From Root → `docs/general/deployment/`
- `DEVOPS_IMPROVEMENTS.md`
- `PRODUCTION_PERMISSION_GUIDE.md`
- `DOCKER_DEPLOYMENT.md`
- `DOCKER_DEPLOYMENT_GUIDE.md`
- `PRODUCTION_DEPLOYMENT_GUIDE.md`
- `LOCAL_DEPLOYMENT_GUIDE.md`
- `PRODUCTION_READINESS.md`

### From Root → `docs/general/troubleshooting/`
- `COMPOSER_TROUBLESHOOTING.md`

### From Root → `docs/general/`
- `start_dev_analysis_report.md`
- `MCP_GITHUB_SETUP.md`
- `WSL-PORT-FORWARDING.md`
- `ENV_VARIABLES_GUIDE.md`

### From Root → `docs/project-management/branches/`
- `BRANCH_READY_FOR_MERGE.md`
- `BRANCH_UPDATE_NOTICE.md`

### From Root → `docs/allocation-system/`
- `allocation_workflow_flowchart.md`

### From Root → `docs/db_cleanup/`
- `DATABASE_STRUCTURE_ANALYSIS.md`

### From `docs/` → `docs/product-management/`
- `product_form_reorder_plan.md`
- `product_supplier_code_rename_plan.md`

## Finding Documentation

### By Topic

**Development Setup:**
- `docs/general/DEVELOPMENT_SETUP.md`
- `docs/general/ENV_VARIABLES_GUIDE.md`

**Testing:**
- `docs/general/TEST_SETUP.md`
- `docs/general/testing/TEST_FAILURES_ANALYSIS.md`
- `docs/general/testing/TEST_DATABASE_SETUP.md`

**Deployment:**
- `docs/general/deployment/PRODUCTION_DEPLOYMENT_GUIDE.md`
- `docs/general/deployment/DOCKER_DEPLOYMENT_GUIDE.md`
- `docs/general/deployment/LOCAL_DEPLOYMENT_GUIDE.md`

**Troubleshooting:**
- `docs/general/troubleshooting/COMPOSER_TROUBLESHOOTING.md`

**Module Documentation:**
- Check module-specific folders (e.g., `docs/product-management/`, `docs/allocation-system/`)

## Guidelines for Adding New Documentation

1. **Module-specific docs** → Place in appropriate module folder
2. **General/system-wide docs** → Place in `docs/general/`
3. **Deployment docs** → Place in `docs/general/deployment/`
4. **Testing docs** → Place in `docs/general/testing/`
5. **Troubleshooting** → Place in `docs/general/troubleshooting/`
6. **Project management** → Place in `docs/project-management/`

## Last Updated

January 2026 - Documentation reorganization completed
