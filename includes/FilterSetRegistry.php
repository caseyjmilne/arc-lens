<?php
/**
 * FilterSetRegistry
 * 
 * Central registry for FilterSet instances
 * Allows lookup by collection key
 */

namespace ARC\Lens;

if (!defined('ABSPATH')) exit;

class FilterSetRegistry
{
    /**
     * Registered FilterSets
     * @var array
     */
    private static $registry = [];

    /**
     * Register a FilterSet instance
     * 
     * @param string $collectionKey Collection identifier
     * @param FilterSet $filterSet FilterSet instance
     */
    public static function register($collectionKey, FilterSet $filterSet)
    {
        self::$registry[$collectionKey] = $filterSet;
    }

    /**
     * Get FilterSet for a collection
     * 
     * @param string $collectionKey Collection identifier
     * @return FilterSet|null
     */
    public static function get($collectionKey)
    {
        return self::$registry[$collectionKey] ?? null;
    }

    /**
     * Check if FilterSet exists for collection
     * 
     * @param string $collectionKey Collection identifier
     * @return bool
     */
    public static function has($collectionKey)
    {
        return isset(self::$registry[$collectionKey]);
    }

    /**
     * Get all registered FilterSets
     * 
     * @return array
     */
    public static function all()
    {
        return self::$registry;
    }

    /**
     * Remove a FilterSet from registry
     * 
     * @param string $collectionKey Collection identifier
     */
    public static function remove($collectionKey)
    {
        unset(self::$registry[$collectionKey]);
    }

    /**
     * Clear all registered FilterSets
     */
    public static function clear()
    {
        self::$registry = [];
    }
}