# 📊 Jovanni Database Structure - Complete Analysis

## 🎯 Database Overview

**Database Name:** `Jovanni_Backup`  
**Database Type:** SQL Server (MS SQL)  
**Purpose:** Inventory Management System  
**Size:** ~4.5GB (across 4 data files)  
**Compatibility Level:** SQL Server 2017+ (140)  

---

## 🏗️ Architecture Pattern

This database follows a **Multi-Entity, Multi-Location** architecture with:
- **Entity-based multi-tenancy** (soft multi-tenancy)
- **Audit trail on every table** (created, createdBy, updated, updatedBy)
- **Header-Detail pattern** (Hdr/Dtl tables)
- **Transaction posting system** (posted, cancelled flags)
- **Full-text search** enabled on key tables
- **Running balance calculations** (ItemLedger)

---

## 📋 Core Entity Hierarchy

### 1️⃣ **Entity (Organization/Tenant Level)**
The root level representing different organizations/tenants in the system.

```
Entity
├── id (bigint) - Primary key
├── created, createdBy, updated, updatedBy - Audit fields
├── expiry (date) - Account expiration
├── name, firstname, midname - Entity name components
├── contactNo, email, address, postcode - Contact information
├── remarks
├── pw (binary(64)) - Password hash
└── salt (uniqueidentifier) - Password salt
```

**Key Relationships:**
- All major tables have `entity` field for multi-tenancy
- Unique constraint on email
- Unique constraint on full name combination

---

## 🏢 Master Data Tables

### 2️⃣ **Location (Warehouses/Stores)**
Physical locations where inventory is stored or sold.

```sql
Location
├── id (bigint)
├── entity (bigint) - FK to Entity
├── name (nvarchar(100)) - Location name
├── code (nvarchar(50)) - Location code
├── category (nvarchar(100)) - Location type/category
├── address (nvarchar(200))
├── disabled (date) - Soft delete marker
└── remarks (nvarchar(500))
```

**Purpose:** 
- Warehouses
- Retail stores
- Distribution centers
- Manufacturing locations

---

### 3️⃣ **Item (Products/Inventory Items)**
The product catalog - heart of the inventory system.

```sql
Item
├── id (bigint)
├── entity (bigint) - FK to Entity
├── name (nvarchar(100)) - Product name
├── specs (nvarchar(200)) - Product specifications
├── category (nvarchar(100)) - Product category
├── sku (nvarchar(50)) - Stock Keeping Unit
├── barcode (nvarchar(50)) - Barcode identifier
├── uom (nvarchar(20)) - Unit of Measurement
├── supplier (bigint) - FK to Supplier
├── supplierCode (nvarchar(50)) - Supplier's product code
├── price (numeric(15,2)) - Selling price
├── priceNote (nvarchar(50)) - Price tier/notes
├── cost (numeric(15,2)) - Unit cost
├── shelfLife (smallint) - Days/months of shelf life
├── pictName (nvarchar(100)) - Product image filename
├── disabled (date) - Soft delete marker
└── remarks (nvarchar(500))
```

**Search Optimization:**
- Full-text indexed view: `ItemSearch`
- Short search view: `ItemSearchShort`
- Unique constraint on (entity, name, specs)

---

### 4️⃣ **Supplier**
Vendor/supplier management.

```sql
Supplier
├── id (bigint)
├── entity (bigint) - FK to Entity
├── name (nvarchar(100)) - Supplier name
├── email (nvarchar(100))
├── disabled (date)
└── remarks (nvarchar(500))
```

---

### 5️⃣ **Customer**
Customer database for sales tracking.

```sql
Customer
├── id (bigint)
├── entity (bigint) - FK to Entity
├── name (nvarchar(100)) - Customer name
├── contact (nvarchar(100)) - Contact person
├── address (nvarchar(200))
├── number (nvarchar(100)) - Phone number
├── email (nvarchar(100))
├── disabled (date)
└── remarks (nvarchar(500))
```

**Unique Constraint:** (entity, name)

---

## 📦 Inventory Movement Tables

### 6️⃣ **SupRecHdr / SupRecDtl (Supplier Receiving)**
Records goods received from suppliers.

**Header (SupRecHdr):**
```sql
├── id (bigint)
├── entity (bigint)
├── posted, postedBy, cancelled, cancelledBy - Transaction status
├── tranRef (nvarchar(20)) - Transaction reference number
├── location (bigint) - FK to Location (receiving location)
├── tranDate (date) - Transaction date
├── supplier (bigint) - FK to Supplier
├── DtlCount (smallint) - Number of detail lines
├── qty (int) - Total quantity
├── tcost (numeric(15,2)) - Total cost
└── remarks (nvarchar(300))
```

**Detail (SupRecDtl):**
```sql
├── id (bigint)
├── hdr (bigint) - FK to SupRecHdr
├── item (bigint) - FK to Item
├── qty (smallint) - Quantity received
├── ucost (numeric(15,2)) - Unit cost
└── remarks (nvarchar(300))
```

**Status Flow:** Empty → Open (has details) → Posted → [Cancelled]

---

### 7️⃣ **LocTraHdr / LocTraDtl (Location Transfer)**
Transfer inventory between locations.

**Header (LocTraHdr):**
```sql
├── id (bigint)
├── entity (bigint)
├── posted, cancelled - Transaction status
├── tranRef (nvarchar(20))
├── location (bigint) - FROM location
├── toLocation (bigint) - TO location
├── toLocationName (nvarchar(200)) - Denormalized name
├── tranDate (date)
├── DtlCount (smallint)
├── qty (int) - Total quantity
├── qtyLoaded (int) - Loaded quantity (for shipping)
└── remarks (nvarchar(300))
```

**Detail (LocTraDtl):**
```sql
├── id (bigint)
├── hdr (bigint) - FK to LocTraHdr
├── item (bigint) - FK to Item
├── qty (smallint)
├── qtyLoaded (smallint)
└── remarks (nvarchar(300))
```

---

### 8️⃣ **LocRecHdr / LocRecDtl (Location Receiving)**
Receive transfers from other locations.

**Header (LocRecHdr):**
```sql
├── id (bigint)
├── entity (bigint)
├── tranRef (nvarchar(20))
├── location (bigint) - TO location (receiving)
├── frLocation (bigint) - FROM location
├── ftLocationName (nvarchar(200)) - Denormalized name
├── tranDate (date)
├── qty, qtyLoaded
└── remarks
```

**Detail (LocRecDtl):**
```sql
├── hdr (bigint)
├── item (bigint)
├── qty (smallint)
├── qtyLoaded (smallint)
└── remarks
```

---

### 9️⃣ **LocIssHdr / LocIssDtl (Location Issuance)**
Issue inventory from location (consumption, waste, etc.).

**Header (LocIssHdr):**
```sql
├── id (bigint)
├── entity (bigint)
├── tranRef (nvarchar(20))
├── location (bigint) - Issuing location
├── tranDate (date)
├── tranType (nvarchar(20)) - Type of issuance
├── DtlCount, qty, qtyLoaded
├── tcost (numeric(15,2)) - Total cost
└── remarks
```

**Detail (LocIssDtl):**
```sql
├── hdr (bigint)
├── item (bigint)
├── qty (smallint)
├── ucost (numeric(15,2)) - Unit cost
├── IssuedTo (nvarchar(100)) - Recipient
├── qtyLoaded (smallint)
└── remarks
```

---

## 💰 Sales & Customer Tables

### 🔟 **CusSalHdr / CusSalDtl (Customer Sales)**
Sales transactions to customers.

**Header (CusSalHdr):**
```sql
├── id (bigint)
├── entity (bigint)
├── posted, cancelled - Transaction status
├── tranRef (nvarchar(20)) - Sales order number
├── location (bigint) - Selling location
├── tranDate (date)
├── customer (bigint) - FK to Customer
├── area (nvarchar(100)) - Sales area
├── sales (nvarchar(100)) - Salesperson
├── term (tinyint) - Payment terms
├── qty (smallint) - Total items
├── totalPrice (numeric(12,2))
├── totalDiscount (numeric(12,2))
├── totalTax (numeric(12,2))
├── netPrice (numeric(12,2))
├── DtlCount (smallint)
└── remarks (nvarchar(300))
```

**Detail (CusSalDtl):**
```sql
├── id (bigint)
├── hdr (bigint) - FK to CusSalHdr
├── item (bigint) - FK to Item
├── qty (smallint)
├── price (numeric(12,2)) - Unit price
├── totalPrice (numeric(12,2))
├── discount (numeric(5,2)) - Discount %
├── totalDiscount (numeric(12,2))
├── tax (numeric(5,2)) - Tax %
├── totalTax (numeric(12,2))
├── netPrice (numeric(12,2)) - Final price
├── priceNote (nvarchar(50)) - Price tier used
└── remarks (nvarchar(300))
```

**Calculations:**
- `totalPrice = qty × price`
- `totalDiscount = totalPrice × (discount / 100)`
- `totalTax = (totalPrice - totalDiscount) × (tax / 100)`
- `netPrice = totalPrice - totalDiscount + totalTax`

---

### 1️⃣1️⃣ **CusSalUpdateHdr / CusSalUpdateDtl (Price Updates)**
Batch update prices for items over a date range.

**Header (CusSalUpdateHdr):**
```sql
├── id (bigint)
├── entity (bigint)
├── tranRef (nvarchar(20))
├── location (bigint)
├── tranDate (date)
├── dateFrom, dateTo - Date range for price updates
├── DtlCount (smallint)
└── remarks
```

**Detail (CusSalUpdateDtl):**
```sql
├── hdr (bigint)
├── item (bigint)
├── price (numeric(12,2)) - New price
└── remarks
```

---

## 📊 Inventory Ledger System

### 1️⃣2️⃣ **ItemLedger (Inventory Movements Ledger)**
**The core of inventory tracking** - records every single movement.

```sql
ItemLedger
├── id (bigint)
├── entity (bigint)
├── location (bigint) - Where the movement occurred
├── item (bigint) - Which item
├── tranDate (date) - When
├── tranType (nvarchar(20)) - Type (Receive, Issue, Transfer, Sale, etc.)
├── tranID (bigint) - Reference to source transaction
├── qty (smallint) - Quantity change (+ or -)
├── runQty (smallint) - Running balance quantity
├── ucost (numeric(15,2)) - Unit cost at transaction
└── runUcost (numeric(15,2)) - Running average cost
```

**Clustered Index:** (entity, location, item, tranDate, id)

**Transaction Types:**
- `SupRec` - Supplier Receiving
- `LocTra` - Location Transfer (out)
- `LocRec` - Location Receiving (in)
- `LocIss` - Location Issuance (out)
- `CusSal` - Customer Sales (out)
- `AdjEnt` - Adjustment Entry

**Running Calculations:**
- `runQty` = Previous runQty + current qty
- `runUcost` = Weighted average cost calculation

---

### 1️⃣3️⃣ **ItemPriceLog (Price History)**
Historical log of price changes.

```sql
ItemPriceLog
├── id (bigint)
├── updated (datetime2(7))
├── updatedBy (bigint)
├── item (bigint)
├── price (numeric(15,2))
└── priceNote (nvarchar(50))
```

---

## 📈 Aggregation/Summary Tables

### 1️⃣4️⃣ **Ran_ItemLedgerMonthlySum**
Monthly summary of inventory movements.

```sql
├── id (bigint)
├── entity, location, item
├── yrmo (nvarchar(6)) - YYYYMM format
├── startQty - Starting quantity
├── startCost - Starting total cost
├── receiptQty - Total received
├── receiptCost
├── issueQty - Total issued
├── issueCost
├── endQty - Ending quantity
└── endCost - Ending total cost
```

---

### 1️⃣5️⃣ **Ran_ItemLedgerItemMonthlySum**
Item-level monthly summaries.

```sql
├── entity, item
├── yrmo (nvarchar(6))
├── startQty, startCost
├── receiptQty, receiptCost
├── issueQty, issueCost
├── endQty, endCost
```

---

### 1️⃣6️⃣ **Ran_ItemLocationTranDailySum**
Daily transaction summaries by location.

```sql
├── entity, location, item
├── tranDate (date)
├── volume - Transaction count
└── value - Transaction value
```

---

### 1️⃣7️⃣ **Ran_SalesProfitMonthlySum**
Monthly sales profitability analysis.

```sql
├── entity, item
├── yrmo (nvarchar(6))
├── qty - Quantity sold
├── totalPrice - Total selling price
├── totalDiscount
├── totalTax
├── netPrice - Net selling price
├── issueCost - Cost of goods sold
└── grossProfit - netPrice - issueCost
```

---

## 🔧 Supporting Tables

### 1️⃣8️⃣ **ItemClass**
Product classification/categorization.

```sql
ItemClass
├── id (bigint)
├── entity (bigint)
├── itemCount (smallint) - Number of items in class
├── name (nvarchar(100))
├── category (nvarchar(100))
├── disabled (date)
└── remarks
```

---

### 1️⃣9️⃣ **Contact**
Contact persons (separate from customers/suppliers).

```sql
Contact
├── id (bigint)
├── entity (bigint)
├── name (nvarchar(100))
├── contactNo (nvarchar(100))
├── email (nvarchar(100))
├── address (nvarchar(200))
├── disabled (date)
└── remarks
```

**Unique Constraint:** (entity, name)

---

### 2️⃣0️⃣ **Fund**
Financial funds or accounts.

```sql
Fund
├── id (bigint)
├── entity (bigint)
├── name (nvarchar(100))
├── balance (numeric(15,2))
├── category (nvarchar(100))
├── disabled (date)
└── remarks
```

---

### 2️⃣1️⃣ **FundLedger**
Financial fund transactions.

```sql
FundLedger
├── id (bigint)
├── entity, fund
├── tranDate (date)
├── tranType (nvarchar(20))
├── tranID (bigint)
├── amount (numeric(15,2))
├── runAmount (numeric(15,2)) - Running balance
└── remarks
```

---

### 2️⃣2️⃣ **Roles**
User roles for access control.

```sql
Roles
├── id (bigint)
├── created, updated
├── entity (bigint)
├── name (nvarchar(100))
├── category (nvarchar(100))
├── canViewData (bit)
├── canInsertData (bit)
├── canUpdateData (bit)
├── canDeleteData (bit)
├── canPostData (bit)
└── remarks
```

---

### 2️⃣3️⃣ **Account**
User accounts for system access.

```sql
Account
├── id (bigint)
├── expiry (date)
├── name, firstname, midname
├── contactNo, email, address, postcode
├── pw (binary(64)) - Password hash
├── salt (uniqueidentifier)
├── LoanInterestRate (numeric(5,2))
└── remarks
```

---

### 2️⃣4️⃣ **ExtraField**
Dynamic custom fields (EAV pattern).

```sql
ExtraField
├── id (bigint)
├── entity (bigint)
├── tableName (nvarchar(100)) - Target table
├── tableID (bigint) - Target record ID
├── columnName (nvarchar(100)) - Field name
└── columnValue (nvarchar(500)) - Field value
```

**Unique Constraint:** (entity, tableName, tableID, columnName)

**Usage:** Add custom fields to any table dynamically.

---

### 2️⃣5️⃣ **Entity_Log**
Audit log for entity changes.

```sql
Entity_Log
├── id (bigint)
├── timestamp (datetime2(7))
├── userID (bigint)
├── action (nvarchar(50))
└── details (nvarchar(max))
```

---

### 2️⃣6️⃣ **Ran_Logs**
System/process logs.

```sql
Ran_Logs
├── id (bigint)
├── timestamp (datetime2(7))
├── process (nvarchar(100))
├── status (nvarchar(50))
└── message (nvarchar(max))
```

---

## 🔍 Full-Text Search Views

The system uses **full-text indexed views** for fast searching:

### ItemSearch
Combines item details with supplier for comprehensive search.

### ItemSearchShort
Simplified search with just name, specs, category, SKU.

### SupRecHdrSearch
Search supplier receiving transactions.

### LocTraHdrSearch
Search location transfers.

### LocIssHdrSearch
Search location issuances.

### CusSalHdrSearch
Search customer sales.

### LocRecHdrSearch
Search location receivings.

---

## 🔄 Transaction Status Functions

SQL Server functions to determine transaction status:

- `ActualHdr_Status(@id)` → 'Empty'|'Open'|'Posted'|'Cancelled'
- `AdjEntHdr_Status(@id)` → Transaction status
- `BrochureHdr_Status(@id)` → Brochure status
- `CusSalHdr_Status(@id)` → Sales status
- `CusSalUpdateHdr_Status(@id)` → Price update status

---

## 📊 Data Organization Summary

### **Hierarchy:**
```
Entity (Multi-tenant)
  ├── Locations (Warehouses/Stores)
  │     ├── Items (Products)
  │     │     └── ItemLedger (All movements)
  │     └── Transactions
  │           ├── SupRec (Supplier Receiving)
  │           ├── LocTra (Transfers Out)
  │           ├── LocRec (Receiving Transfers)
  │           ├── LocIss (Issuances)
  │           └── CusSal (Sales)
  ├── Suppliers
  ├── Customers
  ├── Funds
  └── Users/Accounts
```

### **Transaction Flow:**
```
1. Supplier Receiving (SupRec)
   → Creates ItemLedger entry (type: SupRec, qty: +)
   → Updates running balance

2. Location Transfer (LocTra)
   → Creates ItemLedger entry at source (type: LocTra, qty: -)
   → Creates pending receipt at destination

3. Location Receiving (LocRec)
   → Creates ItemLedger entry at destination (type: LocRec, qty: +)
   → Completes transfer

4. Customer Sales (CusSal)
   → Creates ItemLedger entry (type: CusSal, qty: -)
   → Reduces inventory

5. Location Issuance (LocIss)
   → Creates ItemLedger entry (type: LocIss, qty: -)
   → For waste, consumption, etc.
```

### **Key Design Principles:**

1. **Audit Everything:** Every table has created/updated timestamps and user IDs

2. **Soft Deletes:** Uses `disabled` date field instead of hard deletes

3. **Transaction Integrity:** All transactions follow Header-Detail pattern with status flags

4. **Running Balances:** ItemLedger maintains running quantity and cost averages

5. **Multi-tenancy:** Every table scoped by `entity` for multi-organization support

6. **Full-Text Search:** Optimized search via indexed views

7. **Denormalization:** Some fields duplicated for performance (e.g., toLocationName)

8. **Aggregation Tables:** Pre-calculated summaries for reporting performance

---

## 🎯 Integration with Your Laravel System

### **Key Mapping:**

| SQL Server Table | Laravel Equivalent | Purpose |
|------------------|-------------------|---------|
| Entity | Organizations/Tenants | Multi-tenancy root |
| Location | departments/locations | Warehouse management |
| Item | supply_profiles | Product catalog |
| Supplier | suppliers | Vendor management |
| Customer | customers | Customer database |
| SupRecHdr/Dtl | purchase_orders + purchase_order_items | Purchase receiving |
| CusSalHdr/Dtl | sales_orders + sales_order_items | Sales transactions |
| ItemLedger | activity_log + stock_batches | Inventory tracking |
| LocTraHdr/Dtl | request_slips | Internal transfers |
| LocIssHdr/Dtl | stock_out transactions | Issuances |

### **Feature Parity:**

✅ **Already Implemented in Laravel:**
- Multi-location support
- Product catalog with SKU/barcodes
- Purchase order management
- Sales order management
- Customer and supplier management
- Activity logging
- Batch tracking

🔄 **Features to Add from SQL Server System:**
- Location-to-location transfers (LocTra/LocRec)
- Running balance calculations in ItemLedger style
- Price history logging (ItemPriceLog)
- Transaction status workflow (Empty→Open→Posted→Cancelled)
- Aggregation/summary tables for reporting
- Full-text search optimization
- Multi-entity/tenant support (if needed)
- Fund/financial account tracking

---

## 📝 Notes

- **Database is production-ready:** Contains 37,000+ lines of SQL with full constraints and indexes
- **Highly normalized:** Proper relationships with referential integrity
- **Performance optimized:** Clustered indexes on transaction tables
- **Scale ready:** Multi-file database design for large datasets
- **Comprehensive:** Covers full supply chain from purchase to sale

This SQL Server schema represents a **mature, enterprise-grade inventory management system** with years of production refinement.

