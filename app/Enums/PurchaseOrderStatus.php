<?php

namespace App\Enums;

enum PurchaseOrderStatus: string
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case TO_RECEIVE = 'to_receive';
    case RECEIVED = 'received';
    case CANCELLED = 'cancelled';

    /**
     * Human-readable label for status
     */
    public function label(): string
    {
        return match ($this) {
            self::PENDING    => 'Pending Approval',
            self::APPROVED   => 'Approved',
            self::TO_RECEIVE => 'To Receive',
            self::RECEIVED   => 'Received',
            self::CANCELLED  => 'Cancelled',
        };
    }

    /**
     * Tailwind color name (for badge etc)
     */
    public function color(): string
    {
        return match ($this) {
            self::PENDING    => 'yellow',
            self::APPROVED   => 'blue',
            self::TO_RECEIVE => 'purple',
            self::RECEIVED   => 'green',
            self::CANCELLED  => 'red',
        };
    }

    /**
     * Heroicon name for status
     */
    public function icon(): string
    {
        return match ($this) {
            self::PENDING    => 'clock',
            self::APPROVED   => 'check-circle',
            self::TO_RECEIVE => 'inbox-arrow-down',
            self::RECEIVED   => 'check-badge',
            self::CANCELLED  => 'x-circle',
        };
    }

    /**
     * Can this status transition to another?
     */
    public function canTransitionTo(self $newStatus): bool
    {
        if ($newStatus === self::CANCELLED && !$this->isTerminal()) {
            return true;
        }

        return match ($this) {
            self::PENDING    => $newStatus === self::APPROVED,
            self::APPROVED   => $newStatus === self::TO_RECEIVE,
            self::TO_RECEIVE => in_array($newStatus, [self::APPROVED, self::RECEIVED]),
            self::RECEIVED   => $newStatus === self::TO_RECEIVE,
            default          => false,
        };
    }

    /**
     * Required fields for a status transition
     */
    public function requiredFields(): array
    {
        return match ($this) {
            self::PENDING    => ['supplier_id', 'order_date', 'deliver_to', 'items'],
            self::APPROVED   => [],
            self::TO_RECEIVE => ['loaded_date', 'expected_quantities', 'batch_info'],
            self::RECEIVED   => [],
            self::CANCELLED  => ['cancellation_reason'],
        };
    }

    /**
     * Description of each status
     */
    public function description(): string
    {
        return match ($this) {
            self::PENDING    => 'PO submitted and awaiting manager approval',
            self::APPROVED   => 'PO approved, loading and batch details captured',
            self::TO_RECEIVE => 'Goods ready for receiving/stock-in process',
            self::RECEIVED   => 'All items received and added to inventory',
            self::CANCELLED  => 'PO cancelled and will not be fulfilled',
        };
    }

    /**
     * Is status editable?
     */
    public function isEditable(): bool
    {
        return $this === self::PENDING;
    }

    /**
     * Is cancellation allowed?
     */
    public function isCancellable(): bool
    {
        return ! $this->isTerminal();
    }

    /**
     * Is this a terminal status?
     */
    public function isTerminal(): bool
    {
        return in_array($this, [self::RECEIVED, self::CANCELLED]);
    }

    /**
     * Possible next statuses
     */
    public function possibleNextStatuses(): array
    {
        return array_values(array_filter(
            self::cases(),
            fn($status) => $this->canTransitionTo($status)
        ));
    }

    /**
     * CSS classes for status badge
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::PENDING => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::APPROVED => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-cyan-100 text-cyan-800 dark:bg-cyan-900 dark:text-cyan-300',
            self::TO_RECEIVE => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300',
            self::RECEIVED => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
            self::CANCELLED => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
        };
    }
}