<?php

namespace App\Enums\Enum;

enum PermissionEnum: string
{
    // Purchase-related
    case CREATE_SUPPLY_PURCHASE_ORDER = 'create supply purchase order';
    case APPROVE_SUPPLY_PURCHASE_ORDER = 'approve supply purchase order';
    case VIEW_SUPPLY_PURCHASE_ORDER = 'view supply purchase order';

    case CREATE_RAWMAT_PURCHASE_ORDER = 'create rawmat purchase order';
    case APPROVE_RAWMAT_PURCHASE_ORDER = 'approve rawmat purchase order';
    case VIEW_RAWMAT_PURCHASE_ORDER = 'view rawmat purchase order';

    // Request Slip-related
    case VIEW_REQUEST_SLIP = 'view request slip';
    case APPROVE_REQUEST_SLIP = 'approve request slip';
    case CREATE_REQUEST_SLIP = 'create request slip';
    case DELETE_REQUEST_SLIP = 'delete request slip';

    // Product Management
    case PRODUCT_VIEW = 'product view';
    case PRODUCT_CREATE = 'product create';
    case PRODUCT_EDIT = 'product edit';
    case PRODUCT_DELETE = 'product delete';
    case PRODUCT_EXPORT = 'product export';

    // Agent & Branch Management
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

    // Supplier Management
    case SUPPLIER_VIEW = 'supplier view';
    case SUPPLIER_CREATE = 'supplier create';
    case SUPPLIER_EDIT = 'supplier edit';
    case SUPPLIER_DELETE = 'supplier delete';
    case SUPPLIER_REPORT_VIEW = 'supplier report view';

    // Purchase Order Management
    case PO_VIEW = 'po view';
    case PO_CREATE = 'po create';
    case PO_EDIT = 'po edit';
    case PO_DELETE = 'po delete';
    case PO_APPROVE = 'po approve';
    case PO_RECEIVE = 'po receive';
    case PO_REPORT_VIEW = 'po report view';

    // User Management
    case USER_VIEW = 'user view';
    case USER_CREATE = 'user create';
    case USER_EDIT = 'user edit';
    case USER_DELETE = 'user delete';
    case ROLE_VIEW = 'role view';
    case ROLE_CREATE = 'role create';
    case ROLE_EDIT = 'role edit';
    case ROLE_DELETE = 'role delete';
    case PERMISSION_MANAGE = 'permission manage';

    // 🏷️ Labels
    public function label(): string
    {
        return match ($this) {
            // Purchase
            static::CREATE_SUPPLY_PURCHASE_ORDER => 'Create Supply Purchase Order',
            static::APPROVE_SUPPLY_PURCHASE_ORDER => 'Approve Supply Purchase Order',
            static::VIEW_SUPPLY_PURCHASE_ORDER => 'View Supply Purchase Order',

            static::CREATE_RAWMAT_PURCHASE_ORDER => 'Create Raw Material Purchase Order',
            static::APPROVE_RAWMAT_PURCHASE_ORDER => 'Approve Raw Material Purchase Order',
            static::VIEW_RAWMAT_PURCHASE_ORDER => 'View Raw Material Purchase Order',

            // Request Slip
            static::CREATE_REQUEST_SLIP => 'Create Request Slip',
            static::APPROVE_REQUEST_SLIP => 'Approve Request Slip',
            static::VIEW_REQUEST_SLIP => 'View Request Slip',
            static::DELETE_REQUEST_SLIP => 'Delete Request Slip',

            // Product Management
            static::PRODUCT_VIEW => 'View Products',
            static::PRODUCT_CREATE => 'Add Product',
            static::PRODUCT_EDIT => 'Edit Product',
            static::PRODUCT_DELETE => 'Delete Product',
            static::PRODUCT_EXPORT => 'Export Product Data',

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

            // Supplier Management
            static::SUPPLIER_VIEW => 'View Suppliers',
            static::SUPPLIER_CREATE => 'Add Supplier',
            static::SUPPLIER_EDIT => 'Edit Supplier',
            static::SUPPLIER_DELETE => 'Delete Supplier',
            static::SUPPLIER_REPORT_VIEW => 'View Supplier Reports',

            // Purchase Order Management
            static::PO_VIEW => 'View Purchase Orders',
            static::PO_CREATE => 'Create Purchase Order',
            static::PO_EDIT => 'Edit Purchase Order',
            static::PO_DELETE => 'Delete Purchase Order',
            static::PO_APPROVE => 'Approve/Reject Purchase Order',
            static::PO_RECEIVE => 'Receive Purchase Order Items',
            static::PO_REPORT_VIEW => 'View Purchase Order Reports',

            // User Management
            static::USER_VIEW => 'View Users',
            static::USER_CREATE => 'Add User',
            static::USER_EDIT => 'Edit User',
            static::USER_DELETE => 'Delete User',
            static::ROLE_VIEW => 'View Roles',
            static::ROLE_CREATE => 'Create Role',
            static::ROLE_EDIT => 'Edit Role',
            static::ROLE_DELETE => 'Delete Role',
            static::PERMISSION_MANAGE => 'Manage Permission Definitions',
        };
    }
}
