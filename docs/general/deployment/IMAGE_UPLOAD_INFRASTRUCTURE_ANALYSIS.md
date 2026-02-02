# Image Upload Infrastructure Analysis

## Architecture Overview

```
┌─────────────────┐     ┌──────────────────┐     ┌─────────────────┐
│   Browser       │     │  Cloud Tunnel    │     │  On-Prem        │
│   (User)        │────▶│  (ngrok/CF/etc)  │────▶│  Server         │
└─────────────────┘     └──────────────────┘     └────────┬────────┘
                                                          │
        Livewire wire:model="uploadImages"                 │
        → POST /livewire/upload-file (temp)                │
        → POST /livewire/update (submitImageUpload)        │
                                                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│  nginx:80                                                            │
│  - Receives HTTP (proxy if behind tunnel)                            │
│  - client_max_body_size: NOT SET → 1M default ⚠️                     │
│  - FastCGI to app:9000                                               │
└─────────────────────────────────────────────────────────────────────┘
                                                          │
                                                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│  PHP-FPM (app container)                                             │
│  - upload_max_filesize: 2M (PHP default) ⚠️                          │
│  - post_max_size: 8M (PHP default) ⚠️                                │
│  - php/local.ini exists but NOT loaded in Docker                     │
└─────────────────────────────────────────────────────────────────────┘
                                                          │
                                                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│  Laravel / Livewire                                                  │
│  1. Livewire receives file → storage/app/livewire-tmp/               │
│  2. submitImageUpload → ProductImageService::uploadImage()           │
│  3. Intervention Image resize → Storage::disk('public')              │
│     → storage/app/public/photos/{uuid}.jpg                           │
│  4. public/storage → symlink to storage/app/public                   │
└─────────────────────────────────────────────────────────────────────┘
                                                          │
                                                          ▼
┌─────────────────────────────────────────────────────────────────────┐
│  Storage                                                             │
│  - config/filesystems.php: 'public' disk → storage_path('app/public')│
│  - docker-compose: ./storage bind-mount (persists on host)           │
│  - Served by nginx: /storage/photos/* → try_files in public/         │
└─────────────────────────────────────────────────────────────────────┘
```

## Data Flow

1. **Client**: User selects images → Livewire `lw.upload('uploadImages', file)` → POST multipart to `/livewire/upload-file`
2. **Livewire temp**: File stored in `storage/app/livewire-tmp/` (or configured disk)
3. **Submit**: User clicks "Upload" → `submitImageUpload()` → `ProductImageService::uploadImage()` per file
4. **Processing**: Intervention Image resizes, encodes → `Storage::disk('public')->put('photos/...')`
5. **Serving**: `asset('storage/photos/x.jpg')` → nginx serves `public/storage/photos/x.jpg`

## Why It Works Locally but Not in Production

| Layer | Localhost (Dev) | Production (On-Prem + Tunnel) |
|-------|-----------------|-------------------------------|
| **Web server** | `php artisan serve` – no nginx, no body limit | nginx – `client_max_body_size` 1M default |
| **PHP** | Often uses `php/local.ini` or system php.ini with 40M | Docker PHP-FPM – default 2M/8M |
| **Request path** | Direct to PHP | Browser → Tunnel → nginx → PHP |
| **Timeouts** | Short local round-trip | Tunnel + network can be slower |
| **Storage** | Local filesystem | Bind mount – permissions matter |

## Root Cause Checklist

### 1. Nginx `client_max_body_size` (most likely)
- **Default**: 1M
- **Required**: ≥12M (Livewire default) for 10MB images
- **Fix**: Add `client_max_body_size 20M;` in nginx server block

### 2. PHP upload limits
- **upload_max_filesize**: Default 2M → need ≥12M
- **post_max_size**: Must be ≥ upload_max_filesize
- **Fix**: Load custom php.ini in Docker with 20M+

### 3. Cloud tunnel limits
- Cloudflare Tunnel: 100MB (free), 500MB (paid)
- ngrok: 1MB on free tier
- Tailscale Funnel: Check docs
- **Fix**: Ensure tunnel allows ≥20M or use direct connection for uploads

### 4. PHP-FPM timeouts
- **fastcgi_read_timeout**: 60s may be short for large uploads on slow links
- **Fix**: Increase to 120s or 180s for upload locations

### 5. Storage permissions
- `storage/app/public`, `storage/app/livewire-tmp` must be writable by `www-data`
- **Fix**: `chmod -R 775 storage` and ensure `www-data` owns files

### 6. Storage symlink
- `public/storage` must exist (from `php artisan storage:link`)
- **Fix**: Run `php artisan storage:link` in container

### 7. rsync deploy excludes
- Deploy sync uses `--delete`; `storage/app/public/photos/` may be wiped if not in source
- **Fix**: Exclude `storage/app/public` from sync or use `--exclude` to protect uploads

## Recommended Fixes (in order)

1. **nginx**: Add `client_max_body_size 20M;` and optionally increase timeouts for PHP ✅
2. **Dockerfile**: Copy and load `php/local.ini` with upload limits ✅
3. **Tunnel**: Confirm body size limit and timeout settings (see below)
4. **Deploy**: If using rsync with `--delete`, exclude `storage/app/public/photos` to avoid wiping uploads

## Cloud Tunnel Specifics

| Tunnel | Body limit | Timeout | Notes |
|--------|------------|---------|-------|
| **Cloudflare Tunnel** | 100MB (free) | Configurable | Generally fine |
| **ngrok free** | 1MB | - | Too small; upgrade or use direct |
| **ngrok paid** | 10MB+ | - | Check plan |
| **Tailscale Funnel** | Varies | - | Check docs |
| **Bore/frp** | Depends on config | - | No default limit |

**If uploads fail through tunnel:**
- Test direct to server IP:port (bypass tunnel) to isolate
- Check tunnel logs for 413 (Payload Too Large) or 504 (timeout)
- Consider uploading via direct VPN or local network for large files
