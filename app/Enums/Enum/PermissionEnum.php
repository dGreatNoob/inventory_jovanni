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


    public function label(): string
    {
        return match ($this) {
            static::CREATE_SUPPLY_PURCHASE_ORDER => 'Create Supply Purchase Order',
            static::APPROVE_SUPPLY_PURCHASE_ORDER => 'Approve Supply Purchase Order',
            static::VIEW_SUPPLY_PURCHASE_ORDER => 'View Supply Purchase Order',

            static::VIEW_REQUEST_SLIP => 'View Request Slip',
            static::APPROVE_REQUEST_SLIP => 'Approve Request Slip',
            static::CREATE_REQUEST_SLIP => 'Create Request Slip',
            static::DELETE_REQUEST_SLIP => 'Delete Request Slip',

            static::CREATE_RAWMAT_PURCHASE_ORDER => 'Create Raw Material Purchase Order',
            static::APPROVE_RAWMAT_PURCHASE_ORDER => 'Approve Raw Material Purchase Order',
            static::VIEW_RAWMAT_PURCHASE_ORDER => 'View Raw Material Purchase Order',


          
        };
    }
}
