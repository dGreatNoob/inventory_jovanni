<?php

namespace App\Support;

/**
 * Reusable product search logic for allocation, product management, etc.
 *
 * Segment-prefix matching: splits query and value by "-" (and space, _). Each query
 * segment must match the PREFIX of the corresponding value segment. E.g. "LD-127"
 * matches "LD2505-127" (LD prefix of LD2505, 127 prefix of 127) but NOT "127LD-XXX"
 * or "XXX-LD127".
 */
class ProductSearchHelper
{
    /**
     * Check if a product value matches the search query using segment-prefix logic.
     *
     * @param  string  $query  User search input (e.g. "LD-127")
     * @param  string  $value  Field value to match (e.g. "LD2505-127")
     */
    public static function matchesSegmentPrefix(string $query, string $value): bool
    {
        $value = strtolower(trim($value));
        $querySegments = preg_split('/[\s\-_]+/', strtolower(trim($query)), -1, PREG_SPLIT_NO_EMPTY);
        if (empty($querySegments)) {
            return true;
        }
        $valueSegments = preg_split('/[\s\-_]+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        if (count($querySegments) > count($valueSegments)) {
            return false;
        }
        for ($i = 0; $i < count($querySegments); $i++) {
            $q = $querySegments[$i];
            $v = $valueSegments[$i] ?? '';
            if ($q !== '' && !str_starts_with($v, $q)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Check if any of the given product fields match the query.
     *
     * @param  string  $query  User search input
     * @param  array<string>  $fields  Field values (e.g. name, product_number, remarks, sku)
     */
    public static function matchesAnyField(string $query, array $fields): bool
    {
        foreach ($fields as $field) {
            if (self::matchesSegmentPrefix($query, (string) $field)) {
                return true;
            }
        }
        return false;
    }
}
