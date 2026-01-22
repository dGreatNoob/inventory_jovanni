<?php

namespace App\Enums;

enum PaymentStatus: string
{
    case NOT_PAID = 'not_paid';
    case OVERDUE = 'overdue';
    case PARTIALLY_PAID = 'partially_paid';
    case FULLY_PAID = 'fully_paid';

    /**
     * Human-readable label for status
     */
    public function label(): string
    {
        return match ($this) {
            self::NOT_PAID => 'Not Paid',
            self::OVERDUE => 'Overdue',
            self::PARTIALLY_PAID => 'Partially Paid',
            self::FULLY_PAID => 'Fully Paid',
        };
    }

    /**
     * Tailwind color name (for badge etc)
     */
    public function color(): string
    {
        return match ($this) {
            self::NOT_PAID => 'yellow',
            self::OVERDUE => 'red',
            self::PARTIALLY_PAID => 'blue',
            self::FULLY_PAID => 'green',
        };
    }

    /**
     * Heroicon name for status
     */
    public function icon(): string
    {
        return match ($this) {
            self::NOT_PAID => 'clock',
            self::OVERDUE => 'exclamation-triangle',
            self::PARTIALLY_PAID => 'arrow-path',
            self::FULLY_PAID => 'check-circle',
        };
    }

    /**
     * CSS classes for status badge
     */
    public function badgeClasses(): string
    {
        return match ($this) {
            self::NOT_PAID => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300',
            self::OVERDUE => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300',
            self::PARTIALLY_PAID => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300',
            self::FULLY_PAID => 'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300',
        };
    }

    /**
     * Description of each status
     */
    public function description(): string
    {
        return match ($this) {
            self::NOT_PAID => 'No payment has been made',
            self::OVERDUE => 'Payment is past due date',
            self::PARTIALLY_PAID => 'Partial payment has been received',
            self::FULLY_PAID => 'Payment completed in full',
        };
    }
}
