<?php

namespace App\Enums;

enum RolesEnum: string
{
    

    case PURCHASER = 'Purchasing Head';
    case RAWMAT = 'Raw Material Personnel';
    case SUPPLY = 'Supply Personnel'; 
    case SUPERADMIN = 'Super Admin';

    case PRODUCTMANAGEMENT = 'Product Management';
    case ABMANAGEMENT = 'Agent & Branch Management';
    case SUPPLIERMANAGEMENT = 'Supplier Management';
    case POMANAGEMENT = 'Purchase Order Management';
    case USERMANAGEMENT = 'User Management (Admin Only)';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            static::PURCHASER => 'Purchase Personnel',
            static::RAWMAT => 'Raw Material Personnel',
            static::SUPERADMIN => 'User Administrator',
            static::SUPPLY => 'Supply Personnel',
        };
    }
}
