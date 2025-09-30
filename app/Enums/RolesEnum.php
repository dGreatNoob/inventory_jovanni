<?php

namespace App\Enums;

enum RolesEnum: string
{
    

    case PURCHASER = 'Purchasing Head';
    case RAWMAT = 'Raw Material Personnel';
    case SUPPLY = 'Supply Personnel'; 
    case SUPERADMIN = 'Super Admin';

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
