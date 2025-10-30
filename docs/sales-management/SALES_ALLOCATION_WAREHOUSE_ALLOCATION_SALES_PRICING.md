# Sales & Warehouse Allocation and Sales Pricing Documentation

## Overview

This document provides comprehensive documentation for the Sales & Warehouse Allocation system and Sales Pricing functionality implemented in the inventory management system.

## Table of Contents

1. [Sales & Warehouse Allocation](#sales--warehouse-allocation)
   - [Process and Workflow](#process-and-workflow)
   - [Database Schema](#database-schema)
   - [Models](#models)
   - [Components](#components)
   - [Routes](#routes)
   - [Usage](#usage)

2. [Sales Pricing](#sales-pricing)
   - [Process and Workflow](#process-and-workflow-1)
   - [Database Schema](#database-schema-1)
   - [Models](#models-1)
   - [Components](#components-1)
   - [Routes](#routes-1)
   - [Usage](#usage-1)

3. [Sales Profile](#sales-profile)
   - [Process and Workflow](#process-and-workflow-2)
   - [Database Schema](#database-schema-2)
   - [Models](#models-2)
   - [Components](#components-2)
   - [Routes](#routes-2)
   - [Usage](#usage-2)

---

## Sales & Warehouse Allocation

### Process and Workflow

The Sales & Warehouse Allocation system provides separate management interfaces for sales and warehouse allocations. This allows organizations to categorize and manage different types of allocations based on their business needs.

**Key Processes:**
1. **Allocation Creation**: Users can create allocations with names and descriptions
2. **Type-based Organization**: Allocations are categorized as either 'sales' or 'warehouse'
3. **CRUD Operations**: Full create, read, update, delete functionality
4. **Search and Filter**: Real-time search across allocation names and descriptions
5. **Pagination**: Efficient handling of large datasets

**Workflow:**
1. User navigates to Setup → Allocations
2. Selects either Sales or Warehouse allocation type
3. Creates new allocations or manages existing ones
4. System validates input and prevents duplicates
5. Changes are saved with audit trails

### Database Schema

#### allocations Table
- `id`: Primary key
- `name`: Allocation name (unique within type)
- `description`: Optional description
- `type`: Enum ('sales', 'warehouse')
- `created_at`, `updated_at`: Timestamps

### Models

#### Allocation Model
- Fillable: name, description, type
- Casts: type as string
- Relationships: None (standalone entity)

### Components

#### Sales Allocation Component
- Manages sales-specific allocations
- Filters by type = 'sales'
- Inherits standard CRUD functionality

#### Warehouse Allocation Component
- Manages warehouse-specific allocations
- Filters by type = 'warehouse'
- Inherits standard CRUD functionality

### Routes

- `/Setup/Allocation/Sales` → Sales allocation management
- `/Setup/Allocation/Warehouse` → Warehouse allocation management

### Usage

#### Navigation
1. Go to Setup → Allocations in sidebar
2. Choose Sales or Warehouse tab
3. Use collapsible cards for creation/management

#### Operations
- **Create**: Fill name and description, submit
- **Edit**: Click edit button, modify details
- **Delete**: Click delete, confirm action
- **Search**: Type in search box to filter

---

## Sales Pricing

### Process and Workflow

The Sales Pricing system enables flexible pricing strategies with hierarchical rules. Prices can be set globally, by branch, or by specific agents, with the most specific rule taking precedence.

**Key Processes:**
1. **Hierarchical Pricing**: Global → Branch → Agent specificity
2. **Date-based Validity**: Effective and expiry dates for price rules
3. **Bulk Operations**: Set multiple prices efficiently
4. **Price History**: Track pricing changes over time
5. **Dynamic Application**: System automatically selects appropriate price

**Workflow:**
1. Navigate to Sales Management → Sales Price
2. Select product and optional branch/agent
3. Set price, effective dates, and notes
4. System validates and saves pricing rule
5. During sales, system looks up applicable price

### Database Schema

#### sales_prices Table
- `id`: Primary key
- `product_id`: Foreign key to products
- `branch_id`: Optional foreign key to branches
- `agent_id`: Optional foreign key to agents
- `price`: Decimal price value
- `effective_date`: When price becomes active
- `expiry_date`: Optional expiry date
- `pricing_note`: Optional notes
- `created_at`, `updated_at`: Timestamps

### Models

#### SalesPrice Model
- Fillable: product_id, branch_id, agent_id, price, effective_date, expiry_date, pricing_note
- Casts: price as decimal, dates as date objects
- Relationships: belongsTo Product, Branch, Agent

### Components

#### Sales Price Index Component
- Comprehensive pricing management interface
- Dropdown selections for products, branches, agents
- Date pickers for effective/expiry dates
- Bulk pricing capabilities
- Advanced filtering and search

### Routes

- `/sales-price` → Sales pricing management

### Usage

#### Setting Prices
1. Navigate to Sales Management → Sales Price
2. Select product (required)
3. Optionally select branch and/or agent
4. Set price and date range
5. Add notes if needed
6. Save pricing rule

#### Price Resolution
- Agent-specific prices override branch prices
- Branch prices override global prices
- Only active (non-expired) prices are considered

---

## Sales Profile

### Process and Workflow

The Sales Profile system provides complete sales record management with multi-product support, automatic numbering, and comprehensive tracking.

**Key Processes:**
1. **Sales Record Creation**: Header information + multiple line items
2. **Automatic Numbering**: SLS-YYYYMM-#### format
3. **Multi-product Sales**: Add multiple products per sale
4. **Real-time Calculations**: Automatic total computation
5. **Relationship Management**: Links to branches, agents, products
6. **Audit Trail**: Complete transaction history

**Workflow:**
1. Navigate to Sales Management → Profile
2. Click "Create Sales Profile"
3. Fill header: date, branch, agent, remarks
4. Add products: select product, quantity, unit price
5. System calculates totals automatically
6. Save complete sales record
7. System generates unique sales number

### Database Schema

#### sales_profiles Table (Header)
- `id`: Primary key
- `sales_number`: Unique auto-generated number
- `sales_date`: Transaction date
- `branch_id`: Foreign key to branches
- `agent_id`: Foreign key to agents
- `total_amount`: Calculated total
- `remarks`: Optional notes
- `created_at`, `updated_at`: Timestamps

#### sales_profile_items Table (Line Items)
- `id`: Primary key
- `sales_profile_id`: Foreign key to sales_profiles
- `product_id`: Foreign key to products
- `quantity`: Integer quantity sold
- `unit_price`: Price per unit
- `total_price`: Calculated line total
- `created_at`, `updated_at`: Timestamps

### Models

#### SalesProfile Model
- Fillable: sales_number, sales_date, branch_id, agent_id, total_amount, remarks
- Casts: sales_date as date, total_amount as decimal
- Relationships: belongsTo Branch, Agent; hasMany Items
- Auto-generates sales numbers on creation

#### SalesProfileItem Model
- Fillable: sales_profile_id, product_id, quantity, unit_price, total_price
- Casts: quantity as integer, prices as decimals
- Relationships: belongsTo SalesProfile, Product

### Components

#### Sales Profile Index Component
- Complete sales management interface
- Dynamic item addition/removal
- Real-time total calculations
- Auto-generated document numbers
- Comprehensive search and filtering
- Modal-based editing

### Routes

- `/sales-profile` → Sales profile management

### Usage

#### Creating Sales Records
1. Navigate to Sales Management → Profile
2. Click "Create Sales Profile"
3. Enter header information:
   - Sales date (defaults to today)
   - Select branch and agent
   - Add optional remarks
4. Add products:
   - Select product from dropdown
   - Enter quantity and unit price
   - Click "Add Item" (repeat as needed)
5. Review calculated totals
6. Click "Create Sales Profile"

#### Managing Records
- **View**: Paginated list with search
- **Edit**: Modify header and items
- **Delete**: Remove with confirmation
- **Search**: Filter by number, branch, agent

#### Document Numbering
- Format: SLS-YYYYMM-####
- Example: SLS-202510-0001
- Monthly auto-increment

---

## Navigation Structure

```
Sales Management
├── Sales Order
├── Sales Return
├── Sales Price
└── Profile (Sales Records)

Setup
└── Allocations
    ├── Sales
    └── Warehouse

    
*This documentation covers the business processes and workflows implemented as of October 2025.*