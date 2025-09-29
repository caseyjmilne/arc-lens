<?php
/**
 * Filter Abstract Class
 * 
 * Represents a single filter in a FilterSet
 * Can be extended for complex filters or instantiated from array config
 * 
 * Examples:
 * 1. Array config (simple):
 *    'status' => ['type' => 'select', 'options' => ['draft', 'published']]
 * 
 * 2. Custom class (complex):
 *    class AuthorFilter extends Filter {
 *        protected $type = 'select';
 *        public function getOptions() { return get_users(); }
 *    }
 */

namespace ARC\Lens;

use ARC\Lens\FilterTypes\FilterType;

if (!defined('ABSPATH')) exit;

abstract class Filter
{
    /**
     * Filter key/name
     */
    protected $key;

    /**
     * Filter type (select, search, checkbox, etc.)
     */
    protected $type = 'search';

    /**
     * Display label
     */
    protected $label;

    /**
     * Placeholder text
     */
    protected $placeholder = '';

    /**
     * Options for select/checkbox filters
     */
    protected $options = [];

    /**
     * HTML attributes
     */
    protected $attributes = [];

    /**
     * Collection this filter belongs to
     */
    protected $collection;

    /**
     * FilterType instance (handles rendering)
     */
    private $filterType;

    /**
     * Constructor
     */
    public function __construct($key, $collection)
    {
        $this->key = $key;
        $this->collection = $collection;
        
        // Allow subclass to set label, or default to key
        if (empty($this->label)) {
            $this->label = ucfirst(str_replace('_', ' ', $key));
        }
    }

    /**
     * Get options - override this for dynamic options
     */
    public function getOptions()
    {
        return $this->options;
    }

    /**
     * Render the filter
     */
    public function render()
    {
        if (!$this->filterType) {
            $this->filterType = $this->createFilterType();
        }
        
        return $this->filterType->render();
    }

    /**
     * Get scripts needed for this filter
     */
    public function getScripts()
    {
        if (!$this->filterType) {
            $this->filterType = $this->createFilterType();
        }
        
        return $this->filterType->getScripts();
    }

    /**
     * Get styles needed for this filter
     */
    public function getStyles()
    {
        if (!$this->filterType) {
            $this->filterType = $this->createFilterType();
        }
        
        return $this->filterType->getStyles();
    }

    /**
     * Get inline script for this filter
     */
    public function getInlineScript()
    {
        if (!$this->filterType) {
            $this->filterType = $this->createFilterType();
        }
        
        return $this->filterType->renderInlineScript();
    }

    /**
     * Get filter configuration array
     */
    public function toArray()
    {
        return [
            'type' => $this->type,
            'label' => $this->label,
            'placeholder' => $this->placeholder,
            'options' => $this->getOptions(), // Calls overridden method
            'attributes' => $this->attributes
        ];
    }

    /**
     * Create FilterType instance from this filter
     */
    private function createFilterType()
    {
        $config = $this->toArray();
        $className = $this->getFilterTypeClass($this->type);
        
        if (!class_exists($className)) {
            throw new \Exception("FilterType class not found: {$className}");
        }
        
        return new $className($this->key, $config, $this->collection);
    }

    /**
     * Get FilterType class name
     */
    private function getFilterTypeClass($type)
    {
        $typeMap = [
            'select' => 'ARC\\Lens\\FilterTypes\\Select',
            'search' => 'ARC\\Lens\\FilterTypes\\Search',
            'text' => 'ARC\\Lens\\FilterTypes\\Search',
            'checkbox' => 'ARC\\Lens\\FilterTypes\\Checkbox',
        ];
        
        return $typeMap[$type] ?? 'ARC\\Lens\\FilterTypes\\Search';
    }

    // --- Factory method for creating from array config ---

    /**
     * Create a Filter instance from array configuration
     * This is used when developer defines filters as arrays
     */
    public static function fromArray($key, $config, $collection)
    {
        return new class($key, $collection, $config) extends Filter {
            private $config;
            
            public function __construct($key, $collection, $config)
            {
                $this->config = $config;
                $this->type = $config['type'] ?? 'search';
                $this->label = $config['label'] ?? '';
                $this->placeholder = $config['placeholder'] ?? '';
                $this->options = $config['options'] ?? [];
                $this->attributes = $config['attributes'] ?? [];
                
                parent::__construct($key, $collection);
            }
            
            public function getOptions()
            {
                // If options is callable, call it
                if (is_callable($this->options)) {
                    return call_user_func($this->options);
                }
                
                // If it's a method name on the parent FilterSet, we can't access it here
                // So just return as-is and let FilterType handle it
                return $this->options;
            }
        };
    }
}