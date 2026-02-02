# CSV Import (Production)

One-time migration to import backup data from CSV files into the application.

## Prerequisites

- `backups_csv/` directory in project root containing:
  - `supplier.csv`
  - `location.csv`
  - `item.csv`
- Database migrations applied
- Run during low-traffic window (import can take 5–10 minutes)

## Production Run

```bash
php artisan db:seed --class=CsvImportSeeder --force
```

> **Note:** `--force` is required in production. Without it, the seeder will throw an error.

## What Gets Imported

| Seeder | Source | Target Table |
|--------|--------|--------------|
| CategorySeeder | (built-in) | categories |
| ProductColorSeeder | (built-in) | product_colors |
| SupplierCsvSeeder | supplier.csv | suppliers |
| LocationCsvSeeder | location.csv | branches |
| ItemCsvSeeder | item.csv | products |

## Idempotency

The import is idempotent: re-running will update existing records rather than duplicate them. Matching logic:

- **Suppliers:** by name
- **Branches:** by code
- **Products:** by SKU, barcode, or (product_number + product_color_id)

## Troubleshooting

- **"CSV file not found"** – Ensure `backups_csv/` exists and contains the three CSV files.
- **Lock timeout** – ItemCsvSeeder uses chunked transactions (500 rows). If timeouts persist, reduce `CHUNK_SIZE` in `ItemCsvSeeder`.
- **"Category not found"** – CategorySeeder runs first; if skipped, ensure Accessories and other categories exist.
