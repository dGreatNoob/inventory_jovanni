<?php

namespace App\Enums\Enum;

enum PermissionEnum: string
{

    // 📦 Product Management
    case PRODUCT_VIEW = 'product view';
    case PRODUCT_ANALYTIC = 'product analytic view';
    case PRODUCT_CREATE = 'product create';
    case PRODUCT_EDIT = 'product edit';
    case PRODUCT_DELETE = 'product delete';
    case PRODUCT_EXPORT = 'product export';

    case CATEGORY_VIEW = 'category view';
    case CATEGORY_CREATE = 'category create';
    case CATEGORY_EDIT = 'category edit';
    case CATEGORY_DELETE = 'category delete';

    case IMAGE_VIEW = 'image view';
    case IMAGE_CREATE = 'image create';
    case IMAGE_EDIT = 'image edit';
    case IMAGE_DELETE = 'image delete';

    // 🧍 Agent & Branch Management
    case AGENT_VIEW = 'agent view';
    case AGENT_CREATE = 'agent create';
    case AGENT_EDIT = 'agent edit';
    case AGENT_DELETE = 'agent delete';
    case BRANCH_VIEW = 'branch view';
    case BRANCH_CREATE = 'branch create';
    case BRANCH_EDIT = 'branch edit';
    case BRANCH_DELETE = 'branch delete';
    case AGENT_ASSIGN_BRANCH = 'agent assign branch';
    case AGENT_TRANSFER_BRANCH = 'agent transfer branch';

    // 🏭 Supplier Management
    case SUPPLIER_VIEW = 'supplier view';
    case SUPPLIER_CREATE = 'supplier create';
    case SUPPLIER_EDIT = 'supplier edit';
    case SUPPLIER_DELETE = 'supplier delete';
    case SUPPLIER_REPORT_VIEW = 'supplier report view';

    // 📑 Purchase Order Management
    case PO_VIEW = 'po view';
    case PO_CREATE = 'po create';
    case PO_EDIT = 'po edit';
    case PO_DELETE = 'po delete';
    case PO_APPROVE = 'po approve';
    case PO_RECEIVE = 'po receive';
    case PO_REPORT_VIEW = 'po report view';

    // 👥 User & Role Management
    case USER_VIEW = 'user view';
    case USER_CREATE = 'user create';
    case USER_EDIT = 'user edit';
    case USER_DELETE = 'user delete';
    case ROLE_VIEW = 'role view';
    case ROLE_CREATE = 'role create';
    case ROLE_EDIT = 'role edit';
    case ROLE_DELETE = 'role delete';
    case PERMISSION_MANAGE = 'permission manage';

    // Allocation Management
    case ALLOCATION_WAREHOUSE_TRANSFER = 'allocation warehouse transfer';
    case ALLOCATION_BRANCH_TRANSFER = 'allocation branch transfer';
    case ALLOCATION_SALES_RETURN = 'allocation sales return';

    // Shipment Management
    case SHIPMENT_VIEW = 'shipment view';

    // Finance
    case FINANCE_RECEIVABLES = 'finance receivables';
    case FINANCE_PAYABLES = 'finance payables';
    case FINANCE_EXPENSES = 'finance expenses';
    case FINANCE_PAYMENTS = 'finance payments';

    // Warehouse Staff
    case WAREHOUSE_STOCK_IN = 'warehouse stock in';
    case WAREHOUSE_STOCK_OUT = 'warehouse stock out';

    // Reports
    case REPORT_PRODUCT_INVENTORY = 'report product inventory';
    case REPORT_PURCHASE_ORDERS = 'report purchase orders';
    case REPORT_BRANCH_INVENTORY = 'report branch inventory';
    case REPORT_WAREHOUSE_ALLOCATION = 'report warehouse allocation';

    // 🏷️ Human-readable Labels
    public function label(): string
    {
        return match ($this) {
            

            // Product
            static::PRODUCT_VIEW => 'View Products',
            static::PRODUCT_ANALYTIC => 'View Product Analytics',
            static::PRODUCT_CREATE => 'Add Product',
            static::PRODUCT_EDIT => 'Edit Product',
            static::PRODUCT_DELETE => 'Delete Product',
            static::PRODUCT_EXPORT => 'Export Product Data',

            static::CATEGORY_VIEW => 'View Categories',
            static::CATEGORY_CREATE => 'Add Category',
            static::CATEGORY_EDIT => 'Edit Category',
            static::CATEGORY_DELETE => 'Delete Category',

            static::IMAGE_VIEW => 'View Images',
            static::IMAGE_CREATE => 'Add Image',
            static::IMAGE_EDIT => 'Edit Image',
            static::IMAGE_DELETE => 'Delete Image',


            // Agent & Branch
            static::AGENT_VIEW => 'View Agents',
            static::AGENT_CREATE => 'Add Agent',
            static::AGENT_EDIT => 'Edit Agent',
            static::AGENT_DELETE => 'Delete Agent',
            static::BRANCH_VIEW => 'View Branches',
            static::BRANCH_CREATE => 'Add Branch',
            static::BRANCH_EDIT => 'Edit Branch',
            static::BRANCH_DELETE => 'Delete Branch',
            static::AGENT_ASSIGN_BRANCH => 'Assign Agent to Branch',
            static::AGENT_TRANSFER_BRANCH => 'Transfer Agent to Another Branch',

            // Supplier
            static::SUPPLIER_VIEW => 'View Suppliers',
            static::SUPPLIER_CREATE => 'Add Supplier',
            static::SUPPLIER_EDIT => 'Edit Supplier',
            static::SUPPLIER_DELETE => 'Delete Supplier',
            static::SUPPLIER_REPORT_VIEW => 'View Supplier Reports',

            // Purchase Order
            static::PO_VIEW => 'View Purchase Orders',
            static::PO_CREATE => 'Create Purchase Order',
            static::PO_EDIT => 'Edit Purchase Order',
            static::PO_DELETE => 'Delete Purchase Order',
            static::PO_APPROVE => 'Approve/Reject Purchase Order',
            static::PO_RECEIVE => 'Receive Purchase Order Items',
            static::PO_REPORT_VIEW => 'View Purchase Order Reports',

            // User & Role
            static::USER_VIEW => 'View Users',
            static::USER_CREATE => 'Add User',
            static::USER_EDIT => 'Edit User',
            static::USER_DELETE => 'Delete User',
            static::ROLE_VIEW => 'View Roles',
            static::ROLE_CREATE => 'Create Role',
            static::ROLE_EDIT => 'Edit Role',
            static::ROLE_DELETE => 'Delete Role',
            static::PERMISSION_MANAGE => 'Manage Permission Definitions',

            // Allocation
            static::ALLOCATION_WAREHOUSE_TRANSFER => 'Warehouse Transfer',
            static::ALLOCATION_BRANCH_TRANSFER => 'Branch Transfer',
            static::ALLOCATION_SALES_RETURN => 'Sales Return',

            // Shipment
            static::SHIPMENT_VIEW => 'View Shipments',

            // Finance
            static::FINANCE_RECEIVABLES => 'View Receivables',
            static::FINANCE_PAYABLES => 'View Payables',
            static::FINANCE_EXPENSES => 'View Expenses',
            static::FINANCE_PAYMENTS => 'View Payments',

            // Warehouse Staff
            static::WAREHOUSE_STOCK_IN => 'Stock In',
            static::WAREHOUSE_STOCK_OUT => 'Stock Out',

            // Reports
            static::REPORT_PRODUCT_INVENTORY => 'Product Inventory Report',
            static::REPORT_PURCHASE_ORDERS => 'Purchase Orders Report',
            static::REPORT_BRANCH_INVENTORY => 'Branch Inventory Report',
            static::REPORT_WAREHOUSE_ALLOCATION => 'Warehouse Allocation Report',
        };
    }

    // 📂 Category grouping (used for UI)
    public function category(): string
    {
        return match ($this) {
          

            // Product Management
            static::PRODUCT_VIEW,
            static::PRODUCT_ANALYTIC,
            static::PRODUCT_CREATE,
            static::PRODUCT_EDIT,
            static::PRODUCT_DELETE,
            static::PRODUCT_EXPORT,
            static::CATEGORY_VIEW,
            static::CATEGORY_CREATE,
            static::CATEGORY_EDIT,
            static::CATEGORY_DELETE,
            static::IMAGE_VIEW,
            static::IMAGE_CREATE,
            static::IMAGE_EDIT,
            static::IMAGE_DELETE => 'Product Management',

            // Agent & Branch Management
            static::AGENT_VIEW,
            static::AGENT_CREATE,
            static::AGENT_EDIT,
            static::AGENT_DELETE,
            static::BRANCH_VIEW,
            static::BRANCH_CREATE,
            static::BRANCH_EDIT,
            static::BRANCH_DELETE,
            static::AGENT_ASSIGN_BRANCH,
            static::AGENT_TRANSFER_BRANCH => 'Agent & Branch Management',

            // Supplier Management
            static::SUPPLIER_VIEW,
            static::SUPPLIER_CREATE,
            static::SUPPLIER_EDIT,
            static::SUPPLIER_DELETE,
            static::SUPPLIER_REPORT_VIEW => 'Supplier Management',

            // Purchase Order Management
            static::PO_VIEW,
            static::PO_CREATE,
            static::PO_EDIT,
            static::PO_DELETE,
            static::PO_APPROVE,
            static::PO_RECEIVE,
            static::PO_REPORT_VIEW => 'Purchase Order Management',

            // User & Role Management
            static::USER_VIEW,
            static::USER_CREATE,
            static::USER_EDIT,
            static::USER_DELETE,
            static::ROLE_VIEW,
            static::ROLE_CREATE,
            static::ROLE_EDIT,
            static::ROLE_DELETE,
            static::PERMISSION_MANAGE => 'User & Role Management',

            // Allocation Management
            static::ALLOCATION_WAREHOUSE_TRANSFER,
            static::ALLOCATION_BRANCH_TRANSFER,
            static::ALLOCATION_SALES_RETURN => 'Allocation Management',

            // Shipment Management
            static::SHIPMENT_VIEW => 'Shipment Management',

            // Finance
            static::FINANCE_RECEIVABLES,
            static::FINANCE_PAYABLES,
            static::FINANCE_EXPENSES,
            static::FINANCE_PAYMENTS => 'Finance',

            // Warehouse Staff
            static::WAREHOUSE_STOCK_IN,
            static::WAREHOUSE_STOCK_OUT => 'Warehouse Staff',

            // Reports
            static::REPORT_PRODUCT_INVENTORY,
            static::REPORT_PURCHASE_ORDERS,
            static::REPORT_BRANCH_INVENTORY,
            static::REPORT_WAREHOUSE_ALLOCATION => 'Reports',
        };
    }

    // 📜 Helper: Return all permission values
    public static function all(): array
    {
        return array_column(self::cases(), 'value');
    }
}

